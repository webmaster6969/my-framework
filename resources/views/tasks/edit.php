<?php

use Core\Support\Csrf\Csrf;

$errors = is_array($errors ?? null) ? $errors : [];

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
                    <h1>Edit task</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Edit task</li>
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
                            <h3 class="card-title">Edit task</h3>
                        </div>

                        <form method="post" action="/tasks/update/?id={{ $task->getId() }}">
                            <div class="card-body">
                                <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">

                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" name="title" value="{{ $task->getTitle() }}" class="form-control" id="title" placeholder="Enter title">
                                    <?php if (!empty($errors['title']) && is_array($errors['title'])): ?>
                                        <span class="text-danger"><?php echo implode(', ', $errors['title']); ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <input type="text" name="description" value="{{ $task->getDescription() }}" class="form-control" id="description" placeholder="Enter description">
                                    <?php if (!empty($errors['description']) && is_array($errors['description'])): ?>
                                        <span class="text-danger"><?php echo implode(', ', $errors['description']); ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="start_task">Start task</label>
                                    <input type="datetime-local" step="1" name="start_task" value="{{ $task->getStartTask()->format('Y-m-d\TH:i:s') }}" class="form-control" id="start_task" placeholder="Enter start task">
                                    <?php if (!empty($errors['start_task']) && is_array($errors['start_task'])): ?>
                                        <span class="text-danger"><?php echo implode(', ', $errors['start_task']); ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="end_task">End task</label>
                                    <input type="datetime-local" step="1" name="end_task" value="{{ $task->getEndTask()->format('Y-m-d\TH:i:s') }}" class="form-control" id="end_task" placeholder="Enter end task">
                                    <?php if (!empty($errors['end_task']) && is_array($errors['end_task'])): ?>
                                        <span class="text-danger"><?php echo implode(', ', $errors['end_task']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="/tasks/delete/?id={{ $task->getId() }}" class="btn btn-danger">Delete</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
</div>

@include('partials.footer')