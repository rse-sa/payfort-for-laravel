<?php

namespace RSE\PayfortForLaravel\Facades;

use Illuminate\Support\Facades\Facade;
use RSE\PayfortForLaravel\PayfortIntegration;
use RSE\PayfortForLaravel\Services\CaptureService;
use RSE\PayfortForLaravel\Services\ProcessResponseService;
use RSE\PayfortForLaravel\Services\TokenizationService;
use RSE\PayfortForLaravel\Services\VoidService;

/**
 * phpcs:disable
 * @method static PayfortIntegration setMerchant(array $merchant)
 * @method static PayfortIntegration setMerchantExtra(...$extras)
 * @method static \RSE\PayfortForLaravel\Services\RefundService refund($fort_id, $amount)
 * @method static TokenizationService tokenization(float $amount, string $redirect_url, bool $form_flag = true)
 * @method static ProcessResponseService processResponse(array $fort_params)
 * @method static CaptureService capture(string $fort_id, $amount)
 * @method static VoidService void($fort_id)
 * phpcs:enable
 *
 * @see \RSE\PayfortForLaravel\PayfortIntegration
 */
class Payfort extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return PayfortIntegration::class;
    }
}
