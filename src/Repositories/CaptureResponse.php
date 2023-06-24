<?php

namespace RSE\PayfortForLaravel\Repositories;

class CaptureResponse
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

    public function getResponseCode(): string
    {
        return $this->payload['response_code'];
    }

    public function getResponseMessage(): string
    {
        return $this->payload['response_message'];
    }

    public function getResponseStatusCode(): string
    {
        return $this->payload['status'] ?? substr($this->getResponseCode(), 0, 2);
    }

    public function getResponseMessageCode(): string
    {
        return substr($this->getResponseCode(), 2);
    }

    public function getSignature(): string
    {
        return $this->payload['signature'];
    }

    public function isCaptureSuccessful(): bool
    {
        return $this->getResponseStatusCode() == '04';
    }

    public function getMerchantReference(): string
    {
        return $this->payload['merchant_reference'];
    }

    public function getFortId(): string
    {
        return $this->payload['fort_id'];
    }

    public function getCurrency(): string
    {
        return $this->payload['currency'];
    }

    public function getCommandName(): string
    {
        return $this->payload['command'];
    }

    public function getAmount(): string
    {
        return $this->payload['amount'];
    }

    public function getAmountAsFloat(): float
    {
        return (float)$this->payload['amount'] / 100;
    }

    public function getOrderDescription(): ?string
    {
        return $this->payload['order_description'] ?? null;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getResponse(): array
    {
        return $this->getPayload();
    }

}
