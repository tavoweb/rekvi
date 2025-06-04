<?php
// templates/companies/delete_confirm.php
$company = $view_data['company'] ?? null;

if (!$company) {
    // Should be handled by redirect in index.php
    echo "<p>" . e(trans('no_companies_found')) . "</p>";
    return;
}
// Set meta title
$view_data['meta_title'] = trans('delete_company_meta_title_prefix') . ' ' . e($company['pavadinimas']) . ' - ' . trans('site_title');
?>

<div class="confirmation-container">
    <h1><?php echo e(trans('delete_confirmation_title_prefix')); ?> <?php echo e($company['pavadinimas']); ?></h1>
    <p class="warning-message"><?php echo e(trans('delete_confirmation_warning')); ?></p>

    <form action="<?php echo url('companies', 'delete_submit', $company['id']); ?>" method="POST" class="delete-confirm-form">
        <input type="hidden" name="confirm_delete" value="1">
        <div class="form-actions">
            <button type="submit" class="button button-danger"><?php echo e(trans('confirm_delete_button')); ?></button>
            <a href="<?php echo url('companies', 'view', $company['id']); ?>" class="button button-outline"><?php echo e(trans('cancel_button')); ?></a>
        </div>
    </form>
</div>