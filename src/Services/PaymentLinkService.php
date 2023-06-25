<?php

namespace RSE\PayfortForLaravel\Services;

use Illuminate\Support\Carbon;
use RSE\PayfortForLaravel\Exceptions\RequestFailed;
use RSE\PayfortForLaravel\Repositories\CaptureResponse;
use RSE\PayfortForLaravel\Repositories\Payfort;
use RSE\PayfortForLaravel\Repositories\PaymentLinkCreatedResponse;
use RSE\PayfortForLaravel\Traits\FortParams;
use RSE\PayfortForLaravel\Traits\PaymentResponseHelpers;
use RSE\PayfortForLaravel\Traits\ApiResponseHelpers;

class PaymentLinkService extends Payfort
{
    use ApiResponseHelpers, FortParams, PaymentResponseHelpers;

    protected Carbon $expiry_date;
    protected array $notification_type = ['EMAIL'];
    protected string $order_description = '';

    protected ?string $customer_mobile = null;
    protected ?string $customer_name = null;
    protected string $payment_command = 'PURCHASE';
    protected ?string $merchant_payment_id = null;

    /**
     * @return \RSE\PayfortForLaravel\Repositories\PaymentLinkCreatedResponse
     * @throws \RSE\PayfortForLaravel\Exceptions\PaymentFailed
     * @throws \RSE\PayfortForLaravel\Exceptions\RequestFailed
     */
    public function handle(): PaymentLinkCreatedResponse
    {
        $request = [
            'command' => 'PAYMENT_LINK',
            'access_code' => $this->merchant['access_code'],
            'merchant_identifier' => $this->merchant['merchant_identifier'],
            'merchant_reference' => $this->getMerchantReference(),
            'amount' => $this->convertFortAmount($this->amount),
            'currency' => $this->currency,
            'language' => $this->language,
            'customer_email' => $this->email,
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_mobile,
            'payment_link_id' => $this->merchant_payment_id,
            'request_expiry_date' => $this->expiry_date->format('c'),
            'notification_type' => implode(',', $this->notification_type),
            'order_description' => $this->order_description,
            'link_command' => $this->payment_command,
            'redirect_url' => $this->redirect_url,
        ];

        $request = array_merge($request, $this->merchant_extras);

        // calculating signature
        $request['signature'] = $this->calculateSignature($request);

        $this->response = $this->callApi($request, $this->getOperationUrl());

        $this->setFortParams($this->response);

        $this->validateFortParams();

        if (! $this->isSuccessful($this->getResponseCode())) {
            throw (new RequestFailed($this->getResponseCode() . " - " . $this->getResponseMessage()))->setResponse($this->response);
        }

        $this->validatePaymentResponseCode();

        return PaymentLinkCreatedResponse::fromArray($this->response);
    }

    public function setPaymentCommand($command = 'PURCHASE'): self
    {
        $this->payment_command = $command;

        return $this;
    }

    public function setExpiryDate(Carbon $date): self
    {
        $this->expiry_date = $date;

        return $this;
    }

    public function setNotificationType(array $types): self
    {
        $this->notification_type = $types;

        return $this;
    }

    public function setOrderDescription(string $description): self
    {
        $this->order_description = $description;

        return $this;
    }

    public function setCustomerMobile(string $mobile): self
    {
        $this->customer_mobile = $mobile;

        return $this;
    }

    public function setCustomerName(string $name): self
    {
        $this->customer_name = $name;

        return $this;
    }

    public function setPaymentId(string $payment_id): self
    {
        $this->merchant_payment_id = $payment_id;

        return $this;
    }

    private function isSuccessful($response_code): bool
    {
        return str_starts_with($response_code, '48') && substr($response_code, 2) === '000';
    }
}
