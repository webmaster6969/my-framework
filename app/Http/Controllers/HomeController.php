<?php

namespace App\Http\Controllers;

use Core\View\View;
use Exception;

class HomeController
{
    /**
     * @throws Exception
     */
    public function index()
    {
        $view = new View();
        echo $view->render('home', ['name' => 'John']);
    }
}
