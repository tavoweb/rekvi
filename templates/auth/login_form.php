<?php
// templates/auth/login_form.php
$form_values = $view_data['form_values'] ?? ['username_or_email' => ''];
$errors = $view_data['errors'] ?? [];

// Set meta title for login page
$view_data['meta_title'] = trans('login_meta_title');
?>
<div class="form-container auth-form">
    <h1><?php echo e(trans('login_page_title')); ?></h1>

    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-danger">
            <?php echo e($errors['general']); ?> <?php // This error message comes from index.php, will be translated there ?>
            <?php if (!empty($errors['credentials'])): ?>
                <br><?php echo e(trans('check_entered_data')); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo url('login'); ?>" method="POST">
        <div class="form-group">
            <label for="username_or_email"><?php echo e(trans('username_or_email_label')); ?>:</label>
            <input type="text" id="username_or_email" name="username_or_email" value="<?php echo e($form_values['username_or_email']); ?>" required>
        </div>

        <div class="form-group">
            <label for="password"><?php echo e(trans('password_label')); ?>:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="button button-primary"><?php echo e(trans('login_button')); ?></button>
    </form>
    <p class="auth-switch-prompt">
        <?php echo e(trans('dont_have_account')); ?>
        <a href="<?php echo url('register'); ?>"><?php echo e(trans('register_link_text')); ?></a>
    </p>
</div>