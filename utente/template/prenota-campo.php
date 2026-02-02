<!-- ============================================================================
     PRENOTA CAMPO - Area Utente Campus Sports Arena
     Design e logica identici all'Admin Panel
     ============================================================================ -->

<link rel="stylesheet" href="css/prenota-campo.css">
<link rel="stylesheet" href="css/modal-prenota.css">

<?php
// Emoji sport mapping
function getSportEmoji($sportNome) {
    $map = [
        'calcio' => '⚽', 'calcetto' => '⚽',
        'basket' => '🏀',
        'tennis' => '🎾',
        'pallavolo' => '🏐',
        'padel' => '🎾',
        'badminton' => '🏸',
        'ping pong' => '🏓', 'pingpong' => '🏓'
    ];
    $nome = strtolower($sportNome);
    foreach ($map as $key => $emoji) {
        if (strpos($nome, $key) !== false) return $emoji;
    }
    return '🏟️';
}

// Sport colors
$sportColors = [
    'Calcio a 5' => '#10B981', 'Calcio a 7' => '#10B981', 'Calcetto' => '#10B981',
    'Basket' => '#F59E0B', 'Tennis' => '#3B82F6', 'Pallavolo' => '#8B5CF6',
    'Padel' => '#06B6D4', 'Badminton' => '#EC4899', 'Ping Pong' => '#F97316'
];

$campi = $templateParams["campi"] ?? [];
$sports = $templateParams["sports"] ?? [];
$filtri = $templateParams["filtri"] ?? ['sport' => '', 'tipo' => '', 'search' => '', 'ordina' => 'nome'];
$giorniMaxAnticipo = $templateParams["giorni_max_anticipo"] ?? 7;
$statoUtente = $templateParams["stato_utente"] ?? 'attivo';
$utenteBloccato = $templateParams["utente_bloccato"] ?? false;
?>

<!-- Header - Stile Admin -->
<div class="gestione-header">
    <span class="header-icon">🏟️</span>
    <p class="page-subtitle">Scegli un campo e prenota il tuo slot</p>
    
    <!-- Search -->
    <div class="search-box">
        <span class="search-icon">🔍</span>
        <input type="text" class="search-input" id="searchCampi" placeholder="Cerca campi..." value="<?php echo htmlspecialchars($filtri['search'] ?? ''); ?>">
    </div>
</div>

<?php if ($utenteBloccato): ?>
<!-- Alert Utente Bloccato -->
<div class="alert-bloccato mb-4">
    <div class="alert-bloccato-icon">
        <?= $statoUtente === 'bannato' ? '⛔' : '🔒' ?>
    </div>
    <div class="alert-bloccato-content">
        <h4 class="alert-bloccato-title">
            <?= $statoUtente === 'bannato' ? 'Account Bannato' : 'Account Sospeso' ?>
        </h4>
        <p class="alert-bloccato-message">
            <?php if ($statoUtente === 'bannato'): ?>
                Il tuo account è stato bannato. Non puoi effettuare nuove prenotazioni.
                Contatta l'amministrazione per maggiori informazioni.
            <?php else: ?>
                Il tuo account è temporaneamente sospeso. Non puoi effettuare nuove prenotazioni
                fino al termine della sospensione.
            <?php endif; ?>
        </p>
    </div>
</div>
<?php endif; ?>

<!-- ============================================================================
     FILTRI CARD - Stile Admin Identico
     ============================================================================ -->
<div class="filters-card mb-4">
    <!-- Riga 1: Sport -->
    <div class="filter-row">
        <span class="filter-label">Sport:</span>
        <div class="filter-chips">
            <button type="button" class="filter-chip <?php echo empty($filtri['sport']) ? 'active' : ''; ?>" data-filter="sport" data-value="">
                Tutti
            </button>
            <?php foreach ($sports as $sport): ?>
            <button type="button" class="filter-chip <?php echo ($filtri['sport'] ?? '') == $sport['sport_id'] ? 'active' : ''; ?>" data-filter="sport" data-value="<?php echo $sport['sport_id']; ?>">
                <?php echo getSportEmoji($sport['nome']); ?> <?php echo htmlspecialchars($sport['nome']); ?>
            </button>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Riga 2: Tipo e Ordina -->
    <div class="filter-row">
        <span class="filter-label">Tipo:</span>
        <div class="filter-chips">
            <button type="button" class="filter-chip <?php echo empty($filtri['tipo']) ? 'active' : ''; ?>" data-filter="tipo" data-value="">
                Tutti
            </button>
            <button type="button" class="filter-chip <?php echo ($filtri['tipo'] ?? '') == 'indoor' ? 'active' : ''; ?>" data-filter="tipo" data-value="indoor">
                🏠 Indoor
            </button>
            <button type="button" class="filter-chip <?php echo ($filtri['tipo'] ?? '') == 'outdoor' ? 'active' : ''; ?>" data-filter="tipo" data-value="outdoor">
                🌳 Outdoor
            </button>
        </div>
        
        <!-- Ordina -->
        <select id="selectOrdina" class="sort-select ms-auto">
            <option value="nome" <?php echo ($filtri['ordina'] ?? '') == 'nome' ? 'selected' : ''; ?>>Ordina: Nome</option>
            <option value="rating" <?php echo ($filtri['ordina'] ?? '') == 'rating' ? 'selected' : ''; ?>>Ordina: Rating</option>
        </select>
    </div>
</div>

<!-- ============================================================================
     SEZIONE TITOLO CAMPI
     ============================================================================ -->
<div class="section-header mb-4">
    <div class="d-flex align-items-center gap-2">
        <span class="section-icon">🏟️</span>
        <h2 class="section-title mb-0">Campi Disponibili</h2>
        <span class="section-count">(<?php echo count($campi); ?> campi)</span>
    </div>
</div>

<!-- ============================================================================
     GRIGLIA CAMPI
     ============================================================================ -->
<div class="row g-4 mb-4" id="campiGrid">
    <?php if (empty($campi)): ?>
    <div class="col-12">
        <div class="empty-state">
            <span class="empty-icon">🏟️</span>
            <h3>Nessun campo trovato</h3>
            <p>Prova a modificare i filtri di ricerca.</p>
        </div>
    </div>
    <?php else: ?>
    <?php foreach ($campi as $campo): 
        $sportColor = $sportColors[$campo['sport_nome']] ?? '#3B82F6';
        $sportEmoji = getSportEmoji($campo['sport_nome']);
        $rating = floatval($campo['rating_medio'] ?? 0);
        $isInManutenzione = $campo['stato'] === 'manutenzione';
        $hasManutenzioniPreviste = isset($campo['manutenzioni_future']) && $campo['manutenzioni_future'] > 0;
    ?>
    <div class="col-xl-4 col-lg-6 col-md-6">
        <div class="field-card <?php echo $isInManutenzione ? 'in-manutenzione' : ''; ?>" 
             data-campo-id="<?php echo $campo['campo_id']; ?>" 
             style="--sport-color: <?php echo $sportColor; ?>">
            
            <!-- Header Card -->
            <div class="field-card-header">
                <!-- Status Badge Principale -->
                <?php if ($isInManutenzione): ?>
                <div class="field-status orange">
                    <span class="status-dot"></span>
                    <span class="status-text">In Manutenzione</span>
                </div>
                <?php else: ?>
                <div class="field-status green">
                    <span class="status-dot"></span>
                    <span class="status-text">Disponibile</span>
                </div>
                <?php endif; ?>
                
                <!-- Badge Secondario - Manutenzione Prevista (come in admin) -->
                <?php if ($hasManutenzioniPreviste && !$isInManutenzione): ?>
                <div class="field-status-secondary">
                    <span class="status-secondary-text">⚠️ Manutenzione prevista</span>
                </div>
                <?php endif; ?>
                
                <!-- Sport Icon -->
                <div class="field-icon-wrapper">
                    <span class="field-sport-emoji"><?php echo $sportEmoji; ?></span>
                </div>
                
                <!-- Nome Campo -->
                <h3 class="field-name"><?php echo htmlspecialchars($campo['nome']); ?></h3>
                
                <!-- Sport e Tipo -->
                <div class="field-type">
                    <?php echo htmlspecialchars($campo['sport_nome']); ?> • <?php echo $campo['tipo_campo'] == 'indoor' ? 'Indoor' : 'Outdoor'; ?>
                </div>
            </div>
            
            <!-- Body Card -->
            <div class="field-card-body">
                <!-- Info Grid -->
                <div class="field-info-grid">
                    <div class="field-info-item">
                        <span class="info-icon">📍</span>
                        <span class="info-text"><?php echo htmlspecialchars($campo['location']); ?></span>
                    </div>
                    <div class="field-info-item">
                        <span class="info-icon">🕐</span>
                        <span class="info-text"><?php echo substr($campo['orario_apertura'], 0, 5); ?> - <?php echo substr($campo['orario_chiusura'], 0, 5); ?></span>
                    </div>
                    <div class="field-info-item">
                        <span class="info-icon">👥</span>
                        <span class="info-text">Max <?php echo $campo['capienza_max']; ?> persone</span>
                    </div>
                </div>
                
                <!-- Rating -->
                <div class="field-rating-row">
                    <div class="stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="star <?php echo $i <= round($rating) ? 'filled' : 'empty'; ?>">★</span>
                        <?php endfor; ?>
                    </div>
                    <span class="rating-value"><?php echo number_format($rating, 1); ?></span>
                    <span class="rating-count">(<?php echo $campo['num_recensioni'] ?? 0; ?> recensioni)</span>
                </div>
                
                <!-- Bottone Prenota -->
                <button class="btn-prenota" onclick="tentaPrenota(<?php echo $campo['campo_id']; ?>)">
                    📅 Prenota Ora
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- ============================================================================
     MODAL UTENTE BLOCCATO
     ============================================================================ -->
<?php if ($utenteBloccato): ?>
<div class="modal fade" id="modalBloccato" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered modal-sm" style="z-index: 1071;">
        <div class="modal-content modal-bloccato-content" style="pointer-events: auto;">
            <div class="modal-header modal-header-bloccato">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-bloccato-icon">
                        <?= $statoUtente === 'bannato' ? '⛔' : '🔒' ?>
                    </div>
                    <div>
                        <h5 class="modal-title">
                            <?= $statoUtente === 'bannato' ? 'Account Bannato' : 'Account Sospeso' ?>
                        </h5>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <p class="modal-bloccato-message">
                    <?php if ($statoUtente === 'bannato'): ?>
                        Il tuo account è stato <strong>bannato</strong>. Non puoi effettuare prenotazioni.
                        <br><br>
                        Per maggiori informazioni, contatta l'amministrazione.
                    <?php else: ?>
                        Il tuo account è <strong>temporaneamente sospeso</strong>. Non puoi effettuare prenotazioni fino al termine della sospensione.
                    <?php endif; ?>
                </p>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary-modal" data-bs-dismiss="modal">
                    Ho capito
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ============================================================================
     MODAL PRENOTAZIONE
     ============================================================================ -->
<div class="modal fade" id="modalPrenota" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" style="z-index: 1061;">
        <div class="modal-content prenota-modal" style="pointer-events: auto;">
            <!-- Header -->
            <div class="modal-header prenota-header">
                <div class="modal-title-wrapper">
                    <span class="modal-header-icon" id="modalCampoIcon">🏟️</span>
                    <div class="modal-title-text">
                        <h5 class="modal-title" id="modalCampoNome">Nome Campo</h5>
                        <p class="modal-subtitle" id="modalCampoSport">Sport • Tipo</p>
                    </div>
                </div>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Chiudi">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            <!-- Body -->
            <div class="modal-body prenota-body">
                <!-- Info Campo -->
                <div class="info-section">
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-icon">📍</span>
                            <div class="info-content">
                                <span class="info-label">Posizione</span>
                                <span class="info-value" id="modalCampoLocation">-</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="info-icon">🕐</span>
                            <div class="info-content">
                                <span class="info-label">Orari</span>
                                <span class="info-value" id="modalCampoOrari">-</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="info-icon">👥</span>
                            <div class="info-content">
                                <span class="info-label">Capienza</span>
                                <span class="info-value" id="modalCampoCapienza">-</span>
                            </div>
                        </div>
                        <div class="info-item" id="modalCampoServiziRow" style="display: none;">
                            <span class="info-icon">✨</span>
                            <div class="info-content">
                                <span class="info-label">Servizi</span>
                                <span class="info-value" id="modalCampoServizi">-</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sezione Recensioni - Cliccabile -->
                <div class="recensioni-section">
                    <div class="recensioni-header" onclick="toggleRecensioni()">
                        <div class="recensioni-header-left">
                            <span class="recensioni-icon">⭐</span>
                            <span class="recensioni-title">Recensioni</span>
                            <span class="recensioni-count" id="modalRecensioniCount">(0)</span>
                        </div>
                        <div class="recensioni-header-right">
                            <span class="recensioni-rating" id="modalRecensioniRating">-</span>
                            <span class="recensioni-toggle" id="recensioniToggleIcon">▼</span>
                        </div>
                    </div>
                    <div class="recensioni-content" id="recensioniContent" style="display: none;">
                        <div class="recensioni-list" id="recensioniList">
                            <div class="recensioni-loading">
                                <span>⏳</span> Caricamento recensioni...
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Avviso Manutenzione Prevista -->
                <div class="alert-manutenzione" id="alertManutenzione" style="display: none;">
                    <div class="alert-manutenzione-icon">🔧</div>
                    <div class="alert-manutenzione-content">
                        <div class="alert-manutenzione-title">⚠️ Manutenzione Programmata</div>
                        <div class="alert-manutenzione-dates" id="alertManutenzioneText">
                            Alcuni slot potrebbero non essere disponibili.
                        </div>
                        <div class="alert-manutenzione-note">
                            Gli slot durante questo periodo non saranno prenotabili.
                        </div>
                    </div>
                </div>
                
                <!-- Selezione Data -->
                <div class="form-section">
                    <label class="section-label">📅 Seleziona Data</label>
                    <input type="date" id="dataPrenotazione" class="form-input" 
                           min="<?php echo date('Y-m-d'); ?>" 
                           max="<?php echo date('Y-m-d', strtotime('+' . $giorniMaxAnticipo . ' days')); ?>">
                    <span class="form-hint">Puoi prenotare fino a <?php echo $giorniMaxAnticipo; ?> giorni in anticipo</span>
                </div>
                
                <!-- Selezione Slot -->
                <div class="form-section">
                    <label class="section-label">🕐 Seleziona Orario</label>
                    <div id="slotsContainer" class="slots-grid">
                        <p class="slots-placeholder">Seleziona una data per vedere gli slot disponibili</p>
                    </div>
                </div>
                
                <!-- Partecipanti e Note -->
                <div class="form-section">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="section-label">👥 Partecipanti</label>
                            <input type="number" id="numPartecipanti" class="form-input" value="2" min="1" max="30">
                        </div>
                        <div class="col-md-6">
                            <label class="section-label">📝 Note (opzionale)</label>
                            <input type="text" id="notePrenotazione" class="form-input" placeholder="Es: torneo amatoriale">
                        </div>
                    </div>
                </div>
                
                <!-- Riepilogo -->
                <div class="riepilogo-section" id="riepilogoPrenotazione" style="display: none;">
                    <div class="riepilogo-header">
                        <span class="riepilogo-icon">📋</span>
                        <span class="riepilogo-title">Riepilogo Prenotazione</span>
                    </div>
                    <div class="riepilogo-content">
                        <div class="riepilogo-item">
                            <span class="riepilogo-label">Data:</span>
                            <span class="riepilogo-value" id="riepilogoData">-</span>
                        </div>
                        <div class="riepilogo-item">
                            <span class="riepilogo-label">Orario:</span>
                            <span class="riepilogo-value" id="riepilogoOrario">-</span>
                        </div>
                        <div class="riepilogo-item">
                            <span class="riepilogo-label">Partecipanti:</span>
                            <span class="riepilogo-value" id="riepilogoPartecipanti">-</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="modal-footer prenota-footer">
                <button type="button" class="btn-modal-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn-modal-primary" id="btnConfermaPrenota" disabled onclick="confermaPrenota()">
                    ✅ Conferma Prenotazione
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL SUCCESSO
     ============================================================================ -->
<div class="modal fade" id="modalSuccesso" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered" style="z-index: 1071;">
        <div class="modal-content successo-modal" style="pointer-events: auto;">
            <div class="modal-body text-center py-5">
                <div class="success-animation">
                    <div class="success-icon">✅</div>
                </div>
                <h4 class="success-title">Prenotazione Confermata!</h4>
                <p class="success-subtitle">La tua prenotazione è stata registrata con successo.</p>
                <p class="success-details"><strong id="successoDettagli"></strong></p>
                <div class="success-actions">
                    <a href="le-mie-prenotazioni.php" class="btn-modal-primary">📋 Le mie prenotazioni</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL ERRORE GENERICO
     ============================================================================ -->
<div class="modal fade" id="modalErrore" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered modal-sm" style="z-index: 1071;">
        <div class="modal-content modal-errore-content" style="pointer-events: auto;">
            <div class="modal-header modal-header-errore">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-errore-icon" id="modalErroreIcon">⚠️</div>
                    <div>
                        <h5 class="modal-title" id="modalErroreTitolo">Errore</h5>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <p class="modal-errore-message" id="modalErroreMessaggio">Si è verificato un errore.</p>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary-modal" data-bs-dismiss="modal">
                    Chiudi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     JAVASCRIPT - Logica identica all'Admin Panel
     ============================================================================ -->
<script>
// ============================================================================
// VARIABILI GLOBALI
// ============================================================================
let campoSelezionato = null;
let slotSelezionato = null;
let campoCorrente = null; // Dati del campo selezionato

// Configurazione dal server
const GIORNI_MAX_ANTICIPO = <?php echo $giorniMaxAnticipo; ?>;

// Stato filtri corrente (come nell'admin)
let currentFilters = {
    sport: '<?php echo $filtri['sport'] ?? ''; ?>',
    tipo: '<?php echo $filtri['tipo'] ?? ''; ?>',
    ordina: '<?php echo $filtri['ordina'] ?? 'nome'; ?>',
    search: '<?php echo $filtri['search'] ?? ''; ?>'
};

// ============================================================================
// FUNZIONE APPLICA FILTRI (Identica all'admin)
// ============================================================================
function applyFilters() {
    const url = new URL(window.location);
    url.searchParams.set('sport', currentFilters.sport);
    url.searchParams.set('tipo', currentFilters.tipo);
    url.searchParams.set('ordina', currentFilters.ordina);
    url.searchParams.set('search', currentFilters.search);
    window.location = url;
}

// ============================================================================
// HELPER - Pulisce backdrop residui dei modal
// ============================================================================
function cleanupBackdrops() {
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
}

// ============================================================================
// HELPER - Emoji sport
// ============================================================================
function getSportEmojiJS(sportNome) {
    const map = {
        'calcio': '⚽', 'basket': '🏀', 'tennis': '🎾',
        'pallavolo': '🏐', 'padel': '🎾', 'badminton': '🏸', 'ping pong': '🏓'
    };
    const nome = sportNome.toLowerCase();
    for (const [key, emoji] of Object.entries(map)) {
        if (nome.includes(key)) return emoji;
    }
    return '🏟️';
}

// ============================================================================
// HELPER - Formatta data/ora per display
// ============================================================================
function formatDateTimeIT(datetime) {
    if (!datetime) return '';
    const d = new Date(datetime);
    return d.toLocaleDateString('it-IT', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// ============================================================================
// EVENT LISTENERS
// ============================================================================
document.addEventListener('DOMContentLoaded', function() {
    
    // =========================================================================
    // FIX MODAL - Sposta i modal nel body SUBITO per evitare z-index issues
    // Deve essere la PRIMA cosa eseguita!
    // =========================================================================
    const modalsToMove = ['modalPrenota', 'modalSuccesso', 'modalErrore', 'modalBloccato'];
    modalsToMove.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });
    
    // =========================================================================
    // FILTER CHIPS - Gestione identica all'admin
    // =========================================================================
    document.querySelectorAll('.filter-chip').forEach(chip => {
        chip.addEventListener('click', function() {
            const filter = this.dataset.filter;
            const value = this.dataset.value;
            currentFilters[filter] = value;
            applyFilters();
        });
    });
    
    // =========================================================================
    // SELECT ORDINA
    // =========================================================================
    document.getElementById('selectOrdina').addEventListener('change', function() {
        currentFilters.ordina = this.value;
        applyFilters();
    });
    
    // =========================================================================
    // RICERCA - Con debounce e Enter
    // =========================================================================
    const searchInput = document.getElementById('searchCampi');
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentFilters.search = this.value;
            applyFilters();
        }, 500);
    });
    
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            clearTimeout(searchTimeout);
            currentFilters.search = this.value;
            applyFilters();
        }
    });
    
    // =========================================================================
    // CARD CAMPI - Click per aprire modal
    // =========================================================================
    document.querySelectorAll('.field-card').forEach(card => {
        card.addEventListener('click', function(e) {
            if (e.target.closest('.btn-prenota')) return;
            const campoId = this.dataset.campoId;
            if (campoId) apriModalPrenota(parseInt(campoId));
        });
    });
    
    // =========================================================================
    // DATA PRENOTAZIONE - Carica slot quando cambia
    // =========================================================================
    document.getElementById('dataPrenotazione').addEventListener('change', function() {
        caricaSlot(this.value);
    });
    
    // =========================================================================
    // PARTECIPANTI - Aggiorna riepilogo quando cambia
    // =========================================================================
    document.getElementById('numPartecipanti').addEventListener('change', aggiornaRiepilogo);
});

// ============================================================================
// VERIFICA UTENTE BLOCCATO PRIMA DI PRENOTARE
// ============================================================================
const utenteBloccato = <?= $utenteBloccato ? 'true' : 'false' ?>;

function tentaPrenota(campoId) {
    if (utenteBloccato) {
        // Mostra modal bloccato
        new bootstrap.Modal(document.getElementById('modalBloccato')).show();
    } else {
        // Procedi con la prenotazione
        apriModalPrenota(campoId);
    }
}

// ============================================================================
// MOSTRA ERRORE CON MODAL
// ============================================================================
function mostraErrore(messaggio, titolo = 'Errore', icona = '⚠️') {
    // Controlla se è un errore di ban/sospensione
    if (messaggio.toLowerCase().includes('bannato') || messaggio.toLowerCase().includes('sospeso')) {
        // Usa il modal bloccato se esiste
        const modalBloccato = document.getElementById('modalBloccato');
        if (modalBloccato) {
            new bootstrap.Modal(modalBloccato).show();
            return;
        }
    }
    
    // Altrimenti usa il modal errore generico
    document.getElementById('modalErroreIcon').textContent = icona;
    document.getElementById('modalErroreTitolo').textContent = titolo;
    document.getElementById('modalErroreMessaggio').textContent = messaggio;
    new bootstrap.Modal(document.getElementById('modalErrore')).show();
}

// ============================================================================
// APRI MODAL PRENOTAZIONE
// ============================================================================
function apriModalPrenota(campoId) {
    campoSelezionato = campoId;
    slotSelezionato = null;
    campoCorrente = null;
    recensioniCaricate = false; // Reset stato recensioni
    
    // Reset form
    document.getElementById('dataPrenotazione').value = '';
    document.getElementById('slotsContainer').innerHTML = '<p class="slots-placeholder">Seleziona una data per vedere gli slot disponibili</p>';
    document.getElementById('numPartecipanti').value = 2;
    document.getElementById('notePrenotazione').value = '';
    document.getElementById('riepilogoPrenotazione').style.display = 'none';
    document.getElementById('btnConfermaPrenota').disabled = true;
    document.getElementById('btnConfermaPrenota').textContent = '✅ Conferma Prenotazione';
    document.getElementById('alertManutenzione').style.display = 'none';
    
    // Reset sezione recensioni
    document.getElementById('recensioniContent').style.display = 'none';
    document.getElementById('recensioniToggleIcon').textContent = '▼';
    document.getElementById('recensioniList').innerHTML = '<div class="recensioni-loading"><span>⏳</span> Caricamento recensioni...</div>';
    
    // Carica dettagli campo dal database
    fetch('prenota-campo.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=get_campo&campo_id=' + campoId
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const c = data.campo;
            campoCorrente = c;
            
            document.getElementById('modalCampoNome').textContent = c.nome;
            document.getElementById('modalCampoSport').textContent = c.sport_nome + ' • ' + (c.tipo_campo === 'indoor' ? 'Indoor' : 'Outdoor');
            document.getElementById('modalCampoLocation').textContent = c.location;
            document.getElementById('modalCampoOrari').textContent = c.orario_apertura.substr(0,5) + ' - ' + c.orario_chiusura.substr(0,5);
            document.getElementById('modalCampoCapienza').textContent = 'Max ' + c.capienza_max + ' persone';
            document.getElementById('numPartecipanti').max = c.capienza_max;
            
            if (c.servizi) {
                document.getElementById('modalCampoServizi').textContent = c.servizi;
                document.getElementById('modalCampoServiziRow').style.display = 'flex';
            } else {
                document.getElementById('modalCampoServiziRow').style.display = 'none';
            }
            
            const emoji = getSportEmojiJS(c.sport_nome);
            document.getElementById('modalCampoIcon').textContent = emoji;
            
            // Aggiorna info recensioni nell'header
            aggiornaInfoRecensioni(c.num_recensioni, c.rating_medio);
            
            // Mostra avviso se c'è manutenzione prevista
            if (c.manutenzioni_future && parseInt(c.manutenzioni_future) > 0) {
                document.getElementById('alertManutenzione').style.display = 'flex';
                
                // Mostra date manutenzione se disponibili
                if (c.prossima_manutenzione_inizio && c.prossima_manutenzione_fine) {
                    // Formatta le date in modo leggibile
                    const inizioDate = new Date(c.prossima_manutenzione_inizio);
                    const fineDate = new Date(c.prossima_manutenzione_fine);
                    
                    const opzioniData = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
                    const opzioniOra = { hour: '2-digit', minute: '2-digit' };
                    
                    const inizioDataStr = inizioDate.toLocaleDateString('it-IT', opzioniData);
                    const inizioOraStr = inizioDate.toLocaleTimeString('it-IT', opzioniOra);
                    const fineDataStr = fineDate.toLocaleDateString('it-IT', opzioniData);
                    const fineOraStr = fineDate.toLocaleTimeString('it-IT', opzioniOra);
                    
                    document.getElementById('alertManutenzioneText').innerHTML = 
                        `<div class="manutenzione-periodo">` +
                        `<div class="manutenzione-da"><span class="manutenzione-label">Da:</span> <strong>${inizioDataStr}</strong> alle <strong>${inizioOraStr}</strong></div>` +
                        `<div class="manutenzione-a"><span class="manutenzione-label">A:</span> <strong>${fineDataStr}</strong> alle <strong>${fineOraStr}</strong></div>` +
                        `</div>`;
                } else {
                    document.getElementById('alertManutenzioneText').innerHTML = 
                        '<em>Date manutenzione non specificate</em>';
                }
            } else {
                document.getElementById('alertManutenzione').style.display = 'none';
            }
            
            // Apri modal DOPO aver caricato i dati
            new bootstrap.Modal(document.getElementById('modalPrenota')).show();
        } else {
            console.error('Errore caricamento campo:', data.error);
            mostraErrore('Errore nel caricamento del campo. Riprova.', 'Errore Caricamento', '❌');
        }
    })
    .catch(err => {
        console.error('Errore fetch campo:', err);
        mostraErrore('Errore di connessione. Riprova.', 'Errore Connessione', '🔌');
    });
}

// ============================================================================
// CARICA SLOT DISPONIBILI DAL DATABASE
// La logica degli slot tiene già conto di:
// - Prenotazioni esistenti
// - Blocchi manutenzione (anche parziali durante la giornata)
// - Orari di apertura/chiusura del campo
// - Slot già passati per oggi
// ============================================================================
function caricaSlot(data) {
    if (!data || !campoSelezionato) return;
    
    document.getElementById('slotsContainer').innerHTML = '<p class="slots-loading">⏳ Caricamento slot...</p>';
    slotSelezionato = null;
    document.getElementById('riepilogoPrenotazione').style.display = 'none';
    document.getElementById('btnConfermaPrenota').disabled = true;
    
    // Carica slot dal database
    fetch('prenota-campo.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=get_slot&campo_id=' + campoSelezionato + '&data=' + data
    })
    .then(r => r.json())
    .then(response => {
        if (response.success) {
            if (response.message) {
                // Giorno di chiusura
                document.getElementById('slotsContainer').innerHTML = '<p class="slots-closed">🚫 ' + response.message + '</p>';
                return;
            }
            
            if (response.slots.length === 0) {
                // Potrebbe essere per manutenzione totale o tutti slot prenotati
                document.getElementById('slotsContainer').innerHTML = '<p class="slots-empty">😔 Nessuno slot disponibile per questa data.<br><small>Potrebbe essere dovuto a manutenzione o prenotazioni esistenti.</small></p>';
                return;
            }
            
            // Genera bottoni slot
            let html = '';
            response.slots.forEach(slot => {
                const oraInizio = slot.ora_inizio.substr(0,5);
                const oraFine = slot.ora_fine.substr(0,5);
                html += `<button type="button" class="slot-btn" data-inizio="${slot.ora_inizio}" data-fine="${slot.ora_fine}" onclick="selezionaSlot(this)">${oraInizio} - ${oraFine}</button>`;
            });
            document.getElementById('slotsContainer').innerHTML = html;
        } else {
            document.getElementById('slotsContainer').innerHTML = '<p class="slots-empty">❌ Errore nel caricamento degli slot</p>';
        }
    })
    .catch(err => {
        console.error('Errore fetch slot:', err);
        document.getElementById('slotsContainer').innerHTML = '<p class="slots-empty">❌ Errore di connessione</p>';
    });
}

// ============================================================================
// SELEZIONA SLOT
// ============================================================================
function selezionaSlot(btn) {
    // Rimuovi selezione precedente
    document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
    
    // Seleziona nuovo slot
    btn.classList.add('selected');
    slotSelezionato = {
        inizio: btn.dataset.inizio,
        fine: btn.dataset.fine
    };
    
    aggiornaRiepilogo();
}

// ============================================================================
// AGGIORNA RIEPILOGO
// ============================================================================
function aggiornaRiepilogo() {
    if (!slotSelezionato) return;
    
    const data = document.getElementById('dataPrenotazione').value;
    const dataFormatted = new Date(data).toLocaleDateString('it-IT', {
        weekday: 'long', 
        day: 'numeric', 
        month: 'long', 
        year: 'numeric'
    });
    
    document.getElementById('riepilogoData').textContent = dataFormatted;
    document.getElementById('riepilogoOrario').textContent = slotSelezionato.inizio.substr(0,5) + ' - ' + slotSelezionato.fine.substr(0,5);
    document.getElementById('riepilogoPartecipanti').textContent = document.getElementById('numPartecipanti').value;
    document.getElementById('riepilogoPrenotazione').style.display = 'block';
    document.getElementById('btnConfermaPrenota').disabled = false;
}

// ============================================================================
// CONFERMA PRENOTAZIONE - Salva nel database
// La validazione server-side verifica nuovamente:
// - Giorni massimi anticipo
// - Giorni chiusura struttura
// - Campo chiuso
// - Manutenzione
// - Slot già prenotato
// - Capienza massima
// ============================================================================
function confermaPrenota() {
    if (!campoSelezionato || !slotSelezionato) return;
    
    const data = document.getElementById('dataPrenotazione').value;
    const numPartecipanti = document.getElementById('numPartecipanti').value;
    const note = document.getElementById('notePrenotazione').value;
    
    // Disabilita bottone
    document.getElementById('btnConfermaPrenota').disabled = true;
    document.getElementById('btnConfermaPrenota').textContent = '⏳ Prenotazione in corso...';
    
    // Invia prenotazione al database
    fetch('prenota-campo.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=prenota&campo_id=${campoSelezionato}&data=${data}&ora_inizio=${slotSelezionato.inizio}&ora_fine=${slotSelezionato.fine}&num_partecipanti=${numPartecipanti}&note=${encodeURIComponent(note)}`
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            // Chiudi modal prenotazione
            bootstrap.Modal.getInstance(document.getElementById('modalPrenota')).hide();
            
            // Aspetta, pulisci backdrop e apri modal successo
            setTimeout(() => {
                cleanupBackdrops();
                
                const dataFormatted = new Date(data).toLocaleDateString('it-IT', {
                    weekday: 'long', 
                    day: 'numeric', 
                    month: 'long'
                });
                document.getElementById('successoDettagli').textContent = dataFormatted + ' alle ' + slotSelezionato.inizio.substr(0,5);
                
                new bootstrap.Modal(document.getElementById('modalSuccesso')).show();
            }, 350);
        } else {
            mostraErrore(result.error, 'Prenotazione non riuscita', '⚠️');
            document.getElementById('btnConfermaPrenota').disabled = false;
            document.getElementById('btnConfermaPrenota').textContent = '✅ Conferma Prenotazione';
        }
    })
    .catch(err => {
        console.error('Errore prenotazione:', err);
        mostraErrore('Errore di connessione. Riprova.', 'Errore Connessione', '🔌');
        document.getElementById('btnConfermaPrenota').disabled = false;
        document.getElementById('btnConfermaPrenota').textContent = '✅ Conferma Prenotazione';
    });
}

// ============================================================================
// RECENSIONI - Toggle e caricamento
// ============================================================================
let recensioniCaricate = false;

function toggleRecensioni() {
    const content = document.getElementById('recensioniContent');
    const icon = document.getElementById('recensioniToggleIcon');
    
    if (content.style.display === 'none') {
        content.style.display = 'block';
        icon.textContent = '▲';
        
        // Carica recensioni solo la prima volta
        if (!recensioniCaricate && campoSelezionato) {
            caricaRecensioni();
        }
    } else {
        content.style.display = 'none';
        icon.textContent = '▼';
    }
}

function caricaRecensioni() {
    const list = document.getElementById('recensioniList');
    list.innerHTML = '<div class="recensioni-loading"><span>⏳</span> Caricamento recensioni...</div>';
    
    fetch('prenota-campo.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=get_recensioni&campo_id=' + campoSelezionato
    })
    .then(r => r.json())
    .then(data => {
        recensioniCaricate = true;
        
        if (data.success && data.recensioni && data.recensioni.length > 0) {
            let html = '';
            
            data.recensioni.forEach(rec => {
                const iniziale = rec.utente_iniziale || rec.utente_nome.charAt(0).toUpperCase();
                const dataRec = new Date(rec.created_at).toLocaleDateString('it-IT', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
                
                // Genera stelle
                const stelle = Array(5).fill().map((_, i) => 
                    `<span class="star ${i < rec.rating_generale ? 'filled' : ''}">${i < rec.rating_generale ? '★' : '☆'}</span>`
                ).join('');
                
                html += `
                    <div class="recensione-card-utente">
                        <div class="recensione-header-utente">
                            <div class="recensione-avatar-utente">${iniziale}</div>
                            <div class="recensione-info-utente">
                                <div class="recensione-nome-utente">${rec.utente_nome}</div>
                                <div class="recensione-data-utente">${dataRec}</div>
                            </div>
                            <div class="recensione-rating-utente">${stelle}</div>
                        </div>
                        ${rec.commento ? `<div class="recensione-testo-utente">"${rec.commento}"</div>` : ''}
                        ${rec.risposta ? `
                            <div class="risposta-admin-box">
                                <div class="risposta-admin-header">
                                    <span class="risposta-admin-icon">💬</span>
                                    <span class="risposta-admin-label">Risposta di ${rec.risposta.admin_nome}</span>
                                    <span class="risposta-admin-data">${new Date(rec.risposta.risposta_data).toLocaleDateString('it-IT')}</span>
                                </div>
                                <div class="risposta-admin-testo">${rec.risposta.testo}</div>
                            </div>
                        ` : ''}
                    </div>
                `;
            });
            
            list.innerHTML = html;
        } else {
            list.innerHTML = `
                <div class="recensioni-empty">
                    <div class="recensioni-empty-icon">⭐</div>
                    <p>Nessuna recensione per questo campo</p>
                    <small>Sii il primo a lasciare una recensione dopo aver prenotato!</small>
                </div>
            `;
        }
    })
    .catch(err => {
        console.error('Errore caricamento recensioni:', err);
        list.innerHTML = '<div class="recensioni-error">❌ Errore nel caricamento delle recensioni</div>';
    });
}

// Aggiorna contatore e rating nel modal quando si apre
function aggiornaInfoRecensioni(numRecensioni, ratingMedio) {
    document.getElementById('modalRecensioniCount').textContent = `(${numRecensioni || 0})`;
    
    if (ratingMedio && parseFloat(ratingMedio) > 0) {
        const stars = '★'.repeat(Math.round(parseFloat(ratingMedio))) + '☆'.repeat(5 - Math.round(parseFloat(ratingMedio)));
        document.getElementById('modalRecensioniRating').innerHTML = `<span class="rating-stars-mini">${stars}</span> ${parseFloat(ratingMedio).toFixed(1)}`;
    } else {
        document.getElementById('modalRecensioniRating').textContent = '-';
    }
}
</script>