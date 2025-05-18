<?php

namespace Tests\Unit;

use Core\View\View;
use Exception;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testRenderView()
    {
        $view = new View();
        $output = $view->render('tests.hello', ['title' => 'Test']);

        $this->assertStringContainsString('<title>Test</title>', $output);
    }
}