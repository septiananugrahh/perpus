<?php

namespace App\Http\Controllers;

use App\Models\IndukBuku;
use App\Models\PeminjamanBuku;
use App\Services\GuruService;
use App\Services\SantriService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PeminjamanController extends Controller
{
  // Prefix barcode sama seperti fitur export label
  const BARCODE_PREFIX = 'SAIC';

  public function __construct(
    protected SantriService $santriService,
    protected GuruService $guruService,
  ) {}

  public function index()
  {
    $loans = PeminjamanBuku::belumDikembalikan()
      ->with('buku')
      ->orderByDesc('id_peminjaman')
      ->get()
      ->map(fn($loan) => $this->formatLoan($loan));

    return Inertia::render('Peminjaman/Index', [
      'loans' => $loans,
      'batasHariPinjam' => PeminjamanBuku::BATAS_HARI_PINJAM,
    ]);
  }

  // Cari buku dari kode barcode (dipanggil saat scan/ketik di modal pinjam & kembali)
  public function cariBuku(Request $request)
  {
    $request->validate(['kode' => ['required', 'string']]);

    $idBuku = $this->extractIdFromBarcode($request->kode);

    if (! $idBuku) {
      return response()->json(['found' => false, 'message' => 'Format barcode tidak dikenali.']);
    }

    $buku = IndukBuku::find($idBuku);

    if (! $buku) {
      return response()->json(['found' => false, 'message' => "Buku dengan ID {$idBuku} tidak ditemukan."]);
    }

    $activeLoan = PeminjamanBuku::belumDikembalikan()->where('id_barang', $buku->id_buku)->first();

    return response()->json([
      'found' => true,
      'buku' => $buku,
      'sedang_dipinjam' => (bool) $activeLoan,
      'peminjam_aktif' => $activeLoan ? $this->resolvePeminjamNama($activeLoan->penanggung_jawab) : null,
      'id_peminjaman_aktif' => $activeLoan?->id_peminjaman,
    ]);
  }

  // Cari peminjam (santri/guru) dari kode ID (dipanggil saat scan/ketik di modal pinjam)
  public function cariPeminjam(Request $request)
  {
    $request->validate(['kode' => ['required', 'string']]);
    $kode = trim($request->kode);

    if (Str::startsWith(strtoupper($kode), 'G')) {
      $guruNo = substr($kode, 1);
      $guru = $this->guruService->find($guruNo);

      if (! $guru) {
        return response()->json(['found' => false, 'message' => "Guru dengan kode {$kode} tidak ditemukan."]);
      }

      return response()->json([
        'found' => true,
        'tipe' => 'guru',
        'kode' => 'G' . $guruNo,
        'nama' => $guru['guru_nama'],
        'meta' => 'Guru',
      ]);
    }

    $santri = $this->santriService->find($kode);

    if (! $santri) {
      return response()->json(['found' => false, 'message' => "Santri dengan ID {$kode} tidak ditemukan."]);
    }

    return response()->json([
      'found' => true,
      'tipe' => 'santri',
      'kode' => $santri['id'],
      'nama' => $santri['nama'],
      'meta' => "{$santri['kelas_nama']} • Tingkat {$santri['kelas_tingkat']}",
    ]);
  }

  // Simpan peminjaman baru (dari modal Pinjamkan Buku)
  public function store(Request $request)
  {
    $validated = $request->validate([
      'id_barang' => ['required', 'integer'],
      'penanggung_jawab' => ['required', 'string'],
      'keterangan_peminjaman' => ['nullable', 'string', 'max:50'],
    ]);

    $buku = IndukBuku::find($validated['id_barang']);
    if (! $buku) {
      return back()->withErrors(['id_barang' => 'Buku tidak ditemukan.']);
    }

    $sedangDipinjam = PeminjamanBuku::belumDikembalikan()->where('id_barang', $validated['id_barang'])->exists();
    if ($sedangDipinjam) {
      return back()->withErrors(['id_barang' => 'Buku ini sedang dipinjam, belum bisa dipinjamkan lagi.']);
    }

    PeminjamanBuku::create([
      'id_barang' => $validated['id_barang'],
      'tgl_pinjam' => now()->format('d-m-Y H:i'),
      'tgl_kembali' => '',
      'penanggung_jawab' => $validated['penanggung_jawab'],
      'keterangan_peminjaman' => $validated['keterangan_peminjaman'] ?? '',
    ]);

    return redirect()->route('peminjaman.index')->with('success', "Buku \"{$buku->judul_buku}\" berhasil dipinjamkan.");
  }

  // Tandai buku sudah kembali (dari modal Kembalikan Buku via scan, atau tombol di tabel)
  public function kembalikan(Request $request)
  {
    $validated = $request->validate([
      'id_peminjaman' => ['required', 'integer'],
    ]);

    $loan = PeminjamanBuku::belumDikembalikan()->find($validated['id_peminjaman']);

    if (! $loan) {
      return back()->withErrors(['id_peminjaman' => 'Data peminjaman tidak ditemukan atau sudah dikembalikan.']);
    }

    $loan->update(['tgl_kembali' => now()->format('d-m-Y H:i')]);

    return redirect()->route('peminjaman.index')->with('success', 'Buku berhasil dikembalikan.');
  }

  // Cari buku manual by nama/kode (autocomplete), hanya tampilkan yang TIDAK sedang dipinjam
  public function cariBukuNama(Request $request)
  {
    $q = trim($request->get('q', ''));
    if (strlen($q) < 2) {
      return response()->json([]);
    }

    $activeIds = PeminjamanBuku::belumDikembalikan()->pluck('id_barang')->map(fn($id) => (string) $id);

    $bukus = IndukBuku::where('judul_buku', 'like', "%{$q}%")
      ->orWhere('nomerpanggil_buku', 'like', "%{$q}%")
      ->orWhere('id_buku', 'like', "%{$q}%")
      ->limit(15)
      ->get()
      ->reject(fn($b) => $activeIds->contains((string) $b->id_buku))
      ->take(10)
      ->values();

    return response()->json($bukus);
  }

  // Cari peminjam manual by nama (autocomplete), gabungan santri + guru
  public function cariPeminjamNama(Request $request)
  {
    $q = strtolower(trim($request->get('q', '')));
    if (strlen($q) < 2) {
      return response()->json([]);
    }

    $santris = collect($this->santriService->all())
      ->filter(fn($s) => str_contains(strtolower($s['nama']), $q))
      ->take(7)
      ->map(fn($s) => [
        'tipe' => 'santri',
        'kode' => $s['id'],
        'nama' => $s['nama'],
        'meta' => "{$s['kelas_nama']} • Tingkat {$s['kelas_tingkat']}",
      ]);

    $gurus = collect($this->guruService->all())
      ->filter(fn($g) => str_contains(strtolower($g['guru_nama']), $q))
      ->take(7)
      ->map(fn($g) => [
        'tipe' => 'guru',
        'kode' => 'G' . $g['guru_no'],
        'nama' => $g['guru_nama'],
        'meta' => 'Guru',
      ]);

    return response()->json($santris->concat($gurus)->values());
  }

  protected function extractIdFromBarcode(string $kode): ?int
  {
    $kode = trim($kode);

    // Kalau ada prefix SAIC, buang. Kalau user ngetik manual angka doang, langsung pakai.
    if (str_starts_with(strtoupper($kode), self::BARCODE_PREFIX)) {
      $kode = substr($kode, strlen(self::BARCODE_PREFIX));
    }

    return is_numeric($kode) ? (int) $kode : null;
  }

  protected function resolvePeminjamNama(string $penanggungJawab): array
  {
    if (Str::startsWith(strtoupper($penanggungJawab), 'G')) {
      $guru = $this->guruService->find(substr($penanggungJawab, 1));
      return ['tipe' => 'guru', 'nama' => $guru['guru_nama'] ?? "Guru #{$penanggungJawab}", 'meta' => 'Guru'];
    }

    $santri = $this->santriService->find($penanggungJawab);
    return [
      'tipe' => 'santri',
      'nama' => $santri['nama'] ?? "Santri #{$penanggungJawab}",
      'meta' => $santri ? "{$santri['kelas_nama']} • Tingkat {$santri['kelas_tingkat']}" : '',
    ];
  }

  protected function formatLoan(PeminjamanBuku $loan): array
  {
    $peminjam = $this->resolvePeminjamNama($loan->penanggung_jawab);

    return [
      'id_peminjaman' => $loan->id_peminjaman,
      'judul_buku' => $loan->buku?->judul_buku ?? "#{$loan->id_barang} (buku tidak ditemukan)",
      'id_barang' => $loan->id_barang,
      'tgl_pinjam' => $loan->tgl_pinjam,
      'keterangan_peminjaman' => $loan->keterangan_peminjaman,
      'peminjam_nama' => $peminjam['nama'],
      'peminjam_meta' => $peminjam['meta'],
      'peminjam_tipe' => $peminjam['tipe'],
      'sisa_hari' => $loan->sisaHari(),
    ];
  }
}
