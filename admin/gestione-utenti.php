<?php
require_once '../bootstrap.php';

if(!isUserLoggedIn() || !isAdmin()){
    header("Location: ../login.php");
    exit;
}

// ============================================================================
// GESTIONE AZIONI AJAX
// ============================================================================

$action = $_REQUEST['action'] ?? '';
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($isAjax || isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    
    switch ($action) {
        // ============================================
        // DETTAGLIO UTENTE
        // ============================================
        case 'get_user':
            $userId = intval($_GET['user_id'] ?? 0);
            
            if ($userId) {
                $user = $dbh->getUserById($userId);
                $stats = $dbh->getUserStats($userId);
                $penaltyLog = $dbh->getPenaltyLog($userId, 10);
                $segnalazioniRicevute = $dbh->getSegnalazioniRicevute($userId, 5);
                $segnalazioniFatte = $dbh->getSegnalazioniFatte($userId, 5);
                $badges = $dbh->getUserBadges($userId);
                $sanzioni = $dbh->getUserSanzioni($userId);
                $attivitaRecenti = $dbh->getUserAttivitaRecenti($userId, 10);
                
                echo json_encode([
                    'success' => true,
                    'user' => $user,
                    'stats' => $stats,
                    'penalty_log' => $penaltyLog,
                    'segnalazioni_ricevute' => $segnalazioniRicevute,
                    'segnalazioni_fatte' => $segnalazioniFatte,
                    'badges' => $badges,
                    'sanzioni' => $sanzioni,
                    'attivita_recenti' => $attivitaRecenti
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'ID utente non valido']);
            }
            exit;
            
        // ============================================
        // MODIFICA RUOLO UTENTE
        // ============================================
        case 'change_role':
            $userId = intval($_POST['user_id'] ?? 0);
            $nuovoRuolo = $_POST['ruolo'] ?? '';
            
            if ($userId && in_array($nuovoRuolo, ['user', 'admin'])) {
                // Non permettere di degradare se stessi
                if ($userId == $_SESSION['user_id'] && $nuovoRuolo !== 'admin') {
                    echo json_encode(['success' => false, 'message' => 'Non puoi modificare il tuo stesso ruolo']);
                    exit;
                }
                
                if ($dbh->updateUserRole($userId, $nuovoRuolo, $_SESSION['user_id'])) {
                    $msg = $nuovoRuolo === 'admin' ? 'Utente promosso ad Admin' : 'Utente declassato a ruolo standard';
                    echo json_encode(['success' => true, 'message' => $msg]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Errore durante la modifica del ruolo']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Parametri non validi']);
            }
            exit;
            
        // ============================================
        // SOSPENDI UTENTE
        // ============================================
        case 'suspend_user':
            $userId = intval($_POST['user_id'] ?? 0);
            $giorni = intval($_POST['giorni'] ?? 0);
            $motivo = trim($_POST['motivo'] ?? '');
            
            if ($userId && $giorni > 0 && !empty($motivo)) {
                // Non permettere di sospendere se stessi
                if ($userId == $_SESSION['user_id']) {
                    echo json_encode(['success' => false, 'message' => 'Non puoi sospendere te stesso']);
                    exit;
                }
                
                if ($dbh->suspendUser($userId, $giorni, $motivo, $_SESSION['user_id'])) {
                    echo json_encode(['success' => true, 'message' => "Utente sospeso per {$giorni} giorni"]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Errore durante la sospensione']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Compila tutti i campi obbligatori']);
            }
            exit;
            
        // ============================================
        // RIATTIVA UTENTE
        // ============================================
        case 'reactivate_user':
            $userId = intval($_POST['user_id'] ?? 0);
            
            if ($userId) {
                if ($dbh->reactivateUser($userId, $_SESSION['user_id'])) {
                    echo json_encode(['success' => true, 'message' => 'Utente riattivato con successo']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Errore durante la riattivazione']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'ID utente non valido']);
            }
            exit;
            
        // ============================================
        // BAN UTENTE
        // ============================================
        case 'ban_user':
            $userId = intval($_POST['user_id'] ?? 0);
            $motivo = trim($_POST['motivo'] ?? '');
            
            if ($userId && !empty($motivo)) {
                // Non permettere di bannare se stessi
                if ($userId == $_SESSION['user_id']) {
                    echo json_encode(['success' => false, 'message' => 'Non puoi bannare te stesso']);
                    exit;
                }
                
                if ($dbh->banUser($userId, $motivo, $_SESSION['user_id'])) {
                    echo json_encode(['success' => true, 'message' => 'Utente bannato permanentemente']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Errore durante il ban']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Compila tutti i campi obbligatori']);
            }
            exit;
            
        // ============================================
        // SBANNA UTENTE (Rimuovi Ban)
        // ============================================
        case 'unban_user':
            $userId = intval($_POST['user_id'] ?? 0);
            
            if ($userId) {
                if ($dbh->unbanUser($userId, $_SESSION['user_id'])) {
                    echo json_encode(['success' => true, 'message' => 'Ban rimosso con successo']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Errore durante la rimozione del ban']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'ID utente non valido']);
            }
            exit;
            
        // ============================================
        // AGGIUNGI PENALTY POINTS
        // ============================================
        case 'add_penalty':
            $userId = intval($_POST['user_id'] ?? 0);
            $punti = intval($_POST['punti'] ?? 0);
            $descrizione = trim($_POST['descrizione'] ?? '');
            
            if ($userId && $punti > 0) {
                $motivo = 'admin_add';
                if ($dbh->addPenaltyPoints($userId, $punti, $motivo, $descrizione, $_SESSION['user_id'])) {
                    echo json_encode(['success' => true, 'message' => "Aggiunti {$punti} penalty points"]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Errore durante l\'aggiunta dei punti']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Numero di punti non valido']);
            }
            exit;
            
        // ============================================
        // RIMUOVI PENALTY POINTS
        // ============================================
        case 'remove_penalty':
            $userId = intval($_POST['user_id'] ?? 0);
            $punti = intval($_POST['punti'] ?? 0);
            $descrizione = trim($_POST['descrizione'] ?? '');
            
            if ($userId && $punti > 0) {
                $motivo = 'admin_remove';
                if ($dbh->removePenaltyPoints($userId, $punti, $motivo, $descrizione, $_SESSION['user_id'])) {
                    echo json_encode(['success' => true, 'message' => "Rimossi {$punti} penalty points"]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Errore durante la rimozione dei punti']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Numero di punti non valido']);
            }
            exit;
            
        // ============================================
        // RESET PENALTY POINTS
        // ============================================
        case 'reset_penalty':
            $userId = intval($_POST['user_id'] ?? 0);
            $descrizione = trim($_POST['descrizione'] ?? 'Reset amministrativo');
            
            if ($userId) {
                if ($dbh->resetPenaltyPoints($userId, $descrizione, $_SESSION['user_id'])) {
                    echo json_encode(['success' => true, 'message' => 'Penalty points azzerati']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Errore durante il reset']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'ID utente non valido']);
            }
            exit;
            
        // ============================================
        // INVIA MESSAGGIO
        // ============================================
        case 'send_message':
            $userId = intval($_POST['user_id'] ?? 0);
            $oggetto = trim($_POST['oggetto'] ?? '');
            $messaggio = trim($_POST['messaggio'] ?? '');
            $tipoInvio = $_POST['tipo_invio'] ?? 'notifica';
            
            if ($userId && !empty($oggetto) && !empty($messaggio)) {
                if ($dbh->sendUserMessage($userId, $oggetto, $messaggio, $tipoInvio, $_SESSION['user_id'])) {
                    echo json_encode(['success' => true, 'message' => 'Messaggio inviato con successo']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Errore durante l\'invio del messaggio']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Compila tutti i campi obbligatori']);
            }
            exit;
    }
}

// ============================================================================
// CARICAMENTO DATI PER LA PAGINA
// ============================================================================

// Filtri dalla query string
$filtri = [
    'ruolo' => $_GET['ruolo'] ?? '',
    'stato' => $_GET['stato'] ?? '',
    'corso' => $_GET['corso'] ?? '',
    'penalty_min' => $_GET['penalty_min'] ?? '',
    'search' => $_GET['search'] ?? '',
    'ordina' => $_GET['ordina'] ?? 'nome'
];

// Carica dati
$statsGenerali = $dbh->getUsersStatsGenerali();
$users = $dbh->getAllUsers($filtri);
$corsi = $dbh->getCorsiLaurea();

// Parametri template
$templateParams["titolo"] = "Campus Sports - Gestione Utenti";
$templateParams["titolo_pagina"] = "Gestione Utenti";
$templateParams["nome"] = "gestione-utenti.php";
$templateParams["css_extra"] = ["css/gestione-utenti.css", "css/modal-utente.css"];

// Passa dati al template
$templateParams["statsGenerali"] = $statsGenerali;
$templateParams["users"] = $users;
$templateParams["corsi"] = $corsi;
$templateParams["filtri"] = $filtri;
$templateParams["pageTitle"] = "Gestione Utenti";
$templateParams["pageSubtitle"] = "Monitora, gestisci e modera gli utenti della piattaforma";

require 'template/base.php';
?>