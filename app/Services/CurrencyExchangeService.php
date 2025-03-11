<?php

namespace App\Services;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

class CurrencyExchangeService
{
    private BigDecimal $commission;

    /**
     * Конструктор сервиса обмена валют
     *
     * @param CurrencyRatesApiService $ratesApiService
     */
    public function __construct(private CurrencyRatesApiService $ratesApiService)
    {
        $this->commission = BigDecimal::of('0.02');
    }

    /**
     * Получить все курсы валют с учетом комиссии
     *
     * @param array $currencies Список валют для фильтрации (опционально)
     * @return array
     */
    public function getAllRatesWithCommission(array $currencies = []): array
    {
        $data = $this->ratesApiService->getRates();
        $rates = [];

        foreach ($data as $currency) {
            $symbol = $currency['symbol'];
            $rateUsd = BigDecimal::of($currency['rateUsd']);

            $rateWithCommission = $this->applyCommission($rateUsd);

            $rates[$symbol] = $rateWithCommission->toScale(10, RoundingMode::HALF_UP)->toFloat();
        }

        if (!empty($currencies)) {
            $rates = array_intersect_key($rates, array_flip($currencies));
        }

        asort($rates, SORT_NUMERIC);

        return $rates;
    }

    /**
     * Конвертировать валюту с учетом комиссии
     *
     * @param string $currencyFrom Символ исходной валюты
     * @param string $currencyTo Символ целевой валюты
     * @param string $value Сумма для конвертации
     * @return array
     */
    public function convert(string $currencyFrom, string $currencyTo, string $value): array
    {
        $valueDecimal = BigDecimal::of($value);

        $minValue = BigDecimal::of('0.01');
        if ($valueDecimal->isLessThan($minValue)) {
            throw new \InvalidArgumentException('Minimum amount for conversion: 0.01');
        }

        $fromCurrencyData = $this->ratesApiService->getRateByCurrency($currencyFrom);
        $toCurrencyData = $this->ratesApiService->getRateByCurrency($currencyTo);

        if (!$fromCurrencyData || !$toCurrencyData) {
            throw new \InvalidArgumentException('Unknown currency');
        }

        $rateFrom = BigDecimal::of($fromCurrencyData['rateUsd']);
        $rateTo = BigDecimal::of($toCurrencyData['rateUsd']);

        $conversionRate = $this->calculateConversionRate($rateFrom, $rateTo);

        $convertedValue = $valueDecimal->multipliedBy($conversionRate);

        if ($currencyTo === 'USD') {
            $convertedValue = $convertedValue->toScale(2, RoundingMode::HALF_UP);
        } else {
            $convertedValue = $convertedValue->toScale(10, RoundingMode::HALF_UP);
        }

        return [
            'currency_from' => $currencyFrom,
            'currency_to' => $currencyTo,
            'value' => $valueDecimal->toFloat(),
            'converted_value' => $convertedValue->toFloat(),
            'rate' => $conversionRate->toScale(10, RoundingMode::HALF_UP)->toFloat(),
        ];
    }

    /**
     * Применить комиссию к курсу
     *
     * @param BigDecimal $rate
     * @return BigDecimal
     */
    private function applyCommission(BigDecimal $rate): BigDecimal
    {
        $one = BigDecimal::of(1);
        $commissionFactor = $one->minus($this->commission);
        return $rate->multipliedBy($commissionFactor);
    }

    /**
     * Рассчитать курс конверсии с учетом комиссии
     *
     * @param BigDecimal $rateFrom
     * @param BigDecimal $rateTo
     * @return BigDecimal
     */
    private function calculateConversionRate(BigDecimal $rateFrom, BigDecimal $rateTo): BigDecimal
    {
        if ($rateFrom->isZero() || $rateFrom->isNegative() || $rateTo->isZero() || $rateTo->isNegative()) {
            throw new \InvalidArgumentException('The exchange rate must be greater than zero.');
        }

        $conversionRate = $rateTo->dividedBy($rateFrom, 20, RoundingMode::HALF_UP);

        return $this->applyCommission($conversionRate);
    }
}
