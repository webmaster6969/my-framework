<?php

namespace App\Http\Controllers;

use Core\Storage\File;
use Core\Storage\Storage;
use Core\Support\App\App;
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

    public function uplode(): bool
    {
        $storage = new Storage(App::getBasePath() . '/storage/');
        $file = File::fromGlobals('file');
        return $storage->move($file, 'app');
    }
}
