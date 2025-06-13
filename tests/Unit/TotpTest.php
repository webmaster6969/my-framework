<?php

declare(strict_types=1);

namespace Unit;

use Core\Totp\Totp;
use Core\Totp\TotpException;
use Exception;
use PHPUnit\Framework\TestCase;

final class TotpTest extends TestCase
{
    /**
     * @var Totp
     */
    private Totp $totp;

    /**
     * @return void
     * @throws TotpException
     */
    protected function setUp(): void
    {
        $this->totp = new Totp();
        $this->totp->configure([
            'algorithm' => 'SHA1',
            'digits' => 6,
            'period' => 30,
        ]);
    }

    /**
     * @return void
     */
    public function testConfigureValidOptions(): void
    {
        $this->assertSame('SHA1', $this->totp->getAlgorithm());
        $this->assertSame(6, $this->totp->getDigits());
        $this->assertSame(30, $this->totp->getPeriod());
    }

    /**
     * @return void
     * @throws TotpException
     */
    public function testConfigureInvalidAlgorithm(): void
    {
        $this->expectException(TotpException::class);
        $this->totp->configure(['algorithm' => 'md5']);
    }

    /**
     * @return void
     * @throws TotpException
     */
    public function testConfigureInvalidDigits(): void
    {
        $this->expectException(TotpException::class);
        $this->totp->configure(['digits' => 5]);
    }

    /**
     * @return void
     * @throws TotpException
     */
    public function testConfigureInvalidPeriod(): void
    {
        $this->expectException(TotpException::class);
        $this->totp->configure(['period' => 0]);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testGenerateSecret(): void
    {
        $secret = $this->totp->generateSecret();
        $this->assertMatchesRegularExpression('/^[A-Z2-7]+=*$/', $secret);
    }

    /**
     * @return void
     * @throws TotpException
     */
    public function testGetCodeAndVerify(): void
    {
        $secret = $this->totp->generateSecret();
        $timeSlice = intdiv(time(), $this->totp->getPeriod());
        $code = $this->totp->getCode($secret, $timeSlice);

        $this->assertMatchesRegularExpression('/^\d{6}$/', $code);
        $this->assertTrue($this->totp->verifyCode($secret, $code, 0, $timeSlice));
    }

    /**
     * @return void
     * @throws TotpException
     */
    public function testVerifyCodeInvalid(): void
    {
        $secret = $this->totp->generateSecret();
        $this->assertFalse($this->totp->verifyCode($secret, '123456', 0));
    }

    /**
     * @return void
     * @throws TotpException
     */
    public function testGenerateUri(): void
    {
        $secret = $this->totp->generateSecret();
        $uri = $this->totp->generateUri($secret, 'user@example.com', 'MyApp');

        $this->assertStringContainsString('otpauth://totp/', $uri);
        $this->assertStringContainsString('issuer=MyApp', $uri);
        $this->assertStringContainsString("secret={$secret}", $uri);
        $this->assertStringContainsString('digits=6', $uri);
        $this->assertStringContainsString('period=30', $uri);
        $this->assertStringContainsString('algorithm=SHA1', $uri);
    }
}