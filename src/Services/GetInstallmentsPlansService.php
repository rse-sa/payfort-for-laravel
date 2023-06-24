<?php

namespace RSE\PayfortForLaravel\Services;

use RSE\PayfortForLaravel\Exceptions\RequestFailed;
use RSE\PayfortForLaravel\Repositories\Payfort;
use RSE\PayfortForLaravel\Traits\ResponseHelpers;

class GetInstallmentsPlansService extends Payfort
{
    use ResponseHelpers;

    /**
     * @throws \RSE\PayfortForLaravel\Exceptions\RequestFailed
     */
    public function handle(): array
    {
        $request = [
            'query_command' => 'GET_INSTALLMENTS_PLANS',
            'access_code' => $this->merchant['access_code'],
            'merchant_identifier' => $this->merchant['merchant_identifier'],
            'language' => $this->language,
        ];

        // calculating signature
        $request['signature'] = $this->calculateSignature($request);

        $this->response = $this->callApi($request, $this->getOperationUrl(), false);

        if (! $this->isSuccessful($this->response['response_code'])) {
            throw (new RequestFailed($this->response['response_code'] . " - " . $this->response['response_message']))
                ->setResponse($this->response);
        }

        return $this->getInstallmentDetails();
    }

    private function isSuccessful($response_code): bool
    {
        return str_starts_with($response_code, '62') && substr($response_code, 2) === '000';
    }

    private function getInstallmentDetails(): array
    {
        return $this->response['installment_detail'];
    }
}
