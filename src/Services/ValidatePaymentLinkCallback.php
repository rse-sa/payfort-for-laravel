<?php

namespace RSE\PayfortForLaravel\Services;

use RSE\PayfortForLaravel\Events\PayfortMessageLog;
use RSE\PayfortForLaravel\Repositories\Payfort;
use RSE\PayfortForLaravel\Repositories\PaymentLinkCallbackResponse;
use RSE\PayfortForLaravel\Repositories\PurchaseResponse;
use RSE\PayfortForLaravel\Traits\FortParams;
use RSE\PayfortForLaravel\Traits\PaymentResponseHelpers;
use RSE\PayfortForLaravel\Traits\ApiResponseHelpers;
use RSE\PayfortForLaravel\Traits\Signature;

class ValidatePaymentLinkCallback extends Payfort
{
    use FortParams, ApiResponseHelpers, Signature, PaymentResponseHelpers;

    protected $fort_params = [];

    /**
     * @throws \RSE\PayfortForLaravel\Exceptions\PaymentFailed|\RSE\PayfortForLaravel\Exceptions\RequestFailed
     */
    public function handle(): PaymentLinkCallbackResponse
    {
        $this->validateFortParams();

        PayfortMessageLog::dispatch(null, $this->fort_params);

        $this->validatePaymentResponseCode();

        $this->validateSignature();

        $this->response = $this->fort_params;

        return PaymentLinkCallbackResponse::fromArray($this->response);
    }
}
