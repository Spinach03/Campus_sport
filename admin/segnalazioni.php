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
        // Ottieni dettaglio segnalazione
        case 'get_segnalazione':
            $id = intval($_REQUEST['id'] ?? 0);
            $segnalazione = $dbh->getSegnalazioneById($id);
            
            if ($segnalazione) {
                // Aggiungi profili
                $segnalazione['profilo_segnalante'] = $dbh->getProfiloSegnalante($segnalazione['user_segnalante_id']);
                $segnalazione['profilo_segnalato'] = $dbh->getProfiloSegnalato($segnalazione['user_segnalato_id']);
                
                // Storico segnalazioni ricevute dal segnalato
                $segnalazione['storico_segnalazioni'] = $dbh->getStoricoSegnalazioniUtente(
                    $segnalazione['user_segnalato_id'], 
                    $id, 
                    5
                );
                
                // Sanzioni del segnalato
                $segnalazione['sanzioni_segnalato'] = $dbh->getSanzioniUtente($segnalazione['user_segnalato_id'], 5);
                
                // Contesto prenotazione se presente
                if ($segnalazione['prenotazione_id']) {
                    $segnalazione['contesto_prenotazione'] = $dbh->getPrenotazionePerSegnalazione($segnalazione['prenotazione_id']);
                }
                
                echo json_encode(['success' => true, 'segnalazione' => $segnalazione]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Segnalazione non trovata']);
            }
            exit;
            
        // Cambia stato segnalazione (in_review)
        case 'change_stato':
            $id = intval($_POST['id'] ?? 0);
            $stato = $_POST['stato'] ?? '';
            
            if (!in_array($stato, ['pending'])) {
                echo json_encode(['success' => false, 'message' => 'Stato non valido']);
                exit;
            }
            
            if ($dbh->updateSegnalazioneStato($id, $stato, $adminId)) {
                echo json_encode(['success' => true, 'message' => 'Stato aggiornato']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Errore nell\'aggiornamento']);
            }
            exit;
            
        // Risolvi segnalazione
        case 'resolve':
            $id = intval($_POST['id'] ?? 0);
            
            $data = [
                'azione' => $_POST['azione'] ?? 'nessuna',
                'note' => trim($_POST['note'] ?? ''),
                'penalty_points' => intval($_POST['penalty_points'] ?? 0),
                'giorni_sospensione' => intval($_POST['giorni_sospensione'] ?? 0),
                'invia_notifiche' => isset($_POST['invia_notifiche']) ? true : false
            ];
            
            if (empty($data['note'])) {
                echo json_encode(['success' => false, 'message' => 'Le note sono obbligatorie']);
                exit;
            }
            
            if ($dbh->resolveSegnalazione($id, $data, $adminId)) {
                echo json_encode(['success' => true, 'message' => 'Segnalazione risolta con successo']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Errore nella risoluzione']);
            }
            exit;
            
        // Rigetta segnalazione
        case 'reject':
            $id = intval($_POST['id'] ?? 0);
            $motivo = trim($_POST['motivo'] ?? '');
            $inviaNotifica = isset($_POST['invia_notifica']) ? true : false;
            
            if (empty($motivo)) {
                echo json_encode(['success' => false, 'message' => 'Il motivo è obbligatorio']);
                exit;
            }
            
            if ($dbh->rejectSegnalazione($id, $motivo, $adminId, $inviaNotifica)) {
                echo json_encode(['success' => true, 'message' => 'Segnalazione rifiutata']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Errore nel rifiuto']);
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

// Statistiche per badge
$templateParams['stats'] = $dbh->getSegnalazioniStats();

// Tipi segnalazione per filtri
$templateParams['tipi_segnalazione'] = $dbh->getTipiSegnalazione();

// Filtri dalla query string
$filtri = [
    'stato' => $_GET['stato'] ?? '',
    'tipo' => $_GET['tipo'] ?? '',
    'priorita' => $_GET['priorita'] ?? '',
    'ordina' => $_GET['ordina'] ?? 'recenti',
    'search' => trim($_GET['search'] ?? ''),
    'data_da' => $_GET['data_da'] ?? '',
    'data_a' => $_GET['data_a'] ?? ''
];

$templateParams['filtri'] = $filtri;

// Lista segnalazioni filtrate
$templateParams['segnalazioni'] = $dbh->getAllSegnalazioni($filtri);

// Impostazioni pagina
$templateParams['titolo'] = 'Gestione Segnalazioni';
$templateParams['titolo_pagina'] = 'Gestione Segnalazioni';
$templateParams['nome'] = 'segnalazioni.php';

// Carica template
require 'template/base.php';
?>