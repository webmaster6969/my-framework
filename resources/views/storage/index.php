<?php

use Core\Support\Csrf\Csrf;

$token = Csrf::token();
?>

@include('partials.header')
@include('partials.navbar')
@include('partials.sidebar')

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Uplode file</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">User Profile</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-5">
                    <div class="card card-primary card-outline p-2">
                        <form action="/storage" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                            <input type="file" name="file">

                            <?php if (!empty($errors['file'])): ?>
                                <span class="text-danger"><?php echo implode(', ', $errors['file']); ?></span>
                            <?php endif; ?>
                            <button type="submit">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('partials.footer')