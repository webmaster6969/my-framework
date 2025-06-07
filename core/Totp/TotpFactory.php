<?php

declare(strict_types=1);

namespace Core\Totp;

final class TotpFactory
{
    /**
     * Creates a new instance of the TOTP class.
     *
     * @param array<string, mixed> $options Configuration options for the TOTP instance.
     *        Supported options: 'algorithm' (string), 'digits' (int), 'period' (int).
     * @return TotpInterface A configured TOTP instance.
     * @throws TotpException If the configuration options are invalid.
     */
    public static function create(array $options = []): TotpInterface
    {
        $totp = new Totp();

        if ($options !== []) {
            $totp->configure($options);
        }

        return $totp;
    }
}
