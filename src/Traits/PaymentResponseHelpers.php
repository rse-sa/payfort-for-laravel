<?php

namespace RSE\PayfortForLaravel\Traits;

use RSE\PayfortForLaravel\Exceptions\PaymentFailed;

/**
 * response code
 */
trait PaymentResponseHelpers
{
    protected ?bool $paymentStatus = null;


    /**
     * @throws \RSE\PayfortForLaravel\Exceptions\PaymentFailed
     */
    protected function validatePaymentResponseCode(): void
    {
        $this->paymentStatus = substr($this->fort_params['response_code'], 2) == '000';

        if ($this->throwOnError && ! $this->paymentStatus) {
            throw (new PaymentFailed($this->getResponseCode() . " - " . $this->getResponseMessage(), $this->getResponseCode()))
                ->setResponse($this->getResponse());
        }
    }

    public function isPaymentSuccessful(): bool
    {
        return $this->paymentStatus;
    }

    public function isPaymentFailed(): bool
    {
        return ! $this->paymentStatus;
    }

    public function getPaidAmount(): ?float
    {
        if ($this->isPaymentFailed()) {
            return null;
        }

        return $this->fort_params['amount'] / 100;
    }

    public function getCurrency(): ?string
    {
        if ($this->isPaymentFailed()) {
            return null;
        }

        return $this->fort_params['currency'];
    }
}
