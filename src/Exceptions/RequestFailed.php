<?php

namespace RSE\PayfortForLaravel\Exceptions;

use Exception;

class RequestFailed extends Exception
{
    protected array $params = [];

    public function setResponse(array $responseParams)
    {
        $this->params = $responseParams;

        return $this;
    }

    public function getResponse()
    {
        return $this->params;
    }

    public function getResponseCode(): ?string
    {
        return $this->params['response_code'] ?? null;
    }

    public function getResponseMessage(): ?string
    {
        return $this->params['response_message'] ?? null;
    }

    public function getResponseCodeAndMessage(): string
    {
        return $this->getResponseCode() . ' :: ' . $this->getResponseMessage();
    }
}
