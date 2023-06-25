<?php

namespace RSE\PayfortForLaravel\Test;

use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

class TestCase extends TestbenchTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
    }


    /**
     * Override application aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Payfort' => \RSE\PayfortForLaravel\Facades\Payfort::class,
        ];
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \RSE\PayfortForLaravel\Providers\PayfortServiceProvider::class,
        ];
    }
}
