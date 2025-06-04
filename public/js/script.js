document.addEventListener('DOMContentLoaded', function() {
    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            const context = this;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), delay);
        };
    }

    // Sidebar toggle logic (remains unchanged)
    const sidebarToggles = document.querySelectorAll('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    const sidebarOverlay = document.querySelector('.sidebar-overlay');
    const body = document.body;
    
    function toggleSidebar() {
        sidebar.classList.toggle('open');
        body.classList.toggle('sidebar-open-overlay');
        if (sidebarOverlay) {
            sidebarOverlay.classList.toggle('active');
        }
        sidebarToggles.forEach(toggle => {
            toggle.classList.toggle('active');
        });
    }
    
    sidebarToggles.forEach(toggle => {
        toggle.addEventListener('click', toggleSidebar);
    });
    
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            if (sidebar.classList.contains('open')) {
                toggleSidebar();
            }
        });
    }

    // Generalized Search Suggestions Function
    // IMPORTANT: The suggestionsUrl parameter is expected to be the base path for suggestions,
    // for example, 'companies/search_suggestions'. The query parameter will be appended.
    function initializeSearchSuggestions(inputId, containerId, formId, suggestionsUrl) {
        const searchInput = document.getElementById(inputId);
        const suggestionsContainer = document.getElementById(containerId);
        const searchForm = formId ? document.getElementById(formId) : (searchInput ? searchInput.closest('form') : null);

        if (!searchInput || !suggestionsContainer) {
            // console.warn(`Search input or suggestions container not found for: ${inputId}, ${containerId}`);
            return;
        }

        searchInput.addEventListener('input', debounce(async function() {
            const query = searchInput.value.trim();
            suggestionsContainer.innerHTML = ''; // Clear previous suggestions

            if (query.length === 0) { // Hide suggestions if input is cleared
                suggestionsContainer.style.display = 'none';
                return;
            }

            if (query.length > 1) {
                try {
                    // Construct the full URL correctly
                    const fullSuggestionsUrl = `${suggestionsUrl}?query=${encodeURIComponent(query)}`;
                    const response = await fetch(fullSuggestionsUrl);

                    if (!response.ok) {
                        console.error('Search suggestions fetch error:', response.status, response.statusText);
                        suggestionsContainer.style.display = 'none';
                        return;
                    }
                    const data = await response.json();

                    if (data && data.length > 0) {
                        data.forEach(suggestion => {
                            const a = document.createElement('a');
                            a.textContent = suggestion.pavadinimas; // Assuming 'pavadinimas' is the field to display
                            a.href = '#'; // Prevent navigation initially
                            a.classList.add('suggestion-item');

                            a.addEventListener('click', function(e) {
                                e.preventDefault();
                                searchInput.value = suggestion.pavadinimas; // Or suggestion.id if needed for form
                                suggestionsContainer.innerHTML = '';
                                suggestionsContainer.style.display = 'none';
                                if (searchForm) {
                                    // If the form has a specific 'search_query' field, ensure it's set,
                                    // otherwise the input's own name attribute should handle it.
                                    const formQueryInput = searchForm.elements['search_query'];
                                    if (formQueryInput) {
                                        formQueryInput.value = suggestion.pavadinimas;
                                    }
                                    searchForm.submit();
                                } else {
                                    console.warn("Search form not found for input:", inputId);
                                }
                            });
                            suggestionsContainer.appendChild(a);
                        });
                        suggestionsContainer.style.display = 'block'; // Show suggestions
                    } else {
                        suggestionsContainer.style.display = 'none'; // Hide if no suggestions
                    }
                } catch (error) {
                    console.error('Error fetching search suggestions:', error);
                    suggestionsContainer.style.display = 'none';
                }
            } else {
                 suggestionsContainer.style.display = 'none'; // Hide if query too short
            }
        }, 300));

        // Handle clicking outside to close suggestions
        document.addEventListener('click', function(event) {
            if (!suggestionsContainer.contains(event.target) && event.target !== searchInput) {
                suggestionsContainer.innerHTML = '';
                suggestionsContainer.style.display = 'none';
            }
        });
         // Ensure the form associated with the search input exists for submission.
        if (searchInput && !searchForm) {
            // console.warn(`Search input ${inputId} does not have an associated form or the formId ${formId} was not found.`);
        }
    }

    // --- Instantiate Search Suggestions ---
    // The common base URL for suggestions.
    // Fetch will resolve this relative to the current page's URL.
    // e.g. if on domain.com/index.php?url=home, it becomes domain.com/companies/search_suggestions
    // e.g. if on domain.com/index.php?url=admin/users, it becomes domain.com/companies/search_suggestions
    // This relies on the server routing 'companies/search_suggestions' correctly from the application root.
    const COMMON_SUGGESTIONS_URL = 'companies/search_suggestions';

    // Initialize for sidebar search
    // The sidebar search form doesn't have an ID in the original HTML structure read from header.php
    // We will rely on searchInput.closest('form') within the function.
    // The input ID is 'search-input' and container is 'search-suggestions-container'.
    const sidebarSearchInput = document.getElementById('search-input');
    let sidebarFormId = null;
    if (sidebarSearchInput && sidebarSearchInput.form) {
        // If the form exists but has no ID, assign one dynamically for robustness if needed,
        // though closest('form') inside the function is generally preferred.
        if (!sidebarSearchInput.form.id) {
            sidebarSearchInput.form.id = 'sidebar-search-form-dynamic';
        }
        sidebarFormId = sidebarSearchInput.form.id;
    }
    initializeSearchSuggestions('search-input', 'search-suggestions-container', sidebarFormId, COMMON_SUGGESTIONS_URL);

    // Initialize for home page search
    initializeSearchSuggestions('home-search-input', 'home-search-suggestions-container', 'home-search-form', COMMON_SUGGESTIONS_URL);

    // Load More Companies Functionality
    const loadMoreButton = document.getElementById('load-more-companies');
    if (loadMoreButton) {
        const originalButtonText = loadMoreButton.textContent; // Store original translated text

        loadMoreButton.addEventListener('click', async function() {
            let currentPage = parseInt(this.dataset.page || '1');
            const searchQuery = this.dataset.searchQuery || '';
            const companiesTableBody = document.querySelector('#companies-table tbody');

            if (!companiesTableBody) {
                console.error('Companies table body not found.');
                return;
            }

            this.disabled = true;
            this.textContent = 'Loading...'; // Hardcoded for now, ideally translatable

            try {
                const nextPage = currentPage + 1;
                // Construct URL relative to the domain root.
                // Assumes SITE_BASE_URL is not needed here if paths are correctly handled by server routing.
                // If your AJAX URLs are absolute, you might need to pass SITE_BASE_URL to JS.
                let ajaxUrl = `/companies/load_more_companies?ajax=1&page=${nextPage}`; // Changed: added / at the beginning
                if (searchQuery) {
                    ajaxUrl += `&search_query=${encodeURIComponent(searchQuery)}`;
                }

                // The fetch URL needs to be correctly formed. If the site is in a subfolder,
                // or if url() helper in PHP generates full URLs, ensure this matches.
                // For simplicity, assuming the endpoint 'companies/load_more_companies' is resolvable.
                // If not, it might need to be prefixed, e.g. with window.location.origin or a base path.

                const response = await fetch(ajaxUrl);
                if (!response.ok) {
                    console.error('Error loading more companies:', response.status, response.statusText);
                    this.disabled = false;
                    this.textContent = originalButtonText;
                    // TODO: Show a user-friendly translated error message
                    return;
                }

                const data = await response.json();

                if (data.companies && data.companies.length > 0) {
                    data.companies.forEach(company => {
                        const row = companiesTableBody.insertRow();
                        row.insertCell().textContent = company.pavadinimas;
                        row.insertCell().textContent = company.imones_kodas;
                        row.insertCell().textContent = company.pvm_kodas || '';

                        let addressParts = [];
                        if (company.adresas_gatve) addressParts.push(company.adresas_gatve);
                        if (company.adresas_miestas) addressParts.push(company.adresas_miestas);
                        if (company.adresas_pasto_kodas) addressParts.push(company.adresas_pasto_kodas);
                        if (company.adresas_salis) addressParts.push(company.adresas_salis);
                        row.insertCell().textContent = addressParts.join(', ');

                        const actionsCell = row.insertCell();
                        actionsCell.classList.add('actions-cell');

                        // URLs for actions should be generated carefully.
                        // Assuming url() PHP helper structure for consistency.
                        // These might need to be built relative to a base path if not absolute.
                        let viewUrl = `/companies/view/${company.id}`; // Changed: added /
                        let editUrl = `/companies/edit/${company.id}`; // Changed: added /
                        let deleteUrl = `/companies/delete/${company.id}`; // Changed: added /

                        // For dynamic translated text in JS, it's best to get these from data attributes or pre-loaded JS vars.
                        // Here, using hardcoded English as placeholders, assuming `trans()` calls won't work directly in JS.
                        // A better approach would be to have these action links fully formed in the JSON response if they need to be dynamic per language.
                        // Or, pass `view_action_text`, `edit_action_text`, `delete_action_text` in `data` from PHP.
                        // For now, this is a simplification.
                        // NAUJAS: Naudoti išverstus tekstus iš data objekto
                        const textView = data.text_view || 'View'; // Fallback
                        const textEdit = data.text_edit || 'Edit'; // Fallback
                        const textDelete = data.text_delete || 'Delete'; // Fallback

                        let actionsHTML = `<a href="${viewUrl}" class="button button-small button-outline">${textView}</a>`;
                        if (data.isAdmin) {
                            actionsHTML += ` <a href="${editUrl}" class="button button-small">${textEdit}</a>`;
                            actionsHTML += ` <a href="${deleteUrl}" class="button button-small button-danger">${textDelete}</a>`;
                        }
                        actionsCell.innerHTML = actionsHTML;
                    });
                    this.dataset.page = nextPage;
                    this.disabled = false;
                    this.textContent = originalButtonText;
                } else {
                    this.textContent = data.no_more_companies_text || 'No more companies'; // Taip pat galima perduoti iš PHP
                    this.disabled = true;
                }
            } catch (error) {
                console.error('AJAX request failed:', error);
                this.disabled = false;
                this.textContent = originalButtonText;
                 // TODO: Show a user-friendly translated error message
            }
        });
    }
});