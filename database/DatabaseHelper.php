<?php
class DatabaseHelper {
    private $db;

    public function __construct($servername, $username, $password, $dbname, $port) {
        $this->db = new mysqli($servername, $username, $password, $dbname, $port);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
        $this->db->set_charset("utf8mb4");
    }

    // ============================================================================
    // AUTH - Login
    // ============================================================================
    
    public function checkLogin($email, $password){
        $query = "SELECT user_id, email, nome, cognome, ruolo, stato FROM users WHERE stato = 'attivo' AND email = ? AND password_hash = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ss', $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // ============================================================================
    // DASHBOARD - KPI Stats
    // ============================================================================
    
    public function getPrenotazioniOggi() {
        $query = "SELECT COUNT(*) as totale FROM prenotazioni";
        $result = $this->db->query($query);
        return $result->fetch_assoc()['totale'] ?? 0;
    }
    
    public function getPrenotazioniIeri() {
        $query = "SELECT COUNT(*) as totale FROM prenotazioni WHERE stato = 'completata'";
        $result = $this->db->query($query);
        return $result->fetch_assoc()['totale'] ?? 0;
    }
    
    public function getPrenotazioniSettimana() {
        $query = "SELECT COUNT(*) as totale FROM prenotazioni WHERE stato IN ('confermata', 'completata')";
        $result = $this->db->query($query);
        return $result->fetch_assoc()['totale'] ?? 0;
    }
    
    public function getPrenotazioniSettimanaScorsa() {
        $query = "SELECT COUNT(*) as totale FROM prenotazioni WHERE stato = 'cancellata'";
        $result = $this->db->query($query);
        return $result->fetch_assoc()['totale'] ?? 0;
    }
    
    public function getUtilizzoCampi() {
        $query = "SELECT 
            (SELECT COUNT(*) FROM prenotazioni WHERE stato IN ('completata', 'confermata')) as prenotazioni,
            (SELECT COUNT(*) FROM campi_sportivi WHERE stato != 'chiuso') as campi_attivi";
        $result = $this->db->query($query);
        $row = $result->fetch_assoc();
        $prenotazioni = $row['prenotazioni'] ?? 0;
        $campi = $row['campi_attivi'] ?? 1;
        $maxSlot = 100 * $campi;
        return $maxSlot > 0 ? round(($prenotazioni / $maxSlot) * 100) : 0;
    }
    
    public function getUtentiAttivi() {
        $query = "SELECT COUNT(DISTINCT user_id) as totale FROM prenotazioni";
        $result = $this->db->query($query);
        return $result->fetch_assoc()['totale'] ?? 0;
    }
    
    public function getUtentiAttiviMeseScorso() {
        $query = "SELECT COUNT(*) as totale FROM utenti_standard";
        $result = $this->db->query($query);
        return $result->fetch_assoc()['totale'] ?? 0;
    }
    
    public function getCampiManutenzione() {
        $query = "SELECT COUNT(*) as totale FROM campi_sportivi WHERE stato = 'manutenzione'";
        $result = $this->db->query($query);
        return $result->fetch_assoc()['totale'] ?? 0;
    }
    
    public function getRecensioniTotali() {
        $query = "SELECT COUNT(*) as totale FROM recensioni";
        $result = $this->db->query($query);
        return $result->fetch_assoc()['totale'] ?? 0;
    }
    
    public function getRatingMedioGlobale() {
        $query = "SELECT ROUND(AVG(rating_generale), 1) as media FROM recensioni";
        $result = $this->db->query($query);
        return $result->fetch_assoc()['media'] ?? 0;
    }
    
    // ============================================================================
    // DASHBOARD - Alerts
    // ============================================================================
    
    public function getSegnalazioniPending() {
        $query = "SELECT COUNT(*) as totale FROM segnalazioni WHERE stato = 'pending'";
        $result = $this->db->query($query);
        return $result->fetch_assoc()['totale'] ?? 0;
    }
    
    public function getCampoRatingBasso() {
        $query = "SELECT c.campo_id, c.nome, ROUND(AVG(r.rating_generale), 1) as rating_medio
                  FROM campi_sportivi c
                  JOIN recensioni r ON c.campo_id = r.campo_id
                  GROUP BY c.campo_id
                  HAVING rating_medio < 4
                  ORDER BY rating_medio ASC
                  LIMIT 1";
        $result = $this->db->query($query);
        return $result->fetch_assoc();
    }
    
    public function getNotificheNonLette($userId) {
        $query = "SELECT COUNT(*) as totale FROM notifiche WHERE user_id = ? AND letta = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['totale'] ?? 0;
    }
    
    // ============================================================================
    // DASHBOARD - Charts
    // ============================================================================
    
    public function getTrendPrenotazioni($giorni = 7) {
        $query = "SELECT DAYOFWEEK(data_prenotazione) as giorno_settimana, COUNT(*) as totale
                  FROM prenotazioni
                  GROUP BY DAYOFWEEK(data_prenotazione)
                  ORDER BY giorno_settimana ASC";
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getUtilizzoCampiLista() {
        $query = "SELECT c.campo_id, c.nome, s.nome as sport,
                    COUNT(CASE WHEN p.stato IN ('confermata', 'completata') THEN 1 END) as prenotazioni
                  FROM campi_sportivi c
                  JOIN sport s ON c.sport_id = s.sport_id
                  LEFT JOIN prenotazioni p ON c.campo_id = p.campo_id
                  WHERE c.stato != 'chiuso'
                  GROUP BY c.campo_id, c.nome, s.nome
                  ORDER BY prenotazioni DESC";
        $result = $this->db->query($query);
        $data = $result->fetch_all(MYSQLI_ASSOC);
        
        $maxPren = 0;
        foreach ($data as $row) {
            if (intval($row['prenotazioni']) > $maxPren) {
                $maxPren = intval($row['prenotazioni']);
            }
        }
        
        foreach ($data as &$row) {
            $numPren = intval($row['prenotazioni']);
            $row['percentuale'] = $maxPren > 0 ? round(($numPren / $maxPren) * 100) : 0;
            if ($numPren > 0 && $row['percentuale'] < 5) {
                $row['percentuale'] = 5;
            }
        }
        
        return $data;
    }
    
    public function getDistribuzioneSport() {
        $query = "SELECT s.nome as sport, s.icona, COUNT(p.prenotazione_id) as prenotazioni, COUNT(p.prenotazione_id) as ore
                  FROM sport s
                  JOIN campi_sportivi c ON s.sport_id = c.sport_id
                  LEFT JOIN prenotazioni p ON c.campo_id = p.campo_id
                  GROUP BY s.sport_id
                  ORDER BY prenotazioni DESC";
        $result = $this->db->query($query);
        $data = $result->fetch_all(MYSQLI_ASSOC);
        
        $totale = array_sum(array_column($data, 'prenotazioni'));
        foreach ($data as &$row) {
            $row['percentuale'] = $totale > 0 ? round(($row['prenotazioni'] / $totale) * 100) : 0;
        }
        
        return $data;
    }
    
    public function getAttivitaRecenti($limit = 5) {
        $query = "(SELECT 'booking' as tipo, CONCAT(u.nome, ' ', u.cognome) as utente,
                    CONCAT(UPPER(LEFT(u.nome, 1)), UPPER(LEFT(u.cognome, 1))) as avatar,
                    'Nuova prenotazione' as azione, c.nome as dettaglio, p.created_at as data
                  FROM prenotazioni p
                  JOIN users u ON p.user_id = u.user_id
                  JOIN campi_sportivi c ON p.campo_id = c.campo_id
                  ORDER BY p.created_at DESC LIMIT 10)
                  UNION ALL
                  (SELECT 'review' as tipo, CONCAT(u.nome, ' ', u.cognome) as utente,
                    CONCAT(UPPER(LEFT(u.nome, 1)), UPPER(LEFT(u.cognome, 1))) as avatar,
                    CONCAT('Recensione ', r.rating_generale, '★') as azione, c.nome as dettaglio, r.created_at as data
                  FROM recensioni r
                  JOIN users u ON r.user_id = u.user_id
                  JOIN campi_sportivi c ON r.campo_id = c.campo_id
                  ORDER BY r.created_at DESC LIMIT 10)
                  UNION ALL
                  (SELECT 'report' as tipo, CONCAT(u.nome, ' ', u.cognome) as utente,
                    CONCAT(UPPER(LEFT(u.nome, 1)), UPPER(LEFT(u.cognome, 1))) as avatar,
                    'Nuova segnalazione' as azione, s.tipo as dettaglio, s.created_at as data
                  FROM segnalazioni s
                  JOIN users u ON s.user_segnalante_id = u.user_id
                  ORDER BY s.created_at DESC LIMIT 10)
                  ORDER BY data DESC LIMIT ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // HELPER - Tempo relativo
    // ============================================================================
    
    public function tempoRelativo($datetime) {
        $now = new DateTime();
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
        
        if ($diff->y > 0) return $diff->y . ' ann' . ($diff->y > 1 ? 'i' : 'o') . ' fa';
        if ($diff->m > 0) return $diff->m . ' mes' . ($diff->m > 1 ? 'i' : 'e') . ' fa';
        if ($diff->d > 0) return $diff->d . ' giorn' . ($diff->d > 1 ? 'i' : 'o') . ' fa';
        if ($diff->h > 0) return $diff->h . ' or' . ($diff->h > 1 ? 'e' : 'a') . ' fa';
        if ($diff->i > 0) return $diff->i . ' min fa';
        return 'ora';
    }

    // ============================================================================
    // ============================================================================
    // GESTIONE CAMPI - CRUD E STATISTICHE
    // ============================================================================
    // ============================================================================
    
    // ============================================================================
    // CAMPI - Lista completa con tutti i dati
    // ============================================================================
    
    public function getAllCampi($filtri = []) {
        $query = "SELECT 
                    c.campo_id, c.nome, c.sport_id, s.nome as sport_nome, c.location, c.descrizione,
                    c.capienza_max, c.tipo_superficie, c.tipo_campo, c.lunghezza_m, c.larghezza_m,
                    c.orario_apertura, c.orario_chiusura, c.stato, c.rating_medio, c.num_recensioni, c.created_at,
                    (SELECT COUNT(*) FROM prenotazioni p WHERE p.campo_id = c.campo_id AND p.data_prenotazione = CURDATE() AND p.stato IN ('confermata', 'completata')) as prenotazioni_oggi,
                    (SELECT COUNT(*) FROM prenotazioni p WHERE p.campo_id = c.campo_id AND p.stato IN ('confermata', 'completata')) as prenotazioni_settimana,
                    (SELECT path_foto FROM campo_foto WHERE campo_id = c.campo_id AND is_principale = 1 LIMIT 1) as foto_principale,
                    (SELECT COUNT(*) FROM blocchi_manutenzione bm WHERE bm.campo_id = c.campo_id AND bm.data_inizio > CURDATE()) as manutenzioni_future
                  FROM campi_sportivi c
                  JOIN sport s ON c.sport_id = s.sport_id
                  WHERE 1=1";
        
        $params = [];
        $types = '';
        
        if (!empty($filtri['sport'])) {
            $query .= " AND s.nome = ?";
            $params[] = $filtri['sport'];
            $types .= 's';
        }
        
        if (!empty($filtri['stato'])) {
            $query .= " AND c.stato = ?";
            $params[] = $filtri['stato'];
            $types .= 's';
        }
        
        if (!empty($filtri['tipo'])) {
            $query .= " AND c.tipo_campo = ?";
            $params[] = $filtri['tipo'];
            $types .= 's';
        }
        
        if (!empty($filtri['search'])) {
            $query .= " AND c.nome LIKE ?";
            $params[] = '%' . $filtri['search'] . '%';
            $types .= 's';
        }
        
        $orderBy = " ORDER BY ";
        switch ($filtri['ordina'] ?? 'nome') {
            case 'rating': $orderBy .= "c.rating_medio DESC"; break;
            case 'utilizzo': $orderBy .= "prenotazioni_settimana DESC"; break;
            case 'prenotazioni': $orderBy .= "prenotazioni_oggi DESC"; break;
            default: $orderBy .= "c.nome ASC";
        }
        $query .= $orderBy;
        
        if (!empty($params)) {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->db->query($query);
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // CAMPI - Dettaglio singolo campo
    // ============================================================================
    
    public function getCampoById($campoId) {
        $query = "SELECT c.*, s.nome as sport_nome,
                    (SELECT COUNT(*) FROM prenotazioni p WHERE p.campo_id = c.campo_id AND p.data_prenotazione = CURDATE() AND p.stato IN ('confermata', 'completata')) as prenotazioni_oggi,
                    (SELECT COUNT(*) FROM prenotazioni p WHERE p.campo_id = c.campo_id AND p.stato IN ('confermata', 'completata')) as prenotazioni_settimana,
                    (SELECT COUNT(*) FROM prenotazioni p WHERE p.campo_id = c.campo_id AND p.stato IN ('confermata', 'completata')) as prenotazioni_totali
                  FROM campi_sportivi c
                  JOIN sport s ON c.sport_id = s.sport_id
                  WHERE c.campo_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $campoId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    // ============================================================================
    // CAMPI - Servizi di un campo
    // ============================================================================
    
    public function getCampoServizi($campoId) {
        $query = "SELECT * FROM campo_servizi WHERE campo_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $campoId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    // ============================================================================
    // CAMPI - Foto di un campo
    // ============================================================================
    
    public function getCampoFoto($campoId) {
        $query = "SELECT * FROM campo_foto WHERE campo_id = ? ORDER BY ordine ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $campoId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // CAMPI - Statistiche KPI per Gestione Campi
    // ============================================================================
    
    public function getCampiStats() {
        $query = "SELECT COUNT(*) as totale,
                    SUM(CASE WHEN stato = 'disponibile' THEN 1 ELSE 0 END) as disponibili,
                    SUM(CASE WHEN stato = 'manutenzione' THEN 1 ELSE 0 END) as manutenzione,
                    SUM(CASE WHEN stato = 'chiuso' THEN 1 ELSE 0 END) as chiusi
                  FROM campi_sportivi";
        $result = $this->db->query($query);
        $stats = $result->fetch_assoc();
        
        $query2 = "SELECT COUNT(*) as totale FROM prenotazioni WHERE data_prenotazione = CURDATE() AND stato IN ('confermata', 'completata')";
        $result2 = $this->db->query($query2);
        $stats['prenotazioni_oggi'] = $result2->fetch_assoc()['totale'] ?? 0;
        
        $query3 = "SELECT ROUND(AVG(c.rating_medio), 0) as utilizzo FROM campi_sportivi c WHERE c.stato = 'disponibile'";
        $result3 = $this->db->query($query3);
        $stats['utilizzo_medio'] = $result3->fetch_assoc()['utilizzo'] ?? 0;
        
        return $stats;
    }
    
    // ============================================================================
    // CAMPI - Lista Sport (per dropdown)
    // ============================================================================
    
    public function getAllSport() {
        $query = "SELECT sport_id, nome, icona FROM sport WHERE attivo = 1 ORDER BY nome ASC";
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // CAMPI - Creazione nuovo campo
    // ============================================================================
    
    public function createCampo($data) {
        $query = "INSERT INTO campi_sportivi 
                  (nome, sport_id, location, descrizione, capienza_max, tipo_superficie, tipo_campo, 
                   lunghezza_m, larghezza_m, orario_apertura, orario_chiusura, stato, created_by) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('sissisddssssi',
            $data['nome'], $data['sport_id'], $data['location'], $data['descrizione'],
            $data['capienza_max'], $data['tipo_superficie'], $data['tipo_campo'],
            $data['lunghezza_m'], $data['larghezza_m'], $data['orario_apertura'],
            $data['orario_chiusura'], $data['stato'], $data['created_by']
        );
        
        if ($stmt->execute()) {
            $campoId = $this->db->insert_id;
            $this->insertCampoServizi($campoId, $data['servizi'] ?? []);
            return $campoId;
        }
        return false;
    }
    
    // ============================================================================
    // CAMPI - Inserimento servizi campo
    // ============================================================================
    
    public function insertCampoServizi($campoId, $servizi) {
        $deleteQuery = "DELETE FROM campo_servizi WHERE campo_id = ?";
        $stmt = $this->db->prepare($deleteQuery);
        $stmt->bind_param('i', $campoId);
        $stmt->execute();
        
        $query = "INSERT INTO campo_servizi 
                  (campo_id, illuminazione_notturna, spogliatoi, docce, parcheggio, distributori, noleggio_attrezzatura, bar_ristoro) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        
        $illuminazione = in_array('illuminazione_notturna', $servizi) ? 1 : 0;
        $spogliatoi = in_array('spogliatoi', $servizi) ? 1 : 0;
        $docce = in_array('docce', $servizi) ? 1 : 0;
        $parcheggio = in_array('parcheggio', $servizi) ? 1 : 0;
        $distributori = in_array('distributori', $servizi) ? 1 : 0;
        $noleggio = in_array('noleggio_attrezzatura', $servizi) ? 1 : 0;
        $bar = in_array('bar_ristoro', $servizi) ? 1 : 0;
        
        $stmt->bind_param('iiiiiiii', $campoId, $illuminazione, $spogliatoi, $docce, $parcheggio, $distributori, $noleggio, $bar);
        return $stmt->execute();
    }
    
    // ============================================================================
    // CAMPI - Aggiornamento campo esistente
    // ============================================================================
    
    public function updateCampo($campoId, $data, $adminId) {
        $campoOld = $this->getCampoById($campoId);
        
        $query = "UPDATE campi_sportivi SET 
                    nome = ?, sport_id = ?, location = ?, descrizione = ?, capienza_max = ?,
                    tipo_superficie = ?, tipo_campo = ?, lunghezza_m = ?, larghezza_m = ?,
                    orario_apertura = ?, orario_chiusura = ?, stato = ?
                  WHERE campo_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('sissisddssssi',
            $data['nome'], $data['sport_id'], $data['location'], $data['descrizione'],
            $data['capienza_max'], $data['tipo_superficie'], $data['tipo_campo'],
            $data['lunghezza_m'], $data['larghezza_m'], $data['orario_apertura'],
            $data['orario_chiusura'], $data['stato'], $campoId
        );
        
        if ($stmt->execute()) {
            $this->insertCampoServizi($campoId, $data['servizi'] ?? []);
            $this->logCampoModifica($campoId, $adminId, $campoOld, $data);
            return true;
        }
        return false;
    }
    
    // ============================================================================
    // CAMPI - Log modifiche campo
    // ============================================================================
    
    public function logCampoModifica($campoId, $adminId, $oldData, $newData) {
        $campiDaControllare = ['nome', 'location', 'descrizione', 'capienza_max', 'tipo_superficie', 
                               'tipo_campo', 'orario_apertura', 'orario_chiusura', 'stato'];
        
        foreach ($campiDaControllare as $campo) {
            $oldValue = $oldData[$campo] ?? '';
            $newValue = $newData[$campo] ?? '';
            
            if ($oldValue != $newValue) {
                $query = "INSERT INTO campo_storico_modifiche (campo_id, admin_id, campo_modificato, valore_precedente, valore_nuovo) 
                          VALUES (?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param('iisss', $campoId, $adminId, $campo, $oldValue, $newValue);
                $stmt->execute();
            }
        }
    }
    
    // ============================================================================
    // CAMPI - Storico modifiche campo
    // ============================================================================
    
    public function getCampoStorico($campoId) {
        $query = "SELECT csm.*, CONCAT(u.nome, ' ', u.cognome) as admin_nome
                  FROM campo_storico_modifiche csm
                  JOIN users u ON csm.admin_id = u.user_id
                  WHERE csm.campo_id = ?
                  ORDER BY csm.created_at DESC LIMIT 20";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $campoId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // CAMPI - Cambio stato rapido
    // ============================================================================
    
    public function updateCampoStato($campoId, $nuovoStato, $adminId) {
        $campoOld = $this->getCampoById($campoId);
        
        $query = "UPDATE campi_sportivi SET stato = ? WHERE campo_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('si', $nuovoStato, $campoId);
        
        if ($stmt->execute()) {
            $this->logCampoModifica($campoId, $adminId, $campoOld, ['stato' => $nuovoStato]);
            return true;
        }
        return false;
    }
    
    // Versione semplificata senza admin logging
    public function updateStatoCampo($campoId, $nuovoStato) {
        $query = "UPDATE campi_sportivi SET stato = ? WHERE campo_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('si', $nuovoStato, $campoId);
        return $stmt->execute();
    }
    
    // ============================================================================
    // CAMPI - Eliminazione campo
    // ============================================================================
    
    public function deleteCampo($campoId) {
        $query = "DELETE FROM campi_sportivi WHERE campo_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $campoId);
        return $stmt->execute();
    }
    
    // ============================================================================
    // CAMPI - Conta prenotazioni future di un campo
    // ============================================================================
    
    public function countPrenotazioniFutureCampo($campoId) {
        $query = "SELECT COUNT(*) as totale FROM prenotazioni 
                  WHERE campo_id = ? AND data_prenotazione >= CURDATE() AND stato = 'confermata'";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $campoId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['totale'] ?? 0;
    }
    
    // ============================================================================
    // BLOCCHI MANUTENZIONE - Creazione
    // ============================================================================
    
    public function createBloccoManutenzione($data) {
        $query = "INSERT INTO blocchi_manutenzione 
                  (campo_id, data_inizio, ora_inizio, data_fine, ora_fine, tipo_blocco, motivo, created_by) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('issssssi',
            $data['campo_id'], $data['data_inizio'], $data['ora_inizio'],
            $data['data_fine'], $data['ora_fine'], $data['tipo_blocco'],
            $data['motivo'], $data['created_by']
        );
        
        if ($stmt->execute()) {
            $this->updateCampoStato($data['campo_id'], 'manutenzione', $data['created_by']);
            return $this->db->insert_id;
        }
        return false;
    }
    
    // ============================================================================
    // BLOCCHI MANUTENZIONE - Lista blocchi attivi
    // ============================================================================
    
    public function getBlocchiManutenzione($campoId = null) {
        $query = "SELECT bm.*, c.nome as campo_nome, CONCAT(u.nome, ' ', u.cognome) as admin_nome
                  FROM blocchi_manutenzione bm
                  JOIN campi_sportivi c ON bm.campo_id = c.campo_id
                  JOIN users u ON bm.created_by = u.user_id
                  WHERE bm.data_fine >= CURDATE()";
        
        if ($campoId) {
            $query .= " AND bm.campo_id = ? ORDER BY bm.data_inizio ASC";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $campoId);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $query .= " ORDER BY bm.data_inizio ASC";
            $result = $this->db->query($query);
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // BLOCCHI MANUTENZIONE - Rimozione blocco
    // ============================================================================
    
    public function deleteBloccoManutenzione($bloccoId, $adminId) {
        $query = "SELECT campo_id FROM blocchi_manutenzione WHERE blocco_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $bloccoId);
        $stmt->execute();
        $result = $stmt->get_result();
        $blocco = $result->fetch_assoc();
        
        if ($blocco) {
            $deleteQuery = "DELETE FROM blocchi_manutenzione WHERE blocco_id = ?";
            $stmt2 = $this->db->prepare($deleteQuery);
            $stmt2->bind_param('i', $bloccoId);
            
            if ($stmt2->execute()) {
                $checkQuery = "SELECT COUNT(*) as totale FROM blocchi_manutenzione WHERE campo_id = ? AND data_fine >= CURDATE()";
                $stmt3 = $this->db->prepare($checkQuery);
                $stmt3->bind_param('i', $blocco['campo_id']);
                $stmt3->execute();
                $check = $stmt3->get_result()->fetch_assoc();
                
                if ($check['totale'] == 0) {
                    $this->updateCampoStato($blocco['campo_id'], 'disponibile', $adminId);
                }
                return true;
            }
        }
        return false;
    }
    
    // ============================================================================
    // PRENOTAZIONI - Lista prenotazioni di un campo (per calendario)
    // ============================================================================
    
    public function getPrenotazioniCampo($campoId, $dataInizio = null, $dataFine = null) {
        $query = "SELECT p.*, CONCAT(u.nome, ' ', u.cognome) as utente_nome, u.email as utente_email, u.telefono as utente_telefono
                  FROM prenotazioni p
                  JOIN users u ON p.user_id = u.user_id
                  WHERE p.campo_id = ?";
        
        $params = [$campoId];
        $types = 'i';
        
        if ($dataInizio) {
            $query .= " AND p.data_prenotazione >= ?";
            $params[] = $dataInizio;
            $types .= 's';
        }
        
        if ($dataFine) {
            $query .= " AND p.data_prenotazione <= ?";
            $params[] = $dataFine;
            $types .= 's';
        }
        
        $query .= " ORDER BY p.data_prenotazione ASC, p.ora_inizio ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // PRENOTAZIONI - Prenotazioni di oggi (per widget)
    // ============================================================================
    
    public function getPrenotazioniOggiAll() {
        $query = "SELECT p.*, c.nome as campo_nome, s.nome as sport_nome, CONCAT(u.nome, ' ', u.cognome) as utente_nome
                  FROM prenotazioni p
                  JOIN campi_sportivi c ON p.campo_id = c.campo_id
                  JOIN sport s ON c.sport_id = s.sport_id
                  JOIN users u ON p.user_id = u.user_id
                  WHERE p.data_prenotazione = CURDATE() AND p.stato IN ('confermata', 'completata')
                  ORDER BY p.ora_inizio ASC";
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // PRENOTAZIONI - Dettaglio singola prenotazione
    // ============================================================================
    
    public function getPrenotazioneById($prenotazioneId) {
        $query = "SELECT p.*, c.nome as campo_nome, s.nome as sport_nome,
                    CONCAT(u.nome, ' ', u.cognome) as utente_nome, u.email as utente_email, u.telefono as utente_telefono,
                    us.penalty_points,
                    (SELECT COUNT(*) FROM prenotazioni WHERE user_id = p.user_id) as totale_prenotazioni,
                    (SELECT COUNT(*) FROM prenotazioni WHERE user_id = p.user_id AND stato = 'no_show') as totale_noshow
                  FROM prenotazioni p
                  JOIN campi_sportivi c ON p.campo_id = c.campo_id
                  JOIN sport s ON c.sport_id = s.sport_id
                  JOIN users u ON p.user_id = u.user_id
                  LEFT JOIN utenti_standard us ON p.user_id = us.user_id
                  WHERE p.prenotazione_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $prenotazioneId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    // ============================================================================
    // PRENOTAZIONI - Aggiorna stato prenotazione
    // ============================================================================
    
    public function updatePrenotazioneStato($prenotazioneId, $nuovoStato, $motivo = null) {
        $query = "UPDATE prenotazioni SET stato = ?";
        $params = [$nuovoStato];
        $types = 's';
        
        if ($nuovoStato == 'cancellata') {
            $query .= ", motivo_cancellazione = ?, cancelled_at = NOW()";
            $params[] = $motivo;
            $types .= 's';
        }
        
        $query .= " WHERE prenotazione_id = ?";
        $params[] = $prenotazioneId;
        $types .= 'i';
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$params);
        return $stmt->execute();
    }
    
    // ============================================================================
    // RECENSIONI - Lista recensioni di un campo
    // ============================================================================
    
    public function getRecensioniCampo($campoId, $limit = null) {
        $query = "SELECT r.*, CONCAT(u.nome, ' ', u.cognome) as utente_nome, UPPER(LEFT(u.nome, 1)) as utente_iniziale,
                    (SELECT COUNT(*) FROM recensione_risposte WHERE recensione_id = r.recensione_id) as num_risposte
                  FROM recensioni r
                  JOIN users u ON r.user_id = u.user_id
                  WHERE r.campo_id = ?
                  ORDER BY r.created_at DESC";
        
        if ($limit) {
            $query .= " LIMIT ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('ii', $campoId, $limit);
        } else {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $campoId);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // RECENSIONI - Tutte le recensioni (per gestione)
    // ============================================================================
    
    public function getAllRecensioni($filtri = []) {
        $query = "SELECT r.*, CONCAT(u.nome, ' ', u.cognome) as utente_nome, UPPER(LEFT(u.nome, 1)) as utente_iniziale,
                    c.nome as campo_nome, s.nome as sport_nome,
                    (SELECT COUNT(*) FROM recensione_risposte WHERE recensione_id = r.recensione_id) as num_risposte
                  FROM recensioni r
                  JOIN users u ON r.user_id = u.user_id
                  JOIN campi_sportivi c ON r.campo_id = c.campo_id
                  JOIN sport s ON c.sport_id = s.sport_id
                  WHERE 1=1";
        
        $params = [];
        $types = '';
        
        if (!empty($filtri['campo_id'])) {
            $query .= " AND r.campo_id = ?";
            $params[] = $filtri['campo_id'];
            $types .= 'i';
        }
        
        if (!empty($filtri['rating_min'])) {
            $query .= " AND r.rating_generale >= ?";
            $params[] = $filtri['rating_min'];
            $types .= 'i';
        }
        
        if (!empty($filtri['rating_max'])) {
            $query .= " AND r.rating_generale <= ?";
            $params[] = $filtri['rating_max'];
            $types .= 'i';
        }
        
        if (isset($filtri['senza_risposta']) && $filtri['senza_risposta']) {
            $query .= " AND (SELECT COUNT(*) FROM recensione_risposte WHERE recensione_id = r.recensione_id) = 0";
        }
        
        $query .= " ORDER BY r.created_at DESC";
        
        if (!empty($filtri['limit'])) {
            $query .= " LIMIT ?";
            $params[] = $filtri['limit'];
            $types .= 'i';
        }
        
        if (!empty($params)) {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->db->query($query);
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // RECENSIONI - Statistiche recensioni di un campo
    // ============================================================================
    
    public function getRecensioniStatsCampo($campoId) {
        $query = "SELECT COUNT(*) as totale, ROUND(AVG(rating_generale), 1) as media_generale,
                    ROUND(AVG(rating_condizioni), 1) as media_condizioni, ROUND(AVG(rating_pulizia), 1) as media_pulizia,
                    ROUND(AVG(rating_illuminazione), 1) as media_illuminazione,
                    SUM(CASE WHEN rating_generale = 5 THEN 1 ELSE 0 END) as stelle_5,
                    SUM(CASE WHEN rating_generale = 4 THEN 1 ELSE 0 END) as stelle_4,
                    SUM(CASE WHEN rating_generale = 3 THEN 1 ELSE 0 END) as stelle_3,
                    SUM(CASE WHEN rating_generale = 2 THEN 1 ELSE 0 END) as stelle_2,
                    SUM(CASE WHEN rating_generale = 1 THEN 1 ELSE 0 END) as stelle_1
                  FROM recensioni WHERE campo_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $campoId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    // ============================================================================
    // RECENSIONI - Verifica se esiste già una risposta per la recensione
    // ============================================================================
    
    public function hasRecensioneRisposta($recensioneId) {
        $query = "SELECT COUNT(*) as count FROM recensione_risposte WHERE recensione_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $recensioneId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return ($row['count'] > 0);
    }
    
    // ============================================================================
    // RECENSIONI - Ottieni la risposta singola di una recensione (max 1)
    // ============================================================================
    
    public function getRecensioneRisposta($recensioneId) {
        $query = "SELECT rr.*, CONCAT(u.nome, ' ', u.cognome) as admin_nome
                  FROM recensione_risposte rr
                  JOIN users u ON rr.admin_id = u.user_id
                  WHERE rr.recensione_id = ?
                  LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $recensioneId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Restituisce null se non esiste
    }
    
    // ============================================================================
    // RECENSIONI - Aggiungi risposta admin (solo se non esiste già)
    // ============================================================================
    
    public function addRecensioneRisposta($recensioneId, $adminId, $testo) {
        // Verifica che non esista già una risposta
        if ($this->hasRecensioneRisposta($recensioneId)) {
            return false;
        }
        
        $query = "INSERT INTO recensione_risposte (recensione_id, admin_id, testo) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iis', $recensioneId, $adminId, $testo);
        return $stmt->execute();
    }
    
    // ============================================================================
    // RECENSIONI - Modifica risposta esistente
    // ============================================================================
    
    public function updateRecensioneRisposta($rispostaId, $testo, $adminId) {
        $query = "UPDATE recensione_risposte SET testo = ?, admin_id = ?, created_at = NOW() WHERE risposta_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('sii', $testo, $adminId, $rispostaId);
        return $stmt->execute();
    }
    
    // ============================================================================
    // RECENSIONI - Elimina recensione
    // ============================================================================
    
    public function deleteRecensione($recensioneId) {
        $query = "DELETE FROM recensioni WHERE recensione_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $recensioneId);
        return $stmt->execute();
    }
    
    // ============================================================================
    // RECENSIONI - Risposte admin di una recensione (per compatibilità, max 1)
    // ============================================================================
    
    public function getRecensioneRisposte($recensioneId) {
        $query = "SELECT rr.*, CONCAT(u.nome, ' ', u.cognome) as admin_nome
                  FROM recensione_risposte rr
                  JOIN users u ON rr.admin_id = u.user_id
                  WHERE rr.recensione_id = ?
                  LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $recensioneId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // STATISTICHE AVANZATE - Utilizzo campo nel tempo
    // ============================================================================
    
    public function getStatisticheCampo($campoId, $giorni = 30) {
        $stats = [];
        
        $query1 = "SELECT DAYOFWEEK(data_prenotazione) as giorno, COUNT(*) as totale
                   FROM prenotazioni WHERE campo_id = ? AND stato IN ('completata', 'confermata')
                   GROUP BY DAYOFWEEK(data_prenotazione) ORDER BY giorno";
        $stmt1 = $this->db->prepare($query1);
        $stmt1->bind_param('i', $campoId);
        $stmt1->execute();
        $stats['per_giorno'] = $stmt1->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $query2 = "SELECT HOUR(ora_inizio) as ora, COUNT(*) as totale
                   FROM prenotazioni WHERE campo_id = ? AND stato IN ('completata', 'confermata')
                   GROUP BY HOUR(ora_inizio) ORDER BY ora";
        $stmt2 = $this->db->prepare($query2);
        $stmt2->bind_param('i', $campoId);
        $stmt2->execute();
        $stats['per_ora'] = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $query3 = "SELECT CONCAT(u.nome, ' ', u.cognome) as utente, COUNT(*) as prenotazioni
                   FROM prenotazioni p JOIN users u ON p.user_id = u.user_id
                   WHERE p.campo_id = ? AND p.stato IN ('completata', 'confermata')
                   GROUP BY p.user_id ORDER BY prenotazioni DESC LIMIT 10";
        $stmt3 = $this->db->prepare($query3);
        $stmt3->bind_param('i', $campoId);
        $stmt3->execute();
        $stats['top_utenti'] = $stmt3->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $query4 = "SELECT COUNT(*) as totale_prenotazioni,
                    SUM(CASE WHEN stato = 'completata' THEN 1 ELSE 0 END) as completate,
                    SUM(CASE WHEN stato = 'cancellata' THEN 1 ELSE 0 END) as cancellate,
                    SUM(CASE WHEN stato = 'no_show' THEN 1 ELSE 0 END) as noshow
                   FROM prenotazioni WHERE campo_id = ?";
        $stmt4 = $this->db->prepare($query4);
        $stmt4->bind_param('i', $campoId);
        $stmt4->execute();
        $stats['metriche'] = $stmt4->get_result()->fetch_assoc();
        
        return $stats;
    }

    // ============================================================================
    // GESTIONE UTENTI - Lista completa con filtri
    // ============================================================================
    
    public function getAllUsers($filtri = []) {
        $query = "SELECT 
                    u.user_id, u.email, u.nome, u.cognome, u.telefono, u.ruolo, u.stato, 
                    u.ultimo_accesso, u.created_at,
                    us.corso_laurea_id, us.anno_iscrizione, us.data_nascita, us.penalty_points, 
                    us.xp_points, us.livello_id,
                    cl.nome as corso_nome, cl.facolta,
                    l.nome as livello_nome,
                    (SELECT COUNT(*) FROM prenotazioni p WHERE p.user_id = u.user_id) as totale_prenotazioni,
                    (SELECT COUNT(*) FROM prenotazioni p WHERE p.user_id = u.user_id AND p.stato = 'no_show') as no_show_count
                  FROM users u
                  LEFT JOIN utenti_standard us ON u.user_id = us.user_id
                  LEFT JOIN corsi_laurea cl ON us.corso_laurea_id = cl.corso_id
                  LEFT JOIN livelli l ON us.livello_id = l.livello_id
                  WHERE 1=1";
        
        $params = [];
        $types = '';
        
        if (!empty($filtri['ruolo'])) {
            $query .= " AND u.ruolo = ?";
            $params[] = $filtri['ruolo'];
            $types .= 's';
        }
        
        if (!empty($filtri['stato'])) {
            $query .= " AND u.stato = ?";
            $params[] = $filtri['stato'];
            $types .= 's';
        }
        
        if (!empty($filtri['corso'])) {
            $query .= " AND us.corso_laurea_id = ?";
            $params[] = intval($filtri['corso']);
            $types .= 'i';
        }
        
        if (!empty($filtri['penalty_min'])) {
            $query .= " AND us.penalty_points >= ?";
            $params[] = intval($filtri['penalty_min']);
            $types .= 'i';
        }
        
        if (!empty($filtri['search'])) {
            $query .= " AND (u.nome LIKE ? OR u.cognome LIKE ? OR u.email LIKE ?)";
            $searchTerm = '%' . $filtri['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= 'sss';
        }
        
        $orderBy = " ORDER BY ";
        switch ($filtri['ordina'] ?? 'nome') {
            case 'recente': $orderBy .= "u.created_at DESC"; break;
            case 'attivita': $orderBy .= "totale_prenotazioni DESC"; break;
            case 'penalty': $orderBy .= "us.penalty_points DESC"; break;
            default: $orderBy .= "u.cognome ASC, u.nome ASC";
        }
        $query .= $orderBy;
        
        if (!empty($params)) {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->db->query($query);
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // GESTIONE UTENTI - Statistiche generali
    // ============================================================================
    
    public function getUsersStatsGenerali() {
        $query = "SELECT 
                    COUNT(*) as totale,
                    SUM(CASE WHEN stato = 'attivo' THEN 1 ELSE 0 END) as attivi,
                    SUM(CASE WHEN stato = 'sospeso' THEN 1 ELSE 0 END) as sospesi,
                    SUM(CASE WHEN stato = 'bannato' THEN 1 ELSE 0 END) as bannati
                  FROM users";
        $result = $this->db->query($query);
        return $result->fetch_assoc();
    }
    
    // ============================================================================
    // GESTIONE UTENTI - Dettaglio singolo utente
    // ============================================================================
    
    public function getUserById($userId) {
        $query = "SELECT 
                    u.user_id, u.email, u.nome, u.cognome, u.telefono, u.ruolo, u.stato, 
                    u.ultimo_accesso, u.created_at,
                    us.corso_laurea_id, us.anno_iscrizione, us.data_nascita, us.indirizzo,
                    us.penalty_points, us.xp_points, us.livello_id,
                    cl.nome as corso_nome, cl.facolta,
                    l.nome as livello_nome
                  FROM users u
                  LEFT JOIN utenti_standard us ON u.user_id = us.user_id
                  LEFT JOIN corsi_laurea cl ON us.corso_laurea_id = cl.corso_id
                  LEFT JOIN livelli l ON us.livello_id = l.livello_id
                  WHERE u.user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    // ============================================================================
    // GESTIONE UTENTI - Statistiche utente
    // ============================================================================
    
    public function getUserStats($userId) {
        $query = "SELECT 
                    COUNT(*) as totale_prenotazioni,
                    SUM(CASE WHEN stato = 'completata' THEN 1 ELSE 0 END) as completate,
                    SUM(CASE WHEN stato = 'no_show' THEN 1 ELSE 0 END) as no_show,
                    SUM(CASE WHEN stato = 'cancellata' THEN 1 ELSE 0 END) as cancellate,
                    SUM(CASE WHEN stato = 'completata' THEN TIMESTAMPDIFF(HOUR, ora_inizio, ora_fine) ELSE 0 END) as ore_giocate
                  FROM prenotazioni WHERE user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $stats = $stmt->get_result()->fetch_assoc();
        
        // Tornei (se esiste la tabella)
        $stats['tornei_partecipati'] = 0;
        $stats['tornei_vinti'] = 0;
        
        return $stats;
    }
    
    // ============================================================================
    // GESTIONE UTENTI - Corsi di laurea
    // ============================================================================
    
    public function getCorsiLaurea() {
        $query = "SELECT corso_id, nome, facolta FROM corsi_laurea WHERE attivo = 1 ORDER BY nome";
        $result = $this->db->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    // ============================================================================
    // GESTIONE UTENTI - Penalty Log
    // ============================================================================
    
    public function getPenaltyLog($userId, $limit = 10) {
        $query = "SELECT pl.*, CONCAT(u.nome, ' ', u.cognome) as admin_nome
                  FROM penalty_log pl
                  LEFT JOIN users u ON pl.admin_id = u.user_id
                  WHERE pl.user_id = ?
                  ORDER BY pl.created_at DESC
                  LIMIT ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $userId, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // GESTIONE UTENTI - Segnalazioni ricevute
    // ============================================================================
    
    public function getSegnalazioniRicevute($userId, $limit = 5) {
        $query = "SELECT s.*, CONCAT(u.nome, ' ', u.cognome) as segnalante_nome
                  FROM segnalazioni s
                  JOIN users u ON s.user_segnalante_id = u.user_id
                  WHERE s.user_segnalato_id = ?
                  ORDER BY s.created_at DESC
                  LIMIT ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $userId, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // GESTIONE UTENTI - Segnalazioni fatte
    // ============================================================================
    
    public function getSegnalazioniFatte($userId, $limit = 5) {
        $query = "SELECT s.*, CONCAT(u.nome, ' ', u.cognome) as segnalato_nome
                  FROM segnalazioni s
                  JOIN users u ON s.user_segnalato_id = u.user_id
                  WHERE s.user_segnalante_id = ?
                  ORDER BY s.created_at DESC
                  LIMIT ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $userId, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // GESTIONE UTENTI - Badge utente
    // ============================================================================
    
    public function getUserBadges($userId) {
        $query = "SELECT b.*, ub.sbloccato_at
                  FROM user_badges ub
                  JOIN badges b ON ub.badge_id = b.badge_id
                  WHERE ub.user_id = ?
                  ORDER BY ub.sbloccato_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // GESTIONE UTENTI - Sanzioni utente
    // ============================================================================
    
    public function getUserSanzioni($userId) {
        $query = "SELECT s.*, CONCAT(u.nome, ' ', u.cognome) as admin_nome
                  FROM sanzioni s
                  JOIN users u ON s.admin_id = u.user_id
                  WHERE s.user_id = ?
                  ORDER BY s.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // GESTIONE UTENTI - Attività recenti
    // ============================================================================
    
    public function getUserAttivitaRecenti($userId, $limit = 10) {
        // Combina prenotazioni e altre attività
        $attivita = [];
        
        // Prenotazioni recenti
        $query = "SELECT 'prenotazione' as tipo, p.created_at, 
                    CONCAT('Prenotazione ', c.nome, ' il ', DATE_FORMAT(p.data_prenotazione, '%d/%m/%Y')) as descrizione,
                    '📅' as icona
                  FROM prenotazioni p
                  JOIN campi_sportivi c ON p.campo_id = c.campo_id
                  WHERE p.user_id = ?
                  ORDER BY p.created_at DESC LIMIT ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $userId, $limit);
        $stmt->execute();
        $attivita = array_merge($attivita, $stmt->get_result()->fetch_all(MYSQLI_ASSOC));
        
        // Ordina per data
        usort($attivita, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return array_slice($attivita, 0, $limit);
    }
    
    // ============================================================================
    // GESTIONE UTENTI - Modifica ruolo
    // ============================================================================
    
    public function updateUserRole($userId, $nuovoRuolo, $adminId) {
        $this->db->begin_transaction();
        
        try {
            // Aggiorna ruolo in users
            $query = "UPDATE users SET ruolo = ? WHERE user_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('si', $nuovoRuolo, $userId);
            $stmt->execute();
            
            if ($nuovoRuolo === 'admin') {
                // Aggiungi a tabella admins se non esiste
                $query2 = "INSERT IGNORE INTO admins (user_id) VALUES (?)";
                $stmt2 = $this->db->prepare($query2);
                $stmt2->bind_param('i', $userId);
                $stmt2->execute();
            } else {
                // Rimuovi da tabella admins
                $query2 = "DELETE FROM admins WHERE user_id = ?";
                $stmt2 = $this->db->prepare($query2);
                $stmt2->bind_param('i', $userId);
                $stmt2->execute();
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    
    // ============================================================================
    // GESTIONE UTENTI - Aggiungi penalty points
    // ============================================================================
    
    public function addPenaltyPoints($userId, $punti, $motivo, $descrizione, $adminId) {
        $this->db->begin_transaction();
        
        try {
            // Aggiorna penalty_points
            $query = "UPDATE utenti_standard SET penalty_points = penalty_points + ? WHERE user_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('ii', $punti, $userId);
            $stmt->execute();
            
            // Log
            $query2 = "INSERT INTO penalty_log (user_id, punti, motivo, descrizione, admin_id) VALUES (?, ?, ?, ?, ?)";
            $stmt2 = $this->db->prepare($query2);
            $stmt2->bind_param('iissi', $userId, $punti, $motivo, $descrizione, $adminId);
            $stmt2->execute();
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    
    // ============================================================================
    // GESTIONE UTENTI - Rimuovi penalty points
    // ============================================================================
    
    public function removePenaltyPoints($userId, $punti, $motivo, $descrizione, $adminId) {
        $this->db->begin_transaction();
        
        try {
            // Aggiorna penalty_points (non scende sotto 0)
            $query = "UPDATE utenti_standard SET penalty_points = GREATEST(0, penalty_points - ?) WHERE user_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('ii', $punti, $userId);
            $stmt->execute();
            
            // Log (punti negativi per indicare rimozione)
            $puntiNeg = -$punti;
            $query2 = "INSERT INTO penalty_log (user_id, punti, motivo, descrizione, admin_id) VALUES (?, ?, ?, ?, ?)";
            $stmt2 = $this->db->prepare($query2);
            $stmt2->bind_param('iissi', $userId, $puntiNeg, $motivo, $descrizione, $adminId);
            $stmt2->execute();
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    
    // ============================================================================
    // GESTIONE UTENTI - Reset penalty points
    // ============================================================================
    
    public function resetPenaltyPoints($userId, $descrizione, $adminId) {
        $this->db->begin_transaction();
        
        try {
            // Ottieni punti attuali
            $query = "SELECT penalty_points FROM utenti_standard WHERE user_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $current = $stmt->get_result()->fetch_assoc();
            $puntiAttuali = $current['penalty_points'] ?? 0;
            
            // Azzera
            $query2 = "UPDATE utenti_standard SET penalty_points = 0 WHERE user_id = ?";
            $stmt2 = $this->db->prepare($query2);
            $stmt2->bind_param('i', $userId);
            $stmt2->execute();
            
            // Log
            $puntiNeg = -$puntiAttuali;
            $motivo = 'reset';
            $query3 = "INSERT INTO penalty_log (user_id, punti, motivo, descrizione, admin_id) VALUES (?, ?, ?, ?, ?)";
            $stmt3 = $this->db->prepare($query3);
            $stmt3->bind_param('iissi', $userId, $puntiNeg, $motivo, $descrizione, $adminId);
            $stmt3->execute();
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    
    // ============================================================================
    // GESTIONE UTENTI - Sospendi utente
    // ============================================================================
    
    public function suspendUser($userId, $giorni, $motivo, $adminId) {
        $this->db->begin_transaction();
        
        try {
            // Aggiorna stato
            $query = "UPDATE users SET stato = 'sospeso' WHERE user_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            
            // Crea sanzione
            $dataInizio = date('Y-m-d H:i:s');
            $dataFine = date('Y-m-d H:i:s', strtotime("+{$giorni} days"));
            $tipo = 'sospensione';
            
            $query2 = "INSERT INTO sanzioni (user_id, tipo, motivo, data_inizio, data_fine, admin_id, attiva) 
                       VALUES (?, ?, ?, ?, ?, ?, 1)";
            $stmt2 = $this->db->prepare($query2);
            $stmt2->bind_param('issssi', $userId, $tipo, $motivo, $dataInizio, $dataFine, $adminId);
            $stmt2->execute();
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    
    // ============================================================================
    // GESTIONE UTENTI - Riabilita utente
    // ============================================================================
    
    public function reactivateUser($userId, $adminId) {
        $this->db->begin_transaction();
        
        try {
            // Aggiorna stato
            $query = "UPDATE users SET stato = 'attivo' WHERE user_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            
            // Disattiva sanzioni attive
            $query2 = "UPDATE sanzioni SET attiva = 0 WHERE user_id = ? AND attiva = 1";
            $stmt2 = $this->db->prepare($query2);
            $stmt2->bind_param('i', $userId);
            $stmt2->execute();
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    
    // ============================================================================
    // GESTIONE UTENTI - Ban utente
    // ============================================================================
    
    public function banUser($userId, $motivo, $adminId) {
        $this->db->begin_transaction();
        
        try {
            // Aggiorna stato
            $query = "UPDATE users SET stato = 'bannato' WHERE user_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            
            // Crea sanzione ban (permanente, senza data_fine)
            $dataInizio = date('Y-m-d H:i:s');
            $tipo = 'ban';
            
            $query2 = "INSERT INTO sanzioni (user_id, tipo, motivo, data_inizio, data_fine, admin_id, attiva) 
                       VALUES (?, ?, ?, ?, NULL, ?, 1)";
            $stmt2 = $this->db->prepare($query2);
            $stmt2->bind_param('isssi', $userId, $tipo, $motivo, $dataInizio, $adminId);
            $stmt2->execute();
            
            // Cancella prenotazioni future
            $query3 = "UPDATE prenotazioni SET stato = 'cancellata' 
                       WHERE user_id = ? AND data_prenotazione >= CURDATE() AND stato IN ('confermata', 'pending')";
            $stmt3 = $this->db->prepare($query3);
            $stmt3->bind_param('i', $userId);
            $stmt3->execute();
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    
    // ============================================================================
    // GESTIONE UTENTI - Sbanna utente (Rimuovi Ban)
    // ============================================================================
    
    public function unbanUser($userId, $adminId) {
        $this->db->begin_transaction();
        
        try {
            // Riporta stato ad attivo
            $query = "UPDATE users SET stato = 'attivo' WHERE user_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            
            // Disattiva la sanzione ban
            $query2 = "UPDATE sanzioni SET attiva = 0, data_fine = NOW() 
                       WHERE user_id = ? AND tipo = 'ban' AND attiva = 1";
            $stmt2 = $this->db->prepare($query2);
            $stmt2->bind_param('i', $userId);
            $stmt2->execute();
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    
    // ============================================================================
    // GESTIONE UTENTI - Invia messaggio
    // ============================================================================
    
    public function sendUserMessage($userId, $oggetto, $messaggio, $tipo, $adminId) {
        // Per ora salviamo come notifica nel sistema
        // In futuro si può integrare con sistema email
        
        $query = "INSERT INTO notifiche (user_id, tipo, titolo, messaggio, created_at) 
                  VALUES (?, 'admin_message', ?, ?, NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iss', $userId, $oggetto, $messaggio);
        
        return $stmt->execute();
    }

    // ============================================================================
    // ============================================================================
    // GESTIONE SEGNALAZIONI - CRUD E STATISTICHE
    // ============================================================================
    // ============================================================================
    
    // ============================================================================
    // SEGNALAZIONI - Statistiche contatori per badge
    // ============================================================================
    
    public function getSegnalazioniStats() {
    $query = "SELECT 
                COUNT(*) as totale,
                SUM(CASE WHEN stato = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN stato = 'resolved' AND resolved_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as resolved_week,
                SUM(CASE WHEN stato = 'resolved' THEN 1 ELSE 0 END) as resolved,
                SUM(CASE WHEN stato = 'rejected' THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN priorita = 'alta' AND stato = 'pending' THEN 1 ELSE 0 END) as alta_priorita_pending
              FROM segnalazioni";
    $result = $this->db->query($query);
    return $result->fetch_assoc();
    }
    
    // ============================================================================
    // SEGNALAZIONI - Lista completa con filtri
    // ============================================================================
    
    public function getAllSegnalazioni($filtri = []) {
        $query = "SELECT 
                    s.segnalazione_id, s.tipo, s.descrizione, s.stato, s.priorita,
                    s.created_at, s.resolved_at, s.azione_intrapresa, s.penalty_assegnati,
                    s.note_risoluzione, s.prenotazione_id,
                    u_segnalante.user_id as segnalante_id,
                    CONCAT(u_segnalante.nome, ' ', u_segnalante.cognome) as segnalante_nome,
                    u_segnalante.email as segnalante_email,
                    u_segnalato.user_id as segnalato_id,
                    CONCAT(u_segnalato.nome, ' ', u_segnalato.cognome) as segnalato_nome,
                    u_segnalato.email as segnalato_email,
                    u_segnalato.stato as segnalato_stato,
                    us_segnalato.penalty_points as segnalato_penalty,
                    CONCAT(u_admin.nome, ' ', u_admin.cognome) as admin_nome,
                    DATEDIFF(NOW(), s.created_at) as giorni_attesa,
                    (SELECT COUNT(*) FROM segnalazioni WHERE user_segnalato_id = s.user_segnalato_id) as segnalato_tot_segnalazioni
                  FROM segnalazioni s
                  JOIN users u_segnalante ON s.user_segnalante_id = u_segnalante.user_id
                  JOIN users u_segnalato ON s.user_segnalato_id = u_segnalato.user_id
                  LEFT JOIN utenti_standard us_segnalato ON s.user_segnalato_id = us_segnalato.user_id
                  LEFT JOIN users u_admin ON s.admin_id = u_admin.user_id
                  WHERE 1=1";
        
        $params = [];
        $types = '';
        
        // Filtro stato
        if (!empty($filtri['stato'])) {
            $query .= " AND s.stato = ?";
            $params[] = $filtri['stato'];
            $types .= 's';
        }
        
        // Filtro tipo
        if (!empty($filtri['tipo'])) {
            $query .= " AND s.tipo = ?";
            $params[] = $filtri['tipo'];
            $types .= 's';
        }
        
        // Filtro priorità
        if (!empty($filtri['priorita'])) {
            $query .= " AND s.priorita = ?";
            $params[] = $filtri['priorita'];
            $types .= 's';
        }
        
        // Filtro data inizio
        if (!empty($filtri['data_da'])) {
            $query .= " AND DATE(s.created_at) >= ?";
            $params[] = $filtri['data_da'];
            $types .= 's';
        }
        
        // Filtro data fine
        if (!empty($filtri['data_a'])) {
            $query .= " AND DATE(s.created_at) <= ?";
            $params[] = $filtri['data_a'];
            $types .= 's';
        }
        
        // Filtro ricerca (nome segnalante o segnalato)
        if (!empty($filtri['search'])) {
            $query .= " AND (CONCAT(u_segnalante.nome, ' ', u_segnalante.cognome) LIKE ? 
                        OR CONCAT(u_segnalato.nome, ' ', u_segnalato.cognome) LIKE ?
                        OR u_segnalante.email LIKE ?
                        OR u_segnalato.email LIKE ?)";
            $searchTerm = '%' . $filtri['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= 'ssss';
        }
        
        // Ordinamento
        $orderBy = " ORDER BY ";
        switch ($filtri['ordina'] ?? 'recenti') {
            case 'vecchie':
                $orderBy .= "s.created_at ASC";
                break;
            case 'priorita':
                $orderBy .= "FIELD(s.priorita, 'alta', 'media', 'bassa'), s.created_at DESC";
                break;
            case 'tipo':
                $orderBy .= "s.tipo ASC, s.created_at DESC";
                break;
            default: // recenti
                $orderBy .= "s.created_at DESC";
        }
        $query .= $orderBy;
        
        if (!empty($params)) {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->db->query($query);
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // SEGNALAZIONI - Dettaglio singola segnalazione
    // ============================================================================
    
    public function getSegnalazioneById($segnalazioneId) {
        $query = "SELECT 
                    s.*,
                    u_segnalante.user_id as segnalante_id,
                    CONCAT(u_segnalante.nome, ' ', u_segnalante.cognome) as segnalante_nome,
                    u_segnalante.email as segnalante_email,
                    u_segnalante.telefono as segnalante_telefono,
                    u_segnalante.created_at as segnalante_registrato,
                    u_segnalato.user_id as segnalato_id,
                    CONCAT(u_segnalato.nome, ' ', u_segnalato.cognome) as segnalato_nome,
                    u_segnalato.email as segnalato_email,
                    u_segnalato.telefono as segnalato_telefono,
                    u_segnalato.stato as segnalato_stato_account,
                    u_segnalato.created_at as segnalato_registrato,
                    us_segnalato.penalty_points as segnalato_penalty,
                    us_segnalato.xp_points as segnalato_xp,
                    l.nome as segnalato_livello,
                    CONCAT(u_admin.nome, ' ', u_admin.cognome) as admin_nome
                  FROM segnalazioni s
                  JOIN users u_segnalante ON s.user_segnalante_id = u_segnalante.user_id
                  JOIN users u_segnalato ON s.user_segnalato_id = u_segnalato.user_id
                  LEFT JOIN utenti_standard us_segnalato ON s.user_segnalato_id = us_segnalato.user_id
                  LEFT JOIN livelli l ON us_segnalato.livello_id = l.livello_id
                  LEFT JOIN users u_admin ON s.admin_id = u_admin.user_id
                  WHERE s.segnalazione_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $segnalazioneId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    // ============================================================================
    // SEGNALAZIONI - Dettaglio prenotazione collegata
    // ============================================================================
    
    public function getPrenotazionePerSegnalazione($prenotazioneId) {
        $query = "SELECT p.*, c.nome as campo_nome, sp.nome as sport_nome,
                    CONCAT(u.nome, ' ', u.cognome) as utente_nome
                  FROM prenotazioni p
                  JOIN campi_sportivi c ON p.campo_id = c.campo_id
                  JOIN sport sp ON c.sport_id = sp.sport_id
                  JOIN users u ON p.user_id = u.user_id
                  WHERE p.prenotazione_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $prenotazioneId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    // ============================================================================
    // SEGNALAZIONI - Cambia stato semplice
    // ============================================================================
    
    public function updateSegnalazioneStato($segnalazioneId, $nuovoStato, $adminId) {
        $query = "UPDATE segnalazioni SET stato = ?, admin_id = ? WHERE segnalazione_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('sii', $nuovoStato, $adminId, $segnalazioneId);
        return $stmt->execute();
    }
    
    // ============================================================================
    // SEGNALAZIONI - Risolvi segnalazione (con azioni)
    // ============================================================================
    
    public function resolveSegnalazione($segnalazioneId, $data, $adminId) {
        $this->db->begin_transaction();
        
        try {
            // Ottieni info segnalazione
            $segnalazione = $this->getSegnalazioneById($segnalazioneId);
            if (!$segnalazione) {
                throw new Exception('Segnalazione non trovata');
            }
            
            $userSegnalatoId = $segnalazione['user_segnalato_id'];
            $userSegnalanteId = $segnalazione['user_segnalante_id'];
            
            // Aggiorna segnalazione
            $query = "UPDATE segnalazioni SET 
                        stato = 'resolved',
                        admin_id = ?,
                        azione_intrapresa = ?,
                        penalty_assegnati = ?,
                        note_risoluzione = ?,
                        resolved_at = NOW()
                      WHERE segnalazione_id = ?";
            $stmt = $this->db->prepare($query);
            $penaltyAssegnati = !empty($data['penalty_points']) ? intval($data['penalty_points']) : null;
            $stmt->bind_param('isisi', 
                $adminId, 
                $data['azione'], 
                $penaltyAssegnati,
                $data['note'],
                $segnalazioneId
            );
            $stmt->execute();
            
            // Esegui azione selezionata
            switch ($data['azione']) {
                case 'warning':
                    // Invia notifica di warning
                    $this->inviaNotificaSegnalazione($userSegnalatoId, 'warning', 
                        'Hai ricevuto un avvertimento',
                        'Hai ricevuto un avvertimento formale per: ' . $data['note']
                    );
                    break;
                    
                case 'penalty_points':
                    if (!empty($data['penalty_points'])) {
                        $this->addPenaltyPointsFromSegnalazione(
                            $userSegnalatoId, 
                            intval($data['penalty_points']), 
                            $segnalazioneId,
                            $adminId
                        );
                    }
                    break;
                    
                case 'sospensione':
                    if (!empty($data['giorni_sospensione'])) {
                        $this->suspendUser($userSegnalatoId, intval($data['giorni_sospensione']), 
                            'Sospensione per segnalazione: ' . $data['note'], $adminId);
                    }
                    break;
                    
                case 'ban':
                    $this->banUser($userSegnalatoId, 'Ban per segnalazione: ' . $data['note'], $adminId);
                    break;
            }
            
            // Invia notifiche se richiesto
            if (!empty($data['invia_notifiche'])) {
                // Notifica al segnalante
                $this->inviaNotificaSegnalazione($userSegnalanteId, 'segnalazione_gestita',
                    'La tua segnalazione è stata gestita',
                    'La segnalazione che hai inviato è stata esaminata e risolta. Azione intrapresa: ' . $this->getAzioneLabel($data['azione'])
                );
                
                // Notifica al segnalato (se non è già stato notificato con warning/sospensione/ban)
                if ($data['azione'] === 'nessuna' || $data['azione'] === 'penalty_points') {
                    $this->inviaNotificaSegnalazione($userSegnalatoId, 'segnalazione_ricevuta',
                        'Hai ricevuto una segnalazione',
                        'È stata confermata una segnalazione nei tuoi confronti. ' . 
                        ($data['azione'] === 'penalty_points' ? 'Ti sono stati assegnati ' . $data['penalty_points'] . ' penalty points.' : '')
                    );
                }
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    
    // ============================================================================
    // SEGNALAZIONI - Rigetta segnalazione
    // ============================================================================
    
    public function rejectSegnalazione($segnalazioneId, $motivo, $adminId, $inviaNotifica = true) {
        $this->db->begin_transaction();
        
        try {
            // Ottieni info segnalazione
            $segnalazione = $this->getSegnalazioneById($segnalazioneId);
            if (!$segnalazione) {
                throw new Exception('Segnalazione non trovata');
            }
            
            // Aggiorna segnalazione
            $query = "UPDATE segnalazioni SET 
                        stato = 'rejected',
                        admin_id = ?,
                        azione_intrapresa = 'nessuna',
                        note_risoluzione = ?,
                        resolved_at = NOW()
                      WHERE segnalazione_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('isi', $adminId, $motivo, $segnalazioneId);
            $stmt->execute();
            
            // Notifica al segnalante
            if ($inviaNotifica) {
                $this->inviaNotificaSegnalazione($segnalazione['user_segnalante_id'], 'segnalazione_rifiutata',
                    'Segnalazione non accolta',
                    'La tua segnalazione è stata esaminata ma non è stata accolta. Motivo: ' . $motivo
                );
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    
    // ============================================================================
    // SEGNALAZIONI - Helper per aggiungere penalty da segnalazione
    // ============================================================================
    
    private function addPenaltyPointsFromSegnalazione($userId, $punti, $segnalazioneId, $adminId) {
        // Aggiorna punti utente
        $query = "UPDATE utenti_standard SET penalty_points = penalty_points + ? WHERE user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $punti, $userId);
        $stmt->execute();
        
        // Log (se esiste tabella penalty_log)
        $checkTable = $this->db->query("SHOW TABLES LIKE 'penalty_log'");
        if ($checkTable->num_rows > 0) {
            $motivo = 'segnalazione';
            $query2 = "INSERT INTO penalty_log (user_id, punti, motivo, segnalazione_id, admin_id, created_at) 
                       VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt2 = $this->db->prepare($query2);
            $stmt2->bind_param('iisii', $userId, $punti, $motivo, $segnalazioneId, $adminId);
            $stmt2->execute();
        }
        
        // Notifica
        $this->inviaNotificaSegnalazione($userId, 'penalty_ricevuti',
            'Penalty Points Ricevuti',
            'Hai ricevuto ' . $punti . ' penalty points a seguito di una segnalazione confermata.'
        );
    }
    
    // ============================================================================
    // SEGNALAZIONI - Helper per inviare notifica
    // ============================================================================
    
    private function inviaNotificaSegnalazione($userId, $tipo, $titolo, $messaggio) {
        $query = "INSERT INTO notifiche (user_id, tipo, titolo, messaggio, created_at) 
                  VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('isss', $userId, $tipo, $titolo, $messaggio);
        return $stmt->execute();
    }
    
    // ============================================================================
    // SEGNALAZIONI - Helper per label azione
    // ============================================================================
    
    private function getAzioneLabel($azione) {
        $labels = [
            'nessuna' => 'Nessuna azione',
            'warning' => 'Warning inviato',
            'penalty_points' => 'Penalty points assegnati',
            'sospensione' => 'Sospensione temporanea',
            'ban' => 'Ban permanente'
        ];
        return $labels[$azione] ?? $azione;
    }
    
    // ============================================================================
    // SEGNALAZIONI - Profilo credibilità segnalante
    // ============================================================================
    
    public function getProfiloSegnalante($userId) {
        $query = "SELECT 
                    u.user_id, u.nome, u.cognome, u.email, u.stato, u.created_at,
                    us.penalty_points, us.xp_points,
                    l.nome as livello_nome,
                    (SELECT COUNT(*) FROM segnalazioni WHERE user_segnalante_id = ?) as segnalazioni_fatte,
                    (SELECT COUNT(*) FROM segnalazioni WHERE user_segnalante_id = ? AND stato = 'resolved') as segnalazioni_validate,
                    (SELECT COUNT(*) FROM segnalazioni WHERE user_segnalante_id = ? AND stato = 'rejected') as segnalazioni_rifiutate,
                    (SELECT COUNT(*) FROM segnalazioni WHERE user_segnalato_id = ?) as segnalazioni_ricevute
                  FROM users u
                  LEFT JOIN utenti_standard us ON u.user_id = us.user_id
                  LEFT JOIN livelli l ON us.livello_id = l.livello_id
                  WHERE u.user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iiiii', $userId, $userId, $userId, $userId, $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    // ============================================================================
    // SEGNALAZIONI - Profilo utente segnalato con statistiche complete
    // ============================================================================
    
    public function getProfiloSegnalato($userId) {
        $query = "SELECT 
                    u.user_id, u.nome, u.cognome, u.email, u.stato, u.created_at,
                    us.penalty_points, us.xp_points,
                    l.nome as livello_nome,
                    cl.nome as corso_nome,
                    (SELECT COUNT(*) FROM segnalazioni WHERE user_segnalato_id = ?) as segnalazioni_ricevute,
                    (SELECT COUNT(*) FROM segnalazioni WHERE user_segnalato_id = ? AND stato = 'resolved') as segnalazioni_confermate,
                    (SELECT COUNT(*) FROM prenotazioni WHERE user_id = ? AND stato = 'no_show') as no_show_totali,
                    (SELECT COUNT(*) FROM prenotazioni WHERE user_id = ? AND stato = 'completata') as prenotazioni_completate,
                    (SELECT COUNT(*) FROM prenotazioni WHERE user_id = ?) as prenotazioni_totali,
                    (SELECT COUNT(*) FROM sanzioni WHERE user_id = ?) as sanzioni_totali,
                    (SELECT COUNT(*) FROM sanzioni WHERE user_id = ? AND attiva = 1) as sanzioni_attive
                  FROM users u
                  LEFT JOIN utenti_standard us ON u.user_id = us.user_id
                  LEFT JOIN livelli l ON us.livello_id = l.livello_id
                  LEFT JOIN corsi_laurea cl ON us.corso_laurea_id = cl.corso_id
                  WHERE u.user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iiiiiiii', $userId, $userId, $userId, $userId, $userId, $userId, $userId, $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    // ============================================================================
    // SEGNALAZIONI - Storico segnalazioni ricevute per utente
    // ============================================================================
    
    public function getStoricoSegnalazioniUtente($userId, $excludeId = null, $limit = 5) {
        $query = "SELECT 
                    s.segnalazione_id, s.tipo, s.stato, s.created_at, s.azione_intrapresa,
                    CONCAT(u.nome, ' ', u.cognome) as segnalante_nome
                  FROM segnalazioni s
                  JOIN users u ON s.user_segnalante_id = u.user_id
                  WHERE s.user_segnalato_id = ?";
        
        $params = [$userId];
        $types = 'i';
        
        if ($excludeId) {
            $query .= " AND s.segnalazione_id != ?";
            $params[] = $excludeId;
            $types .= 'i';
        }
        
        $query .= " ORDER BY s.created_at DESC LIMIT ?";
        $params[] = $limit;
        $types .= 'i';
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // SEGNALAZIONI - Sanzioni attive utente
    // ============================================================================
    
    public function getSanzioniUtente($userId, $limit = 5) {
        $query = "SELECT s.*, CONCAT(u.nome, ' ', u.cognome) as admin_nome
                  FROM sanzioni s
                  LEFT JOIN users u ON s.admin_id = u.user_id
                  WHERE s.user_id = ?
                  ORDER BY s.created_at DESC LIMIT ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $userId, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // SEGNALAZIONI - Tipi disponibili con icone
    // ============================================================================
    
    public function getTipiSegnalazione() {
        return [
            'no_show' => ['label' => 'No Show', 'icon' => '🚫', 'color' => '#EF4444'],
            'comportamento_scorretto' => ['label' => 'Comportamento Scorretto', 'icon' => '⚠️', 'color' => '#F59E0B'],
            'linguaggio_offensivo' => ['label' => 'Linguaggio Offensivo', 'icon' => '🗣️', 'color' => '#F97316'],
            'violenza' => ['label' => 'Violenza', 'icon' => '🔴', 'color' => '#DC2626'],
            'altro' => ['label' => 'Altro', 'icon' => '📝', 'color' => '#6B7280']
        ];
    }

    // ============================================================================
    // SEGNALAZIONI - Calcola automaticamente la priorità in base al tipo di segnalazione
    // ============================================================================
    
    public function calcolaPrioritaSegnalazione($tipo) {
        $prioritaAlta = ['no_show', 'comportamento'];
        $prioritaMedia = ['ritardo', 'danno_struttura'];
        $prioritaBassa = ['altro'];
        
        if (in_array($tipo, $prioritaAlta)) {
            return 'alta';
        } elseif (in_array($tipo, $prioritaMedia)) {
            return 'media';
        } else {
            return 'bassa';
        }
    }

    // ============================================================================
    // ============================================================================
    // GESTIONE COMUNICAZIONI - BROADCAST E MESSAGGI
    // ============================================================================
    // ============================================================================
    
    // ============================================================================
    // BROADCAST - Statistiche per KPI
    // ============================================================================
    
    public function getBroadcastStats() {
        $query = "SELECT 
                    COUNT(*) as totale,
                    SUM(CASE WHEN stato = 'inviato' THEN 1 ELSE 0 END) as inviati,
                    SUM(CASE WHEN stato = 'programmato' THEN 1 ELSE 0 END) as programmati,
                    SUM(CASE WHEN stato = 'bozza' THEN 1 ELSE 0 END) as bozze,
                    SUM(CASE WHEN stato = 'fallito' THEN 1 ELSE 0 END) as falliti,
                    SUM(num_destinatari) as destinatari_totali
                  FROM broadcast_messages";
        $result = $this->db->query($query);
        return $result->fetch_assoc();
    }
    
    // ============================================================================
    // BROADCAST - Lista completa con filtri
    // ============================================================================
    
    public function getAllBroadcasts($filtri = []) {
        $query = "SELECT bm.*, CONCAT(u.nome, ' ', u.cognome) as admin_nome
                  FROM broadcast_messages bm
                  JOIN users u ON bm.admin_id = u.user_id
                  WHERE 1=1";
        
        $params = [];
        $types = '';
        
        if (!empty($filtri['stato'])) {
            $query .= " AND bm.stato = ?";
            $params[] = $filtri['stato'];
            $types .= 's';
        }
        
        if (!empty($filtri['search'])) {
            $query .= " AND (bm.oggetto LIKE ? OR bm.messaggio LIKE ?)";
            $searchTerm = '%' . $filtri['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= 'ss';
        }
        
        $query .= " ORDER BY bm.created_at DESC";
        
        if (!empty($params)) {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->db->query($query);
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // BROADCAST - Dettaglio singolo broadcast
    // ============================================================================
    
    public function getBroadcastById($broadcastId) {
        $query = "SELECT bm.*, CONCAT(u.nome, ' ', u.cognome) as admin_nome
                  FROM broadcast_messages bm
                  JOIN users u ON bm.admin_id = u.user_id
                  WHERE bm.broadcast_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $broadcastId);
        $stmt->execute();
        $broadcast = $stmt->get_result()->fetch_assoc();
        
        // Se è un messaggio diretto, recupera i nomi dei destinatari
        if ($broadcast && $broadcast['target_type'] === 'direct' && !empty($broadcast['target_filter'])) {
            $userIds = json_decode($broadcast['target_filter'], true);
            if (is_array($userIds) && count($userIds) > 0) {
                $placeholders = implode(',', array_fill(0, count($userIds), '?'));
                $types = str_repeat('i', count($userIds));
                $destQuery = "SELECT user_id, CONCAT(nome, ' ', cognome) as nome_completo, email 
                              FROM users WHERE user_id IN ($placeholders)";
                $destStmt = $this->db->prepare($destQuery);
                $destStmt->bind_param($types, ...$userIds);
                $destStmt->execute();
                $broadcast['destinatari_dettaglio'] = $destStmt->get_result()->fetch_all(MYSQLI_ASSOC);
            }
        }
        
        // Aggiungi descrizione target leggibile
        $targetDescriptions = [
            'tutti' => 'Tutti gli utenti',
            'attivi' => 'Utenti attivi',
            'corso' => 'Corso di laurea',
            'sport' => 'Sport preferito',
            'livello' => 'Livello utente',
            'custom' => 'Lista personalizzata',
            'direct' => 'Messaggio diretto'
        ];
        $broadcast['target_description'] = $targetDescriptions[$broadcast['target_type']] ?? $broadcast['target_type'];
        
        return $broadcast;
    }
    
    // ============================================================================
    // BROADCAST - Salva bozza
    // ============================================================================
    
    public function saveBroadcastDraft($data) {
        $query = "INSERT INTO broadcast_messages 
                  (admin_id, oggetto, messaggio, target_type, target_filter, canale, stato, created_at)
                  VALUES (?, ?, ?, ?, ?, ?, 'bozza', NOW())";
        
        $stmt = $this->db->prepare($query);
        $oggetto = $data['oggetto'] ?: 'Bozza senza titolo';
        $messaggio = $data['messaggio'] ?: '';
        $targetType = $data['target_type'] ?? 'tutti';
        $targetFilter = $data['target_filter'] ?? null;
        $canale = $data['canale'] ?? 'in_app';
        
        $stmt->bind_param('isssss', 
            $data['admin_id'],
            $oggetto,
            $messaggio,
            $targetType,
            $targetFilter,
            $canale
        );
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }
    
    // ============================================================================
    // BROADCAST - Aggiorna bozza esistente
    // ============================================================================
    
    public function updateBroadcastDraft($broadcastId, $data) {
        // Verifica che sia una bozza
        $check = $this->getBroadcastById($broadcastId);
        if (!$check || $check['stato'] !== 'bozza') {
            return false;
        }
        
        $query = "UPDATE broadcast_messages 
                  SET oggetto = ?, messaggio = ?, target_type = ?, target_filter = ?, canale = ?
                  WHERE broadcast_id = ? AND stato = 'bozza'";
        
        $stmt = $this->db->prepare($query);
        $oggetto = $data['oggetto'] ?: 'Bozza senza titolo';
        $messaggio = $data['messaggio'] ?: '';
        $targetType = $data['target_type'] ?? 'tutti';
        $targetFilter = $data['target_filter'] ?? null;
        $canale = $data['canale'] ?? 'in_app';
        
        $stmt->bind_param('sssssi', 
            $oggetto,
            $messaggio,
            $targetType,
            $targetFilter,
            $canale,
            $broadcastId
        );
        
        return $stmt->execute();
    }
    
    // ============================================================================
    // BROADCAST - Invia bozza (converti in broadcast e invia)
    // ============================================================================
    
    public function sendBroadcastFromDraft($broadcastId, $adminId) {
        // Recupera la bozza
        $draft = $this->getBroadcastById($broadcastId);
        if (!$draft || $draft['stato'] !== 'bozza') {
            return ['success' => false, 'message' => 'Bozza non trovata o già inviata'];
        }
        
        // Prepara i dati per createBroadcast
        $data = [
            'admin_id' => $adminId,
            'oggetto' => $draft['oggetto'],
            'messaggio' => $draft['messaggio'],
            'target_type' => $draft['target_type'],
            'target_filter' => $draft['target_filter'],
            'canale' => $draft['canale'],
            'scheduled_at' => null,
            'salva_template' => false
        ];
        
        // Elimina la bozza
        $this->deleteBroadcast($broadcastId);
        
        // Crea e invia il broadcast
        return $this->createBroadcast($data);
    }
    
    // ============================================================================
    // BROADCAST - Conta destinatari in base ai filtri
    // ============================================================================
    
    public function countDestinatariBroadcast($targetType, $targetFilter = null) {
        $query = "";
        
        switch ($targetType) {
            case 'tutti':
                $query = "SELECT COUNT(*) as count FROM users WHERE ruolo = 'user' AND stato = 'attivo'";
                break;
                
            case 'attivi':
                // Utenti che hanno fatto almeno una prenotazione nell'ultimo mese
                $query = "SELECT COUNT(DISTINCT u.user_id) as count 
                          FROM users u 
                          JOIN prenotazioni p ON u.user_id = p.user_id 
                          WHERE u.ruolo = 'user' AND u.stato = 'attivo' 
                          AND p.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
                break;
                
            case 'corso':
                if (!empty($targetFilter)) {
                    $query = "SELECT COUNT(*) as count 
                              FROM users u 
                              JOIN utenti_standard us ON u.user_id = us.user_id 
                              WHERE u.ruolo = 'user' AND u.stato = 'attivo' 
                              AND us.corso_laurea_id = " . intval($targetFilter);
                } else {
                    $query = "SELECT COUNT(*) as count FROM users WHERE ruolo = 'user' AND stato = 'attivo'";
                }
                break;
                
            case 'sport':
                if (!empty($targetFilter)) {
                    $query = "SELECT COUNT(DISTINCT u.user_id) as count 
                              FROM users u 
                              JOIN user_sport_preferiti usp ON u.user_id = usp.user_id 
                              WHERE u.ruolo = 'user' AND u.stato = 'attivo' 
                              AND usp.sport_id = " . intval($targetFilter);
                } else {
                    $query = "SELECT COUNT(*) as count FROM users WHERE ruolo = 'user' AND stato = 'attivo'";
                }
                break;
                
            case 'livello':
                if (!empty($targetFilter)) {
                    $query = "SELECT COUNT(*) as count 
                              FROM users u 
                              JOIN utenti_standard us ON u.user_id = us.user_id 
                              WHERE u.ruolo = 'user' AND u.stato = 'attivo' 
                              AND us.livello_id = " . intval($targetFilter);
                } else {
                    $query = "SELECT COUNT(*) as count FROM users WHERE ruolo = 'user' AND stato = 'attivo'";
                }
                break;
                
            case 'custom':
                if (!empty($targetFilter)) {
                    // Parse email/ID list
                    $items = array_map('trim', explode(',', $targetFilter));
                    $emailList = [];
                    $idList = [];
                    
                    foreach ($items as $item) {
                        if (filter_var($item, FILTER_VALIDATE_EMAIL)) {
                            $emailList[] = "'" . $this->db->real_escape_string($item) . "'";
                        } elseif (is_numeric($item)) {
                            $idList[] = intval($item);
                        }
                    }
                    
                    $conditions = [];
                    if (!empty($emailList)) {
                        $conditions[] = "u.email IN (" . implode(',', $emailList) . ")";
                    }
                    if (!empty($idList)) {
                        $conditions[] = "u.user_id IN (" . implode(',', $idList) . ")";
                    }
                    
                    if (!empty($conditions)) {
                        $query = "SELECT COUNT(*) as count FROM users u WHERE u.stato = 'attivo' AND (" . implode(' OR ', $conditions) . ")";
                    } else {
                        return 0;
                    }
                } else {
                    return 0;
                }
                break;
                
            default:
                $query = "SELECT COUNT(*) as count FROM users WHERE ruolo = 'user' AND stato = 'attivo'";
        }
        
        $result = $this->db->query($query);
        $row = $result->fetch_assoc();
        return $row['count'] ?? 0;
    }
    
    // ============================================================================
    // BROADCAST - Ottieni lista destinatari
    // ============================================================================
    
    public function getDestinatariBroadcast($targetType, $targetFilter = null) {
        $query = "";
        
        switch ($targetType) {
            case 'tutti':
                $query = "SELECT user_id, email FROM users WHERE ruolo = 'user' AND stato = 'attivo'";
                break;
                
            case 'attivi':
                $query = "SELECT DISTINCT u.user_id, u.email 
                          FROM users u 
                          JOIN prenotazioni p ON u.user_id = p.user_id 
                          WHERE u.ruolo = 'user' AND u.stato = 'attivo' 
                          AND p.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
                break;
                
            case 'corso':
                if (!empty($targetFilter)) {
                    $query = "SELECT u.user_id, u.email 
                              FROM users u 
                              JOIN utenti_standard us ON u.user_id = us.user_id 
                              WHERE u.ruolo = 'user' AND u.stato = 'attivo' 
                              AND us.corso_laurea_id = " . intval($targetFilter);
                } else {
                    $query = "SELECT user_id, email FROM users WHERE ruolo = 'user' AND stato = 'attivo'";
                }
                break;
                
            case 'sport':
                if (!empty($targetFilter)) {
                    $query = "SELECT DISTINCT u.user_id, u.email 
                              FROM users u 
                              JOIN user_sport_preferiti usp ON u.user_id = usp.user_id 
                              WHERE u.ruolo = 'user' AND u.stato = 'attivo' 
                              AND usp.sport_id = " . intval($targetFilter);
                } else {
                    $query = "SELECT user_id, email FROM users WHERE ruolo = 'user' AND stato = 'attivo'";
                }
                break;
                
            case 'livello':
                if (!empty($targetFilter)) {
                    $query = "SELECT u.user_id, u.email 
                              FROM users u 
                              JOIN utenti_standard us ON u.user_id = us.user_id 
                              WHERE u.ruolo = 'user' AND u.stato = 'attivo' 
                              AND us.livello_id = " . intval($targetFilter);
                } else {
                    $query = "SELECT user_id, email FROM users WHERE ruolo = 'user' AND stato = 'attivo'";
                }
                break;
                
            case 'custom':
                if (!empty($targetFilter)) {
                    $items = array_map('trim', explode(',', $targetFilter));
                    $emailList = [];
                    $idList = [];
                    
                    foreach ($items as $item) {
                        if (filter_var($item, FILTER_VALIDATE_EMAIL)) {
                            $emailList[] = "'" . $this->db->real_escape_string($item) . "'";
                        } elseif (is_numeric($item)) {
                            $idList[] = intval($item);
                        }
                    }
                    
                    $conditions = [];
                    if (!empty($emailList)) {
                        $conditions[] = "u.email IN (" . implode(',', $emailList) . ")";
                    }
                    if (!empty($idList)) {
                        $conditions[] = "u.user_id IN (" . implode(',', $idList) . ")";
                    }
                    
                    if (!empty($conditions)) {
                        $query = "SELECT u.user_id, u.email FROM users u WHERE u.stato = 'attivo' AND (" . implode(' OR ', $conditions) . ")";
                    } else {
                        return [];
                    }
                } else {
                    return [];
                }
                break;
                
            default:
                $query = "SELECT user_id, email FROM users WHERE ruolo = 'user' AND stato = 'attivo'";
        }
        
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // BROADCAST - Crea e invia broadcast
    // ============================================================================
    
    public function createBroadcast($data) {
        $this->db->begin_transaction();
        
        try {
            // Determina lo stato
            $stato = !empty($data['scheduled_at']) ? 'programmato' : 'inviato';
            
            // Conta destinatari
            $targetFilter = !empty($data['target_filter']) ? json_decode($data['target_filter'], true) : null;
            if (is_string($targetFilter)) {
                $targetFilter = $targetFilter; // Mantieni come stringa se non è JSON valido
            }
            $numDestinatari = $this->countDestinatariBroadcast($data['target_type'], is_array($targetFilter) ? $targetFilter[0] ?? null : $targetFilter);
            
            // Inserisci broadcast
            $query = "INSERT INTO broadcast_messages 
                      (admin_id, oggetto, messaggio, target_type, target_filter, canale, scheduled_at, num_destinatari, stato) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('issssssis', 
                $data['admin_id'],
                $data['oggetto'],
                $data['messaggio'],
                $data['target_type'],
                $data['target_filter'],
                $data['canale'],
                $data['scheduled_at'],
                $numDestinatari,
                $stato
            );
            $stmt->execute();
            $broadcastId = $this->db->insert_id;
            
            // Se invio immediato, crea le notifiche
            if ($stato === 'inviato') {
                $destinatari = $this->getDestinatariBroadcast($data['target_type'], is_array($targetFilter) ? $targetFilter[0] ?? null : $targetFilter);
                
                // Invia notifiche in-app
                if ($data['canale'] === 'in_app' || $data['canale'] === 'entrambi') {
                    foreach ($destinatari as $dest) {
                        $this->creaNotifica(
                            $dest['user_id'],
                            'broadcast',
                            $data['oggetto'],
                            $data['messaggio']
                        );
                    }
                }
                
                // Aggiorna timestamp invio
                $updateQuery = "UPDATE broadcast_messages SET sent_at = NOW() WHERE broadcast_id = ?";
                $updateStmt = $this->db->prepare($updateQuery);
                $updateStmt->bind_param('i', $broadcastId);
                $updateStmt->execute();
            }
            
            // Salva come template se richiesto
            if (!empty($data['salva_template'])) {
                $this->saveNotificationTemplate([
                    'tipo' => 'broadcast_' . $broadcastId,
                    'titolo' => $data['oggetto'],
                    'messaggio' => $data['messaggio'],
                    'admin_id' => $data['admin_id']
                ]);
            }
            
            $this->db->commit();
            return [
                'success' => true,
                'broadcast_id' => $broadcastId,
                'destinatari' => $numDestinatari
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            return [
                'success' => false,
                'message' => 'Errore: ' . $e->getMessage()
            ];
        }
    }
    
    // ============================================================================
    // BROADCAST - Elimina broadcast (solo bozze)
    // ============================================================================
    
    public function deleteBroadcast($broadcastId) {
        $query = "DELETE FROM broadcast_messages WHERE broadcast_id = ? AND stato = 'bozza'";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $broadcastId);
        return $stmt->execute() && $stmt->affected_rows > 0;
    }
    
    // ============================================================================
    // NOTIFICHE - Crea notifica singola
    // ============================================================================
    
    public function creaNotifica($userId, $tipo, $titolo, $messaggio, $link = null) {
        $query = "INSERT INTO notifiche (user_id, tipo, titolo, messaggio, link, created_at) 
                  VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('issss', $userId, $tipo, $titolo, $messaggio, $link);
        return $stmt->execute();
    }
    
    // ============================================================================
    // TEMPLATE NOTIFICHE - Ottieni tutti i template
    // ============================================================================
    
    public function getNotificationTemplates() {
        $query = "SELECT * FROM notification_templates WHERE attivo = 1 ORDER BY updated_at DESC";
        $result = $this->db->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    // ============================================================================
    // TEMPLATE NOTIFICHE - Salva template
    // ============================================================================
    
    public function saveNotificationTemplate($data) {
        $query = "INSERT INTO notification_templates (tipo, titolo_template, messaggio_template, updated_by) 
                  VALUES (?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE 
                  titolo_template = VALUES(titolo_template),
                  messaggio_template = VALUES(messaggio_template),
                  updated_by = VALUES(updated_by),
                  updated_at = NOW()";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('sssi', $data['tipo'], $data['titolo'], $data['messaggio'], $data['admin_id']);
        return $stmt->execute();
    }
    
    // ============================================================================
    // TEMPLATE NOTIFICHE - Elimina template
    // ============================================================================
    
    public function deleteNotificationTemplate($templateId) {
        $query = "UPDATE notification_templates SET attivo = 0 WHERE template_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $templateId);
        return $stmt->execute();
    }
    
    // ============================================================================
    // MESSAGGI DIRETTI - Cerca utenti
    // ============================================================================
    
    public function searchUsersForMessage($searchTerm) {
        $query = "SELECT user_id, email, nome, cognome 
                  FROM users 
                  WHERE stato = 'attivo' 
                  AND (nome LIKE ? OR cognome LIKE ? OR email LIKE ? OR CONCAT(nome, ' ', cognome) LIKE ?)
                  ORDER BY cognome, nome
                  LIMIT 10";
        $stmt = $this->db->prepare($query);
        $term = '%' . $searchTerm . '%';
        $stmt->bind_param('ssss', $term, $term, $term, $term);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // MESSAGGI DIRETTI - Invia messaggio a singolo utente
    // ============================================================================
    
    public function sendDirectMessage($userIds, $oggetto, $messaggio, $canali, $adminId) {
        // Accetta sia singolo ID che array di ID
        if (!is_array($userIds)) {
            $userIds = [$userIds];
        }
        
        $this->db->begin_transaction();
        
        try {
            $successCount = 0;
            
            foreach ($userIds as $userId) {
                $userId = intval($userId);
                if ($userId <= 0) continue;
                
                // Verifica che l'utente esista
                $checkQuery = "SELECT user_id FROM users WHERE user_id = ?";
                $checkStmt = $this->db->prepare($checkQuery);
                $checkStmt->bind_param('i', $userId);
                $checkStmt->execute();
                if (!$checkStmt->get_result()->fetch_assoc()) {
                    continue; // Utente non esiste, salta
                }
                
                // Crea notifica in-app se richiesto
                if (in_array('in_app', $canali)) {
                    $query = "INSERT INTO notifiche (user_id, tipo, titolo, messaggio, link, created_at) 
                              VALUES (?, 'admin_message', ?, ?, NULL, NOW())";
                    $stmt = $this->db->prepare($query);
                    $stmt->bind_param('iss', $userId, $oggetto, $messaggio);
                    if ($stmt->execute()) {
                        $successCount++;
                    }
                }
            }
            
            // Salva anche come broadcast per lo storico admin (solo se almeno un invio riuscito)
            if ($successCount > 0) {
                // Determina il canale
                $canale = 'in_app';
                if (in_array('in_app', $canali) && in_array('email', $canali)) {
                    $canale = 'entrambi';
                } else if (in_array('email', $canali)) {
                    $canale = 'email';
                }
                
                // Salva il messaggio diretto come broadcast con target_type='direct'
                $targetFilter = json_encode($userIds);
                $broadcastQuery = "INSERT INTO broadcast_messages 
                                   (admin_id, oggetto, messaggio, target_type, target_filter, canale, 
                                    scheduled_at, sent_at, num_destinatari, stato, created_at)
                                   VALUES (?, ?, ?, 'direct', ?, ?, NULL, NOW(), ?, 'inviato', NOW())";
                $broadcastStmt = $this->db->prepare($broadcastQuery);
                $broadcastStmt->bind_param('issssi', 
                    $adminId, 
                    $oggetto, 
                    $messaggio, 
                    $targetFilter,
                    $canale,
                    $successCount
                );
                $broadcastStmt->execute();
            }
            
            $this->db->commit();
            return $successCount;
            
        } catch (Exception $e) {
            $this->db->rollback();
            return 0;
        }
    }
    
    // ============================================================================
    // LIVELLI - Ottieni tutti i livelli
    // ============================================================================
    
    public function getAllLivelli() {
        $query = "SELECT livello_id, nome, xp_minimo, xp_massimo FROM livelli ORDER BY xp_minimo ASC";
        $result = $this->db->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // ============================================================================
    // RECENSIONI - Statistiche generali per KPI admin
    // ============================================================================
    
    public function getRecensioniStatsGenerali() {
        $query = "SELECT 
                    COUNT(*) as totale,
                    ROUND(AVG(rating_generale), 1) as media_generale,
                    SUM(CASE WHEN rating_generale >= 4 THEN 1 ELSE 0 END) as positive,
                    SUM(CASE WHEN rating_generale <= 2 THEN 1 ELSE 0 END) as negative,
                    SUM(CASE WHEN rating_generale = 3 THEN 1 ELSE 0 END) as neutre,
                    (SELECT COUNT(*) FROM recensione_risposte) as totale_risposte,
                    (SELECT COUNT(DISTINCT recensione_id) FROM recensione_risposte) as recensioni_con_risposta
                  FROM recensioni";
        $result = $this->db->query($query);
        $stats = $result->fetch_assoc();
        
        // Calcola senza risposta
        $stats['senza_risposta'] = ($stats['totale'] ?? 0) - ($stats['recensioni_con_risposta'] ?? 0);
        
        return $stats;
    }
    
    // ============================================================================
    // RECENSIONI - Dettaglio singola recensione
    // ============================================================================
    
    public function getRecensioneById($recensioneId) {
        $query = "SELECT r.*, 
                    CONCAT(u.nome, ' ', u.cognome) as utente_nome, 
                    u.email as utente_email,
                    u.telefono as utente_telefono,
                    u.created_at as utente_registrato,
                    c.nome as campo_nome, 
                    c.location as campo_location,
                    s.nome as sport_nome,
                    s.icona as sport_icona,
                    p.data_prenotazione,
                    p.ora_inizio,
                    p.ora_fine,
                    (SELECT COUNT(*) FROM recensioni WHERE user_id = r.user_id) as utente_tot_recensioni,
                    (SELECT ROUND(AVG(rating_generale), 1) FROM recensioni WHERE user_id = r.user_id) as utente_media_rating,
                    (SELECT COUNT(*) FROM prenotazioni WHERE user_id = r.user_id AND stato = 'completata') as utente_prenotazioni
                  FROM recensioni r
                  JOIN users u ON r.user_id = u.user_id
                  JOIN campi_sportivi c ON r.campo_id = c.campo_id
                  JOIN sport s ON c.sport_id = s.sport_id
                  LEFT JOIN prenotazioni p ON r.prenotazione_id = p.prenotazione_id
                  WHERE r.recensione_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $recensioneId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    // ============================================================================
    // RECENSIONI - Lista completa con filtri avanzati per admin
    // ============================================================================
    
    public function getAllRecensioniAdmin($filtri = []) {
        $query = "SELECT r.*, 
                    CONCAT(u.nome, ' ', u.cognome) as utente_nome, 
                    u.email as utente_email,
                    c.nome as campo_nome, 
                    s.nome as sport_nome,
                    s.icona as sport_icona,
                    (SELECT COUNT(*) FROM recensione_risposte WHERE recensione_id = r.recensione_id) as num_risposte,
                    (SELECT MAX(created_at) FROM recensione_risposte WHERE recensione_id = r.recensione_id) as ultima_risposta
                  FROM recensioni r
                  JOIN users u ON r.user_id = u.user_id
                  JOIN campi_sportivi c ON r.campo_id = c.campo_id
                  JOIN sport s ON c.sport_id = s.sport_id
                  WHERE 1=1";
        
        $params = [];
        $types = '';
        
        // Filtro campo
        if (!empty($filtri['campo_id'])) {
            $query .= " AND r.campo_id = ?";
            $params[] = intval($filtri['campo_id']);
            $types .= 'i';
        }
        
        // Filtro sport
        if (!empty($filtri['sport_id'])) {
            $query .= " AND c.sport_id = ?";
            $params[] = intval($filtri['sport_id']);
            $types .= 'i';
        }
        
        // Filtro rating
        if (!empty($filtri['rating'])) {
            if ($filtri['rating'] === 'positive') {
                $query .= " AND r.rating_generale >= 4";
            } elseif ($filtri['rating'] === 'negative') {
                $query .= " AND r.rating_generale <= 2";
            } elseif ($filtri['rating'] === 'neutre') {
                $query .= " AND r.rating_generale = 3";
            } elseif (is_numeric($filtri['rating'])) {
                $query .= " AND r.rating_generale = ?";
                $params[] = intval($filtri['rating']);
                $types .= 'i';
            }
        }
        
        // Filtro con/senza risposta
        if (!empty($filtri['risposta'])) {
            if ($filtri['risposta'] === 'con') {
                $query .= " AND (SELECT COUNT(*) FROM recensione_risposte WHERE recensione_id = r.recensione_id) > 0";
            } elseif ($filtri['risposta'] === 'senza') {
                $query .= " AND (SELECT COUNT(*) FROM recensione_risposte WHERE recensione_id = r.recensione_id) = 0";
            }
        }
        
        // Filtro ricerca
        if (!empty($filtri['search'])) {
            $query .= " AND (CONCAT(u.nome, ' ', u.cognome) LIKE ? 
                        OR u.email LIKE ? 
                        OR c.nome LIKE ?
                        OR r.commento LIKE ?)";
            $searchTerm = '%' . $filtri['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= 'ssss';
        }
        
        // Ordinamento
        $orderBy = " ORDER BY ";
        switch ($filtri['ordina'] ?? 'recenti') {
            case 'vecchie':
                $orderBy .= "r.created_at ASC";
                break;
            case 'rating_alto':
                $orderBy .= "r.rating_generale DESC, r.created_at DESC";
                break;
            case 'rating_basso':
                $orderBy .= "r.rating_generale ASC, r.created_at DESC";
                break;
            case 'campo':
                $orderBy .= "c.nome ASC, r.created_at DESC";
                break;
            default: // recenti
                $orderBy .= "r.created_at DESC";
        }
        $query .= $orderBy;
        
        if (!empty($params)) {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->db->query($query);
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // RECENSIONI - Elimina risposta admin
    // ============================================================================
    
    public function deleteRecensioneRisposta($rispostaId) {
        $query = "DELETE FROM recensione_risposte WHERE risposta_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $rispostaId);
        return $stmt->execute();
    }

    
    // ============================================================================
    // ============================================================================
    // ANALYTICS - Metodi per sezione Analytics
    // ============================================================================
    // ============================================================================
    
    // ============================================================================
    // ANALYTICS - KPI principali con confronto periodo precedente
    // ============================================================================
    
    public function getAnalyticsKPI($dataInizio, $dataFine, $dataInizioPrec, $dataFinePrec) {
        // Periodo corrente
        $queryCorrente = "SELECT 
            COUNT(*) as prenotazioni_totali,
            COUNT(CASE WHEN stato = 'completata' THEN 1 END) as completate,
            COUNT(CASE WHEN stato = 'cancellata' THEN 1 END) as cancellate,
            COUNT(CASE WHEN stato = 'no_show' THEN 1 END) as noshow,
            COUNT(DISTINCT user_id) as utenti_attivi,
            SUM(TIMESTAMPDIFF(HOUR, ora_inizio, ora_fine)) as ore_prenotate
        FROM prenotazioni 
        WHERE data_prenotazione BETWEEN ? AND ?";
        
        $stmt = $this->db->prepare($queryCorrente);
        $stmt->bind_param('ss', $dataInizio, $dataFine);
        $stmt->execute();
        $corrente = $stmt->get_result()->fetch_assoc();
        
        // Periodo precedente
        $stmt = $this->db->prepare($queryCorrente);
        $stmt->bind_param('ss', $dataInizioPrec, $dataFinePrec);
        $stmt->execute();
        $precedente = $stmt->get_result()->fetch_assoc();
        
        // Rating medio
        $queryRating = "SELECT ROUND(AVG(rating_generale), 1) as rating 
                        FROM recensioni WHERE created_at BETWEEN ? AND ?";
        $stmt = $this->db->prepare($queryRating);
        $stmt->bind_param('ss', $dataInizio, $dataFine);
        $stmt->execute();
        $ratingCorrente = $stmt->get_result()->fetch_assoc()['rating'] ?? 0;
        
        $stmt = $this->db->prepare($queryRating);
        $stmt->bind_param('ss', $dataInizioPrec, $dataFinePrec);
        $stmt->execute();
        $ratingPrec = $stmt->get_result()->fetch_assoc()['rating'] ?? 0;
        
        // Calcoli
        $prenotazioniTotali = intval($corrente['prenotazioni_totali'] ?? 0);
        $completate = intval($corrente['completate'] ?? 0);
        $noshow = intval($corrente['noshow'] ?? 0);
        $orePrenotate = intval($corrente['ore_prenotate'] ?? 0);
        $utentiAttivi = intval($corrente['utenti_attivi'] ?? 0);
        
        $tassoCompletamento = $prenotazioniTotali > 0 ? round(($completate / $prenotazioniTotali) * 100) : 0;
        $noshowRate = $prenotazioniTotali > 0 ? round(($noshow / $prenotazioniTotali) * 100) : 0;
        
        // Variazioni percentuali
        $precTotali = intval($precedente['prenotazioni_totali'] ?? 0);
        $precCompletate = intval($precedente['completate'] ?? 0);
        $precNoshow = intval($precedente['noshow'] ?? 0);
        $precOre = intval($precedente['ore_prenotate'] ?? 0);
        $precUtenti = intval($precedente['utenti_attivi'] ?? 0);
        
        $precTassoCompletamento = $precTotali > 0 ? round(($precCompletate / $precTotali) * 100) : 0;
        $precNoshowRate = $precTotali > 0 ? round(($precNoshow / $precTotali) * 100) : 0;
        
        return [
            'prenotazioni_totali' => $prenotazioniTotali,
            'prenotazioni_var' => $this->calcolaVariazione($prenotazioniTotali, $precTotali),
            'tasso_completamento' => $tassoCompletamento,
            'completamento_var' => $tassoCompletamento - $precTassoCompletamento,
            'utenti_attivi' => $utentiAttivi,
            'utenti_var' => $this->calcolaVariazione($utentiAttivi, $precUtenti),
            'noshow_rate' => $noshowRate,
            'noshow_var' => $noshowRate - $precNoshowRate,
            'ore_prenotate' => $orePrenotate,
            'ore_var' => $this->calcolaVariazione($orePrenotate, $precOre),
            'rating_medio' => $ratingCorrente ?: 0,
            'rating_var' => $this->calcolaVariazione($ratingCorrente, $ratingPrec)
        ];
    }
    
    // ============================================================================
    // ANALYTICS - Calcola variazione percentuale
    // ============================================================================
    
    private function calcolaVariazione($corrente, $precedente) {
        if ($precedente == 0) {
            return $corrente > 0 ? 100 : 0;
        }
        return round((($corrente - $precedente) / $precedente) * 100);
    }
    
    // ============================================================================
    // ANALYTICS - Trend prenotazioni giornaliero
    // ============================================================================
    
    public function getAnalyticsTrend($dataInizio, $dataFine) {
        $query = "SELECT 
            DATE(data_prenotazione) as data,
            COUNT(CASE WHEN stato = 'completata' THEN 1 END) as completate,
            COUNT(CASE WHEN stato = 'cancellata' THEN 1 END) as cancellate,
            COUNT(CASE WHEN stato = 'no_show' THEN 1 END) as noshow
        FROM prenotazioni 
        WHERE data_prenotazione BETWEEN ? AND ?
        GROUP BY DATE(data_prenotazione)
        ORDER BY data ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ss', $dataInizio, $dataFine);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Formatta per Chart.js
        $labels = [];
        $completate = [];
        $cancellate = [];
        $noshow = [];
        
        foreach ($result as $row) {
            $labels[] = date('d/m', strtotime($row['data']));
            $completate[] = intval($row['completate']);
            $cancellate[] = intval($row['cancellate']);
            $noshow[] = intval($row['noshow']);
        }
        
        return [
            'labels' => $labels,
            'completate' => $completate,
            'cancellate' => $cancellate,
            'noshow' => $noshow
        ];
    }
    
    // ============================================================================
    // ANALYTICS - Heatmap giorno×ora
    // ============================================================================
    
    public function getAnalyticsHeatmap($dataInizio, $dataFine) {
        $query = "SELECT 
            DAYOFWEEK(data_prenotazione) as giorno_settimana,
            HOUR(ora_inizio) as ora,
            COUNT(*) as count
        FROM prenotazioni 
        WHERE data_prenotazione BETWEEN ? AND ?
            AND stato IN ('confermata', 'completata')
        GROUP BY DAYOFWEEK(data_prenotazione), HOUR(ora_inizio)
        ORDER BY giorno_settimana, ora";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ss', $dataInizio, $dataFine);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Converti DAYOFWEEK (1=domenica) a 1=lunedì
        $heatmap = [];
        foreach ($result as $row) {
            $giorno = $row['giorno_settimana'];
            // MySQL: 1=domenica, 2=lunedì, ... 7=sabato
            // Convertiamo a: 1=lunedì, ... 7=domenica
            $giornoConvertito = $giorno == 1 ? 7 : $giorno - 1;
            
            $heatmap[] = [
                'giorno' => $giornoConvertito,
                'ora' => str_pad($row['ora'], 2, '0', STR_PAD_LEFT),
                'count' => intval($row['count'])
            ];
        }
        
        return $heatmap;
    }
    
    // ============================================================================
    // ANALYTICS - Utilizzo campi (percentuale relativa)
    // ============================================================================
    
    public function getAnalyticsUtilizzoCampi($dataInizio, $dataFine) {
        $query = "SELECT 
            c.campo_id,
            c.nome,
            s.nome as sport,
            COUNT(p.prenotazione_id) as prenotazioni,
            COALESCE(SUM(TIMESTAMPDIFF(HOUR, p.ora_inizio, p.ora_fine)), 0) as ore_utilizzate
        FROM campi_sportivi c
        JOIN sport s ON c.sport_id = s.sport_id
        LEFT JOIN prenotazioni p ON c.campo_id = p.campo_id 
            AND p.data_prenotazione BETWEEN ? AND ?
            AND p.stato IN ('confermata', 'completata')
        WHERE c.stato != 'chiuso'
        GROUP BY c.campo_id, c.nome, s.nome
        ORDER BY prenotazioni DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ss', $dataInizio, $dataFine);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Trova il massimo per calcolare percentuale relativa
        $maxPrenotazioni = 0;
        foreach ($result as $row) {
            if (intval($row['prenotazioni']) > $maxPrenotazioni) {
                $maxPrenotazioni = intval($row['prenotazioni']);
            }
        }
        
        // Calcola percentuali relative (campo più usato = 100%)
        foreach ($result as &$row) {
            $prenotazioni = intval($row['prenotazioni']);
            $row['percentuale'] = $maxPrenotazioni > 0 
                ? round(($prenotazioni / $maxPrenotazioni) * 100, 1) 
                : 0;
        }
        
        return $result;
    }
    
    // ============================================================================
    // ANALYTICS - Distribuzione prenotazioni per sport
    // ============================================================================
    
    public function getAnalyticsDistribuzioneSport($dataInizio, $dataFine) {
        $query = "SELECT 
            s.nome as sport,
            s.icona,
            COUNT(p.prenotazione_id) as prenotazioni
        FROM sport s
        LEFT JOIN campi_sportivi c ON s.sport_id = c.sport_id
        LEFT JOIN prenotazioni p ON c.campo_id = p.campo_id 
            AND p.data_prenotazione BETWEEN ? AND ?
            AND p.stato IN ('confermata', 'completata')
        GROUP BY s.sport_id, s.nome, s.icona
        HAVING prenotazioni > 0
        ORDER BY prenotazioni DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ss', $dataInizio, $dataFine);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // ANALYTICS - Export prenotazioni CSV
    // ============================================================================
    
    public function getPrenotazioniExport($dataInizio, $dataFine) {
        $query = "SELECT 
            p.prenotazione_id,
            p.data_prenotazione,
            p.ora_inizio,
            p.ora_fine,
            c.nome as campo_nome,
            s.nome as sport_nome,
            CONCAT(u.nome, ' ', u.cognome) as utente_nome,
            u.email,
            p.stato,
            p.check_in_effettuato
        FROM prenotazioni p
        JOIN campi_sportivi c ON p.campo_id = c.campo_id
        JOIN sport s ON c.sport_id = s.sport_id
        JOIN users u ON p.user_id = u.user_id
        WHERE p.data_prenotazione BETWEEN ? AND ?
        ORDER BY p.data_prenotazione DESC, p.ora_inizio ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ss', $dataInizio, $dataFine);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }


}
?>