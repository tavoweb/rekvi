<?php
// templates/companies/index.php
$companies = $view_data['companies'] ?? [];
$search_query_active = $view_data['search_query_active'] ?? null;
$auth = $view_data['auth']; // Assuming auth is always passed

// Set meta title
$view_data['meta_title'] = trans('companies_index_meta_title');
?>

<h1>
    <?php if ($search_query_active): ?>
        <?php echo e(trans('search_results_for', ['query' => $search_query_active])); ?>
    <?php else: ?>
        <?php echo e(trans('companies_list_title')); ?>
    <?php endif; ?>
</h1>

<?php if (empty($companies)): ?>
    <p><?php echo e($search_query_active ? trans('no_companies_found_matching_search') : trans('no_companies_found')); ?></p>
<?php else: ?>
    <div class="table-responsive">
        <table class="data-table" id="companies-table">
            <thead>
                <tr>
                    <th><?php echo e(trans('company_name_header')); ?></th>
                    <th><?php echo e(trans('company_code_header')); ?></th>
                    <th><?php echo e(trans('company_pvm_code_header')); ?></th>
                    <th><?php echo e(trans('company_address_header')); ?></th>
                    <th><?php echo e(trans('company_actions_header')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($companies as $company): ?>
                    <tr>
                        <td><?php echo e($company['pavadinimas']); ?></td>
                        <td><?php echo e($company['imones_kodas']); ?></td>
                        <td><?php echo e($company['pvm_kodas'] ?? ''); ?></td>
                        <td>
                            <?php
                            $address_parts = array_filter([
                                $company['adresas_gatve'] ?? null,
                                $company['adresas_miestas'] ?? null,
                                $company['adresas_pasto_kodas'] ?? null,
                                $company['adresas_salis'] ?? null,
                            ]);
                            echo e(implode(', ', $address_parts));
                            ?>
                        </td>
                        <td class="actions-cell">
                            <a href="<?php echo url('companies', 'view', $company['id']); ?>" class="button button-small button-outline"><?php echo e(trans('view_action')); ?></a>
                            <?php if ($auth->isAdmin()): ?>
                                <a href="<?php echo url('companies', 'edit', $company['id']); ?>" class="button button-small"><?php echo e(trans('edit_action')); ?></a>
                                <a href="<?php echo url('companies', 'delete', $company['id']); ?>" class="button button-small button-danger"><?php echo e(trans('delete_action')); ?></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <button id="load-more-companies" class="button" data-page="1" data-search-query="<?php echo e($search_query_active ?? ''); ?>"><?php echo e(trans('load_more_companies_button')); ?></button>
<?php endif; ?>