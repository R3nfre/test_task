<?php

namespace App\Http\Controllers;

use App\Services\CurrencyExchangeService;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CurrencyController extends Controller
{
    public function __construct(
        protected CurrencyExchangeService $currencyExchangeService
    ) {
    }

    /**
     * Обработка всех API запросов
     */
    public function handleRequest(Request $request): JsonResponse
    {
        $method = $request->input('method');

        if (!$method) {
            return $this->errorResponse('Method parameter is required', 400);
        }

        try {
            return match ($method) {
                'rates' => $this->getRates($request),
                'convert' => $this->convertCurrency($request),
                default => $this->errorResponse(400, 'Unknown method'),
            };
        } catch (\Exception $e) {
            return $this->errorResponse(400, $e->getMessage());
        }
    }

    /**
     * Получить курсы валют
     */
    private function getRates(Request $request): JsonResponse
    {
        $currency = $request->input('currency');
        $currencies = $currency ? explode(',', $currency) : [];

        $rates = $this->currencyExchangeService->getAllRatesWithCommission($currencies);

        return $this->showResponse([
            'data' => $rates
        ]);
    }

    /**
     * Конвертировать валюту
     */
    private function convertCurrency(Request $request): JsonResponse
    {
        $validator = validator($request->all(), [
            'currency_from' => 'required|string',
            'currency_to' => 'required|string',
            'value' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 400);
        }

        $currencyFrom = $request->input('currency_from');
        $currencyTo = $request->input('currency_to');
        $value = $request->input('value');

        $result = $this->currencyExchangeService->convert($currencyFrom, $currencyTo, $value);

        return $this->showResponse([
            'data' => $result
        ]);
    }
}
