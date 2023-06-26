<?php

namespace RSE\PayfortForLaravel\Services;

use RSE\PayfortForLaravel\Events\PayfortMessageLog;
use RSE\PayfortForLaravel\Repositories\Payfort;
use RSE\PayfortForLaravel\Repositories\PurchaseResponse;
use RSE\PayfortForLaravel\Traits\ApiResponseHelpers;
use RSE\PayfortForLaravel\Traits\FortParams;
use RSE\PayfortForLaravel\Traits\Signature;

class ValidatePostResponse extends Payfort
{
    use FortParams, ApiResponseHelpers, Signature;

    protected $fort_params = [];

    /**
     * @throws \RSE\PayfortForLaravel\Exceptions\RequestFailed  if wrong parameters were sent, or wrong signature
     */
    public function handle(): PurchaseResponse
    {
        $this->validateFortParams();

        PayfortMessageLog::dispatch(null, $this->fort_params);

        $this->validateSignature();

        $this->setRequestResponseCode();

        $this->response = $this->fort_params;

        return PurchaseResponse::fromArray($this->response);
    }
}
