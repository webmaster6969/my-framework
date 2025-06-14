<?php

use App\domain\Task\Domain\Model\Entities\Task;
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
                    <h1><?php echo t('Create task'); ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><?php echo t('Home'); ?></a></li>
                        <li class="breadcrumb-item active"><?php echo t('Home'); ?></li>
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
                            <h3 class="card-title"><?php echo t('Create task'); ?></h3>
                        </div>

                        <form method="post" action="/tasks/store" id="createTaskForm">
                            <div class="card-body">
                                <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">

                                <div class="form-group">
                                    <label for="title"><?php echo t('Title'); ?></label>
                                    <input type="text" name="title" value="<?php echo old('title', $data); ?>"
                                           class="form-control" id="title" placeholder="<?php echo t('Enter title'); ?>">
                                    <?php showErrors('title', $errors); ?>
                                </div>

                                <div class="form-group">
                                    <label for="description"><?php echo t('Description'); ?></label>
                                    <textarea type="text" name="description" class="form-control" id="description"
                                              placeholder="<?php echo t('Enter description'); ?>"><?php echo old('description', $data); ?></textarea>
                                    <?php showErrors('description', $errors); ?>
                                </div>

                                <div class="form-group">
                                    <label for="status"><?php echo t('Status'); ?></label>
                                    <select name="status" class="form-control" id="status">
                                        <?php foreach (Task::getAllStatuses() as $status) { ?>
                                            <option value="<?php echo $status; ?>"><?php echo t($status); ?></option>
                                        <?php } ?>
                                    </select>
                                    <?php showErrors('status', $errors); ?>
                                </div>

                                <div class="form-group">
                                    <label for="start_task"><?php echo t('Start task'); ?></label>
                                    <input type="datetime-local" step="1" name="start_task"
                                           value="<?php echo old('start_task', $data); ?>" class="form-control"
                                           id="start_task" placeholder="<?php echo t('Enter start task'); ?>">
                                    <?php showErrors('start_task', $errors); ?>
                                </div>

                                <div class="form-group">
                                    <label for="end_task"><?php echo t('End task'); ?></label>
                                    <input type="datetime-local" step="1" name="end_task"
                                           value="<?php echo old('end_task', $data); ?>" class="form-control"
                                           id="end_task" placeholder="<?php echo t('Enter end task'); ?>">
                                    <?php showErrors('end_task', $errors); ?>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><?php echo t('Create task'); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </section>
</div>

@include('partials.footer')