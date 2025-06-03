<?php
// templates/companies/view.php
$company = $view_data['company'] ?? null;
/** @var Auth $auth */ // подсказка IDE
$auth = $view_data['auth'];
$isAdmin = $auth->isAdmin();

if (!$company) {
    echo "<p>Klaida: Įmonės duomenys nerasti.</p>";
    echo '<p><a href="' . url('companies') . '" class="button">Grįžti į sąrašą</a></p>';
    return;
}

$logos_dir_url = LOGO_UPLOAD_DIR_PUBLIC;
?>

<div class="company-profile-header">
    <?php if (!empty($company['logotipas'])): ?>
        <img src="<?php echo $logos_dir_url . e($company['logotipas']); ?>" alt="<?php echo e($company['pavadinimas']); ?> logotipas" class="company-logo-view">
    <?php else: ?>
        <div class="company-logo-placeholder-view">[Nėra logotipo]</div>
    <?php endif; ?>
    <h1><?php echo e($company['pavadinimas']); ?></h1>
</div>


<div class="company-profile-details">
    <div class="field-with-icon"><span class="material-icons">business</span><strong>Įmonės kodas:</strong> <?php echo e($company['imones_kodas']); ?></div>

    <?php if (!empty($company['pvm_kodas'])): ?>
    <div class="field-with-icon"><span class="material-icons">percent</span><strong>PVM kodas:</strong> <?php echo e($company['pvm_kodas']); ?></div>
    <?php endif; ?>

    <?php if (!empty($company['vadovas_vardas_pavarde'])): ?>
    <div class="field-with-icon"><span class="material-icons">person</span><strong>Vadovas:</strong> <?php echo e($company['vadovas_vardas_pavarde']); ?></div>
    <?php endif; ?>

    <?php if (!empty($company['tinklalapis'])): ?>
    <div class="field-with-icon"><span class="material-icons">language</span><strong>Tinklalapis:</strong> <a href="<?php echo e(strpos($company['tinklalapis'], 'http') !== 0 ? 'http://' . $company['tinklalapis'] : $company['tinklalapis']); ?>" target="_blank" rel="noopener noreferrer"><?php echo e($company['tinklalapis']); ?></a></div>
    <?php endif; ?>

    <?php if (!empty($company['darbo_laikas'])): ?>
    <div class="field-with-icon"><span class="material-icons">schedule</span><strong>Darbo laikas:</strong><br><?php echo nl2br(e($company['darbo_laikas'])); ?></div>
    <?php endif; ?>

    <h3>Adresas</h3>
    <p><span class="material-icons">place</span>
        <?php
        $address_parts = [];
        if (!empty($company['adresas_gatve'])) $address_parts[] = e($company['adresas_gatve']);
        if (!empty($company['adresas_miestas'])) $address_parts[] = e($company['adresas_miestas']);
        if (!empty($company['adresas_pasto_kodas'])) $address_parts[] = e($company['adresas_pasto_kodas']);
        if (!empty($company['adresas_salis'])) $address_parts[] = e($company['adresas_salis']);
        echo !empty($address_parts) ? implode(', ', $address_parts) : 'Nenurodyta';
        ?>
    </p>

    <h3>Kontaktinė informacija</h3>
    <div class="field-with-icon"><span class="material-icons">call</span><strong>Telefonas:</strong> <?php echo !empty($company['telefonas']) ? e($company['telefonas']) : '-'; ?></div>
    <div class="field-with-icon"><span class="material-icons">email</span><strong>El. paštas:</strong> <?php echo !empty($company['el_pastas']) ? e($company['el_pastas']) : '-'; ?></div>

    <?php if (!empty($company['kontaktinis_asmuo'])): ?>
    <div class="field-with-icon"><span class="material-icons">person_pin</span><strong>Kontaktinis asmuo:</strong> <?php echo e($company['kontaktinis_asmuo']); ?></div>
    <?php endif; ?>

    <h3>Banko informacija</h3>
    <div class="field-with-icon"><span class="material-icons">account_balance</span><strong>Banko pavadinimas:</strong> <?php echo !empty($company['banko_pavadinimas']) ? e($company['banko_pavadinimas']) : '-'; ?></div>
    <div class="field-with-icon"><span class="material-icons">payment</span><strong>Banko sąskaita (IBAN):</strong> <?php echo !empty($company['banko_saskaita']) ? e($company['banko_saskaita']) : '-'; ?></div>

    <?php if (!empty($company['pastabos'])): ?>
    <h3><span class="material-icons">note</span>Pastabos</h3>
    <p><?php echo nl2br(e($company['pastabos'])); ?></p>
    <?php endif; ?>

    <div class="field-with-icon meta-info"><span class="material-icons">today</span><strong>Duomenys sukurti:</strong> <?php echo e(date("Y-m-d H:i", strtotime($company['sukurimo_data']))); ?></div>
    <div class="field-with-icon meta-info"><span class="material-icons">update</span><strong>Paskutinis atnaujinimas:</strong> <?php echo e(date("Y-m-d H:i", strtotime($company['atnaujinimo_data']))); ?></div>

</div>

<div class="actions-bar">
    <a href="<?php echo url('companies'); ?>" class="button button-secondary">Grįžti į įmonių sąrašą</a>
    <?php if ($isAdmin): // Redagavimo mygtukas matomas tik administratoriams ?>
        <a href="<?php echo url('companies', 'edit', (int)$company['id']); ?>" class="button">Redaguoti šią įmonę</a>
    <?php endif; ?>
</div>
