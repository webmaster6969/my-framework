<?php

declare(strict_types=1);

namespace App\domain\Storage\Presentation\HTTP;

use App\domain\Storage\Application\Repositories\StorageRepository;
use App\domain\Storage\Application\UseCases\Commands\MoveCommand;
use App\domain\Storage\Application\UseCases\Commands\UplodeCommand;
use App\domain\Storage\Domain\Exceptions\NotUplodeFileException;
use Core\Http\Request;
use Core\Routing\Redirect;
use Core\Storage\File;
use Core\Support\Csrf\Csrf;
use Core\Support\Session\Session;
use Core\Validator\Validator;
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
        echo $view->render('storage.index', ['errors' => Session::error()]);
    }

    public function uplode()
    {
        $csrfToken = Request::input('csrf_token');

        if (!Csrf::check($csrfToken)) {
            Redirect::to('/login')->send();
        }

        $data = [
            'file' => $_FILES['file'] ?? null,
        ];

        $rules = [
            'file' => 'required|mimes:jpg,png,jpeg',
        ];

        $validator = new Validator($data, $rules);

        if ($validator->fails()) {
            Redirect::to('/storage')
                ->with('data', $data)
                ->withErrors($validator->errors())
                ->send();
        }

        $uplodeCommand = new UplodeCommand(new StorageRepository(), File::fromGlobals('file'));

        if (empty($uplodeCommand->execute())) {
            throw new NotUplodeFileException('Not upload file');
        }

        $moveCommand = new MoveCommand(new StorageRepository(), File::fromGlobals('file'), 'app');

        if (empty($moveCommand->execute())) {
            throw new NotUplodeFileException('Not upload file');
        }

        Redirect::to('/storage')->send();
    }
}
