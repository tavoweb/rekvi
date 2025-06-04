<?php
// templates/auth/register_form.php
$form_values = $view_data['form_values'] ?? ['username' => '', 'email' => ''];
$errors = $view_data['errors'] ?? [];

// Set meta title for register page
$view_data['meta_title'] = trans('register_meta_title');
?>
<div class="form-container auth-form">
    <h1><?php echo e(trans('register_page_title')); ?></h1>

    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-danger"><?php echo e($errors['general']); ?></div> <?php // This error comes from Auth.php/index.php, translate there ?>
    <?php endif; ?>

    <form action="<?php echo url('register'); ?>" method="POST">
        <div class="form-group">
            <label for="username"><?php echo e(trans('username_label')); ?>:</label>
            <input type="text" id="username" name="username" value="<?php echo e($form_values['username']); ?>" required>
            <?php if (!empty($errors['username'])): ?>
                <div class="field-error-message"><?php echo e($errors['username']); ?></div> <?php // Translate in Auth.php/index.php ?>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email"><?php echo e(trans('email_label')); ?>:</label>
            <input type="email" id="email" name="email" value="<?php echo e($form_values['email']); ?>" required>
            <?php if (!empty($errors['email'])): ?>
                <div class="field-error-message"><?php echo e($errors['email']); ?></div> <?php // Translate in Auth.php/index.php ?>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="password"><?php echo e(trans('password_label')); ?>:</label>
            <input type="password" id="password" name="password" required>
            <?php if (!empty($errors['password'])): ?>
                <div class="field-error-message"><?php echo e($errors['password']); ?></div> <?php // Translate in Auth.php/index.php ?>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="confirm_password"><?php echo e(trans('confirm_password_label')); ?>:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <?php if (!empty($errors['confirm_password'])): ?>
                <div class="field-error-message"><?php echo e($errors['confirm_password']); ?></div> <?php // Translate in Auth.php/index.php ?>
            <?php endif; ?>
        </div>

        <button type="submit" class="button button-primary"><?php echo e(trans('register_button')); ?></button>
    </form>
    <p class="auth-switch-prompt">
        <?php echo e(trans('already_have_account')); ?>
        <a href="<?php echo url('login'); ?>"><?php echo e(trans('login_link_text')); ?></a>
    </p>
</div>