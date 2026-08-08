<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndukBuku extends Model
{
  protected $table = 'tbl_indukbuku';
  protected $primaryKey = 'id_buku';

  protected $fillable = [
    'tanggalpembukuan_buku',
    'kode_subkategori',
    'judul_buku',
    'klasifikasi_buku',
    'penerbit_buku',
    'pengarang_buku',
    'keterangan_buku',
    'edisi_buku',
    'tahunterbit_buku',
    'kotaterbit_buku',
    'isbn_buku',
    'nomerpanggil_buku',
    'eksemplar_buku',
    'series_buku',
    'sumber_buku',
  ];

  // Label subkategori, dipakai di dropdown & tampilan
  public const SUBKATEGORI = [
    '1' => 'Buku Pelajaran',
    '2' => 'Buku Quran',
  ];
}
