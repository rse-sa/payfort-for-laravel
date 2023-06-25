<?php

namespace RSE\PayfortForLaravel\Traits;

trait RepositoryHelpers
{

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

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getResponse(): array
    {
        return $this->getPayload();
    }

}
