<?php
/** @noinspection PhpUnused */

namespace RSE\PayfortForLaravel;

use Illuminate\Support\Carbon;
use RSE\PayfortForLaravel\Repositories\CaptureResponse;
use RSE\PayfortForLaravel\Repositories\PaymentLinkCallbackResponse;
use RSE\PayfortForLaravel\Repositories\PurchaseResponse;
use RSE\PayfortForLaravel\Repositories\RefundResponse;
use RSE\PayfortForLaravel\Repositories\StatusResponse;
use RSE\PayfortForLaravel\Repositories\TokenizationResponse;
use RSE\PayfortForLaravel\Repositories\VoidResponse;
use RSE\PayfortForLaravel\Services\AuthorizePurchaseService;
use RSE\PayfortForLaravel\Services\CaptureService;
use RSE\PayfortForLaravel\Services\CheckStatusService;
use RSE\PayfortForLaravel\Services\GetInstallmentsPlansService;
use RSE\PayfortForLaravel\Services\PaymentLinkService;
use RSE\PayfortForLaravel\Services\RedirectionMethodService;
use RSE\PayfortForLaravel\Services\RefundService;
use RSE\PayfortForLaravel\Services\TokenizationService;
use RSE\PayfortForLaravel\Services\ValidatePaymentLinkCallback;
use RSE\PayfortForLaravel\Services\ValidatePostResponse;
use RSE\PayfortForLaravel\Services\ValidateTokenizationResponse;
use RSE\PayfortForLaravel\Services\VoidService;

class PayfortIntegration
{
    protected array $merchant = [];

    protected array $merchant_extras = [];

    protected bool $throw_on_error = true;

    public function __construct()
    {
        $this->merchant = config('payfort.merchants.default');
    }

    /**
     * Set merchant extras to be sent to payfort
     *
     * @param string $extra1
     * @param string $extra2
     * @param string $extra3
     * @param string $extra4
     * @param string $extra5
     * @return self
     */
    public function setMerchantExtra(string $extra1, string $extra2 = '', string $extra3 = '', string $extra4 = '', string $extra5 = ''): self
    {
        for ($i = 1; $i <= 5; $i++) {
            if (! empty(${'extra' . $i}) && ! is_array(${'extra' . $i})) {
                $this->merchant_extras[] = ${'extra' . $i};
            }
        }

        return $this;
    }

    /**
     * set payfort merchant to be used
     * will use default if not set
     *
     * @param array $merchant
     * @return self
     */
    public function setMerchant(array $merchant): self
    {
        $this->merchant = $merchant;

        return $this;
    }

    /**
     * @param string $fort_id
     * @param        $amount
     * @return \RSE\PayfortForLaravel\Repositories\RefundResponse
     * @throws \RSE\PayfortForLaravel\Exceptions\PaymentFailed
     */
    public function refund(string $fort_id, $amount): RefundResponse
    {
        return app(RefundService::class)
            ->setMerchant($this->merchant)
            ->setMerchantExtras($this->merchant_extras)
            ->setFortId($fort_id)
            ->setAmount($amount)
            ->handle();
    }

    /**
     * @param string $fort_id
     * @return \RSE\PayfortForLaravel\Repositories\VoidResponse
     * @throws \RSE\PayfortForLaravel\Exceptions\PaymentFailed
     */
    public function void(string $fort_id): VoidResponse
    {
        return app(VoidService::class)
            ->setMerchant($this->merchant)
            ->setMerchantExtras($this->merchant_extras)
            ->setFortId($fort_id)
            ->handle();
    }

    /**
     * @param string $fort_id
     * @return \RSE\PayfortForLaravel\Repositories\StatusResponse
     * @throws \RSE\PayfortForLaravel\Exceptions\RequestFailed
     */
    public function checkStatus(string $fort_id): StatusResponse
    {
        return app(CheckStatusService::class)
            ->setMerchant($this->merchant)
            ->setFortId($fort_id)
            ->handle();
    }

    /**
     * prepare tokenization params and return array
     * by default it will return a form params.
     *
     * @param float       $amount
     * @param string      $redirect_url
     * @param int         $form_flag
     * @param string|null $merchant_reference
     * @return array
     * @throws \Throwable
     */
    public function tokenization(
        float $amount,
        string $redirect_url,
        int $form_flag = TokenizationService::FORM_IFRAME,
        ?string $merchant_reference = null
    ): array {
        return app(TokenizationService::class)
            ->setMerchant($this->merchant)
            ->setMerchantReference($merchant_reference)
            ->setAmount($amount)
            ->setMerchantExtras($this->merchant_extras)
            ->setRedirectUrl($redirect_url)
            ->withForm($form_flag)
            ->handle();
    }

    /**
     * prepare tokenization params and return array
     * by default it will return a form params.
     *
     * @param float       $amount
     * @param string      $redirect_url
     * @param string      $card_number
     * @param string      $expiry_date
     * @param string      $card_security_code
     * @param string      $card_holder_name
     * @param string|null $merchant_reference
     * @return array
     * @throws \Throwable
     */
    public function tokenizationForCustom(
        float $amount,
        string $redirect_url,
        string $card_number,
        string $expiry_date,
        string $card_security_code,
        string $card_holder_name,
        ?string $merchant_reference = null
    ): array {
        return app(TokenizationService::class)
            ->setMerchant($this->merchant)
            ->setMerchantReference($merchant_reference)
            ->setAmount($amount)
            ->setMerchantExtras($this->merchant_extras)
            ->setRedirectUrl($redirect_url)
            ->withForm(TokenizationService::FORM_EXTERNAL)
            ->setCardInformation($card_number,$expiry_date,$card_security_code, $card_holder_name)
            ->handle();
    }

    /**
     * @param array $fort_params
     * @return \RSE\PayfortForLaravel\Repositories\TokenizationResponse
     * @throws \RSE\PayfortForLaravel\Exceptions\PaymentFailed
     * @throws \RSE\PayfortForLaravel\Exceptions\RequestFailed
     */
    public function validateTokenizationResponse(array $fort_params): TokenizationResponse
    {
        return app(ValidateTokenizationResponse::class)
            ->setMerchant($this->merchant)
            ->setMerchantExtras($this->merchant_extras)
            ->setFortParams($fort_params)
            ->handle();
    }

    /**
     * @param array  $fort_params
     * @param float  $amount
     * @param string $email
     * @param string $redirect_url
     * @param array  $installments_params
     * @return \RSE\PayfortForLaravel\Repositories\PurchaseResponse
     * @throws \RSE\PayfortForLaravel\Exceptions\PaymentFailed
     * @throws \RSE\PayfortForLaravel\Exceptions\RequestFailed
     */
    public function purchase(
        array $fort_params,
        float $amount,
        string $email,
        string $redirect_url,
        array $installments_params = []
    ): PurchaseResponse {
        return app(AuthorizePurchaseService::class)
            ->setMerchant($this->merchant)
            ->setFortParams($fort_params)
            ->setInstallmentParams($installments_params)
            ->setAmount($amount)
            ->setMerchantExtras($this->merchant_extras)
            ->setEmail($email)
            ->setRedirectUrl($redirect_url)
            ->handle();
    }


    /**
     * @param array  $fort_params
     * @param float  $amount
     * @param string $email
     * @param string $redirect_url
     * @return \RSE\PayfortForLaravel\Repositories\PurchaseResponse
     * @throws \RSE\PayfortForLaravel\Exceptions\PaymentFailed
     * @throws \RSE\PayfortForLaravel\Exceptions\RequestFailed
     */
    public function authorize(
        array $fort_params,
        float $amount,
        string $email,
        string $redirect_url
    ): PurchaseResponse {
        return app(AuthorizePurchaseService::class)
            ->setAuthorizationCommand()
            ->setMerchant($this->merchant)
            ->setFortParams($fort_params)
            ->setMerchantExtras($this->merchant_extras)
            ->setAmount($amount)
            ->setEmail($email)
            ->setRedirectUrl($redirect_url)
            ->handle();
    }


    /**
     * @param string $fort_id
     * @param        $amount
     * @return \RSE\PayfortForLaravel\Repositories\CaptureResponse
     * @throws \RSE\PayfortForLaravel\Exceptions\PaymentFailed
     * @throws \RSE\PayfortForLaravel\Exceptions\RequestFailed
     */
    public function capture(string $fort_id, $amount): CaptureResponse
    {
        return app(CaptureService::class)
            ->setMerchant($this->merchant)
            ->setMerchantExtras($this->merchant_extras)
            ->setFortId($fort_id)
            ->setAmount($amount)
            ->handle();
    }

    /**
     * @param array $fort_params
     * @return \RSE\PayfortForLaravel\Repositories\PurchaseResponse
     * @throws \RSE\PayfortForLaravel\Exceptions\RequestFailed
     */
    public function validatePostResponse(array $fort_params): PurchaseResponse
    {
        return app(ValidatePostResponse::class)
            ->setMerchant($this->merchant)
            ->setMerchantExtras($this->merchant_extras)
            ->setFortParams($fort_params)
            ->handle();
    }

    /**
     * @return array
     * @throws \RSE\PayfortForLaravel\Exceptions\RequestFailed
     */
    public function getInstallmentsPlans(): array
    {
        return app(GetInstallmentsPlansService::class)
            ->setMerchant($this->merchant)
            ->handle();
    }

    /**
     * @param             $amount
     * @param string      $email
     * @param string      $returnUrl
     * @param string|null $merchant_reference
     * @return array
     * @noinspection PhpDocMissingThrowsInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function redirectionMethod($amount, string $email, string $returnUrl, ?string $merchant_reference = null): array
    {
        return app(RedirectionMethodService::class)
            ->setMerchant($this->merchant)
            ->setMerchantExtras($this->merchant_extras)
            ->setEmail($email)
            ->setMerchantReference($merchant_reference)
            ->setRedirectUrl($returnUrl)
            ->setAmount($amount)
            ->handle();
    }

    /**
     * @param                            $amount
     * @param string                     $email
     * @param \Illuminate\Support\Carbon $expiryDate
     * @param string                     $returnUrl
     * @param array                      $notificationType
     * @param string|null                $merchant_reference
     * @return \RSE\PayfortForLaravel\Services\PaymentLinkService
     */
    public function paymentLink(
        $amount,
        string $email,
        Carbon $expiryDate,
        string $returnUrl,
        array $notificationType = ['EMAIL'],
        ?string $merchant_reference = null
    ): PaymentLinkService {
        return app(PaymentLinkService::class)
            ->setMerchant($this->merchant)
            ->setMerchantExtras($this->merchant_extras)
            ->setMerchantReference($merchant_reference)
            ->setEmail($email)
            ->setNotificationType($notificationType)
            ->setExpiryDate($expiryDate)
            ->setRedirectUrl($returnUrl)
            ->setAmount($amount);
    }

    /**
     * @param array $fort_params
     * @return \RSE\PayfortForLaravel\Repositories\PaymentLinkCallbackResponse
     * @throws \RSE\PayfortForLaravel\Exceptions\PaymentFailed
     * @throws \RSE\PayfortForLaravel\Exceptions\RequestFailed
     */
    public function validatePaymentLinkPostResponse(array $fort_params): PaymentLinkCallbackResponse
    {
        return app(ValidatePaymentLinkCallback::class)
            ->setMerchant($this->merchant)
            ->setMerchantExtras($this->merchant_extras)
            ->setFortParams($fort_params)
            ->handle();
    }
}
