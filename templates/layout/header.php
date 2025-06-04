<?php
// templates/layout/header.php
// $auth objektas yra prieinamas iš index.php scope, nes header.php įtraukiamas index.php faile
/** @var Auth $auth */ // подсказка IDE
$isLoggedIn = $auth->isLoggedIn(); 
$currentUsername = $auth->getCurrentUsername();
$currentUserRole = $auth->getCurrentUserRole();
$currentPage = $_GET['page'] ?? 'home';
$currentAction = $_GET['action'] ?? '';
?>
<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($view_data['meta_title']) ? e($view_data['meta_title']) : 'Rekvizitų Valdymo Sistema'; ?></title>
    <meta name="description" content="<?php echo isset($view_data['meta_description']) ? e($view_data['meta_description']) : 'Patogi įmonių rekvizitų paieškos ir valdymo sistema.'; ?>">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css"> <?php // Root-relative path ensures CSS loads on all pages ?>
</head>
<body>
<!-- Overlay for mobile menu -->
<div class="sidebar-overlay"></div>

<!-- Mobile Top Bar -->
<div class="mobile-topbar">
    <a href="<?php echo url('home'); ?>" class="mobile-brand">RekvizitaiPRO</a>
    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Perjungti navigaciją">
        <span></span><span></span><span></span>
    </button>
</div>

<div class="page-wrapper">
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="<?php echo url('home'); ?>" class="sidebar-brand-link">RekvizitaiPRO</a>
            <button class="sidebar-toggle" id="sidebarToggle" aria-label="Perjungti navigaciją">
                <span></span><span></span><span></span>
            </button>
        </div>
        <div class="sidebar-search">
            <form action="<?php echo url('companies'); ?>" method="GET">
                <input type="text" name="search_query" id="search-input" placeholder="Ieškoti įmonės..." value="<?php echo isset($_GET['search_query']) ? e($_GET['search_query']) : ''; ?>">
            </form>
            <div id="search-suggestions-container"></div>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="<?php echo url('home'); ?>" class="<?php echo $currentPage === 'home' ? 'active' : ''; ?>">Pradinis</a></li>
                <li><a href="<?php echo url('companies'); ?>" class="<?php echo ($currentPage === 'companies' && !in_array($currentAction, ['create', 'import'])) ? 'active' : ''; ?>">Įmonių sąrašas</a></li>
                <li><a href="<?php echo url('companies', 'create'); ?>" class="<?php echo ($currentPage === 'companies' && $currentAction === 'create') ? 'active' : ''; ?>">Pridėti įmonę</a></li>
                <?php if ($auth->isAdmin()): ?>
                    <li class="sidebar-nav-separator">Administratoriaus Skydelis</li>
                    <li><a href="<?php echo url('admin', 'dashboard'); ?>" class="<?php echo ($currentPage === 'admin' && ($currentAction === 'dashboard' || $currentAction === null || $currentAction === '')) ? 'active' : ''; ?>">Pagrindinis Skydelis</a></li>
                    <li><a href="<?php echo url('admin', 'users'); ?>" class="<?php echo ($currentPage === 'admin' && $currentAction === 'users') ? 'active' : ''; ?>">Vartotojų Valdymas</a></li>
                    <li><a href="<?php echo url('companies', 'import'); ?>" class="<?php echo ($currentPage === 'companies' && $currentAction === 'import') ? 'active' : ''; ?>">Importuoti Įmones</a></li>
                    <li><a href="<?php echo url('admin', 'sitemap'); ?>" class="<?php echo ($currentPage === 'admin' && $currentAction === 'sitemap') ? 'active' : ''; ?>">Sitemap Generavimas</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <?php if ($isLoggedIn): ?>
                <div class="user-info">
                    <div class="user-name"><?php echo e($currentUsername); ?></div>
                    <div class="user-role">(<?php echo e($currentUserRole); ?>)</div>
                </div>
                <a href="<?php echo url('logout'); ?>" class="button button-outline button-small">Atsijungti</a>
            <?php else: ?>
                <a href="<?php echo url('login'); ?>" class="button button-primary button-small <?php echo $currentPage === 'login' ? 'active' : ''; ?>">Prisijungti</a>
                <a href="<?php echo url('register'); ?>" class="button button-outline button-small <?php echo $currentPage === 'register' ? 'active' : ''; ?>">Registruotis</a>
            <?php endif; ?>
        </div>
    </aside>

    <div class="main-content">
        <div class="content-inner"> <?php // Papildomas wrapper turiniui ir pranešimams ?>
    <?php
    // Flash pranešimai
    $success_message = get_flash_message('success_message');
    $error_message = get_flash_message('error_message');
    if ($success_message) {
        echo '<div class="alert alert-success flash-message">' . e($success_message) . '</div>';
    }
    if ($error_message) {
        echo '<div class="alert alert-danger flash-message">' . e($error_message) . '</div>';
    }
    ?>
    <?php // Čia bus įkeltas $view_template turinys (pvz., home.php, companies/index.php) ?>