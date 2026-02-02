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
        // CREA NUOVA SEGNALAZIONE
        // ====================================================================
        case 'crea_segnalazione':
            $prenotazioneId = intval($_POST['prenotazione_id'] ?? 0);
            $segnalatoId = intval($_POST['segnalato_id'] ?? 0);
            $tipo = trim($_POST['tipo'] ?? '');
            $descrizione = trim($_POST['descrizione'] ?? '');
            
            // Validazione
            if ($prenotazioneId <= 0) {
                echo json_encode(['success' => false, 'error' => 'Seleziona la prenotazione di riferimento']);
                exit;
            }
            
            if ($segnalatoId <= 0) {
                echo json_encode(['success' => false, 'error' => 'Seleziona un utente da segnalare']);
                exit;
            }
            
            $tipiValidi = ['no_show', 'comportamento_scorretto', 'linguaggio_offensivo', 'violenza', 'altro'];
            if (!in_array($tipo, $tipiValidi)) {
                echo json_encode(['success' => false, 'error' => 'Tipo segnalazione non valido']);
                exit;
            }
            
            if (strlen($descrizione) < 20) {
                echo json_encode(['success' => false, 'error' => 'La descrizione deve essere di almeno 20 caratteri']);
                exit;
            }
            
            $result = $dbh->creaSegnalazioneUtente($userId, $segnalatoId, $tipo, $descrizione, $prenotazioneId);
            echo json_encode($result);
            exit;
            
        // ====================================================================
        // CERCA UTENTI
        // ====================================================================
        case 'cerca_utenti':
            $query = trim($_POST['query'] ?? '');
            
            if (strlen($query) < 2) {
                echo json_encode(['success' => true, 'utenti' => []]);
                exit;
            }
            
            $utenti = $dbh->cercaUtentiPerSegnalazione($query, $userId);
            echo json_encode(['success' => true, 'utenti' => $utenti]);
            exit;
            
        // ====================================================================
        // OTTIENI DETTAGLIO SEGNALAZIONE
        // ====================================================================
        case 'get_segnalazione':
            $segnalazioneId = intval($_POST['segnalazione_id'] ?? 0);
            
            if ($segnalazioneId <= 0) {
                echo json_encode(['success' => false, 'error' => 'Segnalazione non valida']);
                exit;
            }
            
            $segnalazione = $dbh->getSegnalazioneUtente($segnalazioneId, $userId);
            
            if ($segnalazione) {
                echo json_encode(['success' => true, 'segnalazione' => $segnalazione]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Segnalazione non trovata']);
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

// Statistiche segnalazioni utente
$statsSegnalazioni = $dbh->contaSegnalazioniUtente($userId);

// Prenotazioni per nuova segnalazione (ultimi 15 giorni)
$prenotazioniRecenti = $dbh->getPrenotazioniPerSegnalazione($userId);

// Le mie segnalazioni (fatte da me)
$mieSegnalazioni = $dbh->getSegnalazioniUtente($userId);

// Segnalazioni ricevute (contro di me)
$segnalazioniRicevute = $dbh->getSegnalazioniRicevuteUtente($userId);

// Tipi segnalazione
$tipiSegnalazione = [
    'no_show' => ['label' => 'No Show', 'icon' => '🚫', 'desc' => 'L\'utente non si è presentato'],
    'comportamento_scorretto' => ['label' => 'Comportamento Scorretto', 'icon' => '😤', 'desc' => 'Comportamento antisportivo o scorretto'],
    'linguaggio_offensivo' => ['label' => 'Linguaggio Offensivo', 'icon' => '🤬', 'desc' => 'Insulti o linguaggio inappropriato'],
    'violenza' => ['label' => 'Violenza', 'icon' => '⚠️', 'desc' => 'Aggressione fisica o minacce'],
    'altro' => ['label' => 'Altro', 'icon' => '📋', 'desc' => 'Altro tipo di problema']
];

// Prepara template
$templateParams["titolo"] = "Campus Sports - Segnalazioni";
$templateParams["titolo_pagina"] = "Segnalazioni";
$templateParams["nome"] = "segnalazioni.php";
$templateParams["stats"] = $statsSegnalazioni;
$templateParams["prenotazioni_recenti"] = $prenotazioniRecenti;
$templateParams["segnalazioni"] = $mieSegnalazioni;
$templateParams["segnalazioni_ricevute"] = $segnalazioniRicevute;
$templateParams["tipi_segnalazione"] = $tipiSegnalazione;

require 'template/base.php';
?>