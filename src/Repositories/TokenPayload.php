<?php

namespace RSE\PayfortForLaravel\Repositories;

class TokenPayload
{

    /*
     * {"response_code":"18000",
     * "card_number":"****************",
     * "card_holder_name":"*******",
     * "signature":"",
     * "merchant_identifier":"",
     * "expiry_date":"****",
     * "access_code":"",
     * "language":"en",
     * "service_command":"TOKENIZATION",
     * "response_message":"Success",
     * "merchant_reference":"-d723aa0a534f",
     * "token_name":"aa88814a496b4292040c5966",
     * "return_url":"http:",
     * "card_bin":"40555",
     * "status":"18"}
     *
     * */
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
