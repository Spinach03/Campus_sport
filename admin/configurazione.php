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