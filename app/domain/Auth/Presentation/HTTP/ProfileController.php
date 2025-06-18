<?php

declare(strict_types=1);

namespace App\domain\Auth\Presentation\HTTP;

use App\domain\Auth\Application\Repositories\UserRepository;
use App\domain\Auth\Application\UseCases\Commands\UpdateUserCommand;
use App\domain\Auth\Services\AuthService;
use App\domain\Common\Domain\Exceptions\EncryptionKeyIsNotFindException;
use App\domain\Task\Services\TaskServices;
use Core\Database\DB;
use Core\Http\Request;
use Core\Response\Response;
use Core\Routing\Redirect;
use Core\Support\Env\Env;
use Core\Support\Session\Session;
use Core\Validator\Validator;
use Core\View\View;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;
use Throwable;

class ProfileController
{
    /**
     * @throws Exception
     */
    public function index(): Response
    {
        $user = AuthService::getUser();

        if ($user === null) {
            return Response::make(Redirect::to('/login'));
        }

        $view = new View('auth.profile', ['user' => $user, 'errors' => Session::error()])
            ->with('title', t('Profile'));

        return Response::make($view)
            ->withHeaders(['Content-Type' => 'text/html',])
            ->withStatus(200);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws \Doctrine\DBAL\Exception
     * @throws Throwable
     */
    public function update(): Response
    {
        $name = Request::input('name');
        $encryptionKey = Request::input('encryption_key');

        $data = [
            'name' => $name,
            'encryption_key' => $encryptionKey
        ];

        $rules = [
            'name' => 'required|min:3|max:100',
            'encryption_key' => 'min:10|max:255',
        ];

        $validator = new Validator($data, $rules);
        if ($validator->fails()) {
            return Response::make(
                Redirect::to('/profile')
                    ->with('data', $data)
                    ->withErrors($validator->errors())
            );
        }

        $user = AuthService::getUser();

        if ($user === null || !is_string($name)) {
            return Response::make(Redirect::to('/login'));
        }

        DB::beginTransaction();
        $oldEncryptionKey = !empty($user->getEncryptionKey()) ? $user->getEncryptionKey() : Env::get('ENCRYPTION_KEY');

        if (empty($oldEncryptionKey) || !is_string($oldEncryptionKey)) {
            throw new EncryptionKeyIsNotFindException('ENCRYPTION_KEY environment variable is not set');
        }

        try {
            if (!empty($user->getEncryptionKey()) && empty($encryptionKey)) {
                $user->setEncryptionKey(null);
                TaskServices::reEncryptDescription($user, $oldEncryptionKey);
            } elseif (
                !empty($encryptionKey) &&
                is_string($encryptionKey) &&
                $encryptionKey !== $user->getEncryptionKey()
            ) {
                $user->setEncryptionKey($encryptionKey);
                TaskServices::reEncryptDescription($user, $oldEncryptionKey);
            }

            $user->setName($name);

            $command = new UpdateUserCommand(new UserRepository(DB::getEntityManager()), $user);
            $command->execute();

            DB::commit();
        } catch (Throwable $e) {
            DB::rollback();
            throw $e;
        }

        return Response::make(Redirect::to('/profile'));
    }
}