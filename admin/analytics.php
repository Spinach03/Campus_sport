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
    
    switch ($action) {
        // ============================================================================
        // Ottieni dati analytics per periodo
        // ============================================================================
        case 'get_analytics_data':
            $periodo = $_REQUEST['periodo'] ?? 'settimana';
            $dataInizio = $_REQUEST['data_inizio'] ?? null;
            $dataFine = $_REQUEST['data_fine'] ?? null;
            
            // Calcola date in base al periodo
            $dates = calcolaPeriodo($periodo, $dataInizio, $dataFine);
            
            $data = [
                'kpi' => $dbh->getAnalyticsKPI($dates['inizio'], $dates['fine'], $dates['inizio_prec'], $dates['fine_prec']),
                'trend' => $dbh->getAnalyticsTrend($dates['inizio'], $dates['fine']),
                'heatmap' => $dbh->getAnalyticsHeatmap($dates['inizio'], $dates['fine']),
                'utilizzo_campi' => $dbh->getAnalyticsUtilizzoCampi($dates['inizio'], $dates['fine']),
                'distribuzione_sport' => $dbh->getAnalyticsDistribuzioneSport($dates['inizio'], $dates['fine']),
                'periodo' => [
                    'inizio' => $dates['inizio'],
                    'fine' => $dates['fine'],
                    'label' => $dates['label']
                ]
            ];
            
            echo json_encode(['success' => true, 'data' => $data]);
            exit;
        
        // ============================================================================
        // Export CSV prenotazioni
        // ============================================================================
        case 'export_csv':
            $periodo = $_REQUEST['periodo'] ?? 'settimana';
            $dataInizio = $_REQUEST['data_inizio'] ?? null;
            $dataFine = $_REQUEST['data_fine'] ?? null;
            
            $dates = calcolaPeriodo($periodo, $dataInizio, $dataFine);
            $prenotazioni = $dbh->getPrenotazioniExport($dates['inizio'], $dates['fine']);
            
            // Genera CSV
            $csv = "ID,Data,Ora Inizio,Ora Fine,Campo,Sport,Utente,Email,Stato,Check-in\n";
            foreach ($prenotazioni as $p) {
                $csv .= sprintf(
                    "%d,%s,%s,%s,\"%s\",\"%s\",\"%s\",%s,%s,%s\n",
                    $p['prenotazione_id'],
                    $p['data_prenotazione'],
                    $p['ora_inizio'],
                    $p['ora_fine'],
                    str_replace('"', '""', $p['campo_nome']),
                    str_replace('"', '""', $p['sport_nome']),
                    str_replace('"', '""', $p['utente_nome']),
                    $p['email'],
                    $p['stato'],
                    $p['check_in_effettuato'] ? 'Sì' : 'No'
                );
            }
            
            echo json_encode(['success' => true, 'csv' => $csv, 'filename' => 'prenotazioni_' . date('Y-m-d') . '.csv']);
            exit;
        
        // ============================================================================
        // Export CSV utilizzo campi
        // ============================================================================
        case 'export_campi_csv':
            $periodo = $_REQUEST['periodo'] ?? 'settimana';
            $dataInizio = $_REQUEST['data_inizio'] ?? null;
            $dataFine = $_REQUEST['data_fine'] ?? null;
            
            $dates = calcolaPeriodo($periodo, $dataInizio, $dataFine);
            $campi = $dbh->getAnalyticsUtilizzoCampi($dates['inizio'], $dates['fine']);
            
            $csv = "Campo,Sport,Prenotazioni,Ore Utilizzate,Utilizzo %\n";
            foreach ($campi as $c) {
                $csv .= sprintf(
                    "\"%s\",\"%s\",%d,%d,%.1f%%\n",
                    str_replace('"', '""', $c['nome']),
                    str_replace('"', '""', $c['sport']),
                    $c['prenotazioni'],
                    $c['ore_utilizzate'],
                    $c['percentuale']
                );
            }
            
            echo json_encode(['success' => true, 'csv' => $csv, 'filename' => 'utilizzo_campi_' . date('Y-m-d') . '.csv']);
            exit;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Azione non valida']);
            exit;
    }
}

// ============================================================================
// HELPER - Calcola date periodo
// ============================================================================
function calcolaPeriodo($periodo, $dataInizio = null, $dataFine = null) {
    $oggi = new DateTime();
    $inizio = new DateTime();
    $fine = new DateTime();
    $inizioPrec = new DateTime();
    $finePrec = new DateTime();
    $label = '';
    
    switch ($periodo) {
        case 'oggi':
            $inizio = clone $oggi;
            $fine = clone $oggi;
            $inizioPrec = (clone $oggi)->modify('-1 day');
            $finePrec = (clone $oggi)->modify('-1 day');
            $label = 'Oggi';
            break;
            
        case 'settimana':
            $inizio = (clone $oggi)->modify('monday this week');
            $fine = clone $oggi;
            $inizioPrec = (clone $inizio)->modify('-1 week');
            $finePrec = (clone $fine)->modify('-1 week');
            $label = 'Questa settimana';
            break;
            
        case 'mese':
            $inizio = (clone $oggi)->modify('first day of this month');
            $fine = clone $oggi;
            $inizioPrec = (clone $inizio)->modify('-1 month');
            $finePrec = (clone $fine)->modify('-1 month');
            $label = 'Questo mese';
            break;
            
        case 'trimestre':
            $inizio = (clone $oggi)->modify('-90 days');
            $fine = clone $oggi;
            $inizioPrec = (clone $inizio)->modify('-90 days');
            $finePrec = (clone $fine)->modify('-90 days');
            $label = 'Ultimi 3 mesi';
            break;
            
        case 'anno':
            $inizio = (clone $oggi)->modify('first day of january this year');
            $fine = clone $oggi;
            $inizioPrec = (clone $inizio)->modify('-1 year');
            $finePrec = (clone $fine)->modify('-1 year');
            $label = 'Quest\'anno';
            break;
            
        case 'custom':
            if ($dataInizio && $dataFine) {
                $inizio = new DateTime($dataInizio);
                $fine = new DateTime($dataFine);
                $diff = $inizio->diff($fine);
                $giorni = $diff->days;
                $inizioPrec = (clone $inizio)->modify("-{$giorni} days");
                $finePrec = (clone $fine)->modify("-{$giorni} days");
                $label = $inizio->format('d/m/Y') . ' - ' . $fine->format('d/m/Y');
            }
            break;
            
        default:
            $inizio = (clone $oggi)->modify('monday this week');
            $fine = clone $oggi;
            $inizioPrec = (clone $inizio)->modify('-1 week');
            $finePrec = (clone $fine)->modify('-1 week');
            $label = 'Questa settimana';
    }
    
    return [
        'inizio' => $inizio->format('Y-m-d'),
        'fine' => $fine->format('Y-m-d'),
        'inizio_prec' => $inizioPrec->format('Y-m-d'),
        'fine_prec' => $finePrec->format('Y-m-d'),
        'label' => $label
    ];
}

// ============================================================================
// CARICAMENTO DATI PER LA VISTA
// ============================================================================

// Periodo di default: settimana corrente
$periodoDefault = 'settimana';
$dates = calcolaPeriodo($periodoDefault);

// Dati iniziali per la vista
$templateParams['periodo_attivo'] = $periodoDefault;
$templateParams['data_inizio'] = $dates['inizio'];
$templateParams['data_fine'] = $dates['fine'];

// KPI iniziali
$templateParams['kpi'] = $dbh->getAnalyticsKPI($dates['inizio'], $dates['fine'], $dates['inizio_prec'], $dates['fine_prec']);

// Lista sport e campi per eventuali filtri
$templateParams['sport'] = $dbh->getAllSport();
$templateParams['campi'] = $dbh->getAllCampi();

// Impostazioni pagina
$templateParams['titolo'] = 'Campus Sports - Analytics';
$templateParams['titolo_pagina'] = 'Analytics';
$templateParams['nome'] = 'analytics.php';
$templateParams['css_extra'] = ['css/analytics.css'];

// Carica template
require 'template/base.php';
?>