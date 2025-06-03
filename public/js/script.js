document.addEventListener('DOMContentLoaded', function() {
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
});