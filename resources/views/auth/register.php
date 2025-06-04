<?php

$token = \Core\Support\Csrf\Csrf::token();

// Гарантируем тип массива для $errors
$errors = isset($errors) && is_array($errors) ? $errors : [];
$nameErrors = isset($errors['name']) ? (array) $errors['name'] : [];
$emailErrors = isset($errors['email']) ? (array) $errors['email'] : [];
$passwordErrors = isset($errors['password']) ? (array) $errors['password'] : [];

?>

@include('partials.header')

<div class="register-page">
    <div class="card register-box">
        <div class="card-body register-card-body">
            <p class="login-box-msg">Register a new membership</p>

            <form action="/register" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">

                <div class="input-group mb-3">
                    <input type="text" name="name" class="form-control" placeholder="Full name">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <?php if (!empty($nameErrors)): ?>
                    <div class="text-danger mb-2"><?php echo implode(', ', $nameErrors); ?></div>
                <?php endif; ?>

                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <?php if (!empty($emailErrors)): ?>
                    <div class="text-danger mb-2"><?php echo implode(', ', $emailErrors); ?></div>
                <?php endif; ?>

                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <?php if (!empty($passwordErrors)): ?>
                    <div class="text-danger mb-2"><?php echo implode(', ', $passwordErrors); ?></div>
                <?php endif; ?>

                <div class="col-4">
                    <button type="submit" class="btn btn-primary btn-block">Register</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('partials.footer')