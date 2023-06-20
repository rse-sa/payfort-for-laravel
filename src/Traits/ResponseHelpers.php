<?php

namespace RSE\PayfortForLaravel\Traits;

/**
 * response code
 */
trait ResponseHelpers
{
    public function getResponseFortId(): ?string
    {
        return $this->fort_params['fort_id'] ?? null;
    }

    public function getResponsePaymentMethod(): ?string
    {
        return $this->fort_params['payment_option'] ?? null;
    }

    public function getResponseCode(): string
    {
        return $this->fort_params['acquirer_response_code'] ?? $this->fort_params['response_code'];
    }

    public function isResponseSuccessful(): bool
    {
        return $this->getResponseMessageStatusCode() == '000';
    }

    public function getResponseStatusCode(): string
    {
        return substr($this->getResponseCode(), 0, 2);
    }

    public function getResponseMessageStatusCode(): string
    {
        return substr($this->getResponseCode(), 2);
    }

    public function getResponseMessage(): string
    {
        return $this->fort_params['response_message'];
    }

    public function getMerchantReference(): ?string
    {
        return $this->fort_params['merchant_reference'] ?? null;
    }

    public function getResponse(): array
    {
        return $this->fort_params;
    }
}
