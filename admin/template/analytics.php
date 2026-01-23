<!-- ============================================================================
     ANALYTICS - Campus Sports Arena Admin
     ============================================================================ -->
<link rel="stylesheet" href="css/analytics.css">

<?php
$kpi = $templateParams['kpi'] ?? [];
?>

<!-- Header -->
<div class="gestione-header">
    <span class="header-icon">📊</span>
    <p class="page-subtitle">Analisi dettagliata delle prenotazioni e utilizzo</p>
    
    <!-- Export Button -->
    <button class="btn-export" onclick="exportCSV()">
        📥 Esporta CSV
    </button>
</div>

<!-- ============================================================================
     SELETTORE PERIODO
     ============================================================================ -->
<div class="periodo-selector mb-4">
    <div class="periodo-buttons">
        <button class="periodo-btn <?= ($templateParams['periodo_attivo'] ?? 'settimana') === 'oggi' ? 'active' : '' ?>" 
                data-periodo="oggi">
            Oggi
        </button>
        <button class="periodo-btn <?= ($templateParams['periodo_attivo'] ?? 'settimana') === 'settimana' ? 'active' : '' ?>" 
                data-periodo="settimana">
            Settimana
        </button>
        <button class="periodo-btn <?= ($templateParams['periodo_attivo'] ?? 'settimana') === 'mese' ? 'active' : '' ?>" 
                data-periodo="mese">
            Mese
        </button>
        <button class="periodo-btn <?= ($templateParams['periodo_attivo'] ?? 'settimana') === 'trimestre' ? 'active' : '' ?>" 
                data-periodo="trimestre">
            Trimestre
        </button>
        <button class="periodo-btn <?= ($templateParams['periodo_attivo'] ?? 'settimana') === 'anno' ? 'active' : '' ?>" 
                data-periodo="anno">
            Anno
        </button>
    </div>
    
    <!-- Periodo attivo label -->
    <div class="periodo-label" id="periodoLabel">
        📅 <span id="periodoText">Questa settimana</span>
    </div>
</div>

<!-- ============================================================================
     KPI CARDS
     ============================================================================ -->
<div class="row g-3 mb-4">
    <!-- Prenotazioni Totali -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card" data-color="blue">
            <span class="kpi-icon">📅</span>
            <div class="kpi-value" id="kpiPrenotazioni"><?= $kpi['prenotazioni_totali'] ?? 0 ?></div>
            <div class="kpi-label">Prenotazioni</div>
            <div class="kpi-trend <?= ($kpi['prenotazioni_var'] ?? 0) >= 0 ? 'positive' : 'negative' ?>" id="kpiPrenotazioniTrend">
                <?= ($kpi['prenotazioni_var'] ?? 0) >= 0 ? '↑' : '↓' ?> 
                <span><?= abs($kpi['prenotazioni_var'] ?? 0) ?>%</span>
            </div>
        </div>
    </div>
    
    <!-- Tasso Completamento -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card" data-color="green">
            <span class="kpi-icon">✅</span>
            <div class="kpi-value" id="kpiCompletamento"><?= $kpi['tasso_completamento'] ?? 0 ?>%</div>
            <div class="kpi-label">Completamento</div>
            <div class="kpi-trend <?= ($kpi['completamento_var'] ?? 0) >= 0 ? 'positive' : 'negative' ?>" id="kpiCompletamentoTrend">
                <?= ($kpi['completamento_var'] ?? 0) >= 0 ? '↑' : '↓' ?> 
                <span><?= abs($kpi['completamento_var'] ?? 0) ?>%</span>
            </div>
        </div>
    </div>
    
    <!-- Utenti Attivi -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card" data-color="purple">
            <span class="kpi-icon">👥</span>
            <div class="kpi-value" id="kpiUtenti"><?= $kpi['utenti_attivi'] ?? 0 ?></div>
            <div class="kpi-label">Utenti Attivi</div>
            <div class="kpi-trend <?= ($kpi['utenti_var'] ?? 0) >= 0 ? 'positive' : 'negative' ?>" id="kpiUtentiTrend">
                <?= ($kpi['utenti_var'] ?? 0) >= 0 ? '↑' : '↓' ?> 
                <span><?= abs($kpi['utenti_var'] ?? 0) ?>%</span>
            </div>
        </div>
    </div>
    
    <!-- No-Show Rate -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card" data-color="red">
            <span class="kpi-icon">🚫</span>
            <div class="kpi-value" id="kpiNoShow"><?= $kpi['noshow_rate'] ?? 0 ?>%</div>
            <div class="kpi-label">No-Show</div>
            <div class="kpi-trend <?= ($kpi['noshow_var'] ?? 0) <= 0 ? 'positive' : 'negative' ?>" id="kpiNoShowTrend">
                <?= ($kpi['noshow_var'] ?? 0) <= 0 ? '↓' : '↑' ?> 
                <span><?= abs($kpi['noshow_var'] ?? 0) ?>%</span>
            </div>
        </div>
    </div>
    
    <!-- Ore Prenotate -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card" data-color="cyan">
            <span class="kpi-icon">⏱️</span>
            <div class="kpi-value" id="kpiOre"><?= $kpi['ore_prenotate'] ?? 0 ?></div>
            <div class="kpi-label">Ore Prenotate</div>
            <div class="kpi-trend <?= ($kpi['ore_var'] ?? 0) >= 0 ? 'positive' : 'negative' ?>" id="kpiOreTrend">
                <?= ($kpi['ore_var'] ?? 0) >= 0 ? '↑' : '↓' ?> 
                <span><?= abs($kpi['ore_var'] ?? 0) ?>%</span>
            </div>
        </div>
    </div>
    
    <!-- Rating Medio -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card" data-color="orange">
            <span class="kpi-icon">⭐</span>
            <div class="kpi-value" id="kpiRating"><?= number_format($kpi['rating_medio'] ?? 0, 1) ?></div>
            <div class="kpi-label">Rating Medio</div>
            <div class="kpi-trend <?= ($kpi['rating_var'] ?? 0) >= 0 ? 'positive' : 'negative' ?>" id="kpiRatingTrend">
                <?= ($kpi['rating_var'] ?? 0) >= 0 ? '↑' : '↓' ?> 
                <span><?= abs($kpi['rating_var'] ?? 0) ?>%</span>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     GRAFICI - Riga 1: Trend + Distribuzione Sport
     ============================================================================ -->
<div class="row g-4 mb-4">
    <!-- Trend Prenotazioni -->
    <div class="col-xl-8 col-lg-7">
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">📈 Trend Prenotazioni</h3>
                <div class="chart-legend" id="trendLegend">
                    <span class="legend-item"><span class="legend-dot" style="background:#10B981"></span> Completate</span>
                    <span class="legend-item"><span class="legend-dot" style="background:#F59E0B"></span> Cancellate</span>
                    <span class="legend-item"><span class="legend-dot" style="background:#EF4444"></span> No-Show</span>
                </div>
            </div>
            <div class="chart-body">
                <canvas id="chartTrend" height="280"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Distribuzione Sport -->
    <div class="col-xl-4 col-lg-5">
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">🏆 Distribuzione Sport</h3>
            </div>
            <div class="chart-body chart-body-donut">
                <canvas id="chartSport" height="280"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     GRAFICI - Riga 2: Heatmap + Utilizzo Campi
     ============================================================================ -->
<div class="row g-4 mb-4">
    <!-- Heatmap Settimanale -->
    <div class="col-xl-6">
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">🗓️ Heatmap Settimanale</h3>
                <p class="chart-subtitle">Distribuzione prenotazioni per giorno e ora</p>
            </div>
            <div class="chart-body">
                <div class="heatmap-container" id="heatmapContainer">
                    <!-- Generata via JS -->
                    <div class="heatmap-loading">
                        <div class="spinner"></div>
                        <span>Caricamento dati...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Utilizzo Campi -->
    <div class="col-xl-6">
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">🏟️ Utilizzo Campi</h3>
                <button class="btn-export-small" onclick="exportCampiCSV()">
                    📥 CSV
                </button>
            </div>
            <div class="chart-body">
                <div class="campi-list" id="campiList">
                    <!-- Generata via JS -->
                    <div class="heatmap-loading">
                        <div class="spinner"></div>
                        <span>Caricamento dati...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     JAVASCRIPT
     ============================================================================ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Variabili globali
let periodoAttivo = '<?= $templateParams['periodo_attivo'] ?? 'settimana' ?>';
let dataInizio = '<?= $templateParams['data_inizio'] ?? '' ?>';
let dataFine = '<?= $templateParams['data_fine'] ?? '' ?>';
let chartTrend = null;
let chartSport = null;

// Colori
const colors = {
    blue: '#3B82F6',
    green: '#10B981',
    orange: '#F59E0B',
    red: '#EF4444',
    purple: '#8B5CF6',
    cyan: '#06B6D4',
    pink: '#EC4899'
};

// ============================================================================
// INIT
// ============================================================================
document.addEventListener('DOMContentLoaded', function() {
    // Init grafici
    initCharts();
    
    // Carica dati iniziali
    loadAnalyticsData();
    
    // Event listeners per periodo
    document.querySelectorAll('.periodo-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const periodo = this.dataset.periodo;
            cambaPeriodo(periodo);
        });
    });
});

// ============================================================================
// INIT CHARTS
// ============================================================================
function initCharts() {
    // Chart Trend
    const ctxTrend = document.getElementById('chartTrend').getContext('2d');
    chartTrend = new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Completate',
                    data: [],
                    borderColor: colors.green,
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2
                },
                {
                    label: 'Cancellate',
                    data: [],
                    borderColor: colors.orange,
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2
                },
                {
                    label: 'No-Show',
                    data: [],
                    borderColor: colors.red,
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(148, 163, 184, 0.1)' },
                    ticks: { color: '#94a3b8' }
                },
                y: {
                    grid: { color: 'rgba(148, 163, 184, 0.1)' },
                    ticks: { color: '#94a3b8' },
                    beginAtZero: true
                }
            }
        }
    });
    
    // Chart Sport (Donut)
    const ctxSport = document.getElementById('chartSport').getContext('2d');
    chartSport = new Chart(ctxSport, {
        type: 'doughnut',
        data: {
            labels: [],
            datasets: [{
                data: [],
                backgroundColor: [colors.blue, colors.green, colors.orange, colors.purple, colors.cyan, colors.pink],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#94a3b8',
                        padding: 15,
                        usePointStyle: true
                    }
                }
            }
        }
    });
}

// ============================================================================
// CAMBIA PERIODO
// ============================================================================
function cambaPeriodo(periodo) {
    periodoAttivo = periodo;
    
    // Aggiorna UI buttons
    document.querySelectorAll('.periodo-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.periodo === periodo);
    });
    
    // Carica nuovi dati
    loadAnalyticsData();
}

// ============================================================================
// LOAD ANALYTICS DATA
// ============================================================================
function loadAnalyticsData() {
    // Mostra loading
    showLoading(true);
    
    const params = new URLSearchParams({
        ajax: 1,
        action: 'get_analytics_data',
        periodo: periodoAttivo,
        data_inizio: dataInizio,
        data_fine: dataFine
    });
    
    fetch('analytics.php?' + params.toString())
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                updateUI(result.data);
            } else {
                console.error('Errore:', result.message);
            }
            showLoading(false);
        })
        .catch(error => {
            console.error('Errore fetch:', error);
            showLoading(false);
        });
}

// ============================================================================
// UPDATE UI
// ============================================================================
function updateUI(data) {
    // Aggiorna KPI
    updateKPI(data.kpi);
    
    // Aggiorna Trend Chart
    updateTrendChart(data.trend);
    
    // Aggiorna Sport Chart
    updateSportChart(data.distribuzione_sport);
    
    // Aggiorna Heatmap
    updateHeatmap(data.heatmap);
    
    // Aggiorna Lista Campi
    updateCampiList(data.utilizzo_campi);
    
    // Aggiorna label periodo
    document.getElementById('periodoText').textContent = data.periodo.label;
}

// ============================================================================
// UPDATE KPI
// ============================================================================
function updateKPI(kpi) {
    // Prenotazioni
    document.getElementById('kpiPrenotazioni').textContent = kpi.prenotazioni_totali || 0;
    updateTrendBadge('kpiPrenotazioniTrend', kpi.prenotazioni_var || 0, true);
    
    // Completamento
    document.getElementById('kpiCompletamento').textContent = (kpi.tasso_completamento || 0) + '%';
    updateTrendBadge('kpiCompletamentoTrend', kpi.completamento_var || 0, true);
    
    // Utenti
    document.getElementById('kpiUtenti').textContent = kpi.utenti_attivi || 0;
    updateTrendBadge('kpiUtentiTrend', kpi.utenti_var || 0, true);
    
    // No-Show
    document.getElementById('kpiNoShow').textContent = (kpi.noshow_rate || 0) + '%';
    updateTrendBadge('kpiNoShowTrend', kpi.noshow_var || 0, false); // Inverso: basso è buono
    
    // Ore
    document.getElementById('kpiOre').textContent = kpi.ore_prenotate || 0;
    updateTrendBadge('kpiOreTrend', kpi.ore_var || 0, true);
    
    // Rating
    document.getElementById('kpiRating').textContent = parseFloat(kpi.rating_medio || 0).toFixed(1);
    updateTrendBadge('kpiRatingTrend', kpi.rating_var || 0, true);
}

function updateTrendBadge(elementId, value, positiveIsGood) {
    const el = document.getElementById(elementId);
    const isPositive = positiveIsGood ? value >= 0 : value <= 0;
    el.className = 'kpi-trend ' + (isPositive ? 'positive' : 'negative');
    el.innerHTML = (value >= 0 ? '↑' : '↓') + ' <span>' + Math.abs(value) + '%</span>';
}

// ============================================================================
// UPDATE TREND CHART
// ============================================================================
function updateTrendChart(trend) {
    if (!trend || !trend.labels) return;
    
    chartTrend.data.labels = trend.labels;
    chartTrend.data.datasets[0].data = trend.completate;
    chartTrend.data.datasets[1].data = trend.cancellate;
    chartTrend.data.datasets[2].data = trend.noshow;
    chartTrend.update();
}

// ============================================================================
// UPDATE SPORT CHART
// ============================================================================
function updateSportChart(sport) {
    if (!sport || !sport.length) return;
    
    chartSport.data.labels = sport.map(s => s.sport);
    chartSport.data.datasets[0].data = sport.map(s => s.prenotazioni);
    chartSport.update();
}

// ============================================================================
// UPDATE HEATMAP
// ============================================================================
function updateHeatmap(heatmapData) {
    const container = document.getElementById('heatmapContainer');
    
    if (!heatmapData || !heatmapData.length) {
        container.innerHTML = '<div class="no-data">Nessun dato disponibile</div>';
        return;
    }
    
    // Giorni e ore
    const giorni = ['Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab', 'Dom'];
    const ore = ['08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21'];
    
    // Trova max per normalizzare
    let maxVal = 0;
    heatmapData.forEach(d => {
        if (d.count > maxVal) maxVal = d.count;
    });
    
    // Crea mappa
    const dataMap = {};
    heatmapData.forEach(d => {
        const key = `${d.giorno}-${d.ora}`;
        dataMap[key] = d.count;
    });
    
    // Genera HTML
    let html = '<div class="heatmap-grid">';
    
    // Header ore
    html += '<div class="heatmap-corner"></div>';
    ore.forEach(o => {
        html += `<div class="heatmap-hour">${o}</div>`;
    });
    
    // Righe giorni
    giorni.forEach((g, gi) => {
        html += `<div class="heatmap-day">${g}</div>`;
        ore.forEach(o => {
            const key = `${gi + 1}-${o}`;
            const val = dataMap[key] || 0;
            const intensity = maxVal > 0 ? val / maxVal : 0;
            const colorClass = getHeatmapColor(intensity);
            html += `<div class="heatmap-cell ${colorClass}" title="${g} ${o}:00 - ${val} prenotazioni">${val > 0 ? val : ''}</div>`;
        });
    });
    
    html += '</div>';
    
    // Legenda
    html += `
        <div class="heatmap-legend">
            <span>Meno</span>
            <div class="heatmap-cell heat-0"></div>
            <div class="heatmap-cell heat-1"></div>
            <div class="heatmap-cell heat-2"></div>
            <div class="heatmap-cell heat-3"></div>
            <div class="heatmap-cell heat-4"></div>
            <span>Più</span>
        </div>
    `;
    
    container.innerHTML = html;
}

function getHeatmapColor(intensity) {
    if (intensity === 0) return 'heat-0';
    if (intensity < 0.25) return 'heat-1';
    if (intensity < 0.5) return 'heat-2';
    if (intensity < 0.75) return 'heat-3';
    return 'heat-4';
}

// ============================================================================
// UPDATE CAMPI LIST
// ============================================================================
function updateCampiList(campi) {
    const container = document.getElementById('campiList');
    
    if (!campi || !campi.length) {
        container.innerHTML = '<div class="no-data">Nessun dato disponibile</div>';
        return;
    }
    
    let html = '';
    campi.forEach(campo => {
        const percentuale = campo.percentuale || 0;
        const barColor = percentuale > 70 ? colors.green : (percentuale > 40 ? colors.orange : colors.red);
        
        html += `
            <div class="campo-item">
                <div class="campo-info">
                    <span class="campo-nome">${campo.nome}</span>
                    <span class="campo-sport">${campo.sport}</span>
                </div>
                <div class="campo-stats">
                    <span class="campo-prenotazioni">${campo.prenotazioni} pren.</span>
                    <span class="campo-ore">${campo.ore_utilizzate}h</span>
                </div>
                <div class="campo-bar-container">
                    <div class="campo-bar" style="width: ${percentuale}%; background: ${barColor}"></div>
                </div>
                <div class="campo-percentuale">${percentuale.toFixed(0)}%</div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// ============================================================================
// SHOW LOADING
// ============================================================================
function showLoading(show) {
    // Per ora usiamo solo CSS per indicare loading
    document.body.style.cursor = show ? 'wait' : 'default';
}

// ============================================================================
// EXPORT CSV
// ============================================================================
function exportCSV() {
    const params = new URLSearchParams({
        ajax: 1,
        action: 'export_csv',
        periodo: periodoAttivo,
        data_inizio: dataInizio,
        data_fine: dataFine
    });
    
    fetch('analytics.php?' + params.toString())
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                downloadCSV(result.csv, result.filename);
            } else {
                alert('Errore durante l\'export');
            }
        })
        .catch(error => {
            console.error('Errore:', error);
            alert('Errore durante l\'export');
        });
}

function exportCampiCSV() {
    const params = new URLSearchParams({
        ajax: 1,
        action: 'export_campi_csv',
        periodo: periodoAttivo,
        data_inizio: dataInizio,
        data_fine: dataFine
    });
    
    fetch('analytics.php?' + params.toString())
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                downloadCSV(result.csv, result.filename);
            } else {
                alert('Errore durante l\'export');
            }
        })
        .catch(error => {
            console.error('Errore:', error);
            alert('Errore durante l\'export');
        });
}

function downloadCSV(csv, filename) {
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    link.click();
}
</script>