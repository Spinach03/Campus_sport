<!-- ============================================================================
     DASHBOARD UTENTE - Campus Sports Arena
     ============================================================================ -->

<?php
// Variabili dal controller
$profilo = $templateParams["profilo"] ?? [];
$stats = $templateParams["stats"] ?? [];
$prossimePrenotazioni = $templateParams["prossime_prenotazioni"] ?? [];
$attivitaRecenti = $templateParams["attivita_recenti"] ?? [];
$distribuzioneSport = $templateParams["distribuzione_sport"] ?? [];
$notificheNonLette = $templateParams["notifiche_non_lette"] ?? 0;

// Data italiana
$giorni = ['Domenica', 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato'];
$mesi = ['', 'gennaio', 'febbraio', 'marzo', 'aprile', 'maggio', 'giugno', 'luglio', 'agosto', 'settembre', 'ottobre', 'novembre', 'dicembre'];
$dataItaliana = $giorni[date('w')] . ' ' . date('d') . ' ' . $mesi[date('n')] . ' ' . date('Y');

// Helper per tempo relativo
function tempoRelativo($data) {
    $now = new DateTime();
    $past = new DateTime($data);
    $diff = $now->diff($past);
    
    if ($diff->d == 0) {
        if ($diff->h == 0) {
            return $diff->i . ' min fa';
        }
        return $diff->h . ' ore fa';
    } elseif ($diff->d == 1) {
        return 'Ieri';
    } elseif ($diff->d < 7) {
        return $diff->d . ' giorni fa';
    } else {
        return date('d/m/Y', strtotime($data));
    }
}

// Helper per icona attività
function getIconaAttivita($tipo) {
    switch ($tipo) {
        case 'prenotazione': return '📅';
        case 'recensione': return '⭐';
        case 'badge': return '🏅';
        default: return '📋';
    }
}

// Helper per colore stato
function getStatoClasse($stato) {
    switch ($stato) {
        case 'confermata': return 'stato-confermata';
        case 'completata': return 'stato-completata';
        case 'cancellata': return 'stato-cancellata';
        case 'no_show': return 'stato-noshow';
        default: return 'stato-default';
    }
}

// Helper per emoji sport
function getSportEmojiDash($icona, $nome) {
    if (empty($icona) || strpos($icona, '.png') !== false) {
        $mapping = ['calcio' => '⚽', 'basket' => '🏀', 'tennis' => '🎾', 'padel' => '🎾', 'pallavolo' => '🏐', 'nuoto' => '🏊'];
        foreach ($mapping as $k => $v) {
            if (stripos($nome, $k) !== false) return $v;
        }
        return '🏅';
    }
    return $icona;
}
?>

<link rel="stylesheet" href="css/dashboard.css">

<!-- Header Dashboard con Benvenuto -->
<div class="dashboard-welcome-card">
    <div class="welcome-content">
        <div class="welcome-text">
            <p class="welcome-date">
                <span class="status-dot"></span>
                <?= $dataItaliana ?>
            </p>
            <h2 class="h1 welcome-title">Ciao, <?= htmlspecialchars($profilo['nome'] ?? 'Utente') ?>! 👋</h2>
            <p class="welcome-subtitle">Benvenuto su <strong>Campus Sports Arena</strong>, la piattaforma per prenotare campi sportivi universitari.</p>
        </div>
        <div class="welcome-actions">
            <a href="notifiche.php" class="btn-icon-dashboard" title="Notifiche">
                🔔
                <?php if ($notificheNonLette > 0): ?>
                <span class="notification-badge"><?= $notificheNonLette ?></span>
                <?php endif; ?>
            </a>
            <a href="prenota-campo.php" class="btn-primary-gradient">
                <span>+</span> Prenota Campo
            </a>
        </div>
    </div>
    
    <!-- Info Box -->
    <div class="welcome-info-box">
        <div class="info-item">
            <span class="info-icon">🏟️</span>
            <span>Prenota campi sportivi</span>
        </div>
        <div class="info-item">
            <span class="info-icon">📅</span>
            <span>Gestisci prenotazioni</span>
        </div>
        <div class="info-item">
            <span class="info-icon">⭐</span>
            <span>Lascia recensioni</span>
        </div>
        <div class="info-item">
            <span class="info-icon">🏅</span>
            <span>Sblocca badges</span>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4 col-6">
        <div class="kpi-card" data-color="blue">
            <span class="kpi-icon">📅</span>
            <div class="kpi-value"><?= $stats['prenotazioni_attive'] ?? 0 ?></div>
            <div class="kpi-label">Prenotazioni Attive</div>
            <div class="kpi-progress">
                <div class="kpi-progress-bar bg-blue" style="width: <?= min(100, ($stats['prenotazioni_attive'] ?? 0) * 20) ?>%"></div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 col-6">
        <div class="kpi-card" data-color="green">
            <span class="kpi-icon">✅</span>
            <div class="kpi-value"><?= $stats['completate_totali'] ?? 0 ?></div>
            <div class="kpi-label">Prenotazioni Completate</div>
            <div class="kpi-progress">
                <div class="kpi-progress-bar bg-green" style="width: <?= min(100, ($stats['completate_totali'] ?? 0) * 3) ?>%"></div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 col-12">
        <div class="kpi-card" data-color="pink">
            <span class="kpi-icon">🔔</span>
            <div class="kpi-value"><?= $stats['notifiche_non_lette'] ?? 0 ?></div>
            <div class="kpi-label">Notifiche</div>
            <div class="kpi-progress">
                <div class="kpi-progress-bar bg-pink" style="width: <?= min(100, ($stats['notifiche_non_lette'] ?? 0) * 10) ?>%"></div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Row -->
<div class="row g-4 mb-4">
    <!-- Prossime Prenotazioni -->
    <div class="col-lg-8">
        <div class="dashboard-card">
            <div class="card-header-custom">
                <h3><span class="card-icon">📅</span> Prossime Prenotazioni</h3>
                <a href="le-mie-prenotazioni.php" class="card-link">Vedi tutte →</a>
            </div>
            <div class="card-body-custom">
                <?php if (empty($prossimePrenotazioni)): ?>
                <div class="empty-state">
                    <span class="empty-icon">📭</span>
                    <h4>Nessuna prenotazione in programma</h4>
                    <p>Prenota un campo per iniziare!</p>
                    <a href="prenota-campo.php" class="btn-primary-gradient btn-sm">Prenota Ora</a>
                </div>
                <?php else: ?>
                <div class="prenotazioni-list">
                    <?php foreach ($prossimePrenotazioni as $p): 
                        $sportEmoji = getSportEmojiDash($p['sport_icona'], $p['sport_nome']);
                        $isOggi = date('Y-m-d') === $p['data_prenotazione'];
                        $isDomani = date('Y-m-d', strtotime('+1 day')) === $p['data_prenotazione'];
                    ?>
                    <div class="prenotazione-item <?= $isOggi ? 'is-today' : '' ?>">
                        <div class="pren-sport-icon"><?= $sportEmoji ?></div>
                        <div class="pren-info">
                            <h5 class="pren-campo"><?= htmlspecialchars($p['campo_nome']) ?></h5>
                            <p class="pren-details">
                                <?= $p['sport_nome'] ?> • <?= htmlspecialchars($p['location']) ?>
                            </p>
                        </div>
                        <div class="pren-datetime">
                            <span class="pren-date <?= $isOggi ? 'today-badge' : ($isDomani ? 'tomorrow-badge' : '') ?>">
                                <?= $isOggi ? '🔴 Oggi' : ($isDomani ? '🟡 Domani' : date('d/m', strtotime($p['data_prenotazione']))) ?>
                            </span>
                            <span class="pren-time"><?= date('H:i', strtotime($p['ora_inizio'])) ?> - <?= date('H:i', strtotime($p['ora_fine'])) ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Azioni Rapide -->
    <div class="col-lg-4">
        <div class="dashboard-card">
            <div class="card-header-custom">
                <h3><span class="card-icon">⚡</span> Azioni Rapide</h3>
            </div>
            <div class="card-body-custom">
                <div class="quick-actions-grid">
                    <a href="prenota-campo.php" class="quick-action-btn bg-gradient-blue">
                        <span class="qa-icon">🏟️</span>
                        <span class="qa-label">Prenota Campo</span>
                    </a>
                    <a href="le-mie-prenotazioni.php" class="quick-action-btn bg-gradient-purple">
                        <span class="qa-icon">📋</span>
                        <span class="qa-label">Le Mie Prenotazioni</span>
                    </a>
                    <a href="recensioni.php" class="quick-action-btn bg-gradient-orange">
                        <span class="qa-icon">⭐</span>
                        <span class="qa-label">Recensioni</span>
                    </a>
                    <a href="notifiche.php" class="quick-action-btn bg-gradient-cyan">
                        <span class="qa-icon">🔔</span>
                        <span class="qa-label">Notifiche</span>
                        <?php if ($notificheNonLette > 0): ?>
                        <span class="qa-badge"><?= $notificheNonLette ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="segnalazioni.php" class="quick-action-btn bg-gradient-red">
                        <span class="qa-icon">🚨</span>
                        <span class="qa-label">Segnalazioni</span>
                    </a>
                    <a href="profilo.php" class="quick-action-btn bg-gradient-gray">
                        <span class="qa-icon">👤</span>
                        <span class="qa-label">Il Mio Profilo</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Second Row -->
<div class="row g-4 align-items-start">
    <!-- Attività Recenti -->
    <div class="col-lg-6">
        <div class="dashboard-card">
            <div class="card-header-custom">
                <h3><span class="card-icon">📜</span> Attività Recenti</h3>
            </div>
            <div class="card-body-custom">
                <?php if (empty($attivitaRecenti)): ?>
                <div class="empty-state small">
                    <span class="empty-icon">📭</span>
                    <p>Nessuna attività recente</p>
                </div>
                <?php else: ?>
                <div class="attivita-list">
                    <?php foreach ($attivitaRecenti as $att): ?>
                    <div class="attivita-item">
                        <span class="att-icon"><?= getIconaAttivita($att['tipo']) ?></span>
                        <div class="att-info">
                            <p class="att-desc"><?= htmlspecialchars($att['descrizione']) ?></p>
                            <span class="att-time"><?= tempoRelativo($att['data']) ?></span>
                        </div>
                        <span class="att-stato <?= getStatoClasse($att['stato']) ?>"></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Sport Preferiti + Chart -->
    <div class="col-lg-6">
        <div class="dashboard-card">
            <div class="card-header-custom">
                <h3><span class="card-icon">🎯</span> I Tuoi Sport</h3>
            </div>
            <div class="card-body-custom">
                <?php if (empty($distribuzioneSport)): ?>
                <div class="empty-state small">
                    <span class="empty-icon">🏅</span>
                    <p>Nessuna prenotazione ancora</p>
                    <a href="prenota-campo.php" class="btn-primary-gradient btn-sm">Inizia Ora</a>
                </div>
                <?php else: ?>
                <div class="sport-stats-container">
                    <div class="sport-chart-wrapper">
                        <canvas id="sportChart"></canvas>
                        <div class="chart-center">
                            <span class="chart-total" id="totalPrenotazioni">0</span>
                            <span class="chart-label">Prenotazioni</span>
                        </div>
                    </div>
                    <div class="sport-legend">
                        <?php 
                        $colors = ['#3B82F6', '#8B5CF6', '#10B981', '#F59E0B', '#06B6D4', '#EF4444', '#EC4899', '#14B8A6'];
                        foreach ($distribuzioneSport as $i => $sport): 
                            $emoji = getSportEmojiDash($sport['icona'], $sport['sport']);
                        ?>
                        <div class="legend-item">
                            <span class="legend-color" style="background: <?= $colors[$i % count($colors)] ?>"></span>
                            <span class="legend-emoji"><?= $emoji ?></span>
                            <span class="legend-name"><?= htmlspecialchars($sport['sport']) ?></span>
                            <span class="legend-value"><?= $sport['prenotazioni'] ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($distribuzioneSport)): ?>
<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const sportData = <?= json_encode($distribuzioneSport) ?>;
const colors = ['#3B82F6', '#8B5CF6', '#10B981', '#F59E0B', '#06B6D4', '#EF4444', '#EC4899', '#14B8A6'];

const sportLabels = sportData.map(s => s.sport);
const sportValues = sportData.map(s => parseInt(s.prenotazioni) || 0);
const sportColors = sportData.map((_, i) => colors[i % colors.length]);

const totalPren = sportValues.reduce((a, b) => a + b, 0);
document.getElementById('totalPrenotazioni').textContent = totalPren;

const sportCtx = document.getElementById('sportChart').getContext('2d');
new Chart(sportCtx, {
    type: 'doughnut',
    data: {
        labels: sportLabels,
        datasets: [{
            data: sportValues,
            backgroundColor: sportColors,
            borderColor: '#0F172A',
            borderWidth: 3,
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        cutout: '70%',
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1E293B',
                titleColor: '#F1F5F9',
                bodyColor: '#94A3B8',
                borderColor: '#334155',
                borderWidth: 1,
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        return context.label + ': ' + context.raw + ' prenotazioni';
                    }
                }
            }
        }
    }
});
</script>
<?php endif; ?>