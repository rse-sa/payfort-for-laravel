<?php

namespace RSE\PayfortForLaravel\Repositories;

class TokenizationResponse
{
    protected array $payload = [];

    public function __construct(array $data)
    {
        $this->payload = $data;
    }

    public static function fromArray(array $data): static
    {
        return (new self($data));
    }

    public function isSuccessful(): bool
    {
        return $this->getStatus() == '18';
    }

    public function getStatus(): string
    {
        return $this->payload['status'];
    }

    public function getResponseCode(): string
    {
        return $this->payload['response_code'];
    }

    public function getResponseMessage(): string
    {
        return $this->payload['response_message'];
    }

    public function getSignature(): string
    {
        return $this->payload['signature'];
    }

    public function getMerchantReference(): string
    {
        return $this->payload['merchant_reference'];
    }

    public function getTokenName(): string
    {
        return $this->payload['token_name'];
    }

    public function getCardBin()
    {
        return $this->payload['card_bin'];
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}
