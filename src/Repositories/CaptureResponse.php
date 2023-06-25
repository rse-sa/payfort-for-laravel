<?php

namespace RSE\PayfortForLaravel\Repositories;

use RSE\PayfortForLaravel\Traits\RepositoryHelpers;

class CaptureResponse
{
    use RepositoryHelpers;

    protected array $payload = [];

    public function __construct(array $data)
    {
        $this->payload = $data;
    }

    /**
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data)
    {
        return (new static($data));
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
}
