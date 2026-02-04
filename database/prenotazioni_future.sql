-- =====================================================
-- PRENOTAZIONI FUTURE - Per Test (VERSIONE COMPLETA)
-- =====================================================
-- Genera prenotazioni per OGGI + PROSSIMI 7 giorni
-- Le date e gli stati sono DINAMICI
-- =====================================================

USE `campus_sports_arena`;

-- =====================================================
-- PULIZIA AUTOMATICA
-- =====================================================
DELETE FROM prenotazioni WHERE data_prenotazione >= CURDATE();

-- =====================================================
-- PRENOTAZIONI OGGI (stato dinamico in base all'ora)
-- =====================================================

-- Mario Verdi (user_id=4) - OGGI
INSERT INTO `prenotazioni` (`user_id`, `campo_id`, `data_prenotazione`, `ora_inizio`, `ora_fine`, `num_partecipanti`, `note`, `stato`, `check_in_effettuato`, `created_at`)
SELECT 4, 1, CURDATE(), '09:00:00', '10:00:00', 8, 'Calcetto mattutino',
       CASE WHEN CURTIME() > '10:00:00' THEN 'completata' ELSE 'confermata' END,
       CASE WHEN CURTIME() > '10:00:00' THEN 1 ELSE 0 END,
       DATE_SUB(NOW(), INTERVAL 2 DAY);

INSERT INTO `prenotazioni` (`user_id`, `campo_id`, `data_prenotazione`, `ora_inizio`, `ora_fine`, `num_partecipanti`, `note`, `stato`, `check_in_effettuato`, `created_at`)
SELECT 4, 8, CURDATE(), '14:00:00', '15:00:00', 4, 'Padel pausa pranzo',
       CASE WHEN CURTIME() > '15:00:00' THEN 'completata' ELSE 'confermata' END,
       CASE WHEN CURTIME() > '15:00:00' THEN 1 ELSE 0 END,
       DATE_SUB(NOW(), INTERVAL 2 DAY);

INSERT INTO `prenotazioni` (`user_id`, `campo_id`, `data_prenotazione`, `ora_inizio`, `ora_fine`, `num_partecipanti`, `note`, `stato`, `check_in_effettuato`, `created_at`)
SELECT 4, 4, CURDATE(), '18:00:00', '19:00:00', 6, 'Basket serale',
       CASE WHEN CURTIME() > '19:00:00' THEN 'completata' ELSE 'confermata' END,
       CASE WHEN CURTIME() > '19:00:00' THEN 1 ELSE 0 END,
       DATE_SUB(NOW(), INTERVAL 2 DAY);

-- Giulia Neri (user_id=5) - OGGI
INSERT INTO `prenotazioni` (`user_id`, `campo_id`, `data_prenotazione`, `ora_inizio`, `ora_fine`, `num_partecipanti`, `note`, `stato`, `check_in_effettuato`, `created_at`)
SELECT 5, 1, CURDATE(), '10:00:00', '11:00:00', 10, 'Torneo calcetto',
       CASE WHEN CURTIME() > '11:00:00' THEN 'completata' ELSE 'confermata' END,
       CASE WHEN CURTIME() > '11:00:00' THEN 1 ELSE 0 END,
       DATE_SUB(NOW(), INTERVAL 2 DAY);

INSERT INTO `prenotazioni` (`user_id`, `campo_id`, `data_prenotazione`, `ora_inizio`, `ora_fine`, `num_partecipanti`, `note`, `stato`, `check_in_effettuato`, `created_at`)
SELECT 5, 6, CURDATE(), '16:00:00', '17:00:00', 2, 'Tennis singolo',
       CASE WHEN CURTIME() > '17:00:00' THEN 'completata' ELSE 'confermata' END,
       CASE WHEN CURTIME() > '17:00:00' THEN 1 ELSE 0 END,
       DATE_SUB(NOW(), INTERVAL 2 DAY);

-- Luca Gialli (user_id=6) - OGGI
INSERT INTO `prenotazioni` (`user_id`, `campo_id`, `data_prenotazione`, `ora_inizio`, `ora_fine`, `num_partecipanti`, `note`, `stato`, `check_in_effettuato`, `created_at`)
SELECT 6, 5, CURDATE(), '11:00:00', '12:00:00', 10, 'Pallavolo',
       CASE WHEN CURTIME() > '12:00:00' THEN 'completata' ELSE 'confermata' END,
       CASE WHEN CURTIME() > '12:00:00' THEN 1 ELSE 0 END,
       DATE_SUB(NOW(), INTERVAL 2 DAY);

INSERT INTO `prenotazioni` (`user_id`, `campo_id`, `data_prenotazione`, `ora_inizio`, `ora_fine`, `num_partecipanti`, `note`, `stato`, `check_in_effettuato`, `created_at`)
SELECT 6, 8, CURDATE(), '17:00:00', '18:00:00', 4, 'Padel con amici',
       CASE WHEN CURTIME() > '18:00:00' THEN 'completata' ELSE 'confermata' END,
       CASE WHEN CURTIME() > '18:00:00' THEN 1 ELSE 0 END,
       DATE_SUB(NOW(), INTERVAL 2 DAY);

-- Sara Blu (user_id=7) - OGGI
INSERT INTO `prenotazioni` (`user_id`, `campo_id`, `data_prenotazione`, `ora_inizio`, `ora_fine`, `num_partecipanti`, `note`, `stato`, `check_in_effettuato`, `created_at`)
SELECT 7, 10, CURDATE(), '09:00:00', '10:00:00', 4, 'Ping pong mattina',
       CASE WHEN CURTIME() > '10:00:00' THEN 'completata' ELSE 'confermata' END,
       CASE WHEN CURTIME() > '10:00:00' THEN 1 ELSE 0 END,
       DATE_SUB(NOW(), INTERVAL 2 DAY);

INSERT INTO `prenotazioni` (`user_id`, `campo_id`, `data_prenotazione`, `ora_inizio`, `ora_fine`, `num_partecipanti`, `note`, `stato`, `check_in_effettuato`, `created_at`)
SELECT 7, 4, CURDATE(), '15:00:00', '16:00:00', 8, 'Basket pomeriggio',
       CASE WHEN CURTIME() > '16:00:00' THEN 'completata' ELSE 'confermata' END,
       CASE WHEN CURTIME() > '16:00:00' THEN 1 ELSE 0 END,
       DATE_SUB(NOW(), INTERVAL 2 DAY);

-- Andrea Rosa (user_id=8) - OGGI
INSERT INTO `prenotazioni` (`user_id`, `campo_id`, `data_prenotazione`, `ora_inizio`, `ora_fine`, `num_partecipanti`, `note`, `stato`, `check_in_effettuato`, `created_at`)
SELECT 8, 2, CURDATE(), '08:00:00', '09:00:00', 10, 'Calcetto alba',
       CASE WHEN CURTIME() > '09:00:00' THEN 'completata' ELSE 'confermata' END,
       CASE WHEN CURTIME() > '09:00:00' THEN 1 ELSE 0 END,
       DATE_SUB(NOW(), INTERVAL 2 DAY);

INSERT INTO `prenotazioni` (`user_id`, `campo_id`, `data_prenotazione`, `ora_inizio`, `ora_fine`, `num_partecipanti`, `note`, `stato`, `check_in_effettuato`, `created_at`)
SELECT 8, 3, CURDATE(), '14:00:00', '15:00:00', 14, 'Calcio 7',
       CASE WHEN CURTIME() > '15:00:00' THEN 'completata' ELSE 'confermata' END,
       CASE WHEN CURTIME() > '15:00:00' THEN 1 ELSE 0 END,
       DATE_SUB(NOW(), INTERVAL 2 DAY);

INSERT INTO `prenotazioni` (`user_id`, `campo_id`, `data_prenotazione`, `ora_inizio`, `ora_fine`, `num_partecipanti`, `note`, `stato`, `check_in_effettuato`, `created_at`)
SELECT 8, 1, CURDATE(), '19:00:00', '20:00:00', 10, 'Calcetto serale',
       CASE WHEN CURTIME() > '20:00:00' THEN 'completata' ELSE 'confermata' END,
       CASE WHEN CURTIME() > '20:00:00' THEN 1 ELSE 0 END,
       DATE_SUB(NOW(), INTERVAL 2 DAY);

-- Alessandro Romano (user_id=10) - OGGI
INSERT INTO `prenotazioni` (`user_id`, `campo_id`, `data_prenotazione`, `ora_inizio`, `ora_fine`, `num_partecipanti`, `note`, `stato`, `check_in_effettuato`, `created_at`)
SELECT 10, 6, CURDATE(), '08:00:00', '09:00:00', 2, 'Tennis mattutino',
       CASE WHEN CURTIME() > '09:00:00' THEN 'completata' ELSE 'confermata' END,
       CASE WHEN CURTIME() > '09:00:00' THEN 1 ELSE 0 END,
       DATE_SUB(NOW(), INTERVAL 2 DAY);

INSERT INTO `prenotazioni` (`user_id`, `campo_id`, `data_prenotazione`, `ora_inizio`, `ora_fine`, `num_partecipanti`, `note`, `stato`, `check_in_effettuato`, `created_at`)
SELECT 10, 9, CURDATE(), '18:00:00', '19:00:00', 4, 'Padel serale',
       CASE WHEN CURTIME() > '19:00:00' THEN 'completata' ELSE 'confermata' END,
       CASE WHEN CURTIME() > '19:00:00' THEN 1 ELSE 0 END,
       DATE_SUB(NOW(), INTERVAL 2 DAY);

-- Matteo Greco (user_id=11) - OGGI
INSERT INTO `prenotazioni` (`user_id`, `campo_id`, `data_prenotazione`, `ora_inizio`, `ora_fine`, `num_partecipanti`, `note`, `stato`, `check_in_effettuato`, `created_at`)
SELECT 11, 1, CURDATE(), '11:00:00', '12:00:00', 8, 'Calcetto',
       CASE WHEN CURTIME() > '12:00:00' THEN 'completata' ELSE 'confermata' END,
       CASE WHEN CURTIME() > '12:00:00' THEN 1 ELSE 0 END,
       DATE_SUB(NOW(), INTERVAL 2 DAY);

INSERT INTO `prenotazioni` (`user_id`, `campo_id`, `data_prenotazione`, `ora_inizio`, `ora_fine`, `num_partecipanti`, `note`, `stato`, `check_in_effettuato`, `created_at`)
SELECT 11, 5, CURDATE(), '16:00:00', '17:00:00', 12, 'Volley',
       CASE WHEN CURTIME() > '17:00:00' THEN 'completata' ELSE 'confermata' END,
       CASE WHEN CURTIME() > '17:00:00' THEN 1 ELSE 0 END,
       DATE_SUB(NOW(), INTERVAL 2 DAY);

-- Elena Marini (user_id=12) - OGGI
INSERT INTO `prenotazioni` (`user_id`, `campo_id`, `data_prenotazione`, `ora_inizio`, `ora_fine`, `num_partecipanti`, `note`, `stato`, `check_in_effettuato`, `created_at`)
SELECT 12, 8, CURDATE(), '10:00:00', '11:00:00', 4, 'Padel mattina',
       CASE WHEN CURTIME() > '11:00:00' THEN 'completata' ELSE 'confermata' END,
       CASE WHEN CURTIME() > '11:00:00' THEN 1 ELSE 0 END,
       DATE_SUB(NOW(), INTERVAL 2 DAY);

INSERT INTO `prenotazioni` (`user_id`, `campo_id`, `data_prenotazione`, `ora_inizio`, `ora_fine`, `num_partecipanti`, `note`, `stato`, `check_in_effettuato`, `created_at`)
SELECT 12, 4, CURDATE(), '19:00:00', '20:00:00', 6, 'Basket serale',
       CASE WHEN CURTIME() > '20:00:00' THEN 'completata' ELSE 'confermata' END,
       CASE WHEN CURTIME() > '20:00:00' THEN 1 ELSE 0 END,
       DATE_SUB(NOW(), INTERVAL 2 DAY);

-- =====================================================
-- PRENOTAZIONI DOMANI (+1 giorno)
-- =====================================================
INSERT INTO `prenotazioni` (`user_id`, `campo_id`, `data_prenotazione`, `ora_inizio`, `ora_fine`, `num_partecipanti`, `note`, `stato`, `check_in_effettuato`, `created_at`) VALUES
-- Mario Verdi (user_id=4)
(4, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:00:00', '10:00:00', 8, 'Partitella con amici del corso', 'confermata', 0, NOW()),
(4, 4, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '15:00:00', '16:00:00', 6, 'Allenamento basket', 'confermata', 0, NOW()),
(4, 8, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '18:00:00', '19:00:00', 4, 'Padel serale', 'confermata', 0, NOW()),

-- Giulia Neri (user_id=5)
(5, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:00:00', '11:00:00', 10, 'Torneo interfacoltà', 'confermata', 0, NOW()),
(5, 6, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '14:00:00', '15:00:00', 2, 'Tennis con Marco', 'confermata', 0, NOW()),

-- Luca Gialli (user_id=6)
(6, 6, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '11:00:00', '12:00:00', 2, 'Tennis singolo', 'confermata', 0, NOW()),
(6, 5, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '16:00:00', '17:00:00', 10, 'Pallavolo mista', 'confermata', 0, NOW()),

-- Sara Blu (user_id=7)
(7, 8, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '14:00:00', '15:00:00', 4, 'Padel principianti', 'confermata', 0, NOW()),
(7, 10, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '17:00:00', '18:00:00', 4, 'Ping pong', 'confermata', 0, NOW()),

-- Andrea Rosa (user_id=8)
(8, 5, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '17:00:00', '18:00:00', 12, 'Pallavolo serale', 'confermata', 0, NOW()),
(8, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '19:00:00', '20:00:00', 10, 'Calcetto notturno', 'confermata', 0, NOW()),

-- Alessandro Romano (user_id=10)
(10, 2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '08:00:00', '09:00:00', 8, 'Allenamento mattutino', 'confermata', 0, NOW()),
(10, 3, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '11:00:00', '12:00:00', 14, 'Calcio 7', 'confermata', 0, NOW()),

-- =====================================================
-- PRENOTAZIONI DOPODOMANI (+2 giorni)
-- =====================================================
-- Mario Verdi (user_id=4)
(4, 2, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '10:00:00', '11:00:00', 10, 'Calcetto con colleghi', 'confermata', 0, NOW()),
(4, 6, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '16:00:00', '17:00:00', 4, 'Tennis doppio', 'confermata', 0, NOW()),

-- Giulia Neri (user_id=5)
(5, 4, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '09:00:00', '10:00:00', 6, 'Basket mattina', 'confermata', 0, NOW()),
(5, 8, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '14:00:00', '15:00:00', 4, 'Padel con amiche', 'confermata', 0, NOW()),

-- Luca Gialli (user_id=6)
(6, 1, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '08:00:00', '09:00:00', 8, 'Calcetto alba', 'confermata', 0, NOW()),

-- Sara Blu (user_id=7)
(7, 5, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '11:00:00', '12:00:00', 10, 'Volley amatoriale', 'confermata', 0, NOW()),
(7, 10, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '15:00:00', '16:00:00', 4, 'Ping pong', 'confermata', 0, NOW()),

-- Andrea Rosa (user_id=8)
(8, 3, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '14:00:00', '15:00:00', 14, 'Calcio 7 amichevole', 'confermata', 0, NOW()),

-- Matteo Greco (user_id=11)
(11, 1, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '09:00:00', '10:00:00', 10, 'Torneo calcetto', 'confermata', 0, NOW()),
(11, 8, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '17:00:00', '18:00:00', 4, 'Padel', 'confermata', 0, NOW()),

-- Elena Marini (user_id=12)
(12, 4, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '11:00:00', '12:00:00', 8, 'Basket 3vs3', 'confermata', 0, NOW()),
(12, 6, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '18:00:00', '19:00:00', 2, 'Tennis serale', 'confermata', 0, NOW()),

-- =====================================================
-- PRENOTAZIONI +3 giorni
-- =====================================================
-- Mario Verdi (user_id=4)
(4, 6, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '14:00:00', '15:00:00', 2, 'Lezione tennis', 'confermata', 0, NOW()),
(4, 1, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '18:00:00', '19:00:00', 10, 'Calcetto serale', 'confermata', 0, NOW()),

-- Giulia Neri (user_id=5)
(5, 5, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '10:00:00', '11:00:00', 12, 'Pallavolo', 'confermata', 0, NOW()),

-- Luca Gialli (user_id=6)
(6, 8, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '09:00:00', '10:00:00', 4, 'Padel mattutino', 'confermata', 0, NOW()),
(6, 4, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '16:00:00', '17:00:00', 8, 'Basket pomeridiano', 'confermata', 0, NOW()),

-- Sara Blu (user_id=7)
(7, 1, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '11:00:00', '12:00:00', 8, 'Calcetto misto', 'confermata', 0, NOW()),

-- Alessandro Romano (user_id=10)
(10, 6, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '08:00:00', '09:00:00', 4, 'Tennis doppio', 'confermata', 0, NOW()),

-- Sofia Costa (user_id=13)
(13, 10, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '15:00:00', '16:00:00', 4, 'Ping pong torneo', 'confermata', 0, NOW()),
(13, 5, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '17:00:00', '18:00:00', 10, 'Volley', 'confermata', 0, NOW()),

-- Lorenzo Martini (user_id=14)
(14, 2, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '10:00:00', '11:00:00', 10, 'Calcetto', 'confermata', 0, NOW()),
(14, 3, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '14:00:00', '15:00:00', 14, 'Calcio 7', 'confermata', 0, NOW()),

-- =====================================================
-- PRENOTAZIONI +4 giorni
-- =====================================================
-- Mario Verdi (user_id=4)
(4, 5, DATE_ADD(CURDATE(), INTERVAL 4 DAY), '11:00:00', '12:00:00', 8, 'Pallavolo con colleghi', 'confermata', 0, NOW()),
(4, 10, DATE_ADD(CURDATE(), INTERVAL 4 DAY), '15:00:00', '16:00:00', 4, 'Ping pong relax', 'confermata', 0, NOW()),

-- Giulia Neri (user_id=5)
(5, 3, DATE_ADD(CURDATE(), INTERVAL 4 DAY), '14:00:00', '15:00:00', 14, 'Partita calcio 7', 'confermata', 0, NOW()),

-- Luca Gialli (user_id=6)
(6, 8, DATE_ADD(CURDATE(), INTERVAL 4 DAY), '16:00:00', '17:00:00', 4, 'Padel', 'confermata', 0, NOW()),

-- Sara Blu (user_id=7)
(7, 9, DATE_ADD(CURDATE(), INTERVAL 4 DAY), '17:00:00', '18:00:00', 4, 'Padel esterno', 'confermata', 0, NOW()),

-- Andrea Rosa (user_id=8)
(8, 1, DATE_ADD(CURDATE(), INTERVAL 4 DAY), '08:00:00', '09:00:00', 10, 'Calcetto mattina', 'confermata', 0, NOW()),
(8, 4, DATE_ADD(CURDATE(), INTERVAL 4 DAY), '10:00:00', '11:00:00', 6, 'Basket', 'confermata', 0, NOW()),

-- Matteo Greco (user_id=11)
(11, 6, DATE_ADD(CURDATE(), INTERVAL 4 DAY), '09:00:00', '10:00:00', 2, 'Tennis singolo', 'confermata', 0, NOW()),

-- Chiara Fontana (user_id=15)
(15, 1, DATE_ADD(CURDATE(), INTERVAL 4 DAY), '10:00:00', '11:00:00', 8, NULL, 'confermata', 0, NOW()),
(15, 5, DATE_ADD(CURDATE(), INTERVAL 4 DAY), '14:00:00', '15:00:00', 12, 'Volley femminile', 'confermata', 0, NOW()),

-- Davide Russo (user_id=16)
(16, 2, DATE_ADD(CURDATE(), INTERVAL 4 DAY), '18:00:00', '19:00:00', 10, 'Calcetto serale', 'confermata', 0, NOW()),

-- =====================================================
-- PRENOTAZIONI +5 giorni
-- =====================================================
-- Mario Verdi (user_id=4)
(4, 8, DATE_ADD(CURDATE(), INTERVAL 5 DAY), '10:00:00', '11:00:00', 4, 'Padel con Sara e Luca', 'confermata', 0, NOW()),
(4, 3, DATE_ADD(CURDATE(), INTERVAL 5 DAY), '15:00:00', '16:00:00', 14, 'Calcio 7 weekend', 'confermata', 0, NOW()),

-- Giulia Neri (user_id=5)
(5, 6, DATE_ADD(CURDATE(), INTERVAL 5 DAY), '09:00:00', '10:00:00', 4, 'Tennis doppio misto', 'confermata', 0, NOW()),
(5, 1, DATE_ADD(CURDATE(), INTERVAL 5 DAY), '17:00:00', '18:00:00', 10, 'Calcetto', 'confermata', 0, NOW()),

-- Luca Gialli (user_id=6)
(6, 4, DATE_ADD(CURDATE(), INTERVAL 5 DAY), '11:00:00', '12:00:00', 8, 'Basket', 'confermata', 0, NOW()),

-- Sara Blu (user_id=7)
(7, 5, DATE_ADD(CURDATE(), INTERVAL 5 DAY), '14:00:00', '15:00:00', 10, 'Pallavolo', 'confermata', 0, NOW()),

-- Andrea Rosa (user_id=8)
(8, 10, DATE_ADD(CURDATE(), INTERVAL 5 DAY), '16:00:00', '17:00:00', 4, 'Ping pong', 'confermata', 0, NOW()),

-- Alessandro Romano (user_id=10)
(10, 1, DATE_ADD(CURDATE(), INTERVAL 5 DAY), '08:00:00', '09:00:00', 10, NULL, 'confermata', 0, NOW()),
(10, 8, DATE_ADD(CURDATE(), INTERVAL 5 DAY), '18:00:00', '19:00:00', 4, 'Padel serale', 'confermata', 0, NOW()),

-- Martina Gallo (user_id=17)
(17, 2, DATE_ADD(CURDATE(), INTERVAL 5 DAY), '10:00:00', '11:00:00', 8, 'Calcetto', 'confermata', 0, NOW()),

-- Filippo Conti (user_id=18)
(18, 6, DATE_ADD(CURDATE(), INTERVAL 5 DAY), '15:00:00', '16:00:00', 2, 'Tennis singolo', 'confermata', 0, NOW()),

-- =====================================================
-- PRENOTAZIONI +6 giorni
-- =====================================================
-- Mario Verdi (user_id=4)
(4, 1, DATE_ADD(CURDATE(), INTERVAL 6 DAY), '16:00:00', '17:00:00', 10, 'Calcetto weekend!', 'confermata', 0, NOW()),
(4, 9, DATE_ADD(CURDATE(), INTERVAL 6 DAY), '11:00:00', '12:00:00', 4, 'Padel esterno', 'confermata', 0, NOW()),

-- Giulia Neri (user_id=5)
(5, 8, DATE_ADD(CURDATE(), INTERVAL 6 DAY), '10:00:00', '11:00:00', 4, 'Padel sabato', 'confermata', 0, NOW()),

-- Luca Gialli (user_id=6)
(6, 3, DATE_ADD(CURDATE(), INTERVAL 6 DAY), '09:00:00', '10:00:00', 14, 'Calcio 7 mattina', 'confermata', 0, NOW()),
(6, 6, DATE_ADD(CURDATE(), INTERVAL 6 DAY), '14:00:00', '15:00:00', 2, 'Tennis', 'confermata', 0, NOW()),

-- Sara Blu (user_id=7)
(7, 4, DATE_ADD(CURDATE(), INTERVAL 6 DAY), '15:00:00', '16:00:00', 6, 'Basket', 'confermata', 0, NOW()),

-- Andrea Rosa (user_id=8)
(8, 5, DATE_ADD(CURDATE(), INTERVAL 6 DAY), '11:00:00', '12:00:00', 12, 'Volley', 'confermata', 0, NOW()),

-- Matteo Greco (user_id=11)
(11, 1, DATE_ADD(CURDATE(), INTERVAL 6 DAY), '08:00:00', '09:00:00', 8, NULL, 'confermata', 0, NOW()),

-- Elena Marini (user_id=12)
(12, 1, DATE_ADD(CURDATE(), INTERVAL 6 DAY), '10:00:00', '11:00:00', 10, 'Torneo sabato', 'confermata', 0, NOW()),

-- Sofia Costa (user_id=13)
(13, 8, DATE_ADD(CURDATE(), INTERVAL 6 DAY), '17:00:00', '18:00:00', 4, 'Padel pomeriggio', 'confermata', 0, NOW()),

-- Valentina Serra (user_id=19)
(19, 10, DATE_ADD(CURDATE(), INTERVAL 6 DAY), '14:00:00', '15:00:00', 4, 'Ping pong', 'confermata', 0, NOW()),

-- =====================================================
-- PRENOTAZIONI +7 giorni
-- =====================================================
-- Mario Verdi (user_id=4)
(4, 3, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '14:00:00', '15:00:00', 14, 'Calcio 7 domenica', 'confermata', 0, NOW()),
(4, 8, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '17:00:00', '18:00:00', 4, 'Padel finale settimana', 'confermata', 0, NOW()),

-- Giulia Neri (user_id=5)
(5, 10, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '16:00:00', '17:00:00', 4, 'Ping pong', 'confermata', 0, NOW()),
(5, 5, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '10:00:00', '11:00:00', 10, 'Volley domenicale', 'confermata', 0, NOW()),

-- Luca Gialli (user_id=6)
(6, 1, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '09:00:00', '10:00:00', 10, 'Calcetto mattina', 'confermata', 0, NOW()),

-- Sara Blu (user_id=7)
(7, 6, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '11:00:00', '12:00:00', 4, 'Tennis doppio', 'confermata', 0, NOW()),

-- Andrea Rosa (user_id=8)
(8, 4, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '15:00:00', '16:00:00', 8, 'Basket domenica', 'confermata', 0, NOW()),

-- Alessandro Romano (user_id=10)
(10, 2, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '08:00:00', '09:00:00', 8, 'Calcetto alba', 'confermata', 0, NOW()),
(10, 9, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '14:00:00', '15:00:00', 4, 'Padel', 'confermata', 0, NOW()),

-- Matteo Greco (user_id=11)
(11, 3, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '10:00:00', '11:00:00', 14, 'Calcio 7', 'confermata', 0, NOW()),

-- Lorenzo Martini (user_id=14)
(14, 8, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '09:00:00', '10:00:00', 4, 'Padel', 'confermata', 0, NOW()),

-- Chiara Fontana (user_id=15)
(15, 6, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '15:00:00', '16:00:00', 2, 'Tennis relax', 'confermata', 0, NOW()),

-- Riccardo Leone (user_id=20)
(20, 1, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '18:00:00', '19:00:00', 10, 'Calcetto serale domenica', 'confermata', 0, NOW()),

-- Alessia Vitale (user_id=21)
(21, 5, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '16:00:00', '17:00:00', 12, 'Pallavolo', 'confermata', 0, NOW());

-- =====================================================
-- NOTIFICHE PER PRENOTAZIONI DI OGGI
-- =====================================================
INSERT INTO `notifiche` (`user_id`, `tipo`, `titolo`, `messaggio`, `link`, `letta`, `created_at`) VALUES
-- Mario Verdi (user_id=4) - OGGI
(4, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A di oggi alle 09:00 è stata confermata.', '/utente/prenotazioni.php', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(4, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 1 di oggi alle 14:00 è stata confermata.', '/utente/prenotazioni.php', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(4, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Palestra Basket di oggi alle 18:00 è stata confermata.', '/utente/prenotazioni.php', 0, DATE_SUB(NOW(), INTERVAL 2 DAY)),

-- Giulia Neri (user_id=5) - OGGI
(5, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A di oggi alle 10:00 è stata confermata.', '/utente/prenotazioni.php', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(5, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Tennis 1 di oggi alle 16:00 è stata confermata.', '/utente/prenotazioni.php', 0, DATE_SUB(NOW(), INTERVAL 2 DAY)),

-- Luca Gialli (user_id=6) - OGGI
(6, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Pallavolo di oggi alle 11:00 è stata confermata.', '/utente/prenotazioni.php', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(6, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 1 di oggi alle 17:00 è stata confermata.', '/utente/prenotazioni.php', 0, DATE_SUB(NOW(), INTERVAL 2 DAY)),

-- Sara Blu (user_id=7) - OGGI
(7, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Sala Ping Pong di oggi alle 09:00 è stata confermata.', '/utente/prenotazioni.php', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(7, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Palestra Basket di oggi alle 15:00 è stata confermata.', '/utente/prenotazioni.php', 0, DATE_SUB(NOW(), INTERVAL 2 DAY)),

-- Andrea Rosa (user_id=8) - OGGI
(8, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto B di oggi alle 08:00 è stata confermata.', '/utente/prenotazioni.php', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(8, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcio 7 di oggi alle 14:00 è stata confermata.', '/utente/prenotazioni.php', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(8, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A di oggi alle 19:00 è stata confermata.', '/utente/prenotazioni.php', 0, DATE_SUB(NOW(), INTERVAL 2 DAY)),

-- Alessandro Romano (user_id=10) - OGGI
(10, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Tennis 1 di oggi alle 08:00 è stata confermata.', '/utente/prenotazioni.php', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(10, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 2 di oggi alle 18:00 è stata confermata.', '/utente/prenotazioni.php', 0, DATE_SUB(NOW(), INTERVAL 2 DAY)),

-- Matteo Greco (user_id=11) - OGGI
(11, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A di oggi alle 11:00 è stata confermata.', '/utente/prenotazioni.php', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(11, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Pallavolo di oggi alle 16:00 è stata confermata.', '/utente/prenotazioni.php', 0, DATE_SUB(NOW(), INTERVAL 2 DAY)),

-- Elena Marini (user_id=12) - OGGI
(12, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 1 di oggi alle 10:00 è stata confermata.', '/utente/prenotazioni.php', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(12, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Palestra Basket di oggi alle 19:00 è stata confermata.', '/utente/prenotazioni.php', 0, DATE_SUB(NOW(), INTERVAL 2 DAY)),

-- =====================================================
-- NOTIFICHE PER PRENOTAZIONI DOMANI (+1)
-- =====================================================
-- Mario Verdi (user_id=4)
(4, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A è stata confermata per domani alle 09:00.', '/utente/prenotazioni.php', 0, NOW()),
(4, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Palestra Basket è stata confermata per domani alle 15:00.', '/utente/prenotazioni.php', 0, NOW()),
(4, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 1 è stata confermata per domani alle 18:00.', '/utente/prenotazioni.php', 0, NOW()),

-- Giulia Neri (user_id=5)
(5, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A è stata confermata per domani alle 10:00.', '/utente/prenotazioni.php', 0, NOW()),
(5, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Tennis 1 è stata confermata per domani alle 14:00.', '/utente/prenotazioni.php', 0, NOW()),

-- Luca Gialli (user_id=6)
(6, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Tennis 1 è stata confermata per domani alle 11:00.', '/utente/prenotazioni.php', 0, NOW()),
(6, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Pallavolo è stata confermata per domani alle 16:00.', '/utente/prenotazioni.php', 0, NOW()),

-- Sara Blu (user_id=7)
(7, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 1 è stata confermata per domani alle 14:00.', '/utente/prenotazioni.php', 0, NOW()),
(7, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Sala Ping Pong è stata confermata per domani alle 17:00.', '/utente/prenotazioni.php', 0, NOW()),

-- Andrea Rosa (user_id=8)
(8, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Pallavolo è stata confermata per domani alle 17:00.', '/utente/prenotazioni.php', 0, NOW()),
(8, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A è stata confermata per domani alle 19:00.', '/utente/prenotazioni.php', 0, NOW()),

-- Alessandro Romano (user_id=10)
(10, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto B è stata confermata per domani alle 08:00.', '/utente/prenotazioni.php', 0, NOW()),
(10, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcio 7 è stata confermata per domani alle 11:00.', '/utente/prenotazioni.php', 0, NOW()),

-- =====================================================
-- NOTIFICHE PER PRENOTAZIONI +2 GIORNI
-- =====================================================
-- Mario Verdi (user_id=4)
(4, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto B è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(4, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Tennis 1 è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),

-- Giulia Neri (user_id=5)
(5, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Palestra Basket è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(5, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 1 è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),

-- Luca Gialli (user_id=6)
(6, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),

-- Sara Blu (user_id=7)
(7, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Pallavolo è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(7, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Sala Ping Pong è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),

-- Andrea Rosa (user_id=8)
(8, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcio 7 è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),

-- Matteo Greco (user_id=11)
(11, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(11, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 1 è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),

-- Elena Marini (user_id=12)
(12, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Palestra Basket è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(12, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Tennis 1 è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),

-- =====================================================
-- NOTIFICHE PER PRENOTAZIONI +3 A +7 GIORNI
-- =====================================================
-- Mario Verdi (user_id=4)
(4, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Tennis 1 è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(4, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(4, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Pallavolo è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(4, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Sala Ping Pong è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(4, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 1 è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(4, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcio 7 è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(4, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A weekend è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(4, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 2 è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(4, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcio 7 domenica è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(4, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 1 finale settimana è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),

-- Giulia Neri (user_id=5)
(5, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Pallavolo è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(5, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcio 7 è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(5, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Tennis 1 è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(5, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(5, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 1 sabato è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(5, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Sala Ping Pong è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(5, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Pallavolo domenicale è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),

-- Luca Gialli (user_id=6)
(6, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 1 è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(6, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Palestra Basket è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(6, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 1 pomeriggio è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(6, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Palestra Basket weekend è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(6, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcio 7 mattina è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(6, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Tennis 1 è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(6, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A domenica è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),

-- Sara Blu (user_id=7)
(7, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(7, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 2 è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(7, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Pallavolo è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(7, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Palestra Basket è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(7, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Tennis 1 domenica è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),

-- Andrea Rosa (user_id=8)
(8, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A mattina è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(8, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Palestra Basket è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(8, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Sala Ping Pong è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(8, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Pallavolo è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(8, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Palestra Basket domenica è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),

-- Altri utenti
(10, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Tennis 1 è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(10, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(10, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 1 serale è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(10, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto B alba è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(10, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 2 è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),

(11, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Tennis 1 è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(11, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(11, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcio 7 è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),

(12, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A torneo è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),

(13, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Sala Ping Pong torneo è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(13, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Pallavolo è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(13, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 1 pomeriggio è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),

(14, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto B è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(14, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcio 7 è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(14, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 1 è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),

(15, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(15, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Pallavolo femminile è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),
(15, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Tennis 1 relax è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),

(16, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto B serale è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),

(17, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto B è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),

(18, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Tennis 1 singolo è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),

(19, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Sala Ping Pong è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),

(20, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A serale domenica è stata confermata.', '/utente/prenotazioni.php', 0, NOW()),

(21, 'prenotazione', 'Prenotazione Confermata', 'La tua prenotazione per Campo Pallavolo è stata confermata.', '/utente/prenotazioni.php', 0, NOW());

-- =====================================================
-- RIEPILOGO FINALE
-- =====================================================
-- Prenotazioni OGGI: 18 (stato dinamico in base all'ora)
-- Prenotazioni DOMANI: 14
-- Prenotazioni +2 giorni: 12
-- Prenotazioni +3 giorni: 12
-- Prenotazioni +4 giorni: 12
-- Prenotazioni +5 giorni: 12
-- Prenotazioni +6 giorni: 10
-- Prenotazioni +7 giorni: 13
-- TOTALE PRENOTAZIONI: ~103
-- TOTALE NOTIFICHE: ~103
-- =====================================================