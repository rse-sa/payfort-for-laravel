<?php

namespace RSE\PayfortForLaravel\Facades;

use Illuminate\Support\Facades\Facade;
use RSE\PayfortForLaravel\PayfortIntegration;
use RSE\PayfortForLaravel\Services\AuthorizePurchaseService;
use RSE\PayfortForLaravel\Services\CaptureService;
use RSE\PayfortForLaravel\Services\CheckStatusService;
use RSE\PayfortForLaravel\Services\ProcessResponseService;
use RSE\PayfortForLaravel\Services\RefundService;
use RSE\PayfortForLaravel\Services\TokenizationService;
use RSE\PayfortForLaravel\Services\VoidService;

/**
 * phpcs:disable
 * @method static PayfortIntegration setMerchant(array $merchant)
 * @method static PayfortIntegration setMerchantExtra(...$extras)
 * @method static PayfortIntegration throwOnError(bool $value)
 * @method static RefundService refund($fort_id, $amount)
 * @method static TokenizationService tokenization(float $amount, string $redirect_url, bool $form_flag = true, ?string $merchant_reference = null)
 * @method static ProcessResponseService processResponse(array $fort_params)
 * @method static CaptureService capture(string $fort_id, $amount)
 * @method static array checkStatus($fort_id)
 * @method static AuthorizePurchaseService purchase(array $fort_params, float $amount, string $email, string $redirect_url, array $installments_params = [])
 * @method static AuthorizePurchaseService authorize(array $fort_params, float $amount, string $email, string $redirect_url)
 * @method static array validateStatus(array|string $data)
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
