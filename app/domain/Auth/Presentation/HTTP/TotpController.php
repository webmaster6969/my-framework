<?php

declare(strict_types=1);

namespace App\domain\Auth\Presentation\HTTP;

use App\domain\Auth\Application\Repositories\UserRepository;
use App\domain\Auth\Application\UseCases\Commands\DisableTwoFactoryCommand;
use App\domain\Auth\Application\UseCases\Commands\EnableTwoFactoryCommand;
use App\domain\Auth\Application\UseCases\Queries\FindUserQuery;
use App\domain\Auth\Services\AuthService;
use Core\Database\DB;
use Core\Http\Request;
use Core\Response\Response;
use Core\Routing\Redirect;
use Core\Support\Session\Session;
use Core\Totp\TotpException;
use Core\Totp\TotpFactory;
use Core\View\View;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\SvgWriter;
use Exception;

class TotpController
{
    /**
     * @throws Exception
     */
    public function index(): Response
    {
        $userId = Session::get('user_id');
        $userId = is_int($userId) ? $userId : null;

        $totp = TotpFactory::create();

        $newSecretKey = false;
        $findUserQuery = new FindUserQuery(new UserRepository(DB::getEntityManager()), $userId);
        $user = $findUserQuery->handle();

        if ($user !== null && !empty($user->getGoogle2faSecret())) {
            $secretKey = $user->getGoogle2faSecret();
        } else {
            $newSecretKey = true;
            $secretKey = $totp->generateSecret();
        }

        $imageString = '';
        if ($secretKey !== '') {
            $builder = new Builder(
                writer: new SvgWriter(),
                writerOptions: [],
                validateResult: false,
                data: $totp->generateUri($secretKey, 'TOTPgenerator', 'TOTPgenerator'),
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::High,
                size: 300,
                margin: 10,
                roundBlockSizeMode: RoundBlockSizeMode::Margin,
                //logoPath: __DIR__.'/assets/bender.png',
                labelText: 'This is the label',
                labelFont: new OpenSans(20),
                labelAlignment: LabelAlignment::Center,
                logoResizeToWidth: 50,
                logoPunchoutBackground: true
            );

            $result = $builder->build();
            $imageString = $result->getString();
        }

        $view = new View('two-factory.index', [
            'image' => $imageString,
            'secret' => $secretKey,
            'newSecretKey' => $newSecretKey,
        ])->with('title', t('Two factor authentication'));

        return Response::make($view)
            ->withHeaders(['Content-Type' => 'text/html',])
            ->withStatus(200);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function enableTwoFactor(): Response
    {
        $userId = Session::get('user_id');
        $userId = is_int($userId) ? $userId : null;

        $newSecret = Request::input('secret');
        if (!is_string($newSecret) || $newSecret === '') {
            return Response::make(Redirect::to('/two-factory'));
        }

        $findUserQuery = new FindUserQuery(new UserRepository(DB::getEntityManager()), $userId);
        $user = $findUserQuery->handle();

        if ($user === null) {
            return Response::make(Redirect::to('/login'));
        }

        $enableTwoFactoryCommand = new EnableTwoFactoryCommand(new UserRepository(DB::getEntityManager()), $user, $newSecret);
        $enableTwoFactoryCommand->execute();

        return Response::make(Redirect::to('/two-factory'));
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws TotpException
     */
    public function newAndEnableTwoFactor(): Response
    {
        $userId = Session::get('user_id');
        $userId = is_int($userId) ? $userId : null;

        $totp = TotpFactory::create();

        $findUserQuery = new FindUserQuery(new UserRepository(DB::getEntityManager()), $userId);
        $user = $findUserQuery->handle();

        if ($user === null) {
            return Response::make(Redirect::to('/login'));
        }

        $newSecret = $totp->generateSecret();
        $enableTwoFactoryCommand = new EnableTwoFactoryCommand(new UserRepository(DB::getEntityManager()), $user, $newSecret);
        $enableTwoFactoryCommand->execute();

        return Response::make(Redirect::to('/two-factory'));
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function disableTwoFactor(): Response
    {
        $userId = Session::get('user_id');
        $userId = is_int($userId) ? $userId : null;

        $findUserQuery = new FindUserQuery(new UserRepository(DB::getEntityManager()), $userId);
        $user = $findUserQuery->handle();

        if ($user === null) {
            return Response::make(Redirect::to('/login'));
        }

        $disableTwoFactoryCommand = new DisableTwoFactoryCommand(new UserRepository(DB::getEntityManager()), $user);
        $disableTwoFactoryCommand->execute();

        Session::set('two_factor_auth', false);

        return Response::make(Redirect::to('/two-factory'));
    }

    /**
     * @return Response
     */
    public function twoFactoryAuth(): Response
    {
        $view = new View('two-factory.input', ['errors' => Session::error()])
            ->with('title', t('Two factor'));

        return Response::make($view)
            ->withHeaders(['Content-Type' => 'text/html',])
            ->withStatus(200);
    }

    /**
     * @throws TotpException
     */
    public function twoFactoryAuthCheck(): Response
    {
        $secret = Request::input('secret');
        if (!is_string($secret) || $secret === '') {
            return Response::make(Redirect::to('/two-factory-auth'));
        }

        $user = AuthService::getUser();

        if ($user === null) {
            return Response::make(Redirect::to('/login'));
        }

        $totp = TotpFactory::create();

        $userSecret = $user->getGoogle2faSecret();
        if ($userSecret === null || $userSecret === '') {
            return Response::make(Redirect::to('/two-factory-auth'));
        }

        try {
            if (!$totp->verifyCode($userSecret, $secret)) {
                return Response::make(
                    Redirect::to('/two-factory-auth')
                        ->withErrors([
                            'secret' => [t('Invalid code')],
                        ])
                );
            }
        } catch (Exception $e) {
            return Response::make(
                Redirect::to('/two-factory-auth')
                    ->withErrors([
                        'secret' => [t($e->getMessage())],
                    ])
            );
        }

        Session::set('two_factor_auth', true);
        return Response::make(Redirect::to('/profile'));
    }
}