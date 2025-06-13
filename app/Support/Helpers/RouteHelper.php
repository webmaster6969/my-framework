<?php

if (!function_exists('generateUrlWithParams')) {
    /**
     * @param array<string, scalar> $newParams
     * @return string
     */
    function generateUrlWithParams(array $newParams = []): string {
        $uri = '/';
        if (isset($_SERVER['REQUEST_URI']) && is_string($_SERVER['REQUEST_URI'])) {
            $uri = $_SERVER['REQUEST_URI'];
        }

        $parts = parse_url($uri);
        $path = $parts['path'] ?? '/';
        parse_str($parts['query'] ?? '', $currentParams);

        $finalParams = array_merge($currentParams, $newParams);
        $queryString = http_build_query($finalParams);

        return $path . ($queryString ? '?' . $queryString : '');
    }
}