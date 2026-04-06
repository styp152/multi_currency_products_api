<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected $defaultHeaders = [
        'X-API-Key' => 'test-api-key',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('api.write_key', 'test-api-key');
    }
}
