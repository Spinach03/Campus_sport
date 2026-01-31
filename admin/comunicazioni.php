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
        // ============================================================================
        // Conta destinatari in base ai filtri
        // ============================================================================
        case 'count_destinatari':
            $targetType = $_POST['target_type'] ?? 'tutti';
            $targetFilter = $_POST['target_filter'] ?? null;
            
            $count = $dbh->countDestinatariBroadcast($targetType, $targetFilter);
            echo json_encode(['success' => true, 'count' => $count]);
            exit;
        
        // ============================================================================
        // Invia broadcast
        // ============================================================================
        case 'send_broadcast':
            $data = [
                'admin_id' => $adminId,
                'oggetto' => trim($_POST['oggetto'] ?? ''),
                'messaggio' => trim($_POST['messaggio'] ?? ''),
                'target_type' => $_POST['target_type'] ?? 'tutti',
                'target_filter' => !empty($_POST['target_filter']) ? json_encode($_POST['target_filter']) : null,
                'canale' => $_POST['canale'] ?? 'in_app',
                'scheduled_at' => !empty($_POST['scheduled_at']) ? $_POST['scheduled_at'] : null
            ];
            
            // Validazione
            if (empty($data['oggetto'])) {
                echo json_encode(['success' => false, 'message' => 'L\'oggetto è obbligatorio']);
                exit;
            }
            if (strlen($data['oggetto']) > 100) {
                echo json_encode(['success' => false, 'message' => 'L\'oggetto non può superare 100 caratteri']);
                exit;
            }
            if (empty($data['messaggio'])) {
                echo json_encode(['success' => false, 'message' => 'Il messaggio è obbligatorio']);
                exit;
            }
            
            // Validazione data futura se programmata
            if (!empty($data['scheduled_at'])) {
                $scheduledTime = strtotime($data['scheduled_at']);
                if ($scheduledTime <= time()) {
                    echo json_encode(['success' => false, 'message' => 'La data di programmazione deve essere nel futuro']);
                    exit;
                }
            }
            
            $result = $dbh->createBroadcast($data);
            
            if ($result['success']) {
                echo json_encode([
                    'success' => true, 
                    'message' => $data['scheduled_at'] ? 'Broadcast programmato con successo' : 'Broadcast inviato con successo',
                    'broadcast_id' => $result['broadcast_id'],
                    'destinatari' => $result['destinatari']
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Errore durante l\'invio']);
            }
            exit;
        
        // ============================================================================
        // Salva bozza broadcast
        // ============================================================================
        case 'save_draft':
            $data = [
                'admin_id' => $adminId,
                'oggetto' => trim($_POST['oggetto'] ?? ''),
                'messaggio' => trim($_POST['messaggio'] ?? ''),
                'target_type' => $_POST['target_type'] ?? 'tutti',
                'target_filter' => !empty($_POST['target_filter']) ? json_encode($_POST['target_filter']) : null,
                'canale' => $_POST['canale'] ?? 'in_app',
                'stato' => 'bozza'
            ];
            
            // Validazione minima per bozza
            if (empty($data['oggetto']) && empty($data['messaggio'])) {
                echo json_encode(['success' => false, 'message' => 'Inserisci almeno un oggetto o un messaggio']);
                exit;
            }
            
            $result = $dbh->saveBroadcastDraft($data);
            
            if ($result) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Bozza salvata con successo',
                    'broadcast_id' => $result
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Errore durante il salvataggio']);
            }
            exit;
        
        // ============================================================================
        // Invia messaggio a uno o più utenti
        // ============================================================================
        case 'send_message':
            // Recupera user_ids - può essere array o stringa JSON
            $userIds = [];
            
            // Prova prima user_ids (nuovo formato multi-utente)
            if (!empty($_POST['user_ids'])) {
                $rawUserIds = $_POST['user_ids'];
                if (is_string($rawUserIds)) {
                    $userIds = json_decode($rawUserIds, true) ?? [];
                } else if (is_array($rawUserIds)) {
                    $userIds = $rawUserIds;
                }
            }
            
            // Retrocompatibilità con singolo user_id (vecchio formato)
            if (empty($userIds) && !empty($_POST['user_id'])) {
                $userIds = [intval($_POST['user_id'])];
            }
            
            // Filtra e converte a interi
            $userIds = array_filter(array_map('intval', $userIds), function($id) {
                return $id > 0;
            });
            $userIds = array_values($userIds); // Re-index
            
            // Recupera oggetto e messaggio
            $oggetto = trim($_POST['oggetto'] ?? '');
            $messaggio = trim($_POST['messaggio'] ?? '');
            
            // Recupera canali
            $canali = $_POST['canali'] ?? ['in_app'];
            if (is_string($canali)) {
                $canali = json_decode($canali, true) ?? ['in_app'];
            }
            if (empty($canali)) {
                $canali = ['in_app'];
            }
            
            // Validazioni
            if (empty($userIds)) {
                echo json_encode(['success' => false, 'message' => 'Seleziona almeno un destinatario']);
                exit;
            }
            if (empty($oggetto)) {
                echo json_encode(['success' => false, 'message' => 'L\'oggetto è obbligatorio']);
                exit;
            }
            if (empty($messaggio)) {
                echo json_encode(['success' => false, 'message' => 'Il messaggio è obbligatorio']);
                exit;
            }
            
            // Invia messaggi (la funzione gestisce tutto internamente)
            $successCount = $dbh->sendDirectMessage($userIds, $oggetto, $messaggio, $canali, $adminId);
            
            // Risposta
            if ($successCount > 0) {
                $totalUsers = count($userIds);
                if ($successCount == $totalUsers) {
                    $message = $successCount == 1 
                        ? 'Messaggio inviato con successo' 
                        : "Messaggio inviato a {$successCount} utenti";
                } else {
                    $message = "Messaggio inviato a {$successCount} di {$totalUsers} utenti";
                }
                echo json_encode(['success' => true, 'message' => $message, 'sent' => $successCount, 'total' => $totalUsers]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Nessun messaggio inviato. Verifica che gli utenti esistano.']);
            }
            exit;
        
        // ============================================================================
        // Ottieni dettaglio broadcast
        // ============================================================================
        case 'get_broadcast':
            $id = intval($_REQUEST['id'] ?? 0);
            $broadcast = $dbh->getBroadcastById($id);
            
            if ($broadcast) {
                echo json_encode(['success' => true, 'broadcast' => $broadcast]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Broadcast non trovato']);
            }
            exit;
        
        // ============================================================================
        // Elimina broadcast (bozze e programmati)
        // ============================================================================
        case 'delete_broadcast':
            $id = intval($_POST['id'] ?? 0);
            
            // Verifica che sia una bozza o programmato
            $broadcast = $dbh->getBroadcastById($id);
            if (!$broadcast || !in_array($broadcast['stato'], ['bozza', 'programmato'])) {
                echo json_encode(['success' => false, 'message' => 'Solo le bozze e le comunicazioni programmate possono essere eliminate']);
                exit;
            }
            
            if ($dbh->deleteBroadcast($id)) {
                $tipo = $broadcast['stato'] === 'bozza' ? 'Bozza' : 'Comunicazione programmata';
                echo json_encode(['success' => true, 'message' => $tipo . ' eliminata']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Errore durante l\'eliminazione']);
            }
            exit;
        
        // ============================================================================
        // Invia subito un broadcast programmato
        // ============================================================================
        case 'send_scheduled_now':
            $id = intval($_POST['id'] ?? 0);
            
            $result = $dbh->sendScheduledNow($id, $adminId);
            
            if ($result['success']) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Comunicazione inviata con successo',
                    'destinatari' => $result['destinatari']
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Errore durante l\'invio']);
            }
            exit;
        
        // ============================================================================
        // Aggiorna broadcast programmato
        // ============================================================================
        case 'update_scheduled':
            $id = intval($_POST['id'] ?? 0);
            $data = [
                'oggetto' => trim($_POST['oggetto'] ?? ''),
                'messaggio' => trim($_POST['messaggio'] ?? ''),
                'target_type' => $_POST['target_type'] ?? 'tutti',
                'target_filter' => !empty($_POST['target_filter']) ? $_POST['target_filter'] : null,
                'canale' => $_POST['canale'] ?? 'in_app',
                'scheduled_at' => !empty($_POST['scheduled_at']) ? $_POST['scheduled_at'] : null
            ];
            
            // Validazione
            if (empty($data['oggetto'])) {
                echo json_encode(['success' => false, 'message' => 'L\'oggetto è obbligatorio']);
                exit;
            }
            if (empty($data['messaggio'])) {
                echo json_encode(['success' => false, 'message' => 'Il messaggio è obbligatorio']);
                exit;
            }
            
            // Validazione data futura se programmata
            if (!empty($data['scheduled_at'])) {
                $scheduledTime = strtotime($data['scheduled_at']);
                if ($scheduledTime <= time()) {
                    echo json_encode(['success' => false, 'message' => 'La data di programmazione deve essere nel futuro']);
                    exit;
                }
            }
            
            if ($dbh->updateScheduledBroadcast($id, $data)) {
                echo json_encode(['success' => true, 'message' => 'Comunicazione aggiornata con successo']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Errore durante l\'aggiornamento']);
            }
            exit;
        
        // ============================================================================
        // Aggiorna bozza
        // ============================================================================
        case 'update_draft':
            $id = intval($_POST['id'] ?? 0);
            $data = [
                'oggetto' => trim($_POST['oggetto'] ?? ''),
                'messaggio' => trim($_POST['messaggio'] ?? ''),
                'target_type' => $_POST['target_type'] ?? 'tutti',
                'target_filter' => !empty($_POST['target_filter']) ? $_POST['target_filter'] : null,
                'canale' => $_POST['canale'] ?? 'in_app'
            ];
            
            if ($dbh->updateBroadcastDraft($id, $data)) {
                echo json_encode(['success' => true, 'message' => 'Bozza aggiornata']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Errore durante l\'aggiornamento']);
            }
            exit;
        
        // ============================================================================
        // Invia bozza
        // ============================================================================
        case 'send_draft':
            $id = intval($_POST['id'] ?? 0);
            
            $result = $dbh->sendBroadcastFromDraft($id, $adminId);
            
            if ($result['success']) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Broadcast inviato con successo',
                    'destinatari' => $result['destinatari']
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Errore durante l\'invio']);
            }
            exit;
            exit;
        
        // ============================================================================
        // Cerca utenti per messaggio diretto
        // ============================================================================
        case 'search_users':
            $query = trim($_REQUEST['query'] ?? '');
            if (strlen($query) < 2) {
                echo json_encode(['success' => true, 'users' => []]);
                exit;
            }
            
            $users = $dbh->searchUsersForMessage($query);
            echo json_encode(['success' => true, 'users' => $users]);
            exit;
        
        // ============================================================================
        // Ottieni lista corsi per filtro
        // ============================================================================
        case 'get_corsi':
            $corsi = $dbh->getCorsiLaurea();
            echo json_encode(['success' => true, 'corsi' => $corsi]);
            exit;
        
        // ============================================================================
        // Ottieni lista sport per filtro
        // ============================================================================
        case 'get_sport':
            $sport = $dbh->getAllSport();
            echo json_encode(['success' => true, 'sport' => $sport]);
            exit;
        
        // ============================================================================
        // Ottieni lista livelli per filtro
        // ============================================================================
        case 'get_livelli':
            $livelli = $dbh->getAllLivelli();
            echo json_encode(['success' => true, 'livelli' => $livelli]);
            exit;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Azione non valida']);
            exit;
    }
}

// ============================================================================
// CARICAMENTO DATI PER LA VISTA
// ============================================================================

// Processa automaticamente i broadcast programmati scaduti
$dbh->processScheduledBroadcasts();

// Statistiche per KPI
$templateParams['stats'] = $dbh->getBroadcastStats();

// Lista broadcast (tutti)
$filtri = [
    'stato' => $_GET['stato'] ?? '',
    'search' => trim($_GET['search'] ?? '')
];
$templateParams['filtri'] = $filtri;
$templateParams['broadcasts'] = $dbh->getAllBroadcasts($filtri);

// Dati per filtri destinatari
$templateParams['corsi'] = $dbh->getCorsiLaurea();
$templateParams['sport'] = $dbh->getAllSport();
$templateParams['livelli'] = $dbh->getAllLivelli();

// Target types per dropdown (solo quelli sensati)
$templateParams['target_types'] = [
    'tutti' => ['label' => 'Tutti gli Utenti', 'icon' => '👥', 'desc' => 'Invia a tutti gli utenti registrati'],
    'corso' => ['label' => 'Per Corso di Laurea', 'icon' => '🎓', 'desc' => 'Seleziona specifici corsi']
];

// Impostazioni pagina
$templateParams['titolo'] = 'Campus Sports - Comunicazioni';
$templateParams['titolo_pagina'] = 'Comunicazioni';
$templateParams['nome'] = 'comunicazioni.php';

// Carica template
require 'template/base.php';
?>