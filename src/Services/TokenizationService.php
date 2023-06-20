<?php

namespace RSE\PayfortForLaravel\Services;

use RSE\PayfortForLaravel\Exceptions\PaymentFailed;
use RSE\PayfortForLaravel\Repositories\Payfort;

class TokenizationService extends Payfort
{
    const FORM_NONE = 0;
    const FORM_EXTERNAL = 1;
    const FORM_IFRAME = 2;

    protected bool $has_3ds;

    protected ?string $payment_method = null;

    protected bool $form_type = false;

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

        $params = array_merge($params, $this->merchant_extras);

        $params['signature'] = $this->calculateSignature($params);

        $result = [
            'url' => $this->getGatewayUrl(),
            'params' => $params,
            'paymentMethod' => 'cc_merchantpage2',
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
        $form = '<form style="display:none" name="payfort_payment_form" id="payfort_payment_form" method="post" action="' . $gatewayUrl . '" '
            . ($this->form_type == self::FORM_IFRAME ? "target='payfortFrame'" : "") .'>';

        foreach ($postData as $k => $v) {
            $form .= '<input type="hidden" name="' . $k . '" value="' . $v . '">';
        }

        $form .= '<input type="submit" id="submit_btn" value="ready to pay">';

        $form .= '</form>';

        if($this->form_type == self::FORM_EXTERNAL || $this->form_type == self::FORM_IFRAME){
            $form .= "<script>document.forms['payfort_payment_form'].submit();</script>";
        }

        return $form;
    }

    public function withForm(int $flag): self
    {
        $this->form_type = $flag;

        return $this;
    }
}
