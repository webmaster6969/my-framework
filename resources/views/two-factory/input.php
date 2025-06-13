<?php

use Core\Support\Csrf\Csrf;

$token = Csrf::token();
/** @var array<string, mixed> $errors */
$errors = isset($errors) && is_array($errors) ? $errors : [];

?>

@include('partials.header')

<div class="login-page">
    <form method="post" action="/two-factory-auth-check">
        <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
        <div class="form-group">
            <label>
                <strong><?php echo t('Key'); ?>:</strong>
                <input type="text" name="secret" class="form-control" autofocus>
            </label>
            <div class="form-errors">
                <?php showErrors('secret', $errors); ?>
            </div>
        </div>
        <button type="submit"><?php echo t('Login'); ?></button>
    </form>
</div>

@include('partials.footer')