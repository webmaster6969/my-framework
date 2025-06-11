<?php

declare(strict_types=1);

namespace Unit;

use PHPUnit\Framework\TestCase;
use Core\Support\Crypt\Crypt;
use RuntimeException;

final class CryptTest extends TestCase
{
    /**
     * @var string
     */
    private string $key = 'super-secret-key';

    /**
     * @return void
     */
    public function testEncryptAndDecryptReturnOriginalData(): void
    {
        $originalData = 'This is a secret message.';

        $encrypted = Crypt::encrypt($originalData, $this->key);
        $decrypted = Crypt::decrypt($encrypted, $this->key);

        $this->assertSame($originalData, $decrypted, 'Decrypted data must match the original');
    }

    /**
     * @return void
     */
    public function testEncryptReturnsDifferentOutputEachTime(): void
    {
        $data = 'repeatable text';

        $encrypted1 = Crypt::encrypt($data, $this->key);
        $encrypted2 = Crypt::encrypt($data, $this->key);

        $this->assertNotSame($encrypted1, $encrypted2, 'Encrypted outputs should differ due to random IV');
    }

    /**
     * @return void
     */
    public function testDecryptWithInvalidDataThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid encrypted data.');

        Crypt::decrypt('invalid_base64_data', $this->key);
    }

    /**
     * @return void
     */
    public function testDecryptWithWrongKeyThrowsException(): void
    {
        $data = 'top secret';
        $encrypted = Crypt::encrypt($data, $this->key);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Decryption failed.');

        Crypt::decrypt($encrypted, 'wrong-key');
    }

    /**
     * @return void
     */
    public function testDecryptWithCorruptedIvThrowsException(): void
    {
        $data = 'secure';
        $encrypted = Crypt::encrypt($data, $this->key);

        $decoded = base64_decode($encrypted);
        $corrupted = str_repeat('x', 16) . substr($decoded, 16);
        $corruptedEncoded = base64_encode($corrupted);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Decryption failed.');

        Crypt::decrypt($corruptedEncoded, $this->key);
    }
}