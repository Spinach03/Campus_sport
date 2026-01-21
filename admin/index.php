<?php
require_once '../bootstrap.php';

if(!isUserLoggedIn() || !isAdmin()){
    header("Location: ../login.php");
    exit;
}

// ============================================================================
// CARICAMENTO DATI DASHBOARD
// ============================================================================

// KPI - Prenotazioni
$prenotazioniOggi = $dbh->getPrenotazioniOggi();
$prenotazioniIeri = $dbh->getPrenotazioniIeri();
$prenotazioniSettimana = $dbh->getPrenotazioniSettimana();
$prenotazioniSettimanaScorsa = $dbh->getPrenotazioniSettimanaScorsa();

// KPI - Altri
$utilizzoCampi = $dbh->getUtilizzoCampi();
$utentiAttivi = $dbh->getUtentiAttivi();
$utentiAttiviPrecedente = $dbh->getUtentiAttiviMeseScorso();
$campiManutenzione = $dbh->getCampiManutenzione();
$recensioniTotali = $dbh->getRecensioniTotali();
$ratingMedio = $dbh->getRatingMedioGlobale();

// Alerts
$segnalazioniPending = $dbh->getSegnalazioniPending();
$campoRatingBasso = $dbh->getCampoRatingBasso();

// Calcolo variazioni percentuali
$variazioneOggi = $prenotazioniIeri > 0 ? round((($prenotazioniOggi - $prenotazioniIeri) / $prenotazioniIeri) * 100) : 0;
$variazioneSettimana = $prenotazioniSettimanaScorsa > 0 ? round((($prenotazioniSettimana - $prenotazioniSettimanaScorsa) / $prenotazioniSettimanaScorsa) * 100) : 0;
$variazioneUtenti = $utentiAttiviPrecedente > 0 ? round((($utentiAttivi - $utentiAttiviPrecedente) / $utentiAttiviPrecedente) * 100) : 0;

// Grafici
$trendPrenotazioni = $dbh->getTrendPrenotazioni(7);
$utilizzoLista = $dbh->getUtilizzoCampiLista();
$distribuzioneSport = $dbh->getDistribuzioneSport();

// Attività recenti
$attivitaRecenti = $dbh->getAttivitaRecenti(8);

// Passa i dati al template
$templateParams["titolo"] = "Campus Sports - Dashboard";
$templateParams["titolo_pagina"] = "Dashboard Overview";
$templateParams["nome"] = "dashboard.php";

// KPI Data
$templateParams["kpi"] = [
    "prenotazioni_oggi" => $prenotazioniOggi,
    "variazione_oggi" => $variazioneOggi,
    "prenotazioni_settimana" => $prenotazioniSettimana,
    "variazione_settimana" => $variazioneSettimana,
    "utilizzo_campi" => $utilizzoCampi,
    "utenti_attivi" => $utentiAttivi,
    "variazione_utenti" => $variazioneUtenti,
    "campi_manutenzione" => $campiManutenzione,
    "recensioni_totali" => $recensioniTotali,
    "rating_medio" => $ratingMedio
];

// Alerts
$templateParams["alerts"] = [
    "segnalazioni_pending" => $segnalazioniPending,
    "campo_rating_basso" => $campoRatingBasso
];

// Grafici
$templateParams["trend"] = $trendPrenotazioni;
$templateParams["utilizzo_lista"] = $utilizzoLista;
$templateParams["sport"] = $distribuzioneSport;

// Attività
$templateParams["attivita"] = $attivitaRecenti;

// Helper per tempo relativo
$templateParams["dbh"] = $dbh;

require 'template/base.php';
?>