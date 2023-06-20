<?php

namespace RSE\PayfortForLaravel\Services;

use RSE\PayfortForLaravel\Exceptions\RequestFailed;
use RSE\PayfortForLaravel\Repositories\Payfort;
use RSE\PayfortForLaravel\Traits\FortParams;
use RSE\PayfortForLaravel\Traits\PaymentResponseHelpers;
use RSE\PayfortForLaravel\Traits\ResponseHelpers;

class CaptureService extends Payfort
{
    use ResponseHelpers, FortParams, PaymentResponseHelpers;

    /**
     * @throws \Exception|\Throwable
     */
    public function handle(): self
    {
        $request = [
            'command' => 'CAPTURE',
            'access_code' => $this->merchant['access_code'],
            'merchant_identifier' => $this->merchant['merchant_identifier'],
            'amount' => $this->convertFortAmount($this->amount),
            'currency' => $this->currency,
            'language' => $this->language,
            'fort_id' => $this->fort_id,
        ];

        $request = array_merge($request, $this->merchant_extras);

        // calculating signature
        $request['signature'] = $this->calculateSignature($request);

        $this->response = $this->callApi($request, $this->getOperationUrl());

        $this->setFortParams($this->response);

        $this->validateFortParams();

        if (! $this->isSuccessful($this->getResponseCode())) {
            throw new RequestFailed($this->getResponseCode() . " - " . $this->getResponseMessage());
        }

        $this->validatePaymentResponseCode();

        return $this;
    }

    private function isSuccessful($response_code): bool
    {
        return str_starts_with($response_code, '04') && substr($response_code, 2) === '000';
    }
}
