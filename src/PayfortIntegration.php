<?php /** @noinspection PhpUnused */

namespace RSE\PayfortForLaravel;

use RSE\PayfortForLaravel\Services\AuthorizePurchaseService;
use RSE\PayfortForLaravel\Services\CaptureService;
use RSE\PayfortForLaravel\Services\CheckStatusService;
use RSE\PayfortForLaravel\Services\GetInstallmentsPlansService;
use RSE\PayfortForLaravel\Services\ProcessResponseService;
use RSE\PayfortForLaravel\Services\RefundService;
use RSE\PayfortForLaravel\Services\TokenizationService;
use RSE\PayfortForLaravel\Services\VoidService;

class PayfortIntegration
{
    protected array $merchant = [];

    protected array $merchant_extras = [];

    public function __construct()
    {
        $this->merchant = config('payfort.merchants.default');
    }

    /**
     * Set merchant extras to be sent to payfort
     *
     * @param string|int $extra1
     * @param string|int $extra2
     * @param string|int $extra3
     * @param string|int $extra4
     * @param string|int $extra5
     * @return self
     */
    public function setMerchantExtra($extra1, $extra2 = '', $extra3 = '', $extra4 = '', $extra5 = ''): self
    {
        for ($i = 1; $i <= 5; $i++) {
            if (! empty(${'extra' . $i}) && ! is_array(${'extra' . $i})) {
                array_push($this->merchant_extras, ${'extra' . $i});
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
     * @throws \Exception
     */
    public function refund($fort_id, $amount)
    {
        return app(RefundService::class)
            ->setMerchant($this->merchant)
            ->setMerchantExtras($this->merchant_extras)
            ->setFortId($fort_id)
            ->setAmount($amount)
            ->handle();
    }

    /**
     * @throws \Exception
     */
    public function void($fort_id)
    {
        return app(VoidService::class)
            ->setMerchant($this->merchant)
            ->setMerchantExtras($this->merchant_extras)
            ->setFortId($fort_id)
            ->handle();
    }

    /**
     * @throws \Exception
     */
    public function checkStatus($fort_id)
    {
        return app(CheckStatusService::class)
            ->setMerchant($this->merchant)
            ->setFortId($fort_id)
            ->handle();
    }

    /**
     * @throws \Exception
     */
    public function purchase(
        array $fort_params,
        float $amount,
        string $email,
        string $redirect_url,
        array $installments_params = []
    ): AuthorizePurchaseService {
        /** @var \RSE\PayfortForLaravel\Services\AuthorizePurchaseService */
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
     * @throws \Exception
     */
    public function authorize(
        array $fort_params,
        float $amount,
        string $email,
        string $redirect_url
    ): AuthorizePurchaseService {
        /** @var \RSE\PayfortForLaravel\Services\AuthorizePurchaseService */
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
     * prepare tokenization params and return array
     * by default it will return a form params.
     *
     * @param float  $amount
     * @param string $redirect_url
     * @param bool   $form_flag
     * @return array
     * @throws \Exception
     */
    public function tokenization(
        float $amount,
        string $redirect_url,
        bool $form_flag = true
    ): array {
        return app(TokenizationService::class)
            ->setMerchant($this->merchant)
            ->setAmount($amount)
            ->setMerchantExtras($this->merchant_extras)
            ->setRedirectUrl($redirect_url)
            ->withForm($form_flag)
            ->handle();
    }

    /**
     * @throws \Exception
     */
    public function processResponse(array $fort_params)
    {
        return app(ProcessResponseService::class)
            ->setMerchant($this->merchant)
            ->setMerchantExtras($this->merchant_extras)
            ->setFortParams($fort_params)
            ->handle();
    }

    /**
     * @throws \Exception
     */
    public function getInstallmentsPlans()
    {
        return app(GetInstallmentsPlansService::class)
            ->setMerchant($this->merchant)
            ->handle();
    }

    /**
     * @throws \Exception
     */
    public function capture(string $fort_id, $amount)
    {
        return app(CaptureService::class)
            ->setMerchant($this->merchant)
            ->setMerchantExtras($this->merchant_extras)
            ->setFortId($fort_id)
            ->setAmount($amount)
            ->handle();
    }
}
