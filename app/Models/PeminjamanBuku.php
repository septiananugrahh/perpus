<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PeminjamanBuku extends Model
{
  protected $table = 'tbl_peminjaman_buku';
  protected $primaryKey = 'id_peminjaman';

  protected $fillable = [
    'id_barang',
    'tgl_pinjam',
    'tgl_kembali',
    'penanggung_jawab',
    'keterangan_peminjaman',
  ];

  // Buku dianggap belum dikembalikan kalau tgl_kembali kosong/default
  public function scopeBelumDikembalikan(Builder $query): Builder
  {
    return $query->where(function ($q) {
      $q->whereNull('tgl_kembali')->orWhere('tgl_kembali', '');
    });
  }

  public function scopeSudahDikembalikan(Builder $query): Builder
  {
    return $query->whereNotNull('tgl_kembali')->where('tgl_kembali', '!=', '');
  }

  public function isDikembalikan(): bool
  {
    return ! empty($this->tgl_kembali);
  }

  // penanggung_jawab numerik = ID Santri, prefix "G" = ID Guru (API belum tersedia)
  public function isPeminjamGuru(): bool
  {
    return str_starts_with((string) $this->penanggung_jawab, 'G');
  }

  public function idPeminjamNumerik(): string
  {
    return $this->isPeminjamGuru()
      ? substr($this->penanggung_jawab, 1)
      : $this->penanggung_jawab;
  }

  public function buku()
  {
    return $this->belongsTo(IndukBuku::class, 'id_barang', 'id_buku');
  }
}
