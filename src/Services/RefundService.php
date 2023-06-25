<?php

namespace RSE\PayfortForLaravel\Services;

use RSE\PayfortForLaravel\Exceptions\PaymentFailed;
use RSE\PayfortForLaravel\Repositories\Payfort;
use RSE\PayfortForLaravel\Repositories\RefundResponse;
use RSE\PayfortForLaravel\Traits\ApiResponseHelpers;

class RefundService extends Payfort
{
    use ApiResponseHelpers;

    /**
     * @throws PaymentFailed
     */
    public function handle(): RefundResponse
    {
        $this->isTestingFortId($this->fort_id);

        $request = [
            'command' => 'REFUND',
            'access_code' => $this->merchant['access_code'],
            'merchant_identifier' => $this->merchant['merchant_identifier'],
            'language' => $this->language,
            'fort_id' => $this->fort_id,
            'currency' => $this->currency,
            'amount' => $this->convertFortAmount($this->amount),
            'order_description' => "REFUND",
        ];

        $request = array_merge($request, $this->merchant_extras);

        // calculating signature
        $request['signature'] = $this->calculateSignature($request);

        $this->response = $this->callApi($request, $this->getOperationUrl());

        if (! $this->isSuccessful($this->response['response_code'])) {
            throw (new PaymentFailed($this->response['response_code'] . " - " . $this->response['response_message']))
                ->setResponse($this->response);
        }

        return RefundResponse::fromArray($this->response);
    }

    private function isSuccessful($response_code): bool
    {
        return (str_starts_with($response_code, '06') && substr($response_code, 2) === '000')
            || (str_starts_with($response_code, '00') && substr($response_code, 2) === '773');
    }
}
