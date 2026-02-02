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
        // AGGIORNA PROFILO
        // ====================================================================
        case 'aggiorna_profilo':
            $dati = [
                'telefono' => trim($_POST['telefono'] ?? ''),
                'indirizzo' => trim($_POST['indirizzo'] ?? '')
            ];
            
            $result = $dbh->aggiornaProfilo($userId, $dati);
            echo json_encode($result);
            exit;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Azione non valida']);
            exit;
    }
}

// ============================================================================
// CARICAMENTO DATI PAGINA
// ============================================================================

// Profilo completo
$profilo = $dbh->getProfiloCompleto($userId);

// Statistiche
$statistiche = $dbh->getStatisticheProfilo($userId);

// Badges
$badges = $dbh->getUserBadges($userId);

// Campo e Sport preferiti
$preferiti = $dbh->getPreferiti($userId);

// Corsi laurea per dropdown modifica
$corsiLaurea = $dbh->getCorsiLaurea();

// Prepara template
$templateParams["titolo"] = "Campus Sports - Il Mio Profilo";
$templateParams["titolo_pagina"] = "Il Mio Profilo";
$templateParams["nome"] = "profilo.php";
$templateParams["profilo"] = $profilo;
$templateParams["statistiche"] = $statistiche;
$templateParams["badges"] = $badges;
$templateParams["preferiti"] = $preferiti;
$templateParams["corsi_laurea"] = $corsiLaurea;

require 'template/base.php';
?>