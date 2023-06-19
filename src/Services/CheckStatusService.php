<?php

namespace RSE\PayfortForLaravel\Services;

use RSE\PayfortForLaravel\Exceptions\RequestFailed;
use RSE\PayfortForLaravel\Repositories\Payfort;

class CheckStatusService extends Payfort
{
    /**
     * @throws \Exception|\Throwable
     */
    public function handle(): array
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

        throw_unless(
            $this->isSuccessful($this->response['response_code']),
            RequestFailed::class,
            "{$this->response['response_code']} - {$this->response['response_message']}"
        );

        return $this->response;
    }

    private function isSuccessful($response_code): bool
    {
        return str_starts_with($response_code, '12') && substr($response_code, 2) === '000';
    }
}
