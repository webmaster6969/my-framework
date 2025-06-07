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
                    <h1>Upload file</h1>
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
                <div class="col-md-5">
                    <div class="card card-primary card-outline p-2">
                        <form action="/storage" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                            <input type="file" name="file" class="form-control mb-1">
                            <div class="form-errors">
                                <?php showErrors('file', $errors); ?>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('partials.footer')