<?php

namespace RSE\PayfortForLaravel\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class PaymentFailed extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
        ], $this->getCode() ?: 500);
    }
}
