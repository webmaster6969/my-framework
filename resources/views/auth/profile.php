<?php

use Core\Support\Csrf\Csrf;

$token = Csrf::token();

/** @var array<string, mixed> $errors */
$errors = isset($errors) && is_array($errors) ? $errors : [];

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
                        <li class="breadcrumb-item"><a href="#"><?php echo t('Home'); ?></a></li>
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

                            <a href="#" class="btn btn-primary btn-block"><b><?php echo t('Follow'); ?></b></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#settings" data-toggle="tab"><?php echo t('Settings'); ?></a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active tab-pane" id="settings">
                                    <form method="post" action="/profile/update" class="form-horizontal">
                                        <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">

                                        <div class="form-group row">
                                            <label for="inputName" class="col-sm-2 col-form-label"><?php echo t('Name'); ?></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="name" class="form-control" id="inputName"
                                                       placeholder="<?php echo t('Enter name'); ?>">
                                                <?php showErrors('name', $errors); ?>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="offset-sm-2 col-sm-10">
                                                <button type="submit" class="btn btn-danger"><?php echo t('Save'); ?></button>
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

@include('partials.footer')