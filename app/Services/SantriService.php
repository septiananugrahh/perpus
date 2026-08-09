<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SantriService
{
  // Tahun ajar di-hardcode dulu sesuai permintaan, nanti bisa dipindah ke .env/config
  const TAHUN_AJAR = '2025/2026';

  const CACHE_KEY = 'santri_list_' . self::TAHUN_AJAR;
  const API_URL = 'https://saicponorogo.com/SIAM/admin/SiswaPublic/all';

  /**
   * Ambil daftar santri, dari cache kalau ada (cache permanen sampai di-refresh manual).
   */
  public function all(): array
  {
    return Cache::rememberForever(self::CACHE_KEY, function () {
      return $this->fetchFromApi();
    });
  }

  /**
   * Paksa ambil ulang dari API dan replace cache (dipanggil tombol "Refresh Data Santri").
   */
  public function refresh(): array
  {
    $data = $this->fetchFromApi();
    Cache::forever(self::CACHE_KEY, $data);

    return $data;
  }

  /**
   * Cari 1 santri berdasarkan ID.
   */
  public function find(string $id): ?array
  {
    return collect($this->all())->firstWhere('id', $id);
  }

  protected function fetchFromApi(): array
  {
    $response = Http::timeout(15)->get(self::API_URL, [
      'tahun_ajar' => self::TAHUN_AJAR,
    ]);

    if (! $response->successful()) {
      return [];
    }

    $raw = $response->json() ?? [];

    // API mengembalikan duplikat per semester (ganjil/genap) untuk santri yang sama.
    // Dedupe berdasarkan "id", ambil kemunculan pertama.
    return collect($raw)
      ->unique('id')
      ->values()
      ->all();
  }
}
