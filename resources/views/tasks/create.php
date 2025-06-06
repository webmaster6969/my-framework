<?php

use Core\Support\Csrf\Csrf;

$token = Csrf::token();

$data = (isset($data) && is_array($data)) ? array_filter($data, fn($k) => is_string($k), ARRAY_FILTER_USE_KEY) : [];
$errors = (isset($errors) && is_array($errors)) ? array_filter($errors, fn($k) => is_string($k), ARRAY_FILTER_USE_KEY) : [];

/**
 * @param string $key
 * @param array<string, mixed> $data
 * @return string
 */
function old(string $key, array $data): string
{
    if (!array_key_exists($key, $data)) {
        return '';
    }

    $value = $data[$key];

    if (is_string($value)) {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    if (is_scalar($value)) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    if (is_object($value) && method_exists($value, '__toString')) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    return '';
}

/**
 * @param string $key
 * @param array<string, mixed> $errors
 * @return void
 */
function showErrors(string $key, array $errors): void
{
    if (!empty($errors[$key]) && is_array($errors[$key])) {
        $escapedErrors = array_map(function($e) {
            if (is_string($e)) {
                return htmlspecialchars($e, ENT_QUOTES, 'UTF-8');
            }
            if (is_scalar($e)) {
                return htmlspecialchars((string)$e, ENT_QUOTES, 'UTF-8');
            }
            if (is_object($e) && method_exists($e, '__toString')) {
                return htmlspecialchars((string)$e, ENT_QUOTES, 'UTF-8');
            }
            return '';
        }, $errors[$key]);

        echo '<span class="text-danger">' . implode(', ', $escapedErrors) . '</span>';
    }
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
                    <h1>Create task</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Create task</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
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
                                    <input type="text" name="title" value="<?php echo old('title', $data); ?>" class="form-control" id="title" placeholder="Enter title">
                                    <?php showErrors('title', $errors); ?>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <input type="text" name="description" value="<?php echo old('description', $data); ?>" class="form-control" id="description" placeholder="Enter description">
                                    <?php showErrors('description', $errors); ?>
                                </div>

                                <div class="form-group">
                                    <label for="start_task">Start task</label>
                                    <input type="datetime-local" step="1" name="start_task" value="<?php echo old('start_task', $data); ?>" class="form-control" id="start_task" placeholder="Enter start task">
                                    <?php showErrors('start_task', $errors); ?>
                                </div>

                                <div class="form-group">
                                    <label for="end_task">End task</label>
                                    <input type="datetime-local" step="1" name="end_task" value="<?php echo old('end_task', $data); ?>" class="form-control" id="end_task" placeholder="Enter end task">
                                    <?php showErrors('end_task', $errors); ?>
                                </div>
                            </div>
                            <!-- /.card-body -->

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