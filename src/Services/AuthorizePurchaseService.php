<?php

namespace RSE\PayfortForLaravel\Services;

use Illuminate\Support\Facades\Validator;
use RSE\PayfortForLaravel\Events\PayfortMessageLog;
use RSE\PayfortForLaravel\Repositories\Payfort;
use RSE\PayfortForLaravel\Repositories\PurchaseResponse;
use RSE\PayfortForLaravel\Traits\FortParams;
use RSE\PayfortForLaravel\Traits\PaymentResponseHelpers;
use RSE\PayfortForLaravel\Traits\ApiResponseHelpers;
use RSE\PayfortForLaravel\Traits\Signature;

class AuthorizePurchaseService extends Payfort
{
    use FortParams, ApiResponseHelpers, Signature, PaymentResponseHelpers;

    protected $fort_params = [];

    protected string $command = "PURCHASE";

    /**
     * Installment Parameters
     */
    protected bool $has_insallments = false;

    protected string $installments_type = 'HOSTED';

    protected array $insallments_params = [];

    /**
     * @throws \RSE\PayfortForLaravel\Exceptions\PaymentFailed|\RSE\PayfortForLaravel\Exceptions\RequestFailed
     */

    public function handle(): PurchaseResponse
    {
        // check form params
        $this->validateFortParams();

        // Log tokenization response
        PayfortMessageLog::dispatch(null, $this->fort_params);

        $this->validateSignature();

        $this->validatePaymentResponseCode();

        $request = [
            'command' => $this->command,
            'merchant_reference' => $this->fort_params['merchant_reference'],
            'access_code' => $this->merchant['access_code'],
            'merchant_identifier' => $this->merchant['merchant_identifier'],
            'customer_ip' => request()->ip(),
            'currency' => $this->currency,
            'customer_email' => $this->email,
            'token_name' => $this->fort_params['token_name'],
            'language' => $this->language,
            'return_url' => $this->redirect_url,
            'amount' => $this->convertFortAmount($this->amount),
        ];

        if ($this->has_insallments) {
            $request = array_merge($request, $this->insallments_params);
        }

        $request = array_merge($request, $this->merchant_extras);

        if (isset($this->fort_params['3ds']) && $this->fort_params['3ds'] == 'no') {
            $request['check_3ds'] = 'NO';
        }

        //calculate request signature
        $signature = $this->calculateSignature($request);
        $request['signature'] = $signature;

        $this->response = $this->callApi($request, $this->getOperationUrl());

        // validate the response returned
        $this->setFortParams($this->response);
        $this->validateFortParams();
        $this->validateSignature();

        $response = PurchaseResponse::fromArray($this->response);

        if (! $response->should3DsRedirect()) {
            $this->validatePaymentResponseCode();
        }

        return $response;
    }

    public function setAuthorizationCommand(): self
    {
        $this->command = "AUTHORIZATION";

        return $this;
    }

    public function setInstallmentParams(array $params = []): self
    {
        if (count($params)) {
            // check installments params
            /** @var \Illuminate\Validation\Validator $validator */
            $validator = Validator::make($params, [
                'issuer_code' => 'required|alpha_num|max:8',
                'plan_code' => 'required|alpha_num|max:8',
            ]);

            if ($validator->passes()) {
                // set installments params
                $this->has_insallments = true;
                $this->insallments_params = $validator->validated();
                $this->insallments_params['installments'] = $this->installments_type;
            }
        }

        return $this;
    }

}
