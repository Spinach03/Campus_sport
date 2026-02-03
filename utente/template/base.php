<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="description" content="Campus Sports Arena - Prenota i campi sportivi del campus universitario">
    <title><?php echo htmlspecialchars($templateParams["titolo"]); ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/mobile-fix.css">
    <link rel="stylesheet" href="../css/accessibility.css">
    <?php 
    if(isset($templateParams["css_extra"]) && is_array($templateParams["css_extra"])){
        foreach($templateParams["css_extra"] as $css){
            echo '<link rel="stylesheet" href="' . htmlspecialchars($css) . '">' . "\n    ";
        }
    }
    ?>
</head>
<body>
    <!-- ====================================================================
         SKIP LINKS - WCAG 2.4.1 Bypass Blocks
         Permettono di saltare direttamente al contenuto
         ==================================================================== -->
    <nav class="skip-links" aria-label="Link di navigazione rapida">
        <a href="#main-content" class="skip-link">Vai al contenuto principale</a>
        <a href="#main-navigation" class="skip-link">Vai alla navigazione</a>
    </nav>

    <!-- HAMBURGER MENU (Mobile) -->
    <button class="mobile-menu-toggle" 
            type="button" 
            aria-label="Apri menu di navigazione" 
            aria-expanded="false" 
            aria-controls="sidebar">
        <span class="hamburger-icon" aria-hidden="true">
            <span></span>
            <span></span>
            <span></span>
        </span>
    </button>
    
    <!-- OVERLAY -->
    <div class="mobile-overlay" aria-hidden="true"></div>
    
    <div class="app-container">
        
        <!-- ================================================================
             SIDEBAR - Con ruoli ARIA corretti
             ================================================================ -->
        <aside class="sidebar" id="sidebar" role="complementary" aria-label="Barra laterale">
            
            <!-- Logo -->
            <div class="sidebar-header">
                <div class="sidebar-logo" aria-hidden="true">🏟️</div>
                <div class="sidebar-brand">
                    <span class="brand-title">Campus Sports</span>
                    <span class="brand-subtitle" aria-label="Area utente">AREA UTENTE</span>
                </div>
            </div>
            
            <!-- Toggle Sidebar (Desktop) -->
            <button class="sidebar-toggle" 
                    id="sidebarToggle" 
                    type="button" 
                    aria-label="Comprimi o espandi la barra laterale"
                    aria-expanded="true">
                <i class="bi bi-chevron-left" id="toggleIcon" aria-hidden="true"></i>
            </button>

            <!-- ============================================================
                 NAVIGAZIONE PRINCIPALE - WCAG 4.1.2
                 ============================================================ -->
            <nav class="sidebar-nav" id="main-navigation" role="navigation" aria-label="Menu principale">
                <ul class="nav flex-column" role="menubar" aria-label="Navigazione principale">
                    
                    <li class="nav-item" role="none">
                        <a href="index.php" 
                           class="nav-link <?php if(basename($_SERVER['PHP_SELF']) == 'index.php') echo 'active'; ?>" 
                           role="menuitem"
                           <?php if(basename($_SERVER['PHP_SELF']) == 'index.php') echo 'aria-current="page"'; ?>>
                            <span class="nav-icon" aria-hidden="true">📊</span>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="nav-item" role="none">
                        <a href="prenota-campo.php" 
                           class="nav-link <?php if(basename($_SERVER['PHP_SELF']) == 'prenota-campo.php') echo 'active'; ?>" 
                           role="menuitem"
                           <?php if(basename($_SERVER['PHP_SELF']) == 'prenota-campo.php') echo 'aria-current="page"'; ?>>
                            <span class="nav-icon" aria-hidden="true">🏟️</span>
                            <span class="nav-text">Prenota Campo</span>
                        </a>
                    </li>
                    
                    <li class="nav-item" role="none">
                        <a href="le-mie-prenotazioni.php" 
                           class="nav-link <?php if(basename($_SERVER['PHP_SELF']) == 'le-mie-prenotazioni.php') echo 'active'; ?>" 
                           role="menuitem"
                           <?php if(basename($_SERVER['PHP_SELF']) == 'le-mie-prenotazioni.php') echo 'aria-current="page"'; ?>>
                            <span class="nav-icon" aria-hidden="true">✅</span>
                            <span class="nav-text">Le mie prenotazioni</span>
                        </a>
                    </li>
                    
                    <li class="nav-item" role="none">
                        <a href="notifiche.php" 
                           class="nav-link <?php if(basename($_SERVER['PHP_SELF']) == 'notifiche.php') echo 'active'; ?>" 
                           role="menuitem"
                           <?php if(basename($_SERVER['PHP_SELF']) == 'notifiche.php') echo 'aria-current="page"'; ?>>
                            <span class="nav-icon" aria-hidden="true">🔔</span>
                            <span class="nav-text">Notifiche</span>
                            <?php 
                            $notificheNonLette = $dbh->getNotificheNonLette($_SESSION['user_id']);
                            if ($notificheNonLette > 0): 
                            ?>
                            <span class="nav-badge" role="status" aria-label="<?= $notificheNonLette ?> notifiche non lette">
                                <?= $notificheNonLette > 99 ? '99+' : $notificheNonLette ?>
                            </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    
                    <li class="nav-item" role="none">
                        <a href="recensioni.php" 
                           class="nav-link <?php if(basename($_SERVER['PHP_SELF']) == 'recensioni.php') echo 'active'; ?>" 
                           role="menuitem"
                           <?php if(basename($_SERVER['PHP_SELF']) == 'recensioni.php') echo 'aria-current="page"'; ?>>
                            <span class="nav-icon" aria-hidden="true">⭐</span>
                            <span class="nav-text">Recensioni</span>
                        </a>
                    </li>
                    
                    <li class="nav-item" role="none">
                        <a href="segnalazioni.php" 
                           class="nav-link <?php if(basename($_SERVER['PHP_SELF']) == 'segnalazioni.php') echo 'active'; ?>" 
                           role="menuitem"
                           <?php if(basename($_SERVER['PHP_SELF']) == 'segnalazioni.php') echo 'aria-current="page"'; ?>>
                            <span class="nav-icon" aria-hidden="true">🚨</span>
                            <span class="nav-text">Segnalazioni</span>
                        </a>
                    </li>
                    
                    <li class="nav-item" role="none">
                        <a href="profilo.php" 
                           class="nav-link <?php if(basename($_SERVER['PHP_SELF']) == 'profilo.php') echo 'active'; ?>" 
                           role="menuitem"
                           <?php if(basename($_SERVER['PHP_SELF']) == 'profilo.php') echo 'aria-current="page"'; ?>>
                            <span class="nav-icon" aria-hidden="true">👤</span>
                            <span class="nav-text">Profilo</span>
                        </a>
                    </li>
                    
                    <li class="nav-item nav-item-logout" role="none">
                        <a href="../logout.php" class="nav-link nav-link-logout" role="menuitem">
                            <span class="nav-icon" aria-hidden="true">🚪</span>
                            <span class="nav-text">Esci</span>
                        </a>
                    </li>
                    
                </ul>
            </nav>

            <!-- User Info -->
            <div class="sidebar-user" role="contentinfo" aria-label="Informazioni utente connesso">
                <div class="user-avatar" aria-hidden="true">
                    <?php echo strtoupper(substr($_SESSION['nome'], 0, 1) . substr($_SESSION['cognome'], 0, 1)); ?>
                </div>
                <div class="user-info">
                    <span class="user-name"><?php echo htmlspecialchars($_SESSION['nome'] . ' ' . $_SESSION['cognome']); ?></span>
                    <span class="user-role">Utente</span>
                </div>
            </div>
        </aside>
        
        <!-- ================================================================
             CONTENUTO PRINCIPALE - WCAG 2.4.1
             ================================================================ -->
        <main class="main-content" id="main-content" role="main" tabindex="-1">
            
            <header class="content-header">
                <h1><?php echo htmlspecialchars($templateParams["titolo_pagina"]); ?></h1>
            </header>
            
            <section class="content-body" aria-label="<?php echo htmlspecialchars($templateParams["titolo_pagina"]); ?>">
                <?php
                if(isset($templateParams["nome"])){
                    require("template/" . $templateParams["nome"]);
                }
                ?>
            </section>
            
        </main>
        
    </div>

    <!-- Live Region per annunci agli screen reader - WCAG 4.1.3 -->
    <div id="live-announcer" 
         class="sr-only" 
         aria-live="polite" 
         aria-atomic="true"
         role="status"></div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/mobile-menu.js"></script>
    
    <!-- Accessibility & Sidebar Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');
        const toggleIcon = document.getElementById('toggleIcon');
        const mobileToggle = document.querySelector('.mobile-menu-toggle');
        const announcer = document.getElementById('live-announcer');
        
        // Funzione per annunciare agli screen reader
        function announce(message) {
            if (announcer) {
                announcer.textContent = message;
                setTimeout(() => { announcer.textContent = ''; }, 1000);
            }
        }
        
        // Carica stato sidebar (solo desktop)
        if (window.innerWidth > 768 && localStorage.getItem('sidebarCollapsed') === 'true') {
            sidebar.classList.add('collapsed');
            toggleIcon.classList.replace('bi-chevron-left', 'bi-chevron-right');
            toggleBtn.setAttribute('aria-expanded', 'false');
        }
        
        // Toggle sidebar desktop
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                if (window.innerWidth > 768) {
                    const isCollapsed = sidebar.classList.toggle('collapsed');
                    
                    toggleIcon.classList.toggle('bi-chevron-left', !isCollapsed);
                    toggleIcon.classList.toggle('bi-chevron-right', isCollapsed);
                    toggleBtn.setAttribute('aria-expanded', !isCollapsed);
                    localStorage.setItem('sidebarCollapsed', isCollapsed);
                    
                    announce(isCollapsed ? 'Menu compresso' : 'Menu espanso');
                }
            });
        }
        
        // Aggiorna aria-expanded mobile
        if (mobileToggle) {
            const observer = new MutationObserver(function() {
                const isOpen = document.body.classList.contains('sidebar-open');
                mobileToggle.setAttribute('aria-expanded', isOpen);
                if (isOpen) announce('Menu aperto');
            });
            observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
        }
        
        // Focus trap per modal (accessibilità)
        document.querySelectorAll('.modal').forEach(function(modal) {
            modal.addEventListener('shown.bs.modal', function() {
                const focusable = modal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
                if (focusable.length) focusable[0].focus();
            });
        });
        
        // Skip link focus management
        document.querySelectorAll('.skip-link').forEach(function(link) {
            link.addEventListener('click', function(e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.focus();
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    });
    </script>
</body>
</html>