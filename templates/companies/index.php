<?php
// templates/companies/index.php
$companies = $view_data['companies'] ?? [];
$search_query_active = $view_data['search_query_active'] ?? null;
/** @var Auth $auth */ // подсказка IDE
$auth = $view_data['auth']; // $auth objektas perduodamas iš index.php
$isAdmin = $auth->isAdmin();

$logos_dir_url = LOGO_UPLOAD_DIR_PUBLIC; // Naudojame konstantą iš index.php
?>
<h2>Įmonių Rekvizitai</h2>

<p style="margin-bottom: 20px;">
    <a href="<?php echo url('companies', 'create'); ?>" class="button button-primary" style="margin-right: 10px;">Pridėti Naują Įmonę</a>
    <?php if ($isAdmin): // Importavimo mygtukas matomas tik administratoriams ?>
    <a href="<?php echo url('companies', 'import'); ?>" class="button button-secondary">Importuoti Įmones (CSV)</a>
    <?php endif; ?>
</p>

<?php if ($search_query_active): ?>
    <div class="alert alert-info">
        Rodomi paieškos rezultatai pagal: "<strong><?php echo e($search_query_active); ?></strong>". <a href="<?php echo url('companies'); ?>">Rodyti visas įmones</a>.
    </div>
<?php endif; ?>

<?php if (empty($companies)): ?>
    <p>Įmonių nerasta.
    <?php if ($isAdmin): // Pasiūlymas pridėti matomas tik administratoriams ?>
        Galite <a href="<?php echo url('companies', 'create'); ?>">pridėti pirmąją</a>.
    <?php endif; ?>
    </p>
<?php else: ?>
    <div class="table-responsive-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Logotipas</th>
                    <th>Pavadinimas</th>
                    <th>Įmonės Kodas</th>
                    <th>PVM Kodas</th>
                    <?php if ($isAdmin): // Veiksmų stulpelis matomas tik administratoriams ?>
                    <th>Veiksmai</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody id="companies-tbody">
                <?php foreach ($companies as $company): ?>
                <tr>
                    <td>
                        <?php if (!empty($company['logotipas'])): ?>
                            <a href="<?php echo url('companies', 'view', (int)$company['id']); ?>">
                                <img src="<?php echo $logos_dir_url . e($company['logotipas']); ?>" alt="<?php echo e($company['pavadinimas']); ?>" class="logo-thumbnail">
                            </a>
                        <?php else: ?>
                            <div class="logo-thumbnail-placeholder">[Nėra]</div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?php echo url('companies', 'view', (int)$company['id']); ?>">
                            <?php echo e($company['pavadinimas']); ?>
                        </a>
                    </td>
                    <td><?php echo e($company['imones_kodas']); ?></td>
                    <td><?php echo e($company['pvm_kodas'] ?? '-'); ?></td>
                    <?php if ($isAdmin): // Veiksmai matomi tik administratoriams ?>
                    <td>
                        <a href="<?php echo url('companies', 'edit', (int)$company['id']); ?>" class="button button-small">Redaguoti</a>
                        <a href="<?php echo url('companies', 'delete', (int)$company['id']); ?>" class="button button-small button-danger">Trinti</a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<div id="loading-indicator" style="display: none; text-align: center; padding: 20px;">
    <p>Kraunama daugiau įmonių...</p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const companiesTbody = document.getElementById('companies-tbody');
    const loadingIndicator = document.getElementById('loading-indicator');
    let currentPage = 1;
    let isLoading = false;
    const isAdmin = <?php echo json_encode($isAdmin); ?>;
    const logosDirUrl = <?php echo json_encode($logos_dir_url); ?>;
    const currentSearchTerm = <?php echo json_encode($search_query_active ?? null); ?>;
    // Function to generate URL (approximating the PHP url() function)
    function siteUrl(controller, action = '', id = 0) {
        let path = `companies/route=${controller}`;
        if (action) {
            path += `&action=${action}`;
        }
        if (id) {
            path += `&id=${id}`;
        }
        return path; // Adjust this base path if your URL structure is different (e.g., using mod_rewrite)
    }

    // Function to escape HTML (approximating PHP e() function)
    function escapeHtml(unsafe) {
        if (unsafe === null || typeof unsafe === 'undefined') {
            return '';
        }
        return unsafe
             .toString()
             .replace(/&/g, "&amp;")
             .replace(/</g, "&lt;")
             .replace(/>/g, "&gt;")
             .replace(/"/g, "&quot;")
             .replace(/'/g, "&#039;");
    }

    // Debounce function
    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            const context = this;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), delay);
        };
    }

    function loadMoreCompanies() {
        if (isLoading) {
            return;
        }
        isLoading = true;
        currentPage++;
        if(loadingIndicator) loadingIndicator.style.display = 'block';

        // Corrected AJAX URL construction
        let ajaxUrl = `companies/route=companies&action=load_more_companies&page=${currentPage}&ajax=1`;
        if (currentSearchTerm) {
            ajaxUrl += `&search_query=${encodeURIComponent(currentSearchTerm)}`;
        }


        fetch(ajaxUrl)
            .then(response => response.json())
            .then(data => {
                if (data.companies && data.companies.length > 0) {
                    data.companies.forEach(company => {
                        const tr = document.createElement('tr');
                        let adminActionsHtml = '';
                        if (isAdmin) {
                            adminActionsHtml = `
                                <a href="${siteUrl('companies', 'edit', company.id)}" class="button button-small">Redaguoti</a>
                                <a href="${siteUrl('companies', 'delete', company.id)}" class="button button-small button-danger" onclick="return confirm('Ar tikrai norite ištrinti šią įmonę?');">Trinti</a>
                            `;
                        }

                        const logoHtml = company.logotipas
                            ? `<a href="${siteUrl('companies', 'view', company.id)}"><img src="${logosDirUrl + escapeHtml(company.logotipas)}" alt="${escapeHtml(company.pavadinimas)}" class="logo-thumbnail"></a>`
                            : `<div class="logo-thumbnail-placeholder">[Nėra]</div>`;

                        tr.innerHTML = `
                            <td>${logoHtml}</td>
                            <td><a href="${siteUrl('companies', 'view', company.id)}">${escapeHtml(company.pavadinimas)}</a></td>
                            <td>${escapeHtml(company.imones_kodas)}</td>
                            <td>${escapeHtml(company.pvm_kodas) || '-'}</td>
                            ${isAdmin ? `<td>${adminActionsHtml}</td>` : ''}
                        `;
                        companiesTbody.appendChild(tr);
                    });
                    if(loadingIndicator) loadingIndicator.style.display = 'none'; // Hide after successful load
                } else {
                    // No more companies to load
                    if(loadingIndicator) loadingIndicator.innerHTML = '<p>Daugiau įmonių nerasta.</p>';
                    window.removeEventListener('scroll', handleScroll); // Optional: remove listener
                }
            })
            .catch(error => {
                console.error('Error loading more companies:', error);
                if(loadingIndicator) loadingIndicator.innerHTML = '<p>Klaida kraunant įmones.</p>';
                // isLoading = false; // Moved to finally
            })
            .finally(() => {
                isLoading = false;
                // If loadingIndicator was set to display:block and no companies were loaded (or error),
                // it will remain visible with the appropriate message.
                // If companies were loaded, it's hidden inside the .then() block.
                // This logic seems fine.
                // If it was loading and then an error, it shows the error.
            });
    }

    function handleScroll() {
        // Window height + scrollY >= document height - threshold
        if ((window.innerHeight + window.scrollY) >= (document.documentElement.scrollHeight - 200)) {
            loadMoreCompanies();
        }
    }

    window.addEventListener('scroll', debounce(handleScroll, 250)); // Debounce scroll with 250ms delay

    // Initial check in case the content is too short to scroll
    // Or if there are fewer than 100 companies and no scrollbar appears.
    // However, we only want to load more if the initial set might not fill the page.
    // This initial load logic might need refinement based on how `load_more_companies` is structured.
    // For now, we assume the initial page (page 1) is loaded via PHP.
    // If the initial load has less than 100 (the limit), it implies no more companies.
    // The logic for this initial state is handled by PHP rendering the initial list.
    // The JS scroll listener will then fetch page 2 onwards.
});
</script>