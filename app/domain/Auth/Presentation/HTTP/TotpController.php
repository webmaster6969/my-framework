<?php

declare(strict_types=1);

namespace App\domain\Auth\Presentation\HTTP;

use App\domain\Auth\Application\Repositories\UserRepositories;
use App\domain\Auth\Application\UseCases\Commands\DisableTwoFactoryCommand;
use App\domain\Auth\Application\UseCases\Commands\EnableTwoFactoryCommand;
use App\domain\Auth\Application\UseCases\Queries\FindUserQuery;
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
        $totp = TotpFactory::create();

        $newSecretKey = false;
        $findUserQuery = new FindUserQuery(new UserRepositories(), Session::get('user_id'));
        $user = $findUserQuery->handle();

        if (empty($user->getGoogle2faSecret())) {
            $newSecretKey = true;
            $secretKey = $totp->generateSecret();
        } else {
            $secretKey = $user->getGoogle2faSecret();
        }

        $imageString = '';
        if (!empty($secretKey)) {
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
        ]);

        return Response::make($view)->withHeaders([
            'Content-Type' => 'text/html',
        ])->withStatus(200);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function enableTwoFactor(): Response
    {
        $newSecret = Request::input('secret');

        $findUserQuery = new FindUserQuery(new UserRepositories(), Session::get('user_id'));
        $user = $findUserQuery->handle();
        $enableTwoFactoryCommand = new EnableTwoFactoryCommand(new UserRepositories(), $user, $newSecret);
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
        $totp = TotpFactory::create();

        $findUserQuery = new FindUserQuery(new UserRepositories(), Session::get('user_id'));
        $user = $findUserQuery->handle();
        $enableTwoFactoryCommand = new EnableTwoFactoryCommand(new UserRepositories(), $user, $totp->generateSecret());
        $enableTwoFactoryCommand->execute();

        return Response::make(Redirect::to('/two-factory'));
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function disableTwoFactor(): Response
    {
        $findUserQuery = new FindUserQuery(new UserRepositories(), Session::get('user_id'));
        $user = $findUserQuery->handle();
        $disableTwoFactoryCommand = new DisableTwoFactoryCommand(new UserRepositories(), $user);
        $disableTwoFactoryCommand->execute();

        return Response::make(Redirect::to('/two-factory'));
    }

    public function twoFactoryAuth(): Response
    {
        $view = new View('two-factory.input');

        return Response::make($view)->withHeaders([
            'Content-Type' => 'text/html',
        ])->withStatus(200);
    }

    /**
     * @throws TotpException
     */
    public function twoFactoryAuthCheck(): Response
    {
        $secret = Request::input('secret');

        $findUserQuery = new FindUserQuery(new UserRepositories(), Session::get('user_id'));
        $user = $findUserQuery->handle();
        $totp = TotpFactory::create();

        if (!$totp->verifyCode($user->getGoogle2faSecret(), $secret)) {
            return Response::make(Redirect::to('/two-factory-auth'));
        }

        Session::set('two_factor_auth', true);
        return Response::make(Redirect::to('/profile'));
    }
}
