<?php
require_once '../bootstrap.php';

if(!isUserLoggedIn() || isAdmin()){
    header("Location: ../login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// ============================================================================
// CARICAMENTO DATI DASHBOARD
// ============================================================================

// Dati utente
$profilo = $dbh->getProfiloCompleto($userId);

// Statistiche rapide
$stats = $dbh->getDashboardStats($userId);

// Prossime prenotazioni (max 5)
$prossimePrenotazioni = $dbh->getProssimePrenotazioni($userId, 5);

// Attività recenti
$attivitaRecenti = $dbh->getAttivitaUtente($userId, 6);

// Sport preferiti (per grafico)
$distribuzioneSport = $dbh->getDistribuzioneSportUtente($userId);

// Notifiche non lette
$notificheNonLette = $dbh->getNotificheNonLette($userId);

// ============================================================================
// PREPARA TEMPLATE
// ============================================================================

$templateParams["titolo"] = "Campus Sports - Dashboard";
$templateParams["titolo_pagina"] = "Dashboard";
$templateParams["nome"] = "dashboard.php";

// Dati per il template
$templateParams["profilo"] = $profilo;
$templateParams["stats"] = $stats;
$templateParams["prossime_prenotazioni"] = $prossimePrenotazioni;
$templateParams["attivita_recenti"] = $attivitaRecenti;
$templateParams["distribuzione_sport"] = $distribuzioneSport;
$templateParams["notifiche_non_lette"] = $notificheNonLette;

require 'template/base.php';
?>