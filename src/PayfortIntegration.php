<?php
/** @noinspection PhpUnused */

namespace RSE\PayfortForLaravel;

use Exception;
use RSE\PayfortForLaravel\Events\PaymentFailed;
use RSE\PayfortForLaravel\Events\PaymentSuccess;
use RSE\PayfortForLaravel\Facades\Payfort;
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

    protected bool $throw_on_error = true;

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

    public function throwOnError(bool $value): self
    {
        $this->throw_on_error = $value;

        return $this;
    }

    /**
     * @throws \Exception|\Throwable
     */
    public function refund($fort_id, $amount)
    {
        return app(RefundService::class)
            ->throwOnError($this->throw_on_error)
            ->setMerchant($this->merchant)
            ->setMerchantExtras($this->merchant_extras)
            ->setFortId($fort_id)
            ->setAmount($amount)
            ->handle();
    }

    /**
     * @throws \Exception|\Throwable
     */
    public function void($fort_id)
    {
        return app(VoidService::class)
            ->throwOnError($this->throw_on_error)
            ->setMerchant($this->merchant)
            ->setMerchantExtras($this->merchant_extras)
            ->setFortId($fort_id)
            ->handle();
    }

    /**
     * @throws \Exception|\Throwable
     */
    public function checkStatus($fort_id): array
    {
        return app(CheckStatusService::class)
            ->throwOnError($this->throw_on_error)
            ->setMerchant($this->merchant)
            ->setFortId($fort_id)
            ->handle();
    }

    public function validateStatus(array|string $data): ?array
    {
        try {
            // Validate Response Body
            if (is_array($data)) {
                Payfort::processResponse($data);
            }

            // Check Status Online
            $fortId = is_array($data) ? $data['fort_id'] : $data;

            $status = Payfort::checkStatus($fortId);

            event(new PaymentSuccess());

            return $status;
        } catch (Exception $exception) {
            event(new PaymentFailed());
            throw $exception;
        }
    }

    public function purchase(
        array $fort_params,
        float $amount,
        string $email,
        string $redirect_url,
        array $installments_params = []
    ): AuthorizePurchaseService {
        /** @var \RSE\PayfortForLaravel\Services\AuthorizePurchaseService */
        return app(AuthorizePurchaseService::class)
            ->throwOnError($this->throw_on_error)
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
     * @throws \Exception|\Throwable
     */
    public function authorize(
        array $fort_params,
        float $amount,
        string $email,
        string $redirect_url
    ): AuthorizePurchaseService {
        /** @var \RSE\PayfortForLaravel\Services\AuthorizePurchaseService */
        return app(AuthorizePurchaseService::class)
            ->throwOnError($this->throw_on_error)
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
     * @param int    $form_flag
     * @return array
     * @throws \Exception|\Throwable
     */
    public function tokenization(
        float $amount,
        string $redirect_url,
        int $form_flag = TokenizationService::FORM_IFRAME,
        ?string $merchant_reference = null,
    ): array {
        return app(TokenizationService::class)
            ->throwOnError($this->throw_on_error)
            ->setMerchant($this->merchant)
            ->setMerchantReference($merchant_reference)
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
            ->throwOnError($this->throw_on_error)
            ->setMerchant($this->merchant)
            ->setMerchantExtras($this->merchant_extras)
            ->setFortParams($fort_params)
            ->handle();
    }

    /**
     * @throws \Exception|\Throwable
     */
    public function getInstallmentsPlans()
    {
        return app(GetInstallmentsPlansService::class)
            ->throwOnError($this->throw_on_error)
            ->setMerchant($this->merchant)
            ->handle();
    }

    /**
     * @throws \Exception|\Throwable
     */
    public function capture(string $fort_id, $amount)
    {
        return app(CaptureService::class)
            ->throwOnError($this->throw_on_error)
            ->setMerchant($this->merchant)
            ->setMerchantExtras($this->merchant_extras)
            ->setFortId($fort_id)
            ->setAmount($amount)
            ->handle();
    }
}
