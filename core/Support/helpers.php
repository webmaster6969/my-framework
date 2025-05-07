<?php

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
