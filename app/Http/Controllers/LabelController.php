<?php

namespace App\Http\Controllers;

use App\Models\IndukBuku;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Picqer\Barcode\BarcodeGeneratorHTML;

class LabelController extends Controller
{
    // Prefix barcode sama seperti aplikasi lama
    const BARCODE_PREFIX = 'SAIC';

    // Ganti sesuai nama instansi/perpustakaan kamu
    const NAMA_PERPUSTAKAAN = 'MI ALAM ISLAMIC CENTER PONOROGO';

    public function index(Request $request)
    {
        // Ambil tahun unik dari tanggalpembukuan_buku (format dd-mm-yyyy)
        $tahunList = IndukBuku::selectRaw('DISTINCT SUBSTRING(tanggalpembukuan_buku, -4) as tahun')
            ->whereNotNull('tanggalpembukuan_buku')
            ->where('tanggalpembukuan_buku', '!=', '')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->filter();

        $query = IndukBuku::query();

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('judul_buku', 'like', "%{$search}%")
                    ->orWhere('pengarang_buku', 'like', "%{$search}%")
                    ->orWhere('nomerpanggil_buku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereRaw("STR_TO_DATE(tanggalpembukuan_buku, '%d-%m-%Y') >= STR_TO_DATE(?, '%d-%m-%Y')", [$request->string('tanggal_mulai')]);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereRaw("STR_TO_DATE(tanggalpembukuan_buku, '%d-%m-%Y') <= STR_TO_DATE(?, '%d-%m-%Y')", [$request->string('tanggal_selesai')]);
        }
        if ($request->filled('tahun')) {
            $query->whereRaw("SUBSTRING(tanggalpembukuan_buku, -4) = ?", [$request->string('tahun')]);
        }
        return Inertia::render('Label/Index', [
            'bukus' => $query->orderBy('id_buku')->paginate(20)->withQueryString(),
            'filters' => $request->only('search', 'tanggal_mulai', 'tanggal_selesai', 'tahun'),
            'tahunList' => $tahunList,
        ]);
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['nullable', 'array'],
            'ids.*' => ['integer'],
            'export_awal' => ['nullable', 'integer'],
            'export_akhir' => ['nullable', 'integer'],
        ]);

        $ids = collect($validated['ids'] ?? [])->filter()->values();

        if ($ids->isNotEmpty()) {
            // Mode pilih manual (checkbox)
            $bukus = IndukBuku::whereIn('id_buku', $ids)->orderBy('id_buku')->get();
        } else {
            // Mode rentang ID
            $awal = $validated['export_awal'] ?? null;
            $akhir = $validated['export_akhir'] ?? null;

            if (! $awal || ! $akhir) {
                return back()->withErrors(['ids' => 'Pilih buku secara manual atau isi rentang ID awal & akhir.']);
            }

            $bukus = IndukBuku::whereBetween('id_buku', [$awal, $akhir])->orderBy('id_buku')->get();
        }

        if ($bukus->isEmpty()) {
            return back()->withErrors(['ids' => 'Tidak ada buku ditemukan untuk kriteria tersebut.']);
        }

        $pdf = Pdf::loadView('pdf.label', [
            'bukus' => $bukus,
            'prefix' => self::BARCODE_PREFIX,
            'namaPerpustakaan' => self::NAMA_PERPUSTAKAAN,
            'generator' => new BarcodeGeneratorHTML(),
        ])->setPaper([0, 0, 609.4, 935.4], 'portrait'); // ukuran F4 dalam pt

        return $pdf->stream('Label Perpus.pdf');
    }
}