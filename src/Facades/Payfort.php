<?php

namespace RSE\PayfortForLaravel\Facades;

use Illuminate\Support\Facades\Facade;
use RSE\PayfortForLaravel\PayfortIntegration;
use RSE\PayfortForLaravel\Repositories\CaptureResponse;
use RSE\PayfortForLaravel\Repositories\PurchaseResponse;
use RSE\PayfortForLaravel\Repositories\RefundResponse;
use RSE\PayfortForLaravel\Repositories\StatusResponse;
use RSE\PayfortForLaravel\Repositories\VoidResponse;
use RSE\PayfortForLaravel\Services\ValidateResponseService;
use RSE\PayfortForLaravel\Services\TokenizationService;
use RSE\PayfortForLaravel\Services\VoidService;

/**
 * phpcs:disable
 * @method static PayfortIntegration    setMerchant(array $merchant)
 * @method static PayfortIntegration    setMerchantExtra(...$extras)
 * @method static TokenizationService   tokenization(float $amount, string $redirect_url, bool $form_flag = true, ?string $merchant_reference = null)
 * @method static PurchaseResponse      purchase(array $fort_params, float $amount, string $email, string $redirect_url, array $installments_params = [])
 * @method static PurchaseResponse      authorize(array $fort_params, float $amount, string $email, string $redirect_url)
 * @method static VoidResponse          void(string $fort_id)
 * @method static CaptureResponse       capture(string $fort_id, $amount)
 * @method static RefundResponse        refund(string $fort_id, $amount)
 * @method static array                 redirectionMethod($amount, string $email, string $returnUrl, ?string $merchant_reference = null)
 * @method static StatusResponse        checkStatus(string $fort_id)
 * @method static StatusResponse        validateStatus(array|string $data)
 * @method static ValidateResponseService validateResponse(array $fort_params)
 * @method static PurchaseResponse      validateResponseForRedirectionMethod(array $fort_params)
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
