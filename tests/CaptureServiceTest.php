<?php

namespace RSE\PayfortForLaravel\Test;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use RSE\PayfortForLaravel\Test\TestCase;
use RSE\PayfortForLaravel\Facades\Payfort;
use RSE\PayfortForLaravel\Services\CaptureService;

class CaptureServiceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Http::fake([
            '*' => Http::response([
                'response_code' => '04000',
                'response_message' => '04000'
            ])
        ]);
    }

    /** @test */
    public function capture_service_send_the_required_params()
    {
        $fort_id = 123123;

        $this->partialMock(CaptureService::class, function ($mock) {
            $mock->shouldAllowMockingProtectedMethods();

            $mock->shouldReceive('validateSignature')->andReturnSelf();
            $mock->shouldReceive('validateResponseCode')->andReturnSelf();
            $mock->shouldReceive('calculateSignature')->andReturn("signature");

            $mock->shouldReceive('getOperationUrl')->andReturn('test_link');
        });

        Payfort::capture($fort_id, 1000);

        Http::assertSent(function (Request $request) use ($fort_id) {
            return count(array_diff($request->data(), [
                'command' => 'CAPTURE',
                'access_code' => null,
                'merchant_identifier' => null,
                'amount' => 100000.0,
                'currency' => 'SAR',
                'language' => 'en',
                'fort_id' => $fort_id,
                "signature" => "signature",
            ])) === 0 && $request->url() === 'test_link' && $request->method() === 'POST';
        });
    }

    /** @test */
    public function capture_service_add_merchant_extras()
    {
        $fort_id = 123123;

        $this->partialMock(CaptureService::class, function ($mock) {
            $mock->shouldAllowMockingProtectedMethods();

            $mock->shouldReceive('validateSignature')->andReturnSelf();
            $mock->shouldReceive('validateResponseCode')->andReturnSelf();
            $mock->shouldReceive('calculateSignature')->andReturn("signature");

            $mock->shouldReceive('getOperationUrl')->andReturn('test_link');
        });

        Payfort::setMerchantExtra(500)->capture($fort_id, 1000);

        Http::assertSent(function (Request $request) use ($fort_id) {
            return count(array_diff($request->data(), [
                'command' => 'CAPTURE',
                'access_code' => null,
                'merchant_identifier' => null,
                'amount' => 100000.0,
                'currency' => 'SAR',
                'language' => 'en',
                'merchant_extra' => 500,
                'fort_id' => $fort_id,
                "signature" => "signature",
            ])) === 0 && $request->url() === 'test_link' && $request->method() === 'POST';
        });
    }
}
