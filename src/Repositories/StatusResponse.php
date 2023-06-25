<?php

namespace RSE\PayfortForLaravel\Repositories;

use RSE\PayfortForLaravel\Traits\RepositoryHelpers;

class StatusResponse
{
    use RepositoryHelpers;

    protected array $payload = [];

    public function __construct(array $data)
    {
        $this->payload = $data;
    }

    public static function fromArray(array $data): static
    {
        return (new self($data));
    }

    public function isPurchaseSuccessful(): bool
    {
        return $this->payload['transaction_status'] == '14';
    }

    public function getTransactionStatus(): string
    {
        return $this->payload['transaction_status'];
    }

    public function getTransactionCode(): string
    {
        return $this->payload['transaction_code'];
    }

    public function getTransactionMessage(): string
    {
        return $this->payload['transaction_message'];
    }

    public function getMerchantReference(): string
    {
        return $this->payload['merchant_reference'];
    }

    public function getFortId(): string
    {
        return $this->payload['fort_id'];
    }

    public function getRefundedAmount(): string
    {
        return $this->payload['refunded_amount'];
    }

    public function getCapturedAmount(): string
    {
        return $this->payload['captured_amount'];
    }

    public function getAuthorizedAmount(): string
    {
        return $this->payload['authorized_amount'];
    }

    public function getRefundedAmountAsFloat(): float
    {
        return (float) $this->payload['refunded_amount'] / 100;
    }

    public function getCapturedAmountAsFloat(): float
    {
        return (float) $this->payload['captured_amount'] / 100;
    }

    public function getAuthorizedAmountAsFloat(): float
    {
        return (float) $this->payload['authorized_amount'] / 100;
    }
}
