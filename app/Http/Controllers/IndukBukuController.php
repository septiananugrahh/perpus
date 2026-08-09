<?php

namespace App\Http\Controllers;

use App\Models\IndukBuku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class IndukBukuController extends Controller
{
    // Urutan kolom ini dipakai konsisten di: template excel, parsing upload, dan validasi
    protected array $columns = [
        'tanggalpembukuan_buku' => 'Tanggal Pembukuan (YYYY-MM-DD)',
        'kode_subkategori' => 'Kode Subkategori (1=Pelajaran, 2=Quran)',
        'judul_buku' => 'Judul Buku',
        'klasifikasi_buku' => 'Klasifikasi',
        'penerbit_buku' => 'Penerbit',
        'pengarang_buku' => 'Pengarang',
        'keterangan_buku' => 'Keterangan',
        'edisi_buku' => 'Edisi',
        'tahunterbit_buku' => 'Tahun Terbit',
        'kotaterbit_buku' => 'Kota Terbit',
        'isbn_buku' => 'ISBN',
        'nomerpanggil_buku' => 'Nomor Panggil',
        'eksemplar_buku' => 'Eksemplar',
        'series_buku' => 'Series',
        'sumber_buku' => 'Sumber',
    ];

    public function index(Request $request)
    {
        $query = IndukBuku::query();

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('judul_buku', 'like', "%{$search}%")
                    ->orWhere('pengarang_buku', 'like', "%{$search}%")
                    ->orWhere('penerbit_buku', 'like', "%{$search}%")
                    ->orWhere('isbn_buku', 'like', "%{$search}%")
                    ->orWhere('nomerpanggil_buku', 'like', "%{$search}%");
            });
        }

        return Inertia::render('Buku/Index', [
            'bukus' => $query->orderByDesc('id_buku')->paginate(20)->withQueryString(),
            'subkategori' => IndukBuku::SUBKATEGORI,
            'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Buku/Input', [
            'subkategori' => IndukBuku::SUBKATEGORI,
        ]);
    }

    // Aturan validasi per baris, dipakai ulang untuk input manual & excel
    protected function rowRules(): array
    {
        return [
            'tanggalpembukuan_buku' => ['required', 'string', 'max:50'],
            'kode_subkategori' => ['nullable', 'string', 'max:50'],
            'judul_buku' => ['required', 'string'],
            'klasifikasi_buku' => ['required', 'string', 'max:50'],
            'penerbit_buku' => ['required', 'string', 'max:50'],
            'pengarang_buku' => ['required', 'string', 'max:50'],
            'keterangan_buku' => ['nullable', 'string', 'max:50'],
            'edisi_buku' => ['nullable', 'string', 'max:50'],
            'tahunterbit_buku' => ['required', 'string', 'max:10'],
            'kotaterbit_buku' => ['required', 'string', 'max:20'],
            'isbn_buku' => ['nullable', 'string', 'max:50'],
            'nomerpanggil_buku' => ['required', 'string', 'max:50'],
            'eksemplar_buku' => ['nullable', 'string', 'max:20'],
            'series_buku' => ['nullable', 'string', 'max:20'],
            'sumber_buku' => ['nullable', 'string', 'max:20'],
        ];
    }

    // Bulk insert — dipakai baik dari input manual maupun setelah preview excel di-konfirmasi
    public function store(Request $request)
    {
        $rules = ['rows' => ['required', 'array', 'min:1']];
        foreach ($this->rowRules() as $field => $fieldRules) {
            $rules["rows.*.{$field}"] = $fieldRules;
        }

        $validated = $request->validate($rules);

        foreach ($validated['rows'] as $row) {
            IndukBuku::create($row);
        }

        return redirect()
            ->route('buku.index')
            ->with('success', count($validated['rows']) . ' buku berhasil ditambahkan.');
    }

    // Download template excel kosong (+ contoh baris & legend subkategori)
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Buku');
        $sheet->fromArray(array_values($this->columns), null, 'A1');
        $sheet->fromArray([
            '2024-08-27',
            '1',
            'Contoh Judul Buku',
            '2X1.12',
            'Penerbit Contoh',
            'Nama Pengarang',
            'Penulis',
            '1',
            '2024',
            'Ponorogo',
            '978-000-000',
            '2X1.12 CON c',
            '1',
            '',
            'Hibah',
        ], null, 'A2');
        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $legend = $spreadsheet->createSheet();
        $legend->setTitle('Legend Subkategori');
        $legend->fromArray([['Kode', 'Label']], null, 'A1');
        $row = 2;
        foreach (IndukBuku::SUBKATEGORI as $code => $label) {
            $legend->fromArray([$code, $label], null, "A{$row}");
            $row++;
        }
        $legend->getColumnDimension('A')->setAutoSize(true);
        $legend->getColumnDimension('B')->setAutoSize(true);

        $spreadsheet->setActiveSheetIndex(0);

        $fileName = 'template-induk-buku.xlsx';
        $tempPath = tempnam(sys_get_temp_dir(), 'tpl') . '.xlsx';
        (new Xlsx($spreadsheet))->save($tempPath);

        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
    }

    // Parse file excel yang diupload user, validasi tiap baris, TIDAK menyimpan ke DB.
    // Frontend menampilkan hasil ini sebagai preview; simpan sebenarnya lewat endpoint store().
    public function uploadPreview(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ]);

        $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
        $sheet = $spreadsheet->getSheetByName('Data Buku') ?? $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, true, true, false);

        // Baris pertama = header, dilewati
        $dataRows = array_slice($data, 1);
        $fields = array_keys($this->columns);

        $rows = [];
        $errors = [];
        $hasError = false;

        foreach ($dataRows as $i => $cells) {
            // lewati baris yang benar-benar kosong semua
            if (count(array_filter($cells, fn($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $row = [];
            foreach ($fields as $index => $field) {
                $row[$field] = trim((string) ($cells[$index] ?? ''));
            }

            $validator = Validator::make($row, $this->rowRules());

            $rowIndex = count($rows); // index sesuai urutan tampil di preview
            $rows[] = $row;

            if ($validator->fails()) {
                $hasError = true;
                $errors[$rowIndex] = $validator->errors()->all();
            }
        }

        return response()->json([
            'rows' => $rows,
            'errors' => $errors,
            'valid' => ! $hasError && count($rows) > 0,
        ]);
    }

    // Hapus 1 buku (soft delete — masih ada di DB, tapi hilang dari listing)
    public function destroy(IndukBuku $buku)
    {
        $judul = $buku->judul_buku;
        $buku->delete();

        return redirect()
            ->route('buku.index')
            ->with('success', "Buku \"{$judul}\" berhasil dihapus.");
    }

    // Hapus banyak buku sekaligus (dari checkbox multi-select)
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $count = IndukBuku::whereIn('id_buku', $validated['ids'])->count();
        IndukBuku::whereIn('id_buku', $validated['ids'])->delete();

        return redirect()
            ->route('buku.index')
            ->with('success', "{$count} buku berhasil dihapus.");
    }
}
