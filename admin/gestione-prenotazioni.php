<?php
require_once '../bootstrap.php';

// Check autenticazione e ruolo admin
if (!isUserLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// ============================================================================
// GESTIONE AZIONI AJAX
// ============================================================================

$action = $_REQUEST['action'] ?? '';
$isAjax = isset($_REQUEST['ajax']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

if ($isAjax && $action) {
    header('Content-Type: application/json');
    
    switch ($action) {
        // ============================================
        // DETTAGLIO PRENOTAZIONE
        // ============================================
        case 'get_prenotazione':
            $prenotazioneId = intval($_GET['id'] ?? 0);
            
            if ($prenotazioneId) {
                $prenotazione = $dbh->getPrenotazioneDettaglio($prenotazioneId);
                
                if ($prenotazione) {
                    // Aggiungi info utente
                    $prenotazione['user_info'] = $dbh->getUserInfoForPrenotazione($prenotazione['user_id']);
                    // Aggiungi invitati
                    $prenotazione['invitati'] = $dbh->getInvitatiPrenotazione($prenotazioneId);
                    
                    echo json_encode(['success' => true, 'prenotazione' => $prenotazione]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Prenotazione non trovata']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'ID prenotazione non valido']);
            }
            exit;
            
        // ============================================
        // CERCA UTENTI (per nuova prenotazione)
        // ============================================
        case 'search_users':
            $search = trim($_GET['search'] ?? '');
            
            if (strlen($search) >= 2) {
                $users = $dbh->searchUsersForPrenotazione($search);
                echo json_encode(['success' => true, 'users' => $users]);
            } else {
                echo json_encode(['success' => true, 'users' => []]);
            }
            exit;
            
        // ============================================
        // OTTIENI CAMPI DISPONIBILI
        // ============================================
        case 'get_campi':
            $sportId = intval($_GET['sport_id'] ?? 0);
            $campi = $dbh->getCampiDisponibiliPerPrenotazione($sportId);
            echo json_encode(['success' => true, 'campi' => $campi]);
            exit;
            
        // ============================================
        // OTTIENI SLOT DISPONIBILI
        // ============================================
        case 'get_slots':
            $campoId = intval($_GET['campo_id'] ?? 0);
            $data = $_GET['data'] ?? '';
            
            if ($campoId && $data) {
                $slots = $dbh->getSlotDisponibili($campoId, $data);
                echo json_encode(['success' => true, 'slots' => $slots]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Parametri mancanti']);
            }
            exit;
            
        // ============================================
        // CREA NUOVA PRENOTAZIONE
        // ============================================
        case 'create_prenotazione':
            $userId = intval($_POST['user_id'] ?? 0);
            $campoId = intval($_POST['campo_id'] ?? 0);
            $data = $_POST['data'] ?? '';
            $oraInizio = $_POST['ora_inizio'] ?? '';
            $oraFine = $_POST['ora_fine'] ?? '';
            $numPartecipanti = intval($_POST['num_partecipanti'] ?? 1);
            $note = trim($_POST['note'] ?? '');
            
            // Validazioni base
            if (!$userId) {
                echo json_encode(['success' => false, 'message' => 'Seleziona un utente']);
                exit;
            }
            if (!$campoId) {
                echo json_encode(['success' => false, 'message' => 'Seleziona un campo']);
                exit;
            }
            if (!$data || !$oraInizio || !$oraFine) {
                echo json_encode(['success' => false, 'message' => 'Data e orario sono obbligatori']);
                exit;
            }
            
            // ============================================
            // VALIDAZIONE GIORNI ANTICIPO MAX
            // ============================================
            $giorniAnticipoMax = $dbh->getConfig('giorni_anticipo_max', 7);
            $dataPrenotazione = new DateTime($data);
            $oggi = new DateTime('today');
            $dataMax = (clone $oggi)->modify("+{$giorniAnticipoMax} days");
            
            if ($dataPrenotazione < $oggi) {
                echo json_encode(['success' => false, 'message' => 'Non puoi prenotare per una data passata']);
                exit;
            }
            
            if ($dataPrenotazione > $dataMax) {
                echo json_encode(['success' => false, 'message' => "Non puoi prenotare oltre {$giorniAnticipoMax} giorni di anticipo"]);
                exit;
            }
            
            // ============================================
            // VALIDAZIONE GIORNI CHIUSURA
            // ============================================
            if ($dbh->isGiornoChiusura($data)) {
                echo json_encode(['success' => false, 'message' => 'La struttura è chiusa in questa data']);
                exit;
            }
            
            // ============================================
            // VALIDAZIONE CAMPO CHIUSO
            // ============================================
            if ($dbh->isCampoChiuso($campoId)) {
                echo json_encode(['success' => false, 'message' => 'Il campo selezionato è chiuso e non accetta prenotazioni']);
                exit;
            }
            
            // ============================================
            // VALIDAZIONE MANUTENZIONE
            // ============================================
            if ($dbh->isSlotInManutenzione($campoId, $data, $oraInizio, $oraFine)) {
                echo json_encode(['success' => false, 'message' => 'Il campo è in manutenzione durante l\'orario selezionato']);
                exit;
            }
            
            // Verifica che l'utente non sia admin
            $user = $dbh->getUserById($userId);
            if (!$user || $user['ruolo'] === 'admin') {
                echo json_encode(['success' => false, 'message' => 'Non puoi creare prenotazioni per admin']);
                exit;
            }
            
            // Verifica disponibilità slot
            if (!$dbh->isSlotDisponibile($campoId, $data, $oraInizio, $oraFine)) {
                echo json_encode(['success' => false, 'message' => 'Lo slot selezionato non è più disponibile']);
                exit;
            }
            
            // Crea prenotazione
            $prenotazioneData = [
                'user_id' => $userId,
                'campo_id' => $campoId,
                'data_prenotazione' => $data,
                'ora_inizio' => $oraInizio,
                'ora_fine' => $oraFine,
                'num_partecipanti' => $numPartecipanti,
                'note' => $note,
                'created_by_admin' => $_SESSION['user_id']
            ];
            
            $result = $dbh->createPrenotazioneAdmin($prenotazioneData);
            
            if ($result) {
                // Invia notifica all'utente
                $dbh->creaNotifica(
                    $userId,
                    'prenotazione_creata',
                    'Prenotazione Creata',
                    'Una prenotazione è stata creata per te dall\'amministrazione.',
                    'prenotazione/' . $result
                );
                
                echo json_encode(['success' => true, 'message' => 'Prenotazione creata con successo', 'prenotazione_id' => $result]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Errore durante la creazione della prenotazione']);
            }
            exit;
            
        // ============================================
        // CANCELLA PRENOTAZIONE
        // ============================================
        case 'cancel_prenotazione':
            $prenotazioneId = intval($_POST['id'] ?? 0);
            $motivo = trim($_POST['motivo'] ?? '');
            $inviaNotifica = isset($_POST['invia_notifica']);
            
            if (!$prenotazioneId) {
                echo json_encode(['success' => false, 'message' => 'ID prenotazione non valido']);
                exit;
            }
            
            // Verifica che la prenotazione sia futura e cancellabile
            $prenotazione = $dbh->getPrenotazioneDettaglio($prenotazioneId);
            if (!$prenotazione) {
                echo json_encode(['success' => false, 'message' => 'Prenotazione non trovata']);
                exit;
            }
            
            // Verifica stato
            if (!in_array($prenotazione['stato'], ['confermata'])) {
                echo json_encode(['success' => false, 'message' => 'Solo le prenotazioni confermate possono essere cancellate']);
                exit;
            }
            
            // Cancella prenotazione
            $result = $dbh->cancellaPrenotazioneAdmin($prenotazioneId, $motivo, $_SESSION['user_id']);
            
            if ($result) {
                // Invia notifica all'utente se richiesto
                if ($inviaNotifica) {
                    $dbh->creaNotifica(
                        $prenotazione['user_id'],
                        'prenotazione_cancellata',
                        'Prenotazione Cancellata',
                        'La tua prenotazione è stata cancellata dall\'amministrazione. Motivo: ' . ($motivo ?: 'Non specificato'),
                        'prenotazione/' . $prenotazioneId
                    );
                }
                
                echo json_encode(['success' => true, 'message' => 'Prenotazione cancellata con successo']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Errore durante la cancellazione']);
            }
            exit;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Azione non valida']);
            exit;
    }
}

// ============================================================================
// CARICAMENTO DATI PER LA VISTA
// ============================================================================

// Aggiorna automaticamente lo stato delle prenotazioni passate
$dbh->aggiornaStatoPrenotazioniPassate();

// Statistiche per KPI
$templateParams['stats'] = $dbh->getPrenotazioniStatsAdmin();

// Filtri dalla query string
$filtri = [
    'stato' => $_GET['stato'] ?? '',
    'campo' => $_GET['campo'] ?? '',
    'sport' => $_GET['sport'] ?? '',
    'data' => $_GET['data'] ?? '',
    'search' => trim($_GET['search'] ?? ''),
    'ordina' => $_GET['ordina'] ?? 'recenti'
];

$templateParams['filtri'] = $filtri;

// Lista prenotazioni filtrate
$templateParams['prenotazioni'] = $dbh->getAllPrenotazioniAdmin($filtri);

// Dati per filtri
$templateParams['campi'] = $dbh->getAllCampiSelect();
$templateParams['sport'] = $dbh->getAllSport();

// Configurazione per nuova prenotazione
$templateParams['giorni_anticipo_max'] = $dbh->getConfig('giorni_anticipo_max', 7);
$templateParams['giorni_chiusura'] = $dbh->getGiorniChiusuraArray();

// Impostazioni pagina
$templateParams['titolo'] = 'Gestione Prenotazioni';
$templateParams['titolo_pagina'] = 'Gestione Prenotazioni';
$templateParams['nome'] = 'gestione-prenotazioni.php';
$templateParams['css_extra'] = ['css/gestione-prenotazioni.css', 'css/modal-prenotazione.css', 'css/modal-nuova-prenotazione.css'];

// Carica template
require 'template/base.php';
?>