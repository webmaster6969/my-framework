<?php

use Core\Support\Csrf\Csrf;

$token = Csrf::token();
?>

@include('partials.header')
<div class="login-page">
    <form method="post" action="/two-factory-auth-check">
        <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
        <label>
            <strong>Ключ:</strong>
            <input type="text" name="secret">
        </label>
        <button type="submit">Login</button>
    </form>
</div>
@include('partials.footer')