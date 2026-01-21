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
        // CREAZIONE NUOVO CAMPO
        // ============================================
        case 'create':
            $servizi = isset($_POST['servizi']) ? $_POST['servizi'] : [];
            
            $data = [
                'nome' => trim($_POST['nome'] ?? ''),
                'sport_id' => intval($_POST['sport_id'] ?? 0),
                'location' => trim($_POST['location'] ?? ''),
                'descrizione' => trim($_POST['descrizione'] ?? ''),
                'capienza_max' => intval($_POST['capienza_max'] ?? 0),
                'tipo_superficie' => $_POST['tipo_superficie'] ?? 'erba_sintetica',
                'tipo_campo' => $_POST['tipo_campo'] ?? 'outdoor',
                'lunghezza_m' => floatval($_POST['lunghezza_m'] ?? 0) ?: null,
                'larghezza_m' => floatval($_POST['larghezza_m'] ?? 0) ?: null,
                'orario_apertura' => $_POST['orario_apertura'] ?? '08:00',
                'orario_chiusura' => $_POST['orario_chiusura'] ?? '22:00',
                'stato' => $_POST['stato'] ?? 'disponibile',
                'created_by' => $_SESSION['user_id'],
                'servizi' => $servizi
            ];
            
            if (empty($data['nome']) || empty($data['sport_id']) || empty($data['location'])) {
                echo json_encode(['success' => false, 'message' => 'Compila tutti i campi obbligatori']);
                exit;
            }
            
            $campoId = $dbh->createCampo($data);
            
            if ($campoId) {
                echo json_encode(['success' => true, 'message' => 'Campo creato con successo', 'campo_id' => $campoId]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Errore durante la creazione del campo']);
            }
            exit;
            
        // ============================================
        // AGGIORNAMENTO CAMPO
        // ============================================
        case 'update':
            $campoId = intval($_POST['campo_id'] ?? 0);
            $servizi = isset($_POST['servizi']) ? $_POST['servizi'] : [];
            
            $data = [
                'nome' => trim($_POST['nome'] ?? ''),
                'sport_id' => intval($_POST['sport_id'] ?? 0),
                'location' => trim($_POST['location'] ?? ''),
                'descrizione' => trim($_POST['descrizione'] ?? ''),
                'capienza_max' => intval($_POST['capienza_max'] ?? 0),
                'tipo_superficie' => $_POST['tipo_superficie'] ?? 'erba_sintetica',
                'tipo_campo' => $_POST['tipo_campo'] ?? 'outdoor',
                'lunghezza_m' => floatval($_POST['lunghezza_m'] ?? 0) ?: null,
                'larghezza_m' => floatval($_POST['larghezza_m'] ?? 0) ?: null,
                'orario_apertura' => $_POST['orario_apertura'] ?? '08:00',
                'orario_chiusura' => $_POST['orario_chiusura'] ?? '22:00',
                'stato' => $_POST['stato'] ?? 'disponibile',
                'servizi' => $servizi
            ];
            
            if ($campoId && $dbh->updateCampo($campoId, $data, $_SESSION['user_id'])) {
                echo json_encode(['success' => true, 'message' => 'Campo aggiornato con successo']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Errore durante l\'aggiornamento']);
            }
            exit;
            
        // ============================================
        // CAMBIO STATO RAPIDO
        // ============================================
        case 'change_status':
            $campoId = intval($_POST['campo_id'] ?? 0);
            $nuovoStato = $_POST['stato'] ?? '';
            
            if ($campoId && in_array($nuovoStato, ['disponibile', 'manutenzione', 'chiuso'])) {
                if ($dbh->updateCampoStato($campoId, $nuovoStato, $_SESSION['user_id'])) {
                    echo json_encode(['success' => true, 'message' => 'Stato aggiornato']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Errore aggiornamento stato']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Parametri non validi']);
            }
            exit;
            
        // ============================================
        // ELIMINAZIONE CAMPO
        // ============================================
        case 'delete':
            $campoId = intval($_POST['campo_id'] ?? 0);
            
            if ($campoId) {
                $prenFuture = $dbh->countPrenotazioniFutureCampo($campoId);
                
                if ($dbh->deleteCampo($campoId)) {
                    echo json_encode(['success' => true, 'message' => 'Campo eliminato con successo', 'prenotazioni_cancellate' => $prenFuture]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Errore durante l\'eliminazione']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'ID campo non valido']);
            }
            exit;
            
        // ============================================
        // AGGIORNA STATO CAMPO (Chiudi/Riapri)
        // ============================================
        case 'update_stato':
            $campoId = intval($_POST['campo_id'] ?? 0);
            $nuovoStato = $_POST['stato'] ?? '';
            
            $statiValidi = ['disponibile', 'manutenzione', 'chiuso'];
            
            if ($campoId && in_array($nuovoStato, $statiValidi)) {
                if ($dbh->updateStatoCampo($campoId, $nuovoStato)) {
                    $messaggi = [
                        'disponibile' => 'Campo riaperto con successo',
                        'manutenzione' => 'Campo messo in manutenzione',
                        'chiuso' => 'Campo chiuso con successo'
                    ];
                    echo json_encode(['success' => true, 'message' => $messaggi[$nuovoStato]]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Errore durante l\'aggiornamento dello stato']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Dati non validi']);
            }
            exit;
            
        // ============================================
        // DETTAGLIO CAMPO
        // ============================================
        case 'get_campo':
            $campoId = intval($_GET['campo_id'] ?? 0);
            
            if ($campoId) {
                $campo = $dbh->getCampoById($campoId);
                $servizi = $dbh->getCampoServizi($campoId);
                $foto = $dbh->getCampoFoto($campoId);
                $storico = $dbh->getCampoStorico($campoId);
                $stats = $dbh->getStatisticheCampo($campoId);
                $recensioni = $dbh->getRecensioniCampo($campoId, 100);
                $recensioniStats = $dbh->getRecensioniStatsCampo($campoId);
                $blocchiManutenzione = $dbh->getBlocchiManutenzione($campoId);
                
                echo json_encode([
                    'success' => true,
                    'campo' => $campo,
                    'servizi' => $servizi,
                    'foto' => $foto,
                    'storico' => $storico,
                    'stats' => $stats,
                    'recensioni' => $recensioni,
                    'recensioni_stats' => $recensioniStats,
                    'blocchi_manutenzione' => $blocchiManutenzione
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'ID campo non valido']);
            }
            exit;
            
        // ============================================
        // BLOCCO MANUTENZIONE
        // ============================================
        case 'blocco_manutenzione':
            $data = [
                'campo_id' => intval($_POST['campo_id'] ?? 0),
                'data_inizio' => $_POST['data_inizio'] ?? '',
                'ora_inizio' => $_POST['ora_inizio'] ?? '08:00',
                'data_fine' => $_POST['data_fine'] ?? '',
                'ora_fine' => $_POST['ora_fine'] ?? '22:00',
                'tipo_blocco' => $_POST['tipo_blocco'] ?? 'manutenzione_ordinaria',
                'motivo' => trim($_POST['motivo'] ?? ''),
                'created_by' => $_SESSION['user_id']
            ];
            
            if (empty($data['campo_id']) || empty($data['data_inizio']) || empty($data['data_fine'])) {
                echo json_encode(['success' => false, 'message' => 'Compila tutti i campi obbligatori']);
                exit;
            }
            
            $bloccoId = $dbh->createBloccoManutenzione($data);
            
            if ($bloccoId) {
                echo json_encode(['success' => true, 'message' => 'Blocco manutenzione creato', 'blocco_id' => $bloccoId]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Errore durante la creazione del blocco']);
            }
            exit;
            
        // ============================================
        // RIMUOVI BLOCCO MANUTENZIONE
        // ============================================
        case 'remove_blocco':
            $bloccoId = intval($_POST['blocco_id'] ?? 0);
            
            if ($bloccoId && $dbh->deleteBloccoManutenzione($bloccoId, $_SESSION['user_id'])) {
                echo json_encode(['success' => true, 'message' => 'Blocco rimosso']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Errore rimozione blocco']);
            }
            exit;
            
        // ============================================
        // PRENOTAZIONI CAMPO (per calendario)
        // ============================================
        case 'get_prenotazioni':
            $campoId = intval($_GET['campo_id'] ?? 0);
            $dataInizio = $_GET['data_inizio'] ?? null;
            $dataFine = $_GET['data_fine'] ?? null;
            
            if ($campoId) {
                $prenotazioni = $dbh->getPrenotazioniCampo($campoId, $dataInizio, $dataFine);
                $blocchi = $dbh->getBlocchiManutenzione($campoId);
                echo json_encode(['success' => true, 'prenotazioni' => $prenotazioni, 'blocchi' => $blocchi]);
            } else {
                echo json_encode(['success' => false, 'message' => 'ID campo non valido']);
            }
            exit;
            
        // ============================================
        // DETTAGLIO PRENOTAZIONE
        // ============================================
        case 'get_prenotazione':
            $prenotazioneId = intval($_GET['prenotazione_id'] ?? 0);
            
            if ($prenotazioneId) {
                $prenotazione = $dbh->getPrenotazioneById($prenotazioneId);
                echo json_encode(['success' => true, 'prenotazione' => $prenotazione]);
            } else {
                echo json_encode(['success' => false, 'message' => 'ID prenotazione non valido']);
            }
            exit;
            
        // ============================================
        // CANCELLA PRENOTAZIONE
        // ============================================
        case 'cancel_prenotazione':
            $prenotazioneId = intval($_POST['prenotazione_id'] ?? 0);
            $motivo = trim($_POST['motivo'] ?? 'Cancellata dall\'amministratore');
            
            if ($prenotazioneId && $dbh->updatePrenotazioneStato($prenotazioneId, 'cancellata', $motivo)) {
                echo json_encode(['success' => true, 'message' => 'Prenotazione cancellata']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Errore cancellazione']);
            }
            exit;
            
        // ============================================
        // RECENSIONI
        // ============================================
        case 'get_recensioni':
            $campoId = isset($_GET['campo_id']) ? intval($_GET['campo_id']) : null;
            $filtri = [
                'campo_id' => $campoId,
                'rating_min' => isset($_GET['rating_min']) ? intval($_GET['rating_min']) : null,
                'rating_max' => isset($_GET['rating_max']) ? intval($_GET['rating_max']) : null,
                'senza_risposta' => isset($_GET['senza_risposta']) && $_GET['senza_risposta'] == '1',
                'limit' => isset($_GET['limit']) ? intval($_GET['limit']) : 50
            ];
            
            $recensioni = $dbh->getAllRecensioni($filtri);
            echo json_encode(['success' => true, 'recensioni' => $recensioni]);
            exit;
            
        // ============================================
        // RISPOSTA RECENSIONE
        // ============================================
        case 'reply_recensione':
            $recensioneId = intval($_POST['recensione_id'] ?? 0);
            $testo = trim($_POST['testo'] ?? '');
            
            if ($recensioneId && !empty($testo)) {
                if ($dbh->addRecensioneRisposta($recensioneId, $_SESSION['user_id'], $testo)) {
                    echo json_encode(['success' => true, 'message' => 'Risposta inviata']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Errore invio risposta']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Parametri non validi']);
            }
            exit;
            
        // ============================================
        // ELIMINA RECENSIONE
        // ============================================
        case 'delete_recensione':
            $recensioneId = intval($_POST['recensione_id'] ?? 0);
            
            if ($recensioneId && $dbh->deleteRecensione($recensioneId)) {
                echo json_encode(['success' => true, 'message' => 'Recensione eliminata']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Errore eliminazione']);
            }
            exit;
    }
}

// ============================================================================
// CARICAMENTO DATI PER LA PAGINA
// ============================================================================

$filtri = [
    'sport' => $_GET['sport'] ?? '',
    'stato' => $_GET['stato'] ?? '',
    'tipo' => $_GET['tipo'] ?? '',
    'search' => $_GET['search'] ?? '',
    'ordina' => $_GET['ordina'] ?? 'nome'
];

$campiStats = $dbh->getCampiStats();
$campi = $dbh->getAllCampi($filtri);
$sports = $dbh->getAllSport();
$prenotazioniOggi = $dbh->getPrenotazioniOggiAll();
$recensioniRecenti = $dbh->getAllRecensioni(['limit' => 5]);
$blocchiAttivi = $dbh->getBlocchiManutenzione();

$templateParams["titolo"] = "Campus Sports - Gestione Campi";
$templateParams["titolo_pagina"] = "Gestione Campi";
$templateParams["nome"] = "gestione-campi.php";
$templateParams["css_extra"] = ["css/gestione-campi.css", "css/modal-nuovo-campo.css", "css/modal-dettaglio-campo.css"];

$templateParams["stats"] = $campiStats;
$templateParams["campi"] = $campi;
$templateParams["sports"] = $sports;
$templateParams["filtri"] = $filtri;
$templateParams["prenotazioni_oggi"] = $prenotazioniOggi;
$templateParams["recensioni_recenti"] = $recensioniRecenti;
$templateParams["blocchi_attivi"] = $blocchiAttivi;
$templateParams["dbh"] = $dbh;

require 'template/base.php';
?>