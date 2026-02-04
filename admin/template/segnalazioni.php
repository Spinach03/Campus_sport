<link rel="stylesheet" href="css/segnalazioni.css">
<link rel="stylesheet" href="css/modal-segnalazione.css">

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
?>

<!-- Header -->
<div class="gestione-header">
    <span class="header-icon">🚨</span>
    <p class="page-subtitle">Gestisci le segnalazioni degli utenti</p>
    
    <!-- Search -->
    <div class="search-box">
        <span class="search-icon">🔍</span>
        <input type="text" class="search-input" id="searchInput" placeholder="Cerca utente..." 
               value="<?= htmlspecialchars($templateParams['filtri']['search'] ?? '') ?>" aria-label="Cerca segnalazioni per nome utente">
    </div>
</div>

<!-- ============================================================================
     KPI CARDS - 4 card: In Attesa, Risolte, Rifiutate, Totali
     ============================================================================ -->
<div class="row g-3 mb-4">
    <!-- In Attesa -->
    <div class="col-xl-3 col-md-6 col-6">
        <div class="kpi-card" data-color="orange" data-stato="pending">
            <span class="kpi-icon">⏳</span>
            <div class="kpi-value"><?= $templateParams['stats']['pending'] ?? 0 ?></div>
            <div class="kpi-label">In Attesa</div>
            <?php if (($templateParams['stats']['pending'] ?? 0) > 0): ?>
                <span class="notification-dot"></span>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Risolte (include anche in_review) -->
    <div class="col-xl-3 col-md-6 col-6">
        <div class="kpi-card" data-color="green" data-stato="resolved">
            <span class="kpi-icon">✅</span>
            <div class="kpi-value"><?= ($templateParams['stats']['resolved'] ?? 0) + ($templateParams['stats']['in_review'] ?? 0) ?></div>
            <div class="kpi-label">Risolte</div>
        </div>
    </div>
    
    <!-- Rifiutate -->
    <div class="col-xl-3 col-md-6 col-6">
        <div class="kpi-card" data-color="red" data-stato="rejected">
            <span class="kpi-icon">❌</span>
            <div class="kpi-value"><?= $templateParams['stats']['rejected'] ?? 0 ?></div>
            <div class="kpi-label">Rifiutate</div>
        </div>
    </div>
    
    <!-- Totali -->
    <div class="col-xl-3 col-md-6 col-6">
        <div class="kpi-card" data-color="purple" data-stato="">
            <span class="kpi-icon">📋</span>
            <div class="kpi-value"><?= $templateParams['stats']['totale'] ?? 0 ?></div>
            <div class="kpi-label">Totali</div>
        </div>
    </div>
</div>

<!-- ============================================================================
     FILTRI CARD
     ============================================================================ -->
<div class="filters-card mb-4">
    <!-- Riga Tipo -->
    <div class="filter-row">
        <span class="filter-label">Tipo:</span>
        <div class="filter-chips">
            <button type="button" class="filter-chip <?= empty($templateParams['filtri']['tipo']) ? 'active' : '' ?>" data-tipo="">
                Tutti
            </button>
            <?php foreach ($templateParams['tipi_segnalazione'] as $key => $tipo): ?>
                <button type="button" class="filter-chip <?= ($templateParams['filtri']['tipo'] ?? '') === $key ? 'active' : '' ?>" 
                        data-tipo="<?= $key ?>">
                    <?= $tipo['icon'] ?> <?= $tipo['label'] ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Riga Filtri con chip In Attesa -->
    <div class="filter-row">
        <span class="filter-label">Filtri:</span>
        
        <!-- Chip In Attesa -->
        <button type="button" class="filter-chip stato-chip <?= ($templateParams['filtri']['stato'] ?? '') === 'pending' ? 'active' : '' ?>" 
                data-stato-filter="pending">
            ⏳ In Attesa
        </button>
        
        <!-- Priorità -->
        <select id="filtroPriorita" class="sort-select" aria-label="Filtra per priorità">
            <option value="" <?= empty($templateParams['filtri']['priorita']) ? 'selected' : '' ?>>Tutte le priorità</option>
            <option value="alta" <?= ($templateParams['filtri']['priorita'] ?? '') === 'alta' ? 'selected' : '' ?>>🔴 Alta</option>
            <option value="media" <?= ($templateParams['filtri']['priorita'] ?? '') === 'media' ? 'selected' : '' ?>>🟡 Media</option>
            <option value="bassa" <?= ($templateParams['filtri']['priorita'] ?? '') === 'bassa' ? 'selected' : '' ?>>🟢 Bassa</option>
        </select>
        
        <!-- Ordinamento -->
        <select id="filtroOrdina" class="sort-select" aria-label="Ordina segnalazioni per">
            <option value="recenti" <?= ($templateParams['filtri']['ordina'] ?? '') === 'recenti' ? 'selected' : '' ?>>Più recenti</option>
            <option value="vecchie" <?= ($templateParams['filtri']['ordina'] ?? '') === 'vecchie' ? 'selected' : '' ?>>Più vecchie</option>
            <option value="priorita" <?= ($templateParams['filtri']['ordina'] ?? '') === 'priorita' ? 'selected' : '' ?>>Priorità</option>
            <option value="tipo" <?= ($templateParams['filtri']['ordina'] ?? '') === 'tipo' ? 'selected' : '' ?>>Tipo</option>
        </select>
    </div>
</div>

<!-- ============================================================================
     GRIGLIA SEGNALAZIONI
     ============================================================================ -->
<div class="segnalazioni-grid">
    <?php if (empty($templateParams['segnalazioni'])): ?>
    <div class="no-results">
        <div class="no-results-icon">📭</div>
        <h3>Nessuna segnalazione trovata</h3>
        <p>Non ci sono segnalazioni che corrispondono ai filtri selezionati.</p>
    </div>
    <?php else: ?>
    
    <?php foreach ($templateParams['segnalazioni'] as $segnalazione): 
        $tipoInfo = $templateParams['tipi_segnalazione'][$segnalazione['tipo']] ?? ['icon' => '📝', 'label' => $segnalazione['tipo'], 'color' => '#6B7280'];
        // Contorno rosso SOLO per segnalazioni pending
        $isUrgent = $segnalazione['stato'] === 'pending';
        // Mappa lo stato per il CSS (in_review diventa resolved)
        $statoCss = ($segnalazione['stato'] === 'in_review') ? 'resolved' : $segnalazione['stato'];
    ?>
    <!-- Card cliccabile -->
    <div class="segnalazione-card <?= $isUrgent ? 'urgent' : '' ?>" 
         data-id="<?= $segnalazione['segnalazione_id'] ?>"
         onclick="apriDettaglio(<?= $segnalazione['segnalazione_id'] ?>)">
        
        <!-- Header Card -->
        <div class="segnalazione-card-header">
            <div class="tipo-badge" style="--tipo-color: <?= $tipoInfo['color'] ?>; border-color: <?= $tipoInfo['color'] ?>; color: <?= $tipoInfo['color'] ?>;">
                <?= $tipoInfo['icon'] ?> <?= $tipoInfo['label'] ?>
            </div>
            <div class="badges-right">
                <span class="stato-badge stato-<?= $statoCss ?>">
                    <?php 
                    // in_review viene mostrato come RISOLTA
                    $statoLabels = ['pending' => 'IN ATTESA', 'in_review' => 'RISOLTA', 'resolved' => 'RISOLTA', 'rejected' => 'RIFIUTATA'];
                    echo $statoLabels[$segnalazione['stato']] ?? $segnalazione['stato'];
                    ?>
                </span>
                <?php if ($segnalazione['priorita'] === 'alta'): ?>
                    <span class="priorita-badge alta">🔴 Alta</span>
                <?php elseif ($segnalazione['priorita'] === 'media'): ?>
                    <span class="priorita-badge media">🟡 Media</span>
                <?php else: ?>
                    <span class="priorita-badge bassa">🟢 Bassa</span>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Utenti coinvolti -->
        <div class="segnalazione-users">
            <div class="user-box">
                <div class="user-avatar segnalante"><?= getInitials($segnalazione['segnalante_nome']) ?></div>
                <div class="user-label">SEGNALANTE</div>
                <div class="user-name"><?= htmlspecialchars($segnalazione['segnalante_nome']) ?></div>
            </div>
            <div class="arrow-separator">➡️</div>
            <div class="user-box">
                <div class="user-avatar segnalato"><?= getInitials($segnalazione['segnalato_nome']) ?></div>
                <div class="user-label">SEGNALATO</div>
                <div class="user-name"><?= htmlspecialchars($segnalazione['segnalato_nome']) ?></div>
            </div>
        </div>
        
        <!-- Descrizione -->
        <div class="segnalazione-description">
            <?= htmlspecialchars(mb_substr($segnalazione['descrizione'], 0, 100)) ?><?= mb_strlen($segnalazione['descrizione']) > 100 ? '...' : '' ?>
        </div>
        
        <!-- Footer (senza bottone) -->
        <div class="segnalazione-footer">
            <div class="footer-meta">
                <span>📅 <?= date('d/m/Y H:i', strtotime($segnalazione['created_at'])) ?></span>
                <?php if ($segnalazione['prenotazione_id']): ?>
                    <span>📍 Collegata a prenotazione</span>
                <?php endif; ?>
                <?php if ($segnalazione['admin_nome']): ?>
                    <span>👤 <?= htmlspecialchars($segnalazione['admin_nome']) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php endif; ?>
</div>

<!-- ============================================================================
     MODAL: DETTAGLIO SEGNALAZIONE
     ============================================================================ -->
<div class="modal fade" id="modalDettaglio" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="z-index: 1061;">
        <div class="modal-content modal-segnalazione-content" style="pointer-events: auto;">
            <div class="modal-header">
                <h5 class="modal-title">
                    🚨 Dettaglio Segnalazione #<span id="modalSegnalazioneId"></span>
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
     MODAL: RISOLUZIONE
     ============================================================================ -->
<div class="modal fade" id="modalRisoluzione" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="z-index: 1061;">
        <div class="modal-content modal-segnalazione-content" style="pointer-events: auto;">
            <div class="modal-header resolve-header">
                <h5 class="modal-title">✅ Risolvi Segnalazione</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>
            <div class="modal-body">
                <form id="formRisoluzione">
                    <input type="hidden" id="resolveId" name="id">
                    
                    <div class="mb-3">
                        <label for="resolveAzione" class="form-label">Azione da intraprendere</label>
                        <select id="resolveAzione" name="azione" class="form-select" required>
                            <option value="nessuna">Nessuna azione</option>
                            <option value="warning">Invia Warning</option>
                            <option value="penalty_points">Assegna Penalty Points</option>
                            <option value="sospensione">Sospensione Temporanea</option>
                            <option value="ban">Ban Permanente</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="penaltyGroup" style="display: none;">
                        <label for="resolvePenalty" class="form-label">Penalty Points da assegnare</label>
                        <input type="number" id="resolvePenalty" name="penalty_points" class="form-control" min="1" max="100" value="5">
                    </div>
                    
                    <div class="mb-3" id="sospensioneGroup" style="display: none;">
                        <label for="resolveGiorni" class="form-label">Giorni di sospensione</label>
                        <select id="resolveGiorni" name="giorni_sospensione" class="form-select">
                            <option value="1">1 giorno</option>
                            <option value="3">3 giorni</option>
                            <option value="7" selected>7 giorni</option>
                            <option value="14">14 giorni</option>
                            <option value="30">30 giorni</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="rejectMotivo" class="form-label">Note risoluzione <span class="text-danger">*</span></label>
                        <textarea id="resolveNote" name="note" class="form-control" rows="4" required
                                  placeholder="Descrivi il ragionamento e perché questa azione è appropriata..."></textarea>
                    </div>
                    
                    <div class="form-check">
                        <input type="checkbox" id="resolveNotifiche" name="invia_notifiche" class="form-check-input" checked>
                        <label for="resolveNotifiche" class="form-check-label">
                            Invia notifiche automatiche a segnalante e segnalato
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-success" onclick="submitRisoluzione()">
                    ✅ Conferma Risoluzione
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL: RIFIUTO
     ============================================================================ -->
<div class="modal fade" id="modalRifiuto" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered" style="z-index: 1061;">
        <div class="modal-content modal-segnalazione-content" style="pointer-events: auto;">
            <div class="modal-header reject-header">
                <h5 class="modal-title">❌ Rifiuta Segnalazione</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>
            <div class="modal-body">
                <form id="formRifiuto">
                    <input type="hidden" id="rejectId" name="id">
                    
                    <div class="mb-3">
                        <label class="form-label">Motivo del rifiuto <span class="text-danger">*</span></label>
                        <textarea id="rejectMotivo" name="motivo" class="form-control" rows="4" required
                                  placeholder="Spiega perché la segnalazione viene rifiutata..."></textarea>
                    </div>
                    
                    <div class="form-check">
                        <input type="checkbox" id="rejectNotifica" name="invia_notifica" class="form-check-input" checked>
                        <label for="rejectNotifica" class="form-check-label">
                            Notifica il segnalante del rifiuto
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-danger" onclick="submitRifiuto()">
                    ❌ Conferma Rifiuto
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form per filtri -->
<form id="filtriForm" method="GET" style="display: none;">
    <input type="hidden" name="stato" id="filtroStato" value="<?= htmlspecialchars($templateParams['filtri']['stato'] ?? '') ?>">
    <input type="hidden" name="tipo" id="filtroTipo" value="<?= htmlspecialchars($templateParams['filtri']['tipo'] ?? '') ?>">
    <input type="hidden" name="priorita" id="filtroPrioritaHidden" value="<?= htmlspecialchars($templateParams['filtri']['priorita'] ?? '') ?>">
    <input type="hidden" name="ordina" id="filtroOrdinaHidden" value="<?= htmlspecialchars($templateParams['filtri']['ordina'] ?? 'recenti') ?>">
    <input type="hidden" name="search" id="filtroSearch" value="<?= htmlspecialchars($templateParams['filtri']['search'] ?? '') ?>">
</form>

<script>
const tipiSegnalazione = <?= json_encode($templateParams['tipi_segnalazione']) ?>;
let currentSegnalazioneId = null;

document.addEventListener('DOMContentLoaded', function() {
    // =========================================================================
    // FIX MODAL - Sposta i modal nel body per evitare stacking context issues
    // DEVE essere la PRIMA cosa eseguita!
    // =========================================================================
    ['modalDettaglio', 'modalRisoluzione', 'modalRifiuto'].forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });
    
    // Click su KPI cards per filtrare
    document.querySelectorAll('.kpi-card[data-stato]').forEach(card => {
        card.addEventListener('click', function() {
            document.getElementById('filtroStato').value = this.dataset.stato;
            document.getElementById('filtriForm').submit();
        });
    });
    
    // Click su filter chips tipo
    document.querySelectorAll('.filter-chip[data-tipo]').forEach(chip => {
        chip.addEventListener('click', function() {
            document.getElementById('filtroTipo').value = this.dataset.tipo;
            document.getElementById('filtriForm').submit();
        });
    });
    
    // Click su chip "In Attesa" (toggle)
    document.querySelectorAll('.filter-chip[data-stato-filter]').forEach(chip => {
        chip.addEventListener('click', function() {
            const currentStato = document.getElementById('filtroStato').value;
            if (currentStato === this.dataset.statoFilter) {
                document.getElementById('filtroStato').value = '';
            } else {
                document.getElementById('filtroStato').value = this.dataset.statoFilter;
            }
            document.getElementById('filtriForm').submit();
        });
    });
    
    document.getElementById('filtroPriorita').addEventListener('change', function() {
        document.getElementById('filtroPrioritaHidden').value = this.value;
        document.getElementById('filtriForm').submit();
    });
    
    document.getElementById('filtroOrdina').addEventListener('change', function() {
        document.getElementById('filtroOrdinaHidden').value = this.value;
        document.getElementById('filtriForm').submit();
    });
    
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            document.getElementById('filtroSearch').value = this.value;
            document.getElementById('filtriForm').submit();
        }, 500);
    });
    
    document.getElementById('resolveAzione').addEventListener('change', function() {
        document.getElementById('penaltyGroup').style.display = this.value === 'penalty_points' ? 'block' : 'none';
        document.getElementById('sospensioneGroup').style.display = this.value === 'sospensione' ? 'block' : 'none';
    });
});

// Helper per pulire backdrop residui
function cleanupBackdrops() {
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
}

// Apri modal dettaglio
function apriDettaglio(id) {
    currentSegnalazioneId = id;
    document.getElementById('modalSegnalazioneId').textContent = id;
    document.getElementById('modalContent').innerHTML = '<div class="loading-spinner"><div class="spinner"></div><p>Caricamento...</p></div>';
    
    const modal = new bootstrap.Modal(document.getElementById('modalDettaglio'));
    modal.show();
    
    fetch(`segnalazioni.php?ajax=1&action=get_segnalazione&id=${id}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) renderDettaglio(data.segnalazione);
            else document.getElementById('modalContent').innerHTML = `<div class="error-message">❌ ${data.message}</div>`;
        })
        .catch(() => document.getElementById('modalContent').innerHTML = '<div class="error-message">❌ Errore di connessione</div>');
}

function renderDettaglio(s) {
    const tipoInfo = tipiSegnalazione[s.tipo] || {icon: '📝', label: s.tipo, color: '#6B7280'};
    const profSegnalante = s.profilo_segnalante || {};
    const profSegnalato = s.profilo_segnalato || {};
    const totSegnFatte = profSegnalante.segnalazioni_fatte || 0;
    const segnValidate = profSegnalante.segnalazioni_validate || 0;
    const credibilita = totSegnFatte > 0 ? Math.round((segnValidate / totSegnFatte) * 100) : 100;
    const totPren = profSegnalato.prenotazioni_totali || 0;
    const prenComplete = profSegnalato.prenotazioni_completate || 0;
    const affidabilita = totPren > 0 ? Math.round((prenComplete / totPren) * 100) : 100;
    
    // Mappa lo stato per il CSS (in_review → resolved)
    const statoCss = (s.stato === 'in_review') ? 'resolved' : s.stato;
    
    let html = `
        <div class="dettaglio-container">
            <div class="dettaglio-header">
                <div class="tipo-grande" style="--tipo-color: ${tipoInfo.color}; border-color: ${tipoInfo.color}; color: ${tipoInfo.color};">${tipoInfo.icon} ${tipoInfo.label}</div>
                <span class="stato-badge stato-${statoCss}">${getStatoLabel(s.stato)}</span>
                <span class="priorita-badge priorita-${s.priorita}">${getPrioritaIcon(s.priorita)} ${s.priorita}</span>
                <span class="data-badge">📅 ${formatDate(s.created_at)}</span>
            </div>
            <div class="dettaglio-section"><h6>📝 Descrizione</h6><div class="descrizione-box">${escapeHtml(s.descrizione)}</div></div>
            ${s.contesto_prenotazione ? `<div class="dettaglio-section contesto-section"><h6>📍 Prenotazione Collegata</h6><div class="contesto-grid"><span><strong>Campo:</strong> ${escapeHtml(s.contesto_prenotazione.campo_nome)}</span><span><strong>Sport:</strong> ${escapeHtml(s.contesto_prenotazione.sport_nome)}</span><span><strong>Data:</strong> ${formatDataSola(s.contesto_prenotazione.data_prenotazione)}</span><span><strong>Orario:</strong> ${formatOrario(s.contesto_prenotazione.ora_inizio)} - ${formatOrario(s.contesto_prenotazione.ora_fine)}</span></div></div>` : ''}
            <div class="row g-3 mb-3">
                <div class="col-md-6"><div class="profilo-box segnalante"><h6>👤 Segnalante</h6><div class="profilo-nome">${escapeHtml(profSegnalante.nome || '')} ${escapeHtml(profSegnalante.cognome || '')}</div><div class="profilo-email">${escapeHtml(profSegnalante.email || '')}</div><div class="profilo-stats"><div><span>Segnalazioni fatte:</span> <strong>${totSegnFatte}</strong></div><div><span>Validate:</span> <strong class="text-success">${segnValidate}</strong></div><div><span>Rifiutate:</span> <strong class="text-danger">${profSegnalante.segnalazioni_rifiutate || 0}</strong></div><div><span>Credibilità:</span> <strong class="${credibilita >= 70 ? 'text-success' : 'text-warning'}">${credibilita}%</strong></div></div></div></div>
                <div class="col-md-6"><div class="profilo-box segnalato"><h6>⚠️ Segnalato</h6><div class="profilo-nome">${escapeHtml(profSegnalato.nome || '')} ${escapeHtml(profSegnalato.cognome || '')}</div><div class="profilo-email">${escapeHtml(profSegnalato.email || '')}</div><div class="profilo-stats"><div><span>Penalty Points:</span> <strong class="text-warning">${profSegnalato.penalty_points || 0}</strong></div><div><span>Segnalazioni ricevute:</span> <strong>${profSegnalato.segnalazioni_ricevute || 0}</strong></div><div><span>No-Show:</span> <strong class="text-danger">${profSegnalato.no_show_totali || 0}</strong></div><div><span>Affidabilità:</span> <strong class="${affidabilita >= 70 ? 'text-success' : 'text-warning'}">${affidabilita}%</strong></div></div></div></div>
            </div>
            ${s.storico_segnalazioni && s.storico_segnalazioni.length > 0 ? `<div class="dettaglio-section"><h6>📋 Storico Segnalazioni Ricevute</h6><div class="storico-list">${s.storico_segnalazioni.map(seg => {
                const segStatoCss = (seg.stato === 'in_review') ? 'resolved' : seg.stato;
                return `<div class="storico-item"><span>${tipiSegnalazione[seg.tipo]?.icon || '📝'} ${tipiSegnalazione[seg.tipo]?.label || seg.tipo}</span><span class="stato-badge stato-${segStatoCss}">${getStatoLabel(seg.stato)}</span><span class="text-muted">${formatDate(seg.created_at)}</span></div>`;
            }).join('')}</div></div>` : ''}
            ${s.stato === 'resolved' || s.stato === 'rejected' || s.stato === 'in_review' ? `<div class="dettaglio-section ${(s.stato === 'resolved' || s.stato === 'in_review') ? 'resolved-section' : 'rejected-section'}"><h6>${(s.stato === 'resolved' || s.stato === 'in_review') ? '✅ Risoluzione' : '❌ Rifiuto'}</h6>${s.azione_intrapresa ? `<p><strong>Azione:</strong> ${getAzioneLabel(s.azione_intrapresa)}</p>` : ''}${s.penalty_assegnati ? `<p><strong>Penalty:</strong> ${s.penalty_assegnati}</p>` : ''}<p><strong>Note:</strong> ${escapeHtml(s.note_risoluzione || 'Nessuna nota')}</p><p class="text-muted"><small>Gestita da ${escapeHtml(s.admin_nome || 'N/A')} il ${formatDate(s.resolved_at)}</small></p></div>` : ''}
            ${s.stato === 'pending' ? `<div class="dettaglio-actions"><button type="button" class="btn btn-success" onclick="apriRisoluzione(${s.segnalazione_id})">✅ Risolvi</button><button type="button" class="btn btn-danger" onclick="apriRifiuto(${s.segnalazione_id})">❌ Rifiuta</button></div>` : ''}
        </div>`;
    document.getElementById('modalContent').innerHTML = html;
}

function apriRisoluzione(id) {
    document.getElementById('resolveId').value = id;
    document.getElementById('resolveAzione').value = 'nessuna';
    document.getElementById('resolveNote').value = '';
    document.getElementById('penaltyGroup').style.display = 'none';
    document.getElementById('sospensioneGroup').style.display = 'none';
    
    // Chiudi modal dettaglio
    bootstrap.Modal.getInstance(document.getElementById('modalDettaglio')).hide();
    
    // Aspetta, pulisci backdrop e apri nuovo modal
    setTimeout(() => {
        cleanupBackdrops();
        new bootstrap.Modal(document.getElementById('modalRisoluzione')).show();
    }, 350);
}

function submitRisoluzione() {
    const form = document.getElementById('formRisoluzione');
    if (!form.checkValidity()) { form.reportValidity(); return; }
    const formData = new FormData(form); 
    formData.append('ajax', '1'); 
    formData.append('action', 'resolve');
    fetch('segnalazioni.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => { 
            showToast(data.message, data.success ? 'success' : 'error'); 
            if (data.success) { 
                bootstrap.Modal.getInstance(document.getElementById('modalRisoluzione')).hide(); 
                setTimeout(() => location.reload(), 1000); 
            } 
        });
}

function apriRifiuto(id) {
    document.getElementById('rejectId').value = id;
    document.getElementById('rejectMotivo').value = '';
    
    // Chiudi modal dettaglio
    bootstrap.Modal.getInstance(document.getElementById('modalDettaglio')).hide();
    
    // Aspetta, pulisci backdrop e apri nuovo modal
    setTimeout(() => {
        cleanupBackdrops();
        new bootstrap.Modal(document.getElementById('modalRifiuto')).show();
    }, 350);
}

function submitRifiuto() {
    const form = document.getElementById('formRifiuto');
    if (!form.checkValidity()) { form.reportValidity(); return; }
    const formData = new FormData(form); 
    formData.append('ajax', '1'); 
    formData.append('action', 'reject');
    fetch('segnalazioni.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => { 
            showToast(data.message, data.success ? 'success' : 'error'); 
            if (data.success) { 
                bootstrap.Modal.getInstance(document.getElementById('modalRifiuto')).hide(); 
                setTimeout(() => location.reload(), 1000); 
            } 
        });
}

// in_review rimosso - ora solo 3 stati
function getStatoLabel(stato) { return {pending: 'IN ATTESA', resolved: 'RISOLTA', rejected: 'RIFIUTATA'}[stato] || stato; }
function getPrioritaIcon(p) { return {alta: '🔴', media: '🟡', bassa: '🟢'}[p] || '⚪'; }
function getAzioneLabel(a) { return {nessuna: 'Nessuna', warning: 'Warning', penalty_points: 'Penalty', sospensione: 'Sospensione', ban: 'Ban'}[a] || a; }
function formatDate(d) { if (!d) return 'N/A'; return new Date(d).toLocaleDateString('it-IT', {day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit'}); }
function formatDataSola(d) { if (!d) return 'N/A'; const parts = d.split('-'); return parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : d; }
function formatOrario(o) { if (!o) return ''; return o.substring(0, 5); }
function escapeHtml(t) { if (!t) return ''; const d = document.createElement('div'); d.textContent = t; return d.innerHTML; }
function showToast(msg, type) { const t = document.createElement('div'); t.className = `toast-notification toast-${type}`; t.textContent = msg; document.body.appendChild(t); setTimeout(() => t.classList.add('show'), 10); setTimeout(() => { t.classList.remove('show'); setTimeout(() => t.remove(), 300); }, 3000); }
</script>