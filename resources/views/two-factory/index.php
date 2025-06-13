<?php

use Core\Support\Csrf\Csrf;

$token = Csrf::token();

$image = $image ?? '';
$secret = $secret ?? '';
$newSecretKey = $newSecretKey ?? false;

?>

@include('partials.header')
@include('partials.navbar')
@include('partials.sidebar')

<section class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?php echo t('Two factory'); ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><?php echo t('Home'); ?></a></li>
                        <li class="breadcrumb-item active"><?php echo t('Two factory'); ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card shadow">
            <div class="card-body text-center">
                <h3 class="mb-4"><?php echo t('Setting up two-factor authentication'); ?></h3>
                <p><?php echo t('Scan this QR code in the Google Authenticator app'); ?>:</p>

                <?php if (!empty($image) && is_string($image)): ?>
                    <svg class="mt-4" width="300" height="300" xmlns="http://www.w3.org/2000/svg"
                         viewBox="<?= $image ?>"></svg>
                <?php else: ?>
                    <p class="text-danger"><?php echo t('QR code is not available'); ?></p>
                <?php endif; ?>

                <p class="mt-3"><strong><?php echo t('Key for manual input'); ?>:</strong> {{ $secret }}</p>
            </div>

            <?php if ($newSecretKey): ?>
                <form method="post" action="/two-factory-enable">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                    <input type="hidden" name="secret" value="{{ $secret }}">
                    <button type="submit"><?php echo t('Enable'); ?></button>
                </form>
            <?php else: ?>
                <form method="post" action="/two-factory-enable-new">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                    <button type="submit"><?php echo t('Enable'); ?></button>
                </form>

                <form method="post" action="/two-factory-disable">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                    <button type="submit"><?php echo t('Disable'); ?></button>
                </form>
            <?php endif; ?>
        </div>
    </section>
</section>

@include('partials.footer')