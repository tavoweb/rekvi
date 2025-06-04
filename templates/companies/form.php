<?php
// templates/companies/form.php
$company = $view_data['company'] ?? null; // Current company data for editing, null for creating
$errors = $view_data['errors'] ?? [];
$is_edit_mode = ($company && isset($company['id']));

if ($is_edit_mode) {
    $view_data['meta_title'] = trans('edit_company_meta_title_prefix') . ' ' . e($company['pavadinimas']) . ' - ' . trans('site_title');
} else {
    $view_data['meta_title'] = trans('create_company_meta_title');
}
?>

<h1>
    <?php if ($is_edit_mode): ?>
        <?php echo e(trans('edit_company_title_prefix')); ?> <?php echo e($company['pavadinimas']); ?>
    <?php else: ?>
        <?php echo e(trans('create_company_title')); ?>
    <?php endif; ?>
</h1>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-danger"><?php echo e($errors['general']); ?></div> <?php // Translate in index.php ?>
<?php endif; ?>

<form action="<?php echo $is_edit_mode ? url('companies', 'edit', $company['id']) : url('companies', 'create'); ?>" method="POST" enctype="multipart/form-data" class="company-form">
    <p><?php echo trans('required_fields_info'); ?></p>

    <div class="form-group">
        <label for="pavadinimas"><?php echo e(trans('label_company_name')); ?><span class="required-asterisk">*</span></label>
        <input type="text" id="pavadinimas" name="pavadinimas" value="<?php echo e($company['pavadinimas'] ?? $_POST['pavadinimas'] ?? ''); ?>" required>
        <?php if (!empty($errors['pavadinimas'])): ?>
            <div class="field-error-message"><?php echo e($errors['pavadinimas']); ?></div> <?php // Translate in index.php ?>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="imones_kodas"><?php echo e(trans('label_company_code')); ?><span class="required-asterisk">*</span></label>
        <input type="text" id="imones_kodas" name="imones_kodas" value="<?php echo e($company['imones_kodas'] ?? $_POST['imones_kodas'] ?? ''); ?>" required <?php echo $is_edit_mode ? 'readonly' : ''; ?>>
         <?php if ($is_edit_mode): ?> <small><?php echo e(trans('company_code_edit_readonly_note')); ?></small> <?php endif; ?>
        <?php if (!empty($errors['imones_kodas'])): ?>
            <div class="field-error-message"><?php echo e($errors['imones_kodas']); ?></div> <?php // Translate in index.php ?>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="pvm_kodas"><?php echo e(trans('label_pvm_code')); ?></label>
        <input type="text" id="pvm_kodas" name="pvm_kodas" value="<?php echo e($company['pvm_kodas'] ?? $_POST['pvm_kodas'] ?? ''); ?>">
    </div>

    <div class="form-group">
        <label for="logotipas"><?php echo e(trans('label_logo')); ?></label>
        <input type="file" id="logotipas" name="logotipas">
        <small><?php echo e(trans('logo_upload_requirements')); ?></small>
        <?php if ($is_edit_mode && !empty($company['logotipas'])): ?>
            <div class="current-logo-display">
                <p><?php echo e(trans('current_logo')); ?></p>
                <img src="<?php echo LOGO_UPLOAD_DIR_PUBLIC . e($company['logotipas']); ?>" alt="<?php echo e(trans('company_logo_alt', ['name' => $company['pavadinimas']])); ?>" style="max-width: 150px; height: auto; margin-top: 10px;">
                <label><input type="checkbox" name="remove_logo" value="1"> <?php echo e(trans('remove_logo_label')); ?></label>
            </div>
        <?php endif; ?>
        <?php if (!empty($errors['logotipas'])): ?>
            <div class="field-error-message"><?php echo e($errors['logotipas']); ?></div> <?php // Error from handle_logo_upload, translate there ?>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="adresas_gatve"><?php echo e(trans('label_address_street')); ?></label>
        <input type="text" id="adresas_gatve" name="adresas_gatve" value="<?php echo e($company['adresas_gatve'] ?? $_POST['adresas_gatve'] ?? ''); ?>">
    </div>

    <div class="form-group">
        <label for="adresas_miestas"><?php echo e(trans('label_address_city')); ?></label>
        <input type="text" id="adresas_miestas" name="adresas_miestas" value="<?php echo e($company['adresas_miestas'] ?? $_POST['adresas_miestas'] ?? ''); ?>">
    </div>

    <div class="form-group">
        <label for="adresas_pasto_kodas"><?php echo e(trans('label_address_postcode')); ?></label>
        <input type="text" id="adresas_pasto_kodas" name="adresas_pasto_kodas" value="<?php echo e($company['adresas_pasto_kodas'] ?? $_POST['adresas_pasto_kodas'] ?? ''); ?>">
    </div>

    <div class="form-group">
        <label for="adresas_salis"><?php echo e(trans('label_address_country')); ?></label>
        <input type="text" id="adresas_salis" name="adresas_salis" value="<?php echo e($company['adresas_salis'] ?? $_POST['adresas_salis'] ?? ''); ?>">
    </div>

    <div class="form-group">
        <label for="telefonas"><?php echo e(trans('label_phone')); ?></label>
        <input type="text" id="telefonas" name="telefonas" value="<?php echo e($company['telefonas'] ?? $_POST['telefonas'] ?? ''); ?>">
    </div>

    <div class="form-group">
        <label for="el_pastas"><?php echo e(trans('label_email')); ?></label>
        <input type="email" id="el_pastas" name="el_pastas" value="<?php echo e($company['el_pastas'] ?? $_POST['el_pastas'] ?? ''); ?>">
    </div>

    <div class="form-group">
        <label for="tinklalapis"><?php echo e(trans('label_website')); ?></label>
        <input type="url" id="tinklalapis" name="tinklalapis" value="<?php echo e($company['tinklalapis'] ?? $_POST['tinklalapis'] ?? ''); ?>" placeholder="<?php echo e(trans('url_placeholder')); ?>">
    </div>

    <div class="form-group">
        <label for="vadovas_vardas_pavarde"><?php echo e(trans('label_contact_person')); ?></label>
        <input type="text" id="vadovas_vardas_pavarde" name="vadovas_vardas_pavarde" value="<?php echo e($company['vadovas_vardas_pavarde'] ?? $_POST['vadovas_vardas_pavarde'] ?? ''); ?>">
    </div>

    <div class="form-group">
        <label for="darbo_laikas"><?php echo e(trans('label_working_hours')); ?></label>
        <textarea id="darbo_laikas" name="darbo_laikas" rows="3"><?php echo e($company['darbo_laikas'] ?? $_POST['darbo_laikas'] ?? ''); ?></textarea>
    </div>

    <div class="form-group">
        <label for="banko_pavadinimas"><?php echo e(trans('label_bank_name')); ?></label>
        <input type="text" id="banko_pavadinimas" name="banko_pavadinimas" value="<?php echo e($company['banko_pavadinimas'] ?? $_POST['banko_pavadinimas'] ?? ''); ?>">
    </div>

    <div class="form-group">
        <label for="banko_saskaita"><?php echo e(trans('label_bank_account')); ?></label>
        <input type="text" id="banko_saskaita" name="banko_saskaita" value="<?php echo e($company['banko_saskaita'] ?? $_POST['banko_saskaita'] ?? ''); ?>">
    </div>

    <div class="form-group">
        <label for="pastabos"><?php echo e(trans('label_notes')); ?></label>
        <textarea id="pastabos" name="pastabos" rows="3"><?php echo e($company['pastabos'] ?? $_POST['pastabos'] ?? ''); ?></textarea>
    </div>

    <div class="form-actions">
        <button type="submit" class="button button-primary"><?php echo e(trans('save_company_button')); ?></button>
        <a href="<?php echo $is_edit_mode ? url('companies', 'view', $company['id']) : url('companies'); ?>" class="button button-outline"><?php echo e(trans('cancel_button')); ?></a>
    </div>
</form>