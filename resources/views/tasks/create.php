<?php

use Core\Support\Csrf\Csrf;

$token = Csrf::token();

$data = (isset($data) && is_array($data)) ? array_filter($data, fn($k) => is_string($k), ARRAY_FILTER_USE_KEY) : [];
$errors = (isset($errors) && is_array($errors)) ? array_filter($errors, fn($k) => is_string($k), ARRAY_FILTER_USE_KEY) : [];
?>

@include('partials.header')
@include('partials.navbar')
@include('partials.sidebar')

<div class="content-wrapper">

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create task</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Create task</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <section class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Create task</h3>
                        </div>

                        <form method="post" action="/tasks/store" id="createTaskForm">
                            <div class="card-body">
                                <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">

                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" name="title" value="<?php echo old('title', $data); ?>"
                                           class="form-control" id="title" placeholder="Enter title">
                                    <?php showErrors('title', $errors); ?>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea type="text" name="description" class="form-control" id="description"
                                              placeholder="Enter description"><?php echo old('description', $data); ?></textarea>
                                    <?php showErrors('description', $errors); ?>
                                </div>

                                <div class="form-group">
                                    <label for="start_task">Start task</label>
                                    <input type="datetime-local" step="1" name="start_task"
                                           value="<?php echo old('start_task', $data); ?>" class="form-control"
                                           id="start_task" placeholder="Enter start task">
                                    <?php showErrors('start_task', $errors); ?>
                                </div>

                                <div class="form-group">
                                    <label for="end_task">End task</label>
                                    <input type="datetime-local" step="1" name="end_task"
                                           value="<?php echo old('end_task', $data); ?>" class="form-control"
                                           id="end_task" placeholder="Enter end task">
                                    <?php showErrors('end_task', $errors); ?>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Create</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </section>
</div>

@include('partials.footer')