/**
 * assets/js/app.js
 * Shared vanilla JS behavior used across all pages.
 */

document.addEventListener('DOMContentLoaded', function () {

    // ------------------------------------------------------------
    // Mobile sidebar toggle
    // ------------------------------------------------------------
    const sidebar = document.getElementById('appSidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const backdrop = document.getElementById('sidebarBackdrop');

    function openSidebar() {
        sidebar?.classList.add('show');
        backdrop?.classList.add('show');
    }

    function closeSidebar() {
        sidebar?.classList.remove('show');
        backdrop?.classList.remove('show');
    }

    toggleBtn?.addEventListener('click', function () {
        if (sidebar?.classList.contains('show')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });

    backdrop?.addEventListener('click', closeSidebar);

    // Close sidebar automatically when a nav link is clicked (mobile)
    document.querySelectorAll('.app-sidebar .nav-link').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth < 992) {
                closeSidebar();
            }
        });
    });

    // ------------------------------------------------------------
    // Auto-dismiss alerts after a few seconds
    // ------------------------------------------------------------
    document.querySelectorAll('.alert').forEach(function (alertEl) {
        setTimeout(function () {
            const alert = bootstrap.Alert.getOrCreateInstance(alertEl);
            alert?.close();
        }, 5000);
    });

    // ------------------------------------------------------------
    // Enable Bootstrap tooltips globally, if any are present
    // ------------------------------------------------------------
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        new bootstrap.Tooltip(el);
    });
});
