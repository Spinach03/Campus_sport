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
        // SALVA REGOLE PRENOTAZIONE
        // ============================================
        case 'save_regole':
            $regole = [
                'giorni_anticipo_max' => intval($_POST['giorni_anticipo_max'] ?? 7),
                'ore_anticipo_cancellazione' => intval($_POST['ore_anticipo_cancellazione'] ?? 24)
            ];
            
            $success = true;
            foreach ($regole as $chiave => $valore) {
                if (!$dbh->saveConfig($chiave, $valore, 'int', $_SESSION['user_id'])) {
                    $success = false;
                }
            }
            
            echo json_encode([
                'success' => $success,
                'message' => $success ? 'Regole salvate con successo' : 'Errore nel salvataggio'
            ]);
            exit;
            
        // ============================================
        // OTTIENI TEMPLATE NOTIFICA
        // ============================================
        case 'get_template':
            $templateId = intval($_GET['id'] ?? 0);
            $template = $dbh->getNotificationTemplate($templateId);
            
            if ($template) {
                echo json_encode(['success' => true, 'template' => $template]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Template non trovato']);
            }
            exit;
            
        // ============================================
        // SALVA TEMPLATE NOTIFICA
        // ============================================
        case 'save_template':
            $templateId = intval($_POST['template_id'] ?? 0);
            $titolo = trim($_POST['titolo'] ?? '');
            $messaggio = trim($_POST['messaggio'] ?? '');
            $attivo = isset($_POST['attivo']) ? 1 : 0;
            
            if (!$titolo || !$messaggio) {
                echo json_encode(['success' => false, 'message' => 'Titolo e messaggio sono obbligatori']);
                exit;
            }
            
            $result = $dbh->updateNotificationTemplate($templateId, $titolo, $messaggio, $attivo, $_SESSION['user_id']);
            
            echo json_encode([
                'success' => $result,
                'message' => $result ? 'Template aggiornato con successo' : 'Errore nel salvataggio'
            ]);
            exit;
            
        // ============================================
        // SALVA ORE REMINDER
        // ============================================
        case 'save_ore_reminder':
            $oreReminder = intval($_POST['ore_reminder'] ?? 48);
            
            $success = $dbh->saveConfig('ore_reminder_prenotazione', $oreReminder, 'int', $_SESSION['user_id']);
            
            echo json_encode([
                'success' => $success,
                'message' => $success ? 'Ore reminder salvate con successo' : 'Errore nel salvataggio'
            ]);
            exit;
            
        // ============================================
        // AGGIUNGI GIORNO CHIUSURA
        // ============================================
        case 'add_chiusura':
            $data = $_POST['data'] ?? '';
            $motivo = trim($_POST['motivo'] ?? '');
            
            if (!$data) {
                echo json_encode(['success' => false, 'message' => 'Data obbligatoria']);
                exit;
            }
            
            $result = $dbh->addGiornoChiusura($data, $motivo, $_SESSION['user_id']);
            
            echo json_encode([
                'success' => $result,
                'message' => $result ? 'Giorno di chiusura aggiunto' : 'Errore o data già presente'
            ]);
            exit;
            
        // ============================================
        // RIMUOVI GIORNO CHIUSURA
        // ============================================
        case 'remove_chiusura':
            $chiusuraId = intval($_POST['id'] ?? 0);
            
            $result = $dbh->removeGiornoChiusura($chiusuraId);
            
            echo json_encode([
                'success' => $result,
                'message' => $result ? 'Giorno di chiusura rimosso' : 'Errore nella rimozione'
            ]);
            exit;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Azione non valida']);
            exit;
    }
}

// ============================================================================
// CARICAMENTO DATI PER LA VISTA
// ============================================================================

// Regole prenotazione
$templateParams['regole'] = [
    'giorni_anticipo_max' => $dbh->getConfig('giorni_anticipo_max', 7),
    'ore_anticipo_cancellazione' => $dbh->getConfig('ore_anticipo_cancellazione', 24)
];

// Template notifiche (solo i 3 per le prenotazioni)
$templateParams['templates'] = $dbh->getNotificationTemplatesPrenotazioni();

// Ore reminder
$templateParams['ore_reminder'] = $dbh->getConfig('ore_reminder_prenotazione', 48);

// Giorni chiusura
$templateParams['giorni_chiusura'] = $dbh->getGiorniChiusura();

// Impostazioni pagina
$templateParams['titolo'] = 'Configurazione Sistema';
$templateParams['titolo_pagina'] = 'Configurazione Sistema';
$templateParams['nome'] = 'configurazione.php';
$templateParams['css_extra'] = ['css/configurazione.css'];

// Carica template
require 'template/base.php';
?>