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
        $userId = $_SESSION['user_id'];
        
        switch ($_POST['action']) {
            
            // Segna notifica come letta
            case 'segna_letta':
                $notificaId = intval($_POST['notifica_id'] ?? 0);
                
                if (!$notificaId) {
                    echo json_encode(['success' => false, 'error' => 'ID notifica non valido']);
                    exit;
                }
                
                $result = $dbh->segnaNotificaLetta($notificaId, $userId);
                echo json_encode(['success' => $result]);
                exit;
            
            // Segna tutte come lette
            case 'segna_tutte_lette':
                $result = $dbh->segnaTutteNotificheLette($userId);
                echo json_encode(['success' => $result]);
                exit;
            
            // Elimina notifica
            case 'elimina':
                $notificaId = intval($_POST['notifica_id'] ?? 0);
                
                if (!$notificaId) {
                    echo json_encode(['success' => false, 'error' => 'ID notifica non valido']);
                    exit;
                }
                
                $result = $dbh->eliminaNotifica($notificaId, $userId);
                echo json_encode(['success' => $result]);
                exit;
            
            // Elimina tutte le lette
            case 'elimina_lette':
                $result = $dbh->eliminaNotificheLette($userId);
                echo json_encode(['success' => $result]);
                exit;
            
            // Ottieni conteggio non lette (per badge header)
            case 'conta_non_lette':
                $count = $dbh->getNotificheNonLette($userId);
                echo json_encode(['success' => true, 'count' => $count]);
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

// Filtro corrente
$filtro = $_GET['filtro'] ?? 'tutte';

// Ottieni notifiche
$soloNonLette = ($filtro === 'non_lette');
$notifiche = $dbh->getNotificheUtente($userId, 100, $soloNonLette);

// Statistiche
$totaleNotifiche = count($notifiche);
$nonLette = $dbh->getNotificheNonLette($userId);

// Raggruppa per data
$notificheOggi = [];
$notificheIeri = [];
$notificheSettimana = [];
$notifichePrecedenti = [];

$oggi = date('Y-m-d');
$ieri = date('Y-m-d', strtotime('-1 day'));
$inizioSettimana = date('Y-m-d', strtotime('-7 days'));

foreach ($notifiche as $n) {
    $dataNotifica = date('Y-m-d', strtotime($n['created_at']));
    
    if ($dataNotifica === $oggi) {
        $notificheOggi[] = $n;
    } elseif ($dataNotifica === $ieri) {
        $notificheIeri[] = $n;
    } elseif ($dataNotifica >= $inizioSettimana) {
        $notificheSettimana[] = $n;
    } else {
        $notifichePrecedenti[] = $n;
    }
}

// Conteggio per tipo
$conteggioPerTipo = $dbh->contaNotifichePerTipo($userId);

// Prepara parametri template
$templateParams["titolo"] = "Notifiche";
$templateParams["titolo_pagina"] = "Notifiche";
$templateParams["nome"] = "notifiche.php";
$templateParams["notifiche_oggi"] = $notificheOggi;
$templateParams["notifiche_ieri"] = $notificheIeri;
$templateParams["notifiche_settimana"] = $notificheSettimana;
$templateParams["notifiche_precedenti"] = $notifichePrecedenti;
$templateParams["totale_notifiche"] = $totaleNotifiche;
$templateParams["non_lette"] = $nonLette;
$templateParams["filtro"] = $filtro;
$templateParams["conteggio_per_tipo"] = $conteggioPerTipo;
$templateParams["css_extra"] = ["css/notifiche.css"];

require 'template/base.php';
?>