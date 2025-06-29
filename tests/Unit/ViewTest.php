<?php

declare(strict_types=1);

namespace Unit;

use Core\View\View;
use Exception;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    /**
     * @return void
     * @throws Exception
     */
    public function testRenderView()
    {
        $view = new View('tests.index', ['title' => 'Test']);
        $output = $view->render();

        $this->assertStringContainsString('<title>Test</title>', $output);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testRenderWithView()
    {
        $view = new View('tests.index')->with('title', 'Test');
        $output = $view->render();

        $this->assertStringContainsString('<title>Test</title>', $output);
    }
}