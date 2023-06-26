<?php

namespace RSE\PayfortForLaravel\Repositories;

use RSE\PayfortForLaravel\Traits\RepositoryHelpers;

class TokenizationResponse
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

    public function isSuccessful(): bool
    {
        return $this->getResponseStatusCode() == '18';
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
}
