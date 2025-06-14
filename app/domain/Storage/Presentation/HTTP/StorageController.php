<?php

declare(strict_types=1);

namespace App\domain\Storage\Presentation\HTTP;

use App\domain\Storage\Application\Repositories\StorageRepository;
use App\domain\Storage\Application\UseCases\Commands\MoveCommand;
use App\domain\Storage\Application\UseCases\Commands\UplodeCommand;
use App\domain\Storage\Domain\Exceptions\NotUplodeFileException;
use Core\Response\Response;
use Core\Routing\Redirect;
use Core\Storage\File;
use Core\Support\Session\Session;
use Core\Validator\Validator;
use Core\View\View;
use Exception;

class StorageController
{
    /**
     * @throws Exception
     */
    public function index(): Response
    {
        $view = new View('storage.index', ['errors' => Session::error()])
            ->with('title', t('Storage'));

        return Response::make($view)
            ->withHeaders(['Content-Type' => 'text/html',])
            ->withStatus(200);
    }

    /**
     * @return Response
     */
    public function uplode(): Response
    {
        $data = [
            'file' => $_FILES['file'] ?? null,
        ];

        $rules = [
            'file' => 'required|mimes:jpg,png,jpeg',
        ];

        $validator = new Validator($data, $rules);

        if ($validator->fails()) {
            return Response::make(
                Redirect::to('/storage')
                ->with('data', $data)
                ->withErrors($validator->errors()));
        }

        $file = File::fromGlobals('file');

        if ($file === null) {
            throw new NotUplodeFileException('File not uploaded or invalid');
        }

        $uplodeCommand = new UplodeCommand(new StorageRepository(), $file);
        $uplodeCommandExecute = $uplodeCommand->execute();

        if (!$uplodeCommandExecute) {
            throw new NotUplodeFileException('Not upload file');
        }

        $moveCommand = new MoveCommand(new StorageRepository(), $file, 'app');
        $moveExecute = $moveCommand->execute();

        if (!$moveExecute) {
            throw new NotUplodeFileException('Not upload file');
        }

        return Response::make(Redirect::to('/storage'));
    }
}