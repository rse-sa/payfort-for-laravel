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

        if (! $this->paymentStatus) {
            throw (new PaymentFailed($this->getResponseCode() . " - " . $this->getResponseMessage(), $this->getResponseCode()))
                ->setResponse($this->getResponse());
        }
    }

    public function isActionSuccessful(): bool
    {
        return $this->paymentStatus;
    }

    public function isActionFailed(): bool
    {
        return ! $this->paymentStatus;
    }
}
