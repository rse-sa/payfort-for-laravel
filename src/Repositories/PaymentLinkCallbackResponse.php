<?php

namespace RSE\PayfortForLaravel\Repositories;

use Illuminate\Support\Carbon;
use RSE\PayfortForLaravel\Traits\RepositoryHelpers;

class PaymentLinkCallbackResponse extends PaymentLinkCreatedResponse
{
    public function isPaymentSuccessful(): bool
    {
        return $this->getResponseStatusCode() == '14' && $this->getResponseMessageCode() == '000';
    }

    public function getCommand(): string
    {
        return $this->payload['command'];
    }

    public function getTokenName(): ?string
    {
        return $this->payload['token_name'] ?? null;
    }

    public function getECI(): ?string
    {
        return $this->payload['eci'] ?? null;
    }

    public function getCustomerIP(): ?string
    {
        return $this->payload['customer_ip'] ?? null;
    }

    public function getAuthorizationCode(): ?string
    {
        return $this->payload['authorization_code'] ?? null;
    }

    public function getCardholderName(): ?string
    {
        return $this->payload['card_holder_name'] ?? null;
    }

    public function getCardExpiryDate(): ?string
    {
        return $this->payload['expiry_date'] ?? null;
    }

    public function getCardNumber(): ?string
    {
        return $this->payload['card_number'] ?? null;
    }

    public function getRememberMeValue(): ?string
    {
        return $this->payload['remember_me'] ?? null;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->payload['phone_number'] ?? null;
    }

    public function getSettlementReference(): ?string
    {
        return $this->payload['settlement_reference'] ?? null;
    }
}
