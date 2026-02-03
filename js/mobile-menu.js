/**
 * CAMPUS SPORTS ARENA - Mobile Menu Handler
 * Gestisce il menu hamburger e la sidebar su mobile
 */

document.addEventListener('DOMContentLoaded', function() {
    const body = document.body;
    const sidebar = document.getElementById('sidebar');
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    const mobileOverlay = document.querySelector('.mobile-overlay');
    
    // Toggle sidebar
    function toggleSidebar() {
        body.classList.toggle('sidebar-open');
        
        // Previeni scroll del body quando sidebar è aperta
        if (body.classList.contains('sidebar-open')) {
            body.style.overflow = 'hidden';
        } else {
            body.style.overflow = '';
        }
    }
    
    // Chiudi sidebar
    function closeSidebar() {
        body.classList.remove('sidebar-open');
        body.style.overflow = '';
    }
    
    // Event: click sul bottone hamburger
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
        });
    }
    
    // Event: click sull'overlay
    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', function(e) {
            e.preventDefault();
            closeSidebar();
        });
    }
    
    // Event: click su un link nella sidebar (chiudi dopo navigazione)
    if (sidebar) {
        sidebar.querySelectorAll('.nav-link').forEach(function(link) {
            link.addEventListener('click', function() {
                // Chiudi sidebar con un piccolo delay per mostrare l'effetto
                setTimeout(closeSidebar, 150);
            });
        });
    }
    
    // Event: tasto ESC per chiudere
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && body.classList.contains('sidebar-open')) {
            closeSidebar();
        }
    });
    
    // Event: resize - chiudi sidebar se si passa a desktop
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.innerWidth > 768) {
                closeSidebar();
            }
        }, 100);
    });
    
    // Swipe gesture per chiudere sidebar su mobile
    let touchStartX = 0;
    let touchEndX = 0;
    
    document.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });
    
    document.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, { passive: true });
    
    function handleSwipe() {
        const swipeThreshold = 100;
        const swipeDistance = touchEndX - touchStartX;
        
        // Swipe verso sinistra per chiudere
        if (swipeDistance < -swipeThreshold && body.classList.contains('sidebar-open')) {
            closeSidebar();
        }
        
        // Swipe verso destra per aprire (solo dal bordo sinistro)
        if (swipeDistance > swipeThreshold && touchStartX < 30 && !body.classList.contains('sidebar-open')) {
            toggleSidebar();
        }
    }
});