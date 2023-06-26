<?php

namespace RSE\PayfortForLaravel\Traits;

/**
 * response code
 */
trait ApiResponseHelpers
{
    protected ?bool $requestStatus = null;

    protected function setRequestResponseCode(): void
    {
        $this->requestStatus = substr($this->fort_params['response_code'], 2) == '000';
    }

    public function isRequestSuccessful(): bool
    {
        return $this->requestStatus;
    }

    public function isRequestFailed(): bool
    {
        return ! $this->requestStatus;
    }

    public function getResponseFortId(): ?string
    {
        return $this->fort_params['fort_id'] ?? null;
    }

    public function getResponseCode(): string
    {
        return $this->fort_params['acquirer_response_code'] ?? $this->fort_params['response_code'];
    }

    public function isResponseSuccessful(): bool
    {
        return $this->getResponseMessageCode() == '000';
    }

    public function getResponseStatusCode(): string
    {
        return substr($this->getResponseCode(), 0, 2);
    }

    public function getResponseMessageCode(): string
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
