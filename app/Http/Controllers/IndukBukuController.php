<?php

namespace App\Http\Controllers;

use App\Models\IndukBuku;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IndukBukuController extends Controller
{
  public function index()
  {
    return Inertia::render('Buku/Index', [
      'bukus' => IndukBuku::orderByDesc('id_buku')->paginate(20),
      'subkategori' => IndukBuku::SUBKATEGORI,
    ]);
  }

  public function create()
  {
    return Inertia::render('Buku/Input', [
      'subkategori' => IndukBuku::SUBKATEGORI,
    ]);
  }

  // Bulk insert dari tampilan excel-style
  public function store(Request $request)
  {
    $validated = $request->validate([
      'rows' => ['required', 'array', 'min:1'],
      'rows.*.tanggalpembukuan_buku' => ['required', 'string', 'max:50'],
      'rows.*.kode_subkategori' => ['nullable', 'string', 'max:50'],
      'rows.*.judul_buku' => ['required', 'string'],
      'rows.*.klasifikasi_buku' => ['required', 'string', 'max:50'],
      'rows.*.penerbit_buku' => ['required', 'string', 'max:50'],
      'rows.*.pengarang_buku' => ['required', 'string', 'max:50'],
      'rows.*.keterangan_buku' => ['nullable', 'string', 'max:50'],
      'rows.*.edisi_buku' => ['nullable', 'string', 'max:50'],
      'rows.*.tahunterbit_buku' => ['required', 'string', 'max:10'],
      'rows.*.kotaterbit_buku' => ['required', 'string', 'max:20'],
      'rows.*.isbn_buku' => ['nullable', 'string', 'max:50'],
      'rows.*.nomerpanggil_buku' => ['required', 'string', 'max:50'],
      'rows.*.eksemplar_buku' => ['nullable', 'string', 'max:20'],
      'rows.*.series_buku' => ['nullable', 'string', 'max:20'],
      'rows.*.sumber_buku' => ['nullable', 'string', 'max:20'],
    ]);

    foreach ($validated['rows'] as $row) {
      IndukBuku::create($row);
    }

    return redirect()
      ->route('buku.index')
      ->with('success', count($validated['rows']) . ' buku berhasil ditambahkan.');
  }
}
