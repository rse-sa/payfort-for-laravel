<?php

namespace RSE\PayfortForLaravel\Services;

use RSE\PayfortForLaravel\Exceptions\PaymentFailed;
use RSE\PayfortForLaravel\Repositories\Payfort;

class TokenizationService extends Payfort
{
    const FORM_NONE = 0;
    const FORM_EXTERNAL = 1;
    const FORM_IFRAME = 2;
    const FORM_DISPLAY = 3;
    protected bool $has_3ds;
    protected string $payment_method = 'cc_merchantpage2';
    protected bool $form_type = false;
    protected string $card_number;
    protected string $expiry_date;
    protected string $card_security_code;
    protected string $card_holder_name;

    /**
     * @throws \Exception|\Throwable
     */
    public function handle(): array
    {
        $params = [
            'service_command' => 'TOKENIZATION',
            'merchant_identifier' => $this->merchant['merchant_identifier'],
            'access_code' => $this->merchant['access_code'],
            'merchant_reference' => $this->getMerchantReference(),
            'language' => $this->language,
            'return_url' => $this->redirect_url,
        ];

        if ($this->payment_method === 'installments_merchantpage') {
            $params['currency'] = strtoupper($this->currency);
            $params['installments'] = 'STANDALONE';
            $params['amount'] = $this->convertFortAmount($this->amount);
        }

        if (isset($this->card_number)) {
            $params['card_number'] = $this->card_number;
            $params['expiry_date'] = $this->expiry_date;
            $params['card_security_code'] = $this->card_security_code;
            $params['card_holder_name'] = $this->card_holder_name;
        }

        $params = array_merge($params, $this->merchant_extras);

        $params['signature'] = $this->calculateSignature($params);

        $result = [
            'url' => $this->getGatewayUrl(),
            'params' => $params,
            'paymentMethod' => $this->payment_method,
        ];

        if ($this->form_type > 0) {
            $result['form'] = $this->getPaymentForm($this->getGatewayUrl(), $params);
        }

        return $result;
    }

    public function set3DSFlag(bool $flag): self
    {
        $this->has_3ds = $flag;

        return $this;
    }

    /**
     * @throws \RSE\PayfortForLaravel\Exceptions\PaymentFailed
     */
    public function setPaymentMethod(string $method): self
    {
        if (! in_array($method, ['cc_merchantpage', 'cc_merchantpage2', 'installments_merchantpage'])) {
            throw new PaymentFailed("payment method not supported");
        }

        $this->payment_method = $method;

        return $this;
    }

    public function getPaymentForm($gatewayUrl, $postData): string
    {
        $form = '<form ' . ($this->form_type == self::FORM_DISPLAY ? '' : 'style="display:none"') . ' name="payfort_payment_form" id="payfort_payment_form"
            method="post" action="' . $gatewayUrl . '" '
            . ($this->form_type == self::FORM_IFRAME ? "target='payfortFrame'" : "") . '>';

        foreach ($postData as $k => $v) {
            $form .= '<input type="hidden" name="' . htmlentities($k) . '" value="' . htmlentities($v) . '">';
        }

        $form .= '<input type="submit" id="submit_btn" value="ready to pay">';

        $form .= '</form>';

        if ($this->form_type == self::FORM_EXTERNAL || $this->form_type == self::FORM_IFRAME) {
            $form .= "<script>document.forms['payfort_payment_form'].submit();</script>";
        }

        return $form;
    }

    public function withForm(int $flag): self
    {
        $this->form_type = $flag;

        return $this;
    }

    public function setCardInformation(string $card_number, string $expiry_date, string $card_security_code, string $card_holder_name): self
    {
        $this->card_number = $card_number;
        $this->expiry_date = $expiry_date;
        $this->card_security_code = $card_security_code;
        $this->card_holder_name = $card_holder_name;

        return $this;
    }
}
