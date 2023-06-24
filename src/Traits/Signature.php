<?php

namespace RSE\PayfortForLaravel\Traits;

use RSE\PayfortForLaravel\Exceptions\PaymentFailed;
use RSE\PayfortForLaravel\Exceptions\RequestFailed;

/**
 * Signature trait
 */
trait Signature
{
    /**
     * @throws \RSE\PayfortForLaravel\Exceptions\RequestFailed
     */
    protected function validateSignature($request_type = 'response'): self
    {
        $responseSignature = $this->fort_params['signature'];
        $calculatedSignature = $this->calculateSignature($this->fort_params, $request_type);

        if ($responseSignature !== $calculatedSignature) {
            $msg = "Invalid signature.";

            throw (new RequestFailed($msg))->setResponse($this->fort_params);
        }

        return $this;
    }
}
