<?php

namespace RSE\PayfortForLaravel\Services;

use RSE\PayfortForLaravel\Exceptions\RequestFailed;
use RSE\PayfortForLaravel\Repositories\Payfort;
use RSE\PayfortForLaravel\Repositories\StatusResponse;
use RSE\PayfortForLaravel\Traits\PaymentResponseHelpers;
use RSE\PayfortForLaravel\Traits\ResponseHelpers;

class CheckStatusService extends Payfort
{
    use ResponseHelpers;

    protected $fort_params = [];

    /**
     * @throws RequestFailed
     */
    public function handle(): StatusResponse
    {
        $request = [
            'query_command' => 'CHECK_STATUS',
            'access_code' => $this->merchant['access_code'],
            'merchant_identifier' => $this->merchant['merchant_identifier'],
            'language' => $this->language,
            'fort_id' => $this->fort_id,
        ];

        // calculating signature
        $request['signature'] = $this->calculateSignature($request);

        $this->response = $this->callApi($request, $this->getOperationUrl(), false);

        $this->fort_params = $this->response;

        if (! $this->isSuccessful($this->getResponseCode())) {
            throw (new RequestFailed($this->getResponseCode() . " - " . $this->getResponseMessage()))
                ->setResponse($this->response);
        }

        return StatusResponse::fromArray($this->response);
    }

    private function isSuccessful($response_code): bool
    {
        return str_starts_with($response_code, '12') && substr($response_code, 2) === '000';
    }
}
