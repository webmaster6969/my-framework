<?php

use Core\Support\Csrf\Csrf;


$token = Csrf::token();
$data = (isset($data) && is_array($data)) ? array_filter($data, fn($k) => is_string($k), ARRAY_FILTER_USE_KEY) : [];
/** @var array<string, mixed> $errors */
$errors = isset($errors) && is_array($errors) ? $errors : [];

?>

@include('partials.header')

<div class="login-page">
    <div class="login-logo">
        <a href="../../index2.html"><b>Admin</b>LTE</a>
    </div>
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg"><?php echo t('Sign In'); ?></p>

            <form action="/login" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                <div class="input-group">
                    <input type="email" name="email" value="<?php echo old('email', $data); ?>" class="form-control"
                           placeholder="<?php echo t('Enter email'); ?>">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <?php showErrors('email', $errors); ?>
                <div class="input-group mt-3">
                    <input type="password" name="password" class="form-control"
                           placeholder="<?php echo t('Enter password'); ?>">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <?php showErrors('password', $errors); ?>
                <div class="row mt-3">
                    <div class="col-7">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember">
                            <label for="remember">
                                <?php echo t('Remember Me'); ?>
                            </label>
                        </div>
                    </div>
                    <div class="col-5">
                        <button type="submit" class="btn btn-primary btn-block"><?php echo t('Sign In'); ?></button>
                    </div>
                </div>
            </form>

            <p class="mb-0">
                <a href="/register" class="text-center"><?php echo t('Register'); ?></a>
            </p>
        </div>
    </div>
</div>

@include('partials.footer')