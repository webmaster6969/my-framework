<?php

use Core\Support\Csrf\Csrf;

$token = Csrf::token();

// Гарантируем, что $errors — массив
$errors = isset($errors) && is_array($errors) ? $errors : [];
$nameErrors = isset($errors['name']) ? (array) $errors['name'] : [];

?>

@include('partials.header')
@include('partials.navbar')
@include('partials.sidebar')

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Profile</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">User Profile</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">

                    <!-- Profile Image -->
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle"
                                     src="/dist/img/user4-128x128.jpg"
                                     alt="User profile picture">
                            </div>

                            <h3 class="profile-username text-center">{{ $user->getName() }}</h3>

                            <p class="text-muted text-center">Software Engineer</p>

                            <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#settings" data-toggle="tab">Settings</a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active tab-pane" id="settings">
                                    <form method="post" action="/profile/update" class="form-horizontal">
                                        <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">

                                        <div class="form-group row">
                                            <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="name" class="form-control" id="inputName"
                                                       placeholder="Name">
                                            </div>

                                            <?php if (!empty($nameErrors)): ?>
                                                <div class="offset-sm-2 col-sm-10">
                                                    <span class="text-danger"><?php echo implode(', ', $nameErrors); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="form-group row">
                                            <div class="offset-sm-2 col-sm-10">
                                                <button type="submit" class="btn btn-danger">Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- /.col -->
            </div> <!-- /.row -->
        </div>
    </section>
</div>

@include('partials.footer')