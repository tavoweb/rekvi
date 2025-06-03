<?php
// templates/companies/import_form.php
$errors = $view_data['errors'] ?? [];
$import_results = $view_data['import_results'] ?? null;

$expected_csv_headers = [
    'Pavadinimas', 'ImonesKodas', 'PVMKodas', 'VadovasVardasPavarde', 'Tinklalapis', 'DarboLaikas',
    'AdresasSalis', 'AdresasMiestas', 'AdresasGatve', 'AdresasPastoKodas',
    'Telefonas', 'ElPastas', 'KontaktinisAsmuo',
    'BankoPavadinimas', 'BankoSaskaita', 'Pastabos'
];
?>
<h2>Importuoti Įmones iš CSV Failo</h2>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-danger"><?php echo e($errors['general']); ?></div>
<?php endif; ?>

<?php if ($import_results): ?>
    <div class="alert alert-<?php echo $import_results['success_count'] > 0 && $import_results['error_count'] == 0 ? 'success' : ($import_results['error_count'] > 0 ? 'danger' : 'info'); ?>">
        <p>Importavimo rezultatai:</p>
        <ul>
            <li>Sėkmingai importuota: <?php echo $import_results['success_count']; ?> įmonių.</li>
            <li>Klaidų importuojant: <?php echo $import_results['error_count']; ?>.</li>
            <?php if (!empty($import_results['error_details'])): ?>
                <li>Klaidų detalės:
                    <ul>
                        <?php foreach ($import_results['error_details'] as $error_detail): ?>
                            <li>Eilutė <?php echo e($error_detail['row']); ?>: <?php echo e($error_detail['message']); ?> (Duomenys: <?php echo e(implode(', ', array_slice($error_detail['data'], 0, 3))); ?>...)</li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endif; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="index.php?page=companies&action=import" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label for="csv_file">Pasirinkite CSV failą importavimui:</label>
        <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
        <small>CSV failas turi turėti šiuos stulpelius (pirma eilutė - antraštės): <?php echo implode(', ', $expected_csv_headers); ?>. Logotipai nebus importuojami.</small>
    </div>
    <button type="submit" class="button button-primary">Importuoti</button>
    <a href="index.php?page=companies" class="button button-secondary">Atšaukti</a>
</form>