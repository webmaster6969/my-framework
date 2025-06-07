<?php

use Core\Support\Csrf\Csrf;

$token = Csrf::token();
$data = (isset($data) && is_array($data)) ? array_filter($data, fn($k) => is_string($k), ARRAY_FILTER_USE_KEY) : [];
/** @var array<string, mixed> $errors */
$errors = isset($errors) && is_array($errors) ? $errors : [];

?>

@include('partials.header')

<div class="register-page">
    <div class="card register-box">
        <div class="card-body register-card-body">
            <p class="login-box-msg">Register</p>

            <form action="/register" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">

                <div class="input-group">
                    <input type="text" name="name" value="<?php echo old('name', $data); ?>" class="form-control"
                           placeholder="Full name">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <?php showErrors('name', $errors); ?>

                <div class="input-group mt-3">
                    <input type="email" name="email" value="<?php echo old('email', $data); ?>" class="form-control"
                           placeholder="Email">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <?php showErrors('email', $errors); ?>

                <div class="input-group mt-3 mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <?php showErrors('password', $errors); ?>

                <div class="col-4">
                    <button type="submit" class="btn btn-primary btn-block">Register</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('partials.footer')