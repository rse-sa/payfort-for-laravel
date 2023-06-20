<?php

namespace RSE\PayfortForLaravel\Traits;

use RSE\PayfortForLaravel\Exceptions\PaymentFailed;
use RSE\PayfortForLaravel\Exceptions\RequestFailed;

/**
 * fort params
 */
trait FortParams
{
    public function setFortParams(array $params): self
    {
        $this->fort_params = $params;

        return $this;
    }

    /**
     * @throws \RSE\PayfortForLaravel\Exceptions\RequestFailed
     */
    private function validateFortParams(): self
    {
        if (count($this->fort_params) === 0) {
            $msg = "Invalid Response Parameters";
            throw new RequestFailed($msg);
        }

        return $this;
    }
}
