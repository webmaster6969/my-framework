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
                    <h1>Two factory</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Projects</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card shadow">
            <div class="card-body text-center">
                <h3 class="mb-4">Настройка двухфакторной аутентификации</h3>
                <p>Отсканируйте этот QR-код в приложении Google Authenticator:</p>

                <?php if (!empty($image) && is_string($image)): ?>
                    <svg class="mt-4" width="300" height="300" xmlns="http://www.w3.org/2000/svg"
                         viewBox="<?= $image ?>"></svg>
                <?php else: ?>
                    <p class="text-danger">QR-код недоступен.</p>
                <?php endif; ?>

                <p class="mt-3"><strong>Ключ вручную:</strong> {{ $secret }}</p>
            </div>

            <?php if ($newSecretKey): ?>
                <form method="post" action="/two-factory-enable">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                    <input type="hidden" name="secret" value="{{ $secret }}">
                    <button type="submit">Включить</button>
                </form>
            <?php else: ?>
                <form method="post" action="/two-factory-enable-new">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                    <button type="submit">Новый ключ</button>
                </form>

                <form method="post" action="/two-factory-disable">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
                    <button type="submit">Выключить</button>
                </form>
            <?php endif; ?>
        </div>
    </section>
</section>

@include('partials.footer')