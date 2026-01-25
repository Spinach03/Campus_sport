<!-- ============================================================================
     CONFIGURAZIONE SISTEMA - Campus Sports Arena Admin
     ============================================================================ -->

<?php
// Estrai variabili
$regole = $templateParams['regole'] ?? [];
$templates = $templateParams['templates'] ?? [];
$oreReminder = $templateParams['ore_reminder'] ?? 48;
$giorniChiusura = $templateParams['giorni_chiusura'] ?? [];

// Helper per tipo template
function getTemplateIcon($tipo) {
    $icons = [
        'conferma_prenotazione' => '✅',
        'reminder_prenotazione' => '⏰',
        'cancellazione_prenotazione' => '❌'
    ];
    return $icons[$tipo] ?? '📧';
}

function getTemplateLabel($tipo) {
    $labels = [
        'conferma_prenotazione' => 'Conferma Prenotazione',
        'reminder_prenotazione' => 'Reminder Prima della Prenotazione',
        'cancellazione_prenotazione' => 'Cancellazione Prenotazione'
    ];
    return $labels[$tipo] ?? ucfirst(str_replace('_', ' ', $tipo));
}

function getTemplateDescription($tipo) {
    $descriptions = [
        'conferma_prenotazione' => 'Inviato automaticamente subito dopo la conferma della prenotazione',
        'reminder_prenotazione' => 'Inviato automaticamente prima della data della prenotazione',
        'cancellazione_prenotazione' => 'Inviato automaticamente quando una prenotazione viene cancellata'
    ];
    return $descriptions[$tipo] ?? '';
}

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
    <button class="tab-btn" data-tab="templates" onclick="switchTab('templates')">
        📧 Template Notifiche
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
                <h3>Regole Prenotazione</h3>
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
                            <label class="config-label">Giorni Massimi Anticipo</label>
                            <p class="config-description">Con quanti giorni di anticipo gli utenti possono prenotare un campo. Es: se imposti 7, gli utenti potranno prenotare fino a 7 giorni in anticipo.</p>
                        </div>
                    </div>
                    <div class="config-input-group">
                        <input type="number" name="giorni_anticipo_max" class="config-input" 
                               value="<?= $regole['giorni_anticipo_max'] ?? 7 ?>" min="1" max="60">
                        <span class="config-unit">giorni</span>
                    </div>
                </div>
                
                <!-- Ore Anticipo Cancellazione -->
                <div class="config-item">
                    <div class="config-item-header">
                        <span class="config-item-icon">🚫</span>
                        <div>
                            <label class="config-label">Ore Anticipo Cancellazione</label>
                            <p class="config-description">Quante ore prima della prenotazione l'utente può ancora cancellarla. Es: se imposti 24, l'utente non può cancellare se mancano meno di 24 ore.</p>
                        </div>
                    </div>
                    <div class="config-input-group">
                        <input type="number" name="ore_anticipo_cancellazione" class="config-input" 
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
     TAB: TEMPLATE NOTIFICHE
     ============================================================================ -->
<div id="tab-templates" class="tab-content">
    <div class="config-card">
        <div class="config-card-header">
            <div class="config-card-icon">📧</div>
            <div>
                <h3>Template Notifiche</h3>
                <p>Personalizza i messaggi automatici inviati agli utenti</p>
            </div>
        </div>
        
        <!-- Configurazione Ore Reminder -->
        <div class="reminder-config">
            <div class="reminder-config-inner">
                <div class="reminder-icon">⏰</div>
                <div class="reminder-text">
                    <label class="config-label">Ore prima per il Reminder</label>
                    <p class="config-description">Quante ore prima della prenotazione inviare il promemoria all'utente</p>
                </div>
                <div class="reminder-input-group">
                    <input type="number" id="oreReminder" class="config-input" 
                           value="<?= $oreReminder ?>" min="1" max="168">
                    <span class="config-unit">ore</span>
                    <button type="button" class="btn-save-small" onclick="salvaOreReminder()">Salva</button>
                </div>
            </div>
        </div>
        
        <div class="templates-grid">
            <?php if (empty($templates)): ?>
            <div class="no-results">
                <div class="no-results-icon">📭</div>
                <h3>Nessun template configurato</h3>
                <p>I template verranno creati automaticamente.</p>
            </div>
            <?php else: ?>
            <?php foreach ($templates as $template): ?>
            <div class="template-card <?= $template['attivo'] ? '' : 'disabled' ?>">
                <div class="template-header">
                    <span class="template-icon"><?= getTemplateIcon($template['tipo']) ?></span>
                    <span class="template-status <?= $template['attivo'] ? 'active' : 'inactive' ?>">
                        <?= $template['attivo'] ? 'Attivo' : 'Disattivo' ?>
                    </span>
                </div>
                <h4 class="template-tipo-label"><?= getTemplateLabel($template['tipo']) ?></h4>
                <p class="template-desc"><?= getTemplateDescription($template['tipo']) ?></p>
                <h5 class="template-title"><?= htmlspecialchars($template['titolo_template']) ?></h5>
                <p class="template-preview"><?= htmlspecialchars(substr($template['messaggio_template'], 0, 100)) ?>...</p>
                <div class="template-footer">
                    <button class="btn-edit-template" onclick="editTemplate(<?= $template['template_id'] ?>)">
                        ✏️ Modifica Template
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
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
                <h3>Giorni di Chiusura</h3>
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
            <input type="date" id="newChiusuraData" class="config-input">
            <input type="text" id="newChiusuraMotivo" class="config-input motivo-input" placeholder="Motivo (es. Natale, Capodanno, Manutenzione...)">
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
     MODAL: MODIFICA TEMPLATE
     ============================================================================ -->
<div class="modal fade" id="modalTemplate" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="z-index: 1061;">
        <div class="modal-content config-modal" style="pointer-events: auto;">
            <div class="modal-header">
                <h5 class="modal-title">📧 Modifica Template Notifica</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formTemplate">
                    <input type="hidden" id="templateId" name="template_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Tipo Notifica</label>
                        <input type="text" id="templateTipo" class="form-control" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Titolo Notifica</label>
                        <input type="text" id="templateTitolo" name="titolo" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Messaggio</label>
                        <textarea id="templateMessaggio" name="messaggio" class="form-control" rows="6" required></textarea>
                        <small class="form-text">
                            Variabili disponibili: <code>{{user_name}}</code>, <code>{{campo}}</code>, <code>{{data}}</code>, <code>{{ora}}</code>
                        </small>
                    </div>
                    
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="templateAttivo" name="attivo">
                        <label class="form-check-label" for="templateAttivo">Notifica Attiva</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-primary" onclick="salvaTemplate()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 6px;">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Salva Template
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     JAVASCRIPT
     ============================================================================ -->
<script>
// ============================================================================
// FIX MODAL - Sposta nel body per evitare problemi z-index
// ============================================================================
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalTemplate');
    if (modal && modal.parentElement !== document.body) {
        document.body.appendChild(modal);
    }
});

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
// ORE REMINDER
// ============================================================================
function salvaOreReminder() {
    const ore = document.getElementById('oreReminder').value;
    
    const formData = new FormData();
    formData.append('ajax', '1');
    formData.append('action', 'save_ore_reminder');
    formData.append('ore_reminder', ore);
    
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
// TEMPLATE NOTIFICHE
// ============================================================================
function editTemplate(id) {
    fetch(`configurazione.php?ajax=1&action=get_template&id=${id}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const t = data.template;
                document.getElementById('templateId').value = t.template_id;
                document.getElementById('templateTipo').value = getTemplateLabel(t.tipo);
                document.getElementById('templateTitolo').value = t.titolo_template;
                document.getElementById('templateMessaggio').value = t.messaggio_template;
                document.getElementById('templateAttivo').checked = t.attivo == 1;
                
                new bootstrap.Modal(document.getElementById('modalTemplate')).show();
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(() => showToast('Errore di connessione', 'error'));
}

function getTemplateLabel(tipo) {
    const labels = {
        'conferma_prenotazione': 'Conferma Prenotazione',
        'reminder_prenotazione': 'Reminder Prima della Prenotazione',
        'cancellazione_prenotazione': 'Cancellazione Prenotazione'
    };
    return labels[tipo] || tipo;
}

function salvaTemplate() {
    const form = document.getElementById('formTemplate');
    const formData = new FormData(form);
    formData.append('ajax', '1');
    formData.append('action', 'save_template');
    
    if (!document.getElementById('templateAttivo').checked) {
        formData.delete('attivo');
    }
    
    fetch('configurazione.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        showToast(data.message, data.success ? 'success' : 'error');
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalTemplate')).hide();
            setTimeout(() => location.reload(), 1000);
        }
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