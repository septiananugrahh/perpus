<?php

namespace App\Http\Controllers;

use App\Models\PeminjamanBuku;
use App\Services\SantriService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SantriController extends Controller
{
  public function __construct(protected SantriService $santriService) {}

  public function index(Request $request)
  {
    $santris = collect($this->santriService->all());

    if ($request->filled('search')) {
      $search = strtolower($request->string('search'));
      $santris = $santris->filter(function ($s) use ($search) {
        return str_contains(strtolower($s['nama']), $search)
          || str_contains(strtolower($s['nis'] ?? ''), $search)
          || str_contains(strtolower($s['kelas_nama'] ?? ''), $search);
      })->values();
    }

    // Hitung jumlah peminjaman aktif & sudah kembali per santri, hindari N+1
    $activeLoanCounts = PeminjamanBuku::belumDikembalikan()
      ->selectRaw('penanggung_jawab, COUNT(*) as total')
      ->groupBy('penanggung_jawab')
      ->pluck('total', 'penanggung_jawab');

    $returnedLoanCounts = PeminjamanBuku::sudahDikembalikan()
      ->selectRaw('penanggung_jawab, COUNT(*) as total')
      ->groupBy('penanggung_jawab')
      ->pluck('total', 'penanggung_jawab');

    $santris = $santris->map(function ($s) use ($activeLoanCounts, $returnedLoanCounts) {
      $s['buku_dipinjam_aktif'] = $activeLoanCounts[$s['id']] ?? 0;
      $s['buku_sudah_kembali'] = $returnedLoanCounts[$s['id']] ?? 0;
      return $s;
    });

    // Pagination manual karena data dari cache/array, bukan query builder
    $perPage = 20;
    $page = (int) $request->get('page', 1);
    $paginated = $santris->forPage($page, $perPage)->values();

    return Inertia::render('Santri/Index', [
      'santris' => [
        'data' => $paginated,
        'current_page' => $page,
        'last_page' => (int) ceil($santris->count() / $perPage),
        'total' => $santris->count(),
      ],
      'filters' => $request->only('search'),
      'tahunAjar' => SantriService::TAHUN_AJAR,
    ]);
  }

  public function show(string $id)
  {
    $santri = $this->santriService->find($id);

    if (! $santri) {
      abort(404, 'Santri tidak ditemukan.');
    }

    $riwayat = PeminjamanBuku::with('buku')
      ->where('penanggung_jawab', $id)
      ->orderByDesc('id_peminjaman')
      ->get();

    return Inertia::render('Santri/Show', [
      'santri' => $santri,
      'riwayatPeminjaman' => $riwayat,
    ]);
  }

  // Dipanggil dari tombol "Refresh Data Santri"
  public function refresh()
  {
    $this->santriService->refresh();

    return redirect()
      ->route('santri.index')
      ->with('success', 'Data santri berhasil diperbarui dari server.');
  }
}
