<?php include __DIR__ . '/../layout/header.php'; // Gali reikėti modifikuoti headerį, kad nerodytų navigacijos neprisijungusiam ?>
<h1>Prisijungimas</h1>
<?php if (isset($loginError)): ?>
    <p style="color:red;"><?php echo htmlspecialchars($loginError); ?></p>
<?php endif; ?>
<form action="index.php?page=login" method="POST">
    <div>
        <label for="username">Vartotojo vardas arba el. paštas:</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div>
        <label for="password">Slaptažodis:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <button type="submit">Prisijungti</button>
</form>
<?php include __DIR__ . '/../layout/footer.php'; ?>