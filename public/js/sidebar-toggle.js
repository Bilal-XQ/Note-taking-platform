// Sidebar Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('backdrop');
    const main = document.querySelector('.main');
    const body = document.body;
    
    // Function to toggle sidebar
    function toggleSidebar() {
        // Toggle the collapsed class on the sidebar
        sidebar.classList.toggle('sidebar--collapsed');
        
        // Toggle the expanded class on the main content
        main.classList.toggle('main--expanded');
        
        // Toggle the collapsed-sidebar class on the body for potential global styling
        body.classList.toggle('collapsed-sidebar');
        
        // For mobile: toggle the sidebar--open class and backdrop--visible class
        if (window.innerWidth < 992) {
            sidebar.classList.toggle('sidebar--open');
            backdrop.classList.toggle('backdrop--visible');
        }
    }
    
    // Add click event to menu toggle button
    if (menuToggle) {
        menuToggle.addEventListener('click', toggleSidebar);
    }
    
    // Close sidebar when clicking on backdrop (mobile only)
    if (backdrop) {
        backdrop.addEventListener('click', function() {
            sidebar.classList.remove('sidebar--open');
            backdrop.classList.remove('backdrop--visible');
            
            // Also remove the collapsed classes if they were added
            if (window.innerWidth < 992) {
                sidebar.classList.remove('sidebar--collapsed');
                main.classList.remove('main--expanded');
                body.classList.remove('collapsed-sidebar');
            }
        });
    }
    
    // Handle window resize
    window.addEventListener('resize', function() {
        // If window is resized to desktop size and sidebar was hidden on mobile
        if (window.innerWidth >= 992 && sidebar.classList.contains('sidebar--open')) {
            sidebar.classList.remove('sidebar--open');
            backdrop.classList.remove('backdrop--visible');
        }
    });
});