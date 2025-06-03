document.addEventListener('DOMContentLoaded', function() {
    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            const context = this;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), delay);
        };
    }

    // Get elements
    const sidebarToggles = document.querySelectorAll('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    const sidebarOverlay = document.querySelector('.sidebar-overlay');
    const body = document.body;
    
    // Toggle sidebar function
    function toggleSidebar() {
        sidebar.classList.toggle('open');
        body.classList.toggle('sidebar-open-overlay');
        
        // Toggle overlay
        if (sidebarOverlay) {
            sidebarOverlay.classList.toggle('active');
        }
        
        // Toggle active class on all toggle buttons
        sidebarToggles.forEach(toggle => {
            toggle.classList.toggle('active');
        });
    }
    
    // Add click event listener to all toggle buttons
    sidebarToggles.forEach(toggle => {
        toggle.addEventListener('click', toggleSidebar);
    });
    
    // Close sidebar when clicking on overlay
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            if (sidebar.classList.contains('open')) {
                toggleSidebar();
            }
        });
    }
    
    // Optional: Close sidebar when clicking on links (uncomment if needed)
    // const sidebarLinks = document.querySelectorAll('.sidebar-nav a');
    // sidebarLinks.forEach(link => {
    //     link.addEventListener('click', () => {
    //         if (sidebar.classList.contains('open') && window.innerWidth < 992) {
    //             toggleSidebar();
    //         }
    //     });
    // });

    // Search suggestions logic
    const searchInput = document.getElementById('search-input');
    const suggestionsContainer = document.getElementById('search-suggestions-container');

    if (searchInput && suggestionsContainer) {
        searchInput.addEventListener('input', debounce(async function() {
            const query = searchInput.value.trim();
            suggestionsContainer.innerHTML = ''; // Clear previous suggestions

            if (query.length > 1) {
                try {
                    const response = await fetch(`companies/search_suggestions?query=${encodeURIComponent(query)}`);
                    if (!response.ok) {
                        // Handle HTTP errors, e.g., response.status
                        console.error('Search suggestions fetch error:', response.status);
                        return;
                    }
                    const data = await response.json();

                    if (data && data.length > 0) {
                        data.forEach(suggestion => {
                            const a = document.createElement('a');
                            a.textContent = suggestion.pavadinimas;
                            a.href = '#'; // Prevent navigation, action handled by click listener
                            a.classList.add('suggestion-item'); // For styling

                            a.addEventListener('click', function(e) {
                                e.preventDefault(); // Prevent default anchor action
                                searchInput.value = suggestion.pavadinimas;
                                suggestionsContainer.innerHTML = '';
                                if (searchInput.form) {
                                    searchInput.form.submit();
                                }
                            });
                            suggestionsContainer.appendChild(a);
                        });
                    } else {
                        // No suggestions found, could display a message if desired
                        // suggestionsContainer.innerHTML = '<div class="suggestion-item-none">No suggestions found.</div>';
                    }
                } catch (error) {
                    console.error('Error fetching search suggestions:', error);
                    // Optionally display an error message in the suggestionsContainer
                }
            }
        }, 300));

        // Handle clicking outside to close suggestions
        document.addEventListener('click', function(event) {
            if (!suggestionsContainer.contains(event.target) && event.target !== searchInput) {
                suggestionsContainer.innerHTML = '';
            }
        });
    }
});