<?php
require_once '../bootstrap.php';

// Check autenticazione e ruolo admin
if (!isUserLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// Gestione richieste AJAX
if (isset($_REQUEST['ajax']) && $_REQUEST['ajax'] == 1) {
    header('Content-Type: application/json');
    
    $action = $_REQUEST['action'] ?? '';
    $adminId = $_SESSION['user_id'];
    
    switch ($action) {
        // Ottieni dettaglio recensione
        case 'get_recensione':
            $id = intval($_REQUEST['id'] ?? 0);
            $recensione = $dbh->getRecensioneById($id);
            
            if ($recensione) {
                // Aggiungi risposte
                $recensione['risposte'] = $dbh->getRecensioneRisposte($id);
                
                // Stats campo
                $recensione['stats_campo'] = $dbh->getRecensioniStatsCampo($recensione['campo_id']);
                
                echo json_encode(['success' => true, 'recensione' => $recensione]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Recensione non trovata']);
            }
            exit;
            
        // Aggiungi risposta
        case 'add_risposta':
            $id = intval($_POST['id'] ?? 0);
            $testo = trim($_POST['testo'] ?? '');
            
            if (empty($testo)) {
                echo json_encode(['success' => false, 'message' => 'Il testo della risposta è obbligatorio']);
                exit;
            }
            
            if (strlen($testo) < 10) {
                echo json_encode(['success' => false, 'message' => 'La risposta deve essere di almeno 10 caratteri']);
                exit;
            }
            
            if ($dbh->addRecensioneRisposta($id, $adminId, $testo)) {
                echo json_encode(['success' => true, 'message' => 'Risposta aggiunta con successo']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Errore nell\'aggiunta della risposta']);
            }
            exit;
            
        // Elimina risposta
        case 'delete_risposta':
            $id = intval($_POST['id'] ?? 0);
            
            if ($dbh->deleteRecensioneRisposta($id)) {
                echo json_encode(['success' => true, 'message' => 'Risposta eliminata']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Errore nell\'eliminazione']);
            }
            exit;
            
        // Elimina recensione
        case 'delete_recensione':
            $id = intval($_POST['id'] ?? 0);
            
            if ($dbh->deleteRecensione($id)) {
                echo json_encode(['success' => true, 'message' => 'Recensione eliminata con successo']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Errore nell\'eliminazione della recensione']);
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

// Statistiche per KPI
$templateParams['stats'] = $dbh->getRecensioniStatsGenerali();

// Lista campi per filtro
$templateParams['campi'] = $dbh->getAllCampi();

// Lista sport per filtro
$templateParams['sport'] = $dbh->getAllSport();

// Filtri dalla query string
$filtri = [
    'campo_id' => $_GET['campo_id'] ?? '',
    'sport_id' => $_GET['sport_id'] ?? '',
    'rating' => $_GET['rating'] ?? '',
    'risposta' => $_GET['risposta'] ?? '',
    'ordina' => $_GET['ordina'] ?? 'recenti',
    'search' => trim($_GET['search'] ?? '')
];

$templateParams['filtri'] = $filtri;

// Lista recensioni filtrate
$templateParams['recensioni'] = $dbh->getAllRecensioniAdmin($filtri);

// Impostazioni pagina
$templateParams['titolo'] = 'Campus Sports - Gestione Recensioni';
$templateParams['titolo_pagina'] = 'Gestione Recensioni';
$templateParams['nome'] = 'recensioni.php';

// Carica template
require 'template/base.php';
?>