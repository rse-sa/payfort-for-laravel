<?php

namespace RSE\PayfortForLaravel\Repositories;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RSE\PayfortForLaravel\Events\PayfortMessageLog;
use RSE\PayfortForLaravel\Exceptions\PaymentFailed;

abstract class Payfort
{
    protected array $merchant;

    protected string $fort_id;

    protected string $language = 'en';

    protected bool $sandbox_mode = true;

    protected string $SHA_type = 'sha256';

    protected string $installment_url;

    protected array $response;

    protected array $merchant_extras = [];

    protected string $email = "";

    protected string $redirect_url = "";

    public string $currency = 'SAR';

    public ?string $merchant_reference = null;

    public float $amount;

    public function __construct()
    {
        $this->language = config('payfort.language');
        $this->sandbox_mode = config('payfort.sandbox_mode');
        $this->SHA_type = config('payfort.SHA_type');
    }

    protected function getOperationUrl(): string
    {
        return $this->sandbox_mode ?
            'https://sbpaymentservices.payfort.com/FortAPI/paymentApi' :
            'https://paymentservices.payfort.com/FortAPI/paymentApi';
    }

    protected function getRedirectionMethodOperationUrl(): string
    {
        return $this->sandbox_mode ?
            'https://sbcheckout.payfort.com/FortAPI/paymentPage' :
            'https://checkout.payfort.com/FortAPI/paymentPage';
    }

    public function setMerchantReference(?string $reference): self
    {
        $this->merchant_reference = $reference;

        return $this;
    }

    protected function getMerchantReference(): ?string
    {
        return $this->merchant_reference ?? $this->generateMerchantReference();
    }

    protected function generateMerchantReference(): string
    {
        return Str::orderedUuid();
    }

    protected function getGatewayUrl(): string
    {
        $base_uri = $this->sandbox_mode ?
            config('payfort.gateway_sandbox_host') :
            config('payfort.gateway_host');

        return "{$base_uri}FortAPI/paymentPage";
    }

    /**
     * calculate fort signature.
     */
    public function calculateSignature(array $data, string $signType = 'request'): string
    {
        unset($data['r']);
        unset($data['signature']);
        unset($data['integration_type']);
        unset($data['token']);
        unset($data['3ds']);

        $shaString = '';
        ksort($data);
        foreach ($data as $k => $v) {
            if (empty($v)) {
                continue;
            }

            $shaString .= "$k=$v";
        }

        if ($signType == 'request') {
            $shaString = $this->merchant['SHA_request_phrase'] . $shaString . $this->merchant['SHA_request_phrase'];
        } else {
            $shaString = $this->merchant['SHA_response_phrase'] . $shaString . $this->merchant['SHA_response_phrase'];
        }

        return hash($this->SHA_type, $shaString);
    }

    /**
     * Send host to host request to the Fort.
     */
    public function callApi(array $postData, string $gatewayUrl, bool $shouldBeLogged = true): array
    {
        $res = Http::post($gatewayUrl, $postData);

        $res = $res->json();

        // save response log
        if ($shouldBeLogged) {
            PayfortMessageLog::dispatch($postData, $res);
        }

        return $res;
    }

    /**
     * Convert Amount with dicemal points.
     *
     * @param float $amount
     * @return float
     */
    public function convertFortAmount(float $amount): float
    {
        $decimalPoints = $this->getCurrencyDecimalPoints($this->currency);

        return round($amount * (pow(10, $decimalPoints)), $decimalPoints);
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

    public function setFortId(string $fort_id): self
    {
        $this->fort_id = $fort_id;

        return $this;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Prepare merchant extras
     *
     * @param array $extras
     * @return self
     */
    public function setMerchantExtras(array $extras): self
    {
        foreach ($extras as $key => $value) {
            $merchant_key = $key !== 0 ? "merchant_extra" . $key : "merchant_extra";
            $this->merchant_extras[$merchant_key] = $value;
        }

        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function setRedirectUrl(string $url): self
    {
        $this->redirect_url = $url;

        return $this;
    }

    /**
     * Fort id for testing
     * 101010101010 --> throw 07666 transaction declined
     */
    public function isTestingFortId(string $fort_id): void
    {
        $fort_ids = [
            '101010101010' => '07666 - حركة مرفوضة',
        ];

        if (app()->environment('staging') && isset($fort_ids[$fort_id])) {
            throw new PaymentFailed($fort_ids[$fort_id]);
        }
    }

    abstract public function handle();

    /**
     * @param string $currency
     * @return int
     */
    private function getCurrencyDecimalPoints(string $currency): int
    {
        $decimalPoint = 2;

        $arrCurrencies = [
            'JOD' => 3,
            'KWD' => 3,
            'OMR' => 3,
            'TND' => 3,
            'BHD' => 3,
            'LYD' => 3,
            'IQD' => 3,
        ];

        if (isset($arrCurrencies[$currency])) {
            $decimalPoint = $arrCurrencies[$currency];
        }

        return $decimalPoint;
    }
}
