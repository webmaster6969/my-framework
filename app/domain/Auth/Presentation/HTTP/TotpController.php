<?php

namespace App\domain\Auth\Presentation\HTTP;

use App\domain\Auth\Application\Repositories\UserRepositories;
use App\domain\Auth\Service\AuthService;
use Core\Http\Request;
use Core\Support\Csrf\Csrf;
use Core\Support\Session\Session;
use Core\Totp\TotpFactory;
use Core\View\View;
use DateMalformedStringException;
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

        //$authService = new AuthService(new UserRepositories());
        //$user = $authService->getUser();
        //$secretKey = $user->getGoogle2faSecret();
        $secretKey = $totp->generateSecret();

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
        echo $view->render('auth.totp', [
            'image' => $imageString,
            'secret' => $secretKey
        ]);
    }
}
