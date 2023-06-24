<?php

namespace RSE\PayfortForLaravel\Repositories;

class StatusResponse
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

    public function isPurchaseSuccessful(): bool
    {
        return $this->payload['transaction_status'] == '14';
    }

    public function getResponseStatus(): string
    {
        return $this->payload['status'];
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

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getResponse(): array
    {
        return $this->getPayload();
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
