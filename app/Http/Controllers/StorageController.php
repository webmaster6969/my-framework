<?php

namespace App\Http\Controllers;

use Core\Http\Request;
use Core\Storage\File;
use Core\Storage\Storage;
use Core\Support\App\App;
use Core\Support\Csrf\Csrf;
use Core\View\View;
use Exception;

class StorageController
{
    /**
     * @throws Exception
     */
    public function index()
    {
        $view = new View();
        echo $view->render('storage.index');
    }

    public function uplode()
    {
        $csrfToken = Request::input('csrf_token');

        if (!Csrf::check($csrfToken)) {
            header('Location: /login');
            exit;
        }

        $storage = new Storage(App::getBasePath() . '/storage/');
        $file = File::fromGlobals('file');
        $storage->move($file, 'app');

        header('Location: /storage');
        exit;
    }
}
