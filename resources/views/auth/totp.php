@include('partials.header')

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-body text-center">
            <h3 class="mb-4">Настройка двухфакторной аутентификации</h3>
            <p>Отсканируйте этот QR-код в приложении Google Authenticator:</p>
            <svg class="mt-4" width="300" height="300" xmlns="http://www.w3.org/2000/svg"
                 viewBox="<?php echo $image; ?>"></svg>

            <p class="mt-3"><strong>Ключ вручную:</strong> {{ $secret }}</p>
        </div>
    </div>
</div>

@include('partials.footer')