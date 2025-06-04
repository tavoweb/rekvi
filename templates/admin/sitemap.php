<?php
// templates/admin/sitemap.php

// Page title
$view_data['meta_title'] = 'Sitemap Generavimas';

// Retrieve flash messages if any (passed from controller)
$success_message = get_flash_message('sitemap_success');
$error_message = get_flash_message('sitemap_error');

?>

<p><a href="<?php echo url('admin', 'dashboard'); ?>" class="button button-outline">&larr; Atgal į Administratoriaus Skydelį</a></p>

<h1>Sitemap Generavimas</h1>

<?php if ($success_message): ?>
    <div class="alert alert-success"><?php echo e($success_message); ?></div>
<?php endif; ?>

<?php if ($error_message): ?>
    <div class="alert alert-danger"><?php echo e($error_message); ?></div>
<?php endif; ?>

<p>Paspauskite mygtuką žemiau, kad sugeneruotumėte arba atnaujintumėte svetainės sitemap failus.</p>
<p>Sitemap failai padeda paieškos sistemoms geriau indeksuoti jūsų svetainės turinį.</p>

<form action="<?php echo url('admin', 'generate_sitemap_action'); ?>" method="POST">
    <button type="submit" class="button button-primary">Generuoti Sitemap</button>
</form>

<p style="margin-top: 20px;"><strong>Pastaba:</strong> Generavimo procesas gali užtrukti keletą akimirkų, priklausomai nuo svetainės dydžio.</p>
