<?php
$token = \Core\Support\Csrf\Csrf::token();
?>
<form action="/login" method="post">
    <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
    <label>
        <input type="email" name="email">
    </label>
    <label>
        <input type="password" name="password">
    </label>
    <button type="submit">Login</button>
</form>

@include('partials.footer')