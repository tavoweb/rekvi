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
});