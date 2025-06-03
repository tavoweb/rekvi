<?php
// templates/companies/form.php
$company = $view_data['company'] ?? null;
$errors = $view_data['errors'] ?? [];
$is_edit = (bool)$company;
$form_action_url = $is_edit && isset($company['id']) ? url('companies', 'edit', (int)$company['id']) : url('companies', 'create');

// Kelias iki logotipų katalogo peržiūrai
$logos_dir_url = 'uploads/logos/'; // Santykinis kelias iš public/ katalogo
?>

<h2><?php echo $is_edit ? 'Redaguoti Įmonės Rekvizitus' : 'Pridėti Naują Įmonę'; ?></h2>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-danger"><?php echo e($errors['general']); ?></div>
<?php endif; ?>

<form action="<?php echo $form_action_url; ?>" method="POST" enctype="multipart/form-data">
    <fieldset>
        <legend>Pagrindinė informacija</legend>
        <div class="form-group">
            <label for="pavadinimas">Pavadinimas: *</label>
            <input type="text" name="pavadinimas" id="pavadinimas" value="<?php echo e($company['pavadinimas'] ?? $_POST['pavadinimas'] ?? ''); ?>" required>
            <?php if (isset($errors['pavadinimas'])): ?><span class="form-error"><?php echo e($errors['pavadinimas']); ?></span><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="imones_kodas">Įmonės kodas: *</label>
            <input type="text" name="imones_kodas" id="imones_kodas" value="<?php echo e($company['imones_kodas'] ?? $_POST['imones_kodas'] ?? ''); ?>" required>
            <?php if (isset($errors['imones_kodas'])): ?><span class="form-error"><?php echo e($errors['imones_kodas']); ?></span><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="pvm_kodas">PVM kodas:</label>
            <input type="text" name="pvm_kodas" id="pvm_kodas" value="<?php echo e($company['pvm_kodas'] ?? $_POST['pvm_kodas'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="vadovas_vardas_pavarde">Vadovo vardas, pavardė:</label>
            <input type="text" name="vadovas_vardas_pavarde" id="vadovas_vardas_pavarde" value="<?php echo e($company['vadovas_vardas_pavarde'] ?? $_POST['vadovas_vardas_pavarde'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="tinklalapis">Tinklalapio adresas (pvz., https://www.imone.lt):</label>
            <input type="url" name="tinklalapis" id="tinklalapis" placeholder="https://www.imone.lt" value="<?php echo e($company['tinklalapis'] ?? $_POST['tinklalapis'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="darbo_laikas">Darbo laikas (pvz., I-V 9:00-18:00):</label>
            <textarea name="darbo_laikas" id="darbo_laikas"><?php echo e($company['darbo_laikas'] ?? $_POST['darbo_laikas'] ?? ''); ?></textarea>
        </div>
    </fieldset>

    <fieldset>
        <legend>Logotipas</legend>
        <div class="form-group">
            <label for="logotipas">Įmonės logotipas (JPG, PNG, GIF, maks. 2MB):</label>
            <input type="file" name="logotipas" id="logotipas" accept="image/jpeg,image/png,image/gif">
            <?php if ($is_edit && !empty($company['logotipas'])): ?>
                <p><strong>Dabartinis logotipas:</strong><br>
                    <img src="<?php echo $logos_dir_url . e($company['logotipas']); ?>" alt="<?php echo e($company['pavadinimas']); ?> logotipas" style="max-width: 150px; max-height: 100px; margin-top: 10px;">
                    <br><input type="checkbox" name="remove_logo" id="remove_logo" value="1"> <label for="remove_logo">Pašalinti dabartinį logotipą</label>
                </p>
            <?php endif; ?>
            <?php if (isset($errors['logotipas'])): ?><span class="form-error"><?php echo e($errors['logotipas']); ?></span><?php endif; ?>
        </div>
    </fieldset>

    <fieldset>
        <legend>Adresas</legend>
        <div class="form-group">
            <label for="adresas_salis">Šalis:</label>
            <input type="text" name="adresas_salis" id="adresas_salis" value="<?php echo e($company['adresas_salis'] ?? $_POST['adresas_salis'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="adresas_miestas">Miestas:</label>
            <input type="text" name="adresas_miestas" id="adresas_miestas" value="<?php echo e($company['adresas_miestas'] ?? $_POST['adresas_miestas'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="adresas_gatve">Gatvė, namo nr.:</label>
            <input type="text" name="adresas_gatve" id="adresas_gatve" value="<?php echo e($company['adresas_gatve'] ?? $_POST['adresas_gatve'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="adresas_pasto_kodas">Pašto kodas:</label>
            <input type="text" name="adresas_pasto_kodas" id="adresas_pasto_kodas" value="<?php echo e($company['adresas_pasto_kodas'] ?? $_POST['adresas_pasto_kodas'] ?? ''); ?>">
        </div>
    </fieldset>

    <fieldset>
        <legend>Kontaktinė ir banko informacija</legend>
        <div class="form-group">
            <label for="telefonas">Telefonas:</label>
            <input type="tel" name="telefonas" id="telefonas" value="<?php echo e($company['telefonas'] ?? $_POST['telefonas'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="el_pastas">El. paštas:</label>
            <input type="email" name="el_pastas" id="el_pastas" value="<?php echo e($company['el_pastas'] ?? $_POST['el_pastas'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="kontaktinis_asmuo">Kontaktinis asmuo:</label>
            <input type="text" name="kontaktinis_asmuo" id="kontaktinis_asmuo" value="<?php echo e($company['kontaktinis_asmuo'] ?? $_POST['kontaktinis_asmuo'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="banko_pavadinimas">Banko pavadinimas:</label>
            <input type="text" name="banko_pavadinimas" id="banko_pavadinimas" value="<?php echo e($company['banko_pavadinimas'] ?? $_POST['banko_pavadinimas'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="banko_saskaita">Banko sąskaita (IBAN):</label>
            <input type="text" name="banko_saskaita" id="banko_saskaita" value="<?php echo e($company['banko_saskaita'] ?? $_POST['banko_saskaita'] ?? ''); ?>">
        </div>
    </fieldset>
    
    <fieldset>
        <legend>Kita informacija</legend>
        <div class="form-group">
            <label for="pastabos">Pastabos:</label>
            <textarea name="pastabos" id="pastabos"><?php echo e($company['pastabos'] ?? $_POST['pastabos'] ?? ''); ?></textarea>
        </div>
    </fieldset>

    <div class="form-actions">
        <button type="submit" class="button button-primary"><?php echo $is_edit ? 'Atnaujinti' : 'Pridėti'; ?></button>
        <a href="<?php echo url('companies'); ?>" class="button button-secondary">Atšaukti</a>
    </div>
</form>