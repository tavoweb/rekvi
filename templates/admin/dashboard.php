<?php
// templates/admin/dashboard.php

// Page title
$view_data['meta_title'] = 'Administratoriaus Skydelis';
?>

<h1>Administratoriaus Skydelis</h1>
<p>Sveiki atvykę į administratoriaus skydelį. Čia galite valdyti įvairias svetainės funkcijas.</p>

<div class="admin-actions">
    <p><a href="<?php echo url('admin', 'users'); ?>" class="button">Vartotojų Sąrašas</a></p>
    <p><a href="<?php echo url('companies', 'import'); ?>" class="button">Importuoti Įmones</a></p>
    <p><a href="<?php echo url('admin', 'sitemap'); ?>" class="button">Sitemap Generavimas</a></p>
</div>
