<?php
$token = \Core\Support\Csrf\Csrf::token();
?>
<form action="/register" method="post">
    <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
    <label>
        <strong>Name:</strong>
        <input type="text" name="name">
    </label>
    <label>
        <strong>Email:</strong>
        <input type="email" name="email">
    </label>
    <label>
        <strong>Password:</strong>
        <input type="password" name="password">
    </label>
    <button type="submit">Login</button>
</form>

@include('partials.footer')