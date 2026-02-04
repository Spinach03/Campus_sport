<!-- ============================================================================
     GESTIONE UTENTI - Campus Sports Arena Admin
     ============================================================================ -->

<?php
// Helper per colori ruolo
function getRoleConfig($ruolo) {
    $config = [
        'admin' => ['color' => '#8B5CF6', 'label' => 'Admin', 'icon' => '👑'],
        'user' => ['color' => '#3B82F6', 'label' => 'Utente', 'icon' => '👤']
    ];
    return $config[$ruolo] ?? $config['user'];
}

// Helper per colori stato
function getStatusConfig($stato) {
    $config = [
        'attivo' => ['color' => '#10B981', 'label' => 'Attivo', 'class' => 'green'],
        'sospeso' => ['color' => '#F59E0B', 'label' => 'Sospeso', 'class' => 'orange'],
        'bannato' => ['color' => '#EF4444', 'label' => 'Bannato', 'class' => 'red']
    ];
    return $config[$stato] ?? $config['attivo'];
}

// Helper per penalty level
function getPenaltyLevel($points) {
    if ($points == 0) return ['color' => '#10B981', 'label' => 'Nessuno', 'level' => 'none'];
    if ($points <= 2) return ['color' => '#3B82F6', 'label' => 'Basso', 'level' => 'low'];
    if ($points <= 5) return ['color' => '#F59E0B', 'label' => 'Medio', 'level' => 'medium'];
    return ['color' => '#EF4444', 'label' => 'Alto', 'level' => 'high'];
}

// Helper per iniziali
function getInitials($nome, $cognome) {
    return strtoupper(substr($nome, 0, 1) . substr($cognome, 0, 1));
}

// Estrai variabili da templateParams
$statsGenerali = $templateParams["statsGenerali"] ?? ['totale' => 0, 'attivi' => 0, 'sospesi' => 0, 'bannati' => 0];
$users = $templateParams["users"] ?? [];
$corsi = $templateParams["corsi"] ?? [];
$filtri = $templateParams["filtri"] ?? ['ruolo' => '', 'stato' => '', 'corso' => '', 'penalty_min' => '', 'search' => '', 'ordina' => 'nome'];
?>

<!-- Header Gestione Utenti - Tutto in linea -->
<div class="gestione-header">
    <span class="header-icon">👥</span>
    <p class="page-subtitle">Monitora, gestisci e modera gli utenti della piattaforma</p>
    
    <!-- Search -->
    <div class="search-box" id="searchContainer">
        <span class="search-icon">🔍</span>
        <input type="text" class="search-input" id="searchUtenti" placeholder="Cerca utenti..." value="<?php echo htmlspecialchars($filtri['search']); ?>">
    </div>
</div>

<!-- ============================================================================
     QUICK STATS - KPI Cards
     ============================================================================ -->
<div class="row g-3 mb-4">
    <!-- Utenti Totali -->
    <div class="col-xl-3 col-md-6 col-6">
        <div class="kpi-card" data-color="blue">
            <div class="kpi-icon-wrapper">
                <span class="kpi-icon">👥</span>
            </div>
            <div class="kpi-value"><?php echo $statsGenerali['totale'] ?? 0; ?></div>
            <div class="kpi-label">Utenti Totali</div>
        </div>
    </div>
    
    <!-- Attivi -->
    <div class="col-xl-3 col-md-6 col-6">
        <div class="kpi-card" data-color="green">
            <div class="kpi-icon-wrapper">
                <span class="kpi-icon">✅</span>
            </div>
            <div class="kpi-value"><?php echo $statsGenerali['attivi'] ?? 0; ?></div>
            <div class="kpi-label">Attivi</div>
        </div>
    </div>
    
    <!-- Sospesi -->
    <div class="col-xl-3 col-md-6 col-6">
        <div class="kpi-card" data-color="orange">
            <div class="kpi-icon-wrapper">
                <span class="kpi-icon">⏸️</span>
            </div>
            <div class="kpi-value"><?php echo $statsGenerali['sospesi'] ?? 0; ?></div>
            <div class="kpi-label">Sospesi</div>
        </div>
    </div>
    
    <!-- Bannati -->
    <div class="col-xl-3 col-md-6 col-6">
        <div class="kpi-card" data-color="red">
            <div class="kpi-icon-wrapper">
                <span class="kpi-icon">🚫</span>
            </div>
            <div class="kpi-value"><?php echo $statsGenerali['bannati'] ?? 0; ?></div>
            <div class="kpi-label">Bannati</div>
        </div>
    </div>
</div>

<!-- ============================================================================
     FILTRI CARD
     ============================================================================ -->
<div class="filters-card mb-4">
    <form id="formFiltri" method="GET">
        <!-- Riga 1: Ruolo -->
        <div class="filter-row">
            <span class="filter-label">Ruolo:</span>
            <div class="filter-chips">
                <button type="button" class="filter-chip <?php echo empty($filtri['ruolo']) ? 'active' : ''; ?>" data-filter="ruolo" data-value="">
                    Tutti
                </button>
                <button type="button" class="filter-chip <?php echo $filtri['ruolo'] === 'user' ? 'active' : ''; ?>" data-filter="ruolo" data-value="user">
                    👤 Utente
                </button>
                <button type="button" class="filter-chip <?php echo $filtri['ruolo'] === 'admin' ? 'active' : ''; ?>" data-filter="ruolo" data-value="admin">
                    👑 Admin
                </button>
            </div>
        </div>
        
        <!-- Riga 2: Stato -->
        <div class="filter-row">
            <span class="filter-label">Stato:</span>
            <div class="filter-chips">
                <button type="button" class="filter-chip <?php echo empty($filtri['stato']) ? 'active' : ''; ?>" data-filter="stato" data-value="">
                    Tutti
                </button>
                <button type="button" class="filter-chip <?php echo $filtri['stato'] === 'attivo' ? 'active' : ''; ?>" data-filter="stato" data-value="attivo">
                    <span class="status-dot green"></span> Attivo
                </button>
                <button type="button" class="filter-chip <?php echo $filtri['stato'] === 'sospeso' ? 'active' : ''; ?>" data-filter="stato" data-value="sospeso">
                    <span class="status-dot orange"></span> Sospeso
                </button>
                <button type="button" class="filter-chip <?php echo $filtri['stato'] === 'bannato' ? 'active' : ''; ?>" data-filter="stato" data-value="bannato">
                    <span class="status-dot red"></span> Bannato
                </button>
            </div>
        </div>
        
        <!-- Riga 3: Selects -->
        <div class="filter-row">
            <!-- Filtro Corso -->
            <select name="corso" id="selectCorso" class="sort-select" aria-label="Filtra per corso di laurea">
                <option value="">Tutti i corsi</option>
                <?php foreach ($corsi as $corso): ?>
                <option value="<?php echo $corso['corso_id']; ?>" <?php echo $filtri['corso'] == $corso['corso_id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($corso['nome']); ?>
                </option>
                <?php endforeach; ?>
            </select>
            
            <!-- Ordinamento -->
            <select name="ordina" id="selectOrdina" class="sort-select" aria-label="Ordina risultati per">
                <option value="nome" <?php echo $filtri['ordina'] === 'nome' ? 'selected' : ''; ?>>Nome A-Z</option>
                <option value="recente" <?php echo $filtri['ordina'] === 'recente' ? 'selected' : ''; ?>>Più recenti</option>
                <option value="attivita" <?php echo $filtri['ordina'] === 'attivita' ? 'selected' : ''; ?>>Più attivi</option>
                <option value="penalty" <?php echo $filtri['ordina'] === 'penalty' ? 'selected' : ''; ?>>Più penalty</option>
            </select>
        </div>
        
        <!-- Hidden fields -->
        <input type="hidden" name="ruolo" id="hiddenRuolo" value="<?php echo htmlspecialchars($filtri['ruolo']); ?>">
        <input type="hidden" name="stato" id="hiddenStato" value="<?php echo htmlspecialchars($filtri['stato']); ?>">
        <input type="hidden" name="search" id="hiddenSearch" value="<?php echo htmlspecialchars($filtri['search']); ?>">
    </form>
</div>

<!-- ============================================================================
     GRIGLIA UTENTI
     ============================================================================ -->
<div class="users-grid">
    <?php if (empty($users)): ?>
    <div class="no-results">
        <div class="no-results-icon">😕</div>
        <h2 class="h5">Nessun utente trovato</h2>
        <p>Prova a modificare i filtri di ricerca</p>
    </div>
    <?php else: ?>
    
    <?php foreach ($users as $user): 
        $role = getRoleConfig($user['ruolo']);
        $status = getStatusConfig($user['stato']);
        $penalty = getPenaltyLevel($user['penalty_points'] ?? 0);
        $initials = getInitials($user['nome'], $user['cognome']);
        
        // Calcolo affidabilità
        $affidabilita = 100 - (($user['penalty_points'] ?? 0) * 5) - (($user['no_show_count'] ?? 0) * 3);
        $affidabilita = max(0, min(100, $affidabilita));
        
        // Gli admin non sono cliccabili
        $isAdmin = ($user['ruolo'] === 'admin');
    ?>
    <div class="user-card <?php echo $isAdmin ? 'admin-card' : ''; ?>" data-user-id="<?php echo $user['user_id']; ?>" data-ruolo="<?php echo $user['ruolo']; ?>">
        <!-- Header Card -->
        <div class="user-card-header">
            <!-- Status Badge -->
            <div class="user-status <?php echo $status['class']; ?>">
                <span class="status-indicator"></span>
                <span class="status-text"><?php echo $status['label']; ?></span>
            </div>
            
            <!-- Avatar -->
            <div class="user-avatar" style="background: linear-gradient(135deg, <?php echo $role['color']; ?>, <?php echo $role['color']; ?>88);">
                <?php echo $initials; ?>
                <?php if ($user['ruolo'] === 'admin'): ?>
                <span class="admin-badge">👑</span>
                <?php endif; ?>
            </div>
            
            <!-- Nome -->
            <h2 class="h5 user-name"><?php echo htmlspecialchars($user['nome'] . ' ' . $user['cognome']); ?></h2>
            
            <!-- Email -->
            <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
            
            <!-- Corso -->
            <?php if (!empty($user['corso_nome'])): ?>
            <div class="user-corso">🎓 <?php echo htmlspecialchars($user['corso_nome']); ?></div>
            <?php endif; ?>
        </div>
        
        <!-- Body Card -->
        <div class="user-card-body">
            <!-- Stats Row -->
            <div class="user-stats-row">
                <div class="user-stat">
                    <span class="stat-value"><?php echo $user['totale_prenotazioni'] ?? 0; ?></span>
                    <span class="stat-label">prenotazioni</span>
                </div>
                <div class="user-stat">
                    <span class="stat-value" style="color: <?php echo $penalty['color']; ?>"><?php echo $user['penalty_points'] ?? 0; ?></span>
                    <span class="stat-label">penalty</span>
                </div>
                <div class="user-stat">
                    <span class="stat-value highlight"><?php echo $affidabilita; ?>%</span>
                    <span class="stat-label">affidabilità</span>
                </div>
            </div>
            
            <!-- Progress Bar Affidabilità -->
            <div class="user-progress-wrapper">
                <div class="user-progress-bar" style="width: <?php echo $affidabilita; ?>%; background: <?php 
                    echo $affidabilita >= 80 ? '#10B981' : ($affidabilita >= 50 ? '#F59E0B' : '#EF4444'); 
                ?>"></div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php endif; ?>
</div>

<!-- ============================================================================
     MODAL: DETTAGLIO UTENTE
     ============================================================================ -->
<div class="modal fade" id="modalDettaglioUtente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content modal-utente-content">
            <!-- Header -->
            <div class="modal-header modal-utente-header">
                <div class="modal-user-info">
                    <div class="modal-user-avatar" id="modalUserAvatar">MB</div>
                    <div class="modal-user-details">
                        <h5 class="modal-user-name" id="modalUserName">Nome Utente</h5>
                        <span class="modal-user-status" id="modalUserStatus">● Attivo</span>
                        <div class="modal-user-meta">
                            <span id="modalUserEmail">📧 email@example.com</span>
                            <span id="modalUserCorso">🎓 Ingegneria</span>
                            <span id="modalUserAnno">📅 Anno 3</span>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>
            
            <!-- Tabs -->
            <ul class="nav nav-tabs modal-utente-tabs" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabInfo" type="button">
                        📋 Informazioni
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tabAttivita" type="button">
                        📊 Attività
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tabSegnalazioni" type="button">
                        🚩 Segnalazioni
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tabAzioni" type="button">
                        ⚡ Azioni
                    </button>
                </li>
            </ul>
            
            <!-- Body -->
            <div class="modal-body modal-utente-body">
                <div class="tab-content">
                    
                    <!-- Tab Informazioni -->
                    <div class="tab-pane fade show active" id="tabInfo">
                        <div class="row g-4">
                            <!-- Statistiche -->
                            <div class="col-md-6">
                                <h6 class="section-title">📊 Statistiche</h6>
                                <div class="stats-grid">
                                    <div class="stat-box">
                                        <span class="stat-box-icon">📅</span>
                                        <span class="stat-box-value" id="statPrenotazioni">0</span>
                                        <span class="stat-box-label">Prenotazioni Totali</span>
                                    </div>
                                    <div class="stat-box">
                                        <span class="stat-box-icon">✅</span>
                                        <span class="stat-box-value" id="statCompletate">0</span>
                                        <span class="stat-box-label">Completate</span>
                                    </div>
                                    <div class="stat-box stat-danger">
                                        <span class="stat-box-icon">❌</span>
                                        <span class="stat-box-value" id="statNoShow">0</span>
                                        <span class="stat-box-label">No-Show</span>
                                    </div>
                                    <div class="stat-box">
                                        <span class="stat-box-icon">🚫</span>
                                        <span class="stat-box-value" id="statCancellazioni">0</span>
                                        <span class="stat-box-label">Cancellazioni</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Affidabilità -->
                            <div class="col-md-6">
                                <h6 class="section-title">⭐ Affidabilità & Livello</h6>
                                
                                <div class="affidabilita-card">
                                    <div class="affidabilita-row">
                                        <span>Punteggio Affidabilità</span>
                                        <span class="affidabilita-value" id="modalAffidabilita">96%</span>
                                    </div>
                                    <div class="affidabilita-bar-bg">
                                        <div class="affidabilita-bar" id="modalAffidabilitaBar"></div>
                                    </div>
                                </div>
                                
                                <div class="penalty-card">
                                    <span>⚠️ Penalty Points</span>
                                    <span class="penalty-value" id="modalPenaltyValue">0</span>
                                </div>
                                
                                <div class="badges-card">
                                    <span class="badges-title">🏆 Badge Sbloccati (<span id="modalBadgeCount">0</span>)</span>
                                    <div class="badges-list" id="modalBadgesList"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab Attività -->
                    <div class="tab-pane fade" id="tabAttivita">
                        <h6 class="section-title">🕐 Attività Recenti</h6>
                        <div class="activity-list" id="modalActivityList">
                            <div class="no-data">Nessuna attività recente</div>
                        </div>
                    </div>
                    
                    <!-- Tab Segnalazioni -->
                    <div class="tab-pane fade" id="tabSegnalazioni">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="section-title">📥 Segnalazioni Ricevute</h6>
                                <div class="segnalazioni-list" id="modalSegnalazioniRicevute">
                                    <div class="no-data">Nessuna segnalazione ricevuta</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="section-title">📤 Segnalazioni Fatte</h6>
                                <div class="segnalazioni-list" id="modalSegnalazioniFatte">
                                    <div class="no-data">Nessuna segnalazione fatta</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab Azioni -->
                    <div class="tab-pane fade" id="tabAzioni">
                        <div class="row g-3">
                            <!-- Penalty Points -->
                            <div class="col-md-6">
                                <div class="action-box">
                                    <h6>⚠️ Penalty Points</h6>
                                    <p>Attualmente: <span id="actionPenaltyCount">0</span> punti</p>
                                    <div class="action-btns">
                                        <button type="button" class="btn-action-warning" id="btnResetPenalty">🔄 Reset</button>
                                        <button type="button" class="btn-action-danger" id="btnAddPenalty">+ Aggiungi</button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sospensione / Riattiva -->
                            <div class="col-md-6">
                                <div class="action-box" id="boxSospensione">
                                    <h6 id="sospensioneTitle">⏸️ Sospensione</h6>
                                    <p id="sospensioneDesc">Sospendi temporaneamente l'utente</p>
                                    <button type="button" class="btn-action-warning" id="btnSospendi">⏸️ Sospendi</button>
                                    <button type="button" class="btn-action-success" id="btnRiattiva" style="display:none;">✅ Riattiva Utente</button>
                                </div>
                            </div>
                            
                            <!-- Ban / Sbanna -->
                            <div class="col-md-6">
                                <div class="action-box" id="boxBan">
                                    <h6 id="banTitle">🚫 Ban Permanente</h6>
                                    <p id="banDesc">Azione irreversibile - richiede conferma</p>
                                    <button type="button" class="btn-action-danger-outline" id="btnBan">🚫 Banna Utente</button>
                                    <button type="button" class="btn-action-success" id="btnSbanna" style="display:none;">✅ Rimuovi Ban</button>
                                </div>
                            </div>
                            
                            <!-- Info Stato -->
                            <div class="col-md-6">
                                <div class="action-box action-box-info" id="boxStatoInfo">
                                    <h6>ℹ️ Stato Attuale</h6>
                                    <p id="statoInfoDesc">L'utente è attivo</p>
                                    <div class="stato-badge-large" id="statoInfoBadge">
                                        <span class="status-dot green"></span> Attivo
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="modal-footer modal-utente-footer">
                <span class="footer-registrato" id="modalRegistrato">Registrato il 01/01/2024</span>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL: SOSPENSIONE
     ============================================================================ -->
<div class="modal fade" id="modalSospensione" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-utente-content">
            <div class="modal-header">
                <h5 class="modal-title">⏸️ Sospendi Utente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formSospensione">
                    <div class="mb-3">
                        <label for="giorni" class="form-label">Durata sospensione</label>
                        <select id="giorni" class="form-select form-select-dark" name="giorni" required>
                            <option value="1">1 giorno</option>
                            <option value="3">3 giorni</option>
                            <option value="7" selected>7 giorni</option>
                            <option value="14">14 giorni</option>
                            <option value="30">30 giorni</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="motivoSospensione" class="form-label">Motivo</label>
                        <textarea id="motivoSospensione" class="form-control form-control-dark" name="motivo" rows="3" placeholder="Inserisci il motivo della sospensione..." required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-warning" id="btnConfirmSospensione">Sospendi</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL: BAN
     ============================================================================ -->
<div class="modal fade" id="modalBan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-utente-content">
            <div class="modal-header">
                <h5 class="modal-title">🚫 Ban Permanente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    ⚠️ Attenzione: questa azione è irreversibile!
                </div>
                <form id="formBan">
                    <div class="mb-3">
                        <label for="confermaBan" class="form-label">Motivo del ban</label>
                        <textarea class="form-control form-control-dark" name="motivo" rows="3" placeholder="Inserisci il motivo del ban..." required></textarea>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="conferma" id="confermaBan" required>
                        <label class="form-check-label" for="confermaBan">Confermo di voler bannare permanentemente questo utente</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-danger" id="btnConfirmBan">Banna Utente</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL: PENALTY - Aggiungi
     ============================================================================ -->
<div class="modal fade" id="modalPenalty" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-utente-content">
            <div class="modal-header">
                <h5 class="modal-title">⚠️ Aggiungi Penalty Points</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    ⚠️ I penalty points influiscono sul punteggio di affidabilità dell'utente.
                </div>
                <div class="penalty-info-box">
                    <p><strong>Penalty attuali:</strong> <span id="addCurrentPenalty">0</span> punti</p>
                </div>
                <form id="formPenalty">
                    <input type="hidden" id="penaltyAction" value="add">
                    <div class="mb-3">
                        <label for="punti" class="form-label">Numero punti da aggiungere</label>
                        <input id="punti" type="number" class="form-control form-control-dark" name="punti" min="1" max="10" value="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="descrizione" class="form-label">Motivo</label>
                        <textarea id="descrizione" class="form-control form-control-dark" name="descrizione" rows="2" placeholder="Es: No-show alla prenotazione, comportamento scorretto..." required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-danger" id="btnConfirmPenalty">⚠️ Aggiungi Punti</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL: RESET PENALTY
     ============================================================================ -->
<div class="modal fade" id="modalResetPenalty" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-utente-content">
            <div class="modal-header">
                <h5 class="modal-title">🔄 Reset Penalty Points</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    ⚠️ Stai per azzerare tutti i penalty points di questo utente.
                </div>
                <div class="reset-info-box">
                    <p><strong>Penalty attuali:</strong> <span id="resetCurrentPenalty">0</span> punti</p>
                    <p><strong>Dopo il reset:</strong> 0 punti</p>
                </div>
                <form id="formResetPenalty">
                    <div class="mb-3">
                        <label for="motivoReset" class="form-label">Motivo del reset (opzionale)</label>
                        <textarea id="motivoReset" class="form-control form-control-dark" name="motivo" rows="2" placeholder="Es: Comportamento migliorato, errore di sistema..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-warning" id="btnConfirmResetPenalty">🔄 Azzera Punti</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL: RIMUOVI BAN
     ============================================================================ -->
<div class="modal fade" id="modalSbanna" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-utente-content">
            <div class="modal-header" style="border-bottom: 1px solid rgba(16, 185, 129, 0.3);">
                <h5 class="modal-title">✅ Rimuovi Ban</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success-custom">
                    ✅ L'utente potrà nuovamente accedere alla piattaforma
                </div>
                <p style="color: #cbd5e1; margin-bottom: 16px;">
                    Stai per rimuovere il ban dall'utente <strong id="sbannaUserName" style="color: #f1f5f9;">-</strong>.
                </p>
                <p style="color: #94a3b8; font-size: 14px;">
                    L'utente tornerà attivo e potrà effettuare prenotazioni normalmente.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-success" id="btnConfirmSbanna">✅ Rimuovi Ban</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL: RIATTIVA UTENTE
     ============================================================================ -->
<div class="modal fade" id="modalRiattiva" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-utente-content">
            <div class="modal-header" style="border-bottom: 1px solid rgba(16, 185, 129, 0.3);">
                <h5 class="modal-title">✅ Riattiva Utente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success-custom">
                    ✅ La sospensione verrà rimossa
                </div>
                <p style="color: #cbd5e1; margin-bottom: 16px;">
                    Stai per riattivare l'utente <strong id="riattivaUserName" style="color: #f1f5f9;">-</strong>.
                </p>
                <p style="color: #94a3b8; font-size: 14px;">
                    L'utente tornerà attivo e potrà effettuare prenotazioni normalmente.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-success" id="btnConfirmRiattiva">✅ Riattiva Utente</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     JAVASCRIPT
     ============================================================================ -->
<script>
let currentUserId = null;
let currentFilters = {
    ruolo: '<?php echo $filtri['ruolo']; ?>',
    stato: '<?php echo $filtri['stato']; ?>',
    corso: '<?php echo $filtri['corso']; ?>',
    ordina: '<?php echo $filtri['ordina']; ?>',
    search: '<?php echo $filtri['search']; ?>'
};

// Click su card utente (esclusi admin)
document.querySelectorAll('.user-card:not(.admin-card)').forEach(card => {
    card.addEventListener('click', function() {
        const userId = this.dataset.userId;
        openUserModal(userId);
    });
});

// Apri modal utente
function openUserModal(userId) {
    currentUserId = userId;
    loadUserDetail(userId);
    const modal = new bootstrap.Modal(document.getElementById('modalDettaglioUtente'));
    modal.show();
}

// Carica dettaglio utente
function loadUserDetail(userId) {
    fetch(`gestione-utenti.php?action=get_user&user_id=${userId}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            populateUserModal(data);
        } else {
            showToast(data.message || 'Errore caricamento', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Errore di connessione', 'error');
    });
}

// Popola il modal con i dati
function populateUserModal(data) {
    const user = data.user;
    const stats = data.stats || {};
    
    // Header
    document.getElementById('modalUserAvatar').textContent = 
        (user.nome?.charAt(0) || '') + (user.cognome?.charAt(0) || '');
    document.getElementById('modalUserAvatar').style.background = 
        user.ruolo === 'admin' ? 'linear-gradient(135deg, #8B5CF6, #8B5CF688)' : 'linear-gradient(135deg, #3B82F6, #3B82F688)';
    document.getElementById('modalUserName').textContent = `${user.nome} ${user.cognome}`;
    
    // Status
    const statusColors = { attivo: '#10B981', sospeso: '#F59E0B', bannato: '#EF4444' };
    const statusEl = document.getElementById('modalUserStatus');
    statusEl.style.color = statusColors[user.stato] || '#10B981';
    statusEl.textContent = `● ${user.stato?.charAt(0).toUpperCase() + user.stato?.slice(1) || 'Attivo'}`;
    
    // Meta
    document.getElementById('modalUserEmail').textContent = `📧 ${user.email || '-'}`;
    document.getElementById('modalUserCorso').textContent = `🎓 ${user.corso_nome || '-'}`;
    document.getElementById('modalUserAnno').textContent = `📅 Anno ${user.anno_iscrizione || '-'}`;
    
    // Stats
    document.getElementById('statPrenotazioni').textContent = stats.totale_prenotazioni || 0;
    document.getElementById('statCompletate').textContent = stats.completate || 0;
    document.getElementById('statNoShow').textContent = stats.no_show || 0;
    document.getElementById('statCancellazioni').textContent = stats.cancellate || 0;
    
    // Affidabilità
    const penaltyPoints = user.penalty_points || 0;
    const noShow = stats.no_show || 0;
    const affidabilita = Math.max(0, Math.min(100, 100 - (penaltyPoints * 5) - (noShow * 3)));
    document.getElementById('modalAffidabilita').textContent = `${affidabilita}%`;
    const affBar = document.getElementById('modalAffidabilitaBar');
    affBar.style.width = `${affidabilita}%`;
    affBar.style.background = affidabilita >= 80 ? '#10B981' : (affidabilita >= 50 ? '#F59E0B' : '#EF4444');
    
    // Penalty
    document.getElementById('modalPenaltyValue').textContent = penaltyPoints;
    document.getElementById('actionPenaltyCount').textContent = penaltyPoints;
    
    // Disabilita il bottone Reset se i penalty sono già a 0
    const btnResetPenalty = document.getElementById('btnResetPenalty');
    if (penaltyPoints === 0) {
        btnResetPenalty.disabled = true;
        btnResetPenalty.style.opacity = '0.5';
        btnResetPenalty.style.cursor = 'not-allowed';
        btnResetPenalty.title = 'Nessun punto da azzerare';
    } else {
        btnResetPenalty.disabled = false;
        btnResetPenalty.style.opacity = '1';
        btnResetPenalty.style.cursor = 'pointer';
        btnResetPenalty.title = '';
    }
    
    // Badge - Mostra solo la descrizione (non il nome file .png)
    const badges = data.badges || [];
    document.getElementById('modalBadgeCount').textContent = badges.length;
    const badgesList = document.getElementById('modalBadgesList');
    if (badges.length > 0) {
        badgesList.innerHTML = badges.map(b => `<span class="badge-item">🏅 ${b.descrizione || b.nome}</span>`).join('');
    } else {
        badgesList.innerHTML = '<span class="no-badges">Nessun badge sbloccato</span>';
    }
    
    // Attività recenti
    const attivita = data.attivita_recenti || [];
    const activityList = document.getElementById('modalActivityList');
    if (attivita.length > 0) {
        activityList.innerHTML = attivita.map(a => `
            <div class="activity-item">
                <span class="activity-icon">${a.icona || '📋'}</span>
                <span class="activity-text">${a.descrizione}</span>
                <span class="activity-time">${formatDataItaliana(a.created_at)}</span>
            </div>
        `).join('');
    } else {
        activityList.innerHTML = '<div class="no-data">Nessuna attività recente</div>';
    }
    
    // Segnalazioni Ricevute
    const segnRicevute = data.segnalazioni_ricevute || [];
    document.getElementById('modalSegnalazioniRicevute').innerHTML = segnRicevute.length > 0 ?
        segnRicevute.map(s => `
            <div class="segnalazione-item">
                <div class="segnalazione-motivo">${getTipoSegnalazioneLabel(s.tipo)}</div>
                <div class="segnalazione-meta">Da: ${s.segnalante_nome || '-'} • ${formatDataItaliana(s.created_at)}</div>
            </div>
        `).join('') :
        '<div class="no-data">Nessuna segnalazione ricevuta</div>';
    
    // Segnalazioni Fatte
    const segnFatte = data.segnalazioni_fatte || [];
    document.getElementById('modalSegnalazioniFatte').innerHTML = segnFatte.length > 0 ?
        segnFatte.map(s => `
            <div class="segnalazione-item">
                <div class="segnalazione-motivo">${getTipoSegnalazioneLabel(s.tipo)}</div>
                <div class="segnalazione-meta">Verso: ${s.segnalato_nome || '-'} • ${formatDataItaliana(s.created_at)}</div>
            </div>
        `).join('') :
        '<div class="no-data">Nessuna segnalazione fatta</div>';
    
    // Footer - Data registrazione formato gg/mm/aaaa
    const dataReg = user.created_at ? formatDataItaliana(user.created_at) : '-';
    document.getElementById('modalRegistrato').textContent = `Registrato il ${dataReg}`;
    
    // ============================================================================
    // GESTIONE STATO UTENTE - Mostra bottoni corretti
    // ============================================================================
    const stato = user.stato || 'attivo';
    
    // Bottoni Sospensione/Riattiva
    const btnSospendi = document.getElementById('btnSospendi');
    const btnRiattiva = document.getElementById('btnRiattiva');
    const sospensioneTitle = document.getElementById('sospensioneTitle');
    const sospensioneDesc = document.getElementById('sospensioneDesc');
    
    if (stato === 'sospeso') {
        btnSospendi.style.display = 'none';
        btnRiattiva.style.display = 'inline-flex';
        sospensioneTitle.textContent = '✅ Riattiva Utente';
        sospensioneDesc.textContent = 'L\'utente è attualmente sospeso';
    } else {
        btnSospendi.style.display = 'inline-flex';
        btnRiattiva.style.display = 'none';
        sospensioneTitle.textContent = '⏸️ Sospensione';
        sospensioneDesc.textContent = 'Sospendi temporaneamente l\'utente';
    }
    
    // Bottoni Ban/Sbanna
    const btnBan = document.getElementById('btnBan');
    const btnSbanna = document.getElementById('btnSbanna');
    const banTitle = document.getElementById('banTitle');
    const banDesc = document.getElementById('banDesc');
    const boxBan = document.getElementById('boxBan');
    
    if (stato === 'bannato') {
        btnBan.style.display = 'none';
        btnSbanna.style.display = 'inline-flex';
        banTitle.textContent = '✅ Rimuovi Ban';
        banDesc.textContent = 'L\'utente è attualmente bannato';
        boxBan.classList.remove('action-box-danger');
        boxBan.classList.add('action-box-success');
    } else {
        btnBan.style.display = 'inline-flex';
        btnSbanna.style.display = 'none';
        banTitle.textContent = '🚫 Ban Permanente';
        banDesc.textContent = 'Azione irreversibile - richiede conferma';
        boxBan.classList.add('action-box-danger');
        boxBan.classList.remove('action-box-success');
    }
    
    // Info Stato
    const statoInfoDesc = document.getElementById('statoInfoDesc');
    const statoInfoBadge = document.getElementById('statoInfoBadge');
    
    const statoConfig = {
        'attivo': { desc: 'L\'utente può utilizzare normalmente la piattaforma', badge: '<span class="status-dot green"></span> Attivo', color: '#10B981' },
        'sospeso': { desc: 'L\'utente è temporaneamente sospeso', badge: '<span class="status-dot orange"></span> Sospeso', color: '#F59E0B' },
        'bannato': { desc: 'L\'utente è stato bannato permanentemente', badge: '<span class="status-dot red"></span> Bannato', color: '#EF4444' }
    };
    
    const statoInfo = statoConfig[stato] || statoConfig['attivo'];
    statoInfoDesc.textContent = statoInfo.desc;
    statoInfoBadge.innerHTML = statoInfo.badge;
    statoInfoBadge.style.borderColor = statoInfo.color;
}

// Helper per label tipo segnalazione con emoji
function getTipoSegnalazioneLabel(tipo) {
    const tipi = {
        'no_show': '🚫 No Show',
        'comportamento_scorretto': '⚠️ Comportamento Scorretto',
        'linguaggio_offensivo': '🗣️ Linguaggio Offensivo',
        'violenza': '👊 Violenza',
        'altro': '📝 Altro'
    };
    return tipi[tipo] || tipo || 'Non specificato';
}

// Formatta data in formato italiano gg/mm/aaaa
function formatDataItaliana(dataStr) {
    if (!dataStr) return '-';
    const parts = dataStr.split(' ')[0].split('-');
    if (parts.length === 3) {
        return `${parts[2]}/${parts[1]}/${parts[0]}`;
    }
    return dataStr;
}

// Toast
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 100);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Applica filtri
function applyFilters() {
    const url = new URL(window.location);
    url.searchParams.set('ruolo', currentFilters.ruolo);
    url.searchParams.set('stato', currentFilters.stato);
    url.searchParams.set('corso', currentFilters.corso);
    url.searchParams.set('ordina', currentFilters.ordina);
    url.searchParams.set('search', currentFilters.search);
    window.location = url;
}

// ============================================================================
// EVENT LISTENERS
// ============================================================================
document.addEventListener('DOMContentLoaded', function() {
    
    // ============================================================================
    // FIX MODAL - Sposta i modal nel body SUBITO per evitare problemi di z-index
    // Deve essere la prima cosa eseguita!
    // ============================================================================
    const modalsToMove = ['modalDettaglioUtente', 'modalSospensione', 'modalBan', 'modalPenalty', 'modalResetPenalty', 'modalSbanna', 'modalRiattiva'];
    modalsToMove.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });
    
    // Filter chips
    document.querySelectorAll('.filter-chip').forEach(chip => {
        chip.addEventListener('click', function() {
            const filter = this.dataset.filter;
            const value = this.dataset.value;
            currentFilters[filter] = value;
            applyFilters();
        });
    });
    
    // Selects
    document.getElementById('selectCorso').addEventListener('change', function() {
        currentFilters.corso = this.value;
        applyFilters();
    });
    
    document.getElementById('selectOrdina').addEventListener('change', function() {
        currentFilters.ordina = this.value;
        applyFilters();
    });
    
    // Ricerca
    const searchInput = document.getElementById('searchUtenti');
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
    
    // Sospendi
    document.getElementById('btnSospendi').addEventListener('click', function() {
        bootstrap.Modal.getInstance(document.getElementById('modalDettaglioUtente')).hide();
        setTimeout(() => {
            new bootstrap.Modal(document.getElementById('modalSospensione')).show();
        }, 300);
    });
    
    // Riattiva utente sospeso - Apre modal
    document.getElementById('btnRiattiva').addEventListener('click', function() {
        // Imposta il nome utente nel modal
        const userName = document.getElementById('modalUserName').textContent;
        document.getElementById('riattivaUserName').textContent = userName;
        
        // Chiudi modal dettaglio e apri modal riattiva
        bootstrap.Modal.getInstance(document.getElementById('modalDettaglioUtente')).hide();
        setTimeout(() => {
            new bootstrap.Modal(document.getElementById('modalRiattiva')).show();
        }, 300);
    });
    
    // Conferma riattivazione
    document.getElementById('btnConfirmRiattiva').addEventListener('click', function() {
        const formData = new FormData();
        formData.append('action', 'reactivate_user');
        formData.append('user_id', currentUserId);
        formData.append('ajax', '1');
        
        fetch('gestione-utenti.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            showToast(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalRiattiva')).hide();
                setTimeout(() => location.reload(), 1000);
            }
        });
    });
    
    document.getElementById('btnConfirmSospensione').addEventListener('click', function() {
        const form = document.getElementById('formSospensione');
        const giorni = form.querySelector('[name="giorni"]').value;
        const motivo = form.querySelector('[name="motivo"]').value.trim();
        
        if (!motivo) {
            showToast('Inserisci il motivo', 'error');
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'suspend_user');
        formData.append('user_id', currentUserId);
        formData.append('giorni', giorni);
        formData.append('motivo', motivo);
        formData.append('ajax', '1');
        
        fetch('gestione-utenti.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            showToast(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalSospensione')).hide();
                setTimeout(() => location.reload(), 1000);
            }
        });
    });
    
    // Ban
    document.getElementById('btnBan').addEventListener('click', function() {
        bootstrap.Modal.getInstance(document.getElementById('modalDettaglioUtente')).hide();
        setTimeout(() => {
            new bootstrap.Modal(document.getElementById('modalBan')).show();
        }, 300);
    });
    
    document.getElementById('btnConfirmBan').addEventListener('click', function() {
        const form = document.getElementById('formBan');
        const motivo = form.querySelector('[name="motivo"]').value.trim();
        const conferma = form.querySelector('[name="conferma"]').checked;
        
        if (!motivo || !conferma) {
            showToast('Compila tutti i campi e conferma', 'error');
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'ban_user');
        formData.append('user_id', currentUserId);
        formData.append('motivo', motivo);
        formData.append('ajax', '1');
        
        fetch('gestione-utenti.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            showToast(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalBan')).hide();
                setTimeout(() => location.reload(), 1000);
            }
        });
    });
    
    // Sbanna utente bannato - Apre modal
    document.getElementById('btnSbanna').addEventListener('click', function() {
        // Imposta il nome utente nel modal
        const userName = document.getElementById('modalUserName').textContent;
        document.getElementById('sbannaUserName').textContent = userName;
        
        // Chiudi modal dettaglio e apri modal sbanna
        bootstrap.Modal.getInstance(document.getElementById('modalDettaglioUtente')).hide();
        setTimeout(() => {
            new bootstrap.Modal(document.getElementById('modalSbanna')).show();
        }, 300);
    });
    
    // Conferma rimozione ban
    document.getElementById('btnConfirmSbanna').addEventListener('click', function() {
        const formData = new FormData();
        formData.append('action', 'unban_user');
        formData.append('user_id', currentUserId);
        formData.append('ajax', '1');
        
        fetch('gestione-utenti.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            showToast(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalSbanna')).hide();
                setTimeout(() => location.reload(), 1000);
            }
        });
    });
    
    // Penalty - Reset (Apre modal)
    document.getElementById('btnResetPenalty').addEventListener('click', function() {
        // Se disabilitato, non fare nulla
        if (this.disabled) return;
        
        // Mostra il numero di penalty attuali nel modal
        document.getElementById('resetCurrentPenalty').textContent = document.getElementById('actionPenaltyCount').textContent;
        bootstrap.Modal.getInstance(document.getElementById('modalDettaglioUtente')).hide();
        setTimeout(() => {
            new bootstrap.Modal(document.getElementById('modalResetPenalty')).show();
        }, 300);
    });
    
    // Conferma Reset Penalty
    document.getElementById('btnConfirmResetPenalty').addEventListener('click', function() {
        const motivo = document.querySelector('#formResetPenalty [name="motivo"]').value.trim();
        
        const formData = new FormData();
        formData.append('action', 'reset_penalty');
        formData.append('user_id', currentUserId);
        formData.append('motivo', motivo);
        formData.append('ajax', '1');
        
        fetch('gestione-utenti.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            showToast(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalResetPenalty')).hide();
                setTimeout(() => location.reload(), 1000);
            }
        });
    });
    
    // Penalty - Aggiungi (Apre modal)
    document.getElementById('btnAddPenalty').addEventListener('click', function() {
        // Mostra il numero di penalty attuali nel modal
        document.getElementById('addCurrentPenalty').textContent = document.getElementById('actionPenaltyCount').textContent;
        document.getElementById('penaltyAction').value = 'add';
        // Reset form
        document.querySelector('#formPenalty [name="punti"]').value = 1;
        document.querySelector('#formPenalty [name="descrizione"]').value = '';
        bootstrap.Modal.getInstance(document.getElementById('modalDettaglioUtente')).hide();
        setTimeout(() => {
            new bootstrap.Modal(document.getElementById('modalPenalty')).show();
        }, 300);
    });
    
    // Conferma Aggiungi Penalty
    document.getElementById('btnConfirmPenalty').addEventListener('click', function() {
        const form = document.getElementById('formPenalty');
        const punti = form.querySelector('[name="punti"]').value;
        const descrizione = form.querySelector('[name="descrizione"]').value.trim();
        
        if (!punti || punti < 1) {
            showToast('Inserisci un numero valido', 'error');
            return;
        }
        
        if (!descrizione) {
            showToast('Inserisci il motivo', 'error');
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'add_penalty');
        formData.append('user_id', currentUserId);
        formData.append('punti', punti);
        formData.append('descrizione', descrizione);
        formData.append('ajax', '1');
        
        fetch('gestione-utenti.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            showToast(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalPenalty')).hide();
                setTimeout(() => location.reload(), 1000);
            }
        });
    });
});
</script>