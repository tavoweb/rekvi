<?php
// templates/companies/index.php
$companies = $view_data['companies'] ?? [];
$search_query_active = $view_data['search_query_active'] ?? null;
/** @var Auth $auth */ // подсказка IDE
$auth = $view_data['auth']; // $auth objektas perduodamas iš index.php
$isAdmin = $auth->isAdmin();

$logos_dir_url = LOGO_UPLOAD_DIR_PUBLIC; // Naudojame konstantą iš index.php
?>
<h2>Įmonių Rekvizitai</h2>

<p style="margin-bottom: 20px;">
    <a href="<?php echo url('companies', 'create'); ?>" class="button button-primary" style="margin-right: 10px;">Pridėti Naują Įmonę</a>
    <?php if ($isAdmin): // Importavimo mygtukas matomas tik administratoriams ?>
    <a href="<?php echo url('companies', 'import'); ?>" class="button button-secondary">Importuoti Įmones (CSV)</a>
    <?php endif; ?>
</p>

<?php if ($search_query_active): ?>
    <div class="alert alert-info">
        Rodomi paieškos rezultatai pagal: "<strong><?php echo e($search_query_active); ?></strong>". <a href="<?php echo url('companies'); ?>">Rodyti visas įmones</a>.
    </div>
<?php endif; ?>

<?php if (empty($companies)): ?>
    <p>Įmonių nerasta.
    <?php if ($isAdmin): // Pasiūlymas pridėti matomas tik administratoriams ?>
        Galite <a href="<?php echo url('companies', 'create'); ?>">pridėti pirmąją</a>.
    <?php endif; ?>
    </p>
<?php else: ?>
    <div class="table-responsive-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Logotipas</th>
                    <th>Pavadinimas</th>
                    <th>Įmonės Kodas</th>
                    <th>PVM Kodas</th>
                    <?php if ($isAdmin): // Veiksmų stulpelis matomas tik administratoriams ?>
                    <th>Veiksmai</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($companies as $company): ?>
                <tr>
                    <td>
                        <?php if (!empty($company['logotipas'])): ?>
                            <a href="<?php echo url('companies', 'view', (int)$company['id']); ?>">
                                <img src="<?php echo $logos_dir_url . e($company['logotipas']); ?>" alt="<?php echo e($company['pavadinimas']); ?>" class="logo-thumbnail">
                            </a>
                        <?php else: ?>
                            <div class="logo-thumbnail-placeholder">[Nėra]</div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?php echo url('companies', 'view', (int)$company['id']); ?>">
                            <?php echo e($company['pavadinimas']); ?>
                        </a>
                    </td>
                    <td><?php echo e($company['imones_kodas']); ?></td>
                    <td><?php echo e($company['pvm_kodas'] ?? '-'); ?></td>
                    <?php if ($isAdmin): // Veiksmai matomi tik administratoriams ?>
                    <td>
                        <a href="<?php echo url('companies', 'edit', (int)$company['id']); ?>" class="button button-small">Redaguoti</a>
                        <a href="<?php echo url('companies', 'delete', (int)$company['id']); ?>" class="button button-small button-danger">Trinti</a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>