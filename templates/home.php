<?php
// templates/home.php
// $isLoggedIn ir $username perduodami iš index.php
$isLoggedIn = $view_data['isLoggedIn'] ?? false;
$username = $view_data['username'] ?? null;
?>
<h1>Sveiki atvykę į Rekvizitų Valdymo Sistemą!</h1>
    <div class="home-search-container">
        <h2>Ieškoti įmonės</h2>
        <form action="<?php echo url('companies'); ?>" method="GET" id="home-search-form">
            <input type="text" name="search_query" id="home-search-input" class="form-control large-input" placeholder="Įveskite įmonės pavadinimą ar kodą..." value="<?php echo isset($_GET['search_query']) ? e($_GET['search_query']) : ''; ?>">
            <button type="submit" class="button button-primary">Ieškoti</button>
        </form>
        <div id="home-search-suggestions-container" class="search-suggestions-container" style="display: none;"></div>
    </div>
<p>Tai paprasta sistema, skirta tvarkyti įmonių rekvizitus.</p>
<?php if ($isLoggedIn): ?>
    <p>Esate prisijungęs kaip <?php echo e($username); ?>. Galite <a href="index.php?page=companies">peržiūrėti įmones</a>.</p>
<?php else: ?>
    <p>Norėdami pradėti, prašome <a href="index.php?page=login">prisijungti</a> arba <a href="index.php?page=register">užsiregistruoti</a>.</p>
<?php endif; ?>

<p>Esate Kaune, Lietuvoje.</p>
<p>Dabartinė data ir laikas: <?php echo date('Y-m-d H:i:s'); ?> EEST.</p>