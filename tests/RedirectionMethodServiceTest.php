<?php

namespace RSE\PayfortForLaravel\Test;

use RSE\PayfortForLaravel\Services\RedirectionMethodService;
use RSE\PayfortForLaravel\Test\TestCase;
use RSE\PayfortForLaravel\Facades\Payfort;
use RSE\PayfortForLaravel\Services\TokenizationService;

class RedirectionMethodServiceTest extends TestCase
{
    /** @test */
    public function params_are_set_correctly()
    {
        $returnUrl = 'http://localhost';

        $this->partialMock(RedirectionMethodService::class, function ($mock) {
            $mock->shouldAllowMockingProtectedMethods();

            $mock->shouldReceive('generateMerchantReference')->andReturn('refernece');

            $mock->shouldReceive('calculateSignature')->andReturn('signature');
        });

        $return = Payfort::redirectionMethod(100, 'test@test.com', $returnUrl);

        unset($return['form']);

        $this->assertEquals([
            "command" => "PURCHASE",
            "access_code" => null,
            "merchant_identifier" => null,
            "merchant_reference" => "refernece",
            "amount" => 10000.0,
            "currency" => "SAR",
            "customer_email" => "test@test.com",
            "return_url" => "http://localhost",
            "language" => "en",
            "signature" => "signature",
        ], $return);
    }

    /** @test */
    public function return_form_params()
    {
        $returnUrl = 'http://localhost';

        $this->partialMock(TokenizationService::class, function ($mock) {
            $mock->shouldAllowMockingProtectedMethods();

            $mock->shouldReceive('generateMerchantReference')->andReturn('refernece');

            $mock->shouldReceive('calculateSignature')->andReturn('signature');
        });

        $return = Payfort::redirectionMethod(100, 'test@test.com', $returnUrl);

        $this->assertArrayHasKey('form', $return);
    }

    /** @test */
    public function can_set_merchant_extras_successfully()
    {
        $returnUrl = 'http://localhost';

        $this->partialMock(TokenizationService::class, function ($mock) {
            $mock->shouldAllowMockingProtectedMethods();

            $mock->shouldReceive('generateMerchantReference')->andReturn('refernece');

            $mock->shouldReceive('calculateSignature')->andReturn('signature');
        });

        $return = Payfort::setMerchantExtra(100, "new")
                         ->redirectionMethod(100, 'test@test.com', $returnUrl);


        $this->assertEquals("new", $return['merchant_extra1']);
    }
}
