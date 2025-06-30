<?php

use App\domain\Auth\Domain\Exceptions\UserNotFoundException;
use App\domain\Auth\Domain\Model\Entities\User;
use Core\Support\Csrf\Csrf;

$token = Csrf::token();

/** @var array<string, mixed> $errors */
$errors = isset($errors) && is_array($errors) ? $errors : [];

/** @var ?User $user */
if (empty($user)) {
    throw new UserNotFoundException('User not found');
}

?>

@include('partials.header')
@include('partials.navbar')
@include('partials.sidebar')

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?php echo t('Profile'); ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/"><?php echo t('Home'); ?></a></li>
                        <li class="breadcrumb-item active"><?php echo t('Profile'); ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">

                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle"
                                     src="/plugins/adminlte/img/user4-128x128.jpg"
                                     alt="User profile picture">
                            </div>
                            <h3 class="profile-username text-center">{{ $user->getName() }}</h3>
                            <p class="text-muted text-center"><?php echo t('Profile'); ?></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#settings"
                                       data-toggle="tab"><?php echo t('Settings'); ?></a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active tab-pane" id="settings">
                                    <form method="post" action="/profile/update" class="form-horizontal">
                                        <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                                        <div class="form-group row">
                                            <label for="name"
                                                   class="col-sm-2 col-form-label"><?php echo t('Name'); ?></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="name" class="form-control" id="name"
                                                       value="{{ $user->getName() }}"
                                                       placeholder="<?php echo t('Enter name'); ?>">
                                                <?php showErrors('name', $errors); ?>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="encryptionKey"
                                                   class="col-sm-2 col-form-label"><?php echo t('Encryption key'); ?></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="encryption_key" class="form-control"
                                                       id="encryptionKey"
                                                       value="<?php echo $user->getEncryptionKey() ?? ''; ?>"
                                                       placeholder="<?php echo t('Enter encryption key'); ?>">
                                                <div class="text-danger mt-1">
                                                    <?php showErrors('encryption_key', $errors); ?>
                                                </div>
                                                <button type="button" class="btn btn-info mt-2"
                                                        onclick="generateEncryptionKey()">
                                                    <?php echo t('Generate encryption key'); ?>
                                                </button>
                                                <button type="button" class="btn btn-warning mt-2"
                                                        onclick="location.reload()">
                                                    <?php echo t('Restore default encryption key'); ?>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="offset-sm-2 col-sm-10">
                                                <button type="submit"
                                                        class="btn btn-success"><?php echo t('Save'); ?></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    function generateEncryptionKey() {
        const charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        const length = 32;
        let key = '';
        for (let i = 0; i < length; i++) {
            key += charset.charAt(Math.floor(Math.random() * charset.length));
        }
        document.getElementById('encryptionKey').value = key;
    }
</script>

@include('partials.footer')