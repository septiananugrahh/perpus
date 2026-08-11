<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PeminjamanBuku extends Model
{
  protected $table = 'tbl_peminjaman_buku';
  protected $primaryKey = 'id_peminjaman';

  // Batas waktu peminjaman dalam hari. Ubah nilai ini saja kalau kebijakan berubah.
  const BATAS_HARI_PINJAM = 14;

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

  /**
   * Parsing tanggal fleksibel karena data lama formatnya tidak konsisten
   * (contoh: "27-09-2024 7:10" dan "7-10-2024 13:7").
   */
  public static function parseTanggal(?string $value): ?Carbon
  {
    if (empty($value)) {
      return null;
    }

    $formats = ['d-m-Y H:i', 'j-n-Y G:i', 'd-m-Y', 'j-n-Y'];

    foreach ($formats as $format) {
      try {
        return Carbon::createFromFormat($format, trim($value));
      } catch (\Exception $e) {
        continue;
      }
    }

    return null;
  }

  /**
   * Sisa hari sebelum jatuh tempo. Negatif = sudah terlambat.
   * Null kalau tanggal pinjam tidak bisa di-parse atau buku sudah dikembalikan.
   */
  public function sisaHari(): ?int
  {
    if ($this->isDikembalikan()) {
      return null;
    }

    $tglPinjam = self::parseTanggal($this->tgl_pinjam);
    if (! $tglPinjam) {
      return null;
    }

    $jatuhTempo = $tglPinjam->copy()->addDays(self::BATAS_HARI_PINJAM);

    return (int) now()->diffInDays($jatuhTempo, false);
  }
}
