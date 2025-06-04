<?php
// templates/home.php

// It's good practice to ensure $view_data variables are set before using them.
$isLoggedIn = $view_data['isLoggedIn'] ?? false;
$username = $view_data['username'] ?? null;
$total_companies = $view_data['total_companies'] ?? 0;
$isAdmin = $view_data['auth'] ? $view_data['auth']->isAdmin() : false;

// Set meta title for the home page specifically if desired
// This will be used by header.php
$view_data['meta_title'] = trans('home_meta_title');

?>

<h1><?php echo e(trans('home_page_title')); ?></h1>

<p><?php
    echo trans('home_intro_text', [
        'company_list_url' => url('companies')
    ]);
?></p>

<p><?php echo e(trans('total_companies_info', ['count' => $total_companies])); ?></p>

<?php if ($isLoggedIn && $username): ?>
    <p><?php echo e(trans('logged_in_as', ['username' => $username])); ?></p>

    <?php /* Example: Link to a user profile page - uncomment and adapt if such a page exists
    <p><a href="<?php echo url('profile'); ?>" class="button"><?php echo e(trans('view_profile_button')); ?></a></p>
    */ ?>

    <?php if ($isAdmin): ?>
        <p><a href="<?php echo url('admin', 'dashboard'); ?>" class="button"><?php echo e(trans('go_to_admin_panel_button')); ?></a></p>
    <?php endif; ?>

<?php else: ?>
    <?php // Potentially add a message for guests, or rely on login/register buttons in header ?>
<?php endif; ?>