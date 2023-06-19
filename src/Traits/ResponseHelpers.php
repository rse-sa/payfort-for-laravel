<?php

namespace RSE\PayfortForLaravel\Traits;

use RSE\PayfortForLaravel\Exceptions\PaymentFailed;

/**
 * response code
 */
trait ResponseHelpers
{
    /**
     * @throws \RSE\PayfortForLaravel\Exceptions\PaymentFailed
     */
    protected function validateResponseCode(): self
    {
        if (substr($this->fort_params['response_code'], 2) != '000') {
            extract($this->fort_params);

            $code = $this->fort_params['acquirer_response_code'] ?? $this->fort_params['response_code'];
            $message = "{$this->fort_params['response_code']} - {$this->fort_params['response_message']}";

            throw new PaymentFailed($message, (string) $code);
        }

        return $this;
    }

    public function getResponseFortId(): string
    {
        return $this->fort_params['fort_id'];
    }

    public function getResponsePaymentMethod(): string
    {
        return $this->fort_params['payment_option'];
    }
}
