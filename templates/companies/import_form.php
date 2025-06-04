<?php
// templates/companies/import_form.php
$errors = $view_data['errors'] ?? [];
$import_results = $view_data['import_results'] ?? null;

// Set meta title
$view_data['meta_title'] = trans('import_companies_meta_title');
?>

<h1><?php echo e(trans('import_companies')); ?></h1> <?php // Reusing existing key ?>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-danger"><?php echo e($errors['general']); ?></div> <?php // Translate in index.php ?>
<?php endif; ?>

<form action="<?php echo url('companies', 'import'); ?>" method="POST" enctype="multipart/form-data" class="import-form">
    <div class="form-group">
        <label for="csv_file"><?php echo e(trans('select_csv_file_label')); ?></label>
        <input type="file" id="csv_file" name="csv_file" accept=".csv" required>
        <small><?php echo e(trans('csv_file_requirements')); ?></small><br>
        <small><?php echo e(trans('csv_column_requirements')); ?></small>
        <?php /* <br><a href="/path/to/template.csv" download><?php echo e(trans('download_csv_template_link_text')); ?></a> */ ?>
        <?php // Actual link to CSV template TBD if needed ?>
    </div>

    <button type="submit" class="button button-primary"><?php echo e(trans('upload_and_import_button')); ?></button>
</form>

<?php if ($import_results): ?>
    <div class="import-results-section">
        <h2><?php echo e(trans('import_results_title')); ?></h2>
        <p><?php echo e(trans('successfully_imported_count', ['count' => $import_results['success_count']])); ?></p>
        <?php if ($import_results['error_count'] > 0): ?>
            <p><?php echo e(trans('import_errors_count', ['count' => $import_results['error_count']])); ?></p>
            <h3><?php echo e(trans('import_error_details_title')); ?></h3>
            <div class="table-responsive">
                <table class="data-table import-errors-table">
                    <thead>
                        <tr>
                            <th><?php echo e(trans('row_number')); ?></th>
                            <th><?php echo e(trans('error_message')); ?></th>
                            <th><?php echo e(trans('original_data')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($import_results['error_details'] as $error_detail): ?>
                            <tr>
                                <td><?php echo e($error_detail['row']); ?></td>
                                <td><?php echo e($error_detail['message']); ?></td> <?php // Error messages from index.php, translate there ?>
                                <td><?php echo e(implode(', ', array_map('htmlspecialchars', (array)$error_detail['data']))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>