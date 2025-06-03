<?php
// templates/companies/delete_confirm.php
$company = $view_data['company'] ?? null;

// Jei $company kintamasis neperduotas arba tuščias, nukreipiame atgal
if (!$company) {
    // Galima nustatyti flash pranešimą prieš nukreipiant
    // set_flash_message('error_message', 'Klaida gaunant įmonės duomenis trynimui.');
    redirect('companies');
}
?>
<h2>Trinti Įmonę: <?php echo e($company['pavadinimas']); ?> (ID: <?php echo e($company['id']); ?>)</h2>
<p><strong>Ar tikrai norite negrįžtamai ištrinti šią įmonę ir visus jos duomenis?</strong></p>

<form action="<?php echo url('companies', 'delete_submit', (int)$company['id']); ?>" method="POST" style="display: inline-block; margin-right: 10px;">
    <input type="hidden" name="confirm_delete" value="1">
    <button type="submit" class="button button-danger">Taip, Trinti Įmonę</button>
</form>

<a href="<?php echo url('companies', 'view', (int)$company['id']); ?>" class="button button-secondary">Ne, Atšaukti</a>
<a href="<?php echo url('companies'); ?>" class="button button-outline" style="margin-left:10px;">Grįžti į sąrašą</a>