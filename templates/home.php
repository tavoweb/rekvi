<?php
// templates/home.php

$isLoggedIn = $view_data['isLoggedIn'] ?? false;
$username = $view_data['username'] ?? null;
$total_companies = $view_data['total_companies'] ?? 0;
$isAdmin = $view_data['auth'] ? $view_data['auth']->isAdmin() : false;

$view_data['meta_title'] = trans('home_meta_title');
?>

<h1><?php echo e(trans('home_page_title')); ?></h1>

<?php // Search form specific to home page, if it was indeed present ?>
<div class="home-search-section card"> <?php // Added a wrapper class for styling ?>
    <h2><?php echo e(trans('search_placeholder')); ?></h2> <?php // Re-using placeholder as a title for search section ?>
    <form id="home-search-form" action="<?php echo url('companies'); ?>" method="GET" class="search-form">
        <input type="text" name="search_query" id="home-search-input" class="search-input-field" placeholder="<?php echo e(trans('search_placeholder')); ?>" value="<?php echo isset($_GET['search_query']) ? e($_GET['search_query']) : ''; ?>">
        <button type="submit" class="button search-submit-button"><span class="material-icons">search</span></button>
        <div id="home-search-suggestions-container" class="suggestions-container"></div>
    </form>
</div>

<p><?php
    echo trans('home_intro_text', [
        'company_list_url' => url('companies')
    ]);
?></p>

<p><?php echo e(trans('total_companies_info', ['count' => $total_companies])); ?></p>

<?php if ($isLoggedIn && $username): ?>
    <p><?php echo e(trans('logged_in_as', ['username' => $username])); ?></p>

    <?php if ($isAdmin): ?>
        <p><a href="<?php echo url('admin', 'dashboard'); ?>" class="button"><?php echo e(trans('go_to_admin_panel_button')); ?></a></p>
    <?php endif; ?>

<?php endif; ?>