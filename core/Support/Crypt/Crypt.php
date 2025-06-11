<?php

declare(strict_types=1);

namespace Core\Support\Crypt;

use RuntimeException;

class Crypt
{
    /**
     * @param string $data
     * @param string $encryptionKey
     * @param string $encryptionMethod
     * @return string
     */
    public static function encrypt(string $data, string $encryptionKey, string $encryptionMethod = 'AES-256-CBC'): string
    {
        $key = hash('sha256', $encryptionKey, true);
        $iv = openssl_random_pseudo_bytes(16);

        $cipherText = openssl_encrypt($data, $encryptionMethod, $key, OPENSSL_RAW_DATA, $iv);

        if ($cipherText === false) {
            throw new RuntimeException('Encryption failed.');
        }

        return base64_encode($iv . $cipherText);
    }

    /**
     * @param string $encryptedData
     * @param string $encryptionKey
     * @param string $encryptionMethod
     * @return string
     */
    public static function decrypt(string $encryptedData, string $encryptionKey, string $encryptionMethod = 'AES-256-CBC'): string
    {
        $data = base64_decode($encryptedData, true);

        if ($data === false || strlen($data) < 16) {
            throw new RuntimeException('Invalid encrypted data.');
        }

        $iv = substr($data, 0, 16);
        $cipherText = substr($data, 16);

        $key = hash('sha256', $encryptionKey, true);

        $plainText = openssl_decrypt($cipherText, $encryptionMethod, $key, OPENSSL_RAW_DATA, $iv);

        if ($plainText === false) {
            throw new RuntimeException('Decryption failed.');
        }

        return $plainText;
    }
}