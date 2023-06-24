<?php

namespace RSE\PayfortForLaravel\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class PaymentFailed extends Exception
{
    protected array $params = [];

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
        ], $this->getCode() ?: 500);
    }

    public function setResponse(array $responseParams)
    {
        $this->params = $responseParams;

        return $this;
    }

    public function getResponse()
    {
        return $this->params;
    }

    public function getResponseCode(): ?string
    {
        return $this->params['response_code'] ?? null;
    }

    public function getResponseMessage(): ?string
    {
        return $this->params['response_message'] ?? null;
    }

    public function getResponseCodeAndMessage(): string
    {
        return $this->getResponseCode() . ' :: ' . $this->getResponseMessage();
    }
}
