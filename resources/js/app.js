import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Sidebar Toggle Functionality
window.toggleSidebar = function() {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    const mainContent = document.getElementById('mainContent');

    if (!sidebar || !backdrop || !mainContent) return;

    if (window.innerWidth < 1024) {
        // Mobile/Tablet: Toggle sidebar visibility from left
        const isHidden = sidebar.classList.contains('-translate-x-full');

        if (isHidden) {
            // Show sidebar
            sidebar.classList.remove('-translate-x-full');
            backdrop.classList.remove('hidden');
            // Force reflow
            void backdrop.offsetWidth;
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            document.body.style.overflow = 'hidden';
        } else {
            // Hide sidebar
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('opacity-0');
            backdrop.classList.remove('opacity-100');
            setTimeout(() => {
                backdrop.classList.add('hidden');
            }, 300);
            document.body.style.overflow = '';
        }
    } else {
        // Desktop: Toggle sidebar collapse/expand
        const isHidden = sidebar.classList.contains('-translate-x-full');

        if (isHidden) {
            // Show sidebar
            sidebar.classList.remove('-translate-x-full');
            mainContent.classList.remove('lg:ml-0');
            mainContent.classList.add('lg:ml-[230px]');
        } else {
            // Hide sidebar
            sidebar.classList.add('-translate-x-full');
            mainContent.classList.remove('lg:ml-[230px]');
            mainContent.classList.add('lg:ml-0');
        }
    }
};

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    const mainContent = document.getElementById('mainContent');

    // Initialize sidebar state based on screen size
    function initializeSidebar() {
        if (!sidebar || !mainContent) return;

        if (window.innerWidth >= 1024) {
            // Desktop: Show sidebar by default
            sidebar.classList.remove('-translate-x-full');
            mainContent.classList.add('lg:ml-[230px]');
        } else {
            // Mobile: Hide sidebar by default
            sidebar.classList.add('-translate-x-full');
            mainContent.classList.remove('lg:ml-[230px]');
        }
    }

    // Initialize on load
    initializeSidebar();

    // Reinitialize on window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(initializeSidebar, 250);
    });

    // Close sidebar when clicking backdrop
    if (backdrop) {
        backdrop.addEventListener('click', function() {
            if (sidebar && !sidebar.classList.contains('-translate-x-full')) {
                window.toggleSidebar();
            }
        });
    }

    // Close sidebar on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar && backdrop) {
            if (!sidebar.classList.contains('-translate-x-full') && window.innerWidth < 1024) {
                window.toggleSidebar();
            }
        }
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        if (window.innerWidth < 1024 && sidebar && backdrop) {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickOnToggle = event.target.closest('[onclick="toggleSidebar()"]');

            if (!isClickInsideSidebar && !isClickOnToggle && !sidebar.classList.contains('-translate-x-full')) {
                window.toggleSidebar();
            }
        }
    });
});
