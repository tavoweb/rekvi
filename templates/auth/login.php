<?php
// templates/auth/login.php
// Assuming header.php handles its own logic for what to show for non-logged-in users.
// $view_data['meta_title'] should be set here or in the controller (index.php for this route)
$view_data['meta_title'] = trans('login_page_title'); // Ensure this translation key exists
?>
<h1><?php echo e(trans('login_page_heading')); ?></h1>

<?php if (isset($view_data['loginError'])): // Adjusted to use $view_data consistently ?>
    <div class="alert alert-danger" role="alert" style="margin-bottom: 16px; padding: 8px; border: 1px solid var(--md-sys-color-error); color: var(--md-sys-color-error);">
        <?php echo e($view_data['loginError']); ?>
    </div>
<?php endif; ?>

<form action="<?php echo url('login'); ?>" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
    <md-outlined-text-field
        label="<?php echo e(trans('username_or_email_label')); ?>"
        type="text"
        id="username"
        name="username"
        required
        autocomplete="username"
        value="<?php echo e($view_data['submitted_username'] ?? ''); ?>"> <?php // Preserve submitted username on error ?>
    </md-outlined-text-field>

    <md-outlined-text-field
        label="<?php echo e(trans('password_label')); ?>"
        type="password"
        id="password"
        name="password"
        required
        autocomplete="current-password">
    </md-outlined-text-field>

    <md-filled-button type="submit" style="align-self: flex-start;">
        <md-icon slot="icon">login</md-icon>
        <?php echo e(trans('login_button_label')); ?>

    </md-filled-button>
</form>
<?php
// Note: login.php in the original structure was a standalone page without full layout.
// If it's meant to be integrated into the main layout (with header.php, footer.php),
// that integration happens at the router/controller level (index.php).
// The provided snippet only shows the form part.
// For consistency, I'm assuming this template might be included within a larger structure
// or that header/footer are handled by the calling script if it's a full page.
// The original code had <?php include __DIR__ . '/../layout/header.php'; ?>
// and <?php include __DIR__ . '/../layout/footer.php'; ?>
// These should be kept if this is a full HTML page output.
// For this exercise, I'm focusing on the form elements themselves.
?>