<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\TotpService;
use Tests\TestCase;

class TotpServiceTest extends TestCase
{
    public function test_generates_and_verifies_totp_code(): void
    {
        $service = new TotpService;
        $secret = $service->generateSecret();
        $code = $service->getCode($secret);

        $this->assertTrue($service->verify($secret, $code));
        $this->assertFalse($service->verify($secret, '000000'));
    }

    public function test_qr_uri_contains_otpauth(): void
    {
        $service = new TotpService;
        $secret = $service->generateSecret();
        $uri = $service->getQrUri($secret, 'admin@test.com');

        $this->assertStringStartsWith('otpauth://totp/', $uri);
        $this->assertStringContainsString('Precifique', $uri);
    }
}
