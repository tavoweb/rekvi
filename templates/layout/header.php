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
    <link rel="stylesheet" href="/css/style.css"> <?php // Root-relative path ensures CSS loads on all pages ?>
</head>
<body>
<!-- Overlay for mobile menu -->
<div class="sidebar-overlay"></div>

<!-- Mobile Top Bar -->
<div class="mobile-topbar">
    <a href="<?php echo url('home'); ?>" class="mobile-brand"><?php echo e(trans('main_site_brand')); ?></a>
    <button class="sidebar-toggle" id="sidebarToggleMobile" aria-label="<?php echo e(trans('toggle_navigation')); ?>"> <?php // Changed id to avoid conflict, if any ?>
        <span></span><span></span><span></span>
    </button>
</div>

<div class="page-wrapper">
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="<?php echo url('home'); ?>" class="sidebar-brand-link"><?php echo e(trans('main_site_brand')); ?></a>
            <button class="sidebar-toggle" id="sidebarToggleDesktop" aria-label="<?php echo e(trans('toggle_navigation')); ?>"> <?php // Changed id to avoid conflict ?>
                <span></span><span></span><span></span>
            </button>
        </div>
        <div class="sidebar-search">
            <form action="<?php echo url('companies'); ?>" method="GET">
                <input type="text" name="search_query" id="search-input" placeholder="<?php echo e(trans('search_placeholder')); ?>" value="<?php echo isset($_GET['search_query']) ? e($_GET['search_query']) : ''; ?>">
            </form>
            <div id="search-suggestions-container"></div>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="<?php echo url('home'); ?>" class="<?php echo $currentPage === 'home' ? 'active' : ''; ?>"><?php echo e(trans('homepage')); ?></a></li>
                <li><a href="<?php echo url('companies'); ?>" class="<?php echo ($currentPage === 'companies' && !in_array($currentAction, ['create', 'import'])) ? 'active' : ''; ?>"><?php echo e(trans('company_list')); ?></a></li>
                <li><a href="<?php echo url('companies', 'create'); ?>" class="<?php echo ($currentPage === 'companies' && $currentAction === 'create') ? 'active' : ''; ?>"><?php echo e(trans('add_company')); ?></a></li>
                <?php if ($auth->isAdmin()): ?>
                    <li class="sidebar-nav-separator"><?php echo e(trans('admin_panel_title')); ?></li>
                    <li><a href="<?php echo url('admin', 'dashboard'); ?>" class="<?php echo ($currentPage === 'admin' && $currentAction === 'dashboard') ? 'active' : ''; ?>"><?php echo e(trans('admin_dashboard')); ?></a></li>
                    <li><a href="<?php echo url('admin', 'users'); ?>" class="<?php echo ($currentPage === 'admin' && $currentAction === 'users') ? 'active' : ''; ?>"><?php echo e(trans('user_management')); ?></a></li>
                    <li><a href="<?php echo url('companies', 'import'); ?>" class="<?php echo ($currentPage === 'companies' && $currentAction === 'import') ? 'active' : ''; ?>"><?php echo e(trans('import_companies')); ?></a></li>
                    <li><a href="<?php echo url('admin', 'sitemap'); ?>" class="<?php echo ($currentPage === 'admin' && $currentAction === 'sitemap') ? 'active' : ''; ?>"><?php echo e(trans('sitemap_generation')); ?></a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <?php if ($isLoggedIn): ?>
                <div class="user-info">
                    <div class="user-name"><?php echo e($currentUsername); ?></div>
                    <div class="user-role"><?php echo e(trans('user_role_display', ['role' => $currentUserRole])); ?></div>
                </div>
                <a href="<?php echo url('logout'); ?>" class="button button-outline button-small"><?php echo e(trans('logout')); ?></a>
            <?php else: ?>
                <a href="<?php echo url('login'); ?>" class="button button-primary button-small <?php echo $currentPage === 'login' ? 'active' : ''; ?>"><?php echo e(trans('login')); ?></a>
                <a href="<?php echo url('register'); ?>" class="button button-outline button-small <?php echo $currentPage === 'register' ? 'active' : ''; ?>"><?php echo e(trans('register')); ?></a>
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