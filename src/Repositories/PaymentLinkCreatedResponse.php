<?php

namespace RSE\PayfortForLaravel\Repositories;

use Illuminate\Support\Carbon;
use RSE\PayfortForLaravel\Traits\RepositoryHelpers;

class PaymentLinkCreatedResponse
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

    public function isPaymentCreated(): bool
    {
        return $this->getResponseStatusCode() == '48';
    }

    public function getServiceCommand(): string
    {
        return $this->payload['service_command'];
    }

    public function getLinkCommand(): string
    {
        return $this->payload['link_command'];
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

    public function getAmount(): string
    {
        return $this->payload['amount'];
    }

    public function getAmountAsFloat(): float
    {
        return (float)$this->payload['amount'] / 100;
    }

    public function getCustomerName(): ?string
    {
        return $this->payload['customer_name'] ?? null;
    }

    public function getCustomerEmail(): ?string
    {
        return $this->payload['customer_email'] ?? null;
    }

    public function getCustomerPhone(): ?string
    {
        return $this->payload['customer_phone'] ?? null;
    }

    public function getExpiryDate(): Carbon
    {
        return Carbon::parse($this->payload['request_expiry_date']);
    }

    public function getNotificationTypes(): array
    {
        return explode(',', $this->payload['notification_type'] ?? '');
    }

    public function getPaymentLinkId(): string
    {
        return $this->payload['payment_link_id'];
    }

    public function getPaymentLink(): string
    {
        return $this->payload['payment_link'];
    }

    public function getPaymentOptions(): string
    {
        return $this->payload['payment_option'];
    }

    public function getReturnUrl(): string
    {
        return $this->payload['return_url'];
    }

    public function getOrderDescription(): ?string
    {
        return $this->payload['order_description'] ?? null;
    }
}
