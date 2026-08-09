<?php

namespace App\Http\Controllers;

use App\Models\PeminjamanBuku;
use App\Services\GuruService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GuruController extends Controller
{
  public function __construct(protected GuruService $guruService) {}

  public function index(Request $request)
  {
    $gurus = collect($this->guruService->all());

    if ($request->filled('search')) {
      $search = strtolower($request->string('search'));
      $gurus = $gurus->filter(fn($g) => str_contains(strtolower($g['guru_nama']), $search))->values();
    }

    // Semua peminjaman dengan penanggung_jawab berawalan "G" = guru.
    // Strip prefix "G" supaya bisa dicocokkan ke guru_no.
    $loans = PeminjamanBuku::where('penanggung_jawab', 'like', 'G%')->get();

    $activeCounts = [];
    $returnedCounts = [];
    foreach ($loans as $loan) {
      $guruNo = substr($loan->penanggung_jawab, 1);
      if ($loan->isDikembalikan()) {
        $returnedCounts[$guruNo] = ($returnedCounts[$guruNo] ?? 0) + 1;
      } else {
        $activeCounts[$guruNo] = ($activeCounts[$guruNo] ?? 0) + 1;
      }
    }

    $gurus = $gurus->map(function ($g) use ($activeCounts, $returnedCounts) {
      $g['buku_dipinjam_aktif'] = $activeCounts[$g['guru_no']] ?? 0;
      $g['buku_sudah_kembali'] = $returnedCounts[$g['guru_no']] ?? 0;
      return $g;
    });

    $perPage = 20;
    $page = (int) $request->get('page', 1);
    $paginated = $gurus->forPage($page, $perPage)->values();

    return Inertia::render('Guru/Index', [
      'gurus' => [
        'data' => $paginated,
        'current_page' => $page,
        'last_page' => (int) ceil($gurus->count() / $perPage),
        'total' => $gurus->count(),
      ],
      'filters' => $request->only('search'),
    ]);
  }

  public function show(string $guruNo)
  {
    $guru = $this->guruService->find($guruNo);

    if (! $guru) {
      abort(404, 'Guru tidak ditemukan.');
    }

    $riwayat = PeminjamanBuku::with(['buku' => fn($q) => $q->withTrashed()])
      ->where('penanggung_jawab', 'G' . $guruNo)
      ->orderByDesc('id_peminjaman')
      ->get();

    return Inertia::render('Guru/Show', [
      'guru' => $guru,
      'riwayatPeminjaman' => $riwayat,
    ]);
  }

  public function refresh()
  {
    $this->guruService->refresh();

    return redirect()
      ->route('guru.index')
      ->with('success', 'Data guru berhasil diperbarui dari server.');
  }
}
