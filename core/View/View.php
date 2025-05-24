<?php

namespace Core\View;

use Exception;

class View
{
    protected string $viewsPath;

    public function __construct(string $viewsPath = __DIR__ . '/../../resources/views')
    {
        $this->viewsPath = rtrim($viewsPath, '/');
    }

    /**
     * @throws Exception
     */
    public function render(string $view, array $data = []): string
    {
        $templatePath = $this->viewsPath . '/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($templatePath)) {
            throw new Exception("View [$view] not found at $templatePath");
        }

        $template = file_get_contents($templatePath);

        $compiled = $this->compileTemplate($template);
        extract($data);
        ob_start();
        eval('?>' . $compiled);
        return ob_get_clean();
    }

    protected function compileTemplate(string $template): string
    {
        // Поддержка выражений вида {{ $user->name }}
        $template = preg_replace_callback('/{{\s*(.+?)\s*}}/', function ($matches) {
            return '<?= htmlspecialchars(' . $matches[1] . ') ?>';
        }, $template);

        // Управляющие конструкции
        $template = preg_replace('/@if\s*\((.*?)\)/', '<?php if ($1): ?>', $template);
        $template = preg_replace('/@elseif\s*\((.*?)\)/', '<?php elseif ($1): ?>', $template);
        $template = preg_replace('/@else/', '<?php else: ?>', $template);
        $template = preg_replace('/@endif/', '<?php endif; ?>', $template);

        $template = preg_replace('/@foreach\s*\((.*?)\)/', '<?php foreach ($1): ?>', $template);
        $template = preg_replace('/@endforeach/', '<?php endforeach; ?>', $template);

        $template = preg_replace_callback('/@include\s*\(\s*[\'"](.*?)[\'"]\s*\)/', function ($matches) {
            $included = file_get_contents($this->viewsPath . '/' . str_replace('.', '/', $matches[1]) . '.php');
            return $this->compileTemplate($included);
        }, $template);

        return $template;
    }

}
