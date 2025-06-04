<?php
// templates/layout/header.php
// $auth objektas yra prieinamas iš index.php scope, nes header.php įtraukiamas index.php faile
/** @var Auth $auth */ // подсказка IDE
$isLoggedIn = $auth->isLoggedIn(); 
$currentUsername = $auth->getCurrentUsername();
$currentUserRole = $auth->getCurrentUserRole();
$currentPage = $view_data['current_page_resolved'] ?? 'home';
$currentAction = $view_data['current_action_resolved'] ?? '';
?>
<!DOCTYPE html>
<html lang="<?php echo getCurrentLanguageCode(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($view_data['meta_title']) ? e($view_data['meta_title']) : e(trans('site_title')); ?></title>
    <meta name="description" content="<?php echo isset($view_data['meta_description']) ? e($view_data['meta_description']) : e(trans('meta_description_default')); ?>">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css"> <?php // Root-relative path ensures CSS loads on all pages ?>
    <link rel="stylesheet" href="/css/material-custom.css">
    <script type="module" src="/js/bundle.js"></script>
</head>
<body>
<!-- Overlay for mobile menu -->
<div class="sidebar-overlay"></div>

<!-- Mobile Top Bar -->
<div class="mobile-topbar">
    <a href="<?php echo url('home'); ?>" class="mobile-brand"><?php echo e(trans('main_site_brand')); ?></a>
    <md-icon-button id="sidebarToggleMobile" aria-label="<?php echo e(trans('toggle_navigation')); ?>">
        <md-icon>menu</md-icon>
    </md-icon-button>
</div>

<div class="page-wrapper">
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="<?php echo url('home'); ?>" class="sidebar-brand-link"><?php echo e(trans('main_site_brand')); ?></a>
            <md-icon-button id="sidebarToggleDesktop" aria-label="<?php echo e(trans('toggle_navigation')); ?>">
                <md-icon>menu</md-icon>
            </md-icon-button>
        </div>
        <div class="sidebar-search">
            <form action="<?php echo url('companies'); ?>" method="GET" style="width: 100%;">
                <md-outlined-text-field
                    style="width: 100%;"
                    label="<?php echo e(trans('search_placeholder')); ?>"
                    name="search_query"
                    id="search-input"
                    value="<?php echo isset($_GET['search_query']) ? e($_GET['search_query']) : ''; ?>">
                </md-outlined-text-field>
            </form>
            <div id="search-suggestions-container"></div>
        </div>
        <nav>
            <md-list style="--md-list-container-color: transparent;">
                <md-list-item headline="<?php echo e(trans('homepage')); ?>" href="<?php echo url('home'); ?>" <?php if ($currentPage === 'home') echo 'activated'; ?>></md-list-item>
                <md-list-item headline="<?php echo e(trans('company_list')); ?>" href="<?php echo url('companies'); ?>" <?php if ($currentPage === 'companies' && !in_array($currentAction, ['create', 'import'])) echo 'activated'; ?>></md-list-item>
                <md-list-item headline="<?php echo e(trans('add_company')); ?>" href="<?php echo url('companies', 'create'); ?>" <?php if ($currentPage === 'companies' && $currentAction === 'create') echo 'activated'; ?>></md-list-item>
                <?php if ($auth->isAdmin()): ?>
                    <md-divider></md-divider>
                    <md-list-item headline="<?php echo e(trans('admin_dashboard')); ?>" href="<?php echo url('admin', 'dashboard'); ?>" <?php if ($currentPage === 'admin' && $currentAction === 'dashboard') echo 'activated'; ?>></md-list-item>
                    <md-list-item headline="<?php echo e(trans('user_management')); ?>" href="<?php echo url('admin', 'users'); ?>" <?php if ($currentPage === 'admin' && $currentAction === 'users') echo 'activated'; ?>></md-list-item>
                    <md-list-item headline="<?php echo e(trans('import_companies')); ?>" href="<?php echo url('companies', 'import'); ?>" <?php if ($currentPage === 'companies' && $currentAction === 'import') echo 'activated'; ?>></md-list-item>
                    <md-list-item headline="<?php echo e(trans('sitemap_generation')); ?>" href="<?php echo url('admin', 'sitemap'); ?>" <?php if ($currentPage === 'admin' && $currentAction === 'sitemap') echo 'activated'; ?>></md-list-item>
                <?php endif; ?>
            </md-list>
        </nav>
        <div class="sidebar-footer">
            <?php if ($isLoggedIn): ?>
                <div class="user-info">
                    <div class="user-name"><?php echo e($currentUsername); ?></div>
                    <div class="user-role"><?php echo e(trans('user_role_display', ['role' => $currentUserRole])); ?></div>
                </div>
                <md-outlined-button style="width: 100%; margin-top: 5px;" href="<?php echo url('logout'); ?>"><?php echo e(trans('logout')); ?></md-outlined-button>
            <?php else: ?>
                <md-filled-button style="width: 100%; margin-top: 5px;" href="<?php echo url('login'); ?>"><?php echo e(trans('login')); ?></md-filled-button>
                <md-outlined-button style="width: 100%; margin-top: 5px;" href="<?php echo url('register'); ?>"><?php echo e(trans('register')); ?></md-outlined-button>
            <?php endif; ?>
        </div>
    </aside>

    <div class="main-content">
        <div class="content-inner"> <?php // Papildomas wrapper turiniui ir pranešimams ?>
    <?php
    // Flash pranešimai
    $success_message = get_flash_message('success_message');
    $error_message = get_flash_message('error_message');
    // Note: Flash messages themselves are translated at the point they are SET.
    // Here we just display them. We could add aria-labels for accessibility.
    if ($success_message) {
        echo '<div class="alert alert-success flash-message" role="alert" aria-label="' . e(trans('flash_success_message_aria')) . '">' . e($success_message) . '</div>';
    }
    if ($error_message) {
        echo '<div class="alert alert-danger flash-message" role="alert" aria-label="' . e(trans('flash_error_message_aria')) . '">' . e($error_message) . '</div>';
    }
    ?>
    <?php // Čia bus įkeltas $view_template turinys (pvz., home.php, companies/index.php) ?>