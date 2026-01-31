<!-- ============================================================================
     GESTIONE COMUNICAZIONI - Campus Sports Arena Admin
     ============================================================================ -->
<link rel="stylesheet" href="css/comunicazioni.css">

<?php
// Helper per iniziali
function getInitials($nome) {
    $parts = explode(' ', $nome);
    $initials = '';
    foreach ($parts as $part) {
        $initials .= strtoupper(substr($part, 0, 1));
    }
    return substr($initials, 0, 2);
}

// Target labels
$targetLabels = [
    'tutti' => 'Tutti gli Utenti',
    'attivi' => 'Utenti Attivi',
    'corso' => 'Per Corso',
    'sport' => 'Per Sport',
    'livello' => 'Per Livello',
    'custom' => 'Lista Custom',
    'direct' => 'Messaggio Diretto'
];

$stats = $templateParams['stats'] ?? ['totale' => 0, 'inviati' => 0, 'programmati' => 0, 'bozze' => 0];
?>

<!-- Header -->
<div class="gestione-header">
    <span class="header-icon">💬</span>
    <p class="page-subtitle">Gestisci comunicazioni broadcast e messaggi diretti</p>
</div>

<!-- ============================================================================
     KPI CARDS
     ============================================================================ -->
<div class="row g-3 mb-4">
    <!-- Totale Inviate -->
    <div class="col-xl-4 col-md-4 col-12">
        <div class="kpi-card" data-color="blue">
            <span class="kpi-icon">📨</span>
            <div class="kpi-value"><?= $stats['inviati'] ?? 0 ?></div>
            <div class="kpi-label">Inviate</div>
        </div>
    </div>
    
    <!-- Programmate -->
    <div class="col-xl-4 col-md-4 col-12">
        <div class="kpi-card" data-color="orange">
            <span class="kpi-icon">⏰</span>
            <div class="kpi-value"><?= $stats['programmati'] ?? 0 ?></div>
            <div class="kpi-label">Programmate</div>
        </div>
    </div>
    
    <!-- Bozze -->
    <div class="col-xl-4 col-md-4 col-12">
        <div class="kpi-card" data-color="purple">
            <span class="kpi-icon">📝</span>
            <div class="kpi-value"><?= $stats['bozze'] ?? 0 ?></div>
            <div class="kpi-label">Bozze</div>
        </div>
    </div>
</div>

<!-- ============================================================================
     TABS NAVIGATION
     ============================================================================ -->
<div class="tabs-container mb-4">
    <button class="tab-btn active" data-tab="storico" onclick="switchTab('storico')">
        📋 Storico
        <span class="tab-badge"><?= $stats['totale'] ?? 0 ?></span>
    </button>
    <button class="tab-btn" data-tab="compose" onclick="switchTab('compose')">
        ✏️ Componi Broadcast
    </button>
    <button class="tab-btn" data-tab="messaggio" onclick="switchTab('messaggio')">
        ✉️ Messaggio Diretto
    </button>
</div>

<!-- ============================================================================
     TAB: STORICO BROADCAST
     ============================================================================ -->
<div id="tab-storico" class="tab-content active">
    <div class="broadcast-grid">
        <?php if (empty($templateParams['broadcasts'])): ?>
        <div class="no-results">
            <div class="no-results-icon">📭</div>
            <h3>Nessuna comunicazione trovata</h3>
            <p>Non hai ancora inviato comunicazioni broadcast.</p>
            <button class="btn-add-new mt-3" onclick="switchTab('compose')">
                <span>+</span> Crea la prima comunicazione
            </button>
        </div>
        <?php else: ?>
        
        <?php 
        // Icone per target types (incluso direct per messaggi diretti)
        $targetIcons = [
            'tutti' => '👥',
            'attivi' => '⚡',
            'corso' => '🎓',
            'sport' => '🏆',
            'livello' => '🏅',
            'custom' => '📋',
            'direct' => '✉️'
        ];
        ?>
        <?php foreach ($templateParams['broadcasts'] as $broadcast): ?>
        <div class="broadcast-card" onclick="viewBroadcast(<?= $broadcast['broadcast_id'] ?>)">
            <!-- Header -->
            <div class="broadcast-card-header">
                <div class="broadcast-target">
                    <?= $targetIcons[$broadcast['target_type']] ?? '👥' ?>
                    <?= $targetLabels[$broadcast['target_type']] ?? $broadcast['target_type'] ?>
                </div>
                <span class="broadcast-status <?= $broadcast['stato'] ?>">
                    <?php
                    $statoIcons = ['inviato' => '✅', 'programmato' => '⏰', 'bozza' => '📝', 'fallito' => '❌'];
                    echo ($statoIcons[$broadcast['stato']] ?? '') . ' ' . ucfirst($broadcast['stato']);
                    ?>
                </span>
            </div>
            
            <!-- Body -->
            <div class="broadcast-card-body">
                <div class="broadcast-oggetto"><?= htmlspecialchars($broadcast['oggetto']) ?></div>
                <div class="broadcast-preview"><?= htmlspecialchars(mb_substr($broadcast['messaggio'], 0, 120)) ?><?= mb_strlen($broadcast['messaggio']) > 120 ? '...' : '' ?></div>
                <div class="broadcast-meta">
                    <div class="broadcast-meta-item">
                        📅 <?= date('d/m/Y H:i', strtotime($broadcast['created_at'])) ?>
                    </div>
                    <div class="broadcast-meta-item">
                        👤 <?= htmlspecialchars($broadcast['admin_nome'] ?? 'Admin') ?>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="broadcast-card-footer">
                <div class="broadcast-channels">
                    <span class="channel-badge <?= strpos($broadcast['canale'], 'in_app') !== false || $broadcast['canale'] === 'entrambi' ? 'active' : '' ?>">
                        🔔 In-App
                    </span>
                    <span class="channel-badge <?= strpos($broadcast['canale'], 'email') !== false || $broadcast['canale'] === 'entrambi' ? 'active' : '' ?>">
                        ✉️ Email
                    </span>
                </div>
                <div class="broadcast-stats">
                    <span>👥 <?= number_format($broadcast['num_destinatari']) ?></span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php endif; ?>
    </div>
</div>

<!-- ============================================================================
     TAB: COMPONI BROADCAST
     ============================================================================ -->
<div id="tab-compose" class="tab-content">
    <div class="compose-container">
        <!-- Form di composizione -->
        <div class="compose-form-card">
            <div class="compose-header">
                <h3>📣 Componi Broadcast</h3>
            </div>
            <div class="compose-body">
                <form id="broadcastForm">
                    <!-- Destinatari -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <span class="icon">👥</span> Destinatari
                        </div>
                        <div class="target-options">
                            <?php foreach ($templateParams['target_types'] as $key => $type): ?>
                            <label class="target-option <?= $key === 'tutti' ? 'selected' : '' ?>" data-target="<?= $key ?>">
                                <input type="radio" name="target_type" value="<?= $key ?>" <?= $key === 'tutti' ? 'checked' : '' ?>>
                                <span class="target-icon"><?= $type['icon'] ?></span>
                                <span class="target-label"><?= $type['label'] ?></span>
                                <span class="target-check">✓</span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Sub-filtro per Corso -->
                        <div id="subfilter-corso" class="target-subfilter">
                            <label class="form-section-title" style="font-size:13px;">🎓 Seleziona Corso</label>
                            <select class="form-select-dark" name="target_filter_corso" id="filterCorso">
                                <option value="">Tutti i corsi</option>
                                <?php foreach ($templateParams['corsi'] as $corso): ?>
                                <option value="<?= $corso['corso_id'] ?>"><?= htmlspecialchars($corso['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Oggetto -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <span class="icon">📝</span> Oggetto
                        </div>
                        <input type="text" class="form-control-dark" name="oggetto" id="broadcastOggetto" 
                               placeholder="Titolo della comunicazione (max 100 caratteri)" maxlength="100" required>
                        <div class="text-end mt-1">
                            <small class="text-muted"><span id="oggettoCount">0</span>/100</small>
                        </div>
                    </div>
                    
                    <!-- Messaggio -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <span class="icon">💬</span> Messaggio
                        </div>
                        <textarea class="form-control-dark" name="messaggio" id="broadcastMessaggio" rows="6"
                                  placeholder="Scrivi il contenuto della tua comunicazione..." required></textarea>
                    </div>
                    
                    <!-- Canale di invio -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <span class="icon">📡</span> Canale di Invio
                        </div>
                        <div class="channel-options">
                            <label class="channel-option selected">
                                <input type="checkbox" name="canale_inapp" value="1" checked>
                                <span class="channel-icon">🔔</span>
                                <span class="channel-text">Notifica In-App</span>
                            </label>
                            <label class="channel-option">
                                <input type="checkbox" name="canale_email" value="1">
                                <span class="channel-icon">✉️</span>
                                <span class="channel-text">Email</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Programmazione -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <span class="icon">⏰</span> Programmazione
                        </div>
                        <div class="schedule-options">
                            <label class="schedule-option selected">
                                <input type="radio" name="schedule" value="now" checked>
                                🚀 Invia Subito
                            </label>
                            <label class="schedule-option">
                                <input type="radio" name="schedule" value="later">
                                📅 Programma Invio
                            </label>
                        </div>
                        <div id="scheduleDatetime" class="schedule-datetime">
                            <input type="date" class="form-control-dark" name="schedule_date" id="scheduleDate" min="<?= date('Y-m-d') ?>">
                            <input type="time" class="form-control-dark" name="schedule_time" id="scheduleTime">
                            <small class="text-muted" style="width: 100%; margin-top: 5px; color: var(--text-muted);">* Data e ora sono entrambi obbligatori</small>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="form-actions">
                        <button type="button" class="btn-secondary-dark" onclick="resetForm()">🗑️ Cancella</button>
                        <button type="button" class="btn-secondary-dark" onclick="saveDraft()" style="background: rgba(139, 92, 246, 0.2); border-color: rgba(139, 92, 246, 0.3);">
                            📝 Salva Bozza
                        </button>
                        <button type="submit" class="btn-primary-gradient" id="btnInvia">
                            📨 Invia Comunicazione
                        </button>
                    </div>
                    
                    <!-- Progress bar invio -->
                    <div id="sendProgress" class="send-progress">
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" id="progressBar" style="width: 0%"></div>
                        </div>
                        <div class="progress-text" id="progressText">Invio in corso...</div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Preview -->
        <div class="preview-card">
            <div class="preview-header">
                <h4>👁️ Anteprima</h4>
                <div class="preview-tabs">
                    <button class="preview-tab active" onclick="switchPreview('inapp')">In-App</button>
                    <button class="preview-tab" onclick="switchPreview('email')">Email</button>
                </div>
            </div>
            <div class="preview-body">
                <!-- Preview In-App -->
                <div id="preview-inapp" class="preview-inapp">
                    <div class="preview-inapp-header">
                        <div class="preview-inapp-icon">🏟️</div>
                        <div class="preview-inapp-header-text">
                            <div class="preview-inapp-from">Campus Sports Arena</div>
                            <div class="preview-inapp-title" id="previewTitleInapp">Titolo comunicazione</div>
                        </div>
                    </div>
                    <div class="preview-inapp-body">
                        <div class="preview-inapp-message" id="previewMessageInapp">Il tuo messaggio apparirà qui...</div>
                        <div class="preview-inapp-time">⏱️ Adesso</div>
                    </div>
                </div>
                
                <!-- Preview Email (nascosta di default) -->
                <div id="preview-email" class="preview-email" style="display: none;">
                    <div class="preview-email-header">
                        <div class="preview-email-field">
                            <span class="preview-email-label">Da:</span>
                            <span class="preview-email-value">noreply@campus-sports.it</span>
                        </div>
                        <div class="preview-email-field">
                            <span class="preview-email-label">A:</span>
                            <span class="preview-email-value">utente@email.com</span>
                        </div>
                        <div class="preview-email-field">
                            <span class="preview-email-label">Oggetto:</span>
                            <span class="preview-email-value" id="previewSubjectEmail">Titolo comunicazione</span>
                        </div>
                    </div>
                    <div class="preview-email-body">
                        <div class="preview-email-title" id="previewTitleEmail">Titolo comunicazione</div>
                        <div class="preview-email-message" id="previewMessageEmail">Il tuo messaggio apparirà qui...</div>
                    </div>
                    <div class="preview-email-footer">
                        Campus Sports Arena - Università di Bologna<br>
                        Questa email è stata inviata automaticamente.
                    </div>
                </div>
                
                <!-- Contatore destinatari -->
                <div class="recipients-counter">
                    <span class="counter-value" id="recipientsCount">0</span>
                    <span class="counter-label">Destinatari</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     TAB: MESSAGGIO DIRETTO
     ============================================================================ -->
<div id="tab-messaggio" class="tab-content">
    <div class="compose-container">
        <div class="compose-form-card">
            <div class="compose-header">
                <h3>✉️ Messaggio Diretto a Utenti</h3>
            </div>
            <div class="compose-body">
                <form id="messageForm">
                    <!-- Ricerca Utente -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <span class="icon">👥</span> Destinatari
                            <span class="text-muted" style="font-size: 12px; font-weight: normal; margin-left: 8px;">
                                (Puoi selezionare più utenti)
                            </span>
                        </div>
                        <input type="text" class="form-control-dark" id="searchUser" 
                               placeholder="Cerca utente per nome, cognome o email...">
                        <div id="userSearchResults" class="mt-2" style="display: none;"></div>
                        
                        <!-- Lista utenti selezionati -->
                        <div id="selectedUsersContainer" class="mt-3" style="display: none;">
                            <div class="form-section-title" style="font-size: 13px; margin-bottom: 8px;">
                                <span class="icon">✅</span> Utenti Selezionati: <span id="selectedCount">0</span>
                            </div>
                            <div id="selectedUsersList" class="selected-users-list"></div>
                        </div>
                    </div>
                    
                    <!-- Oggetto -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <span class="icon">📝</span> Oggetto
                        </div>
                        <input type="text" class="form-control-dark" name="msg_oggetto" id="msgOggetto" 
                               placeholder="es: Chiarimenti su segnalazione ricevuta" required>
                    </div>
                    
                    <!-- Messaggio -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <span class="icon">💬</span> Messaggio
                        </div>
                        <textarea class="form-control-dark" name="msg_messaggio" id="msgMessaggio" rows="6"
                                  placeholder="Scrivi il tuo messaggio personale..." required></textarea>
                    </div>
                    
                    <!-- Canale -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <span class="icon">📡</span> Canale di Invio
                        </div>
                        <div class="channel-options">
                            <label class="channel-option selected">
                                <input type="checkbox" name="msg_canale_inapp" value="1" checked>
                                <span class="channel-icon">🔔</span>
                                <span class="channel-text">Notifica In-App</span>
                            </label>
                            <label class="channel-option">
                                <input type="checkbox" name="msg_canale_email" value="1">
                                <span class="channel-icon">✉️</span>
                                <span class="channel-text">Email</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="form-actions">
                        <button type="button" class="btn-secondary-dark" onclick="resetMessageForm()">🗑️ Cancella</button>
                        <button type="submit" class="btn-primary-gradient">
                            📨 Invia Messaggio
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Preview Card per Messaggio Diretto -->
        <div class="preview-card">
            <div class="preview-header">
                <h4>👁️ Anteprima</h4>
                <div class="preview-tabs">
                    <button class="preview-tab active" onclick="switchPreviewMsg('inapp')">In-App</button>
                    <button class="preview-tab" onclick="switchPreviewMsg('email')">Email</button>
                </div>
            </div>
            <div class="preview-body">
                <!-- Preview In-App -->
                <div id="preview-msg-inapp" class="preview-inapp">
                    <div class="preview-inapp-header">
                        <div class="preview-inapp-icon">🏟️</div>
                        <div class="preview-inapp-header-text">
                            <div class="preview-inapp-from">Campus Sports Arena</div>
                            <div class="preview-inapp-title" id="previewMsgTitleInapp">Titolo messaggio</div>
                        </div>
                    </div>
                    <div class="preview-inapp-body">
                        <div class="preview-inapp-message" id="previewMsgMessageInapp">Il tuo messaggio apparirà qui...</div>
                        <div class="preview-inapp-time">⏱️ Adesso</div>
                    </div>
                </div>
                
                <!-- Preview Email (nascosta di default) -->
                <div id="preview-msg-email" class="preview-email" style="display: none;">
                    <div class="preview-email-header">
                        <div class="preview-email-field">
                            <span class="preview-email-label">Da:</span>
                            <span class="preview-email-value">noreply@campus-sports.it</span>
                        </div>
                        <div class="preview-email-field">
                            <span class="preview-email-label">A:</span>
                            <span class="preview-email-value" id="previewMsgRecipients">Seleziona destinatari...</span>
                        </div>
                        <div class="preview-email-field">
                            <span class="preview-email-label">Oggetto:</span>
                            <span class="preview-email-value" id="previewMsgSubjectEmail">Titolo messaggio</span>
                        </div>
                    </div>
                    <div class="preview-email-body">
                        <div class="preview-email-title" id="previewMsgTitleEmail">Titolo messaggio</div>
                        <div class="preview-email-message" id="previewMsgMessageEmail">Il tuo messaggio apparirà qui...</div>
                    </div>
                    <div class="preview-email-footer">
                        Campus Sports Arena - Università di Bologna<br>
                        Questa email è stata inviata automaticamente.
                    </div>
                </div>
                
                <!-- Contatore destinatari -->
                <div class="recipients-counter">
                    <span class="counter-value" id="msgRecipientsCount">0</span>
                    <span class="counter-label">Destinatari Selezionati</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL: DETTAGLIO BROADCAST
     ============================================================================ -->
<div class="modal fade" id="modalDettaglioBroadcast" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="z-index: 1061;">
        <div class="modal-content modal-comunicazione-content" style="pointer-events: auto;">
            <div class="modal-header">
                <h5 class="modal-title">📣 Dettaglio Broadcast</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>
            <div class="modal-body" id="broadcastDetailContent">
                <div class="loading-spinner">
                    <div class="spinner"></div>
                    <p>Caricamento...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL: CONFERMA AZIONE
     ============================================================================ -->
<div class="modal fade" id="modalConferma" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered" style="z-index: 1071;">
        <div class="modal-content modal-comunicazione-content" style="pointer-events: auto;">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfermaTitle">⚠️ Conferma</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div id="modalConfermaIcon" style="font-size: 48px; margin-bottom: 16px;">⚠️</div>
                <p id="modalConfermaMessage" style="font-size: 16px; color: var(--text-primary); margin: 0;"></p>
            </div>
            <div class="modal-footer" style="justify-content: center; gap: 12px; border-top: 1px solid rgba(255,255,255,0.1);">
                <button type="button" class="btn-secondary-dark" data-bs-dismiss="modal" style="padding: 10px 24px;">
                    Annulla
                </button>
                <button type="button" class="btn-primary-gradient" id="modalConfermaBtn" style="padding: 10px 24px;">
                    Conferma
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="toast-notification"></div>

<!-- ============================================================================
     JAVASCRIPT
     ============================================================================ -->
<script>
// ============================================================================
// FIX MODAL - Sposta nel body per evitare problemi z-index
// ============================================================================
(function() {
    const modalsToMove = ['modalDettaglioBroadcast', 'modalConferma'];
    modalsToMove.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });
})();

// ============================================================================
// TAB SWITCHING
// ============================================================================
function switchTab(tabId) {
    // Rimuovi active da tutti i tab e contenuti
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    
    // Attiva il tab selezionato
    document.querySelector(`[data-tab="${tabId}"]`).classList.add('active');
    document.getElementById(`tab-${tabId}`).classList.add('active');
    
    // Se è il tab compose, aggiorna il conteggio destinatari
    if (tabId === 'compose') {
        updateRecipientsCount();
    }
}

// ============================================================================
// TARGET SELECTION
// ============================================================================
document.querySelectorAll('.target-option').forEach(option => {
    option.addEventListener('click', function() {
        // Rimuovi selected da tutte le opzioni
        document.querySelectorAll('.target-option').forEach(o => o.classList.remove('selected'));
        this.classList.add('selected');
        this.querySelector('input').checked = true;
        
        // Nascondi tutti i subfilter
        document.querySelectorAll('.target-subfilter').forEach(sf => sf.classList.remove('active'));
        
        // Mostra il subfilter corrispondente
        const target = this.dataset.target;
        const subfilter = document.getElementById(`subfilter-${target}`);
        if (subfilter) {
            subfilter.classList.add('active');
        }
        
        // Aggiorna conteggio
        updateRecipientsCount();
    });
});

// ============================================================================
// CHANNEL SELECTION
// ============================================================================
document.querySelectorAll('.channel-option').forEach(option => {
    const checkbox = option.querySelector('input[type="checkbox"]');
    if (checkbox) {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                option.classList.add('selected');
            } else {
                option.classList.remove('selected');
            }
        });
    }
});

// ============================================================================
// SCHEDULE OPTIONS
// ============================================================================
document.querySelectorAll('.schedule-option').forEach(option => {
    option.addEventListener('click', function() {
        document.querySelectorAll('.schedule-option').forEach(o => o.classList.remove('selected'));
        this.classList.add('selected');
        this.querySelector('input').checked = true;
        
        const scheduleDatetime = document.getElementById('scheduleDatetime');
        if (this.querySelector('input').value === 'later') {
            scheduleDatetime.classList.add('active');
        } else {
            scheduleDatetime.classList.remove('active');
        }
    });
});

// ============================================================================
// PREVIEW UPDATE
// ============================================================================
const oggettoInput = document.getElementById('broadcastOggetto');
const messaggioInput = document.getElementById('broadcastMessaggio');
const oggettoCount = document.getElementById('oggettoCount');

if (oggettoInput) {
    oggettoInput.addEventListener('input', function() {
        oggettoCount.textContent = this.value.length;
        document.getElementById('previewTitleInapp').textContent = this.value || 'Titolo comunicazione';
        document.getElementById('previewTitleEmail').textContent = this.value || 'Titolo comunicazione';
        document.getElementById('previewSubjectEmail').textContent = this.value || 'Titolo comunicazione';
    });
}

if (messaggioInput) {
    messaggioInput.addEventListener('input', function() {
        document.getElementById('previewMessageInapp').textContent = this.value || 'Il tuo messaggio apparirà qui...';
        document.getElementById('previewMessageEmail').textContent = this.value || 'Il tuo messaggio apparirà qui...';
    });
}

// ============================================================================
// PREVIEW TAB SWITCH
// ============================================================================
function switchPreview(type) {
    document.querySelectorAll('.preview-tab').forEach(tab => tab.classList.remove('active'));
    document.querySelector(`.preview-tab[onclick="switchPreview('${type}')"]`).classList.add('active');
    
    document.getElementById('preview-inapp').style.display = type === 'inapp' ? 'block' : 'none';
    document.getElementById('preview-email').style.display = type === 'email' ? 'block' : 'none';
}

// ============================================================================
// UPDATE RECIPIENTS COUNT
// ============================================================================
function updateRecipientsCount() {
    const targetType = document.querySelector('input[name="target_type"]:checked')?.value || 'tutti';
    let targetFilter = null;
    
    // Ottieni il filtro in base al tipo
    switch(targetType) {
        case 'corso':
            targetFilter = document.getElementById('filterCorso')?.value;
            break;
        case 'livello':
            targetFilter = document.getElementById('filterLivello')?.value;
            break;
    }
    
    fetch('comunicazioni.php?ajax=1', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=count_destinatari&target_type=${targetType}&target_filter=${encodeURIComponent(targetFilter || '')}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('recipientsCount').textContent = data.count.toLocaleString();
        }
    })
    .catch(err => console.error('Errore conteggio:', err));
}

// Aggiorna conteggio quando cambiano i filtri
document.getElementById('filterCorso')?.addEventListener('change', updateRecipientsCount);
document.getElementById('filterLivello')?.addEventListener('change', updateRecipientsCount);

// Debounce helper
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Carica il conteggio iniziale
updateRecipientsCount();

// ============================================================================
// SALVA BOZZA
// ============================================================================
function saveDraft() {
    const formData = new FormData();
    formData.append('action', 'save_draft');
    formData.append('oggetto', document.getElementById('broadcastOggetto').value);
    formData.append('messaggio', document.getElementById('broadcastMessaggio').value);
    formData.append('target_type', document.querySelector('input[name="target_type"]:checked').value);
    
    // Target filter
    const targetType = document.querySelector('input[name="target_type"]:checked').value;
    let targetFilter = '';
    switch(targetType) {
        case 'corso': targetFilter = document.getElementById('filterCorso').value; break;
        case 'livello': targetFilter = document.getElementById('filterLivello').value; break;
    }
    formData.append('target_filter', targetFilter);
    
    // Canali
    const canaleInapp = document.querySelector('input[name="canale_inapp"]')?.checked;
    const canaleEmail = document.querySelector('input[name="canale_email"]')?.checked;
    let canale = 'in_app';
    if (canaleInapp && canaleEmail) canale = 'entrambi';
    else if (canaleEmail) canale = 'email';
    formData.append('canale', canale);
    
    fetch('comunicazioni.php?ajax=1', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(err => {
        showToast('Errore durante il salvataggio', 'error');
    });
}

// ============================================================================
// BROADCAST FORM SUBMIT
// ============================================================================
document.getElementById('broadcastForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Verifica se stiamo aggiornando una comunicazione programmata o una bozza
    const scheduledId = this.dataset.scheduledId;
    const draftId = this.dataset.draftId;
    
    const formData = new FormData();
    
    // Determina l'azione
    if (scheduledId) {
        formData.append('action', 'update_scheduled');
        formData.append('id', scheduledId);
    } else if (draftId) {
        formData.append('action', 'send_broadcast');
    } else {
        formData.append('action', 'send_broadcast');
    }
    
    formData.append('oggetto', document.getElementById('broadcastOggetto').value);
    formData.append('messaggio', document.getElementById('broadcastMessaggio').value);
    formData.append('target_type', document.querySelector('input[name="target_type"]:checked').value);
    
    // Target filter
    const targetType = document.querySelector('input[name="target_type"]:checked').value;
    let targetFilter = '';
    switch(targetType) {
        case 'corso': targetFilter = document.getElementById('filterCorso').value; break;
        case 'livello': targetFilter = document.getElementById('filterLivello').value; break;
    }
    formData.append('target_filter', targetFilter);
    
    // Canali
    const canaleInapp = document.querySelector('input[name="canale_inapp"]')?.checked;
    const canaleEmail = document.querySelector('input[name="canale_email"]')?.checked;
    let canale = 'in_app';
    if (canaleInapp && canaleEmail) canale = 'entrambi';
    else if (canaleEmail) canale = 'email';
    formData.append('canale', canale);
    
    // Schedule
    const scheduleChecked = document.querySelector('input[name="schedule"]:checked');
    const schedule = scheduleChecked ? scheduleChecked.value : 'now';
    if (schedule === 'later') {
        const date = document.getElementById('scheduleDate').value;
        const time = document.getElementById('scheduleTime').value;
        
        // Validazione: entrambi data e ora sono obbligatori
        if (!date || !time) {
            showToast('Per programmare l\'invio devi inserire sia la data che l\'ora', 'error');
            return;
        }
        
        formData.append('scheduled_at', `${date} ${time}:00`);
    }
    
    // Mostra progress
    const progress = document.getElementById('sendProgress');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    progress.classList.add('active');
    document.getElementById('btnInvia').disabled = true;
    
    // Simula progress
    let percent = 0;
    const progressInterval = setInterval(() => {
        percent += 10;
        if (percent <= 90) {
            progressBar.style.width = percent + '%';
            progressText.textContent = scheduledId ? `Aggiornamento in corso... ${percent}%` : `Invio in corso... ${percent}%`;
        }
    }, 200);
    
    fetch('comunicazioni.php?ajax=1', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        clearInterval(progressInterval);
        progressBar.style.width = '100%';
        
        if (data.success) {
            progressText.textContent = `✅ ${data.message}`;
            showToast(data.message, 'success');
            
            // Pulisci i dataset
            delete document.getElementById('broadcastForm').dataset.scheduledId;
            delete document.getElementById('broadcastForm').dataset.draftId;
            
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            progressText.textContent = `❌ ${data.message}`;
            showToast(data.message, 'error');
            document.getElementById('btnInvia').disabled = false;
        }
    })
    .catch(err => {
        clearInterval(progressInterval);
        progress.classList.remove('active');
        document.getElementById('btnInvia').disabled = false;
        showToast('Errore durante l\'invio', 'error');
    });
});

// ============================================================================
// MESSAGE FORM (DIRETTO)
// ============================================================================
let searchTimeout;
const searchUserInput = document.getElementById('searchUser');

if (searchUserInput) {
    searchUserInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            document.getElementById('userSearchResults').style.display = 'none';
            return;
        }
        
        searchTimeout = setTimeout(() => {
            fetch(`comunicazioni.php?ajax=1&action=search_users&query=${encodeURIComponent(query)}`)
            .then(r => r.json())
            .then(data => {
                if (data.success && data.users.length > 0) {
                    let html = '<div class="templates-grid" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 8px;">';
                    data.users.forEach(user => {
                        // Controlla se l'utente è già selezionato
                        const isSelected = selectedUsers.some(u => u.id === user.user_id);
                        const selectedClass = isSelected ? 'opacity: 0.5; pointer-events: none;' : '';
                        html += `
                            <div class="template-card" style="padding: 12px; cursor: pointer; ${selectedClass}" onclick="addUser(${user.user_id}, '${user.nome.replace(/'/g, "\\'")} ${user.cognome.replace(/'/g, "\\'")}', '${user.email}')">
                                <div style="font-weight: 600;">${user.nome} ${user.cognome}</div>
                                <div style="font-size: 12px; color: var(--text-muted);">${user.email}</div>
                                ${isSelected ? '<span style="color: #10B981; font-size: 11px;">✓ Già selezionato</span>' : ''}
                            </div>
                        `;
                    });
                    html += '</div>';
                    document.getElementById('userSearchResults').innerHTML = html;
                    document.getElementById('userSearchResults').style.display = 'block';
                } else {
                    document.getElementById('userSearchResults').innerHTML = '<p class="text-muted" style="font-size: 13px;">Nessun utente trovato</p>';
                    document.getElementById('userSearchResults').style.display = 'block';
                }
            });
        }, 300);
    });
}

// Array per utenti selezionati
let selectedUsers = [];

function addUser(id, nome, email) {
    // Controlla se già selezionato
    if (selectedUsers.some(u => u.id === id)) {
        showToast('Utente già selezionato', 'warning');
        return;
    }
    
    selectedUsers.push({ id, nome, email });
    updateSelectedUsersList();
    
    // Nascondi risultati ricerca e pulisci input
    document.getElementById('searchUser').value = '';
    document.getElementById('userSearchResults').style.display = 'none';
}

function removeUser(id) {
    selectedUsers = selectedUsers.filter(u => u.id !== id);
    updateSelectedUsersList();
}

function updateSelectedUsersList() {
    const container = document.getElementById('selectedUsersContainer');
    const list = document.getElementById('selectedUsersList');
    const count = document.getElementById('selectedCount');
    
    if (selectedUsers.length === 0) {
        container.style.display = 'none';
        updateMsgRecipientsPreview(); // Aggiorna preview
        return;
    }
    
    container.style.display = 'block';
    count.textContent = selectedUsers.length;
    
    let html = '';
    selectedUsers.forEach(user => {
        html += `
            <div class="selected-user-item" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 12px; background: rgba(59, 130, 246, 0.1); border-radius: 8px; margin-bottom: 8px;">
                <div>
                    <span style="font-weight: 600;">👤 ${user.nome}</span>
                    <span style="font-size: 12px; color: var(--text-muted); margin-left: 8px;">${user.email}</span>
                </div>
                <button type="button" onclick="removeUser(${user.id})" style="background: rgba(239, 68, 68, 0.2); border: none; color: #EF4444; width: 24px; height: 24px; border-radius: 4px; cursor: pointer; font-size: 14px;">✕</button>
            </div>
        `;
    });
    list.innerHTML = html;
    
    // Aggiorna preview destinatari
    updateMsgRecipientsPreview();
}

function clearSelectedUsers() {
    selectedUsers = [];
    updateSelectedUsersList();
}

document.getElementById('messageForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Verifica utenti selezionati
    if (selectedUsers.length === 0) {
        showToast('Seleziona almeno un destinatario', 'error');
        return;
    }
    
    // Verifica campi obbligatori
    const oggetto = document.getElementById('msgOggetto').value.trim();
    const messaggio = document.getElementById('msgMessaggio').value.trim();
    
    if (!oggetto) {
        showToast('L\'oggetto è obbligatorio', 'error');
        return;
    }
    if (!messaggio) {
        showToast('Il messaggio è obbligatorio', 'error');
        return;
    }
    
    // Prepara array di ID utenti
    const userIdArray = selectedUsers.map(u => u.id);
    
    // Prepara canali
    const canali = [];
    if (document.querySelector('input[name="msg_canale_inapp"]')?.checked) canali.push('in_app');
    if (document.querySelector('input[name="msg_canale_email"]')?.checked) canali.push('email');
    if (canali.length === 0) canali.push('in_app'); // Default
    
    // Crea FormData
    const formData = new FormData();
    formData.append('action', 'send_message');
    formData.append('user_ids', JSON.stringify(userIdArray));
    formData.append('oggetto', oggetto);
    formData.append('messaggio', messaggio);
    formData.append('canali', JSON.stringify(canali));
    
    // Disabilita pulsante durante invio
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '⏳ Invio in corso...';
    
    fetch('comunicazioni.php?ajax=1', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        
        if (data.success) {
            showToast(data.message, 'success');
            resetMessageForm();
            // Ricarica la pagina dopo 1 secondo per mostrare il messaggio nello storico
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showToast(data.message || 'Errore durante l\'invio', 'error');
        }
    })
    .catch(err => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        showToast('Errore di connessione', 'error');
        console.error('Errore:', err);
    });
});

function resetMessageForm() {
    document.getElementById('messageForm').reset();
    clearSelectedUsers();
    // Reset anche preview
    updateMsgPreview();
}

// ============================================================================
// PREVIEW MESSAGGIO DIRETTO
// ============================================================================

// Aggiorna preview messaggio diretto in tempo reale
document.getElementById('msgOggetto')?.addEventListener('input', updateMsgPreview);
document.getElementById('msgMessaggio')?.addEventListener('input', updateMsgPreview);

function updateMsgPreview() {
    const oggetto = document.getElementById('msgOggetto')?.value || 'Titolo messaggio';
    const messaggio = document.getElementById('msgMessaggio')?.value || 'Il tuo messaggio apparirà qui...';
    
    // Aggiorna preview In-App
    const titleInapp = document.getElementById('previewMsgTitleInapp');
    const msgInapp = document.getElementById('previewMsgMessageInapp');
    if (titleInapp) titleInapp.textContent = oggetto || 'Titolo messaggio';
    if (msgInapp) msgInapp.textContent = messaggio || 'Il tuo messaggio apparirà qui...';
    
    // Aggiorna preview Email
    const titleEmail = document.getElementById('previewMsgTitleEmail');
    const msgEmail = document.getElementById('previewMsgMessageEmail');
    const subjectEmail = document.getElementById('previewMsgSubjectEmail');
    if (titleEmail) titleEmail.textContent = oggetto || 'Titolo messaggio';
    if (msgEmail) msgEmail.textContent = messaggio || 'Il tuo messaggio apparirà qui...';
    if (subjectEmail) subjectEmail.textContent = oggetto || 'Titolo messaggio';
    
    // Aggiorna lista destinatari nella preview email
    updateMsgRecipientsPreview();
}

function updateMsgRecipientsPreview() {
    const recipientsEl = document.getElementById('previewMsgRecipients');
    const countEl = document.getElementById('msgRecipientsCount');
    
    if (selectedUsers.length === 0) {
        if (recipientsEl) recipientsEl.textContent = 'Seleziona destinatari...';
        if (countEl) countEl.textContent = '0';
    } else if (selectedUsers.length === 1) {
        if (recipientsEl) recipientsEl.textContent = selectedUsers[0].email;
        if (countEl) countEl.textContent = '1';
    } else {
        if (recipientsEl) recipientsEl.textContent = `${selectedUsers[0].email} (+${selectedUsers.length - 1} altri)`;
        if (countEl) countEl.textContent = selectedUsers.length;
    }
}

function switchPreviewMsg(type) {
    const inappPreview = document.getElementById('preview-msg-inapp');
    const emailPreview = document.getElementById('preview-msg-email');
    const tabs = document.querySelectorAll('#tab-messaggio .preview-tab');
    
    tabs.forEach(tab => tab.classList.remove('active'));
    event.target.classList.add('active');
    
    if (type === 'inapp') {
        if (inappPreview) inappPreview.style.display = 'block';
        if (emailPreview) emailPreview.style.display = 'none';
    } else {
        if (inappPreview) inappPreview.style.display = 'none';
        if (emailPreview) emailPreview.style.display = 'block';
    }
}

// ============================================================================
// VIEW BROADCAST DETAIL
// ============================================================================
function viewBroadcast(id) {
    const modal = new bootstrap.Modal(document.getElementById('modalDettaglioBroadcast'));
    modal.show();
    
    document.getElementById('broadcastDetailContent').innerHTML = `
        <div class="loading-spinner">
            <div class="spinner"></div>
            <p>Caricamento...</p>
        </div>
    `;
    
    fetch(`comunicazioni.php?ajax=1&action=get_broadcast&id=${id}`)
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const b = data.broadcast;
            const statoColors = {
                'inviato': '#10B981',
                'programmato': '#F59E0B',
                'bozza': '#8B5CF6',
                'fallito': '#EF4444'
            };
            
            // Formatta data senza secondi
            const formatDate = (dateStr) => {
                const d = new Date(dateStr);
                return d.toLocaleDateString('it-IT') + ', ' + d.toLocaleTimeString('it-IT', {hour: '2-digit', minute: '2-digit'});
            };
            
            // Genera HTML destinatari
            let destinatariHtml = '';
            if (b.target_type === 'direct' && b.destinatari_dettaglio && b.destinatari_dettaglio.length > 0) {
                // Messaggio diretto: mostra i nomi
                destinatariHtml = `
                    <p style="margin: 0 0 8px 0; font-size: 18px; font-weight: 700; color: #3B82F6;">${b.num_destinatari} utenti</p>
                    <div style="max-height: 120px; overflow-y: auto;">
                        ${b.destinatari_dettaglio.map(d => `
                            <div style="padding: 6px 10px; background: rgba(59, 130, 246, 0.1); border-radius: 6px; margin-bottom: 4px; font-size: 13px;">
                                👤 <strong>${d.nome_completo}</strong>
                                <span style="color: var(--text-muted); margin-left: 8px;">${d.email}</span>
                            </div>
                        `).join('')}
                    </div>
                `;
            } else {
                // Broadcast: mostra tipo target
                destinatariHtml = `
                    <p style="margin: 0; font-size: 24px; font-weight: 800; color: #3B82F6;">${b.num_destinatari.toLocaleString()}</p>
                    <small style="color: var(--text-secondary);">${b.target_description || b.target_type}</small>
                `;
            }
            
            // Pulsanti azioni per bozze
            let azioniHtml = '';
            if (b.stato === 'bozza') {
                azioniHtml = `
                    <div class="dettaglio-section mt-4" style="background: rgba(139, 92, 246, 0.1); border-left: 3px solid #8B5CF6; padding: 16px;">
                        <h6 style="margin-bottom: 12px;">⚡ Azioni Bozza</h6>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <button onclick="editDraft(${b.broadcast_id})" class="btn-action-primary" style="padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600;">
                                ✏️ Modifica
                            </button>
                            <button onclick="sendDraft(${b.broadcast_id})" class="btn-action-success" style="padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; background: rgba(16, 185, 129, 0.2); color: #10B981;">
                                📨 Invia Ora
                            </button>
                            <button onclick="deleteDraft(${b.broadcast_id})" class="btn-action-danger" style="padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; background: rgba(239, 68, 68, 0.2); color: #EF4444;">
                                🗑️ Elimina
                            </button>
                        </div>
                    </div>
                `;
            } else if (b.stato === 'programmato') {
                azioniHtml = `
                    <div class="dettaglio-section mt-4" style="background: rgba(245, 158, 11, 0.1); border-left: 3px solid #F59E0B; padding: 16px;">
                        <h6 style="margin-bottom: 12px;">⚡ Azioni Programmata</h6>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <button onclick="editScheduled(${b.broadcast_id})" class="btn-action-primary" style="padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600;">
                                ✏️ Modifica
                            </button>
                            <button onclick="sendScheduledNow(${b.broadcast_id})" class="btn-action-success" style="padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; background: rgba(16, 185, 129, 0.2); color: #10B981;">
                                📨 Invia Subito
                            </button>
                            <button onclick="cancelScheduled(${b.broadcast_id})" class="btn-action-danger" style="padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; background: rgba(239, 68, 68, 0.2); color: #EF4444;">
                                ❌ Annulla
                            </button>
                        </div>
                    </div>
                `;
            }
            
            document.getElementById('broadcastDetailContent').innerHTML = `
                <div class="dettaglio-header mb-4">
                    <span class="stato-badge" style="background: ${statoColors[b.stato]}20; color: ${statoColors[b.stato]}; padding: 6px 14px; border-radius: 20px; font-weight: 600;">
                        ${b.stato.toUpperCase()}
                    </span>
                    <span class="data-badge" style="color: var(--text-secondary);">📅 ${formatDate(b.created_at)}</span>
                </div>
                
                <div class="dettaglio-section mb-3">
                    <h6>📝 Oggetto</h6>
                    <p style="font-size: 16px; font-weight: 600; color: var(--text-primary); margin: 0;">${b.oggetto}</p>
                </div>
                
                <div class="dettaglio-section mb-3">
                    <h6>💬 Messaggio</h6>
                    <div class="descrizione-box">${b.messaggio.replace(/\n/g, '<br>')}</div>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="dettaglio-section">
                            <h6>👥 Destinatari</h6>
                            ${destinatariHtml}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="dettaglio-section">
                            <h6>📡 Canale</h6>
                            <div style="display: flex; gap: 8px; margin-top: 8px;">
                                <span class="channel-badge ${b.canale === 'in_app' || b.canale === 'entrambi' ? 'active' : ''}">🔔 In-App</span>
                                <span class="channel-badge ${b.canale === 'email' || b.canale === 'entrambi' ? 'active' : ''}">✉️ Email</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                ${b.scheduled_at ? `
                <div class="dettaglio-section mt-3" style="background: rgba(245, 158, 11, 0.1); border-left: 3px solid #F59E0B;">
                    <h6>⏰ Programmato per</h6>
                    <p style="margin: 0; font-weight: 600;">${formatDate(b.scheduled_at)}</p>
                </div>
                ` : ''}
                
                ${b.sent_at ? `
                <div class="dettaglio-section mt-3" style="background: rgba(16, 185, 129, 0.1); border-left: 3px solid #10B981;">
                    <h6>✅ Inviato il</h6>
                    <p style="margin: 0; font-weight: 600;">${formatDate(b.sent_at)}</p>
                </div>
                ` : ''}
                
                ${azioniHtml}
            `;
        } else {
            document.getElementById('broadcastDetailContent').innerHTML = `
                <div class="error-message">
                    <p>❌ ${data.message}</p>
                </div>
            `;
        }
    })
    .catch(err => {
        document.getElementById('broadcastDetailContent').innerHTML = `
            <div class="error-message">
                <p>❌ Errore nel caricamento</p>
            </div>
        `;
    });
}

// Funzioni per gestione bozze
function editDraft(id) {
    // Chiudi il modal
    bootstrap.Modal.getInstance(document.getElementById('modalDettaglioBroadcast')).hide();
    
    // Carica i dati della bozza nel form
    fetch(`comunicazioni.php?ajax=1&action=get_broadcast&id=${id}`)
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const b = data.broadcast;
            
            // Vai al tab compose
            switchTab('compose');
            
            // Compila il form
            document.getElementById('broadcastOggetto').value = b.oggetto;
            document.getElementById('broadcastMessaggio').value = b.messaggio;
            
            // Seleziona target type
            document.querySelectorAll('.target-option').forEach(opt => {
                opt.classList.remove('selected');
                const radio = opt.querySelector('input[type="radio"]');
                if (radio.value === b.target_type) {
                    opt.classList.add('selected');
                    radio.checked = true;
                }
            });
            
            // Canali
            document.querySelector('input[name="canale_inapp"]').checked = (b.canale === 'in_app' || b.canale === 'entrambi');
            document.querySelector('input[name="canale_email"]').checked = (b.canale === 'email' || b.canale === 'entrambi');
            
            // Aggiorna preview
            document.getElementById('previewTitleInapp').textContent = b.oggetto;
            document.getElementById('previewMessageInapp').textContent = b.messaggio;
            document.getElementById('previewTitleEmail').textContent = b.oggetto;
            document.getElementById('previewMessageEmail').textContent = b.messaggio;
            document.getElementById('previewSubjectEmail').textContent = b.oggetto;
            document.getElementById('oggettoCount').textContent = b.oggetto.length;
            
            // Aggiorna conteggio destinatari
            updateRecipientsCount();
            
            // Salva l'ID della bozza per l'aggiornamento
            document.getElementById('broadcastForm').dataset.draftId = id;
            
            showToast('Bozza caricata. Modifica e invia!', 'info');
        }
    });
}

// ============================================================================
// MODAL DI CONFERMA
// ============================================================================
let confirmCallback = null;

function showConfirmModal(title, message, icon, buttonText, buttonClass, callback) {
    document.getElementById('modalConfermaTitle').textContent = title;
    document.getElementById('modalConfermaMessage').textContent = message;
    document.getElementById('modalConfermaIcon').textContent = icon;
    
    const btn = document.getElementById('modalConfermaBtn');
    btn.textContent = buttonText;
    btn.className = buttonClass;
    
    confirmCallback = callback;
    
    const modal = new bootstrap.Modal(document.getElementById('modalConferma'));
    modal.show();
}

// Listener per il pulsante conferma
document.getElementById('modalConfermaBtn')?.addEventListener('click', function() {
    if (confirmCallback) {
        confirmCallback();
        confirmCallback = null;
    }
    bootstrap.Modal.getInstance(document.getElementById('modalConferma')).hide();
});

function sendDraft(id) {
    showConfirmModal(
        '📨 Invia Bozza',
        'Vuoi inviare questa bozza adesso?',
        '📨',
        'Invia',
        'btn-primary-gradient',
        () => {
            fetch('comunicazioni.php?ajax=1', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=send_draft&id=${id}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    bootstrap.Modal.getInstance(document.getElementById('modalDettaglioBroadcast'))?.hide();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message, 'error');
                }
            });
        }
    );
}

function deleteDraft(id) {
    showConfirmModal(
        '🗑️ Elimina Bozza',
        'Sei sicuro di voler eliminare questa bozza? L\'azione è irreversibile.',
        '🗑️',
        'Elimina',
        'btn-danger-gradient',
        () => {
            fetch('comunicazioni.php?ajax=1', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=delete_broadcast&id=${id}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    bootstrap.Modal.getInstance(document.getElementById('modalDettaglioBroadcast'))?.hide();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message, 'error');
                }
            });
        }
    );
}

// ============================================================================
// FUNZIONI PER COMUNICAZIONI PROGRAMMATE
// ============================================================================

function editScheduled(id) {
    // Chiudi il modal
    bootstrap.Modal.getInstance(document.getElementById('modalDettaglioBroadcast')).hide();
    
    // Carica i dati della comunicazione programmata nel form
    fetch(`comunicazioni.php?ajax=1&action=get_broadcast&id=${id}`)
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const b = data.broadcast;
            
            // Vai al tab compose
            switchTab('compose');
            
            // Compila il form
            document.getElementById('broadcastOggetto').value = b.oggetto;
            document.getElementById('broadcastMessaggio').value = b.messaggio;
            
            // Seleziona target type
            document.querySelectorAll('.target-option').forEach(opt => {
                opt.classList.remove('selected');
                const radio = opt.querySelector('input[type="radio"]');
                if (radio.value === b.target_type) {
                    opt.classList.add('selected');
                    radio.checked = true;
                }
            });
            
            // Canali
            document.querySelector('input[name="canale_inapp"]').checked = (b.canale === 'in_app' || b.canale === 'entrambi');
            document.querySelector('input[name="canale_email"]').checked = (b.canale === 'email' || b.canale === 'entrambi');
            
            // Imposta data programmata se presente
            if (b.scheduled_at) {
                // Seleziona opzione "Programma"
                document.querySelectorAll('.schedule-option').forEach(o => o.classList.remove('selected'));
                const scheduleOption = document.querySelector('.schedule-option[data-schedule="later"]');
                if (scheduleOption) {
                    scheduleOption.classList.add('selected');
                }
                
                // Mostra il campo datetime e imposta il valore
                const scheduleDatetime = document.getElementById('scheduleDatetime');
                if (scheduleDatetime) {
                    scheduleDatetime.classList.add('active');
                    // Converti la data nel formato corretto per datetime-local
                    const dt = new Date(b.scheduled_at);
                    const formattedDate = dt.toISOString().slice(0, 16);
                    scheduleDatetime.querySelector('input').value = formattedDate;
                }
            }
            
            // Aggiorna preview
            document.getElementById('previewTitleInapp').textContent = b.oggetto;
            document.getElementById('previewMessageInapp').textContent = b.messaggio;
            document.getElementById('previewTitleEmail').textContent = b.oggetto;
            document.getElementById('previewMessageEmail').textContent = b.messaggio;
            document.getElementById('previewSubjectEmail').textContent = b.oggetto;
            document.getElementById('oggettoCount').textContent = b.oggetto.length;
            
            // Aggiorna conteggio destinatari
            updateRecipientsCount();
            
            // Salva l'ID della comunicazione programmata per l'aggiornamento
            document.getElementById('broadcastForm').dataset.scheduledId = id;
            delete document.getElementById('broadcastForm').dataset.draftId;
            
            showToast('Comunicazione programmata caricata. Modifica e salva!', 'info');
        }
    });
}

function sendScheduledNow(id) {
    showConfirmModal(
        '📨 Invia Subito',
        'Vuoi inviare questa comunicazione adesso ignorando la programmazione?',
        '📨',
        'Invia Ora',
        'btn-primary-gradient',
        () => {
            fetch('comunicazioni.php?ajax=1', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=send_scheduled_now&id=${id}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message + ` (${data.destinatari} destinatari)`, 'success');
                    bootstrap.Modal.getInstance(document.getElementById('modalDettaglioBroadcast'))?.hide();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message, 'error');
                }
            });
        }
    );
}

function cancelScheduled(id) {
    showConfirmModal(
        '❌ Annulla Comunicazione',
        'Sei sicuro di voler annullare questa comunicazione programmata? L\'azione è irreversibile.',
        '❌',
        'Annulla Comunicazione',
        'btn-danger-gradient',
        () => {
            fetch('comunicazioni.php?ajax=1', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=delete_broadcast&id=${id}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    bootstrap.Modal.getInstance(document.getElementById('modalDettaglioBroadcast'))?.hide();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message, 'error');
                }
            });
        }
    );
}

// ============================================================================
// RESET FORM
// ============================================================================
function resetForm() {
    document.getElementById('broadcastForm').reset();
    document.querySelectorAll('.target-option').forEach(o => o.classList.remove('selected'));
    document.querySelector('.target-option[data-target="tutti"]').classList.add('selected');
    document.querySelectorAll('.target-subfilter').forEach(sf => sf.classList.remove('active'));
    document.querySelectorAll('.channel-option').forEach(o => {
        const checkbox = o.querySelector('input[type="checkbox"]');
        if (checkbox?.name === 'canale_inapp') {
            o.classList.add('selected');
            checkbox.checked = true;
        } else {
            o.classList.remove('selected');
            if (checkbox) checkbox.checked = false;
        }
    });
    document.querySelectorAll('.schedule-option').forEach(o => o.classList.remove('selected'));
    document.querySelector('.schedule-option:first-child').classList.add('selected');
    document.getElementById('scheduleDatetime').classList.remove('active');
    document.getElementById('previewTitleInapp').textContent = 'Titolo comunicazione';
    document.getElementById('previewMessageInapp').textContent = 'Il tuo messaggio apparirà qui...';
    document.getElementById('previewTitleEmail').textContent = 'Titolo comunicazione';
    document.getElementById('previewMessageEmail').textContent = 'Il tuo messaggio apparirà qui...';
    document.getElementById('previewSubjectEmail').textContent = 'Titolo comunicazione';
    document.getElementById('oggettoCount').textContent = '0';
    updateRecipientsCount();
}

// ============================================================================
// TOAST
// ============================================================================
function showToast(message, type = 'info') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = `toast-notification toast-${type} show`;
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}
</script>