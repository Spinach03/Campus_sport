<?php
// Variabili dal controller
$profilo = $templateParams["profilo"] ?? [];
$statistiche = $templateParams["statistiche"] ?? [];
$badges = $templateParams["badges"] ?? [];
$preferiti = $templateParams["preferiti"] ?? ['campo' => null, 'sport' => null];
$corsiLaurea = $templateParams["corsi_laurea"] ?? [];

// Helper per colore rarità badge
function getRaritaConfig($rarita) {
    $config = [
        'comune' => ['color' => '#94A3B8', 'label' => 'Comune'],
        'non_comune' => ['color' => '#10B981', 'label' => 'Non Comune'],
        'raro' => ['color' => '#3B82F6', 'label' => 'Raro'],
        'epico' => ['color' => '#8B5CF6', 'label' => 'Epico'],
        'leggendario' => ['color' => '#F59E0B', 'label' => 'Leggendario']
    ];
    return $config[$rarita] ?? $config['comune'];
}

// Helper per emoji sport (converte path .png in emoji)
function getSportEmoji($icona, $sportNome = '') {
    // Se l'icona contiene .png o path, usa emoji basato sul nome sport
    if (empty($icona) || strpos($icona, '.png') !== false || strpos($icona, '.jpg') !== false || strpos($icona, '/') !== false) {
        $sportMapping = [
            'calcio' => '⚽',
            'calcetto' => '⚽',
            'basket' => '🏀',
            'pallacanestro' => '🏀',
            'tennis' => '🎾',
            'padel' => '🎾',
            'pallavolo' => '🏐',
            'volley' => '🏐',
            'nuoto' => '🏊',
            'rugby' => '🏉',
            'golf' => '⛳',
            'ping pong' => '🏓',
            'pingpong' => '🏓',
            'badminton' => '🏸',
            'baseball' => '⚾',
            'hockey' => '🏒',
            'boxe' => '🥊',
            'pallamano' => '🤾',
            'atletica' => '🏃',
            'ciclismo' => '🚴',
            'fitness' => '💪',
            'yoga' => '🧘',
            'crossfit' => '🏋️'
        ];
        
        $nomeLower = strtolower($sportNome);
        foreach ($sportMapping as $keyword => $emoji) {
            if (strpos($nomeLower, $keyword) !== false) {
                return $emoji;
            }
        }
        return '🏅'; // Fallback generico
    }
    return $icona; // È già un emoji
}

// Helper per emoji badge (fallback se icona è un file)
function getBadgeEmoji($icona, $nome) {
    // Se l'icona contiene .png o path, usa emoji di fallback basato sul nome
    if (empty($icona) || strpos($icona, '.png') !== false || strpos($icona, '.jpg') !== false || strpos($icona, '/') !== false) {
        // Mapping basato su parole chiave nel nome
        $mapping = [
            'prima' => '🎯',
            'first' => '🎯',
            'attivo' => '⚡',
            'active' => '⚡',
            'sportivo' => '🏃',
            'prenotazion' => '📅',
            'booking' => '📅',
            'recensi' => '⭐',
            'review' => '⭐',
            'fedelt' => '💎',
            'loyal' => '💎',
            'veterano' => '🏆',
            'veteran' => '🏆',
            'campione' => '🥇',
            'champion' => '🥇',
            'esperto' => '🎖️',
            'expert' => '🎖️',
            'sociale' => '👥',
            'social' => '👥',
            'puntual' => '⏰',
            'affidabil' => '✅',
            'reliable' => '✅'
        ];
        
        $nomeLower = strtolower($nome);
        foreach ($mapping as $keyword => $emoji) {
            if (strpos($nomeLower, $keyword) !== false) {
                return $emoji;
            }
        }
        return '🏅'; // Fallback generico
    }
    return $icona; // È già un emoji
}

// Helper per iniziali avatar
$iniziali = strtoupper(substr($profilo['nome'] ?? 'U', 0, 1) . substr($profilo['cognome'] ?? 'U', 0, 1));
?>

<link rel="stylesheet" href="css/profilo.css">

<!-- Header Profilo -->
<div class="profilo-header-card">
    <div class="profilo-header-content">
        <div class="profilo-avatar">
            <span class="avatar-iniziali"><?= $iniziali ?></span>
        </div>
        
        <div class="profilo-info-principale">
            <h2 class="h1 profilo-nome"><?= htmlspecialchars($profilo['nome'] ?? '') ?> <?= htmlspecialchars($profilo['cognome'] ?? '') ?></h2>
            <p class="profilo-email"><?= htmlspecialchars($profilo['email'] ?? '') ?></p>
            
            <div class="profilo-meta-row">
                <?php if ($profilo['corso_nome']): ?>
                <span class="meta-tag">🎓 <?= htmlspecialchars($profilo['corso_nome']) ?></span>
                <?php endif; ?>
                <?php if ($profilo['stato'] === 'attivo'): ?>
                <span class="meta-tag tag-attivo">✅ Account Attivo</span>
                <?php elseif ($profilo['stato'] === 'sospeso'): ?>
                <span class="meta-tag tag-sospeso">⏸️ Account Sospeso</span>
                <?php else: ?>
                <span class="meta-tag tag-bannato">⛔ Account Bannato</span>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="profilo-actions">
            <button class="btn-edit-profilo" onclick="apriModalModifica()">
                ✏️ Modifica Profilo
            </button>
        </div>
    </div>
</div>

<!-- Statistiche -->
<div class="section-header">
    <h2 class="h5 section-title">
        <span class="section-icon">📊</span>
        Le Mie Statistiche
    </h2>
</div>

<div class="stats-grid">
    <div class="stat-card" data-color="blue">
        <div class="stat-icon">📅</div>
        <div class="stat-value"><?= $statistiche['totale_prenotazioni'] ?? 0 ?></div>
        <div class="stat-label">Prenotazioni Totali</div>
    </div>
    
    <div class="stat-card" data-color="green">
        <div class="stat-icon">✅</div>
        <div class="stat-value"><?= $statistiche['completate'] ?? 0 ?></div>
        <div class="stat-label">Completate</div>
    </div>
    
    <div class="stat-card" data-color="purple">
        <div class="stat-icon">⏱️</div>
        <div class="stat-value"><?= $statistiche['ore_giocate'] ?? 0 ?>h</div>
        <div class="stat-label">Ore Giocate</div>
    </div>
    
    <div class="stat-card" data-color="cyan">
        <div class="stat-icon">💯</div>
        <div class="stat-value"><?= $statistiche['affidabilita'] ?? 100 ?>%</div>
        <div class="stat-label">Affidabilità</div>
    </div>
    
    <div class="stat-card" data-color="orange">
        <div class="stat-icon">⭐</div>
        <div class="stat-value"><?= $statistiche['recensioni_fatte'] ?? 0 ?></div>
        <div class="stat-label">Recensioni Scritte</div>
    </div>
    
    <div class="stat-card" data-color="pink">
        <div class="stat-icon">🏅</div>
        <div class="stat-value"><?= $statistiche['badges_sbloccati'] ?? 0 ?></div>
        <div class="stat-label">Badges Ottenuti</div>
    </div>
</div>

<!-- Informazioni e Preferiti -->
<div class="row g-4 mt-2">
    <!-- Informazioni Personali -->
    <div class="col-lg-6">
        <div class="info-card">
            <div class="info-card-header">
                <span class="info-icon">👤</span>
                <h3 class="h5">Informazioni Personali</h3>
            </div>
            <div class="info-card-body">
                <div class="info-row">
                    <span class="info-label">Email</span>
                    <span class="info-value"><?= htmlspecialchars($profilo['email'] ?? '-') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Telefono</span>
                    <span class="info-value"><?= htmlspecialchars($profilo['telefono'] ?? 'Non specificato') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Data di Nascita</span>
                    <span class="info-value"><?= $profilo['data_nascita'] ? date('d/m/Y', strtotime($profilo['data_nascita'])) : 'Non specificata' ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Indirizzo</span>
                    <span class="info-value"><?= htmlspecialchars($profilo['indirizzo'] ?? 'Non specificato') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Corso di Laurea</span>
                    <span class="info-value"><?= htmlspecialchars($profilo['corso_nome'] ?? 'Non specificato') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Anno Iscrizione</span>
                    <span class="info-value"><?= $profilo['anno_iscrizione'] ?? 'Non specificato' ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Preferiti e Penalty -->
    <div class="col-lg-6">
        <div class="info-card preferiti-card">
            <div class="info-card-header">
                <span class="info-icon">❤️</span>
                <h3 class="h5">I Miei Preferiti</h3>
            </div>
            <div class="info-card-body">
                <?php if ($preferiti['sport']): 
                    $sportEmoji = getSportEmoji($preferiti['sport']['icona'] ?? '', $preferiti['sport']['sport_nome'] ?? '');
                ?>
                <div class="preferito-row">
                    <span class="preferito-icon"><?= $sportEmoji ?></span>
                    <div class="preferito-info">
                        <span class="preferito-label">Sport Preferito</span>
                        <span class="preferito-value"><?= htmlspecialchars($preferiti['sport']['sport_nome']) ?></span>
                        <span class="preferito-stats"><?= $preferiti['sport']['num_prenotazioni'] ?> prenotazioni • <?= $preferiti['sport']['ore_totali'] ?? 0 ?>h giocate</span>
                    </div>
                </div>
                <?php else: ?>
                <div class="preferito-row empty">
                    <span class="preferito-icon">⚽</span>
                    <div class="preferito-info">
                        <span class="preferito-label">Sport Preferito</span>
                        <span class="preferito-value empty-text">Nessuna prenotazione ancora</span>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($preferiti['campo']): ?>
                <div class="preferito-row">
                    <span class="preferito-icon">🏟️</span>
                    <div class="preferito-info">
                        <span class="preferito-label">Campo Preferito</span>
                        <span class="preferito-value"><?= htmlspecialchars($preferiti['campo']['campo_nome']) ?></span>
                        <span class="preferito-stats"><?= $preferiti['campo']['num_prenotazioni'] ?> prenotazioni • <?= $preferiti['campo']['ore_totali'] ?? 0 ?>h giocate</span>
                    </div>
                </div>
                <?php else: ?>
                <div class="preferito-row empty">
                    <span class="preferito-icon">🏟️</span>
                    <div class="preferito-info">
                        <span class="preferito-label">Campo Preferito</span>
                        <span class="preferito-value empty-text">Nessuna prenotazione ancora</span>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (($profilo['penalty_points'] ?? 0) > 0): ?>
                <div class="penalty-warning">
                    <span class="penalty-icon">⚠️</span>
                    <div class="penalty-info">
                        <span class="penalty-label">Punti Penalty</span>
                        <span class="penalty-value"><?= $profilo['penalty_points'] ?> punti</span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Badges -->
<div class="section-header mt-4">
    <h2 class="h5 section-title">
        <span class="section-icon">🏅</span>
        I Miei Badges
        <span class="section-badge"><?= count($badges) ?></span>
    </h2>
</div>

<?php if (empty($badges)): ?>
<div class="empty-state-card">
    <div class="empty-icon">🏅</div>
    <h3 class="h5">Nessun badge ancora</h3>
    <p>Completa prenotazioni e attività per sbloccare badges!</p>
</div>
<?php else: ?>
<div class="badges-grid">
    <?php foreach ($badges as $badge): 
        $raritaConfig = getRaritaConfig($badge['rarita']);
        $badgeEmoji = getBadgeEmoji($badge['icona'], $badge['nome']);
    ?>
    <div class="badge-card" style="--rarita-color: <?= $raritaConfig['color'] ?>">
        <div class="badge-icon"><?= $badgeEmoji ?></div>
        <div class="badge-info">
            <h4 class="h6 badge-nome"><?= htmlspecialchars($badge['nome']) ?></h4>
            <p class="badge-desc"><?= htmlspecialchars($badge['descrizione']) ?></p>
            <div class="badge-meta">
                <span class="badge-rarita" style="color: <?= $raritaConfig['color'] ?>"><?= $raritaConfig['label'] ?></span>
                <span class="badge-data">Ottenuto il <?= date('d/m/Y', strtotime($badge['sbloccato_at'])) ?></span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ============================================================================
     MODAL MODIFICA PROFILO
     ============================================================================ -->
<div class="modal fade" id="modalModificaProfilo" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="z-index: 1061;">
        <div class="modal-content modal-content-dark" style="pointer-events: auto;">
            <div class="modal-header modal-header-gradient">
                <div class="modal-header-content">
                    <span class="modal-header-icon">✏️</span>
                    <div>
                        <h5 class="modal-title">Modifica Profilo</h5>
                        <p class="modal-subtitle">Aggiorna le tue informazioni</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nome <span class="field-locked">🔒</span></label>
                            <input type="text" id="editNome" class="form-control-dark form-control-readonly" value="<?= htmlspecialchars($profilo['nome'] ?? '') ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Cognome <span class="field-locked">🔒</span></label>
                            <input type="text" id="editCognome" class="form-control-dark form-control-readonly" value="<?= htmlspecialchars($profilo['cognome'] ?? '') ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Telefono</label>
                            <input type="tel" id="editTelefono" class="form-control-dark" value="<?= htmlspecialchars($profilo['telefono'] ?? '') ?>" placeholder="+39 123 456 7890">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Data di Nascita <span class="field-locked">🔒</span></label>
                            <input type="date" id="editDataNascita" class="form-control-dark form-control-readonly" value="<?= $profilo['data_nascita'] ?? '' ?>" readonly>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>Indirizzo</label>
                            <input type="text" id="editIndirizzo" class="form-control-dark" value="<?= htmlspecialchars($profilo['indirizzo'] ?? '') ?>" placeholder="Via, Città, CAP">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Corso di Laurea <span class="field-locked">🔒</span></label>
                            <select id="editCorso" class="form-control-dark form-control-readonly" disabled>
                                <option value="">-- Seleziona corso --</option>
                                <?php foreach ($corsiLaurea as $corso): ?>
                                <option value="<?= $corso['corso_id'] ?>" <?= $profilo['corso_laurea_id'] == $corso['corso_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($corso['nome']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Anno Iscrizione <span class="field-locked">🔒</span></label>
                            <select id="editAnnoIscrizione" class="form-control-dark form-control-readonly" disabled>
                                <option value="">-- Anno --</option>
                                <?php for ($anno = date('Y'); $anno >= 2015; $anno--): ?>
                                <option value="<?= $anno ?>" <?= $profilo['anno_iscrizione'] == $anno ? 'selected' : '' ?>><?= $anno ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <p class="field-locked-hint">🔒 I campi bloccati non possono essere modificati</p>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary-dark" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn-primary-gradient" onclick="salvaProfilo()">
                    💾 Salva Modifiche
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     JAVASCRIPT
     ============================================================================ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fix modal - sposta nel body
    const modalsToMove = ['modalModificaProfilo'];
    modalsToMove.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });
});

// ============================================================================
// APRI MODAL
// ============================================================================
function apriModalModifica() {
    new bootstrap.Modal(document.getElementById('modalModificaProfilo')).show();
}

// ============================================================================
// SALVA PROFILO
// ============================================================================
function salvaProfilo() {
    const telefono = document.getElementById('editTelefono').value.trim();
    const indirizzo = document.getElementById('editIndirizzo').value.trim();
    
    const formData = new FormData();
    formData.append('action', 'aggiorna_profilo');
    formData.append('telefono', telefono);
    formData.append('indirizzo', indirizzo);
    
    fetch('profilo.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalModificaProfilo')).hide();
                mostraToast('Profilo aggiornato con successo!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                mostraToast(data.error || 'Errore', 'error');
            }
        })
        .catch(() => mostraToast('Errore di connessione', 'error'));
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