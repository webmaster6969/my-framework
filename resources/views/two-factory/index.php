<?php

use Core\Support\Csrf\Csrf;

$token = Csrf::token();
?>

@include('partials.header')
@include('partials.navbar')
@include('partials.sidebar')

<!-- Content Wrapper. Contains page content -->
<section class="content-wrapper">
    <!-- Content Header (Page header) -->
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
        </div><!-- /.container-fluid -->
    </section>

    <section class="content">
        <div class="card shadow">
            <div class="card-body text-center">
                <h3 class="mb-4">Настройка двухфакторной аутентификации</h3>
                <p>Отсканируйте этот QR-код в приложении Google Authenticator:</p>
                <svg class="mt-4" width="300" height="300" xmlns="http://www.w3.org/2000/svg"
                     viewBox="<?php echo $image; ?>"></svg>

                <p class="mt-3"><strong>Ключ вручную:</strong> {{ $secret }}</p>
            </div>

            <?php if ($newSecretKey): ?>
                <form method="post" action="/two-factory-enable">
                    <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                    <input type="hidden" name="secret" value="{{ $secret }}">
                    <button type="submit">Включить</button>
                </form>
            <?php endif; ?>

            <?php if (!$newSecretKey): ?>
                <form method="post" action="/two-factory-enable-new">
                    <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                    <button type="submit">Новый ключ</button>
                </form>

                <form method="post" action="/two-factory-disable">
                    <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                    <button type="submit">Выключить</button>
                </form>
            <?php endif; ?>
        </div>
    </section>

    @include('partials.footer')