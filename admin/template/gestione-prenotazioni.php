<!-- ============================================================================
     GESTIONE PRENOTAZIONI - Campus Sports Arena Admin
     ============================================================================ -->

<?php
// Helper per iniziali
function getInitials($nome, $cognome = '') {
    $parts = $cognome ? [$nome, $cognome] : explode(' ', $nome);
    $initials = '';
    foreach ($parts as $part) {
        $initials .= strtoupper(substr($part, 0, 1));
    }
    return substr($initials, 0, 2);
}

// Helper per colori stato
function getStatoConfig($stato) {
    $config = [
        'confermata' => ['color' => '#3B82F6', 'label' => 'Confermata', 'class' => 'blue'],
        'completata' => ['color' => '#10B981', 'label' => 'Completata', 'class' => 'green'],
        'cancellata' => ['color' => '#EF4444', 'label' => 'Cancellata', 'class' => 'red'],
        'no_show' => ['color' => '#F59E0B', 'label' => 'No Show', 'class' => 'orange']
    ];
    return $config[$stato] ?? $config['confermata'];
}

// Helper per verificare se prenotazione è futura
function isFuturePrenotazione($data, $oraInizio) {
    $prenotazioneDateTime = strtotime($data . ' ' . $oraInizio);
    return $prenotazioneDateTime > time();
}

// Helper per emoji sport (converte nome file in emoji)
function getSportEmoji($icona) {
    $emojiMap = [
        'calcio5.png' => '⚽',
        'calcio7.png' => '⚽',
        'basket.png' => '🏀',
        'pallavolo.png' => '🏐',
        'tennis.png' => '🎾',
        'padel.png' => '🎾',
        'badminton.png' => '🏸',
        'pingpong.png' => '🏓'
    ];
    return $emojiMap[$icona] ?? '🏆';
}

// Estrai variabili
$stats = $templateParams['stats'] ?? ['totale' => 0, 'confermate' => 0, 'completate' => 0, 'cancellate' => 0, 'no_show' => 0];
$prenotazioni = $templateParams['prenotazioni'] ?? [];
$campi = $templateParams['campi'] ?? [];
$sport = $templateParams['sport'] ?? [];
$filtri = $templateParams['filtri'] ?? [];
?>

<!-- Header -->
<div class="gestione-header">
    <span class="header-icon">📋</span>
    <p class="page-subtitle">Gestisci le prenotazioni degli utenti</p>
    
    <!-- Bottone Nuova Prenotazione -->
    <button class="btn-add-new" onclick="apriNuovaPrenotazione()">
        <span>+</span> Nuova Prenotazione
    </button>
</div>

<!-- ============================================================================
     KPI CARDS - Stile Comunicazioni
     ============================================================================ -->
<div class="row g-3 mb-4">
    <!-- Totali -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card" data-color="purple" data-stato="">
            <span class="kpi-icon">📋</span>
            <div class="kpi-value"><?= $stats['totale'] ?? 0 ?></div>
            <div class="kpi-label">Totali</div>
        </div>
    </div>
    
    <!-- Confermate -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card" data-color="blue" data-stato="confermata">
            <span class="kpi-icon">📅</span>
            <div class="kpi-value"><?= $stats['confermate'] ?? 0 ?></div>
            <div class="kpi-label">Confermate</div>
        </div>
    </div>
    
    <!-- Completate -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card" data-color="green" data-stato="completata">
            <span class="kpi-icon">✅</span>
            <div class="kpi-value"><?= $stats['completate'] ?? 0 ?></div>
            <div class="kpi-label">Completate</div>
        </div>
    </div>
    
    <!-- Cancellate -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card" data-color="red" data-stato="cancellata">
            <span class="kpi-icon">❌</span>
            <div class="kpi-value"><?= $stats['cancellate'] ?? 0 ?></div>
            <div class="kpi-label">Cancellate</div>
        </div>
    </div>
    
    <!-- No Show -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card" data-color="orange" data-stato="no_show">
            <span class="kpi-icon">⚠️</span>
            <div class="kpi-value"><?= $stats['no_show'] ?? 0 ?></div>
            <div class="kpi-label">No Show</div>
        </div>
    </div>
    
    <!-- Oggi -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card" data-color="cyan" data-stato="oggi">
            <span class="kpi-icon">🕐</span>
            <div class="kpi-value"><?= $stats['oggi'] ?? 0 ?></div>
            <div class="kpi-label">Oggi</div>
        </div>
    </div>
</div>

<!-- ============================================================================
     FILTRI CARD
     ============================================================================ -->
<div class="filters-card mb-4">
    <!-- Riga Stato -->
    <div class="filter-row">
        <span class="filter-label">Stato:</span>
        <div class="filter-chips">
            <button type="button" class="filter-chip <?= empty($filtri['stato']) ? 'active' : '' ?>" data-stato="">
                Tutti
            </button>
            <button type="button" class="filter-chip <?= ($filtri['stato'] ?? '') === 'future' ? 'active' : '' ?>" data-stato="future">
                <span class="status-dot cyan"></span> Future
            </button>
            <button type="button" class="filter-chip <?= ($filtri['stato'] ?? '') === 'confermata' ? 'active' : '' ?>" data-stato="confermata">
                <span class="status-dot blue"></span> Confermate
            </button>
            <button type="button" class="filter-chip <?= ($filtri['stato'] ?? '') === 'completata' ? 'active' : '' ?>" data-stato="completata">
                <span class="status-dot green"></span> Completate
            </button>
            <button type="button" class="filter-chip <?= ($filtri['stato'] ?? '') === 'cancellata' ? 'active' : '' ?>" data-stato="cancellata">
                <span class="status-dot red"></span> Cancellate
            </button>
            <button type="button" class="filter-chip <?= ($filtri['stato'] ?? '') === 'no_show' ? 'active' : '' ?>" data-stato="no_show">
                <span class="status-dot orange"></span> No Show
            </button>
        </div>
    </div>
    
    <!-- Riga Filtri Aggiuntivi -->
    <div class="filter-row">
        <span class="filter-label">Filtri:</span>
        
        <!-- Ricerca Utente -->
        <div class="search-box-filter">
            <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
            </svg>
            <input type="text" id="filtroSearch" class="search-input-filter" placeholder="Cerca utente..." value="<?= htmlspecialchars($filtri['search'] ?? '') ?>">
        </div>
        
        <!-- Campo -->
        <select id="filtroCampo" class="sort-select">
            <option value="">Tutti i campi</option>
            <?php foreach ($campi as $campo): ?>
            <option value="<?= $campo['campo_id'] ?>" <?= ($filtri['campo'] ?? '') == $campo['campo_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($campo['nome']) ?>
            </option>
            <?php endforeach; ?>
        </select>
        
        <!-- Sport -->
        <select id="filtroSport" class="sort-select">
            <option value="">Tutti gli sport</option>
            <?php foreach ($sport as $s): ?>
            <option value="<?= $s['sport_id'] ?>" <?= ($filtri['sport'] ?? '') == $s['sport_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($s['nome']) ?>
            </option>
            <?php endforeach; ?>
        </select>
        
        <!-- Data -->
        <input type="date" id="filtroData" class="sort-select date-input" value="<?= $filtri['data'] ?? '' ?>">
        
        <!-- Ordinamento -->
        <select id="filtroOrdina" class="sort-select">
            <option value="recenti" <?= ($filtri['ordina'] ?? '') === 'recenti' ? 'selected' : '' ?>>Più recenti</option>
            <option value="data_asc" <?= ($filtri['ordina'] ?? '') === 'data_asc' ? 'selected' : '' ?>>Data crescente</option>
            <option value="data_desc" <?= ($filtri['ordina'] ?? '') === 'data_desc' ? 'selected' : '' ?>>Data decrescente</option>
        </select>
    </div>
</div>

<!-- ============================================================================
     GRIGLIA PRENOTAZIONI
     ============================================================================ -->
<div class="prenotazioni-grid">
    <?php if (empty($prenotazioni)): ?>
    <div class="no-results">
        <div class="no-results-icon">📭</div>
        <h3>Nessuna prenotazione trovata</h3>
        <p>Non ci sono prenotazioni che corrispondono ai filtri selezionati.</p>
    </div>
    <?php else: ?>
    
    <?php foreach ($prenotazioni as $prenotazione): 
        $statoConfig = getStatoConfig($prenotazione['stato']);
        $isFuture = isFuturePrenotazione($prenotazione['data_prenotazione'], $prenotazione['ora_inizio']);
        $canCancel = $prenotazione['stato'] === 'confermata' && $isFuture;
    ?>
    <!-- Card Prenotazione -->
    <div class="prenotazione-card <?= $canCancel ? 'future clickable' : 'past' ?>" 
         data-id="<?= $prenotazione['prenotazione_id'] ?>"
         <?php if ($canCancel): ?>
         onclick="apriCancellazione(<?= $prenotazione['prenotazione_id'] ?>, '<?= addslashes($prenotazione['campo_nome']) ?>', '<?= date('d/m/Y', strtotime($prenotazione['data_prenotazione'])) ?> <?= substr($prenotazione['ora_inizio'], 0, 5) ?>-<?= substr($prenotazione['ora_fine'], 0, 5) ?>', '<?= addslashes($prenotazione['user_nome'] . ' ' . $prenotazione['user_cognome']) ?>')"
         <?php endif; ?>>
        
        <!-- Header Card -->
        <div class="prenotazione-card-header">
            <div class="prenotazione-id">#<?= $prenotazione['prenotazione_id'] ?></div>
            <span class="stato-badge stato-<?= $statoConfig['class'] ?>">
                <?= $statoConfig['label'] ?>
            </span>
        </div>
        
        <!-- Info Campo -->
        <div class="prenotazione-campo">
            <div class="campo-icon"><?= getSportEmoji($prenotazione['sport_icona']) ?></div>
            <div class="campo-info">
                <div class="campo-nome"><?= htmlspecialchars($prenotazione['campo_nome']) ?></div>
                <div class="campo-sport"><?= htmlspecialchars($prenotazione['sport_nome']) ?></div>
            </div>
        </div>
        
        <!-- Data e Orario -->
        <div class="prenotazione-datetime">
            <div class="datetime-row">
                <span class="datetime-label">Data</span>
                <span class="datetime-value"><?= date('d/m/Y', strtotime($prenotazione['data_prenotazione'])) ?></span>
            </div>
            <div class="datetime-row">
                <span class="datetime-label">Orario</span>
                <span class="datetime-value"><?= substr($prenotazione['ora_inizio'], 0, 5) ?> - <?= substr($prenotazione['ora_fine'], 0, 5) ?></span>
            </div>
        </div>
        
        <!-- Utente -->
        <div class="prenotazione-user">
            <div class="user-avatar-small"><?= getInitials($prenotazione['user_nome'], $prenotazione['user_cognome']) ?></div>
            <div class="user-info-small">
                <div class="user-name-small"><?= htmlspecialchars($prenotazione['user_nome'] . ' ' . $prenotazione['user_cognome']) ?></div>
                <div class="user-email-small"><?= htmlspecialchars($prenotazione['user_email']) ?></div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="prenotazione-footer">
            <div class="footer-meta">
                <span class="partecipanti-count"><?= $prenotazione['num_partecipanti'] ?> partecipant<?= $prenotazione['num_partecipanti'] > 1 ? 'i' : 'e' ?></span>
                <?php if ($prenotazione['check_in_effettuato']): ?>
                <span class="check-in-done">Check-in</span>
                <?php endif; ?>
            </div>
            <?php if ($canCancel): ?>
            <span class="badge-cancellabile">Cancellabile</span>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php endif; ?>
</div>

<!-- ============================================================================
     MODAL: DETTAGLIO PRENOTAZIONE
     ============================================================================ -->
<div class="modal fade" id="modalDettaglio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content modal-prenotazione-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Dettaglio Prenotazione #<span id="modalPrenotazioneId"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>
            <div class="modal-body">
                <div id="modalContent">
                    <div class="loading-spinner">
                        <div class="spinner"></div>
                        <p>Caricamento...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL: NUOVA PRENOTAZIONE
     ============================================================================ -->
<div class="modal fade" id="modalNuovaPrenotazione" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content nuovo-campo-modal">
            <!-- Header -->
            <div class="modal-header nuovo-campo-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon">📅</div>
                    <div>
                        <h5 class="modal-title" id="modalNuovaPrenotazioneLabel">Nuova Prenotazione</h5>
                        <p class="modal-subtitle mb-0">Crea una prenotazione per un utente</p>
                    </div>
                </div>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Chiudi">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            
            <!-- Body -->
            <div class="modal-body nuovo-campo-body">
                <form id="formNuovaPrenotazione" novalidate>
                    <!-- Sezione 1: Seleziona Utente -->
                    <div class="np-section mb-4">
                        <label class="nc-label">👤 Seleziona Utente <span class="text-danger">*</span></label>
                        <div class="search-user-container">
                            <input type="text" id="searchUserInput" class="nc-input" 
                                   placeholder="Cerca per nome, cognome o email..." autocomplete="off">
                            <div id="userSearchResults" class="search-results-dropdown"></div>
                        </div>
                        <input type="hidden" id="selectedUserId" name="user_id">
                        <div id="selectedUserCard" class="selected-user-card" style="display: none;">
                            <div class="selected-user-avatar" id="selectedUserAvatar"></div>
                            <div class="selected-user-info">
                                <div class="selected-user-name" id="selectedUserName"></div>
                                <div class="selected-user-email" id="selectedUserEmail"></div>
                            </div>
                            <button type="button" class="btn-remove-user" onclick="removeSelectedUser()">✕</button>
                        </div>
                    </div>
                    
                    <!-- Sezione 2: Sport e Campo -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="nc-label">🏆 Sport <span class="text-danger">*</span></label>
                            <select id="selectSport" name="sport_id" class="nc-select" required>
                                <option value="">Seleziona sport...</option>
                                <?php foreach ($sport as $s): ?>
                                <option value="<?= $s['sport_id'] ?>"><?= getSportEmoji($s['icona']) ?> <?= htmlspecialchars($s['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="nc-label">🏟️ Campo <span class="text-danger">*</span></label>
                            <select id="selectCampo" name="campo_id" class="nc-select" required disabled>
                                <option value="">Prima seleziona uno sport...</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Sezione 3: Data e Orario -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="nc-label">📆 Data <span class="text-danger">*</span></label>
                            <input type="date" id="inputData" name="data" class="nc-input" 
                                   min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="nc-label">🕐 Ora Inizio <span class="text-danger">*</span></label>
                            <select id="selectOraInizio" name="ora_inizio" class="nc-select" required disabled>
                                <option value="">Seleziona data...</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="nc-label">🕑 Ora Fine <span class="text-danger">*</span></label>
                            <select id="selectOraFine" name="ora_fine" class="nc-select" required disabled>
                                <option value="">Seleziona ora inizio...</option>
                            </select>
                        </div>
                    </div>
                    <div id="slotsInfo" class="slots-info" style="display: none;"></div>
                    
                    <!-- Sezione 4: Dettagli -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="nc-label">👥 Numero Partecipanti <span class="text-danger">*</span></label>
                            <input type="number" name="num_partecipanti" class="nc-input" 
                                   min="1" max="30" value="2" required>
                        </div>
                        <div class="col-md-6">
                            <label class="nc-label">📝 Note (opzionale)</label>
                            <input type="text" name="note" class="nc-input" 
                                   placeholder="Note aggiuntive...">
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Footer -->
            <div class="modal-footer nuovo-campo-footer">
                <button type="button" class="nc-btn-cancel" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="nc-btn-submit" id="btnCreaPrenotazione" onclick="creaPrenotazione()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Crea Prenotazione
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL: CANCELLA PRENOTAZIONE
     ============================================================================ -->
<div class="modal fade" id="modalCancella" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-prenotazione-content">
            <div class="modal-header modal-header-danger">
                <h5 class="modal-title">Cancella Prenotazione</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>
            <div class="modal-body">
                <form id="formCancella">
                    <input type="hidden" id="cancellaPrenotazioneId" name="id">
                    
                    <div class="cancel-info-box">
                        <p>Stai per cancellare la prenotazione:</p>
                        <div class="cancel-prenotazione-info">
                            <strong id="cancelCampoNome"></strong><br>
                            <span id="cancelDataOra"></span><br>
                            <span>Utente: <strong id="cancelUserNome"></strong></span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Motivo della cancellazione</label>
                        <textarea name="motivo" class="form-control form-control-dark" rows="3" 
                                  placeholder="Inserisci il motivo della cancellazione..."></textarea>
                    </div>
                    
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="inviaNotificaCancella" name="invia_notifica" checked>
                        <label class="form-check-label" for="inviaNotificaCancella">
                            Invia notifica all'utente
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-danger" onclick="confermaCancellazione()">
                    Conferma Cancellazione
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     JAVASCRIPT
     ============================================================================ -->
<script>
let currentPrenotazioneId = null;
let searchTimeout = null;

// ============================================================================
// GESTIONE FILTRI
// ============================================================================

// Filtro stato (chips)
document.querySelectorAll('.filter-chip[data-stato]').forEach(chip => {
    chip.addEventListener('click', function() {
        document.querySelectorAll('.filter-chip[data-stato]').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        applicaFiltri();
    });
});

// KPI cards cliccabili
document.querySelectorAll('.kpi-card[data-stato]').forEach(card => {
    card.addEventListener('click', function() {
        const stato = this.dataset.stato;
        document.querySelectorAll('.filter-chip[data-stato]').forEach(c => c.classList.remove('active'));
        const chip = document.querySelector(`.filter-chip[data-stato="${stato}"]`);
        if (chip) chip.classList.add('active');
        applicaFiltri();
    });
});

// Altri filtri
document.getElementById('filtroCampo').addEventListener('change', applicaFiltri);
document.getElementById('filtroSport').addEventListener('change', applicaFiltri);
document.getElementById('filtroData').addEventListener('change', applicaFiltri);
document.getElementById('filtroOrdina').addEventListener('change', applicaFiltri);

// Ricerca con debounce
document.getElementById('filtroSearch').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applicaFiltri, 500);
});

// Ricerca con Enter
document.getElementById('filtroSearch').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        clearTimeout(searchTimeout);
        applicaFiltri();
    }
});

function applicaFiltri() {
    const params = new URLSearchParams();
    
    const statoAttivo = document.querySelector('.filter-chip[data-stato].active');
    if (statoAttivo && statoAttivo.dataset.stato) {
        params.set('stato', statoAttivo.dataset.stato);
    }
    
    const search = document.getElementById('filtroSearch').value.trim();
    if (search) params.set('search', search);
    
    const campo = document.getElementById('filtroCampo').value;
    if (campo) params.set('campo', campo);
    
    const sport = document.getElementById('filtroSport').value;
    if (sport) params.set('sport', sport);
    
    const data = document.getElementById('filtroData').value;
    if (data) params.set('data', data);
    
    const ordina = document.getElementById('filtroOrdina').value;
    if (ordina) params.set('ordina', ordina);
    
    window.location.href = 'gestione-prenotazioni.php?' + params.toString();
}

// ============================================================================
// MODAL DETTAGLIO
// ============================================================================

function apriDettaglio(id) {
    currentPrenotazioneId = id;
    document.getElementById('modalPrenotazioneId').textContent = id;
    document.getElementById('modalContent').innerHTML = '<div class="loading-spinner"><div class="spinner"></div><p>Caricamento...</p></div>';
    
    const modal = new bootstrap.Modal(document.getElementById('modalDettaglio'));
    modal.show();
    
    fetch(`gestione-prenotazioni.php?ajax=1&action=get_prenotazione&id=${id}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                renderDettaglio(data.prenotazione);
            } else {
                document.getElementById('modalContent').innerHTML = `<div class="error-message">${data.message}</div>`;
            }
        })
        .catch(() => {
            document.getElementById('modalContent').innerHTML = '<div class="error-message">Errore di connessione</div>';
        });
}

function renderDettaglio(p) {
    const statoConfig = {
        'confermata': { color: '#3B82F6', label: 'Confermata', class: 'blue' },
        'completata': { color: '#10B981', label: 'Completata', class: 'green' },
        'cancellata': { color: '#EF4444', label: 'Cancellata', class: 'red' },
        'no_show': { color: '#F59E0B', label: 'No Show', class: 'orange' }
    };
    const stato = statoConfig[p.stato] || statoConfig['confermata'];
    const isFuture = new Date(p.data_prenotazione + 'T' + p.ora_inizio) > new Date();
    const canCancel = p.stato === 'confermata' && isFuture;
    
    let html = `
        <div class="dettaglio-container">
            <!-- Header -->
            <div class="dettaglio-header">
                <span class="stato-badge-large stato-${stato.class}">${stato.label}</span>
                <span class="data-badge">Creata il ${formatDate(p.created_at)}</span>
            </div>
            
            <!-- Info Campo -->
            <div class="dettaglio-section">
                <h6>Campo</h6>
                <div class="campo-dettaglio">
                    <div class="campo-info-large">
                        <div class="campo-nome-large">${escapeHtml(p.campo_nome)}</div>
                        <div class="campo-sport-large">${escapeHtml(p.sport_nome)}</div>
                        ${p.campo_tipo ? `<div class="campo-tipo">${p.campo_tipo === 'indoor' ? 'Indoor' : 'Outdoor'}</div>` : ''}
                    </div>
                </div>
            </div>
            
            <!-- Data e Orario -->
            <div class="dettaglio-section">
                <h6>Data e Orario</h6>
                <div class="datetime-dettaglio">
                    <div class="datetime-item">
                        <span class="datetime-label">Data</span>
                        <span class="datetime-value-large">${formatDataSola(p.data_prenotazione)}</span>
                    </div>
                    <div class="datetime-item">
                        <span class="datetime-label">Orario</span>
                        <span class="datetime-value-large">${p.ora_inizio.substring(0, 5)} - ${p.ora_fine.substring(0, 5)}</span>
                    </div>
                    <div class="datetime-item">
                        <span class="datetime-label">Partecipanti</span>
                        <span class="datetime-value-large">${p.num_partecipanti}</span>
                    </div>
                </div>
            </div>
            
            <!-- Utente -->
            <div class="dettaglio-section">
                <h6>Utente</h6>
                <div class="user-dettaglio">
                    <div class="user-avatar-large">${getInitialsJS(p.user_nome, p.user_cognome)}</div>
                    <div class="user-info-large">
                        <div class="user-nome-large">${escapeHtml(p.user_nome)} ${escapeHtml(p.user_cognome)}</div>
                        <div class="user-email-large">${escapeHtml(p.user_email)}</div>
                        ${p.user_info ? `
                        <div class="user-stats-mini">
                            <span>${p.user_info.totale_prenotazioni || 0} prenotazioni</span>
                            <span>${p.user_info.no_show || 0} no-show</span>
                            <span>${p.user_info.penalty_points || 0} penalty</span>
                        </div>
                        ` : ''}
                    </div>
                </div>
            </div>
            
            ${p.note ? `
            <!-- Note -->
            <div class="dettaglio-section">
                <h6>Note</h6>
                <div class="note-box">${escapeHtml(p.note)}</div>
            </div>
            ` : ''}
            
            ${p.stato === 'cancellata' && p.motivo_cancellazione ? `
            <!-- Motivo Cancellazione -->
            <div class="dettaglio-section section-danger">
                <h6>Motivo Cancellazione</h6>
                <div class="note-box">${escapeHtml(p.motivo_cancellazione)}</div>
                ${p.cancelled_at ? `<p class="text-muted mt-2"><small>Cancellata il ${formatDate(p.cancelled_at)}</small></p>` : ''}
            </div>
            ` : ''}
            
            ${p.check_in_effettuato ? `
            <!-- Check-in -->
            <div class="dettaglio-section section-success">
                <h6>Check-in Effettuato</h6>
                <p>Orario check-in: ${p.ora_check_in ? formatDate(p.ora_check_in) : 'N/A'}</p>
            </div>
            ` : ''}
            
            <!-- Azioni -->
            ${canCancel ? `
            <div class="dettaglio-actions">
                <button class="btn btn-danger" onclick="apriCancellazione(${p.prenotazione_id}, '${escapeHtml(p.campo_nome)}', '${formatDataSola(p.data_prenotazione)} ${p.ora_inizio.substring(0, 5)}-${p.ora_fine.substring(0, 5)}', '${escapeHtml(p.user_nome)} ${escapeHtml(p.user_cognome)}')">
                    Cancella Prenotazione
                </button>
            </div>
            ` : ''}
        </div>
    `;
    
    document.getElementById('modalContent').innerHTML = html;
}

// ============================================================================
// MODAL NUOVA PRENOTAZIONE
// ============================================================================

function apriNuovaPrenotazione() {
    // Reset form
    document.getElementById('formNuovaPrenotazione').reset();
    document.getElementById('selectedUserId').value = '';
    document.getElementById('selectedUserCard').style.display = 'none';
    document.getElementById('searchUserInput').value = '';
    document.getElementById('userSearchResults').innerHTML = '';
    document.getElementById('selectCampo').innerHTML = '<option value="">Prima seleziona uno sport...</option>';
    document.getElementById('selectCampo').disabled = true;
    document.getElementById('selectOraInizio').innerHTML = '<option value="">Seleziona data...</option>';
    document.getElementById('selectOraInizio').disabled = true;
    document.getElementById('selectOraFine').innerHTML = '<option value="">Seleziona ora inizio...</option>';
    document.getElementById('selectOraFine').disabled = true;
    document.getElementById('slotsInfo').style.display = 'none';
    
    new bootstrap.Modal(document.getElementById('modalNuovaPrenotazione')).show();
}

// Ricerca utenti
document.getElementById('searchUserInput').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const search = this.value.trim();
    const resultsContainer = document.getElementById('userSearchResults');
    
    if (search.length < 2) {
        resultsContainer.innerHTML = '';
        resultsContainer.classList.remove('show');
        return;
    }
    
    searchTimeout = setTimeout(() => {
        fetch(`gestione-prenotazioni.php?ajax=1&action=search_users&search=${encodeURIComponent(search)}`)
            .then(r => r.json())
            .then(data => {
                if (data.success && data.users.length > 0) {
                    let html = '';
                    data.users.forEach(user => {
                        html += `
                            <div class="search-result-item" onclick="selectUser(${user.user_id}, '${escapeHtml(user.nome)}', '${escapeHtml(user.cognome)}', '${escapeHtml(user.email)}')">
                                <div class="search-result-avatar">${getInitialsJS(user.nome, user.cognome)}</div>
                                <div class="search-result-info">
                                    <div class="search-result-name">${escapeHtml(user.nome)} ${escapeHtml(user.cognome)}</div>
                                    <div class="search-result-email">${escapeHtml(user.email)}</div>
                                </div>
                            </div>
                        `;
                    });
                    resultsContainer.innerHTML = html;
                    resultsContainer.classList.add('show');
                } else {
                    resultsContainer.innerHTML = '<div class="no-results-message">Nessun utente trovato</div>';
                    resultsContainer.classList.add('show');
                }
            });
    }, 300);
});

// Chiudi dropdown quando si clicca fuori
document.addEventListener('click', function(e) {
    const container = document.querySelector('.search-user-container');
    const resultsContainer = document.getElementById('userSearchResults');
    if (container && !container.contains(e.target)) {
        resultsContainer.classList.remove('show');
    }
});

function selectUser(id, nome, cognome, email) {
    document.getElementById('selectedUserId').value = id;
    document.getElementById('selectedUserAvatar').textContent = getInitialsJS(nome, cognome);
    document.getElementById('selectedUserName').textContent = nome + ' ' + cognome;
    document.getElementById('selectedUserEmail').textContent = email;
    document.getElementById('selectedUserCard').style.display = 'flex';
    document.getElementById('searchUserInput').value = '';
    document.getElementById('userSearchResults').innerHTML = '';
    document.getElementById('userSearchResults').classList.remove('show');
}

function removeSelectedUser() {
    document.getElementById('selectedUserId').value = '';
    document.getElementById('selectedUserCard').style.display = 'none';
}

// Cambio sport -> carica campi
document.getElementById('selectSport').addEventListener('change', function() {
    const sportId = this.value;
    const selectCampo = document.getElementById('selectCampo');
    
    if (!sportId) {
        selectCampo.innerHTML = '<option value="">Prima seleziona uno sport...</option>';
        selectCampo.disabled = true;
        return;
    }
    
    fetch(`gestione-prenotazioni.php?ajax=1&action=get_campi&sport_id=${sportId}`)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.campi.length > 0) {
                let html = '<option value="">Seleziona campo...</option>';
                data.campi.forEach(campo => {
                    html += `<option value="${campo.campo_id}">${campo.nome} (${campo.tipo})</option>`;
                });
                selectCampo.innerHTML = html;
                selectCampo.disabled = false;
            } else {
                selectCampo.innerHTML = '<option value="">Nessun campo disponibile</option>';
                selectCampo.disabled = true;
            }
        });
});

// Cambio campo o data -> carica slot
document.getElementById('selectCampo').addEventListener('change', caricaSlot);
document.getElementById('inputData').addEventListener('change', caricaSlot);

function caricaSlot() {
    const campoId = document.getElementById('selectCampo').value;
    const data = document.getElementById('inputData').value;
    const selectOraInizio = document.getElementById('selectOraInizio');
    const selectOraFine = document.getElementById('selectOraFine');
    
    if (!campoId || !data) {
        selectOraInizio.innerHTML = '<option value="">Seleziona campo e data...</option>';
        selectOraInizio.disabled = true;
        selectOraFine.innerHTML = '<option value="">Seleziona ora inizio...</option>';
        selectOraFine.disabled = true;
        document.getElementById('slotsInfo').style.display = 'none';
        return;
    }
    
    fetch(`gestione-prenotazioni.php?ajax=1&action=get_slots&campo_id=${campoId}&data=${data}`)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.slots.length > 0) {
                let html = '<option value="">Seleziona ora...</option>';
                data.slots.forEach(slot => {
                    html += `<option value="${slot.ora_inizio}" data-ora-fine="${slot.ora_fine}">${slot.ora_inizio.substring(0, 5)}</option>`;
                });
                selectOraInizio.innerHTML = html;
                selectOraInizio.disabled = false;
                document.getElementById('slotsInfo').innerHTML = `<span class="text-success">${data.slots.length} slot disponibili</span>`;
                document.getElementById('slotsInfo').style.display = 'block';
            } else {
                selectOraInizio.innerHTML = '<option value="">Nessuno slot disponibile</option>';
                selectOraInizio.disabled = true;
                document.getElementById('slotsInfo').innerHTML = '<span class="text-danger">Nessuno slot disponibile per questa data</span>';
                document.getElementById('slotsInfo').style.display = 'block';
            }
        });
}

// Cambio ora inizio -> imposta ora fine
document.getElementById('selectOraInizio').addEventListener('change', function() {
    const selectOraFine = document.getElementById('selectOraFine');
    const selectedOption = this.options[this.selectedIndex];
    
    if (!this.value) {
        selectOraFine.innerHTML = '<option value="">Seleziona ora inizio...</option>';
        selectOraFine.disabled = true;
        return;
    }
    
    const oraFine = selectedOption.dataset.oraFine;
    selectOraFine.innerHTML = `<option value="${oraFine}">${oraFine.substring(0, 5)}</option>`;
    selectOraFine.disabled = false;
});

function creaPrenotazione() {
    const form = document.getElementById('formNuovaPrenotazione');
    
    // Validazione
    if (!document.getElementById('selectedUserId').value) {
        showToast('Seleziona un utente', 'error');
        return;
    }
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const formData = new FormData(form);
    formData.append('ajax', '1');
    formData.append('action', 'create_prenotazione');
    
    document.getElementById('btnCreaPrenotazione').disabled = true;
    
    fetch('gestione-prenotazioni.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        showToast(data.message, data.success ? 'success' : 'error');
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalNuovaPrenotazione')).hide();
            setTimeout(() => location.reload(), 1000);
        }
        document.getElementById('btnCreaPrenotazione').disabled = false;
    })
    .catch(() => {
        showToast('Errore di connessione', 'error');
        document.getElementById('btnCreaPrenotazione').disabled = false;
    });
}

// ============================================================================
// MODAL CANCELLAZIONE
// ============================================================================

function apriCancellazione(id, campoNome, dataOra, userNome) {
    document.getElementById('cancellaPrenotazioneId').value = id;
    document.getElementById('cancelCampoNome').textContent = campoNome;
    document.getElementById('cancelDataOra').textContent = dataOra;
    document.getElementById('cancelUserNome').textContent = userNome;
    document.getElementById('formCancella').reset();
    document.getElementById('inviaNotificaCancella').checked = true;
    
    // Chiudi modal dettaglio
    bootstrap.Modal.getInstance(document.getElementById('modalDettaglio')).hide();
    
    setTimeout(() => {
        new bootstrap.Modal(document.getElementById('modalCancella')).show();
    }, 300);
}

function confermaCancellazione() {
    const form = document.getElementById('formCancella');
    const formData = new FormData(form);
    formData.append('ajax', '1');
    formData.append('action', 'cancel_prenotazione');
    
    fetch('gestione-prenotazioni.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        showToast(data.message, data.success ? 'success' : 'error');
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalCancella')).hide();
            setTimeout(() => location.reload(), 1000);
        }
    })
    .catch(() => {
        showToast('Errore di connessione', 'error');
    });
}

// ============================================================================
// UTILITIES
// ============================================================================

function getInitialsJS(nome, cognome) {
    return (nome.charAt(0) + (cognome ? cognome.charAt(0) : '')).toUpperCase();
}

function formatDate(d) {
    if (!d) return 'N/A';
    return new Date(d).toLocaleDateString('it-IT', {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
}

function formatDataSola(d) {
    if (!d) return 'N/A';
    const parts = d.split('-');
    return parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : d;
}

function escapeHtml(t) {
    if (!t) return '';
    const d = document.createElement('div');
    d.textContent = t;
    return d.innerHTML;
}

function showToast(msg, type) {
    const t = document.createElement('div');
    t.className = `toast-notification toast-${type}`;
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(() => t.classList.add('show'), 10);
    setTimeout(() => {
        t.classList.remove('show');
        setTimeout(() => t.remove(), 300);
    }, 3000);
}
</script>