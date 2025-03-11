<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CurrencyRatesApiService
{
    private string $apiUrl = 'https://api.coincap.io/v2/rates';

    /**
     * Получить все курсы валют из API
     *
     * @return array
     */
    public function getRates(): array
    {
        return Cache::remember('currency_rates', 300, function () {
            $response = Http::get($this->apiUrl);

            if ($response->successful()) {
                return $response->json()['data'];
            }

            return [];
        });
    }

    /**
     * Получить данные о курсах валют из API в ассоциативном массиве
     * с символами валют в качестве ключей
     *
     * @return array
     */
    public function getRatesAssociative(): array
    {
        $rates = $this->getRates();
        $result = [];

        foreach ($rates as $currency) {
            $result[$currency['symbol']] = $currency;
        }

        return $result;
    }

    /**
     * Получить курс определенной валюты
     *
     * @param string $symbol Символ валюты
     * @return array|null Данные о валюте или null, если валюта не найдена
     */
    public function getRateByCurrency(string $symbol): ?array
    {
        return Cache::remember("currency_rate_{$symbol}", 300, function () use ($symbol) {
            $response = Http::get("{$this->apiUrl}/{$symbol}");

            if ($response->successful() && isset($response->json()['data'])) {
                return $response->json()['data'];
            }

            $rates = $this->getRatesAssociative();
            return $rates[$symbol] ?? null;
        });
    }
}
