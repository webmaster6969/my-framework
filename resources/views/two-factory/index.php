<?php
    $token = \Core\Support\Csrf\Csrf::token();
?>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-body text-center">
            <h3 class="mb-4">Настройка двухфакторной аутентификации</h3>
            <p>Отсканируйте этот QR-код в приложении Google Authenticator:</p>
            <svg class="mt-4" width="300" height="300" xmlns="http://www.w3.org/2000/svg" viewBox="<?php echo $image; ?>"></svg>

            <p class="mt-3"><strong>Ключ вручную:</strong> {{ $secret }}</p>
        </div>
    </div>
</div>

<?php if($newSecretKey): ?>
    <form method="post" action="/two-factory-enable">
        <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
        <input type="hidden" name="secret" value="{{ $secret }}">
        <button type="submit">Включить</button>
    </form>
<?php endif; ?>

<?php if(!$newSecretKey): ?>
    <form method="post" action="/two-factory-enable-new">
        <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
        <button type="submit">Новый ключ</button>
    </form>

    <form method="post" action="/two-factory-disable">
        <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
        <button type="submit">Выключить</button>
    </form>
<?php endif; ?>
