<?php

use App\domain\Task\Domain\Model\Entities\Task;

/** @var Task[] $tasks */
?>

@include('partials.header')
@include('partials.navbar')
@include('partials.sidebar')

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?php echo t('Tasks'); ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/"><?php echo t('Home'); ?></a></li>
                        <li class="breadcrumb-item active"><?php echo t('Tasks'); ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        @include('tasks.search')
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?php echo t('Tasks'); ?></h3>
                <a href="/tasks/create" class="btn btn-primary float-right">
                    <i class="fas fa-plus"></i>
                    <?php echo t('Add new task'); ?>
                </a>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped projects">
                    <thead>
                    <tr>
                        <th style="width: 1%">#</th>
                        <th style="width: 20%"><?php echo t('Task title'); ?></th>
                        <th style="width: 8%" class="text-center"><?php echo t('Status'); ?></th>
                        <th style="width: 20%"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (count($tasks) > 0): ?>
                        <?php foreach ($tasks as $task): ?>
                            <tr>
                                <td>#</td>
                                <td>
                                    <a><?= htmlspecialchars((string)$task->getTitle(), ENT_QUOTES, 'UTF-8') ?></a>
                                    <br/>
                                    <small>
                                        <?= htmlspecialchars($task->getCreatedAt()->format('Y-m-d H:i:s'), ENT_QUOTES, 'UTF-8') ?>
                                    </small>
                                </td>
                                <td class="project-state">
                                        <span class="badge badge-success">
                                            <?php echo t(htmlspecialchars((string)$task->getStatus(), ENT_QUOTES, 'UTF-8')); ?>
                                        </span>
                                </td>
                                <td class="project-actions text-right">
                                    <a class="btn btn-info btn-sm"
                                       href="/tasks/edit/?id=<?= urlencode((string)$task->getId()) ?>">
                                        <i class="fas fa-pencil-alt"></i>
                                        <?php echo t('Edit'); ?>
                                    </a>
                                    <a class="btn btn-danger btn-sm"
                                       href="/tasks/delete/?id=<?= urlencode((string)$task->getId()) ?>">
                                        <i class="fas fa-trash"></i>
                                        <?php echo t('Delete'); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center"><?php echo t('No tasks found'); ?></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </section>
</div>

@include('partials.footer')

<script>
    $(function () {
        $('.select2').select2()
    });
</script>