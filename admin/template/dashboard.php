<!-- ============================================================================
     DASHBOARD ADMIN - Campus Sports Arena
     ============================================================================ -->

<!-- Header Dashboard -->
<?php
// Array giorni e mesi in italiano
$giorni = ['Domenica', 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato'];
$mesi = ['', 'gennaio', 'febbraio', 'marzo', 'aprile', 'maggio', 'giugno', 'luglio', 'agosto', 'settembre', 'ottobre', 'novembre', 'dicembre'];
$dataItaliana = $giorni[date('w')] . ' ' . date('d') . ' ' . $mesi[date('n')] . ' ' . date('Y');
?>
<div class="dashboard-header d-flex justify-content-between align-items-start mb-4">
    <div>
        <p class="dashboard-date mb-0">
            <span class="status-dot"></span>
            <?php echo $dataItaliana; ?>
        </p>
    </div>
    <div class="d-flex align-items-center gap-3">
        <!-- Notifications - Link a Segnalazioni -->
        <a href="segnalazioni.php" class="btn-icon position-relative" title="Vai alle Segnalazioni">
            <span>🔔</span>
            <?php if($templateParams["alerts"]["segnalazioni_pending"] > 0): ?>
            <span class="notification-badge"><?php echo $templateParams["alerts"]["segnalazioni_pending"]; ?></span>
            <?php endif; ?>
        </a>
        
        <!-- New Action Button -->
        <div class="dropdown">
            <button class="btn-primary-gradient dropdown-toggle" data-bs-toggle="dropdown">
                + Nuova Azione
            </button>
            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                <li><a class="dropdown-item" href="gestione-campi.php?action=add">🏟️ Aggiungi Campo</a></li>
                <li><a class="dropdown-item" href="gestione-utenti.php">👥 Gestione Utenti</a></li>
                <li><a class="dropdown-item" href="comunicazioni.php?action=new">📣 Nuova Comunicazione</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="segnalazioni.php">🚨 Vedi Segnalazioni</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Alert Cards -->
<div class="row g-3 mb-4">
    <!-- Segnalazioni Pending -->
    <?php if($templateParams["alerts"]["segnalazioni_pending"] > 0): ?>
    <div class="col-md-6">
        <div class="alert-card alert-critical">
            <div class="alert-indicator"></div>
            <div class="alert-icon">🔴</div>
            <div class="alert-content">
                <h4><?php echo $templateParams["alerts"]["segnalazioni_pending"]; ?> segnalazioni pending</h4>
                <p>Alta priorità - richiedono attenzione immediata</p>
            </div>
            <div class="alert-meta">
                <span class="alert-time">ora</span>
                <a href="segnalazioni.php" class="alert-action">Gestisci →</a>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Campo Rating Basso -->
    <?php if($templateParams["alerts"]["campo_rating_basso"]): ?>
    <div class="col-md-6">
        <div class="alert-card alert-warning">
            <div class="alert-indicator"></div>
            <div class="alert-icon">🟡</div>
            <div class="alert-content">
                <h4>Campo <?php echo htmlspecialchars($templateParams["alerts"]["campo_rating_basso"]["nome"]); ?></h4>
                <p>Rating sceso a <?php echo $templateParams["alerts"]["campo_rating_basso"]["rating_medio"]; ?> stelle - verificare recensioni</p>
            </div>
            <div class="alert-meta">
                <span class="alert-time">1 ora fa</span>
                <a href="gestione-campi.php" class="alert-action">Verifica →</a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <!-- Prenotazioni Totali -->
    <div class="col-xl col-md-4 col-6">
        <div class="kpi-card" data-color="blue">
            <div class="kpi-header">
                <span class="kpi-icon">📅</span>
            </div>
            <div class="kpi-value"><?php echo $templateParams["kpi"]["prenotazioni_oggi"]; ?></div>
            <div class="kpi-label">Prenotazioni Totali</div>
            <div class="kpi-progress">
                <div class="kpi-progress-bar bg-blue" style="width: <?php echo min(100, $templateParams["kpi"]["prenotazioni_oggi"]); ?>%"></div>
            </div>
        </div>
    </div>
    
    <!-- Confermate/Completate -->
    <div class="col-xl col-md-4 col-6">
        <div class="kpi-card" data-color="purple">
            <div class="kpi-header">
                <span class="kpi-icon">✅</span>
            </div>
            <div class="kpi-value"><?php echo $templateParams["kpi"]["prenotazioni_settimana"]; ?></div>
            <div class="kpi-label">Completate</div>
            <div class="kpi-progress">
                <div class="kpi-progress-bar bg-purple" style="width: <?php echo min(100, $templateParams["kpi"]["prenotazioni_settimana"]); ?>%"></div>
            </div>
        </div>
    </div>
    
    <!-- Utilizzo Campi -->
    <div class="col-xl col-md-4 col-6">
        <div class="kpi-card" data-color="green">
            <div class="kpi-header">
                <span class="kpi-icon">🏟️</span>
            </div>
            <div class="kpi-value"><?php echo $templateParams["kpi"]["utilizzo_campi"]; ?>%</div>
            <div class="kpi-label">Utilizzo Campi</div>
            <div class="kpi-progress">
                <div class="kpi-progress-bar bg-green" style="width: <?php echo $templateParams["kpi"]["utilizzo_campi"]; ?>%"></div>
            </div>
        </div>
    </div>
    
    <!-- Utenti Attivi -->
    <div class="col-xl col-md-4 col-6">
        <div class="kpi-card" data-color="cyan">
            <div class="kpi-header">
                <span class="kpi-icon">👥</span>
            </div>
            <div class="kpi-value"><?php echo $templateParams["kpi"]["utenti_attivi"]; ?></div>
            <div class="kpi-label">Utenti Attivi</div>
            <div class="kpi-progress">
                <div class="kpi-progress-bar bg-cyan" style="width: <?php echo min(100, $templateParams["kpi"]["utenti_attivi"] * 3); ?>%"></div>
            </div>
        </div>
    </div>
    
    <!-- In Manutenzione -->
    <div class="col-xl col-md-4 col-6">
        <div class="kpi-card" data-color="red">
            <div class="kpi-header">
                <span class="kpi-icon">🔧</span>
            </div>
            <div class="kpi-value"><?php echo $templateParams["kpi"]["campi_manutenzione"]; ?></div>
            <div class="kpi-label">In Manutenzione</div>
            <div class="kpi-progress">
                <div class="kpi-progress-bar bg-red" style="width: <?php echo $templateParams["kpi"]["campi_manutenzione"] * 20; ?>%"></div>
            </div>
        </div>
    </div>
    
    <!-- Recensioni -->
    <div class="col-xl col-md-4 col-6">
        <div class="kpi-card" data-color="orange">
            <div class="kpi-header">
                <span class="kpi-icon">⭐</span>
            </div>
            <div class="kpi-value"><?php echo $templateParams["kpi"]["recensioni_totali"]; ?></div>
            <div class="kpi-label">Recensioni (<?php echo $templateParams["kpi"]["rating_medio"]; ?>★)</div>
            <div class="kpi-progress">
                <div class="kpi-progress-bar bg-orange" style="width: <?php echo ($templateParams["kpi"]["rating_medio"] / 5) * 100; ?>%"></div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <!-- Prenotazioni per Giorno -->
    <div class="col-lg-8">
        <div class="dashboard-card">
            <div class="card-header-custom">
                <h3><span class="card-icon">📈</span> Prenotazioni per Giorno</h3>
                <span class="text-muted small">Distribuzione settimanale</span>
            </div>
            <div class="card-body-custom">
                <div class="chart-container" style="height: 280px; position: relative;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Utilizzo Campi Lista -->
    <div class="col-lg-4">
        <div class="dashboard-card">
            <div class="card-header-custom">
                <h3><span class="card-icon">🏟️</span> Utilizzo Campi</h3>
                <a href="gestione-campi.php" class="card-link">Vedi tutti →</a>
            </div>
            <div class="card-body-custom">
                <div class="usage-list">
                    <?php foreach($templateParams["utilizzo_lista"] as $campo): ?>
                    <div class="usage-item">
                        <span class="usage-name"><?php echo htmlspecialchars($campo["nome"]); ?></span>
                        <div class="usage-bar-container">
                            <div class="usage-bar <?php 
                                echo $campo["percentuale"] > 80 ? 'bg-green' : 
                                    ($campo["percentuale"] > 50 ? 'bg-orange' : 'bg-blue'); 
                            ?>" style="width: <?php echo max(5, $campo["percentuale"]); ?>%"></div>
                        </div>
                        <span class="usage-value"><?php echo $campo["prenotazioni"]; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Row -->
<div class="row g-4 align-items-start">
    <!-- Distribuzione Sport -->
    <div class="col-lg-3">
        <div class="dashboard-card">
            <div class="card-header-custom">
                <h3><span class="card-icon">⚽</span> Distribuzione Sport</h3>
            </div>
            <div class="card-body-custom text-center">
                <div class="donut-container">
                    <canvas id="sportChart" width="150" height="150"></canvas>
                    <div class="donut-center">
                        <span class="donut-value" id="totalHours">0</span>
                        <span class="donut-label">prenotazioni</span>
                    </div>
                </div>
                <div class="sport-legend">
                    <?php 
                    // Colori distinti per ogni sport
                    $sportColors = ['#3B82F6', '#8B5CF6', '#10B981', '#F59E0B', '#06B6D4', '#EF4444', '#EC4899', '#14B8A6'];
                    $i = 0;
                    foreach($templateParams["sport"] as $sport): 
                    ?>
                    <div class="sport-legend-item">
                        <span class="sport-dot" style="background: <?php echo $sportColors[$i % 8]; ?>"></span>
                        <span class="sport-name"><?php echo htmlspecialchars($sport["sport"]); ?></span>
                        <span class="sport-percent"><?php echo $sport["percentuale"]; ?>%</span>
                    </div>
                    <?php $i++; endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Attività Recenti -->
    <div class="col-lg-5">
        <div class="dashboard-card">
            <div class="card-header-custom">
                <h3><span class="card-icon">⏱️</span> Attività Recenti</h3>
            </div>
            <div class="card-body-custom">
                <div class="activity-list">
                    <?php 
                    // Traduzione tipi segnalazione
                    $traduzioniTipo = [
                        'no_show' => 'Mancata presenza',
                        'comportamento_scorretto' => 'Comportamento',
                        'linguaggio_offensivo' => 'Linguaggio',
                        'violenza' => 'Violenza',
                        'altro' => 'Altro'
                    ];
                    foreach($templateParams["attivita"] as $att): 
                        // Traduci il dettaglio se è un tipo di segnalazione
                        $dettaglio = $att["dettaglio"];
                        if ($att["tipo"] == "report" && isset($traduzioniTipo[$dettaglio])) {
                            $dettaglio = $traduzioniTipo[$dettaglio];
                        }
                    ?>
                    <div class="activity-item" data-type="<?php echo $att["tipo"]; ?>">
                        <div class="activity-icon">
                            <?php 
                            $icons = ['booking' => '📅', 'review' => '⭐', 'report' => '🚨', 'cancel' => '❌'];
                            echo $icons[$att["tipo"]] ?? '📌';
                            ?>
                        </div>
                        <div class="activity-content">
                            <span class="activity-user"><?php echo htmlspecialchars($att["utente"]); ?></span>
                            <span class="activity-action">
                                <?php echo htmlspecialchars($att["azione"]); ?> • 
                                <span class="activity-detail"><?php echo htmlspecialchars($dettaglio); ?></span>
                            </span>
                        </div>
                        <span class="activity-time"><?php echo $templateParams["dbh"]->tempoRelativo($att["data"]); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
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
                    <a href="gestione-campi.php?action=maintenance" class="quick-action-btn bg-gradient-orange">
                        <span class="qa-icon">🔧</span>
                        <span class="qa-label">Blocca Campo</span>
                    </a>
                    <a href="segnalazioni.php" class="quick-action-btn bg-gradient-red">
                        <span class="qa-icon">🚨</span>
                        <span class="qa-label">Segnalazioni</span>
                        <?php if($templateParams["alerts"]["segnalazioni_pending"] > 0): ?>
                        <span class="qa-badge"><?php echo $templateParams["alerts"]["segnalazioni_pending"]; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="analytics.php" class="quick-action-btn bg-gradient-blue">
                        <span class="qa-icon">📊</span>
                        <span class="qa-label">Genera Report</span>
                    </a>
                    <a href="gestione-utenti.php" class="quick-action-btn bg-gradient-purple">
                        <span class="qa-icon">👥</span>
                        <span class="qa-label">Gestione Utenti</span>
                    </a>
                    <a href="comunicazioni.php" class="quick-action-btn bg-gradient-cyan">
                        <span class="qa-icon">📣</span>
                        <span class="qa-label">Broadcast</span>
                    </a>
                    <a href="configurazione.php" class="quick-action-btn bg-gradient-gray">
                        <span class="qa-icon">⚙️</span>
                        <span class="qa-label">Impostazioni</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Dati PHP per i grafici
const trendData = <?php echo json_encode($templateParams["trend"]); ?>;
const sportData = <?php echo json_encode($templateParams["sport"]); ?>;

// Configurazione colori
const colors = {
    blue: '#3B82F6',
    purple: '#8B5CF6',
    green: '#10B981',
    orange: '#F59E0B',
    red: '#EF4444',
    cyan: '#06B6D4',
    pink: '#EC4899',
    teal: '#14B8A6'
};

// ============================================================================
// TREND CHART - Prenotazioni per giorno della settimana
// ============================================================================
const trendCtx = document.getElementById('trendChart').getContext('2d');

// Giorni della settimana (MySQL: 1=Domenica, 2=Lunedì, ...)
const giorniSettimana = ['Dom', 'Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab'];

// Prepara i dati - inizializza array con 7 giorni a 0
let prenotazioniPerGiorno = [0, 0, 0, 0, 0, 0, 0];
trendData.forEach(d => {
    const idx = parseInt(d.giorno_settimana) - 1; // MySQL parte da 1
    if (idx >= 0 && idx < 7) {
        prenotazioniPerGiorno[idx] = parseInt(d.totale) || 0;
    }
});

// Riordina per iniziare da Lunedì
const labels = ['Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab', 'Dom'];
const valori = [
    prenotazioniPerGiorno[1], // Lunedì
    prenotazioniPerGiorno[2], // Martedì
    prenotazioniPerGiorno[3], // Mercoledì
    prenotazioniPerGiorno[4], // Giovedì
    prenotazioniPerGiorno[5], // Venerdì
    prenotazioniPerGiorno[6], // Sabato
    prenotazioniPerGiorno[0]  // Domenica
];

const trendChart = new Chart(trendCtx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Prenotazioni',
            data: valori,
            backgroundColor: colors.blue + '80',
            borderColor: colors.blue,
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
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
                displayColors: false,
                callbacks: {
                    label: function(context) {
                        return context.raw + ' prenotazioni';
                    }
                }
            }
        },
        scales: {
            x: {
                grid: {
                    display: false
                },
                ticks: { color: '#64748B', font: { weight: 600 } }
            },
            y: {
                grid: {
                    color: 'rgba(148, 163, 184, 0.1)',
                    drawBorder: false
                },
                ticks: { color: '#64748B' },
                beginAtZero: true
            }
        }
    }
});

// ============================================================================
// SPORT DISTRIBUTION DONUT
// ============================================================================
const sportCtx = document.getElementById('sportChart').getContext('2d');

const sportLabels = sportData.map(s => s.sport);
const sportValues = sportData.map(s => parseInt(s.prenotazioni) || 0);
// 8 colori distinti
const sportColors = [colors.blue, colors.purple, colors.green, colors.orange, colors.cyan, colors.red, colors.pink, colors.teal];

// Calcola totale prenotazioni
const totalOre = sportValues.reduce((a, b) => a + b, 0);
document.getElementById('totalHours').textContent = totalOre;

const sportChart = new Chart(sportCtx, {
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