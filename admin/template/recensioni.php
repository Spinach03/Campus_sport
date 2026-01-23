<link rel="stylesheet" href="css/recensioni.css">
<link rel="stylesheet" href="css/modal-recensione.css">

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

// Helper per stelle
function renderStars($rating) {
    $html = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $html .= '⭐';
        } else {
            $html .= '<span class="star-empty">☆</span>';
        }
    }
    return $html;
}
?>

<!-- Header -->
<div class="gestione-header">
    <span class="header-icon">⭐</span>
    <p class="page-subtitle">Gestisci le recensioni dei campi sportivi</p>
    
    <!-- Search -->
    <div class="search-box">
        <span class="search-icon">🔍</span>
        <input type="text" class="search-input" id="searchInput" placeholder="Cerca utente, campo, commento..." 
               value="<?= htmlspecialchars($templateParams['filtri']['search'] ?? '') ?>">
    </div>
</div>

<!-- ============================================================================
     KPI CARDS
     ============================================================================ -->
<div class="row g-3 mb-4">
    <!-- Totale Recensioni -->
    <div class="col-xl-3 col-md-6 col-6">
        <div class="kpi-card" data-color="purple">
            <span class="kpi-icon">📝</span>
            <div class="kpi-value"><?= $templateParams['stats']['totale'] ?? 0 ?></div>
            <div class="kpi-label">Totale Recensioni</div>
        </div>
    </div>
    
    <!-- Media Generale -->
    <div class="col-xl-3 col-md-6 col-6">
        <div class="kpi-card" data-color="orange">
            <span class="kpi-icon">⭐</span>
            <div class="kpi-value"><?= $templateParams['stats']['media_generale'] ?? '0.0' ?></div>
            <div class="kpi-label">Rating Medio</div>
        </div>
    </div>
    
    <!-- Positive (4-5 stelle) -->
    <div class="col-xl-3 col-md-6 col-6">
        <div class="kpi-card" data-color="green" data-rating="positive">
            <span class="kpi-icon">😊</span>
            <div class="kpi-value"><?= $templateParams['stats']['positive'] ?? 0 ?></div>
            <div class="kpi-label">Positive (4-5⭐)</div>
        </div>
    </div>
    
    <!-- Senza Risposta -->
    <div class="col-xl-3 col-md-6 col-6">
        <div class="kpi-card" data-color="red" data-risposta="senza">
            <span class="kpi-icon">💬</span>
            <div class="kpi-value"><?= $templateParams['stats']['senza_risposta'] ?? 0 ?></div>
            <div class="kpi-label">Senza Risposta</div>
            <?php if (($templateParams['stats']['senza_risposta'] ?? 0) > 0): ?>
                <span class="notification-dot"></span>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ============================================================================
     FILTRI CARD
     ============================================================================ -->
<div class="filters-card mb-4">
    <!-- Riga Rating -->
    <div class="filter-row">
        <span class="filter-label">Rating:</span>
        <div class="filter-chips">
            <button type="button" class="filter-chip <?= empty($templateParams['filtri']['rating']) ? 'active' : '' ?>" data-rating="">
                Tutti
            </button>
            <button type="button" class="filter-chip <?= ($templateParams['filtri']['rating'] ?? '') === 'positive' ? 'active' : '' ?>" data-rating="positive">
                😊 Positive (4-5)
            </button>
            <button type="button" class="filter-chip <?= ($templateParams['filtri']['rating'] ?? '') === 'neutre' ? 'active' : '' ?>" data-rating="neutre">
                😐 Neutre (3)
            </button>
            <button type="button" class="filter-chip <?= ($templateParams['filtri']['rating'] ?? '') === 'negative' ? 'active' : '' ?>" data-rating="negative">
                😞 Negative (1-2)
            </button>
        </div>
    </div>
    
    <!-- Riga Filtri -->
    <div class="filter-row">
        <span class="filter-label">Filtri:</span>
        
        <!-- Chip Senza Risposta -->
        <button type="button" class="filter-chip risposta-chip <?= ($templateParams['filtri']['risposta'] ?? '') === 'senza' ? 'active' : '' ?>" 
                data-risposta-filter="senza">
            💬 Senza Risposta
        </button>
        
        <!-- Sport -->
        <select id="filtroSport" class="sort-select">
            <option value="" <?= empty($templateParams['filtri']['sport_id']) ? 'selected' : '' ?>>Tutti gli sport</option>
            <?php foreach ($templateParams['sport'] as $sport): ?>
                <option value="<?= $sport['sport_id'] ?>" <?= ($templateParams['filtri']['sport_id'] ?? '') == $sport['sport_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sport['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <!-- Campo -->
        <select id="filtroCampo" class="sort-select">
            <option value="" <?= empty($templateParams['filtri']['campo_id']) ? 'selected' : '' ?>>Tutti i campi</option>
            <?php foreach ($templateParams['campi'] as $campo): ?>
                <option value="<?= $campo['campo_id'] ?>" <?= ($templateParams['filtri']['campo_id'] ?? '') == $campo['campo_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($campo['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <!-- Ordinamento -->
        <select id="filtroOrdina" class="sort-select">
            <option value="recenti" <?= ($templateParams['filtri']['ordina'] ?? '') === 'recenti' ? 'selected' : '' ?>>Più recenti</option>
            <option value="vecchie" <?= ($templateParams['filtri']['ordina'] ?? '') === 'vecchie' ? 'selected' : '' ?>>Più vecchie</option>
            <option value="rating_alto" <?= ($templateParams['filtri']['ordina'] ?? '') === 'rating_alto' ? 'selected' : '' ?>>Rating più alto</option>
            <option value="rating_basso" <?= ($templateParams['filtri']['ordina'] ?? '') === 'rating_basso' ? 'selected' : '' ?>>Rating più basso</option>
            <option value="campo" <?= ($templateParams['filtri']['ordina'] ?? '') === 'campo' ? 'selected' : '' ?>>Per campo</option>
        </select>
    </div>
</div>

<!-- ============================================================================
     GRIGLIA RECENSIONI
     ============================================================================ -->
<div class="recensioni-grid">
    <?php if (empty($templateParams['recensioni'])): ?>
    <div class="no-results">
        <div class="no-results-icon">📭</div>
        <h3>Nessuna recensione trovata</h3>
        <p>Non ci sono recensioni che corrispondono ai filtri selezionati.</p>
    </div>
    <?php else: ?>
    
    <?php foreach ($templateParams['recensioni'] as $recensione): 
        $hasRisposta = ($recensione['num_risposte'] ?? 0) > 0;
        $ratingClass = $recensione['rating_generale'] >= 4 ? 'positive' : ($recensione['rating_generale'] <= 2 ? 'negative' : 'neutral');
    ?>
    <!-- Card cliccabile -->
    <div class="recensione-card <?= !$hasRisposta ? 'no-risposta' : '' ?>" 
         data-id="<?= $recensione['recensione_id'] ?>"
         onclick="apriDettaglio(<?= $recensione['recensione_id'] ?>)">
        
        <!-- Header Card -->
        <div class="recensione-card-header">
            <div class="campo-info">
                <span class="sport-icon"><?= $recensione['sport_icona'] ?? '🏟️' ?></span>
                <div class="campo-details">
                    <div class="campo-nome"><?= htmlspecialchars($recensione['campo_nome']) ?></div>
                    <div class="sport-nome"><?= htmlspecialchars($recensione['sport_nome']) ?></div>
                </div>
            </div>
            <div class="rating-badge rating-<?= $ratingClass ?>">
                <?= $recensione['rating_generale'] ?> ⭐
            </div>
        </div>
        
        <!-- Utente -->
        <div class="recensione-user">
            <div class="user-avatar"><?= getInitials($recensione['utente_nome']) ?></div>
            <div class="user-info">
                <div class="user-name"><?= htmlspecialchars($recensione['utente_nome']) ?></div>
                <div class="user-email"><?= htmlspecialchars($recensione['utente_email']) ?></div>
            </div>
        </div>
        
        <!-- Stelle dettagliate -->
        <div class="rating-stars">
            <?= renderStars($recensione['rating_generale']) ?>
        </div>
        
        <!-- Commento -->
        <?php if (!empty($recensione['commento'])): ?>
        <div class="recensione-commento">
            "<?= htmlspecialchars(mb_substr($recensione['commento'], 0, 120)) ?><?= mb_strlen($recensione['commento']) > 120 ? '...' : '' ?>"
        </div>
        <?php else: ?>
        <div class="recensione-commento no-comment">
            <em>Nessun commento</em>
        </div>
        <?php endif; ?>
        
        <!-- Footer -->
        <div class="recensione-footer">
            <div class="footer-meta">
                <span>📅 <?= date('d/m/Y', strtotime($recensione['created_at'])) ?></span>
                <?php if ($hasRisposta): ?>
                    <span class="risposta-badge">✅ Risposta</span>
                <?php else: ?>
                    <span class="no-risposta-badge">💬 Da rispondere</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php endif; ?>
</div>

<!-- ============================================================================
     MODAL: DETTAGLIO RECENSIONE
     ============================================================================ -->
<div class="modal fade" id="modalDettaglio" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="z-index: 1061;">
        <div class="modal-content modal-recensione-content" style="pointer-events: auto;">
            <div class="modal-header">
                <h5 class="modal-title">
                    ⭐ Dettaglio Recensione #<span id="modalRecensioneId"></span>
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
     MODAL: RISPONDI
     ============================================================================ -->
<div class="modal fade" id="modalRisposta" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="z-index: 1061;">
        <div class="modal-content modal-recensione-content" style="pointer-events: auto;">
            <div class="modal-header risposta-header">
                <h5 class="modal-title">💬 Rispondi alla Recensione</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>
            <div class="modal-body">
                <form id="formRisposta">
                    <input type="hidden" id="rispostaRecensioneId" name="id">
                    
                    <div class="risposta-context mb-3">
                        <div class="context-label">Stai rispondendo a:</div>
                        <div id="rispostaContext" class="context-content"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">La tua risposta <span class="text-danger">*</span></label>
                        <textarea id="rispostaTesto" name="testo" class="form-control" rows="5" required minlength="10"
                                  placeholder="Scrivi una risposta professionale e cortese..."></textarea>
                        <div class="form-text">Minimo 10 caratteri. La risposta sarà visibile pubblicamente.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-primary" onclick="submitRisposta()">
                    💬 Invia Risposta
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL: CONFERMA ELIMINAZIONE RECENSIONE
     ============================================================================ -->
<div class="modal fade" id="modalElimina" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered" style="z-index: 1061;">
        <div class="modal-content modal-recensione-content" style="pointer-events: auto;">
            <div class="modal-header delete-header">
                <h5 class="modal-title">🗑️ Conferma Eliminazione</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>
            <div class="modal-body">
                <p>Sei sicuro di voler eliminare questa recensione?</p>
                <p class="text-warning"><strong>⚠️ Attenzione:</strong> Questa azione è irreversibile e rimuoverà anche tutte le risposte associate.</p>
                <input type="hidden" id="eliminaRecensioneId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-danger" onclick="confermaElimina()">
                    🗑️ Elimina Definitivamente
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL: CONFERMA ELIMINAZIONE RISPOSTA
     ============================================================================ -->
<div class="modal fade" id="modalEliminaRisposta" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered" style="z-index: 1071;">
        <div class="modal-content modal-recensione-content" style="pointer-events: auto;">
            <div class="modal-header delete-header">
                <h5 class="modal-title">🗑️ Elimina Risposta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>
            <div class="modal-body">
                <p>Sei sicuro di voler eliminare questa risposta?</p>
                <p class="text-muted"><small>Questa azione è irreversibile.</small></p>
                <input type="hidden" id="eliminaRispostaId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-danger" onclick="confermaEliminaRisposta()">
                    🗑️ Elimina
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form per filtri -->
<form id="filtriForm" method="GET" style="display: none;">
    <input type="hidden" name="rating" id="filtroRatingHidden" value="<?= htmlspecialchars($templateParams['filtri']['rating'] ?? '') ?>">
    <input type="hidden" name="risposta" id="filtroRispostaHidden" value="<?= htmlspecialchars($templateParams['filtri']['risposta'] ?? '') ?>">
    <input type="hidden" name="sport_id" id="filtroSportHidden" value="<?= htmlspecialchars($templateParams['filtri']['sport_id'] ?? '') ?>">
    <input type="hidden" name="campo_id" id="filtroCampoHidden" value="<?= htmlspecialchars($templateParams['filtri']['campo_id'] ?? '') ?>">
    <input type="hidden" name="ordina" id="filtroOrdinaHidden" value="<?= htmlspecialchars($templateParams['filtri']['ordina'] ?? 'recenti') ?>">
    <input type="hidden" name="search" id="filtroSearch" value="<?= htmlspecialchars($templateParams['filtri']['search'] ?? '') ?>">
</form>

<script>
let currentRecensioneId = null;

document.addEventListener('DOMContentLoaded', function() {
    // FIX MODAL - Sposta i modal nel body
    ['modalDettaglio', 'modalRisposta', 'modalElimina', 'modalEliminaRisposta'].forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });
    
    // Click su KPI cards per filtrare
    document.querySelectorAll('.kpi-card[data-rating]').forEach(card => {
        card.addEventListener('click', function() {
            document.getElementById('filtroRatingHidden').value = this.dataset.rating;
            document.getElementById('filtriForm').submit();
        });
    });
    
    document.querySelectorAll('.kpi-card[data-risposta]').forEach(card => {
        card.addEventListener('click', function() {
            document.getElementById('filtroRispostaHidden').value = this.dataset.risposta;
            document.getElementById('filtriForm').submit();
        });
    });
    
    // Click su filter chips rating
    document.querySelectorAll('.filter-chip[data-rating]').forEach(chip => {
        chip.addEventListener('click', function() {
            document.getElementById('filtroRatingHidden').value = this.dataset.rating;
            document.getElementById('filtriForm').submit();
        });
    });
    
    // Click su chip "Senza Risposta" (toggle)
    document.querySelectorAll('.filter-chip[data-risposta-filter]').forEach(chip => {
        chip.addEventListener('click', function() {
            const currentRisposta = document.getElementById('filtroRispostaHidden').value;
            if (currentRisposta === this.dataset.rispostaFilter) {
                document.getElementById('filtroRispostaHidden').value = '';
            } else {
                document.getElementById('filtroRispostaHidden').value = this.dataset.rispostaFilter;
            }
            document.getElementById('filtriForm').submit();
        });
    });
    
    // Select sport
    document.getElementById('filtroSport').addEventListener('change', function() {
        document.getElementById('filtroSportHidden').value = this.value;
        document.getElementById('filtriForm').submit();
    });
    
    // Select campo
    document.getElementById('filtroCampo').addEventListener('change', function() {
        document.getElementById('filtroCampoHidden').value = this.value;
        document.getElementById('filtriForm').submit();
    });
    
    // Select ordina
    document.getElementById('filtroOrdina').addEventListener('change', function() {
        document.getElementById('filtroOrdinaHidden').value = this.value;
        document.getElementById('filtriForm').submit();
    });
    
    // Search con debounce
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            document.getElementById('filtroSearch').value = this.value;
            document.getElementById('filtriForm').submit();
        }, 500);
    });
});

// Helper per pulire backdrop
function cleanupBackdrops() {
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
}

// Apri modal dettaglio
function apriDettaglio(id) {
    currentRecensioneId = id;
    document.getElementById('modalRecensioneId').textContent = id;
    document.getElementById('modalContent').innerHTML = '<div class="loading-spinner"><div class="spinner"></div><p>Caricamento...</p></div>';
    
    const modal = new bootstrap.Modal(document.getElementById('modalDettaglio'));
    modal.show();
    
    fetch(`recensioni.php?ajax=1&action=get_recensione&id=${id}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) renderDettaglio(data.recensione);
            else document.getElementById('modalContent').innerHTML = `<div class="error-message">❌ ${data.message}</div>`;
        })
        .catch(() => document.getElementById('modalContent').innerHTML = '<div class="error-message">❌ Errore di connessione</div>');
}

function renderDettaglio(r) {
    const ratingClass = r.rating_generale >= 4 ? 'positive' : (r.rating_generale <= 2 ? 'negative' : 'neutral');
    const stars = '⭐'.repeat(r.rating_generale) + '<span class="star-empty">' + '☆'.repeat(5 - r.rating_generale) + '</span>';
    
    let risposteHtml = '';
    if (r.risposte && r.risposte.length > 0) {
        risposteHtml = r.risposte.map(risp => `
            <div class="risposta-item">
                <div class="risposta-header">
                    <span class="risposta-admin">👤 ${escapeHtml(risp.admin_nome)}</span>
                    <span class="risposta-data">${formatDate(risp.created_at)}</span>
                    <button class="btn btn-sm btn-outline-danger btn-elimina-risposta" onclick="eliminaRisposta(${risp.risposta_id})" title="Elimina risposta">🗑️</button>
                </div>
                <div class="risposta-testo">${escapeHtml(risp.testo)}</div>
            </div>
        `).join('');
    } else {
        risposteHtml = '<div class="no-risposte">Nessuna risposta ancora. Clicca su "Rispondi" per aggiungerne una.</div>';
    }
    
    let html = `
        <div class="dettaglio-container">
            <!-- Header con campo e rating -->
            <div class="dettaglio-header-top">
                <div class="campo-box">
                    <span class="sport-icon-big">${r.sport_icona || '🏟️'}</span>
                    <div>
                        <div class="campo-nome-big">${escapeHtml(r.campo_nome)}</div>
                        <div class="campo-location">${escapeHtml(r.campo_location || '')}</div>
                        <div class="sport-nome-small">${escapeHtml(r.sport_nome)}</div>
                    </div>
                </div>
                <div class="rating-box rating-${ratingClass}">
                    <div class="rating-big">${r.rating_generale}</div>
                    <div class="rating-stars-big">${stars}</div>
                </div>
            </div>
            
            <!-- Info utente -->
            <div class="dettaglio-section">
                <h6>👤 Autore Recensione</h6>
                <div class="utente-box">
                    <div class="utente-avatar">${getInitials(r.utente_nome)}</div>
                    <div class="utente-info">
                        <div class="utente-nome">${escapeHtml(r.utente_nome)}</div>
                        <div class="utente-email">${escapeHtml(r.utente_email)}</div>
                    </div>
                    <div class="utente-stats">
                        <div><span>Recensioni:</span> <strong>${r.utente_tot_recensioni || 0}</strong></div>
                        <div><span>Media rating:</span> <strong>${r.utente_media_rating || 'N/A'}⭐</strong></div>
                        <div><span>Prenotazioni:</span> <strong>${r.utente_prenotazioni || 0}</strong></div>
                    </div>
                </div>
            </div>
            
            <!-- Rating dettagliati -->
            <div class="dettaglio-section">
                <h6>📊 Valutazioni Dettagliate</h6>
                <div class="ratings-grid">
                    <div class="rating-item">
                        <span class="rating-label">Generale</span>
                        <div class="rating-bar">
                            <div class="rating-fill" style="width: ${r.rating_generale * 20}%"></div>
                        </div>
                        <span class="rating-value">${r.rating_generale}/5</span>
                    </div>
                    ${r.rating_condizioni ? `
                    <div class="rating-item">
                        <span class="rating-label">Condizioni</span>
                        <div class="rating-bar">
                            <div class="rating-fill" style="width: ${r.rating_condizioni * 20}%"></div>
                        </div>
                        <span class="rating-value">${r.rating_condizioni}/5</span>
                    </div>` : ''}
                    ${r.rating_pulizia ? `
                    <div class="rating-item">
                        <span class="rating-label">Pulizia</span>
                        <div class="rating-bar">
                            <div class="rating-fill" style="width: ${r.rating_pulizia * 20}%"></div>
                        </div>
                        <span class="rating-value">${r.rating_pulizia}/5</span>
                    </div>` : ''}
                    ${r.rating_illuminazione ? `
                    <div class="rating-item">
                        <span class="rating-label">Illuminazione</span>
                        <div class="rating-bar">
                            <div class="rating-fill" style="width: ${r.rating_illuminazione * 20}%"></div>
                        </div>
                        <span class="rating-value">${r.rating_illuminazione}/5</span>
                    </div>` : ''}
                </div>
            </div>
            
            <!-- Commento -->
            <div class="dettaglio-section">
                <h6>💬 Commento</h6>
                <div class="commento-box">
                    ${r.commento ? `"${escapeHtml(r.commento)}"` : '<em class="text-muted">Nessun commento inserito</em>'}
                </div>
                <div class="commento-meta">
                    📅 Pubblicata il ${formatDate(r.created_at)}
                    ${r.data_prenotazione ? ` • 🎯 Prenotazione del ${formatDataSola(r.data_prenotazione)}` : ''}
                </div>
            </div>
            
            <!-- Risposte Admin -->
            <div class="dettaglio-section risposte-section">
                <h6>💬 Risposte Admin (${r.risposte ? r.risposte.length : 0})</h6>
                <div class="risposte-list">
                    ${risposteHtml}
                </div>
            </div>
            
            <!-- Azioni -->
            <div class="dettaglio-actions">
                <button class="btn btn-primary" onclick="apriRisposta(${r.recensione_id}, '${escapeHtml(r.utente_nome)}', ${r.rating_generale})">
                    💬 Rispondi
                </button>
                <button class="btn btn-danger" onclick="apriElimina(${r.recensione_id})">
                    🗑️ Elimina Recensione
                </button>
            </div>
        </div>`;
    
    document.getElementById('modalContent').innerHTML = html;
}

function apriRisposta(id, utente, rating) {
    document.getElementById('rispostaRecensioneId').value = id;
    document.getElementById('rispostaTesto').value = '';
    document.getElementById('rispostaContext').innerHTML = `
        <strong>${escapeHtml(utente)}</strong> - Rating: ${'⭐'.repeat(rating)}
    `;
    
    bootstrap.Modal.getInstance(document.getElementById('modalDettaglio')).hide();
    
    setTimeout(() => {
        cleanupBackdrops();
        new bootstrap.Modal(document.getElementById('modalRisposta')).show();
    }, 350);
}

function submitRisposta() {
    const form = document.getElementById('formRisposta');
    if (!form.checkValidity()) { form.reportValidity(); return; }
    
    const formData = new FormData(form);
    formData.append('ajax', '1');
    formData.append('action', 'add_risposta');
    
    fetch('recensioni.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            showToast(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalRisposta')).hide();
                setTimeout(() => location.reload(), 1000);
            }
        });
}

function apriElimina(id) {
    document.getElementById('eliminaRecensioneId').value = id;
    
    bootstrap.Modal.getInstance(document.getElementById('modalDettaglio')).hide();
    
    setTimeout(() => {
        cleanupBackdrops();
        new bootstrap.Modal(document.getElementById('modalElimina')).show();
    }, 350);
}

function confermaElimina() {
    const id = document.getElementById('eliminaRecensioneId').value;
    
    const formData = new FormData();
    formData.append('ajax', '1');
    formData.append('action', 'delete_recensione');
    formData.append('id', id);
    
    fetch('recensioni.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            showToast(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalElimina')).hide();
                setTimeout(() => location.reload(), 1000);
            }
        });
}

function eliminaRisposta(id) {
    document.getElementById('eliminaRispostaId').value = id;
    new bootstrap.Modal(document.getElementById('modalEliminaRisposta')).show();
}

function confermaEliminaRisposta() {
    const id = document.getElementById('eliminaRispostaId').value;
    
    const formData = new FormData();
    formData.append('ajax', '1');
    formData.append('action', 'delete_risposta');
    formData.append('id', id);
    
    fetch('recensioni.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            showToast(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalEliminaRisposta')).hide();
                // Ricarica il dettaglio
                setTimeout(() => {
                    apriDettaglio(currentRecensioneId);
                }, 300);
            }
        });
}

// Helpers
function getInitials(nome) {
    if (!nome) return '??';
    return nome.split(' ').map(p => p[0]).join('').substring(0, 2).toUpperCase();
}

function formatDate(d) { 
    if (!d) return 'N/A'; 
    return new Date(d).toLocaleDateString('it-IT', {day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit'}); 
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
    setTimeout(() => { t.classList.remove('show'); setTimeout(() => t.remove(), 300); }, 3000); 
}
</script>