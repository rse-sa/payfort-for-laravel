<?php

namespace RSE\PayfortForLaravel\Services;

use RSE\PayfortForLaravel\Repositories\Payfort;

class RedirectionMethodService extends Payfort
{
    /**
     * @return array
     */
    public function handle(): array
    {
        $request = [
            'command' => 'PURCHASE',
            'access_code' => $this->merchant['access_code'],
            'merchant_identifier' => $this->merchant['merchant_identifier'],
            'merchant_reference' => $this->getMerchantReference(),
            'amount' => $this->convertFortAmount($this->amount),
            'currency' => $this->currency,
            'customer_email' => $this->email,
            'return_url' => $this->redirect_url,
            'language' => $this->language,
        ];

        $request = array_merge($request, $this->merchant_extras);

        // calculating signature
        $request['signature'] = $this->calculateSignature($request);

        $request['form'] = $this->getRedirectionForm($this->getRedirectionMethodOperationUrl(), $request);

        return $request;
    }

    public function getRedirectionForm($gatewayUrl, $postData): string
    {
        $form = '<div style="text-align: center;padding:5rem;direction: ltr"><form name="payfort_payment_form" id="payfort_payment_form" method="post" action="' . $gatewayUrl . '">';

        foreach ($postData as $k => $v) {
            $form .= '<input type="hidden" name="' . htmlentities($k) . '" value="' . htmlentities($v) . '">';
        }

        $form .= '<input type="submit" value="You will be redirected to the payment page ..">';

        $form .= '</form></div>';


        //$form .= 'You will be redirected to the payment page ..';

        $form .= "<script>document.forms['payfort_payment_form'].submit();</script>";

        return $form;
    }
}
