<?php

declare(strict_types=1);

namespace Core\View;

use Core\Support\App\App;
use Exception;

class View
{
    /**
     * @var string
     */
    protected string $viewsPath;

    /**
     * @var string
     */
    protected string $view;

    /**
     * @var array<string, mixed>
     */
    protected array $data = [];

    /**
     * @param string $view
     * @param array<string, mixed> $data
     * @param string $viewsPath
     */
    public function __construct(string $view = '', array $data = [], string $viewsPath = '/../../resources/views')
    {
        $this->viewsPath = __DIR__ . rtrim($viewsPath, '/');
        $this->view = $view;
        $this->data = $data;
    }

    /**
     * @throws Exception
     */
    public function render(): string
    {
        $templatePath = $this->viewsPath . '/' . str_replace('.', '/', $this->view) . '.php';

        if (!file_exists($templatePath)) {
            throw new Exception("View [$this->view] not found at $templatePath");
        }

        $template = file_get_contents($templatePath);
        if ($template === false) {
            throw new Exception("Failed to read view file at $templatePath");
        }

        $compiled = $this->compileTemplate($template);

        extract($this->data, EXTR_SKIP);

        ob_start();
        eval('?>' . $compiled);
        $output = ob_get_clean();

        return $output !== false ? $output : '';
    }

    /**
     * @param string $template
     * @return string
     */
    protected function compileTemplate(string $template): string
    {
        $template = preg_replace_callback('/@include\s*\(\s*[\'"](.*?)[\'"]\s*\)/', function ($matches) {
            $includedPath = $this->viewsPath . '/' . str_replace('.', '/', $matches[1]) . '.php';
            if (!file_exists($includedPath)) {
                return '';
            }
            $included = file_get_contents($includedPath);
            if ($included === false) {
                return '';
            }
            return $this->compileTemplate($included);
        }, $template) ?? '';

        $template = preg_replace_callback('/{{\s*(.+?)\s*}}/', function ($matches) {
            return '<?= htmlspecialchars(' . $matches[1] . ', ENT_QUOTES, \'UTF-8\') ?>';
        }, $template);

        if ($template === null) {
            $template = '';
        }

        $template = preg_replace('/@if\s*\((.*?)\)/', '<?php if ($1): ?>', $template) ?? '';
        $template = preg_replace('/@elseif\s*\((.*?)\)/', '<?php elseif ($1): ?>', $template) ?? '';
        $template = preg_replace('/@else/', '<?php else: ?>', $template) ?? '';
        $template = preg_replace('/@endif/', '<?php endif; ?>', $template) ?? '';

        $template = preg_replace('/@foreach\s*\((.*?)\)/', '<?php foreach ($1): ?>', $template) ?? '';
        return preg_replace('/@endforeach/', '<?php endforeach; ?>', $template) ?? '';
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function with(string $key, mixed $value): View
    {
        $this->data[$key] = $value;
        return $this;
    }
}