<?php
// templates/admin/sitemap.php
$view_data['meta_title'] = trans('sitemap_meta_title');

// Display flash messages for sitemap generation results
$sitemap_success_message = get_flash_message('sitemap_success');
$sitemap_error_message = get_flash_message('sitemap_error');
?>

<h1><?php echo e(trans('sitemap_generation')); ?></h1> <?php // Reusing existing key ?>

<?php if ($sitemap_success_message): ?>
    <div class="alert alert-success flash-message"><?php echo e($sitemap_success_message); ?></div> <?php // This message comes from index.php, will be translated there ?>
<?php endif; ?>
<?php if ($sitemap_error_message): ?>
    <div class="alert alert-danger flash-message"><?php echo e($sitemap_error_message); ?></div> <?php // This message comes from index.php, will be translated there ?>
<?php endif; ?>

<p><?php echo e(trans('sitemap_description')); ?></p>

<form action="<?php echo url('admin', 'generate_sitemap_action'); ?>" method="POST">
    <button type="submit" class="button"><?php echo e(trans('generate_sitemap_button')); ?></button>
</form>
