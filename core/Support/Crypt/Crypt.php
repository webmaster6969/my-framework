<?php

declare(strict_types=1);

namespace Core\Support\Crypt;

use RuntimeException;

class Crypt
{
    /**
     * @var string
     */
    protected static string $ENCRYPTION_KEY = 'your-32-byte-secret-key-goes-here-123!';
    /**
     * @var string
     */
    protected static string $ENCRYPTION_METHOD = 'AES-256-CBC';

    /**
     * @var Crypt
     */
    protected static Crypt $instance;

    /**
     * @param string $ENCRYPTION_KEY
     * @param string $ENCRYPTION_METHOD
     */
    public function __construct(string $ENCRYPTION_KEY, string $ENCRYPTION_METHOD = 'AES-256-CBC')
    {
        static::$ENCRYPTION_KEY = $ENCRYPTION_KEY;
        static::$ENCRYPTION_METHOD = $ENCRYPTION_METHOD;

        static::$instance = $this;
    }

    /**
     * @param string $data
     * @return string
     */
    public static function encrypt(string $data): string
    {
        $key = hash('sha256', static::$ENCRYPTION_KEY, true);
        $iv = openssl_random_pseudo_bytes(16);

        $cipherText = openssl_encrypt($data, static::$ENCRYPTION_METHOD, $key, OPENSSL_RAW_DATA, $iv);

        if ($cipherText === false) {
            throw new RuntimeException('Encryption failed.');
        }

        return base64_encode($iv . $cipherText);
    }

    /**
     * @param string $encryptedData
     * @return string
     */
    public static function decrypt(string $encryptedData): string
    {
        $data = base64_decode($encryptedData, true);

        if ($data === false || strlen($data) < 16) {
            throw new RuntimeException('Invalid encrypted data.');
        }

        $iv = substr($data, 0, 16);
        $cipherText = substr($data, 16);

        $key = hash('sha256', static::$ENCRYPTION_KEY, true);

        $plainText = openssl_decrypt($cipherText, static::$ENCRYPTION_METHOD, $key, OPENSSL_RAW_DATA, $iv);

        if ($plainText === false) {
            throw new RuntimeException('Decryption failed.');
        }

        return $plainText;
    }
}