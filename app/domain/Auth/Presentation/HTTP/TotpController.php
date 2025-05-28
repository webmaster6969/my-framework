<?php

namespace App\domain\Auth\Presentation\HTTP;

use App\domain\Auth\Application\Repositories\UserRepositories;
use App\domain\Auth\Application\UseCases\Commands\DisableTwoFactoryCommand;
use App\domain\Auth\Application\UseCases\Commands\EnableTwoFactoryCommand;
use App\domain\Auth\Application\UseCases\Queries\FindUserQuery;
use Core\Http\Request;
use Core\Support\Csrf\Csrf;
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
    public function index()
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

        $view = new View();
        echo $view->render('two-factory.index', [
            'image' => $imageString,
            'secret' => $secretKey,
            'newSecretKey' => $newSecretKey,
        ]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function enableTwoFactor()
    {
        $newSecret = Request::input('secret');
        $csrfToken = Request::input('csrf_token');

        if (!Csrf::check($csrfToken)) {
            header('Location: /register');
            exit;
        }

        $findUserQuery = new FindUserQuery(new UserRepositories(), Session::get('user_id'));
        $user = $findUserQuery->handle();
        $enableTwoFactoryCommand = new EnableTwoFactoryCommand(new UserRepositories(), $user, $newSecret);
        $enableTwoFactoryCommand->execute();

        header('Location: /two-factory');
        exit;
    }

    public function newAndEnableTwoFactor()
    {
        $totp = TotpFactory::create();
        $csrfToken = Request::input('csrf_token');
        $newSecret = Request::input('secret');

        if (!Csrf::check($csrfToken)) {
            header('Location: /register');
            exit;
        }

        $findUserQuery = new FindUserQuery(new UserRepositories(), Session::get('user_id'));
        $user = $findUserQuery->handle();
        $enableTwoFactoryCommand = new EnableTwoFactoryCommand(new UserRepositories(), $user, $totp->generateSecret());
        $enableTwoFactoryCommand->execute();
        header('Location: /two-factory');
        exit;
    }

    /**
     * @throws OptimisticLockException
     * @throws TotpException
     * @throws ORMException
     */
    public function disableTwoFactor()
    {
        $csrfToken = Request::input('csrf_token');

        if (!Csrf::check($csrfToken)) {
            header('Location: /register');
            exit;
        }

        $findUserQuery = new FindUserQuery(new UserRepositories(), Session::get('user_id'));
        $user = $findUserQuery->handle();
        $disableTwoFactoryCommand = new DisableTwoFactoryCommand(new UserRepositories(), $user);
        $disableTwoFactoryCommand->execute();
        header('Location: /two-factory');
        exit;
    }

    public function twoFactoryAuth()
    {
        $view = new View();
        echo $view->render('two-factory.input');
    }

    public function twoFactoryAuthCheck()
    {
        $csrfToken = Request::input('csrf_token');

        if (!Csrf::check($csrfToken)) {
            header('Location: /register');
            exit;
        }

        $secret = Request::input('secret');

        $findUserQuery = new FindUserQuery(new UserRepositories(), Session::get('user_id'));
        $user = $findUserQuery->handle();
        $totp = TotpFactory::create();

        if (!$totp->verifyCode($user->getGoogle2faSecret(), $secret)) {
            header('Location: /two-factory-auth');
            exit;
        }

        Session::set('two_factor_auth', true);
        header('Location: /profile');
        exit;
    }
}
