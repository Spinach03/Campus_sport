<?php
// Helper per emoji sport
function getSportEmojiRecensioni($sportNome) {
    $sportNome = strtolower($sportNome ?? '');
    $emojiMap = [
        'calcio' => '⚽',
        'basket' => '🏀',
        'pallavolo' => '🏐',
        'tennis' => '🎾',
        'padel' => '🎾',
        'badminton' => '🏸',
        'ping pong' => '🏓'
    ];
    
    foreach ($emojiMap as $key => $emoji) {
        if (strpos($sportNome, $key) !== false) {
            return $emoji;
        }
    }
    return '🏟️';
}

// Variabili dal controller
$stats = $templateParams["stats"] ?? ['totali' => 0, 'media_rating' => 0, 'cinque_stelle' => 0, 'positive' => 0];
$daRecensire = $templateParams["da_recensire"] ?? [];
$recensioni = $templateParams["recensioni"] ?? [];
?>

<link rel="stylesheet" href="css/recensioni.css">

<!-- Header -->
<div class="gestione-header">
    <span class="header-icon">⭐</span>
    <p class="page-subtitle">Recensisci i campi dove hai giocato</p>
</div>

<!-- ============================================================================
     SEZIONE: DA RECENSIRE
     ============================================================================ -->
<?php if (!empty($daRecensire)): ?>
<div class="section-header">
    <h3 class="section-title">
        <span class="section-icon">✍️</span>
        Da Recensire
        <span class="section-badge"><?= count($daRecensire) ?></span>
    </h3>
</div>

<div class="row g-3 mb-4">
    <?php foreach ($daRecensire as $prenotazione): ?>
    <div class="col-xl-4 col-md-6">
        <div class="prenotazione-card" data-prenotazione-id="<?= $prenotazione['prenotazione_id'] ?>">
            <!-- Header Card -->
            <div class="prenotazione-header">
                <div class="prenotazione-campo">
                    <span class="campo-emoji"><?= getSportEmojiRecensioni($prenotazione['sport_nome']) ?></span>
                    <div>
                        <h4 class="campo-nome"><?= htmlspecialchars($prenotazione['campo_nome']) ?></h4>
                        <span class="campo-sport"><?= htmlspecialchars($prenotazione['sport_nome']) ?> • <?= htmlspecialchars($prenotazione['tipo_campo']) ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Body Card -->
            <div class="prenotazione-body">
                <div class="prenotazione-info-grid">
                    <div class="info-item">
                        <span class="info-icon">📅</span>
                        <span class="info-text"><?= date('d/m/Y', strtotime($prenotazione['data_prenotazione'])) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-icon">🕐</span>
                        <span class="info-text"><?= substr($prenotazione['ora_inizio'], 0, 5) ?> - <?= substr($prenotazione['ora_fine'], 0, 5) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-icon">📍</span>
                        <span class="info-text"><?= htmlspecialchars($prenotazione['location']) ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Footer Card -->
            <div class="prenotazione-footer">
                <button class="btn-action-primary w-100" onclick="apriModalNuovaRecensione(<?= $prenotazione['prenotazione_id'] ?>, '<?= htmlspecialchars($prenotazione['campo_nome'], ENT_QUOTES) ?>', '<?= htmlspecialchars($prenotazione['sport_nome'], ENT_QUOTES) ?>')">
                    ✍️ Scrivi Recensione
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ============================================================================
     SEZIONE: LE MIE RECENSIONI
     ============================================================================ -->
<div class="section-header">
    <h3 class="section-title">
        <span class="section-icon">⭐</span>
        Le Mie Recensioni
        <span class="section-badge"><?= count($recensioni) ?></span>
    </h3>
</div>

<?php if (empty($recensioni)): ?>
<div class="empty-state-card">
    <div class="empty-icon">📝</div>
    <h4>Nessuna recensione</h4>
    <p>Non hai ancora scritto recensioni. Dopo aver completato una prenotazione, potrai recensire il campo!</p>
</div>
<?php else: ?>
<div class="row g-3">
    <?php foreach ($recensioni as $recensione): ?>
    <div class="col-12">
        <div class="prenotazione-card recensione-card-item" data-recensione-id="<?= $recensione['recensione_id'] ?>">
            <!-- Header Card -->
            <div class="prenotazione-header">
                <div class="prenotazione-campo">
                    <span class="campo-emoji"><?= getSportEmojiRecensioni($recensione['sport_nome']) ?></span>
                    <div>
                        <h4 class="campo-nome"><?= htmlspecialchars($recensione['campo_nome']) ?></h4>
                        <span class="campo-sport"><?= htmlspecialchars($recensione['sport_nome']) ?> • <?= htmlspecialchars($recensione['tipo_campo']) ?></span>
                    </div>
                </div>
                
                <div class="recensione-rating-display">
                    <div class="stars-display">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="star <?= $i <= $recensione['rating_generale'] ? 'filled' : '' ?>">★</span>
                        <?php endfor; ?>
                    </div>
                    <span class="rating-number"><?= $recensione['rating_generale'] ?>/5</span>
                </div>
            </div>
            
            <!-- Body Card -->
            <div class="prenotazione-body">
                <?php if ($recensione['commento']): ?>
                <p class="recensione-commento"><?= nl2br(htmlspecialchars($recensione['commento'])) ?></p>
                <?php endif; ?>
                
                <?php if ($recensione['rating_condizioni'] || $recensione['rating_pulizia'] || $recensione['rating_illuminazione']): ?>
                <div class="recensione-ratings-detail">
                    <?php if ($recensione['rating_condizioni']): ?>
                    <span class="rating-pill">
                        <span class="pill-label">Condizioni</span>
                        <span class="pill-stars"><?= str_repeat('★', $recensione['rating_condizioni']) ?><?= str_repeat('☆', 5 - $recensione['rating_condizioni']) ?></span>
                    </span>
                    <?php endif; ?>
                    
                    <?php if ($recensione['rating_pulizia']): ?>
                    <span class="rating-pill">
                        <span class="pill-label">Pulizia</span>
                        <span class="pill-stars"><?= str_repeat('★', $recensione['rating_pulizia']) ?><?= str_repeat('☆', 5 - $recensione['rating_pulizia']) ?></span>
                    </span>
                    <?php endif; ?>
                    
                    <?php if ($recensione['rating_illuminazione']): ?>
                    <span class="rating-pill">
                        <span class="pill-label">Illuminazione</span>
                        <span class="pill-stars"><?= str_repeat('★', $recensione['rating_illuminazione']) ?><?= str_repeat('☆', 5 - $recensione['rating_illuminazione']) ?></span>
                    </span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <!-- Risposta Admin -->
                <?php if ($recensione['num_risposte'] > 0): ?>
                <div class="risposta-admin-box">
                    <div class="risposta-header">
                        <span class="risposta-icon">💬</span>
                        <span class="risposta-label">Risposta dell'amministrazione</span>
                    </div>
                    <button class="btn-link" onclick="vediRisposte(<?= $recensione['recensione_id'] ?>)">
                        Vedi risposta →
                    </button>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Footer Card -->
            <div class="prenotazione-footer">
                <div class="footer-meta">
                    <span class="meta-item">📅 <?= date('d/m/Y', strtotime($recensione['created_at'])) ?></span>
                    <span class="meta-item">🕐 Prenotazione del <?= date('d/m/Y', strtotime($recensione['data_prenotazione'])) ?></span>
                    <?php if (!$recensione['modificabile']): ?>
                    <span class="meta-item meta-warning">🔒 Non modificabile (oltre 15 giorni)</span>
                    <?php endif; ?>
                </div>
                
                <div class="footer-actions">
                    <?php if ($recensione['modificabile']): ?>
                    <button class="btn-icon-action btn-edit" onclick="apriModalModifica(<?= $recensione['recensione_id'] ?>)" title="Modifica">
                        ✏️
                    </button>
                    <?php else: ?>
                    <button class="btn-icon-action btn-edit btn-disabled" title="Non modificabile (oltre 15 giorni)" disabled>
                        ✏️
                    </button>
                    <?php endif; ?>
                    <button class="btn-icon-action btn-delete" onclick="confermaElimina(<?= $recensione['recensione_id'] ?>)" title="Elimina">
                        🗑️
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ============================================================================
     MODAL NUOVA RECENSIONE
     ============================================================================ -->
<div class="modal fade" id="modalNuovaRecensione" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="z-index: 1061;">
        <div class="modal-content modal-content-dark" style="pointer-events: auto;">
            <div class="modal-header modal-header-gradient">
                <div class="modal-header-content">
                    <span class="modal-header-icon">✍️</span>
                    <div>
                        <h5 class="modal-title">Scrivi Recensione</h5>
                        <p class="modal-subtitle" id="modalNuovaCampoNome">Nome Campo</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <input type="hidden" id="nuovaPrenotazioneId">
                
                <!-- Rating Generale -->
                <div class="rating-section-main">
                    <label class="rating-label">Valutazione Generale *</label>
                    <div class="star-rating-interactive" id="ratingGenerale" data-rating="0">
                        <span class="star-btn" data-value="1">★</span>
                        <span class="star-btn" data-value="2">★</span>
                        <span class="star-btn" data-value="3">★</span>
                        <span class="star-btn" data-value="4">★</span>
                        <span class="star-btn" data-value="5">★</span>
                    </div>
                    <span class="rating-text" id="ratingGeneraleText">Seleziona una valutazione</span>
                </div>
                
                <!-- Rating Dettagliati -->
                <div class="rating-details-row">
                    <div class="rating-detail-item">
                        <label>Condizioni Campo</label>
                        <div class="star-rating-small" id="ratingCondizioni" data-rating="0">
                            <span class="star-btn-sm" data-value="1">★</span>
                            <span class="star-btn-sm" data-value="2">★</span>
                            <span class="star-btn-sm" data-value="3">★</span>
                            <span class="star-btn-sm" data-value="4">★</span>
                            <span class="star-btn-sm" data-value="5">★</span>
                        </div>
                    </div>
                    
                    <div class="rating-detail-item">
                        <label>Pulizia</label>
                        <div class="star-rating-small" id="ratingPulizia" data-rating="0">
                            <span class="star-btn-sm" data-value="1">★</span>
                            <span class="star-btn-sm" data-value="2">★</span>
                            <span class="star-btn-sm" data-value="3">★</span>
                            <span class="star-btn-sm" data-value="4">★</span>
                            <span class="star-btn-sm" data-value="5">★</span>
                        </div>
                    </div>
                    
                    <div class="rating-detail-item">
                        <label>Illuminazione</label>
                        <div class="star-rating-small" id="ratingIlluminazione" data-rating="0">
                            <span class="star-btn-sm" data-value="1">★</span>
                            <span class="star-btn-sm" data-value="2">★</span>
                            <span class="star-btn-sm" data-value="3">★</span>
                            <span class="star-btn-sm" data-value="4">★</span>
                            <span class="star-btn-sm" data-value="5">★</span>
                        </div>
                    </div>
                </div>
                
                <!-- Commento -->
                <div class="form-group">
                    <label for="commentoNuova">Il tuo commento (opzionale)</label>
                    <textarea id="commentoNuova" class="form-control-dark" rows="4" placeholder="Racconta la tua esperienza..." maxlength="500"></textarea>
                    <span class="char-counter"><span id="charCountNuova">0</span>/500</span>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary-dark" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn-primary-gradient" onclick="inviaRecensione()">
                    ⭐ Pubblica Recensione
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL MODIFICA RECENSIONE
     ============================================================================ -->
<div class="modal fade" id="modalModificaRecensione" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="z-index: 1061;">
        <div class="modal-content modal-content-dark" style="pointer-events: auto;">
            <div class="modal-header modal-header-gradient">
                <div class="modal-header-content">
                    <span class="modal-header-icon">✏️</span>
                    <div>
                        <h5 class="modal-title">Modifica Recensione</h5>
                        <p class="modal-subtitle" id="modalModificaCampoNome">Nome Campo</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <input type="hidden" id="modificaRecensioneId">
                
                <!-- Rating Generale -->
                <div class="rating-section-main">
                    <label class="rating-label">Valutazione Generale *</label>
                    <div class="star-rating-interactive" id="ratingGeneraleMod" data-rating="0">
                        <span class="star-btn" data-value="1">★</span>
                        <span class="star-btn" data-value="2">★</span>
                        <span class="star-btn" data-value="3">★</span>
                        <span class="star-btn" data-value="4">★</span>
                        <span class="star-btn" data-value="5">★</span>
                    </div>
                    <span class="rating-text" id="ratingGeneraleModText">Seleziona una valutazione</span>
                </div>
                
                <!-- Rating Dettagliati -->
                <div class="rating-details-row">
                    <div class="rating-detail-item">
                        <label>Condizioni Campo</label>
                        <div class="star-rating-small" id="ratingCondizioniMod" data-rating="0">
                            <span class="star-btn-sm" data-value="1">★</span>
                            <span class="star-btn-sm" data-value="2">★</span>
                            <span class="star-btn-sm" data-value="3">★</span>
                            <span class="star-btn-sm" data-value="4">★</span>
                            <span class="star-btn-sm" data-value="5">★</span>
                        </div>
                    </div>
                    
                    <div class="rating-detail-item">
                        <label>Pulizia</label>
                        <div class="star-rating-small" id="ratingPuliziaMod" data-rating="0">
                            <span class="star-btn-sm" data-value="1">★</span>
                            <span class="star-btn-sm" data-value="2">★</span>
                            <span class="star-btn-sm" data-value="3">★</span>
                            <span class="star-btn-sm" data-value="4">★</span>
                            <span class="star-btn-sm" data-value="5">★</span>
                        </div>
                    </div>
                    
                    <div class="rating-detail-item">
                        <label>Illuminazione</label>
                        <div class="star-rating-small" id="ratingIlluminazioneMod" data-rating="0">
                            <span class="star-btn-sm" data-value="1">★</span>
                            <span class="star-btn-sm" data-value="2">★</span>
                            <span class="star-btn-sm" data-value="3">★</span>
                            <span class="star-btn-sm" data-value="4">★</span>
                            <span class="star-btn-sm" data-value="5">★</span>
                        </div>
                    </div>
                </div>
                
                <!-- Commento -->
                <div class="form-group">
                    <label for="commentoModifica">Il tuo commento (opzionale)</label>
                    <textarea id="commentoModifica" class="form-control-dark" rows="4" placeholder="Racconta la tua esperienza..." maxlength="500"></textarea>
                    <span class="char-counter"><span id="charCountModifica">0</span>/500</span>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary-dark" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn-primary-gradient" onclick="salvaModifica()">
                    💾 Salva Modifiche
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL CONFERMA ELIMINA
     ============================================================================ -->
<div class="modal fade" id="modalConfermaElimina" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-sm" style="z-index: 1061;">
        <div class="modal-content modal-content-dark" style="pointer-events: auto;">
            <div class="modal-header modal-header-danger">
                <div class="modal-header-content">
                    <span class="modal-header-icon">🗑️</span>
                    <h5 class="modal-title">Elimina Recensione</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <input type="hidden" id="eliminaRecensioneId">
                <p>Sei sicuro di voler eliminare questa recensione? L'azione non può essere annullata.</p>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary-dark" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn-danger" onclick="eseguiElimina()">
                    🗑️ Elimina
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL VEDI RISPOSTE
     ============================================================================ -->
<div class="modal fade" id="modalRisposte" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered" style="z-index: 1061;">
        <div class="modal-content modal-content-dark" style="pointer-events: auto;">
            <div class="modal-header modal-header-gradient">
                <div class="modal-header-content">
                    <span class="modal-header-icon">💬</span>
                    <h5 class="modal-title">Risposta Amministrazione</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body" id="risposteContainer">
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
const ratingTexts = { 1: 'Pessimo', 2: 'Scarso', 3: 'Nella media', 4: 'Buono', 5: 'Eccellente' };

document.addEventListener('DOMContentLoaded', function() {
    // FIX MODAL - Sposta i modal nel body per evitare problemi di z-index
    const modalsToMove = ['modalNuovaRecensione', 'modalModificaRecensione', 'modalConfermaElimina', 'modalRisposte'];
    modalsToMove.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });
    
    initStarRating('ratingGenerale', 'ratingGeneraleText');
    initStarRating('ratingCondizioni');
    initStarRating('ratingPulizia');
    initStarRating('ratingIlluminazione');
    initStarRating('ratingGeneraleMod', 'ratingGeneraleModText');
    initStarRating('ratingCondizioniMod');
    initStarRating('ratingPuliziaMod');
    initStarRating('ratingIlluminazioneMod');
    
    document.getElementById('commentoNuova')?.addEventListener('input', function() {
        document.getElementById('charCountNuova').textContent = this.value.length;
    });
    document.getElementById('commentoModifica')?.addEventListener('input', function() {
        document.getElementById('charCountModifica').textContent = this.value.length;
    });
});

function initStarRating(containerId, textId = null) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    const stars = container.querySelectorAll('.star-btn, .star-btn-sm');
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const value = parseInt(this.dataset.value);
            container.dataset.rating = value;
            stars.forEach((s, idx) => s.classList.toggle('active', idx < value));
            if (textId) document.getElementById(textId).textContent = ratingTexts[value] || '';
        });
        
        star.addEventListener('mouseenter', function() {
            const value = parseInt(this.dataset.value);
            stars.forEach((s, idx) => s.classList.toggle('hover', idx < value));
        });
        
        star.addEventListener('mouseleave', () => stars.forEach(s => s.classList.remove('hover')));
    });
}

function setRating(containerId, value, textId = null) {
    const container = document.getElementById(containerId);
    if (!container) return;
    container.dataset.rating = value;
    container.querySelectorAll('.star-btn, .star-btn-sm').forEach((s, idx) => s.classList.toggle('active', idx < value));
    if (textId && ratingTexts[value]) document.getElementById(textId).textContent = ratingTexts[value];
}

function apriModalNuovaRecensione(prenotazioneId, campoNome, sportNome) {
    document.getElementById('nuovaPrenotazioneId').value = prenotazioneId;
    document.getElementById('modalNuovaCampoNome').textContent = campoNome + ' • ' + sportNome;
    setRating('ratingGenerale', 0, 'ratingGeneraleText');
    setRating('ratingCondizioni', 0);
    setRating('ratingPulizia', 0);
    setRating('ratingIlluminazione', 0);
    document.getElementById('commentoNuova').value = '';
    document.getElementById('charCountNuova').textContent = '0';
    document.getElementById('ratingGeneraleText').textContent = 'Seleziona una valutazione';
    new bootstrap.Modal(document.getElementById('modalNuovaRecensione')).show();
}

function inviaRecensione() {
    const prenotazioneId = document.getElementById('nuovaPrenotazioneId').value;
    const ratingGenerale = document.getElementById('ratingGenerale').dataset.rating;
    
    if (ratingGenerale < 1) { mostraToast('Seleziona una valutazione generale', 'warning'); return; }
    
    const formData = new FormData();
    formData.append('action', 'crea_recensione');
    formData.append('prenotazione_id', prenotazioneId);
    formData.append('rating_generale', ratingGenerale);
    formData.append('rating_condizioni', document.getElementById('ratingCondizioni').dataset.rating);
    formData.append('rating_pulizia', document.getElementById('ratingPulizia').dataset.rating);
    formData.append('rating_illuminazione', document.getElementById('ratingIlluminazione').dataset.rating);
    formData.append('commento', document.getElementById('commentoNuova').value);
    
    fetch('recensioni.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalNuovaRecensione')).hide();
                mostraToast('Recensione pubblicata!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                mostraToast(data.error || 'Errore', 'error');
            }
        })
        .catch(() => mostraToast('Errore di connessione', 'error'));
}

function apriModalModifica(recensioneId) {
    const formData = new FormData();
    formData.append('action', 'get_recensione');
    formData.append('recensione_id', recensioneId);
    
    fetch('recensioni.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const r = data.recensione;
                document.getElementById('modificaRecensioneId').value = r.recensione_id;
                document.getElementById('modalModificaCampoNome').textContent = r.campo_nome + ' • ' + r.sport_nome;
                setRating('ratingGeneraleMod', r.rating_generale, 'ratingGeneraleModText');
                setRating('ratingCondizioniMod', r.rating_condizioni || 0);
                setRating('ratingPuliziaMod', r.rating_pulizia || 0);
                setRating('ratingIlluminazioneMod', r.rating_illuminazione || 0);
                document.getElementById('commentoModifica').value = r.commento || '';
                document.getElementById('charCountModifica').textContent = (r.commento || '').length;
                new bootstrap.Modal(document.getElementById('modalModificaRecensione')).show();
            } else {
                mostraToast(data.error || 'Errore', 'error');
            }
        });
}

function salvaModifica() {
    const recensioneId = document.getElementById('modificaRecensioneId').value;
    const ratingGenerale = document.getElementById('ratingGeneraleMod').dataset.rating;
    
    if (ratingGenerale < 1) { mostraToast('Seleziona una valutazione generale', 'warning'); return; }
    
    const formData = new FormData();
    formData.append('action', 'modifica_recensione');
    formData.append('recensione_id', recensioneId);
    formData.append('rating_generale', ratingGenerale);
    formData.append('rating_condizioni', document.getElementById('ratingCondizioniMod').dataset.rating);
    formData.append('rating_pulizia', document.getElementById('ratingPuliziaMod').dataset.rating);
    formData.append('rating_illuminazione', document.getElementById('ratingIlluminazioneMod').dataset.rating);
    formData.append('commento', document.getElementById('commentoModifica').value);
    
    fetch('recensioni.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalModificaRecensione')).hide();
                mostraToast('Modifiche salvate!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                mostraToast(data.error || 'Errore', 'error');
            }
        });
}

function confermaElimina(recensioneId) {
    document.getElementById('eliminaRecensioneId').value = recensioneId;
    new bootstrap.Modal(document.getElementById('modalConfermaElimina')).show();
}

function eseguiElimina() {
    const recensioneId = document.getElementById('eliminaRecensioneId').value;
    const formData = new FormData();
    formData.append('action', 'elimina_recensione');
    formData.append('recensione_id', recensioneId);
    
    fetch('recensioni.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalConfermaElimina')).hide();
                mostraToast('Recensione eliminata', 'success');
                const card = document.querySelector(`[data-recensione-id="${recensioneId}"]`);
                if (card) {
                    card.style.animation = 'slideOut 0.3s ease forwards';
                    setTimeout(() => card.closest('.col-12')?.remove(), 300);
                }
            } else {
                mostraToast(data.error || 'Errore', 'error');
            }
        });
}

function vediRisposte(recensioneId) {
    const container = document.getElementById('risposteContainer');
    container.innerHTML = '<div class="text-center text-muted">Caricamento...</div>';
    new bootstrap.Modal(document.getElementById('modalRisposte')).show();
    
    const formData = new FormData();
    formData.append('action', 'get_recensione');
    formData.append('recensione_id', recensioneId);
    
    fetch('recensioni.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.recensione.risposte && data.recensione.risposte.length > 0) {
                let html = '';
                data.recensione.risposte.forEach(r => {
                    html += `<div class="risposta-item">
                        <div class="risposta-meta">
                            <span class="risposta-admin-name">👤 ${r.admin_nome} ${r.admin_cognome}</span>
                            <span class="risposta-data">📅 ${new Date(r.created_at).toLocaleDateString('it-IT')}</span>
                        </div>
                        <p class="risposta-testo">${r.testo}</p>
                    </div>`;
                });
                container.innerHTML = html;
            } else {
                container.innerHTML = '<p class="text-center text-muted">Nessuna risposta</p>';
            }
        });
}

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