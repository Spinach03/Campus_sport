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
            
            // Cancella prenotazione
            case 'cancella':
                $prenotazioneId = intval($_POST['prenotazione_id'] ?? 0);
                $motivo = trim($_POST['motivo'] ?? '');
                
                if (!$prenotazioneId) {
                    echo json_encode(['success' => false, 'error' => 'ID prenotazione non valido']);
                    exit;
                }
                
                $userId = $_SESSION['user_id'];
                $result = $dbh->cancellaPrenotazioneUtente($prenotazioneId, $userId, $motivo ?: null);
                
                echo json_encode($result);
                exit;
            
            // Ottieni dettaglio prenotazione
            case 'get_dettaglio':
                $prenotazioneId = intval($_POST['prenotazione_id'] ?? 0);
                
                if (!$prenotazioneId) {
                    echo json_encode(['success' => false, 'error' => 'ID prenotazione non valido']);
                    exit;
                }
                
                $userId = $_SESSION['user_id'];
                $prenotazioni = $dbh->getPrenotazioniUtente($userId, 'tutte');
                
                // Cerca la prenotazione specifica
                $prenotazione = null;
                foreach ($prenotazioni as $p) {
                    if ($p['prenotazione_id'] == $prenotazioneId) {
                        $prenotazione = $p;
                        break;
                    }
                }
                
                if ($prenotazione) {
                    // Aggiungi info sulla cancellabilità
                    $oreAnticipo = intval($dbh->getConfig('ore_anticipo_cancellazione', 24));
                    $dataOraPrenotazione = $prenotazione['data_prenotazione'] . ' ' . $prenotazione['ora_inizio'];
                    $differenzaOre = (strtotime($dataOraPrenotazione) - time()) / 3600;
                    
                    $prenotazione['cancellabile'] = ($prenotazione['stato'] === 'confermata' && $differenzaOre >= $oreAnticipo);
                    $prenotazione['ore_mancanti'] = round($differenzaOre, 1);
                    $prenotazione['ore_richieste'] = $oreAnticipo;
                    
                    echo json_encode(['success' => true, 'prenotazione' => $prenotazione]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Prenotazione non trovata']);
                }
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
// CARICAMENTO PAGINA
// ============================================================================
$userId = $_SESSION['user_id'];

// Ottieni prenotazioni
$prenotazioniFuture = $dbh->getPrenotazioniUtente($userId, 'future');
$prenotazioniPassate = $dbh->getPrenotazioniUtente($userId, 'passate');

// Ottieni configurazione ore anticipo
$oreAnticipoCancellazione = intval($dbh->getConfig('ore_anticipo_cancellazione', 24));

// Separa le prenotazioni di oggi da quelle future
$oggi = date('Y-m-d');
$prenotazioniOggi = [];
$prenotazioniFutureFiltered = [];

foreach ($prenotazioniFuture as $p) {
    if ($p['data_prenotazione'] === $oggi) {
        $prenotazioniOggi[] = $p;
    } else {
        $prenotazioniFutureFiltered[] = $p;
    }
}

// Conteggi
$totaleOggi = count($prenotazioniOggi);
$totaleFuture = count($prenotazioniFutureFiltered);
$totalePassate = count($prenotazioniPassate);

// Prepara parametri template
$templateParams["titolo"] = "Le Mie Prenotazioni";
$templateParams["titolo_pagina"] = "Le Mie Prenotazioni";
$templateParams["nome"] = "le-mie-prenotazioni.php";
$templateParams["prenotazioni_oggi"] = $prenotazioniOggi;
$templateParams["prenotazioni_future"] = $prenotazioniFutureFiltered;
$templateParams["prenotazioni_passate"] = $prenotazioniPassate;
$templateParams["totale_oggi"] = $totaleOggi;
$templateParams["totale_future"] = $totaleFuture;
$templateParams["totale_passate"] = $totalePassate;
$templateParams["ore_anticipo_cancellazione"] = $oreAnticipoCancellazione;
$templateParams["css_extra"] = ["css/le-mie-prenotazioni.css"];

require 'template/base.php';
?>