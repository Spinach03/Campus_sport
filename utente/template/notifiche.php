<?php
// Helper per icona tipo notifica
function getTipoNotificaConfig($tipo) {
    $config = [
        'prenotazione_creata' => ['icon' => '✅', 'color' => 'green', 'label' => 'Prenotazione'],
        'prenotazione_cancellata' => ['icon' => '❌', 'color' => 'red', 'label' => 'Cancellazione'],
        'prenotazione_admin' => ['icon' => '📅', 'color' => 'blue', 'label' => 'Prenotazione Admin'],
        'prenotazione_cancellata_admin' => ['icon' => '⚠️', 'color' => 'orange', 'label' => 'Cancellazione Admin'],
        'segnalazione_ricevuta' => ['icon' => '🚨', 'color' => 'red', 'label' => 'Segnalazione'],
        'segnalazione_esito' => ['icon' => '📋', 'color' => 'purple', 'label' => 'Esito Segnalazione'],
        'comunicazione' => ['icon' => '📢', 'color' => 'blue', 'label' => 'Comunicazione'],
        'penalty' => ['icon' => '⚠️', 'color' => 'orange', 'label' => 'Penalty'],
        'penalty_rimossa' => ['icon' => '✅', 'color' => 'green', 'label' => 'Penalty Rimossa'],
        'penalty_reset' => ['icon' => '🎉', 'color' => 'green', 'label' => 'Penalty Azzerata'],
        'sospensione' => ['icon' => '🔒', 'color' => 'red', 'label' => 'Sospensione'],
        'riattivazione' => ['icon' => '🔓', 'color' => 'green', 'label' => 'Riattivazione'],
        'ban' => ['icon' => '⛔', 'color' => 'red', 'label' => 'Ban'],
        'unban' => ['icon' => '🎉', 'color' => 'green', 'label' => 'Ban Rimosso'],
        'campo_chiuso' => ['icon' => '🚫', 'color' => 'orange', 'label' => 'Campo Chiuso'],
        'manutenzione' => ['icon' => '🔧', 'color' => 'orange', 'label' => 'Manutenzione'],
        'risposta_recensione' => ['icon' => '💬', 'color' => 'purple', 'label' => 'Risposta'],
        'promemoria' => ['icon' => '⏰', 'color' => 'cyan', 'label' => 'Promemoria'],
    ];
    return $config[$tipo] ?? ['icon' => '🔔', 'color' => 'blue', 'label' => 'Notifica'];
}

// Helper per tempo relativo
function getTempoRelativo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'Adesso';
    } elseif ($diff < 3600) {
        $minuti = floor($diff / 60);
        return $minuti . ' minut' . ($minuti == 1 ? 'o' : 'i') . ' fa';
    } elseif ($diff < 86400) {
        $ore = floor($diff / 3600);
        return $ore . ' or' . ($ore == 1 ? 'a' : 'e') . ' fa';
    } elseif ($diff < 172800) {
        return 'Ieri';
    } elseif ($diff < 604800) {
        $giorni = floor($diff / 86400);
        return $giorni . ' giorn' . ($giorni == 1 ? 'o' : 'i') . ' fa';
    } else {
        return date('d/m/Y', $timestamp);
    }
}

// Variabili dal controller
$notificheOggi = $templateParams['notifiche_oggi'] ?? [];
$notificheIeri = $templateParams['notifiche_ieri'] ?? [];
$notificheSettimana = $templateParams['notifiche_settimana'] ?? [];
$notifichePrecedenti = $templateParams['notifiche_precedenti'] ?? [];
$totaleNotifiche = $templateParams['totale_notifiche'] ?? 0;
$nonLette = $templateParams['non_lette'] ?? 0;
$filtro = $templateParams['filtro'] ?? 'tutte';
?>

<!-- Header -->
<div class="gestione-header">
    <span class="header-icon">🔔</span>
    <p class="page-subtitle">Le tue notifiche e avvisi</p>
    
    <!-- Azioni Rapide -->
    <div class="header-actions">
        <?php if ($nonLette > 0): ?>
        <button class="btn-action-secondary" onclick="segnaTutteLette()">
            ✓ Segna tutte lette
        </button>
        <?php endif; ?>
        <button class="btn-action-secondary" onclick="eliminaTutteLette()">
            🗑️ Elimina lette
        </button>
    </div>
</div>

<!-- ============================================================================
     KPI CARDS
     ============================================================================ -->
<div class="row g-3 mb-4">
    <!-- Non Lette -->
    <div class="col-xl-3 col-md-6 col-6">
        <div class="kpi-card <?= $filtro === 'non_lette' ? 'active' : '' ?>" data-color="red" onclick="filtraNotifiche('non_lette')">
            <span class="kpi-icon">📩</span>
            <div class="kpi-value"><?= $nonLette ?></div>
            <div class="kpi-label">Non Lette</div>
            <?php if ($nonLette > 0): ?>
                <span class="notification-dot"></span>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Totali -->
    <div class="col-xl-3 col-md-6 col-6">
        <div class="kpi-card <?= $filtro === 'tutte' ? 'active' : '' ?>" data-color="blue" onclick="filtraNotifiche('tutte')">
            <span class="kpi-icon">🔔</span>
            <div class="kpi-value"><?= $totaleNotifiche ?></div>
            <div class="kpi-label">Totali</div>
        </div>
    </div>
    
    <!-- Oggi -->
    <div class="col-xl-3 col-md-6 col-6">
        <div class="kpi-card" data-color="green">
            <span class="kpi-icon">📅</span>
            <div class="kpi-value"><?= count($notificheOggi) ?></div>
            <div class="kpi-label">Oggi</div>
        </div>
    </div>
    
    <!-- Questa Settimana -->
    <div class="col-xl-3 col-md-6 col-6">
        <div class="kpi-card" data-color="purple">
            <span class="kpi-icon">📆</span>
            <div class="kpi-value"><?= count($notificheOggi) + count($notificheIeri) + count($notificheSettimana) ?></div>
            <div class="kpi-label">Questa Settimana</div>
        </div>
    </div>
</div>

<!-- ============================================================================
     FILTRI CARD
     ============================================================================ -->
<div class="filters-card mb-4">
    <div class="filter-row">
        <span class="filter-label">Visualizza:</span>
        <div class="filter-chips">
            <button type="button" class="filter-chip <?= $filtro === 'tutte' ? 'active' : '' ?>" onclick="filtraNotifiche('tutte')">
                🔔 Tutte
            </button>
            <button type="button" class="filter-chip <?= $filtro === 'non_lette' ? 'active' : '' ?>" onclick="filtraNotifiche('non_lette')">
                📩 Non Lette
                <?php if ($nonLette > 0): ?>
                <span class="chip-badge"><?= $nonLette ?></span>
                <?php endif; ?>
            </button>
        </div>
    </div>
</div>

<!-- ============================================================================
     SEZIONE: NOTIFICHE OGGI
     ============================================================================ -->
<?php if (!empty($notificheOggi)): ?>
<div class="section-header">
    <h2 class="h5 section-title">
        <span class="section-icon">📅</span>
        Oggi
    </h2>
    <span class="section-badge badge-oggi"><?= count($notificheOggi) ?></span>
</div>

<div class="notifiche-list mb-4">
    <?php foreach ($notificheOggi as $n): 
        $config = getTipoNotificaConfig($n['tipo']);
    ?>
    <div class="notifica-card <?= $n['letta'] ? 'letta' : 'non-letta' ?>" 
         data-id="<?= $n['notifica_id'] ?>"
         onclick="apriNotifica(<?= $n['notifica_id'] ?>, '<?= htmlspecialchars($n['link'] ?? '') ?>', <?= $n['letta'] ? 'true' : 'false' ?>)">
        
        <div class="notifica-icon" data-color="<?= $config['color'] ?>">
            <?= $config['icon'] ?>
        </div>
        
        <div class="notifica-content">
            <div class="notifica-header">
                <span class="notifica-titolo"><?= htmlspecialchars($n['titolo']) ?></span>
                <span class="notifica-tempo"><?= getTempoRelativo($n['created_at']) ?></span>
            </div>
            <p class="notifica-messaggio"><?= htmlspecialchars($n['messaggio']) ?></p>
            <div class="notifica-meta">
                <span class="notifica-tipo" data-color="<?= $config['color'] ?>"><?= $config['label'] ?></span>
                <?php if (!$n['letta']): ?>
                <span class="badge-non-letta">Nuova</span>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="notifica-actions">
            <?php if (!$n['letta']): ?>
            <button class="btn-notifica-action" onclick="event.stopPropagation(); segnaLetta(<?= $n['notifica_id'] ?>)" title="Segna come letta">
                ✓
            </button>
            <?php endif; ?>
            <button class="btn-notifica-action btn-delete" onclick="event.stopPropagation(); eliminaNotifica(<?= $n['notifica_id'] ?>)" title="Elimina">
                🗑️
            </button>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ============================================================================
     SEZIONE: NOTIFICHE IERI
     ============================================================================ -->
<?php if (!empty($notificheIeri)): ?>
<div class="section-header">
    <h2 class="h5 section-title">
        <span class="section-icon">📆</span>
        Ieri
    </h2>
    <span class="section-badge badge-ieri"><?= count($notificheIeri) ?></span>
</div>

<div class="notifiche-list mb-4">
    <?php foreach ($notificheIeri as $n): 
        $config = getTipoNotificaConfig($n['tipo']);
    ?>
    <div class="notifica-card <?= $n['letta'] ? 'letta' : 'non-letta' ?>" 
         data-id="<?= $n['notifica_id'] ?>"
         onclick="apriNotifica(<?= $n['notifica_id'] ?>, '<?= htmlspecialchars($n['link'] ?? '') ?>', <?= $n['letta'] ? 'true' : 'false' ?>)">
        
        <div class="notifica-icon" data-color="<?= $config['color'] ?>">
            <?= $config['icon'] ?>
        </div>
        
        <div class="notifica-content">
            <div class="notifica-header">
                <span class="notifica-titolo"><?= htmlspecialchars($n['titolo']) ?></span>
                <span class="notifica-tempo"><?= getTempoRelativo($n['created_at']) ?></span>
            </div>
            <p class="notifica-messaggio"><?= htmlspecialchars($n['messaggio']) ?></p>
            <div class="notifica-meta">
                <span class="notifica-tipo" data-color="<?= $config['color'] ?>"><?= $config['label'] ?></span>
                <?php if (!$n['letta']): ?>
                <span class="badge-non-letta">Nuova</span>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="notifica-actions">
            <?php if (!$n['letta']): ?>
            <button class="btn-notifica-action" onclick="event.stopPropagation(); segnaLetta(<?= $n['notifica_id'] ?>)" title="Segna come letta">
                ✓
            </button>
            <?php endif; ?>
            <button class="btn-notifica-action btn-delete" onclick="event.stopPropagation(); eliminaNotifica(<?= $n['notifica_id'] ?>)" title="Elimina">
                🗑️
            </button>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ============================================================================
     SEZIONE: QUESTA SETTIMANA
     ============================================================================ -->
<?php if (!empty($notificheSettimana)): ?>
<div class="section-header">
    <h2 class="h5 section-title">
        <span class="section-icon">📋</span>
        Questa Settimana
    </h2>
    <span class="section-badge badge-settimana"><?= count($notificheSettimana) ?></span>
</div>

<div class="notifiche-list mb-4">
    <?php foreach ($notificheSettimana as $n): 
        $config = getTipoNotificaConfig($n['tipo']);
    ?>
    <div class="notifica-card <?= $n['letta'] ? 'letta' : 'non-letta' ?>" 
         data-id="<?= $n['notifica_id'] ?>"
         onclick="apriNotifica(<?= $n['notifica_id'] ?>, '<?= htmlspecialchars($n['link'] ?? '') ?>', <?= $n['letta'] ? 'true' : 'false' ?>)">
        
        <div class="notifica-icon" data-color="<?= $config['color'] ?>">
            <?= $config['icon'] ?>
        </div>
        
        <div class="notifica-content">
            <div class="notifica-header">
                <span class="notifica-titolo"><?= htmlspecialchars($n['titolo']) ?></span>
                <span class="notifica-tempo"><?= getTempoRelativo($n['created_at']) ?></span>
            </div>
            <p class="notifica-messaggio"><?= htmlspecialchars($n['messaggio']) ?></p>
            <div class="notifica-meta">
                <span class="notifica-tipo" data-color="<?= $config['color'] ?>"><?= $config['label'] ?></span>
                <?php if (!$n['letta']): ?>
                <span class="badge-non-letta">Nuova</span>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="notifica-actions">
            <?php if (!$n['letta']): ?>
            <button class="btn-notifica-action" onclick="event.stopPropagation(); segnaLetta(<?= $n['notifica_id'] ?>)" title="Segna come letta">
                ✓
            </button>
            <?php endif; ?>
            <button class="btn-notifica-action btn-delete" onclick="event.stopPropagation(); eliminaNotifica(<?= $n['notifica_id'] ?>)" title="Elimina">
                🗑️
            </button>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ============================================================================
     SEZIONE: PRECEDENTI
     ============================================================================ -->
<?php if (!empty($notifichePrecedenti)): ?>
<div class="section-header collapsible" onclick="togglePrecedenti()">
    <h2 class="h5 section-title">
        <span class="section-icon">📁</span>
        Precedenti
    </h2>
    <div class="section-right">
        <span class="section-badge badge-precedenti"><?= count($notifichePrecedenti) ?></span>
        <span class="toggle-icon" id="togglePrecedentiIcon">▼</span>
    </div>
</div>

<div class="notifiche-list precedenti-list" id="precedentiContent" style="display: none;">
    <?php foreach ($notifichePrecedenti as $n): 
        $config = getTipoNotificaConfig($n['tipo']);
    ?>
    <div class="notifica-card <?= $n['letta'] ? 'letta' : 'non-letta' ?>" 
         data-id="<?= $n['notifica_id'] ?>"
         onclick="apriNotifica(<?= $n['notifica_id'] ?>, '<?= htmlspecialchars($n['link'] ?? '') ?>', <?= $n['letta'] ? 'true' : 'false' ?>)">
        
        <div class="notifica-icon" data-color="<?= $config['color'] ?>">
            <?= $config['icon'] ?>
        </div>
        
        <div class="notifica-content">
            <div class="notifica-header">
                <span class="notifica-titolo"><?= htmlspecialchars($n['titolo']) ?></span>
                <span class="notifica-tempo"><?= getTempoRelativo($n['created_at']) ?></span>
            </div>
            <p class="notifica-messaggio"><?= htmlspecialchars($n['messaggio']) ?></p>
            <div class="notifica-meta">
                <span class="notifica-tipo" data-color="<?= $config['color'] ?>"><?= $config['label'] ?></span>
            </div>
        </div>
        
        <div class="notifica-actions">
            <button class="btn-notifica-action btn-delete" onclick="event.stopPropagation(); eliminaNotifica(<?= $n['notifica_id'] ?>)" title="Elimina">
                🗑️
            </button>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ============================================================================
     EMPTY STATE
     ============================================================================ -->
<?php if ($totaleNotifiche === 0): ?>
<div class="no-results">
    <div class="no-results-icon">🔔</div>
    <h2 class="h5">Nessuna notifica</h2>
    <p>
        <?php if ($filtro === 'non_lette'): ?>
            Non hai notifiche non lette.
        <?php else: ?>
            Non hai ancora ricevuto notifiche.
        <?php endif; ?>
    </p>
</div>
<?php endif; ?>

<!-- ============================================================================
     MODAL: CONFERMA AZIONE
     ============================================================================ -->
<div class="modal fade" id="modalConferma" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-sm" style="z-index: 1061;">
        <div class="modal-content modal-notifica-content" style="pointer-events: auto;">
            <div class="modal-header modal-header-action">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon" id="modalConfermaIcon">✓</div>
                    <div>
                        <h5 class="modal-title" id="modalConfermaTitolo">Conferma</h5>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <p class="modal-messaggio" id="modalConfermaMessaggio">Sei sicuro di voler procedere?</p>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary-dark" data-bs-dismiss="modal">
                    Annulla
                </button>
                <button type="button" class="btn btn-confirm-action" id="btnConfermaAzione">
                    Conferma
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// ============================================================================
// FUNZIONI JAVASCRIPT
// ============================================================================

// Filtro corrente
const filtroCorrente = '<?= $filtro ?>';

// Variabile per azione corrente
let azioneCorrente = null;

// Fix modal - sposta nel body
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalConferma');
    if (modal && modal.parentElement !== document.body) {
        document.body.appendChild(modal);
    }
});

// Mostra modal di conferma
function mostraConferma(tipo, titolo, messaggio, icona) {
    document.getElementById('modalConfermaIcon').textContent = icona;
    document.getElementById('modalConfermaTitolo').textContent = titolo;
    document.getElementById('modalConfermaMessaggio').textContent = messaggio;
    
    // Cambia stile bottone in base al tipo
    const btn = document.getElementById('btnConfermaAzione');
    btn.className = 'btn ' + (tipo === 'elimina' ? 'btn-danger-custom' : 'btn-confirm-action');
    btn.textContent = tipo === 'elimina' ? '🗑️ Elimina' : '✓ Conferma';
    
    azioneCorrente = tipo;
    new bootstrap.Modal(document.getElementById('modalConferma')).show();
}

// Gestisci click conferma
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('btnConfermaAzione').addEventListener('click', function() {
        bootstrap.Modal.getInstance(document.getElementById('modalConferma')).hide();
        
        setTimeout(() => {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            
            if (azioneCorrente === 'segna_tutte') {
                eseguiSegnaTutteLette();
            } else if (azioneCorrente === 'elimina') {
                eseguiEliminaTutteLette();
            }
        }, 300);
    });
});

// Filtra notifiche
function filtraNotifiche(filtro) {
    window.location.href = 'notifiche.php?filtro=' + filtro;
}

// Toggle sezione precedenti
function togglePrecedenti() {
    const content = document.getElementById('precedentiContent');
    const icon = document.getElementById('togglePrecedentiIcon');
    
    if (content.style.display === 'none') {
        content.style.display = 'block';
        icon.textContent = '▲';
    } else {
        content.style.display = 'none';
        icon.textContent = '▼';
    }
}

// Aggiorna tutti i contatori nella pagina
function aggiornaContatori(decremento = 1) {
    // KPI Card "Non Lette"
    const kpiNonLette = document.querySelector('.kpi-card[data-color="red"] .kpi-value');
    if (kpiNonLette) {
        let val = parseInt(kpiNonLette.textContent) - decremento;
        if (val < 0) val = 0;
        kpiNonLette.textContent = val;
        
        // Nascondi notification-dot se 0
        if (val === 0) {
            const dot = document.querySelector('.kpi-card[data-color="red"] .notification-dot');
            if (dot) dot.style.display = 'none';
        }
    }
    
    // Badge nel filtro "Non Lette"
    const chipBadge = document.querySelector('.filter-chip.active .chip-badge, .filter-chip:nth-child(2) .chip-badge');
    if (chipBadge) {
        let val = parseInt(chipBadge.textContent) - decremento;
        if (val < 0) val = 0;
        chipBadge.textContent = val;
        if (val === 0) chipBadge.style.display = 'none';
    }
    
    // Badge sidebar (se esiste)
    const sidebarBadge = document.querySelector('.sidebar-nav .nav-badge');
    if (sidebarBadge) {
        let val = parseInt(sidebarBadge.textContent) - decremento;
        if (val < 0) val = 0;
        if (val === 0) {
            sidebarBadge.style.display = 'none';
        } else {
            sidebarBadge.textContent = val > 99 ? '99+' : val;
        }
    }
}

// Apri notifica (segna come letta e naviga al link)
function apriNotifica(notificaId, link, giaLetta) {
    // Se non è già letta, segnala come letta
    if (!giaLetta) {
        fetch('notifiche.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `action=segna_letta&notifica_id=${notificaId}`
        }).then(() => {
            const card = document.querySelector(`.notifica-card[data-id="${notificaId}"]`);
            if (card) {
                // Se siamo nel filtro "non_lette", rimuovi la card
                if (filtroCorrente === 'non_lette') {
                    card.style.animation = 'slideOut 0.3s ease forwards';
                    setTimeout(() => card.remove(), 300);
                } else {
                    // Altrimenti aggiorna solo lo stile
                    card.classList.remove('non-letta');
                    card.classList.add('letta');
                    const badge = card.querySelector('.badge-non-letta');
                    if (badge) badge.remove();
                    const btnCheck = card.querySelector('.btn-notifica-action:not(.btn-delete)');
                    if (btnCheck) btnCheck.remove();
                }
                aggiornaContatori(1);
            }
        });
    }
    
    // Naviga al link se presente
    if (link) {
        setTimeout(() => {
            window.location.href = link;
        }, giaLetta ? 0 : 350);
    }
}

// Segna singola notifica come letta
function segnaLetta(notificaId) {
    fetch('notifiche.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=segna_letta&notifica_id=${notificaId}`
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            const card = document.querySelector(`.notifica-card[data-id="${notificaId}"]`);
            if (card) {
                // Se siamo nel filtro "non_lette", rimuovi la card con animazione
                if (filtroCorrente === 'non_lette') {
                    card.style.animation = 'slideOut 0.3s ease forwards';
                    setTimeout(() => card.remove(), 300);
                } else {
                    // Altrimenti aggiorna solo lo stile
                    card.classList.remove('non-letta');
                    card.classList.add('letta');
                    const badge = card.querySelector('.badge-non-letta');
                    if (badge) badge.remove();
                    const btnCheck = card.querySelector('.btn-notifica-action:not(.btn-delete)');
                    if (btnCheck) btnCheck.remove();
                }
                
                // Aggiorna contatori
                aggiornaContatori(1);
            }
        }
    });
}

// Segna tutte come lette - mostra modal
function segnaTutteLette() {
    mostraConferma('segna_tutte', 'Segna tutte lette', 'Vuoi segnare tutte le notifiche come già lette?', '✓');
}

// Esegui segna tutte lette
function eseguiSegnaTutteLette() {
    fetch('notifiche.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=segna_tutte_lette'
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            // Se siamo nel filtro "non_lette", rimuovi tutte le card
            if (filtroCorrente === 'non_lette') {
                document.querySelectorAll('.notifica-card').forEach(card => {
                    card.style.animation = 'slideOut 0.3s ease forwards';
                });
                setTimeout(() => location.reload(), 350);
            } else {
                // Aggiorna tutte le card come lette
                document.querySelectorAll('.notifica-card.non-letta').forEach(card => {
                    card.classList.remove('non-letta');
                    card.classList.add('letta');
                    const badge = card.querySelector('.badge-non-letta');
                    if (badge) badge.remove();
                    const btnCheck = card.querySelector('.btn-notifica-action:not(.btn-delete)');
                    if (btnCheck) btnCheck.remove();
                });
                
                // Azzera contatori
                const kpiNonLette = document.querySelector('.kpi-card[data-color="red"] .kpi-value');
                if (kpiNonLette) kpiNonLette.textContent = '0';
                
                const dot = document.querySelector('.kpi-card[data-color="red"] .notification-dot');
                if (dot) dot.style.display = 'none';
                
                const chipBadge = document.querySelector('.filter-chip .chip-badge');
                if (chipBadge) chipBadge.style.display = 'none';
                
                const sidebarBadge = document.querySelector('.sidebar-nav .nav-badge');
                if (sidebarBadge) sidebarBadge.style.display = 'none';
            }
        }
    });
}

// Elimina notifica
function eliminaNotifica(notificaId) {
    const card = document.querySelector(`.notifica-card[data-id="${notificaId}"]`);
    const eraNonLetta = card && card.classList.contains('non-letta');
    
    fetch('notifiche.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=elimina&notifica_id=${notificaId}`
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            if (card) {
                card.style.animation = 'slideOut 0.3s ease forwards';
                setTimeout(() => card.remove(), 300);
            }
            
            // Aggiorna contatori solo se era non letta
            if (eraNonLetta) {
                aggiornaContatori(1);
            }
            
            // Aggiorna KPI "Totali"
            const kpiTotali = document.querySelector('.kpi-card[data-color="blue"] .kpi-value');
            if (kpiTotali) {
                let val = parseInt(kpiTotali.textContent) - 1;
                if (val < 0) val = 0;
                kpiTotali.textContent = val;
            }
        }
    });
}

// Elimina tutte le lette - mostra modal
function eliminaTutteLette() {
    mostraConferma('elimina', 'Elimina notifiche lette', 'Vuoi eliminare tutte le notifiche già lette? Questa azione non può essere annullata.', '🗑️');
}

// Esegui elimina tutte lette
function eseguiEliminaTutteLette() {
    fetch('notifiche.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=elimina_lette'
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            location.reload();
        }
    });
}
</script>