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
<div class="home-search-section card" style="padding: 16px; margin-bottom: 24px;">
    <h2><?php echo e(trans('search_companies_section_title')); ?></h2> <?php // Changed to a more descriptive title ?>
    <form id="home-search-form" action="<?php echo url('companies'); ?>" method="GET" style="display: flex; align-items: center; gap: 8px;">
        <md-outlined-text-field
            style="flex-grow: 1;"
            label="<?php echo e(trans('search_placeholder')); ?>"
            name="search_query"
            id="home-search-input"
            value="<?php echo isset($_GET['search_query']) ? e($_GET['search_query']) : ''; ?>">
        </md-outlined-text-field>
        <md-filled-button type="submit">
            <md-icon slot="icon">search</md-icon>
            <?php echo e(trans('search_button_label')); ?>
        </md-filled-button>
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
        <p>
            <md-filled-button href="<?php echo url('admin', 'dashboard'); ?>">
                <md-icon slot="icon">admin_panel_settings</md-icon>
                <?php echo e(trans('go_to_admin_panel_button')); ?>

            </md-filled-button>
        </p>
    <?php endif; ?>

<?php endif; ?>