<?php

namespace RSE\PayfortForLaravel\Services;

use RSE\PayfortForLaravel\Events\PayfortMessageLog;
use RSE\PayfortForLaravel\Exceptions\PaymentFailed;
use RSE\PayfortForLaravel\Repositories\Payfort;
use RSE\PayfortForLaravel\Repositories\PurchaseResponse;
use RSE\PayfortForLaravel\Repositories\TokenizationResponse;
use RSE\PayfortForLaravel\Traits\FortParams;
use RSE\PayfortForLaravel\Traits\PaymentResponseHelpers;
use RSE\PayfortForLaravel\Traits\ApiResponseHelpers;
use RSE\PayfortForLaravel\Traits\Signature;

class ValidateTokenizationResponse extends Payfort
{
    use FortParams, ApiResponseHelpers, Signature, PaymentResponseHelpers;

    protected $fort_params = [];

    /**
     * @throws \RSE\PayfortForLaravel\Exceptions\PaymentFailed|\RSE\PayfortForLaravel\Exceptions\RequestFailed
     */
    public function handle(): TokenizationResponse
    {
        $this->validateFortParams();

        PayfortMessageLog::dispatch(null, $this->fort_params);

        $this->validateSignature();

        $this->validatePaymentResponseCode();

        $this->response = $this->fort_params;

        if(! $this->isSuccessful($this->response['response_code'])){
            throw (new PaymentFailed($this->response['response_code'] . " - " . $this->response['response_message']))->setResponse($this->response);
        }

        return TokenizationResponse::fromArray($this->response);
    }

    public function isSuccessful(string $response_code): bool
    {
        return $response_code == '18';
    }
}
