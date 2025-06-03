<?php include __DIR__ . '/../layout/header.php'; ?>

<h1>Pridėti Naują Įmonę</h1>
<?php if (isset($errorMessage)): ?>
    <p style="color:red;"><?php echo htmlspecialchars($errorMessage); ?></p>
<?php endif; ?>

<form action="index.php?page=companies&action=create" method="POST">
    <div>
        <label for="pavadinimas">Pavadinimas: *</label>
        <input type="text" id="pavadinimas" name="pavadinimas" required>
    </div>
    <div>
        <label for="imones_kodas">Įmonės kodas: *</label>
        <input type="text" id="imones_kodas" name="imones_kodas" required>
    </div>
    <div>
        <label for="pvm_kodas">PVM kodas:</label>
        <input type="text" id="pvm_kodas" name="pvm_kodas">
    </div>
    <div>
        <label for="adresas_salis">Šalis:</label>
        <input type="text" id="adresas_salis" name="adresas_salis">
    </div>
    <div>
        <label for="adresas_miestas">Miestas:</label>
        <input type="text" id="adresas_miestas" name="adresas_miestas">
    </div>
    <div>
        <label for="adresas_gatve">Gatvė, namo nr.:</label>
        <input type="text" id="adresas_gatve" name="adresas_gatve">
    </div>
    <div>
        <label for="adresas_pasto_kodas">Pašto kodas:</label>
        <input type="text" id="adresas_pasto_kodas" name="adresas_pasto_kodas">
    </div>
    <div>
        <label for="telefonas">Telefonas:</label>
        <input type="tel" id="telefonas" name="telefonas">
    </div>
    <div>
        <label for="el_pastas">El. paštas:</label>
        <input type="email" id="el_pastas" name="el_pastas">
    </div>
    <div>
        <label for="banko_pavadinimas">Banko pavadinimas:</label>
        <input type="text" id="banko_pavadinimas" name="banko_pavadinimas">
    </div>
    <div>
        <label for="banko_saskaita">Banko sąskaita (IBAN):</label>
        <input type="text" id="banko_saskaita" name="banko_saskaita">
    </div>
     <div>
        <label for="kontaktinis_asmuo">Kontaktinis asmuo:</label>
        <input type="text" id="kontaktinis_asmuo" name="kontaktinis_asmuo">
    </div>
    <div>
        <label for="pastabos">Pastabos:</label>
        <textarea id="pastabos" name="pastabos"></textarea>
    </div>
    <button type="submit">Pridėti</button>
</form>

<?php include __DIR__ . '/../layout/footer.php'; ?>