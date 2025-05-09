<?php

namespace App\Http\Controllers;

use Core\View\View;
use Exception;

class ApiController
{
    /**
     * @throws Exception
     */
    public function index(int $id)
    {
        $view = new View();
        echo $view->render('home', ['name' => $id]);
    }
}
