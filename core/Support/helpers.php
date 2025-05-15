<?php

const ENCRYPTION_KEY = 'your-32-byte-secret-key-goes-here-123!'; // длина должна быть 32 байта
const ENCRYPTION_METHOD = 'AES-256-CBC';

function encrypt(string $plainText): string
{
    $key = hash('sha256', ENCRYPTION_KEY, true); // 32 байта
    $iv = openssl_random_pseudo_bytes(16); // 16 байт для AES-256-CBC

    $cipherText = openssl_encrypt($plainText, ENCRYPTION_METHOD, $key, OPENSSL_RAW_DATA, $iv);

    if ($cipherText === false) {
        throw new RuntimeException('Encryption failed.');
    }

    // Возвращаем base64 строку, содержащую IV + зашифрованный текст
    return base64_encode($iv . $cipherText);
}

function decrypt(string $encryptedBase64): string
{
    $data = base64_decode($encryptedBase64, true);

    if ($data === false || strlen($data) < 16) {
        throw new RuntimeException('Invalid encrypted data.');
    }

    $iv = substr($data, 0, 16);
    $cipherText = substr($data, 16);

    $key = hash('sha256', ENCRYPTION_KEY, true); // 32 байта

    $plainText = openssl_decrypt($cipherText, ENCRYPTION_METHOD, $key, OPENSSL_RAW_DATA, $iv);

    if ($plainText === false) {
        throw new RuntimeException('Decryption failed.');
    }

    return $plainText;
}

if (!function_exists('view')) {
    /**
     * @throws Exception
     */
    function view(string $view, array $data = []): void
    {
        extract($data);
        $viewPath = __DIR__ . '/../../resources/views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            throw new Exception("View not found: $viewPath");
        }

        include $viewPath;
    }
}

if (!function_exists('session')) {
    function session(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $_SESSION ?? [];
        }

        return $_SESSION[$key] ?? $default;
    }
}