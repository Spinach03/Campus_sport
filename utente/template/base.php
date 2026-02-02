<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $templateParams["titolo"]; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons (per freccia toggle) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts - Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <?php 
    // CSS aggiuntivi specifici per pagina
    if(isset($templateParams["css_extra"]) && is_array($templateParams["css_extra"])){
        foreach($templateParams["css_extra"] as $css){
            echo '<link rel="stylesheet" href="' . htmlspecialchars($css) . '">' . "\n    ";
        }
    }
    ?>
</head>
<body>
    <div class="app-container">
        
        <!-- SIDEBAR FISSA -->
        <aside class="sidebar" id="sidebar">
            
            <!-- Logo e Titolo -->
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    🏟️
                </div>
                <div class="sidebar-brand">
                    <span class="brand-title">Campus Sports</span>
                    <span class="brand-subtitle">AREA UTENTE</span>
                </div>
            </div>
            
            <!-- Toggle Button -->
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-chevron-left" id="toggleIcon"></i>
            </button>

            <!-- Navigation -->
            <nav class="sidebar-nav">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF']) == 'index.php') echo 'active'; ?>">
                            <span class="nav-icon">📊</span>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="prenota-campo.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF']) == 'prenota-campo.php') echo 'active'; ?>">
                            <span class="nav-icon">🏟️</span>
                            <span class="nav-text">Prenota Campo</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="le-mie-prenotazioni.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF']) == 'le-mie-prenotazioni.php') echo 'active'; ?>">
                            <span class="nav-icon">✅</span>
                            <span class="nav-text">Le mie prenotazioni</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="notifiche.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF']) == 'notifiche.php') echo 'active'; ?>">
                            <span class="nav-icon">🔔</span>
                            <span class="nav-text">Notifiche</span>
                            <?php 
                            $notificheNonLette = $dbh->getNotificheNonLette($_SESSION['user_id']);
                            if ($notificheNonLette > 0): 
                            ?>
                            <span class="nav-badge"><?= $notificheNonLette > 99 ? '99+' : $notificheNonLette ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="recensioni.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF']) == 'recensioni.php') echo 'active'; ?>">
                            <span class="nav-icon">⭐</span>
                            <span class="nav-text">Recensioni</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="segnalazioni.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF']) == 'segnalazioni.php') echo 'active'; ?>">
                            <span class="nav-icon">🚨</span>
                            <span class="nav-text">Segnalazioni</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="profilo.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF']) == 'profilo.php') echo 'active'; ?>">
                            <span class="nav-icon">👤</span>
                            <span class="nav-text">Profilo</span>
                        </a>
                    </li>
                    <li class="nav-item nav-item-logout">
                        <a href="../logout.php" class="nav-link nav-link-logout">
                            <span class="nav-icon">🚪</span>
                            <span class="nav-text">Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- User Profile -->
            <div class="sidebar-user">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($_SESSION['nome'], 0, 1) . substr($_SESSION['cognome'], 0, 1)); ?>
                </div>
                <div class="user-info">
                    <span class="user-name"><?php echo $_SESSION['nome'] . ' ' . $_SESSION['cognome']; ?></span>
                    <span class="user-role">Utente</span>
                </div>
            </div>
        </aside>
        
        <!-- AREA CENTRALE DINAMICA -->
        <main class="main-content">
            <header class="content-header">
                <h2><?php echo $templateParams["titolo_pagina"]; ?></h2>
            </header>
            
            <section class="content-body">
                <?php
                if(isset($templateParams["nome"])){
                    require("template/" . $templateParams["nome"]);
                }
                ?>
            </section>
        </main>
        
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Sidebar Toggle Script con localStorage -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');
        const toggleIcon = document.getElementById('toggleIcon');
        
        // Carica stato salvato
        if(localStorage.getItem('sidebarCollapsed') === 'true') {
            sidebar.classList.add('collapsed');
            toggleIcon.classList.remove('bi-chevron-left');
            toggleIcon.classList.add('bi-chevron-right');
        }
        
        // Toggle e salva stato
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            
            if(sidebar.classList.contains('collapsed')) {
                toggleIcon.classList.remove('bi-chevron-left');
                toggleIcon.classList.add('bi-chevron-right');
                localStorage.setItem('sidebarCollapsed', 'true');
            } else {
                toggleIcon.classList.remove('bi-chevron-right');
                toggleIcon.classList.add('bi-chevron-left');
                localStorage.setItem('sidebarCollapsed', 'false');
            }
        });
    </script>
</body>
</html>