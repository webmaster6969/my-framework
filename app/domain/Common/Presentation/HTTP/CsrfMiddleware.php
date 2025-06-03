<?php

declare(strict_types=1);

namespace App\domain\Common\Presentation\HTTP;

use App\domain\Common\Domain\Exceptions\CsrfException;
use Core\Http\Middleware\MiddlewareInterface;
use Core\Http\Request;
use Core\Support\Csrf\Csrf;

class CsrfMiddleware implements MiddlewareInterface
{
    /**
     * @param callable $next
     * @return mixed
     */
    public function handle(callable $next): mixed
    {
        $csrfToken = Request::input('csrf_token');

        if (empty($csrfToken) || (is_string($csrfToken) && !Csrf::check($csrfToken))) {
            throw new CsrfException('Csrf error');
        }

        return $next();
    }
}
