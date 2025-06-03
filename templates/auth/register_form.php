<?php
// templates/auth/register_form.php
$errors = $view_data['errors'] ?? [];
$form_values = $view_data['form_values'] ?? ['username' => '', 'email' => ''];
?>
<div class="auth-form">
    <h2>Registracija</h2>
    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-danger"><?php echo e($errors['general']); ?></div>
    <?php endif; ?>
    <form action="<?php echo url('register'); ?>" method="POST" novalidate>
        <div class="form-group">
            <label for="username">Vartotojo vardas:</label>
            <input type="text" name="username" id="username" class="<?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" value="<?php echo e($form_values['username']); ?>" required>
            <?php if (isset($errors['username'])): ?><div class="form-error"><?php echo e($errors['username']); ?></div><?php endif; ?>
        </div>
        <div class="form-group">
            <label for="email">El. paštas:</label>
            <input type="email" name="email" id="email" class="<?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" value="<?php echo e($form_values['email']); ?>" required>
            <?php if (isset($errors['email'])): ?><div class="form-error"><?php echo e($errors['email']); ?></div><?php endif; ?>
        </div>
        <div class="form-group">
            <label for="password">Slaptažodis (min. 8 simboliai):</label>
            <input type="password" name="password" id="password" class="<?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" required>
            <?php if (isset($errors['password'])): ?><div class="form-error"><?php echo e($errors['password']); ?></div><?php endif; ?>
        </div>
        <div class="form-group">
            <label for="confirm_password">Pakartokite slaptažodį:</label>
            <input type="password" name="confirm_password" id="confirm_password" class="<?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" required>
            <?php if (isset($errors['confirm_password'])): ?><div class="form-error"><?php echo e($errors['confirm_password']); ?></div><?php endif; ?>
        </div>
        <button type="submit" class="button button-primary full-width">Registruotis</button>
    </form>
    <p>Jau turite paskyrą? <a href="<?php echo url('login'); ?>">Prisijunkite čia</a>.</p>
</div>