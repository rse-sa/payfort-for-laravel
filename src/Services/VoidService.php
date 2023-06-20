<?php

namespace RSE\PayfortForLaravel\Services;

use RSE\PayfortForLaravel\Exceptions\PaymentFailed;
use RSE\PayfortForLaravel\Repositories\Payfort;
use RSE\PayfortForLaravel\Traits\ResponseHelpers;

class VoidService extends Payfort
{
    use ResponseHelpers;

    /**
     * @throws PaymentFailed
     */
    public function handle(): self
    {
        $request = [
            'command' => 'VOID_AUTHORIZATION',
            'access_code' => $this->merchant['access_code'],
            'merchant_identifier' => $this->merchant['merchant_identifier'],
            'language' => $this->language,
            'fort_id' => $this->fort_id,
        ];

        $request = array_merge($request, $this->merchant_extras);

        $request['signature'] = $this->calculateSignature($request);

        $this->response = $response = $this->callApi($request, $this->getOperationUrl());

        if(! $this->isSuccessful($this->response['response_code'])){
            throw (new PaymentFailed($this->response['response_code'] . " - " . $this->response['response_message']))
                ->setResponse($response);
        }

        return $this;
    }

    private function isSuccessful($response_code): bool
    {
        return str_starts_with($response_code, '08') && substr($response_code, 2) === '000';
    }
}
