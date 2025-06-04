<?php
// templates/admin/users_list.php
$users = $view_data['users'] ?? [];
$view_data['meta_title'] = trans('users_list_meta_title');
?>

<h1><?php echo e(trans('user_management')); ?></h1> <?php // Reusing existing key ?>

<?php if (empty($users)): ?>
    <p><?php echo e(trans('no_users_found')); ?></p>
<?php else: ?>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th><?php echo e(trans('user_id_header')); ?></th>
                    <th><?php echo e(trans('user_username_header')); ?></th>
                    <th><?php echo e(trans('user_email_header')); ?></th>
                    <th><?php echo e(trans('user_role_header')); ?></th>
                    <th><?php echo e(trans('user_registration_date_header')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo e($user['id']); ?></td>
                        <td><?php echo e($user['username']); ?></td>
                        <td><?php echo e($user['email']); ?></td>
                        <td><?php echo e($user['role']); ?></td>
                        <td><?php echo e($user['created_at']); ?></td> <?php // Assuming 'created_at' is the registration date column ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
