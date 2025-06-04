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

    <md-outlined-text-field
        label="<?php echo e(trans('label_company_name')); ?>*"
        name="pavadinimas"
        id="pavadinimas"
        value="<?php echo e($company['pavadinimas'] ?? $_POST['pavadinimas'] ?? ''); ?>"
        required
        style="width: 100%; margin-bottom: 16px;">
        <?php if (!empty($errors['pavadinimas'])): ?>
            <span slot="error"><?php echo e($errors['pavadinimas']); ?></span> <?php // Translate in index.php ?>
        <?php endif; ?>
    </md-outlined-text-field>

    <md-outlined-text-field
        label="<?php echo e(trans('label_company_code')); ?>*"
        name="imones_kodas"
        id="imones_kodas"
        value="<?php echo e($company['imones_kodas'] ?? $_POST['imones_kodas'] ?? ''); ?>"
        required
        <?php echo $is_edit_mode ? 'readonly' : ''; ?>
        style="width: 100%; margin-bottom: 16px;">
        <?php if ($is_edit_mode): ?> <small slot="supporting-text"><?php echo e(trans('company_code_edit_readonly_note')); ?></small> <?php endif; ?>
        <?php if (!empty($errors['imones_kodas'])): ?>
            <span slot="error"><?php echo e($errors['imones_kodas']); ?></span> <?php // Translate in index.php ?>
        <?php endif; ?>
    </md-outlined-text-field>

    <md-outlined-text-field
        label="<?php echo e(trans('label_pvm_code')); ?>"
        name="pvm_kodas"
        id="pvm_kodas"
        value="<?php echo e($company['pvm_kodas'] ?? $_POST['pvm_kodas'] ?? ''); ?>"
        style="width: 100%; margin-bottom: 16px;">
    </md-outlined-text-field>

    <div class="form-group" style="margin-bottom: 16px;"> <?php // File input styling might need custom CSS if md-web components don't cover it ?>
        <label for="logotipas"><?php echo e(trans('label_logo')); ?></label>
        <input type="file" id="logotipas" name="logotipas" style="display: block; margin-top: 8px; margin-bottom: 4px;">
        <small><?php echo e(trans('logo_upload_requirements')); ?></small>
        <?php if ($is_edit_mode && !empty($company['logotipas'])): ?>
            <div class="current-logo-display" style="margin-top: 8px;">
                <p><?php echo e(trans('current_logo')); ?></p>
                <img src="<?php echo LOGO_UPLOAD_DIR_PUBLIC . e($company['logotipas']); ?>" alt="<?php echo e(trans('company_logo_alt', ['name' => $company['pavadinimas']])); ?>" style="max-width: 150px; height: auto; margin-top: 10px; border: 1px solid #ccc; padding: 4px;">
                <label style="display: block; margin-top: 4px;"><input type="checkbox" name="remove_logo" value="1"> <?php echo e(trans('remove_logo_label')); ?></label>
            </div>
        <?php endif; ?>
        <?php if (!empty($errors['logotipas'])): ?>
            <div class="field-error-message" style="color: var(--md-sys-color-error); font-size: 0.75rem; margin-top: 4px;"><?php echo e($errors['logotipas']); ?></div> <?php // Error from handle_logo_upload, translate there ?>
        <?php endif; ?>
    </div>

    <md-outlined-text-field
        label="<?php echo e(trans('label_address_street')); ?>"
        name="adresas_gatve"
        id="adresas_gatve"
        value="<?php echo e($company['adresas_gatve'] ?? $_POST['adresas_gatve'] ?? ''); ?>"
        style="width: 100%; margin-bottom: 16px;">
    </md-outlined-text-field>

    <md-outlined-text-field
        label="<?php echo e(trans('label_address_city')); ?>"
        name="adresas_miestas"
        id="adresas_miestas"
        value="<?php echo e($company['adresas_miestas'] ?? $_POST['adresas_miestas'] ?? ''); ?>"
        style="width: 100%; margin-bottom: 16px;">
    </md-outlined-text-field>

    <md-outlined-text-field
        label="<?php echo e(trans('label_address_postcode')); ?>"
        name="adresas_pasto_kodas"
        id="adresas_pasto_kodas"
        value="<?php echo e($company['adresas_pasto_kodas'] ?? $_POST['adresas_pasto_kodas'] ?? ''); ?>"
        style="width: 100%; margin-bottom: 16px;">
    </md-outlined-text-field>

    <md-outlined-text-field
        label="<?php echo e(trans('label_address_country')); ?>"
        name="adresas_salis"
        id="adresas_salis"
        value="<?php echo e($company['adresas_salis'] ?? $_POST['adresas_salis'] ?? ''); ?>"
        style="width: 100%; margin-bottom: 16px;">
    </md-outlined-text-field>

    <md-outlined-text-field
        label="<?php echo e(trans('label_phone')); ?>"
        name="telefonas"
        id="telefonas"
        type="tel"
        value="<?php echo e($company['telefonas'] ?? $_POST['telefonas'] ?? ''); ?>"
        style="width: 100%; margin-bottom: 16px;">
    </md-outlined-text-field>

    <md-outlined-text-field
        label="<?php echo e(trans('label_email')); ?>"
        name="el_pastas"
        id="el_pastas"
        type="email"
        value="<?php echo e($company['el_pastas'] ?? $_POST['el_pastas'] ?? ''); ?>"
        style="width: 100%; margin-bottom: 16px;">
    </md-outlined-text-field>

    <md-outlined-text-field
        label="<?php echo e(trans('label_website')); ?>"
        name="tinklalapis"
        id="tinklalapis"
        type="url"
        value="<?php echo e($company['tinklalapis'] ?? $_POST['tinklalapis'] ?? ''); ?>"
        placeholder="<?php echo e(trans('url_placeholder')); ?>"
        style="width: 100%; margin-bottom: 16px;">
    </md-outlined-text-field>

    <md-outlined-text-field
        label="<?php echo e(trans('label_contact_person')); ?>"
        name="vadovas_vardas_pavarde"
        id="vadovas_vardas_pavarde"
        value="<?php echo e($company['vadovas_vardas_pavarde'] ?? $_POST['vadovas_vardas_pavarde'] ?? ''); ?>"
        style="width: 100%; margin-bottom: 16px;">
    </md-outlined-text-field>

    <md-outlined-text-field
        type="textarea"
        label="<?php echo e(trans('label_working_hours')); ?>"
        name="darbo_laikas"
        id="darbo_laikas"
        rows="3"
        style="width: 100%; margin-bottom: 16px;"><?php echo e($company['darbo_laikas'] ?? $_POST['darbo_laikas'] ?? ''); ?></md-outlined-text-field>

    <md-outlined-text-field
        label="<?php echo e(trans('label_bank_name')); ?>"
        name="banko_pavadinimas"
        id="banko_pavadinimas"
        value="<?php echo e($company['banko_pavadinimas'] ?? $_POST['banko_pavadinimas'] ?? ''); ?>"
        style="width: 100%; margin-bottom: 16px;">
    </md-outlined-text-field>

    <md-outlined-text-field
        label="<?php echo e(trans('label_bank_account')); ?>"
        name="banko_saskaita"
        id="banko_saskaita"
        value="<?php echo e($company['banko_saskaita'] ?? $_POST['banko_saskaita'] ?? ''); ?>"
        style="width: 100%; margin-bottom: 16px;">
    </md-outlined-text-field>

    <md-outlined-text-field
        type="textarea"
        label="<?php echo e(trans('label_notes')); ?>"
        name="pastabos"
        id="pastabos"
        rows="3"
        style="width: 100%; margin-bottom: 24px;"><?php echo e($company['pastabos'] ?? $_POST['pastabos'] ?? ''); ?></md-outlined-text-field>

    <div class="form-actions" style="display: flex; gap: 8px; justify-content: flex-start; margin-top: 16px;">
        <md-filled-button type="submit">
            <md-icon slot="icon">save</md-icon>
            <?php echo e(trans('save_company_button')); ?>

        </md-filled-button>
        <md-outlined-button href="<?php echo $is_edit_mode ? url('companies', 'view', $company['id']) : url('companies'); ?>">
            <md-icon slot="icon">cancel</md-icon>
            <?php echo e(trans('cancel_button')); ?>

        </md-outlined-button>
    </div>
</form>