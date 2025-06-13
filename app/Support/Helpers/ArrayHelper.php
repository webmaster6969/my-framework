<?php

declare(strict_types=1);

if (!function_exists('old')) {
    /**
     * @param string $key
     * @param array<string, mixed> $data
     * @return string
     */
    function old(string $key, array $data): string
    {
        if (!array_key_exists($key, $data)) {
            return '';
        }

        $value = $data[$key];

        if (is_string($value)) {
            return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }

        if (is_scalar($value)) {
            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
        }

        return '';
    }
}

if (!function_exists('showErrors')) {
    /**
     * @param string $key
     * @param array<string, mixed> $errors
     * @return void
     */
    function showErrors(string $key, array $errors): void
    {
        if (!empty($errors[$key]) && is_array($errors[$key])) {
            $escapedErrors = array_map(function ($e) {
                if (is_string($e)) {
                    return htmlspecialchars($e, ENT_QUOTES, 'UTF-8');
                }
                if (is_scalar($e)) {
                    return htmlspecialchars((string)$e, ENT_QUOTES, 'UTF-8');
                }
                if (is_object($e) && method_exists($e, '__toString')) {
                    return htmlspecialchars((string)$e, ENT_QUOTES, 'UTF-8');
                }
                return '';
            }, $errors[$key]);

            echo '<span class="text-danger">' . implode(', ', $escapedErrors) . '</span>';
        }
    }
}