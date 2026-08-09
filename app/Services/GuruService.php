<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GuruService
{
  const CACHE_KEY = 'guru_list';
  const API_URL = 'https://saicponorogo.com/SIAM/PublicAPI/guru';

  public function all(): array
  {
    return Cache::rememberForever(self::CACHE_KEY, function () {
      return $this->fetchFromApi();
    });
  }

  public function refresh(): array
  {
    $data = $this->fetchFromApi();
    Cache::forever(self::CACHE_KEY, $data);

    return $data;
  }

  public function find(string $guruNo): ?array
  {
    return collect($this->all())->firstWhere('guru_no', $guruNo);
  }

  protected function fetchFromApi(): array
  {
    $response = Http::timeout(15)->get(self::API_URL);

    if (! $response->successful()) {
      return [];
    }

    return $response->json() ?? [];
  }
}
