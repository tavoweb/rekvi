<?php include __DIR__ . '/../layout/header.php'; ?>

<h1>Redaguoti Įmonės Rekvizitus: <?php echo htmlspecialchars($company['pavadinimas'] ?? ''); ?></h1>

<?php if (isset($errorMessage)): ?>
    <p style="color:red;"><?php echo htmlspecialchars($errorMessage); ?></p>
<?php endif; ?>

<form action="index.php?page=companies&action=edit&id=<?php echo $company['id']; ?>" method="POST">
    <div>
        <label for="pavadinimas">Pavadinimas: *</label>
        <input type="text" id="pavadinimas" name="pavadinimas" value="<?php echo htmlspecialchars($company['pavadinimas'] ?? ''); ?>" required>
    </div>
    <div>
        <label for="imones_kodas">Įmonės kodas: *</label>
        <input type="text" id="imones_kodas" name="imones_kodas" value="<?php echo htmlspecialchars($company['imones_kodas'] ?? ''); ?>" required>
    </div>
    <div>
        <label for="pvm_kodas">PVM kodas:</label>
        <input type="text" id="pvm_kodas" name="pvm_kodas" value="<?php echo htmlspecialchars($company['pvm_kodas'] ?? ''); ?>">
    </div>
    <?php // ... Kiti laukai analogiškai ... ?>
    <div>
        <label for="adresas_salis">Šalis:</label>
        <input type="text" id="adresas_salis" name="adresas_salis" value="<?php echo htmlspecialchars($company['adresas_salis'] ?? ''); ?>">
    </div>
    <div>
        <label for="adresas_miestas">Miestas:</label>
        <input type="text" id="adresas_miestas" name="adresas_miestas" value="<?php echo htmlspecialchars($company['adresas_miestas'] ?? ''); ?>">
    </div>
    <div>
        <label for="adresas_gatve">Gatvė, namo nr.:</label>
        <input type="text" id="adresas_gatve" name="adresas_gatve" value="<?php echo htmlspecialchars($company['adresas_gatve'] ?? ''); ?>">
    </div>
    <div>
        <label for="adresas_pasto_kodas">Pašto kodas:</label>
        <input type="text" id="adresas_pasto_kodas" name="adresas_pasto_kodas" value="<?php echo htmlspecialchars($company['adresas_pasto_kodas'] ?? ''); ?>">
    </div>
    <div>
        <label for="telefonas">Telefonas:</label>
        <input type="tel" id="telefonas" name="telefonas" value="<?php echo htmlspecialchars($company['telefonas'] ?? ''); ?>">
    </div>
    <div>
        <label for="el_pastas">El. paštas:</label>
        <input type="email" id="el_pastas" name="el_pastas" value="<?php echo htmlspecialchars($company['el_pastas'] ?? ''); ?>">
    </div>
    <div>
        <label for="banko_pavadinimas">Banko pavadinimas:</label>
        <input type="text" id="banko_pavadinimas" name="banko_pavadinimas" value="<?php echo htmlspecialchars($company['banko_pavadinimas'] ?? ''); ?>">
    </div>
    <div>
        <label for="banko_saskaita">Banko sąskaita (IBAN):</label>
        <input type="text" id="banko_saskaita" name="banko_saskaita" value="<?php echo htmlspecialchars($company['banko_saskaita'] ?? ''); ?>">
    </div>
     <div>
        <label for="kontaktinis_asmuo">Kontaktinis asmuo:</label>
        <input type="text" id="kontaktinis_asmuo" name="kontaktinis_asmuo" value="<?php echo htmlspecialchars($company['kontaktinis_asmuo'] ?? ''); ?>">
    </div>
    <div>
        <label for="pastabos">Pastabos:</label>
        <textarea id="pastabos" name="pastabos"><?php echo htmlspecialchars($company['pastabos'] ?? ''); ?></textarea>
    </div>
    <button type="submit">Atnaujinti</button>
</form>

<?php include __DIR__ . '/../layout/footer.php'; ?>