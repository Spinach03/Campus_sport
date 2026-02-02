<?php
require_once '../bootstrap.php';

if(!isUserLoggedIn() || isAdmin()){
    header("Location: ../login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// ============================================================================
// GESTIONE AZIONI AJAX
// ============================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'];
    
    switch ($action) {
        // ====================================================================
        // CREA NUOVA RECENSIONE
        // ====================================================================
        case 'crea_recensione':
            $prenotazioneId = intval($_POST['prenotazione_id'] ?? 0);
            $ratingGenerale = intval($_POST['rating_generale'] ?? 0);
            $ratingCondizioni = intval($_POST['rating_condizioni'] ?? 0);
            $ratingPulizia = intval($_POST['rating_pulizia'] ?? 0);
            $ratingIlluminazione = intval($_POST['rating_illuminazione'] ?? 0);
            $commento = trim($_POST['commento'] ?? '');
            
            // Validazione
            if ($prenotazioneId <= 0) {
                echo json_encode(['success' => false, 'error' => 'Prenotazione non valida']);
                exit;
            }
            
            if ($ratingGenerale < 1 || $ratingGenerale > 5) {
                echo json_encode(['success' => false, 'error' => 'Valutazione generale obbligatoria (1-5 stelle)']);
                exit;
            }
            
            $result = $dbh->creaRecensioneUtente(
                $userId, 
                $prenotazioneId, 
                $ratingGenerale, 
                $ratingCondizioni ?: null, 
                $ratingPulizia ?: null, 
                $ratingIlluminazione ?: null, 
                $commento ?: null
            );
            
            echo json_encode($result);
            exit;
            
        // ====================================================================
        // MODIFICA RECENSIONE
        // ====================================================================
        case 'modifica_recensione':
            $recensioneId = intval($_POST['recensione_id'] ?? 0);
            $ratingGenerale = intval($_POST['rating_generale'] ?? 0);
            $ratingCondizioni = intval($_POST['rating_condizioni'] ?? 0);
            $ratingPulizia = intval($_POST['rating_pulizia'] ?? 0);
            $ratingIlluminazione = intval($_POST['rating_illuminazione'] ?? 0);
            $commento = trim($_POST['commento'] ?? '');
            
            if ($recensioneId <= 0 || $ratingGenerale < 1 || $ratingGenerale > 5) {
                echo json_encode(['success' => false, 'error' => 'Dati non validi']);
                exit;
            }
            
            $result = $dbh->modificaRecensioneUtente(
                $recensioneId,
                $userId,
                $ratingGenerale,
                $ratingCondizioni ?: null,
                $ratingPulizia ?: null,
                $ratingIlluminazione ?: null,
                $commento ?: null
            );
            
            echo json_encode($result);
            exit;
            
        // ====================================================================
        // ELIMINA RECENSIONE
        // ====================================================================
        case 'elimina_recensione':
            $recensioneId = intval($_POST['recensione_id'] ?? 0);
            
            if ($recensioneId <= 0) {
                echo json_encode(['success' => false, 'error' => 'Recensione non valida']);
                exit;
            }
            
            $result = $dbh->eliminaRecensioneUtente($recensioneId, $userId);
            echo json_encode($result);
            exit;
            
        // ====================================================================
        // OTTIENI DETTAGLIO RECENSIONE
        // ====================================================================
        case 'get_recensione':
            $recensioneId = intval($_POST['recensione_id'] ?? 0);
            
            if ($recensioneId <= 0) {
                echo json_encode(['success' => false, 'error' => 'Recensione non valida']);
                exit;
            }
            
            $recensione = $dbh->getRecensioneUtente($recensioneId, $userId);
            
            if ($recensione) {
                echo json_encode(['success' => true, 'recensione' => $recensione]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Recensione non trovata']);
            }
            exit;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Azione non valida']);
            exit;
    }
}

// ============================================================================
// CARICAMENTO DATI PAGINA
// ============================================================================

// Statistiche recensioni utente
$statsRecensioni = $dbh->contaRecensioniUtente($userId);

// Prenotazioni da recensire
$daRecensire = $dbh->getPrenotazioniDaRecensire($userId);

// Le mie recensioni
$mieRecensioni = $dbh->getRecensioniUtente($userId);

// Prepara template
$templateParams["titolo"] = "Campus Sports - Le Mie Recensioni";
$templateParams["titolo_pagina"] = "Recensioni";
$templateParams["nome"] = "recensioni.php";
$templateParams["stats"] = $statsRecensioni;
$templateParams["da_recensire"] = $daRecensire;
$templateParams["recensioni"] = $mieRecensioni;

require 'template/base.php';
?>