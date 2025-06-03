<?php
// templates/auth/login_form.php
// $errors ir $form_values turėtų būti perduoti iš index.php
$errors = $view_data['errors'] ?? [];
$form_values = $view_data['form_values'] ?? ['username_or_email' => ''];
?>
<div class="auth-form">
    <h2>Prisijungimas</h2>
    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-danger"><?php echo e($errors['general']); ?></div>
    <?php endif; ?>
    <form action="<?php echo url('login'); ?>" method="POST" novalidate>
        <div class="form-group">
            <label for="username_or_email">Vartotojo vardas arba el. paštas:</label>
            <input type="text" name="username_or_email" id="username_or_email" class="<?php echo isset($errors['credentials']) ? 'is-invalid' : ''; ?>" value="<?php echo e($form_values['username_or_email']); ?>" required>
            <?php if (isset($errors['credentials'])): ?>
                <div class="form-error"><?php echo e($errors['credentials']); ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="password">Slaptažodis:</label>
            <input type="password" name="password" id="password" class="<?php echo isset($errors['credentials']) ? 'is-invalid' : ''; ?>" required>
        </div>
        <button type="submit" class="button button-primary full-width">Prisijungti</button>
    </form>
    <p>Neturite paskyros? <a href="<?php echo url('register'); ?>">Registruokitės čia</a>.</p>
</div>