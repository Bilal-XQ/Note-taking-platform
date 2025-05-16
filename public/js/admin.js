/**
 * Admin Dashboard JavaScript
 * Handles sidebar toggle functionality and other interactive elements
 */

document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const adminContainer = document.querySelector('.admin');
    const menuToggleBtn = document.getElementById('menuToggle');
    const sidebarToggleBtn = document.getElementById('sidebarToggle');
    const backdrop = document.getElementById('backdrop');

    // Toggle sidebar on mobile
    function toggleMobileSidebar() {
        adminContainer.classList.toggle('admin--mobile-sidebar-open');
    }

    // Event listeners
    if (menuToggleBtn) {
        menuToggleBtn.addEventListener('click', toggleMobileSidebar);
    }

    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', toggleMobileSidebar);
    }

    if (backdrop) {
        backdrop.addEventListener('click', toggleMobileSidebar);
    }

    // Responsive behavior
    function handleResize() {
        if (window.innerWidth >= 768) {
            adminContainer.classList.remove('admin--mobile-sidebar-open');
        }
    }

    // Listen for window resize
    window.addEventListener('resize', handleResize);

    // Initialize any data or charts here in the future
    console.log('Admin dashboard loaded');
});
