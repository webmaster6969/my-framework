<?php

namespace App\domain\Task\Presentation\HTTP;

use Core\View\View;
use Exception;

class TaskController
{
    /**
     * @throws Exception
     */
    public function index()
    {
        $view = new View();

        echo $view->render('task.index');
    }
}
