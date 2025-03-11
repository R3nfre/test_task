<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait MakesApiResponses
{
    /**
     * Standard error envelope structure
     *
     * @param int $code
     * @param string|null $message
     * @return array
     */
    private function errorEnvelope(
        int $code = 400,
        string $message = null,
    ): array {
        if (empty($message)) {
            $message = 'Server error';
        }

        return [
            /** Успешен ли запрос */
            'success' => false,
            /** HTTP-статус */
            'code' => $code,
            /** @var null|string Сообщение */
            'message' => $message,
        ];
    }

    /**
     * Standard success envelope structure
     *
     * @param int $code
     * @param array $data
     * @param string|null $message
     * @return array
     */
    private function successEnvelope(int $code = Response::HTTP_OK, mixed $data = []): array
    {
        return [
            /** Успешен ли запрос */
            'success' => true,
            /** HTTP-статус */
            'code' => $code,
            ...$data
        ];
    }

    /**
     * General multi-purpose response
     *
     * @param $errors
     * @param $data
     * @return JsonResponse
     */
    protected function response($errors, $data): JsonResponse
    {
        if (!empty($errors)) {
            return $this->errorResponse($errors);
        }

        return $this->showResponse($data);
    }

    /**
     * Return information for single resource
     *
     * @param mixed|array $data
     * @return JsonResponse
     */
    protected function showResponse(mixed $data = []): JsonResponse
    {
        $response = $this->successEnvelope(Response::HTTP_OK, $data);

        return response()->json($response);
    }

    protected function errorResponse(
        int $status = Response::HTTP_BAD_REQUEST,
        string $message = null,
    ): JsonResponse {
        $response = $this->errorEnvelope($status, $message);

        return response()->json($response, $status);
    }
}
