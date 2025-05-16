<?php

namespace Core\Support;

use RuntimeException;

class Crypt
{
    protected static string $ENCRYPTION_KEY = 'your-32-byte-secret-key-goes-here-123!'; // длина должна быть 32 байта
    protected static string $ENCRYPTION_METHOD = 'AES-256-CBC';

    protected static Crypt $instance;

    public function __construct(string $ENCRYPTION_KEY, string $ENCRYPTION_METHOD = 'AES-256-CBC')
    {
        static::$ENCRYPTION_KEY = $ENCRYPTION_KEY;
        static::$ENCRYPTION_METHOD = $ENCRYPTION_METHOD;

        static::$instance = $this;
    }

    public static function encrypt(string $data): string
    {
        $key = hash('sha256', static::$ENCRYPTION_KEY, true); // 32 байта
        $iv = openssl_random_pseudo_bytes(16); // 16 байт для AES-256-CBC

        $cipherText = openssl_encrypt($data, static::$ENCRYPTION_METHOD, $key, OPENSSL_RAW_DATA, $iv);

        if ($cipherText === false) {
            throw new RuntimeException('Encryption failed.');
        }

        // Возвращаем base64 строку, содержащую IV + зашифрованный текст
        return base64_encode($iv . $cipherText);
    }

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