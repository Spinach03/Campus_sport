<?php
// Helper per configurazione stato segnalazione
function getStatoSegnalazioneConfig($stato) {
    $config = [
        'pending' => ['label' => 'In Attesa', 'color' => 'orange', 'icon' => '⏳'],
        'resolved' => ['label' => 'Risolta', 'color' => 'green', 'icon' => '✅'],
        'rejected' => ['label' => 'Respinta', 'color' => 'red', 'icon' => '❌']
    ];
    return $config[$stato] ?? $config['pending'];
}

// Helper per configurazione tipo segnalazione
function getTipoSegnalazioneConfig($tipo) {
    $config = [
        'no_show' => ['label' => 'No Show', 'icon' => '🚫', 'color' => 'orange'],
        'comportamento_scorretto' => ['label' => 'Comportamento Scorretto', 'icon' => '😤', 'color' => 'yellow'],
        'linguaggio_offensivo' => ['label' => 'Linguaggio Offensivo', 'icon' => '🤬', 'color' => 'purple'],
        'violenza' => ['label' => 'Violenza', 'icon' => '⚠️', 'color' => 'red'],
        'altro' => ['label' => 'Altro', 'icon' => '📋', 'color' => 'blue']
    ];
    return $config[$tipo] ?? $config['altro'];
}

// Helper per configurazione azione intrapresa
function getAzioneConfig($azione) {
    $config = [
        'nessuna' => ['label' => 'Nessuna azione', 'icon' => '➖'],
        'warning' => ['label' => 'Avvertimento', 'icon' => '⚠️'],
        'penalty_points' => ['label' => 'Punti Penalty', 'icon' => '📍'],
        'sospensione' => ['label' => 'Sospensione', 'icon' => '🔒'],
        'ban' => ['label' => 'Ban', 'icon' => '⛔']
    ];
    return $config[$azione] ?? $config['nessuna'];
}

// Variabili dal controller
$stats = $templateParams["stats"] ?? ['totali' => 0, 'in_attesa' => 0, 'risolte' => 0, 'respinte' => 0];
$prenotazioniRecenti = $templateParams["prenotazioni_recenti"] ?? [];
$segnalazioni = $templateParams["segnalazioni"] ?? [];
$segnalazioniRicevute = $templateParams["segnalazioni_ricevute"] ?? [];
$tipiSegnalazione = $templateParams["tipi_segnalazione"] ?? [];
?>

<link rel="stylesheet" href="css/segnalazioni.css">

<!-- Header -->
<div class="gestione-header">
    <span class="header-icon">🚨</span>
    <p class="page-subtitle">Segnala comportamenti scorretti</p>
    
    <button class="btn-add-new" onclick="apriModalNuovaSegnalazione()">
        <span>+</span> Nuova Segnalazione
    </button>
</div>

<!-- ============================================================================
     SEZIONE: LE MIE SEGNALAZIONI
     ============================================================================ -->
<div class="section-header">
    <h3 class="section-title">
        <span class="section-icon">📋</span>
        Le Mie Segnalazioni
        <span class="section-badge"><?= count($segnalazioni) ?></span>
    </h3>
</div>

<?php if (empty($segnalazioni)): ?>
<div class="empty-state-card">
    <div class="empty-icon">📋</div>
    <h4>Nessuna segnalazione</h4>
    <p>Non hai ancora inviato segnalazioni. Puoi segnalare comportamenti scorretti di altri utenti.</p>
</div>
<?php else: ?>
<div class="row g-3">
    <?php foreach ($segnalazioni as $segnalazione): 
        $statoConfig = getStatoSegnalazioneConfig($segnalazione['stato']);
        $tipoConfig = getTipoSegnalazioneConfig($segnalazione['tipo']);
    ?>
    <div class="col-12">
        <div class="prenotazione-card segnalazione-card-item" data-segnalazione-id="<?= $segnalazione['segnalazione_id'] ?>">
            <!-- Header Card -->
            <div class="prenotazione-header">
                <div class="prenotazione-campo">
                    <span class="campo-emoji"><?= $tipoConfig['icon'] ?></span>
                    <div>
                        <h4 class="campo-nome"><?= $tipoConfig['label'] ?></h4>
                        <span class="campo-sport">Utente segnalato: <strong><?= htmlspecialchars($segnalazione['segnalato_nome']) ?></strong></span>
                    </div>
                </div>
                
                <div class="stato-badge-container">
                    <span class="stato-badge stato-<?= $statoConfig['color'] ?>">
                        <?= $statoConfig['icon'] ?> <?= $statoConfig['label'] ?>
                    </span>
                </div>
            </div>
            
            <!-- Body Card -->
            <div class="prenotazione-body">
                <p class="segnalazione-descrizione"><?= nl2br(htmlspecialchars(substr($segnalazione['descrizione'], 0, 200))) ?><?= strlen($segnalazione['descrizione']) > 200 ? '...' : '' ?></p>
                
                <?php if ($segnalazione['campo_nome']): ?>
                <div class="segnalazione-prenotazione-info">
                    <span class="info-label">Prenotazione collegata:</span>
                    <span class="info-value">
                        <?= htmlspecialchars($segnalazione['campo_nome']) ?> • 
                        <?= date('d/m/Y', strtotime($segnalazione['data_prenotazione'])) ?> • 
                        <?= substr($segnalazione['ora_inizio'], 0, 5) ?>
                    </span>
                </div>
                <?php endif; ?>
                
                <?php if ($segnalazione['stato'] === 'resolved' && $segnalazione['azione_intrapresa']): 
                    $azioneConfig = getAzioneConfig($segnalazione['azione_intrapresa']);
                ?>
                <div class="esito-box esito-resolved">
                    <div class="esito-header">
                        <span class="esito-icon">✅</span>
                        <span class="esito-label">Esito della segnalazione</span>
                    </div>
                    <div class="esito-content">
                        <span class="azione-badge"><?= $azioneConfig['icon'] ?> <?= $azioneConfig['label'] ?></span>
                        <?php if ($segnalazione['penalty_assegnati']): ?>
                        <span class="penalty-info">+<?= $segnalazione['penalty_assegnati'] ?> punti penalty</span>
                        <?php endif; ?>
                    </div>
                    <?php if ($segnalazione['note_risoluzione']): ?>
                    <p class="esito-note"><?= nl2br(htmlspecialchars($segnalazione['note_risoluzione'])) ?></p>
                    <?php endif; ?>
                </div>
                <?php elseif ($segnalazione['stato'] === 'rejected'): ?>
                <div class="esito-box esito-rejected">
                    <div class="esito-header">
                        <span class="esito-icon">❌</span>
                        <span class="esito-label">Segnalazione respinta</span>
                    </div>
                    <?php if ($segnalazione['note_risoluzione']): ?>
                    <p class="esito-note"><?= nl2br(htmlspecialchars($segnalazione['note_risoluzione'])) ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Footer Card -->
            <div class="prenotazione-footer">
                <div class="footer-meta">
                    <span class="meta-item">📅 Inviata il <?= date('d/m/Y H:i', strtotime($segnalazione['created_at'])) ?></span>
                    <?php if ($segnalazione['resolved_at']): ?>
                    <span class="meta-item">✓ Gestita il <?= date('d/m/Y', strtotime($segnalazione['resolved_at'])) ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="footer-actions">
                    <button class="btn-icon-action btn-view" onclick="vediDettaglio(<?= $segnalazione['segnalazione_id'] ?>)" title="Vedi dettagli">
                        👁️
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ============================================================================
     SEZIONE: SEGNALAZIONI RICEVUTE
     ============================================================================ -->
<div class="section-header" style="margin-top: 40px;">
    <h3 class="section-title">
        <span class="section-icon">⚠️</span>
        Segnalazioni Ricevute
        <span class="section-badge badge-ricevute"><?= count($segnalazioniRicevute) ?></span>
    </h3>
</div>

<?php if (empty($segnalazioniRicevute)): ?>
<div class="empty-state-card empty-ricevute">
    <div class="empty-icon">✅</div>
    <h4>Nessuna segnalazione ricevuta</h4>
    <p>Non hai ricevuto segnalazioni da altri utenti. Continua così!</p>
</div>
<?php else: ?>
<div class="row g-3">
    <?php foreach ($segnalazioniRicevute as $segnalazione): 
        $statoConfig = getStatoSegnalazioneConfig($segnalazione['stato']);
        $tipoConfig = getTipoSegnalazioneConfig($segnalazione['tipo']);
    ?>
    <div class="col-12">
        <div class="prenotazione-card segnalazione-ricevuta-card" data-segnalazione-id="<?= $segnalazione['segnalazione_id'] ?>">
            <!-- Header Card -->
            <div class="prenotazione-header header-ricevuta">
                <div class="prenotazione-campo">
                    <span class="campo-emoji"><?= $tipoConfig['icon'] ?></span>
                    <div>
                        <h4 class="campo-nome"><?= $tipoConfig['label'] ?></h4>
                        <span class="campo-sport">Segnalato da: <strong><?= htmlspecialchars($segnalazione['segnalante_nome']) ?></strong></span>
                    </div>
                </div>
                
                <div class="stato-badge-container">
                    <span class="stato-badge stato-<?= $statoConfig['color'] ?>">
                        <?= $statoConfig['icon'] ?> <?= $statoConfig['label'] ?>
                    </span>
                </div>
            </div>
            
            <!-- Body Card -->
            <div class="prenotazione-body">
                <p class="segnalazione-descrizione"><?= nl2br(htmlspecialchars(substr($segnalazione['descrizione'], 0, 200))) ?><?= strlen($segnalazione['descrizione']) > 200 ? '...' : '' ?></p>
                
                <?php if ($segnalazione['campo_nome']): ?>
                <div class="segnalazione-prenotazione-info info-ricevuta">
                    <span class="info-label">Prenotazione collegata:</span>
                    <span class="info-value">
                        <?= htmlspecialchars($segnalazione['campo_nome']) ?> • 
                        <?= date('d/m/Y', strtotime($segnalazione['data_prenotazione'])) ?> • 
                        <?= substr($segnalazione['ora_inizio'], 0, 5) ?>
                    </span>
                </div>
                <?php endif; ?>
                
                <?php if ($segnalazione['stato'] === 'resolved' && $segnalazione['azione_intrapresa']): 
                    $azioneConfig = getAzioneConfig($segnalazione['azione_intrapresa']);
                ?>
                <div class="esito-box esito-resolved-ricevuta">
                    <div class="esito-header">
                        <span class="esito-icon">⚠️</span>
                        <span class="esito-label">Provvedimento ricevuto</span>
                    </div>
                    <div class="esito-content">
                        <span class="azione-badge"><?= $azioneConfig['icon'] ?> <?= $azioneConfig['label'] ?></span>
                        <?php if ($segnalazione['penalty_assegnati']): ?>
                        <span class="penalty-info">+<?= $segnalazione['penalty_assegnati'] ?> punti penalty</span>
                        <?php endif; ?>
                    </div>
                    <?php if ($segnalazione['note_risoluzione']): ?>
                    <p class="esito-note"><?= nl2br(htmlspecialchars($segnalazione['note_risoluzione'])) ?></p>
                    <?php endif; ?>
                </div>
                <?php elseif ($segnalazione['stato'] === 'rejected'): ?>
                <div class="esito-box esito-rejected-ricevuta">
                    <div class="esito-header">
                        <span class="esito-icon">✅</span>
                        <span class="esito-label">Segnalazione respinta (non fondata)</span>
                    </div>
                    <?php if ($segnalazione['note_risoluzione']): ?>
                    <p class="esito-note"><?= nl2br(htmlspecialchars($segnalazione['note_risoluzione'])) ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Footer Card -->
            <div class="prenotazione-footer footer-ricevuta">
                <div class="footer-meta">
                    <span class="meta-item">📅 Ricevuta il <?= date('d/m/Y H:i', strtotime($segnalazione['created_at'])) ?></span>
                    <?php if ($segnalazione['resolved_at']): ?>
                    <span class="meta-item">✓ Gestita il <?= date('d/m/Y', strtotime($segnalazione['resolved_at'])) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ============================================================================
     MODAL NUOVA SEGNALAZIONE
     ============================================================================ -->
<div class="modal fade" id="modalNuovaSegnalazione" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="z-index: 1061;">
        <div class="modal-content modal-content-dark" style="pointer-events: auto;">
            <div class="modal-header modal-header-gradient">
                <div class="modal-header-content">
                    <span class="modal-header-icon">🚨</span>
                    <div>
                        <h5 class="modal-title">Nuova Segnalazione</h5>
                        <p class="modal-subtitle">Segnala un comportamento scorretto</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <!-- Step 1: Seleziona Prenotazione (OBBLIGATORIO) -->
                <div class="form-group">
                    <label>Prenotazione di riferimento *</label>
                    <?php if (!empty($prenotazioniRecenti)): ?>
                    <select id="prenotazioneId" class="form-control-dark" required>
                        <option value="">-- Seleziona la prenotazione --</option>
                        <?php foreach ($prenotazioniRecenti as $p): ?>
                        <option value="<?= $p['prenotazione_id'] ?>">
                            <?= htmlspecialchars($p['campo_nome']) ?> - <?= date('d/m/Y', strtotime($p['data_prenotazione'])) ?> <?= substr($p['ora_inizio'], 0, 5) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="form-hint">Seleziona la prenotazione durante la quale è avvenuto l'incidente</span>
                    <?php else: ?>
                    <div class="alert-warning-box">
                        <span class="alert-icon">⚠️</span>
                        <p>Non hai prenotazioni completate negli ultimi 15 giorni. Puoi segnalare solo incidenti avvenuti durante una tua prenotazione recente.</p>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Step 2: Cerca Utente -->
                <div class="form-group">
                    <label>Utente da segnalare *</label>
                    <div class="search-utente-container">
                        <input type="text" id="searchUtente" class="form-control-dark" placeholder="Cerca per nome o cognome..." autocomplete="off">
                        <div id="searchResults" class="search-results"></div>
                    </div>
                    <input type="hidden" id="segnalatoId">
                    <div id="utenteSelezionato" class="utente-selezionato" style="display: none;">
                        <span class="utente-nome"></span>
                        <button type="button" class="btn-remove-utente" onclick="rimuoviUtente()">✕</button>
                    </div>
                </div>
                
                <!-- Step 3: Tipo Segnalazione -->
                <div class="form-group">
                    <label>Tipo di segnalazione *</label>
                    <div class="tipo-segnalazione-grid">
                        <?php foreach ($tipiSegnalazione as $key => $tipo): ?>
                        <div class="tipo-option" data-tipo="<?= $key ?>">
                            <span class="tipo-icon"><?= $tipo['icon'] ?></span>
                            <span class="tipo-label"><?= $tipo['label'] ?></span>
                            <span class="tipo-desc"><?= $tipo['desc'] ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" id="tipoSegnalazione">
                </div>
                
                <!-- Step 4: Descrizione -->
                <div class="form-group">
                    <label for="descrizioneSegnalazione">Descrizione dettagliata *</label>
                    <textarea id="descrizioneSegnalazione" class="form-control-dark" rows="4" placeholder="Descrivi cosa è successo in modo dettagliato (minimo 20 caratteri)..." maxlength="1000"></textarea>
                    <span class="char-counter"><span id="charCount">0</span>/1000</span>
                </div>
                
                <div class="alert-info-box">
                    <span class="alert-icon">ℹ️</span>
                    <p>Le segnalazioni false o infondate possono portare a sanzioni sul tuo account. Segnala solo comportamenti realmente avvenuti.</p>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary-dark" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn-primary-gradient" onclick="inviaSegnalazione()">
                    🚨 Invia Segnalazione
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL DETTAGLIO SEGNALAZIONE
     ============================================================================ -->
<div class="modal fade" id="modalDettaglio" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="z-index: 1061;">
        <div class="modal-content modal-content-dark" style="pointer-events: auto;">
            <div class="modal-header modal-header-gradient">
                <div class="modal-header-content">
                    <span class="modal-header-icon">📋</span>
                    <div>
                        <h5 class="modal-title">Dettaglio Segnalazione</h5>
                        <p class="modal-subtitle" id="dettaglioTipo">Tipo segnalazione</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body" id="dettaglioContainer">
                <div class="text-center text-muted">Caricamento...</div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary-dark" data-bs-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     JAVASCRIPT
     ============================================================================ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // FIX MODAL - Sposta i modal nel body
    const modalsToMove = ['modalNuovaSegnalazione', 'modalDettaglio'];
    modalsToMove.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });
    
    // Char count
    document.getElementById('descrizioneSegnalazione')?.addEventListener('input', function() {
        document.getElementById('charCount').textContent = this.value.length;
    });
    
    // Search utente
    let searchTimeout;
    document.getElementById('searchUtente')?.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            document.getElementById('searchResults').innerHTML = '';
            document.getElementById('searchResults').style.display = 'none';
            return;
        }
        
        searchTimeout = setTimeout(() => cercaUtenti(query), 300);
    });
    
    // Tipo segnalazione click
    document.querySelectorAll('.tipo-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.tipo-option').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            document.getElementById('tipoSegnalazione').value = this.dataset.tipo;
        });
    });
    
    // Click outside search results
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.search-utente-container')) {
            document.getElementById('searchResults').style.display = 'none';
        }
    });
});

// ============================================================================
// CERCA UTENTI
// ============================================================================
function cercaUtenti(query) {
    const formData = new FormData();
    formData.append('action', 'cerca_utenti');
    formData.append('query', query);
    
    fetch('segnalazioni.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('searchResults');
            
            if (data.success && data.utenti.length > 0) {
                let html = '';
                data.utenti.forEach(u => {
                    html += `<div class="search-result-item" onclick="selezionaUtente(${u.user_id}, '${u.nome} ${u.cognome}')">
                        <span class="result-name">${u.nome} ${u.cognome}</span>
                        <span class="result-email">${u.email}</span>
                    </div>`;
                });
                container.innerHTML = html;
                container.style.display = 'block';
            } else {
                container.innerHTML = '<div class="search-no-results">Nessun utente trovato</div>';
                container.style.display = 'block';
            }
        });
}

function selezionaUtente(userId, nome) {
    document.getElementById('segnalatoId').value = userId;
    document.getElementById('searchUtente').style.display = 'none';
    document.getElementById('searchResults').style.display = 'none';
    
    const selezionato = document.getElementById('utenteSelezionato');
    selezionato.querySelector('.utente-nome').textContent = nome;
    selezionato.style.display = 'flex';
}

function rimuoviUtente() {
    document.getElementById('segnalatoId').value = '';
    document.getElementById('searchUtente').value = '';
    document.getElementById('searchUtente').style.display = 'block';
    document.getElementById('utenteSelezionato').style.display = 'none';
}

// ============================================================================
// APRI MODAL NUOVA SEGNALAZIONE
// ============================================================================
function apriModalNuovaSegnalazione() {
    // Reset form
    rimuoviUtente();
    document.querySelectorAll('.tipo-option').forEach(o => o.classList.remove('selected'));
    document.getElementById('tipoSegnalazione').value = '';
    document.getElementById('descrizioneSegnalazione').value = '';
    document.getElementById('charCount').textContent = '0';
    if (document.getElementById('prenotazioneId')) {
        document.getElementById('prenotazioneId').value = '';
    }
    
    new bootstrap.Modal(document.getElementById('modalNuovaSegnalazione')).show();
}

// ============================================================================
// INVIA SEGNALAZIONE
// ============================================================================
function inviaSegnalazione() {
    const prenotazioneId = document.getElementById('prenotazioneId')?.value || '';
    const segnalatoId = document.getElementById('segnalatoId').value;
    const tipo = document.getElementById('tipoSegnalazione').value;
    const descrizione = document.getElementById('descrizioneSegnalazione').value;
    
    // Validazione
    if (!prenotazioneId) {
        mostraToast('Seleziona la prenotazione di riferimento', 'warning');
        return;
    }
    
    if (!segnalatoId) {
        mostraToast('Seleziona un utente da segnalare', 'warning');
        return;
    }
    
    if (!tipo) {
        mostraToast('Seleziona un tipo di segnalazione', 'warning');
        return;
    }
    
    if (descrizione.length < 20) {
        mostraToast('La descrizione deve essere di almeno 20 caratteri', 'warning');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'crea_segnalazione');
    formData.append('prenotazione_id', prenotazioneId);
    formData.append('segnalato_id', segnalatoId);
    formData.append('tipo', tipo);
    formData.append('descrizione', descrizione);
    
    fetch('segnalazioni.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalNuovaSegnalazione')).hide();
                mostraToast('Segnalazione inviata con successo!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                mostraToast(data.error || 'Errore', 'error');
            }
        })
        .catch(() => mostraToast('Errore di connessione', 'error'));
}

// ============================================================================
// VEDI DETTAGLIO
// ============================================================================
function vediDettaglio(segnalazioneId) {
    const container = document.getElementById('dettaglioContainer');
    container.innerHTML = '<div class="text-center text-muted">Caricamento...</div>';
    
    new bootstrap.Modal(document.getElementById('modalDettaglio')).show();
    
    const formData = new FormData();
    formData.append('action', 'get_segnalazione');
    formData.append('segnalazione_id', segnalazioneId);
    
    fetch('segnalazioni.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const s = data.segnalazione;
                
                const tipoLabels = {
                    'no_show': 'No Show',
                    'comportamento_scorretto': 'Comportamento Scorretto',
                    'linguaggio_offensivo': 'Linguaggio Offensivo',
                    'violenza': 'Violenza',
                    'altro': 'Altro'
                };
                
                const statoLabels = {
                    'pending': '<span class="stato-badge stato-orange">⏳ In Attesa</span>',
                    'resolved': '<span class="stato-badge stato-green">✅ Risolta</span>',
                    'rejected': '<span class="stato-badge stato-red">❌ Respinta</span>'
                };
                
                document.getElementById('dettaglioTipo').textContent = tipoLabels[s.tipo] || s.tipo;
                
                let html = `
                    <div class="dettaglio-section">
                        <h6>Stato</h6>
                        ${statoLabels[s.stato]}
                    </div>
                    
                    <div class="dettaglio-section">
                        <h6>Utente Segnalato</h6>
                        <p>${s.segnalato_nome}</p>
                    </div>
                    
                    <div class="dettaglio-section">
                        <h6>Descrizione</h6>
                        <p class="descrizione-full">${s.descrizione.replace(/\n/g, '<br>')}</p>
                    </div>
                `;
                
                if (s.campo_nome) {
                    html += `
                        <div class="dettaglio-section">
                            <h6>Prenotazione Collegata</h6>
                            <p>${s.campo_nome} • ${new Date(s.data_prenotazione).toLocaleDateString('it-IT')} • ${s.ora_inizio.substring(0,5)}</p>
                        </div>
                    `;
                }
                
                html += `
                    <div class="dettaglio-section">
                        <h6>Data Invio</h6>
                        <p>${new Date(s.created_at).toLocaleString('it-IT')}</p>
                    </div>
                `;
                
                if (s.stato !== 'pending') {
                    const azioneLabels = {
                        'nessuna': 'Nessuna azione',
                        'warning': 'Avvertimento',
                        'penalty_points': 'Punti Penalty',
                        'sospensione': 'Sospensione',
                        'ban': 'Ban'
                    };
                    
                    html += `
                        <div class="dettaglio-section esito-section">
                            <h6>Esito</h6>
                            ${s.azione_intrapresa ? `<p><strong>Azione:</strong> ${azioneLabels[s.azione_intrapresa] || s.azione_intrapresa}</p>` : ''}
                            ${s.penalty_assegnati ? `<p><strong>Penalty assegnati:</strong> +${s.penalty_assegnati} punti</p>` : ''}
                            ${s.note_risoluzione ? `<p><strong>Note:</strong> ${s.note_risoluzione}</p>` : ''}
                            ${s.resolved_at ? `<p><strong>Data gestione:</strong> ${new Date(s.resolved_at).toLocaleString('it-IT')}</p>` : ''}
                        </div>
                    `;
                }
                
                container.innerHTML = html;
            } else {
                container.innerHTML = '<p class="text-center text-muted">Errore nel caricamento</p>';
            }
        });
}

// ============================================================================
// TOAST
// ============================================================================
function mostraToast(messaggio, tipo = 'info') {
    document.querySelectorAll('.toast-notification').forEach(t => t.remove());
    const icons = { success: '✅', error: '❌', warning: '⚠️', info: 'ℹ️' };
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${tipo}`;
    toast.innerHTML = `<span>${icons[tipo]}</span> ${messaggio}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 300); }, 3000);
}
</script>