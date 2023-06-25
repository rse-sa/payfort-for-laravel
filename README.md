Payfort For Laravel
=======================
This repo was inspired by https://github.com/TamkeenTech/laravel-payfort and has been developed and updated.

Helps you integrate Payfort into your application. currently it supports `Custom merchant page integration` refer to this [link](https://paymentservices-reference.payfort.com/docs/api/build/index.html#custom-merchant-page-integration) to understand more, also this package support using multiple merchant accounts.

Currently, this package supports the below operation list:
- AUTHORIZATION/PURCHASE
- TOKENIZATION
- CAPTURE
- REFUND
- INSTALLMENTS
- VOID
- CHECK_STATUS
- REDIRECTION [NEW]
- PAYMENT LINKS (INVOICES) [NEW]

Please make sure to read and understand `payfort` documentation.
https://paymentservices-reference.payfort.com/docs/api/build/index.html

Currently, it supports only Laravel 9.

## Installation
You need to run this command
```bash
composer require rse-sa/payfort-for-laravel
```
To publish the configurations please run this command
```bash
php artisan vendor:publish --tag payfort-config
```
This will generate a `config/payfort.php` with the default configurations

```php
return [
    'gateway_host' => env('PAYFORT_GATEWAY_HOST', 'https://checkout.payfort.com/'),
    
    'gateway_sandbox_host' => env('PAYFORT_GATEWAY_SAND_BOX_HOST', 'https://sbcheckout.payfort.com/'),

    'merchants' => [
        'default' => [
            'merchant_identifier' => env('PAYFORT_MERCHANT_IDENTIFIER', null),
            'access_code' => env('PAYFORT_ACCESS_CODE', null),
            'SHA_request_phrase' => env('PAYFORT_SHA_REQUEST_PHRASE', null),
            'SHA_response_phrase' => env('PAYFORT_SHA_RESPONSE_PHRASE', null),
        ],
    ],

    'sandbox_mode' => env('PAYFORT_SANDBOX_MODE', true),
    
    'SHA_type' => env('PAYFORT_SHA_TYPE', 'sha256'),
    
    'language' => env('PAYFORT_LANGUAGE', 'en'),
];
```
### Then you can update your `.env` file to have the correct credentials:
```bash
PAYFORT_SANDBOX_MODE=true                     # Defines wether to activate the payfort sandbox enviroment or not.
PAYFORT_MERCHANT_IDENTIFIER=test              # The payfort merchant account identifier
PAYFORT_ACCESS_CODE=test                      # The payfort account access code
PAYFORT_SHA_TYPE=sha256                       # The payfort account sha type. sha256/sha512
PAYFORT_SHA_REQUEST_PHRASE=test               # The payfort account sha request phrase
PAYFORT_SHA_RESPONSE_PHRASE=test              # The payfort account sha response phrase
```

## Usage (Redirection Method)

Add the below code to your controller's method, which will embed the form for the redirection payment method (the form will be submitted automatically by javascript).

```php
$merchantReference = Str::ulid();

$redirection = Payfort::redirectionMethod(
    $invoice_amount,
    $user_email,
    route('pay-invoice-callback', [$invoice]),
    $merchantReference
);

return response()->view('payment-redirection', [
    'form' => $redirection['form'],
]);
```

Add the below code to your callback controller's method
```php
$payfort = app(PayfortIntegration::class);

// Validate Callback Response From Payfort
try {
    $purchase = $payfort->validatePostResponse($post);
} catch (RequestFailed $requestFailed) {
    // Handle Error
} catch (PaymentFailed $paymentFailed) {
    // Handle Error
}

// Fetch Transaction Status From Payfort
try {
    $status = $payfort->checkStatus($purchase->getFortId());
} catch (RequestFailed $requestFailed) {
    // Handle Error
}

// Validate Payment Status
if ($status->isPurchaseSuccessful()) {
    // Handle Success
}else{
    // Handle Error
}
```


------------------

## Usage (Standard Integration Method)
Once you identified your credentials and configurations, your are ready to use payfort operations.

### Tokenization request:
To display tokenization page, in your controller method you can add the following
```php
$tokenization = Payfort::tokenization(
    $billAmount,
    'redirect_url',
    TokenizationService::FORM_IFRAME,
    $merchant_reference,
);

return response()->view('payment-form', [
    'form' => $tokenization['form'],
]);
```
### Handle Callback After Tokenization
In your return url controller's methods (callback):
```php
$tokenization = TokenizationResponse::fromArray(request()->post());

// Check If Tokenization Was Success Or Not
if (! $tokenization->isSuccessful()) {
    $responseCode = $tokenization->getResponseCode();
    // Handle Error
}

/**
 * add your payment logic either Purchase Or Authorization as per the next section. 
 */
```


### Authorization/Purchase:
To send a purchase or authorization command, in your controller on the return of the tokenization request from payfort add this code
```php
try{
    $response = Payfort::purchase(
        [],  # Request body coming from the tokenization
        100, # Bill amount
        'test@test.ts', # User email
        'redirect_url', # The return back url after purchase
        [] # installment data (optional)
    );
} catch (RequestFailed $exception) {
    // Handle Error
} catch (PaymentFailed $exception) {
    // Handle Error
}
```

```php
try{
    $response = Payfort::authorize(
        [],  # Request body coming from the tokenization
        100, # Bill amount
        'test@test.ts', # User email
        'redirect_url' # The return back url after purchase
    );
} catch (RequestFailed $exception) {
    // Handle Error
} catch (PaymentFailed $exception) {
    // Handle Error
}
```

To handle the 3Ds redirection, you can use this code snippet 
```php
if ($response->should3DsRedirect()) {
    return redirect()->away($response->get3DsUri());
}
```

Where `$response` is the response coming from the purchase or the authorization.

if the transaction is done successfully you can get the transaction fort id by using this:
```php
$response->getFortId();
```

or the used payment method by this:
```php
$response->getPaymentOption()
```

### Process Post Response
To process the response coming from payfort and to make sure it's valid you can use the following code snippet:
```php
try{
    $tokenization = Payfort::validateTokenizationResponse(request()->post());
}catch (RequestFailed|PaymentFailed $requestFailed){
    // Handle Error
}
```
it will throw exception `\RSE\PayfortForLaravel\Exceptions\PaymentFailed`, if the response is not valid.

if the transaction is done successfully you can get the transaction fort id by using this:
```php
$response->getFortId();
```

or the used payment method by this:
```php
$response->getPaymentOption()
```

### Capture
Used only after authorization, to send a capture command use code below:
```php
Payfort::capture(
    'fort_id', # fort id for the payment transaction
    100.0 # bill amount
);
```

### Void
Used only after authorization, to send a void command use code below:
```php
Payfort::void(
    'fort_id' # fort id for the payment transaction
);
```

### Refund
Used only after purchase, to send a refund command use the code below:
```php
Payfort::refund(
    'fort_id', # fort id for the payment transaction
    1000 # amount to be reunded must not exceed the bill amount
);
```

### Merchant extra
Payfort support sending extra fields to the request and they will be returned back to you on the response, so to add merchant extras to any command, you do the following:
```php
Payfort::setMerchantExtra('test')->tokenization(
    1000, # Bill amount
    'redirect_url', # the recirect to url after tokenization
    true # either to return form html or not (optional)
);
```

you can use this method `setMerchantExtra` before any command you want, and you have max 5 extras to add.


## Payment Link (Invoice)
Payfort support creating invoices and send it to your customers.
Inside your controller add the below code
```php
$payfort = app(\RSE\PayfortForLaravel\PayfortIntegration::class);

try{
    $paymentLink = $payfort->paymentLink(
        $amount,
        'test@test.com',
        now()->addDays(15),
        'return_url',           // callback url after finalizing payment
        ['EMAIL'],
        \Illuminate\Support\Str::ulid(),
    )->setCustomerMobile('+966xxxxxxxx')
    ->setCustomerName('NAME')
    ->setOrderDescription('Invoice 1')
    ->setPaymentId('100001')
    ->handle();
} catch (\RSE\PayfortForLaravel\Exceptions\RequestFailed $requestFailed){
    // Handle Error
} catch (\RSE\PayfortForLaravel\Exceptions\PaymentFailed $paymentFailed){
    // Handle Error
}

$link = $paymentLink->getPaymentLink();
```

In your callback controller's method:

```php

$payfort = app(\RSE\PayfortForLaravel\PayfortIntegration::class);

try{
    $response = $payfort->validatePaymentLinkPostResponse(request()->post());
} catch (\RSE\PayfortForLaravel\Exceptions\RequestFailed $requestFailed){
    // Handle Error
} catch (\RSE\PayfortForLaravel\Exceptions\PaymentFailed $paymentFailed){
    // Handle Error
}

if($response->isPaymentSuccessful()){
    // Handle Success Payment
}else{
    // Handle Failed Payment
}
```

## Logging
To log your requests with payfort you can listen to this event `\RSE\PayfortForLaravel\Events\PayfortMessageLog` it will contain the data sent and the resposne

This is an example on how it can be used:
```php
$log = app(PayfortLog::class);

$log->contract_id = data_get($event->request, 'merchant_extra', data_get($event->response, 'merchant_extra', null));

if (isset($event->response['card_number'])) {
    $last_four_digits = substr($event->response['card_number'], -4);
    $log->card_number = '************'.$last_four_digits;
}

if (isset($event->response['amount'])) {
    $log->amount = floatval($event->response['amount'] / 100);
}

if (isset($event->response['response_message'])) {
    $log->response_message = data_get($event->response, 'response_message');
}

if (isset($event->response['merchant_reference'])) {
    $log->merchant_reference = $event->response['merchant_reference'];
}

if (isset($event->request['merchant_reference'])) {
    $log->merchant_reference = $event->request['merchant_reference'];
}

$log->fort_id = data_get($event->response, 'fort_id');
$log->payment_option = data_get($event->response, 'payment_option');
$log->command = data_get($event->response, 'command', data_get($event->response, 'service_command'));
$log->response_code = data_get($event->response, 'response_code');

$log->request = $event->request ? json_encode($event->request) : "";
$log->response = json_encode($event->response);

$log->save();
```

## License

The MIT License (MIT). Please see License File for more information.
