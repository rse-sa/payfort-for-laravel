<?php

namespace RSE\PayfortForLaravel\Traits;

use RSE\PayfortForLaravel\Exceptions\PaymentFailed;

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
     * @throws \RSE\PayfortForLaravel\Exceptions\PaymentFailed
     */
    private function validateFortParams(): self
    {
        if (count($this->fort_params) === 0) {
            $msg = "Invalid Response Parameters";
            throw new PaymentFailed($msg);
        }

        return $this;
    }
}
