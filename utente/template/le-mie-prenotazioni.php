<?php
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

// Helper per emoji sport
function getSportEmoji($sportNome) {
    $sportNome = strtolower($sportNome);
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
$prenotazioniOggi = $templateParams['prenotazioni_oggi'] ?? [];
$prenotazioniFuture = $templateParams['prenotazioni_future'] ?? [];
$prenotazioniPassate = $templateParams['prenotazioni_passate'] ?? [];
$totaleOggi = $templateParams['totale_oggi'] ?? 0;
$totaleFuture = $templateParams['totale_future'] ?? 0;
$totalePassate = $templateParams['totale_passate'] ?? 0;
$oreAnticipo = $templateParams['ore_anticipo_cancellazione'] ?? 24;

// Tutte le prenotazioni combinate per la visualizzazione
$tuttePrenotazioni = array_merge($prenotazioniOggi, $prenotazioniFuture);
?>

<!-- Header -->
<div class="gestione-header">
    <span class="header-icon">📅</span>
    <p class="page-subtitle">Gestisci le tue prenotazioni ai campi sportivi</p>
    
    <!-- Bottone Nuova Prenotazione -->
    <a href="prenota-campo.php" class="btn-add-new">
        <span>+</span> Nuova Prenotazione
    </a>
</div>

<!-- ============================================================================
     KPI CARDS
     ============================================================================ -->
<div class="row g-3 mb-4">
    <!-- Oggi -->
    <div class="col-xl-3 col-md-6 col-6">
        <div class="kpi-card" data-color="cyan">
            <span class="kpi-icon">🎯</span>
            <div class="kpi-value"><?= $totaleOggi ?></div>
            <div class="kpi-label">Oggi</div>
            <?php if ($totaleOggi > 0): ?>
                <span class="notification-dot"></span>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Prossime -->
    <div class="col-xl-3 col-md-6 col-6">
        <div class="kpi-card" data-color="blue">
            <span class="kpi-icon">📆</span>
            <div class="kpi-value"><?= $totaleFuture ?></div>
            <div class="kpi-label">Prossime</div>
        </div>
    </div>
    
    <!-- Passate -->
    <div class="col-xl-3 col-md-6 col-6">
        <div class="kpi-card" data-color="purple">
            <span class="kpi-icon">📋</span>
            <div class="kpi-value"><?= $totalePassate ?></div>
            <div class="kpi-label">Passate</div>
        </div>
    </div>
    
    <!-- Totale -->
    <div class="col-xl-3 col-md-6 col-6">
        <div class="kpi-card" data-color="green">
            <span class="kpi-icon">📊</span>
            <div class="kpi-value"><?= $totaleOggi + $totaleFuture + $totalePassate ?></div>
            <div class="kpi-label">Totali</div>
        </div>
    </div>
</div>

<!-- ============================================================================
     INFO CANCELLAZIONE
     ============================================================================ -->
<div class="filters-card mb-4">
    <div class="filter-row">
        <span class="filter-icon">ℹ️</span>
        <span class="filter-info">Puoi cancellare una prenotazione fino a <strong><?= $oreAnticipo ?> ore</strong> prima dell'orario previsto.</span>
    </div>
</div>

<!-- ============================================================================
     SEZIONE: PRENOTAZIONI OGGI
     ============================================================================ -->
<?php if ($totaleOggi > 0): ?>
<div class="section-header">
    <h3 class="section-title">
        <span class="section-icon">🎯</span>
        Oggi
    </h3>
    <span class="section-badge badge-oggi"><?= $totaleOggi ?></span>
</div>

<div class="prenotazioni-grid mb-4">
    <?php foreach ($prenotazioniOggi as $p): 
        $statoConfig = getStatoConfig($p['stato']);
        $oraInizio = substr($p['ora_inizio'], 0, 5);
        $oraFine = substr($p['ora_fine'], 0, 5);
        
        // Calcola se è cancellabile
        $dataOraPrenotazione = $p['data_prenotazione'] . ' ' . $p['ora_inizio'];
        $differenzaOre = (strtotime($dataOraPrenotazione) - time()) / 3600;
        $cancellabile = ($p['stato'] === 'confermata' && $differenzaOre >= $oreAnticipo);
        
        // Determina se è in corso
        $oraCorrente = date('H:i:s');
        $inCorso = ($p['stato'] === 'confermata' && $oraCorrente >= $p['ora_inizio'] && $oraCorrente < $p['ora_fine']);
    ?>
    <div class="prenotazione-card <?= $inCorso ? 'in-corso' : '' ?> <?= $cancellabile && !$inCorso ? 'future clickable' : '' ?>"
         data-id="<?= $p['prenotazione_id'] ?>"
         <?php if ($cancellabile && !$inCorso): ?>
         onclick="apriModalCancella(<?= $p['prenotazione_id'] ?>, '<?= htmlspecialchars(addslashes($p['campo_nome'])) ?>', 'Oggi <?= $oraInizio ?>')"
         <?php endif; ?>>
        
        <?php if ($inCorso): ?>
        <div class="badge-in-corso">
            <span class="pulse-dot"></span> IN CORSO
        </div>
        <?php endif; ?>
        
        <!-- Header Card -->
        <div class="prenotazione-card-header">
            <div class="prenotazione-id">#<?= $p['prenotazione_id'] ?></div>
            <span class="stato-badge stato-<?= $statoConfig['class'] ?>">
                <?= $statoConfig['label'] ?>
            </span>
        </div>
        
        <!-- Info Campo -->
        <div class="prenotazione-campo">
            <div class="campo-icon"><?= getSportEmoji($p['sport_nome']) ?></div>
            <div class="campo-info">
                <div class="campo-nome"><?= htmlspecialchars($p['campo_nome']) ?></div>
                <div class="campo-sport"><?= htmlspecialchars($p['sport_nome']) ?></div>
            </div>
        </div>
        
        <!-- Data e Orario -->
        <div class="prenotazione-datetime">
            <div class="datetime-row">
                <span class="datetime-label">Orario</span>
                <span class="datetime-value"><?= $oraInizio ?> - <?= $oraFine ?></span>
            </div>
            <div class="datetime-row">
                <span class="datetime-label">Luogo</span>
                <span class="datetime-value"><?= htmlspecialchars($p['location']) ?></span>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="prenotazione-footer">
            <div class="footer-meta">
                <span class="partecipanti-count">👥 <?= $p['num_partecipanti'] ?> partecipant<?= $p['num_partecipanti'] > 1 ? 'i' : 'e' ?></span>
            </div>
            <?php if ($cancellabile && !$inCorso): ?>
            <button class="btn-cancella-card" onclick="event.stopPropagation(); apriModalCancella(<?= $p['prenotazione_id'] ?>, '<?= htmlspecialchars(addslashes($p['campo_nome'])) ?>', 'Oggi <?= $oraInizio ?>')">
                ❌ Cancella
            </button>
            <?php elseif ($inCorso): ?>
            <span class="badge-in-corso-small">🎮 In corso</span>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ============================================================================
     SEZIONE: PRENOTAZIONI FUTURE
     ============================================================================ -->
<div class="section-header">
    <h3 class="section-title">
        <span class="section-icon">📆</span>
        Prossime Prenotazioni
    </h3>
    <span class="section-badge badge-future"><?= $totaleFuture ?></span>
</div>

<div class="prenotazioni-grid mb-4">
    <?php if (empty($prenotazioniFuture) && empty($prenotazioniOggi)): ?>
    <div class="no-results">
        <div class="no-results-icon">📭</div>
        <h3>Nessuna prenotazione</h3>
        <p>Non hai prenotazioni future. Prenota subito un campo!</p>
        <a href="prenota-campo.php" class="btn-add-new" style="margin-top: 20px;">
            <span>+</span> Prenota Campo
        </a>
    </div>
    <?php else: ?>
    
    <?php foreach ($prenotazioniFuture as $p): 
        $statoConfig = getStatoConfig($p['stato']);
        $oraInizio = substr($p['ora_inizio'], 0, 5);
        $oraFine = substr($p['ora_fine'], 0, 5);
        $dataFormatted = date('d/m/Y', strtotime($p['data_prenotazione']));
        
        // Giorni della settimana in italiano
        $giorniSettimana = ['Domenica', 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato'];
        $giornoSettimana = $giorniSettimana[date('w', strtotime($p['data_prenotazione']))];
        
        // Calcola se è cancellabile
        $dataOraPrenotazione = $p['data_prenotazione'] . ' ' . $p['ora_inizio'];
        $differenzaOre = (strtotime($dataOraPrenotazione) - time()) / 3600;
        $cancellabile = ($p['stato'] === 'confermata' && $differenzaOre >= $oreAnticipo);
        
        // Calcola giorni mancanti
        $giorniMancanti = ceil((strtotime($p['data_prenotazione']) - strtotime(date('Y-m-d'))) / 86400);
    ?>
    <div class="prenotazione-card <?= $cancellabile ? 'future clickable' : '' ?>"
         data-id="<?= $p['prenotazione_id'] ?>"
         <?php if ($cancellabile): ?>
         onclick="apriModalCancella(<?= $p['prenotazione_id'] ?>, '<?= htmlspecialchars(addslashes($p['campo_nome'])) ?>', '<?= $dataFormatted ?> <?= $oraInizio ?>')"
         <?php endif; ?>>
        
        <!-- Header Card -->
        <div class="prenotazione-card-header">
            <div class="prenotazione-id">#<?= $p['prenotazione_id'] ?></div>
            <span class="stato-badge stato-<?= $statoConfig['class'] ?>">
                <?= $statoConfig['label'] ?>
            </span>
        </div>
        
        <!-- Info Campo -->
        <div class="prenotazione-campo">
            <div class="campo-icon"><?= getSportEmoji($p['sport_nome']) ?></div>
            <div class="campo-info">
                <div class="campo-nome"><?= htmlspecialchars($p['campo_nome']) ?></div>
                <div class="campo-sport"><?= htmlspecialchars($p['sport_nome']) ?></div>
            </div>
        </div>
        
        <!-- Data e Orario -->
        <div class="prenotazione-datetime">
            <div class="datetime-row">
                <span class="datetime-label">Data</span>
                <span class="datetime-value"><?= $giornoSettimana ?> <?= $dataFormatted ?></span>
            </div>
            <div class="datetime-row">
                <span class="datetime-label">Orario</span>
                <span class="datetime-value"><?= $oraInizio ?> - <?= $oraFine ?></span>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="prenotazione-footer">
            <div class="footer-meta">
                <span class="partecipanti-count">👥 <?= $p['num_partecipanti'] ?></span>
                <span class="giorni-mancanti">
                    <?php if ($giorniMancanti == 1): ?>
                        Domani
                    <?php else: ?>
                        Tra <?= $giorniMancanti ?> giorni
                    <?php endif; ?>
                </span>
            </div>
            <?php if ($cancellabile): ?>
            <button class="btn-cancella-card" onclick="event.stopPropagation(); apriModalCancella(<?= $p['prenotazione_id'] ?>, '<?= htmlspecialchars(addslashes($p['campo_nome'])) ?>', '<?= $dataFormatted ?> <?= $oraInizio ?>')">
                ❌ Cancella
            </button>
            <?php else: ?>
            <span class="badge-non-cancellabile">🔒 Non cancellabile</span>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php endif; ?>
</div>

<!-- ============================================================================
     SEZIONE: STORICO PRENOTAZIONI
     ============================================================================ -->
<?php if ($totalePassate > 0): ?>
<div class="section-header collapsible" onclick="toggleStorico()">
    <h3 class="section-title">
        <span class="section-icon">📋</span>
        Storico Prenotazioni
    </h3>
    <div class="section-right">
        <span class="section-badge badge-passate"><?= $totalePassate ?></span>
        <span class="toggle-icon" id="toggleStoricoIcon">▼</span>
    </div>
</div>

<div class="prenotazioni-grid storico-grid" id="storicoContent" style="display: none;">
    <?php foreach ($prenotazioniPassate as $p): 
        $statoConfig = getStatoConfig($p['stato']);
        $oraInizio = substr($p['ora_inizio'], 0, 5);
        $oraFine = substr($p['ora_fine'], 0, 5);
        $dataFormatted = date('d/m/Y', strtotime($p['data_prenotazione']));
    ?>
    <div class="prenotazione-card past">
        
        <!-- Header Card -->
        <div class="prenotazione-card-header">
            <div class="prenotazione-id">#<?= $p['prenotazione_id'] ?></div>
            <span class="stato-badge stato-<?= $statoConfig['class'] ?>">
                <?= $statoConfig['label'] ?>
            </span>
        </div>
        
        <!-- Info Campo -->
        <div class="prenotazione-campo">
            <div class="campo-icon"><?= getSportEmoji($p['sport_nome']) ?></div>
            <div class="campo-info">
                <div class="campo-nome"><?= htmlspecialchars($p['campo_nome']) ?></div>
                <div class="campo-sport"><?= htmlspecialchars($p['sport_nome']) ?></div>
            </div>
        </div>
        
        <!-- Data e Orario -->
        <div class="prenotazione-datetime">
            <div class="datetime-row">
                <span class="datetime-label">Data</span>
                <span class="datetime-value"><?= $dataFormatted ?></span>
            </div>
            <div class="datetime-row">
                <span class="datetime-label">Orario</span>
                <span class="datetime-value"><?= $oraInizio ?> - <?= $oraFine ?></span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ============================================================================
     MODAL: CONFERMA CANCELLAZIONE
     ============================================================================ -->
<div class="modal fade" id="modalCancella" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered" style="z-index: 1061;">
        <div class="modal-content modal-prenotazione-content" style="pointer-events: auto;">
            <div class="modal-header modal-header-danger">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon">❌</div>
                    <div>
                        <h5 class="modal-title">Cancella Prenotazione</h5>
                        <p class="modal-subtitle mb-0">Questa azione non può essere annullata</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <div class="cancella-riepilogo">
                    <div class="riepilogo-row">
                        <span class="riepilogo-icon">🏟️</span>
                        <span class="riepilogo-value" id="cancellaCampo">Campo</span>
                    </div>
                    <div class="riepilogo-row">
                        <span class="riepilogo-icon">📅</span>
                        <span class="riepilogo-value" id="cancellaOrario">Orario</span>
                    </div>
                </div>
                
                <div class="form-group mt-4">
                    <label class="form-label">Motivo cancellazione (opzionale)</label>
                    <textarea id="motivoCancellazione" class="form-control form-control-dark" rows="3" 
                              placeholder="Es: Impegno improvviso, maltempo..."></textarea>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary-dark" data-bs-dismiss="modal">
                    Annulla
                </button>
                <button type="button" class="btn btn-danger-custom" onclick="confermaCancellazione()">
                    <span id="btnCancellaText">❌ Conferma Cancellazione</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL: SUCCESSO
     ============================================================================ -->
<div class="modal fade" id="modalSuccesso" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered modal-sm" style="z-index: 1071;">
        <div class="modal-content modal-prenotazione-content" style="pointer-events: auto;">
            <div class="modal-body text-center py-5">
                <div class="success-icon">✅</div>
                <h4 class="success-title">Prenotazione Cancellata</h4>
                <p class="success-text">La tua prenotazione è stata cancellata con successo.</p>
                <button type="button" class="btn btn-add-new mt-3" onclick="location.reload()">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="prenotazioneIdCancella" value="">

<script>
// ============================================================================
// FIX MODAL - Sposta nel body per evitare z-index issues
// ============================================================================
document.addEventListener('DOMContentLoaded', function() {
    const modalsToMove = ['modalCancella', 'modalSuccesso'];
    modalsToMove.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });
});

// Toggle storico
function toggleStorico() {
    const content = document.getElementById('storicoContent');
    const icon = document.getElementById('toggleStoricoIcon');
    
    if (content.style.display === 'none') {
        content.style.display = 'grid';
        icon.textContent = '▲';
    } else {
        content.style.display = 'none';
        icon.textContent = '▼';
    }
}

// Apri modal cancellazione
function apriModalCancella(prenotazioneId, campo, orario) {
    document.getElementById('prenotazioneIdCancella').value = prenotazioneId;
    document.getElementById('cancellaCampo').textContent = campo;
    document.getElementById('cancellaOrario').textContent = orario;
    document.getElementById('motivoCancellazione').value = '';
    document.getElementById('btnCancellaText').textContent = '❌ Conferma Cancellazione';
    
    const btn = document.querySelector('.btn-danger-custom');
    btn.disabled = false;
    
    new bootstrap.Modal(document.getElementById('modalCancella')).show();
}

// Conferma cancellazione
function confermaCancellazione() {
    const prenotazioneId = document.getElementById('prenotazioneIdCancella').value;
    const motivo = document.getElementById('motivoCancellazione').value;
    
    const btn = document.querySelector('.btn-danger-custom');
    btn.disabled = true;
    document.getElementById('btnCancellaText').textContent = '⏳ Cancellazione...';
    
    fetch('le-mie-prenotazioni.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=cancella&prenotazione_id=${prenotazioneId}&motivo=${encodeURIComponent(motivo)}`
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            // Chiudi modal cancella
            bootstrap.Modal.getInstance(document.getElementById('modalCancella')).hide();
            
            // Pulisci backdrop e apri modal successo
            setTimeout(() => {
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
                
                new bootstrap.Modal(document.getElementById('modalSuccesso')).show();
            }, 350);
        } else {
            alert('Errore: ' + result.error);
            btn.disabled = false;
            document.getElementById('btnCancellaText').textContent = '❌ Conferma Cancellazione';
        }
    })
    .catch(err => {
        console.error('Errore:', err);
        alert('Errore di connessione. Riprova.');
        btn.disabled = false;
        document.getElementById('btnCancellaText').textContent = '❌ Conferma Cancellazione';
    });
}
</script>