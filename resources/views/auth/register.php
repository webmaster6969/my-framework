<?php

$token = \Core\Support\Csrf\Csrf::token();
?>

@include('partials.header')

<div class="register-page">
    <div class="card register-box ">
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

                    <?php if (!empty($errors['name'])): ?>
                        <span class="text-danger"><?php echo implode(', ', $errors['name']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>

                    <?php if (!empty($errors['email'])): ?>
                        <span class="text-danger"><?php echo implode(', ', $errors['email']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>

                    <?php if (!empty($errors['password'])): ?>
                        <span class="text-danger"><?php echo implode(', ', $errors['password']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="col-4">
                    <button type="submit" class="btn btn-primary btn-block">Register</button>
                </div>
            </form>
        </div>
        <!-- /.form-box -->
    </div><!-- /.card -->
</div>

@include('partials.footer')