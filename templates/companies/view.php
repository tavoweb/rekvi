<?php
// templates/companies/view.php
$company = $view_data['company'] ?? null;
$auth = $view_data['auth'];

if (!$company) {
    echo "<p>" . e(trans('no_companies_found')) . "</p>";
    return;
}
?>

<div class="company-view-container">
    <h1><?php echo e(trans('company_details_title_prefix')); ?> <?php echo e($company['pavadinimas']); ?></h1>

    <div class="company-details">
        <?php if (!empty($company['logotipas'])): ?>
            <div class="company-logo-view">
                <img src="<?php echo LOGO_UPLOAD_DIR_PUBLIC . e($company['logotipas']); ?>" alt="<?php echo e(trans('company_logo_alt', ['name' => $company['pavadinimas']])); ?>">
            </div>
        <?php else: ?>
            <p><?php echo e(trans('no_logo_available')); ?></p>
        <?php endif; ?>

        <dl class="details-list">
            <dt><?php echo e(trans('label_company_name')); ?></dt>
            <dd><?php echo e($company['pavadinimas']); ?></dd>

            <dt><?php echo e(trans('label_company_code')); ?></dt>
            <dd><?php echo e($company['imones_kodas']); ?></dd>

            <?php if (!empty($company['pvm_kodas'])): ?>
                <dt><?php echo e(trans('label_pvm_code')); ?></dt>
                <dd><?php echo e($company['pvm_kodas']); ?></dd>
            <?php endif; ?>

            <dt><?php echo e(trans('label_address')); ?></dt>
            <dd>
                <?php
                $address_parts = [];
                if (!empty($company['adresas_gatve'])) $address_parts[] = e($company['adresas_gatve']);
                if (!empty($company['adresas_miestas'])) $address_parts[] = e($company['adresas_miestas']);
                if (!empty($company['adresas_pasto_kodas'])) $address_parts[] = e($company['adresas_pasto_kodas']);
                if (!empty($company['adresas_salis'])) $address_parts[] = e($company['adresas_salis']);
                echo implode(', ', $address_parts ?: [e(trans('not_specified'))]);
                ?>
            </dd>

            <?php if (!empty($company['telefonas'])): ?>
                <dt><?php echo e(trans('label_phone')); ?></dt>
                <dd><?php echo e($company['telefonas']); ?></dd>
            <?php endif; ?>

            <?php if (!empty($company['el_pastas'])): ?>
                <dt><?php echo e(trans('label_email')); ?></dt>
                <dd><a href="mailto:<?php echo e($company['el_pastas']); ?>"><?php echo e($company['el_pastas']); ?></a></dd>
            <?php endif; ?>

            <?php if (!empty($company['tinklalapis'])): ?>
                <dt><?php echo e(trans('label_website')); ?></dt>
                <dd><a href="<?php echo e(ensure_http_prefix($company['tinklalapis'])); ?>" target="_blank" rel="noopener noreferrer"><?php echo e($company['tinklalapis']); ?></a></dd>
            <?php endif; ?>

            <?php if (!empty($company['vadovas_vardas_pavarde'])): ?>
                <dt><?php echo e(trans('label_contact_person')); ?></dt>
                <dd><?php echo e($company['vadovas_vardas_pavarde']); ?></dd>
            <?php endif; ?>

            <?php if (!empty($company['darbo_laikas'])): ?>
                <dt><?php echo e(trans('label_working_hours')); ?></dt>
                <dd><?php echo nl2br(e($company['darbo_laikas'])); ?></dd>
            <?php endif; ?>

            <?php if (!empty($company['banko_pavadinimas'])): ?>
                <dt><?php echo e(trans('label_bank_name')); ?></dt>
                <dd><?php echo e($company['banko_pavadinimas']); ?></dd>
            <?php endif; ?>

            <?php if (!empty($company['banko_saskaita'])): ?>
                <dt><?php echo e(trans('label_bank_account')); ?></dt>
                <dd><?php echo e($company['banko_saskaita']); ?></dd>
            <?php endif; ?>

            <?php if (!empty($company['pastabos'])): ?>
                <dt><?php echo e(trans('label_notes')); ?></dt>
                <dd><?php echo nl2br(e($company['pastabos'])); ?></dd>
            <?php endif; ?>
        </dl>
    </div>

    <div class="company-view-actions">
        <a href="<?php echo url('companies'); ?>" class="button button-outline">
            <span class="material-icons">arrow_back</span> <?php echo e(trans('back_to_company_list_button')); ?>
        </a>
        <?php if ($auth->isAdmin()): ?>
            <a href="<?php echo url('companies', 'edit', $company['id']); ?>" class="button">
                <span class="material-icons">edit</span> <?php echo e(trans('edit_company_button')); ?>
            </a>
            <a href="<?php echo url('companies', 'delete', $company['id']); ?>" class="button button-danger">
                <span class="material-icons">delete</span> <?php echo e(trans('delete_company_button')); ?>
            </a>
        <?php endif; ?>
    </div>
</div>
