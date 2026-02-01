<?php
require_once '../bootstrap.php';

if(!isUserLoggedIn() || isAdmin()){
    header("Location: ../login.php");
    exit;
}

// ============================================================================
// GESTIONE AZIONI AJAX
// ============================================================================
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        switch ($_POST['action']) {
            
            // Ottieni dettagli campo
            case 'get_campo':
                $campoId = intval($_POST['campo_id'] ?? 0);
                
                if (!$campoId) {
                    echo json_encode(['success' => false, 'error' => 'ID campo non valido']);
                    exit;
                }
                
                $campo = $dbh->getCampoDettaglioUtente($campoId);
                
                if ($campo) {
                    echo json_encode(['success' => true, 'campo' => $campo]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Campo non trovato o non disponibile']);
                }
                exit;
                
            // Ottieni slot disponibili
            case 'get_slot':
                $campoId = intval($_POST['campo_id'] ?? 0);
                $data = $_POST['data'] ?? '';
                
                if (!$campoId || !$data) {
                    echo json_encode(['success' => false, 'error' => 'Dati mancanti']);
                    exit;
                }
                
                // Verifica se è un giorno di chiusura
                if ($dbh->isGiornoChiusura($data)) {
                    echo json_encode(['success' => true, 'slots' => [], 'message' => 'Il centro è chiuso in questa data']);
                    exit;
                }
                
                $slots = $dbh->getSlotDisponibiliUtente($campoId, $data);
                echo json_encode(['success' => true, 'slots' => $slots]);
                exit;
                
            // Crea prenotazione
            case 'prenota':
                $campoId = intval($_POST['campo_id'] ?? 0);
                $data = $_POST['data'] ?? '';
                $oraInizio = $_POST['ora_inizio'] ?? '';
                $oraFine = $_POST['ora_fine'] ?? '';
                $numPartecipanti = intval($_POST['num_partecipanti'] ?? 1);
                $note = trim($_POST['note'] ?? '');
                
                if (!$campoId || !$data || !$oraInizio || !$oraFine) {
                    echo json_encode(['success' => false, 'error' => 'Dati mancanti']);
                    exit;
                }
                
                $userId = $_SESSION['user_id'];
                $result = $dbh->createPrenotazioneUtente($userId, $campoId, $data, $oraInizio, $oraFine, $numPartecipanti, $note ?: null);
                
                echo json_encode($result);
                exit;
            
            // Ottieni recensioni campo
            case 'get_recensioni':
                $campoId = intval($_POST['campo_id'] ?? 0);
                
                if (!$campoId) {
                    echo json_encode(['success' => false, 'error' => 'ID campo non valido']);
                    exit;
                }
                
                $recensioni = $dbh->getRecensioniCampoConRisposte($campoId);
                echo json_encode(['success' => true, 'recensioni' => $recensioni]);
                exit;
                
            default:
                echo json_encode(['success' => false, 'error' => 'Azione non riconosciuta']);
                exit;
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Errore del server: ' . $e->getMessage()]);
        exit;
    }
}

// ============================================================================
// CARICAMENTO DATI PAGINA
// ============================================================================

// Filtri
$filtri = [
    'sport' => $_GET['sport'] ?? '',
    'tipo' => $_GET['tipo'] ?? '',
    'search' => $_GET['search'] ?? '',
    'ordina' => $_GET['ordina'] ?? 'nome'
];

// Ottieni campi disponibili dal database
$campi = $dbh->getCampiPerUtente($filtri);

// Ottieni lista sport per filtri dal database
$sports = $dbh->getAllSport();

// Configurazione dal database (usa la stessa chiave dell'admin)
$giorniMaxAnticipo = $dbh->getConfig('giorni_anticipo_max', 7);

// Prepara template
$templateParams["titolo"] = "Campus Sports - Prenota Campo";
$templateParams["titolo_pagina"] = "Prenota Campo";
$templateParams["nome"] = "prenota-campo.php";
$templateParams["campi"] = $campi;
$templateParams["sports"] = $sports;
$templateParams["filtri"] = $filtri;
$templateParams["giorni_max_anticipo"] = $giorniMaxAnticipo;

require 'template/base.php';
?>