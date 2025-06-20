<?php

use App\domain\Auth\Services\AuthService;

$user = AuthService::getUser();
$name = '';
if (!empty($user)) {
    $name = $user->getName();
}

/**
 * @param string $path
 * @return string
 */
function active(string $path): string
{
    return $_SERVER['REQUEST_URI'] === $path ? 'active' : '';
}

?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="./" class="brand-link">
        <img src="/plugins/adminlte/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
             style="opacity: .8">
        <span class="brand-text font-weight-light">AdminLTE 3</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="/plugins/adminlte/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="/profile" class="d-block"><?php echo $name; ?></a>
            </div>
        </div>

        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                        <p><?php echo t('Dashboard'); ?></p>
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/tasks" class="nav-link <?php echo active('/tasks'); ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?php echo t('Tasks'); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/profile" class="nav-link <?php echo active('/profile'); ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?php echo t('Profile'); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/storage" class="nav-link <?php echo active('/storage'); ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?php echo t('Working with files'); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/two-factory" class="nav-link <?php echo active('/two-factory'); ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?php echo t('Two factory'); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/logout" class="nav-link <?php echo active('/logout'); ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?php echo t('Logout'); ?></p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</aside>