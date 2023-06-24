<?php

namespace RSE\PayfortForLaravel\Services;

use RSE\PayfortForLaravel\Events\PayfortMessageLog;
use RSE\PayfortForLaravel\Repositories\Payfort;
use RSE\PayfortForLaravel\Repositories\PurchaseResponse;
use RSE\PayfortForLaravel\Traits\FortParams;
use RSE\PayfortForLaravel\Traits\PaymentResponseHelpers;
use RSE\PayfortForLaravel\Traits\ResponseHelpers;
use RSE\PayfortForLaravel\Traits\Signature;

class ValidatePostResponse extends Payfort
{
    use FortParams, ResponseHelpers, Signature, PaymentResponseHelpers;

    protected $fort_params = [];

    /**
     * @throws \RSE\PayfortForLaravel\Exceptions\PaymentFailed|\RSE\PayfortForLaravel\Exceptions\RequestFailed
     */
    public function handle(): PurchaseResponse
    {
        $this->validateFortParams();

        PayfortMessageLog::dispatch(null, $this->fort_params);

        $this->validatePaymentResponseCode();

        $this->validateSignature();

        $this->response = $this->fort_params;

        return PurchaseResponse::fromArray($this->response);
    }
}
