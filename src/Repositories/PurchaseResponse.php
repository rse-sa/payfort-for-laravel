<?php

namespace RSE\PayfortForLaravel\Repositories;

class PurchaseResponse
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

    public function isPurchaseSuccessful(): bool
    {
        return $this->payload['transaction_status'] == '14';
    }

    public function getMerchantReference(): string
    {
        return $this->payload['merchant_reference'];
    }

    public function getCardNumber(): string
    {
        return $this->payload['card_number'];
    }

    public function getCardHolderName(): string
    {
        return $this->payload['card_holder_name'];
    }

    public function getTokenName(): ?string
    {
        return $this->payload['token_name'] ?? null;
    }

    public function getPaymentOption(): string
    {
        return $this->payload['payment_option'];
    }

    public function getExpiryDate(): string
    {
        return $this->payload['expiry_date'];
    }

    public function getCustomerIP(): string
    {
        return $this->payload['customer_ip'];
    }

    public function getECI(): string
    {
        return $this->payload['eci'];
    }

    public function getFortId(): string
    {
        return $this->payload['fort_id'];
    }

    public function getAuthorizationCode(): string
    {
        return $this->payload['authorization_code'];
    }

    public function getCustomerEmail(): string
    {
        return $this->payload['customer_email'];
    }

    public function getAcquirerResponseCode(): string
    {
        return $this->payload['acquirer_response_code'];
    }

    public function getCurrency(): string
    {
        return $this->payload['currency'];
    }

    public function getCommandName(): string
    {
        return $this->payload['command'];
    }

    public function get3dsUrl(): ?string
    {
        return $this->payload['3ds_url'] ?? null;
    }

    public function getAmount(): string
    {
        return $this->payload['amount'];
    }

    public function getAmountAsFloat(): float
    {
        return (float)$this->payload['amount'] / 100;
    }

    public function should3DsRedirect(): bool
    {
        return str_starts_with($this->getResponseCode(), '20') && substr($this->getResponseCode(), 2) === '064' && isset($this->payload['3ds_url']);
    }

    public function get3DsUri(): ?string
    {
        if (! $this->should3DsRedirect()) {
            return null;
        }

        return $this->payload['3ds_url'];
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
