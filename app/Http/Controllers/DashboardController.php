<?php

namespace App\Http\Controllers;

use App\Models\IndukBuku;
use App\Models\PeminjamanBuku;
use App\Services\GuruService;
use App\Services\SantriService;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
  public function __construct(
    protected SantriService $santriService,
    protected GuruService $guruService,
  ) {}

  public function index()
  {
    return Inertia::render('Dashboard', [
      'stats' => $this->buildStats(),
      'statusBuku' => $this->buildStatusBuku(),
      'bukuSeringDipinjam' => $this->buildBukuSeringDipinjam(),
      'totalPeminjamanBulanIni' => $this->countPeminjamanBulanIni(),
    ]);
  }

  protected function buildStats(): array
  {
    $hariIni = now()->format('d-m-Y');

    $dipinjamHariIni = PeminjamanBuku::where('tgl_pinjam', 'like', "{$hariIni}%")->count();

    return [
      [
        'label' => 'Buku Dimiliki',
        'value' => IndukBuku::count(),
        'icon' => '📖',
        'color' => 'pink',
      ],
      [
        'label' => 'Dipinjam Hari Ini',
        'value' => $dipinjamHariIni,
        'icon' => '🔄',
        'color' => 'cyan',
      ],
      [
        'label' => 'Asatidz',
        'value' => count($this->guruService->all()),
        'icon' => '🎓',
        'color' => 'yellow',
      ],
      [
        'label' => 'Santri',
        'value' => count($this->santriService->all()),
        'icon' => '👥',
        'color' => 'purple',
      ],
    ];
  }

  // Breakdown buku aktif dipinjam berdasarkan sisa hari (bukan status dummy lama)
  protected function buildStatusBuku(): array
  {
    $aktif = PeminjamanBuku::belumDikembalikan()->get();
    $total = $aktif->count();

    if ($total === 0) {
      return [];
    }

    $aman = $aktif->filter(fn($l) => $l->sisaHari() !== null && $l->sisaHari() > 3)->count();
    $mendekati = $aktif->filter(fn($l) => $l->sisaHari() !== null && $l->sisaHari() >= 0 && $l->sisaHari() <= 3)->count();
    $terlambat = $aktif->filter(fn($l) => $l->sisaHari() !== null && $l->sisaHari() < 0)->count();
    $takValid = $total - $aman - $mendekati - $terlambat; // tanggal tidak bisa di-parse

    $bucket = function (string $label, int $count, string $color) use ($total) {
      return [
        'label' => $label,
        'jumlah' => $count,
        'percent' => $total > 0 ? round(($count / $total) * 100, 1) : 0,
        'color' => $color,
      ];
    };

    $result = [
      $bucket('Aman', $aman, 'cyan'),
      $bucket('Mendekati Jatuh Tempo', $mendekati, 'yellow'),
      $bucket('Terlambat', $terlambat, 'red'),
    ];

    if ($takValid > 0) {
      $result[] = $bucket('Tanggal Tidak Valid', $takValid, 'purple');
    }

    // buang kategori yang jumlahnya 0 biar progress bar tidak penuh segmen kosong
    return array_values(array_filter($result, fn($b) => $b['jumlah'] > 0));
  }

  protected function buildBukuSeringDipinjam(): array
  {
    $counts = PeminjamanBuku::select('id_barang', DB::raw('COUNT(*) as jumlah'))
      ->groupBy('id_barang')
      ->orderByDesc('jumlah')
      ->limit(5)
      ->get();

    $bukuIds = $counts->pluck('id_barang');
    $bukus = IndukBuku::withTrashed()->whereIn('id_buku', $bukuIds)->get()->keyBy('id_buku');

    return $counts->map(function ($row, $i) use ($bukus) {
      $buku = $bukus->get($row->id_barang);
      return [
        'no' => $i + 1,
        'nama' => $buku?->judul_buku ?? "#{$row->id_barang} (buku dihapus)",
        'jumlah' => $row->jumlah,
      ];
    })->values()->all();
  }

  protected function countPeminjamanBulanIni(): int
  {
    $prefix = now()->format('m-Y'); // format tanggal kita: dd-mm-yyyy, jadi cek substring bulan-tahun

    return PeminjamanBuku::where('tgl_pinjam', 'like', "%-{$prefix}%")->count();
  }
}
