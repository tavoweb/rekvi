<?php
// templates/admin/dashboard.php

// Page title
// The $view_data['meta_title'] is set in index.php for this route.
// We should ensure that the string set in index.php is also translatable,
// or set it here directly using trans(). For now, let's assume index.php will handle its translation.
// If $view_data['meta_title'] is specifically for this page, we can do:
// $view_data['meta_title'] = trans('admin_dashboard_meta_title');
// This line might be better placed in index.php where $view_data is populated for this route.
// For now, I will leave the original PHP comment and focus on the visible text here.

?>

<h1><?php echo e(trans('admin_panel_title')); ?></h1>
<p><?php echo e(trans('welcome_to_admin_panel')); ?></p>

<div class="admin-actions">
    <p><a href="<?php echo url('admin', 'users'); ?>" class="button"><?php echo e(trans('user_list_link')); ?></a></p> <?php // Assuming 'user_list_link' is preferred over 'user_management' for this specific link ?>
    <p><a href="<?php echo url('companies', 'import'); ?>" class="button"><?php echo e(trans('import_companies')); ?></a></p>
    <p><a href="<?php echo url('admin', 'sitemap'); ?>" class="button"><?php echo e(trans('sitemap_generation')); ?></a></p>
</div>

<hr class="admin-section-divider">
<h2><?php echo e(trans('settings_section_title')); ?></h2>

<div class="admin-settings-section">
    <h3><?php echo e(trans('language_settings_title')); ?></h3>
    <form action="<?php echo url('admin', 'update_language_settings'); ?>" method="POST">
        <div class="form-group">
            <label for="default_language_select"><?php echo e(trans('select_default_language_label')); ?></label>
            <select name="selected_language" id="default_language_select">
                <option value="lt" <?php echo (getCurrentLanguageCode() === 'lt') ? 'selected' : ''; ?>><?php echo e(trans('language_lithuanian')); ?></option>
                <option value="en" <?php echo (getCurrentLanguageCode() === 'en') ? 'selected' : ''; ?>><?php echo e(trans('language_english')); ?></option>
            </select>
        </div>
        <button type="submit" class="button"><?php echo e(trans('save_language_button')); ?></button>
    </form>
</div>
