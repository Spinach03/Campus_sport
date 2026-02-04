<!-- ============================================================================
     CONFIGURAZIONE SISTEMA - Campus Sports Arena Admin
     ============================================================================ -->

<?php
// Estrai variabili
$regole = $templateParams['regole'] ?? [];
$giorniChiusura = $templateParams['giorni_chiusura'] ?? [];

// Helper per nome giorno
function getNomeGiorno($data) {
    $giorni = ['Domenica', 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato'];
    return $giorni[date('w', strtotime($data))];
}
?>

<!-- Header -->
<div class="gestione-header">
    <span class="header-icon">⚙️</span>
    <p class="page-subtitle">Configura le impostazioni del sistema</p>
</div>

<!-- ============================================================================
     TABS NAVIGATION
     ============================================================================ -->
<div class="tabs-container mb-4">
    <button class="tab-btn active" data-tab="regole" onclick="switchTab('regole')">
        📋 Regole Prenotazione
    </button>
    <button class="tab-btn" data-tab="chiusure" onclick="switchTab('chiusure')">
        📅 Giorni Chiusura
    </button>
</div>

<!-- ============================================================================
     TAB: REGOLE PRENOTAZIONE
     ============================================================================ -->
<div id="tab-regole" class="tab-content active">
    <div class="config-card">
        <div class="config-card-header">
            <div class="config-card-icon">📋</div>
            <div>
                <h2 class="h5">Regole Prenotazione</h2>
                <p>Configura i parametri che regolano le prenotazioni degli utenti</p>
            </div>
        </div>
        
        <form id="formRegole" class="config-form">
            <div class="config-grid">
                <!-- Giorni Anticipo Max -->
                <div class="config-item">
                    <div class="config-item-header">
                        <span class="config-item-icon">📅</span>
                        <div>
                            <label for="inputGiorniAnticipo" class="config-label">Giorni Massimi Anticipo</label>
                            <p class="config-description">Con quanti giorni di anticipo gli utenti possono prenotare un campo. Es: se imposti 7, gli utenti potranno prenotare fino a 7 giorni in anticipo.</p>
                        </div>
                    </div>
                    <div class="config-input-group">
                        <input type="number" id="inputGiorniAnticipo" name="giorni_anticipo_max" class="config-input" 
                               value="<?= $regole['giorni_anticipo_max'] ?? 7 ?>" min="1" max="60">
                        <span class="config-unit">giorni</span>
                    </div>
                </div>
                
                <!-- Ore Anticipo Cancellazione -->
                <div class="config-item">
                    <div class="config-item-header">
                        <span class="config-item-icon">🚫</span>
                        <div>
                            <label for="inputOreCancellazione" class="config-label">Ore Anticipo Cancellazione</label>
                            <p class="config-description">Quante ore prima della prenotazione l'utente può ancora cancellarla. Es: se imposti 24, l'utente non può cancellare se mancano meno di 24 ore.</p>
                        </div>
                    </div>
                    <div class="config-input-group">
                        <input type="number" id="inputOreCancellazione" name="ore_anticipo_cancellazione" class="config-input" 
                               value="<?= $regole['ore_anticipo_cancellazione'] ?? 24 ?>" min="1" max="72">
                        <span class="config-unit">ore</span>
                    </div>
                </div>
            </div>
            
            <div class="config-actions">
                <button type="button" class="btn-save" onclick="salvaRegole()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Salva Modifiche
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================================
     TAB: GIORNI CHIUSURA
     ============================================================================ -->
<div id="tab-chiusure" class="tab-content">
    <div class="config-card">
        <div class="config-card-header">
            <div class="config-card-icon">📅</div>
            <div>
                <h2 class="h5">Giorni di Chiusura</h2>
                <p>Festività e giorni in cui la struttura è chiusa e nessuno può prenotare</p>
            </div>
        </div>
        
        <div class="chiusure-info">
            <div class="info-box">
                <span class="info-icon">💡</span>
                <p>Aggiungi i giorni di chiusura per festività o manutenzione. In questi giorni nessun utente (e nessun admin) potrà effettuare prenotazioni su nessun campo.</p>
            </div>
        </div>
        
        <div class="add-chiusura-form">
            <input type="date" id="newChiusuraData" class="config-input" aria-label="Data di chiusura">
            <input type="text" id="newChiusuraMotivo" class="config-input motivo-input" aria-label="Motivo della chiusura" placeholder="Motivo (es. Natale, Capodanno, Manutenzione...)">
            <button type="button" class="btn-add" onclick="aggiungiChiusura()">
                <span>+</span> Aggiungi Chiusura
            </button>
        </div>
        
        <div class="chiusure-list" id="chiusureList">
            <?php if (empty($giorniChiusura)): ?>
            <div class="empty-list-message" id="emptyMessage">
                <div class="empty-icon">📭</div>
                <p>Nessun giorno di chiusura configurato</p>
            </div>
            <?php else: ?>
            <?php foreach ($giorniChiusura as $chiusura): ?>
            <div class="chiusura-item" data-id="<?= $chiusura['id'] ?>">
                <div class="chiusura-info">
                    <span class="chiusura-data"><?= date('d/m/Y', strtotime($chiusura['data'])) ?></span>
                    <span class="chiusura-giorno"><?= getNomeGiorno($chiusura['data']) ?></span>
                    <span class="chiusura-motivo"><?= htmlspecialchars($chiusura['motivo'] ?? 'Chiusura') ?></span>
                </div>
                <button class="btn-remove" onclick="rimuoviChiusura(<?= $chiusura['id'] ?>)" title="Rimuovi">✕</button>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ============================================================================
     JAVASCRIPT
     ============================================================================ -->
<script>
// ============================================================================
// TAB NAVIGATION
// ============================================================================
function switchTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    document.getElementById('tab-' + tabName).classList.add('active');
    document.querySelector(`.tab-btn[data-tab="${tabName}"]`).classList.add('active');
}

// ============================================================================
// REGOLE PRENOTAZIONE
// ============================================================================
function salvaRegole() {
    const form = document.getElementById('formRegole');
    const formData = new FormData(form);
    formData.append('ajax', '1');
    formData.append('action', 'save_regole');
    
    fetch('configurazione.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        showToast(data.message, data.success ? 'success' : 'error');
    })
    .catch(() => showToast('Errore di connessione', 'error'));
}

// ============================================================================
// GIORNI CHIUSURA
// ============================================================================
function aggiungiChiusura() {
    const data = document.getElementById('newChiusuraData').value;
    const motivo = document.getElementById('newChiusuraMotivo').value;
    
    if (!data) {
        showToast('Seleziona una data', 'error');
        return;
    }
    
    const formData = new FormData();
    formData.append('ajax', '1');
    formData.append('action', 'add_chiusura');
    formData.append('data', data);
    formData.append('motivo', motivo);
    
    fetch('configurazione.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(response => {
        showToast(response.message, response.success ? 'success' : 'error');
        if (response.success) {
            setTimeout(() => location.reload(), 1000);
        }
    })
    .catch(() => showToast('Errore di connessione', 'error'));
}

function rimuoviChiusura(id) {
    if (!confirm('Rimuovere questo giorno di chiusura?')) return;
    
    const formData = new FormData();
    formData.append('ajax', '1');
    formData.append('action', 'remove_chiusura');
    formData.append('id', id);
    
    fetch('configurazione.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        showToast(data.message, data.success ? 'success' : 'error');
        if (data.success) {
            document.querySelector(`.chiusura-item[data-id="${id}"]`).remove();
            checkEmptyList();
        }
    })
    .catch(() => showToast('Errore di connessione', 'error'));
}

function checkEmptyList() {
    const list = document.getElementById('chiusureList');
    const items = list.querySelectorAll('.chiusura-item');
    if (items.length === 0) {
        list.innerHTML = `
            <div class="empty-list-message" id="emptyMessage">
                <div class="empty-icon">📭</div>
                <p>Nessun giorno di chiusura configurato</p>
            </div>
        `;
    }
}

// ============================================================================
// TOAST NOTIFICATION
// ============================================================================
function showToast(message, type) {
    document.querySelectorAll('.toast-notification').forEach(t => t.remove());
    
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>