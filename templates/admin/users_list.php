<?php
// templates/admin/users_list.php
/** @var array $view_data */
$users = $view_data['users'] ?? [];
?>

<div class="container">
    <h1>Administratoriaus Panelė - Vartotojų Sąrašas</h1>

    <?php if (empty($users)): ?>
        <p>Registruotų vartotojų nėra.</p>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vartotojo Vardas</th>
                    <th>El. Paštas</th>
                    <th>Rolė</th>
                    <th>Registracijos Data</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo e($user['id']); ?></td>
                        <td><?php echo e($user['vartotojo_vardas']); ?></td>
                        <td><?php echo e($user['el_pastas']); ?></td>
                        <td><?php echo e($user['role']); ?></td>
                        <td><?php echo e(isset($user['registracijos_data']) ? date('Y-m-d H:i:s', strtotime($user['registracijos_data'])) : 'N/A'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p><a href="<?php echo url('home'); // Or a link to an admin dashboard if one exists ?>" class="button">Grįžti į Pagrindinį</a></p>
</div>
