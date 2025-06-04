<?php

use App\domain\Task\Domain\Model\Entities\Task;

/** @var Task[] $tasks */
?>

@include('partials.header')
@include('partials.navbar')
@include('partials.sidebar')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tasks</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Tasks</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tasks</h3>
                <a href="/tasks/create" class="btn btn-primary float-right">
                    <i class="fas fa-plus"></i>
                    Add New
                </a>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped projects">
                    <thead>
                    <tr>
                        <th style="width: 1%">#</th>
                        <th style="width: 20%">Task Name</th>
                        <th style="width: 8%" class="text-center">Status</th>
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
                                            <?= htmlspecialchars((string)$task->getStatus(), ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td class="project-actions text-right">
                                        <a class="btn btn-info btn-sm"
                                           href="/tasks/edit/?id=<?= urlencode((string)$task->getId()) ?>">
                                            <i class="fas fa-pencil-alt"></i>
                                            Edit
                                        </a>
                                        <a class="btn btn-danger btn-sm"
                                           href="/tasks/delete/?id=<?= urlencode((string)$task->getId()) ?>">
                                            <i class="fas fa-trash"></i>
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No tasks found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </section>
</div>

@include('partials.footer')