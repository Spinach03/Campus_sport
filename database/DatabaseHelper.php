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
    // RECENSIONI - Aggiungi risposta admin
    // ============================================================================
    
    public function addRecensioneRisposta($recensioneId, $adminId, $testo) {
        $query = "INSERT INTO recensione_risposte (recensione_id, admin_id, testo) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iis', $recensioneId, $adminId, $testo);
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
    // RECENSIONI - Risposte admin di una recensione
    // ============================================================================
    
    public function getRecensioneRisposte($recensioneId) {
        $query = "SELECT rr.*, CONCAT(u.nome, ' ', u.cognome) as admin_nome
                  FROM recensione_risposte rr
                  JOIN users u ON rr.admin_id = u.user_id
                  WHERE rr.recensione_id = ?
                  ORDER BY rr.created_at ASC";
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

}
?>