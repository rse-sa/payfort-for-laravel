<?php

namespace RSE\PayfortForLaravel\Facades;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Facade;
use RSE\PayfortForLaravel\PayfortIntegration;
use RSE\PayfortForLaravel\Repositories\CaptureResponse;
use RSE\PayfortForLaravel\Repositories\PaymentLinkCallbackResponse;
use RSE\PayfortForLaravel\Repositories\PurchaseResponse;
use RSE\PayfortForLaravel\Repositories\RefundResponse;
use RSE\PayfortForLaravel\Repositories\StatusResponse;
use RSE\PayfortForLaravel\Repositories\TokenizationResponse;
use RSE\PayfortForLaravel\Repositories\VoidResponse;
use RSE\PayfortForLaravel\Services\PaymentLinkService;
use RSE\PayfortForLaravel\Services\TokenizationService;

/**
 * phpcs:disable
 * @method static PayfortIntegration            setMerchant(array $merchant)
 * @method static PayfortIntegration            setMerchantExtra(...$extras)
 * @method static array                         tokenization(float $amount, string $redirect_url, bool $form_flag = true, ?string $merchant_reference = null)
 * @method static array                         tokenizationForCustom(float $amount,string $redirect_url,string $card_number,string $expiry_date,string $card_security_code,string $card_holder_name,?string $merchant_reference = null)
 * @method static PurchaseResponse              purchase(array $fort_params, float $amount, string $email, string $redirect_url, array $installments_params = [])
 * @method static PurchaseResponse              authorize(array $fort_params, float $amount, string $email, string $redirect_url)
 * @method static VoidResponse                  void(string $fort_id)
 * @method static CaptureResponse               capture(string $fort_id, $amount)
 * @method static RefundResponse                refund(string $fort_id, $amount)
 * @method static array                         redirectionMethod($amount, string $email, string $returnUrl, ?string $merchant_reference = null)
 * @method static PaymentLinkService            paymentLink($amount,string $email,Carbon $expiryDate,string $returnUrl,array $notificationType = ['EMAIL'],?string $merchant_reference = null)
 * @method static StatusResponse                checkStatus(string $fort_id)
 * @method static PurchaseResponse              validatePostResponse(array $fort_params)
 * @method static TokenizationResponse          validateTokenizationResponse(array $fort_params)
 * @method static PurchaseResponse              validateResponseForRedirectionMethod(array $fort_params)
 * @method static PaymentLinkCallbackResponse   validatePaymentLinkPostResponse(array $fort_params)
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
