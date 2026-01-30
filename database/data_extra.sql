-- =====================================================
-- DATI EXTRA - Campus Sports Arena (CORRETTO)
-- =====================================================
-- Eseguire DOPO aver eseguito data.sql e tabelle_mancanti.sql
-- Contiene: prenotazioni, recensioni, segnalazioni, badges, notifiche
-- =====================================================

USE `campus_sports_arena`;

-- -----------------------------------------------------
-- PULIZIA TABELLE (nel caso di reimportazione)
-- -----------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM `sanzioni`;
DELETE FROM `penalty_log`;
DELETE FROM `notifiche`;
DELETE FROM `user_badges`;
DELETE FROM `segnalazioni`;
DELETE FROM `recensioni`;
DELETE FROM `prenotazioni`;
SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------
-- PRENOTAZIONI (70 prenotazioni)
-- Mix di: passate completate, passate no_show, presenti, future
-- -----------------------------------------------------

-- Prenotazioni PASSATE COMPLETATE (50)
INSERT INTO `prenotazioni` (`prenotazione_id`, `user_id`, `campo_id`, `data_prenotazione`, `ora_inizio`, `ora_fine`, `num_partecipanti`, `stato`, `check_in_effettuato`, `ora_check_in`, `note`, `created_at`) VALUES
-- Gennaio 2025
(1, 4, 1, '2025-01-06', '10:00:00', '11:00:00', 10, 'completata', 1, '2025-01-06 09:55:00', 'Partita amichevole', '2025-01-04 14:30:00'),
(2, 5, 4, '2025-01-06', '14:00:00', '15:00:00', 8, 'completata', 1, '2025-01-06 13:50:00', NULL, '2025-01-05 09:00:00'),
(3, 7, 8, '2025-01-07', '18:00:00', '19:00:00', 4, 'completata', 1, '2025-01-07 17:58:00', 'Padel con amici', '2025-01-05 16:00:00'),
(4, 10, 2, '2025-01-08', '16:00:00', '17:00:00', 10, 'completata', 1, '2025-01-08 15:52:00', NULL, '2025-01-06 11:00:00'),
(5, 12, 6, '2025-01-08', '09:00:00', '10:00:00', 2, 'completata', 1, '2025-01-08 08:55:00', 'Tennis singolo', '2025-01-07 08:00:00'),
(6, 14, 1, '2025-01-09', '18:00:00', '19:00:00', 10, 'completata', 1, '2025-01-09 17:48:00', NULL, '2025-01-07 20:00:00'),
(7, 6, 3, '2025-01-10', '15:00:00', '16:00:00', 14, 'completata', 1, '2025-01-10 14:55:00', 'Calcio a 7', '2025-01-08 12:00:00'),
(8, 8, 5, '2025-01-10', '17:00:00', '18:00:00', 12, 'completata', 1, '2025-01-10 16:50:00', 'Pallavolo misto', '2025-01-09 10:00:00'),
(9, 11, 10, '2025-01-11', '14:00:00', '15:00:00', 4, 'completata', 1, '2025-01-11 13:58:00', 'Ping pong', '2025-01-09 15:00:00'),
(10, 13, 9, '2025-01-11', '10:00:00', '11:00:00', 4, 'completata', 1, '2025-01-11 09:52:00', NULL, '2025-01-10 08:00:00'),
-- Seconda settimana gennaio
(11, 15, 8, '2025-01-13', '19:00:00', '20:00:00', 4, 'completata', 1, '2025-01-13 18:55:00', NULL, '2025-01-11 14:00:00'),
(12, 17, 4, '2025-01-13', '20:00:00', '21:00:00', 10, 'completata', 1, '2025-01-13 19:50:00', 'Basket serale', '2025-01-12 09:00:00'),
(13, 4, 6, '2025-01-14', '11:00:00', '12:00:00', 4, 'completata', 1, '2025-01-14 10:55:00', 'Tennis doppio', '2025-01-12 16:00:00'),
(14, 19, 1, '2025-01-14', '17:00:00', '18:00:00', 10, 'completata', 1, '2025-01-14 16:52:00', NULL, '2025-01-13 11:00:00'),
(15, 20, 5, '2025-01-15', '18:00:00', '19:00:00', 12, 'completata', 1, '2025-01-15 17:58:00', NULL, '2025-01-13 19:00:00'),
(16, 22, 2, '2025-01-15', '15:00:00', '16:00:00', 10, 'completata', 1, '2025-01-15 14:50:00', 'Calcetto', '2025-01-14 08:00:00'),
(17, 24, 9, '2025-01-16', '16:00:00', '17:00:00', 4, 'completata', 1, '2025-01-16 15:55:00', 'Padel', '2025-01-14 14:00:00'),
(18, 5, 3, '2025-01-16', '10:00:00', '11:00:00', 14, 'completata', 1, '2025-01-16 09:52:00', NULL, '2025-01-15 07:00:00'),
(19, 26, 10, '2025-01-17', '13:00:00', '14:00:00', 4, 'completata', 1, '2025-01-17 12:58:00', 'Ping pong', '2025-01-15 18:00:00'),
(20, 28, 4, '2025-01-17', '19:00:00', '20:00:00', 10, 'completata', 1, '2025-01-17 18:48:00', NULL, '2025-01-16 10:00:00'),
-- Terza/quarta settimana gennaio
(21, 7, 1, '2025-01-18', '11:00:00', '12:00:00', 10, 'completata', 1, '2025-01-18 10:55:00', NULL, '2025-01-16 15:00:00'),
(22, 30, 8, '2025-01-18', '17:00:00', '18:00:00', 4, 'completata', 1, '2025-01-18 16:52:00', 'Padel principianti', '2025-01-17 09:00:00'),
(23, 31, 5, '2025-01-20', '14:00:00', '15:00:00', 12, 'completata', 1, '2025-01-20 13:58:00', NULL, '2025-01-18 11:00:00'),
(24, 32, 6, '2025-01-20', '10:00:00', '11:00:00', 2, 'completata', 1, '2025-01-20 09:55:00', 'Allenamento tennis', '2025-01-19 08:00:00'),
(25, 33, 2, '2025-01-21', '16:00:00', '17:00:00', 10, 'completata', 1, '2025-01-21 15:50:00', NULL, '2025-01-19 14:00:00'),
(26, 4, 9, '2025-01-21', '18:00:00', '19:00:00', 4, 'completata', 1, '2025-01-21 17:55:00', 'Padel', '2025-01-20 10:00:00'),
(27, 10, 4, '2025-01-22', '20:00:00', '21:00:00', 10, 'completata', 1, '2025-01-22 19:52:00', 'Basket', '2025-01-20 16:00:00'),
(28, 12, 1, '2025-01-22', '09:00:00', '10:00:00', 10, 'completata', 1, '2025-01-22 08:55:00', NULL, '2025-01-21 07:00:00'),
(29, 14, 10, '2025-01-23', '15:00:00', '16:00:00', 4, 'completata', 1, '2025-01-23 14:58:00', NULL, '2025-01-21 18:00:00'),
(30, 16, 3, '2025-01-23', '11:00:00', '12:00:00', 14, 'completata', 1, '2025-01-23 10:52:00', 'Calcio 7', '2025-01-22 09:00:00'),
-- Fine gennaio
(31, 18, 8, '2025-01-24', '19:00:00', '20:00:00', 4, 'completata', 1, '2025-01-24 18:55:00', NULL, '2025-01-22 14:00:00'),
(32, 5, 5, '2025-01-24', '16:00:00', '17:00:00', 12, 'completata', 1, '2025-01-24 15:50:00', 'Pallavolo', '2025-01-23 08:00:00'),
(33, 21, 2, '2025-01-25', '10:00:00', '11:00:00', 10, 'completata', 1, '2025-01-25 09:55:00', NULL, '2025-01-23 15:00:00'),
(34, 23, 6, '2025-01-25', '14:00:00', '15:00:00', 4, 'completata', 1, '2025-01-25 13:52:00', 'Tennis doppio', '2025-01-24 10:00:00'),
(35, 25, 1, '2025-01-27', '17:00:00', '18:00:00', 10, 'completata', 1, '2025-01-27 16:55:00', NULL, '2025-01-25 11:00:00'),
(36, 27, 4, '2025-01-27', '18:00:00', '19:00:00', 10, 'completata', 1, '2025-01-27 17:50:00', 'Basket', '2025-01-26 09:00:00'),
(37, 7, 9, '2025-01-28', '20:00:00', '21:00:00', 4, 'completata', 1, '2025-01-28 19:58:00', NULL, '2025-01-26 16:00:00'),
(38, 8, 10, '2025-01-28', '13:00:00', '14:00:00', 4, 'completata', 1, '2025-01-28 12:55:00', 'Ping pong', '2025-01-27 08:00:00'),
(39, 11, 3, '2025-01-29', '15:00:00', '16:00:00', 14, 'completata', 1, '2025-01-29 14:52:00', NULL, '2025-01-27 14:00:00'),
(40, 13, 8, '2025-01-29', '18:00:00', '19:00:00', 4, 'completata', 1, '2025-01-29 17:55:00', 'Padel', '2025-01-28 10:00:00'),
-- Prenotazioni NO_SHOW (10)
(41, 6, 1, '2025-01-10', '09:00:00', '10:00:00', 10, 'no_show', 0, NULL, NULL, '2025-01-08 10:00:00'),
(42, 6, 4, '2025-01-15', '16:00:00', '17:00:00', 8, 'no_show', 0, NULL, 'Basket', '2025-01-13 14:00:00'),
(43, 17, 1, '2025-01-20', '10:00:00', '11:00:00', 10, 'no_show', 0, NULL, NULL, '2025-01-18 09:00:00'),
(44, 20, 8, '2025-01-22', '19:00:00', '20:00:00', 4, 'no_show', 0, NULL, 'Padel', '2025-01-20 15:00:00'),
(45, 26, 5, '2025-01-25', '17:00:00', '18:00:00', 12, 'no_show', 0, NULL, NULL, '2025-01-23 11:00:00'),
(46, 31, 6, '2025-01-27', '11:00:00', '12:00:00', 2, 'no_show', 0, NULL, 'Tennis', '2025-01-25 16:00:00'),
(47, 19, 9, '2025-01-28', '16:00:00', '17:00:00', 4, 'no_show', 0, NULL, NULL, '2025-01-26 10:00:00'),
(48, 15, 2, '2025-01-29', '10:00:00', '11:00:00', 10, 'no_show', 0, NULL, 'Calcetto', '2025-01-27 08:00:00'),
(49, 22, 10, '2025-01-30', '14:00:00', '15:00:00', 4, 'no_show', 0, NULL, NULL, '2025-01-28 12:00:00'),
(50, 28, 3, '2025-01-30', '15:00:00', '16:00:00', 14, 'no_show', 0, NULL, 'Calcio 7', '2025-01-28 14:00:00'),
-- Prenotazioni FUTURE confermate (10)
(51, 4, 1, '2025-02-03', '10:00:00', '11:00:00', 10, 'confermata', 0, NULL, 'Calcetto lunedì', '2025-01-17 09:00:00'),
(52, 5, 8, '2025-02-03', '15:00:00', '16:00:00', 4, 'confermata', 0, NULL, 'Padel', '2025-01-17 14:00:00'),
(53, 7, 4, '2025-02-03', '18:00:00', '19:00:00', 10, 'confermata', 0, NULL, 'Basket', '2025-01-17 16:00:00'),
(54, 10, 5, '2025-02-04', '14:00:00', '15:00:00', 12, 'confermata', 0, NULL, 'Pallavolo', '2025-01-18 08:00:00'),
(55, 12, 9, '2025-02-04', '17:00:00', '18:00:00', 4, 'confermata', 0, NULL, NULL, '2025-01-18 10:00:00'),
(56, 14, 2, '2025-02-05', '16:00:00', '17:00:00', 10, 'confermata', 0, NULL, 'Calcetto', '2025-01-18 15:00:00'),
(57, 16, 10, '2025-02-05', '13:00:00', '14:00:00', 4, 'confermata', 0, NULL, 'Ping pong', '2025-01-18 18:00:00'),
(58, 18, 1, '2025-02-06', '11:00:00', '12:00:00', 10, 'confermata', 0, NULL, NULL, '2025-01-19 09:00:00'),
(59, 21, 6, '2025-02-06', '10:00:00', '11:00:00', 4, 'confermata', 0, NULL, 'Tennis doppio', '2025-01-19 11:00:00'),
(60, 23, 8, '2025-02-07', '19:00:00', '20:00:00', 4, 'confermata', 0, NULL, 'Padel serale', '2025-01-19 14:00:00'),
-- Prenotazioni FUTURE in attesa (10)
(61, 25, 3, '2025-02-08', '15:00:00', '16:00:00', 14, 'in_attesa', 0, NULL, 'Calcio 7', '2025-01-19 16:00:00'),
(62, 27, 5, '2025-02-09', '16:00:00', '17:00:00', 12, 'in_attesa', 0, NULL, NULL, '2025-01-20 08:00:00'),
(63, 8, 4, '2025-02-09', '20:00:00', '21:00:00', 10, 'in_attesa', 0, NULL, 'Basket domenica', '2025-01-20 10:00:00'),
(64, 11, 9, '2025-02-10', '11:00:00', '12:00:00', 4, 'in_attesa', 0, NULL, 'Padel', '2025-01-20 12:00:00'),
(65, 13, 1, '2025-02-10', '17:00:00', '18:00:00', 10, 'in_attesa', 0, NULL, NULL, '2025-01-20 15:00:00'),
(66, 15, 6, '2025-02-11', '09:00:00', '10:00:00', 2, 'in_attesa', 0, NULL, 'Tennis singolo', '2025-01-20 17:00:00'),
(67, 17, 10, '2025-02-11', '15:00:00', '16:00:00', 4, 'in_attesa', 0, NULL, NULL, '2025-01-20 19:00:00'),
(68, 19, 2, '2025-02-12', '18:00:00', '19:00:00', 10, 'in_attesa', 0, NULL, 'Calcetto serale', '2025-01-21 08:00:00'),
(69, 24, 8, '2025-02-13', '20:00:00', '21:00:00', 4, 'in_attesa', 0, NULL, 'Padel', '2025-01-21 10:00:00'),
(70, 30, 3, '2025-02-14', '10:00:00', '11:00:00', 14, 'in_attesa', 0, NULL, 'Calcio 7 San Valentino', '2025-01-21 12:00:00');

ALTER TABLE `prenotazioni` AUTO_INCREMENT = 71;


-- -----------------------------------------------------
-- RECENSIONI (35 recensioni) - COLONNE CORRETTE
-- -----------------------------------------------------
INSERT INTO `recensioni` (`recensione_id`, `prenotazione_id`, `user_id`, `campo_id`, `rating_generale`, `commento`, `created_at`) VALUES
-- Recensioni Campo Calcetto A (campo_id=1)
(1, 1, 4, 1, 5, 'Campo in ottime condizioni, erba sintetica perfetta. Ci tornerò sicuramente!', '2025-01-06 12:00:00'),
(2, 6, 14, 1, 4, 'Campo buono, illuminazione un po'' scarsa la sera.', '2025-01-09 20:00:00'),
(3, 21, 7, 1, 5, 'Dimensioni giuste, fondo regolare. Consigliatissimo.', '2025-01-18 13:00:00'),
(4, 28, 12, 1, 4, 'Buon rapporto qualità prezzo.', '2025-01-22 11:00:00'),
(5, 35, 25, 1, 5, 'Il migliore campo di calcetto della zona.', '2025-01-27 19:00:00'),
-- Recensioni Campo Calcetto B (campo_id=2)
(6, 4, 10, 2, 4, 'Leggermente più piccolo del campo A ma comunque valido.', '2025-01-08 18:00:00'),
(7, 16, 22, 2, 3, 'Campo discreto, qualche buca sul fondo.', '2025-01-15 17:00:00'),
(8, 25, 33, 2, 4, 'Bel campo, spogliatoi puliti.', '2025-01-21 18:00:00'),
(9, 33, 21, 2, 5, 'Tutto perfetto, personale gentilissimo.', '2025-01-25 12:00:00'),
-- Recensioni Campo Calcio 7 (campo_id=3)
(10, 7, 6, 3, 4, 'Perfetto per partite 7vs7, erba ben mantenuta.', '2025-01-10 17:00:00'),
(11, 18, 5, 3, 5, 'Campo fantastico, ci giochiamo ogni settimana.', '2025-01-16 12:00:00'),
(12, 30, 16, 3, 4, 'Ampio e ben curato.', '2025-01-23 13:00:00'),
(13, 39, 11, 3, 5, 'Nulla da dire, perfetto in tutto.', '2025-01-29 17:00:00'),
-- Recensioni Palestra Basket (campo_id=4)
(14, 2, 5, 4, 5, 'Pavimento perfetto, canestri regolamentari. Ottima!', '2025-01-06 16:00:00'),
(15, 12, 17, 4, 4, 'Buona illuminazione e spazio.', '2025-01-13 22:00:00'),
(16, 20, 28, 4, 4, 'Buona palestra per basket, un po'' calda d''estate.', '2025-01-17 21:00:00'),
(17, 27, 10, 4, 5, 'La migliore palestra per basket in zona.', '2025-01-22 22:00:00'),
(18, 36, 27, 4, 4, 'Spogliatoi ampi, campo regolamentare.', '2025-01-27 20:00:00'),
-- Recensioni Campo Pallavolo (campo_id=5)
(19, 8, 8, 5, 5, 'Rete a norma, pavimento antiscivolo. Top!', '2025-01-10 19:00:00'),
(20, 15, 20, 5, 4, 'Campo valido, a volte un po'' affollato.', '2025-01-15 20:00:00'),
(21, 23, 31, 5, 4, 'Bel campo di pallavolo.', '2025-01-20 16:00:00'),
(22, 32, 5, 5, 5, 'Struttura eccellente.', '2025-01-24 18:00:00'),
-- Recensioni Campo Tennis 1 (campo_id=6)
(23, 5, 12, 6, 5, 'Superficie perfetta, rete nuova. Bellissimo giocare qui.', '2025-01-08 11:00:00'),
(24, 13, 4, 6, 4, 'Campo in buone condizioni generali.', '2025-01-14 13:00:00'),
(25, 24, 32, 6, 5, 'Il mio campo preferito per il tennis.', '2025-01-20 12:00:00'),
(26, 34, 23, 6, 4, 'Campo ben mantenuto.', '2025-01-25 16:00:00'),
-- Recensioni Campo Padel 1 (campo_id=8)
(27, 3, 7, 8, 5, 'Campo di padel fantastico, vetri puliti, erba sintetica nuova.', '2025-01-07 20:00:00'),
(28, 11, 15, 8, 4, 'Buon campo, illuminazione ok.', '2025-01-13 21:00:00'),
(29, 22, 30, 8, 4, 'Campo adatto anche ai principianti.', '2025-01-18 19:00:00'),
(30, 31, 18, 8, 5, 'Il migliore campo di padel!', '2025-01-24 21:00:00'),
(31, 40, 13, 8, 4, 'Bel campo, tornerò.', '2025-01-29 20:00:00'),
-- Recensioni Campo Padel 2 (campo_id=9)
(32, 10, 13, 9, 4, 'Campo valido, leggermente più piccolo del campo 1.', '2025-01-11 12:00:00'),
(33, 17, 24, 9, 5, 'Perfetto per giocare in tranquillità.', '2025-01-16 18:00:00'),
(34, 26, 4, 9, 4, 'Campo ok, nulla di eccezionale ma funzionale.', '2025-01-21 20:00:00'),
(35, 37, 7, 9, 5, 'Ci gioco spesso, sempre soddisfatto.', '2025-01-28 22:00:00');

ALTER TABLE `recensioni` AUTO_INCREMENT = 36;


-- -----------------------------------------------------
-- SEGNALAZIONI (15 segnalazioni)
-- -----------------------------------------------------
INSERT INTO `segnalazioni` (`segnalazione_id`, `user_segnalante_id`, `user_segnalato_id`, `tipo`, `descrizione`, `prenotazione_id`, `stato`, `priorita`, `admin_id`, `azione_intrapresa`, `penalty_assegnati`, `note_risoluzione`, `created_at`, `resolved_at`) VALUES
-- Segnalazioni PENDING (10)
(1, 4, 6, 'no_show', 'L''utente non si è presentato alla partita di calcetto, eravamo rimasti in 9.', 41, 'pending', 'media', NULL, NULL, NULL, NULL, '2025-01-10 13:00:00', NULL),
(2, 10, 17, 'no_show', 'Assente senza preavviso, abbiamo dovuto annullare la partita.', 43, 'pending', 'alta', NULL, NULL, NULL, NULL, '2025-01-20 12:00:00', NULL),
(3, 12, 26, 'comportamento_scorretto', 'Ha lasciato il campo in disordine e non ha raccolto le bottiglie.', 19, 'pending', 'bassa', NULL, NULL, NULL, NULL, '2025-01-17 16:00:00', NULL),
(4, 14, 20, 'no_show', 'Non si è presentato al padel, ho aspettato 20 minuti.', 44, 'pending', 'media', NULL, NULL, NULL, NULL, '2025-01-22 20:00:00', NULL),
(5, 5, 31, 'linguaggio_offensivo', 'Ha usato linguaggio inappropriato durante la partita.', 23, 'pending', 'alta', NULL, NULL, NULL, NULL, '2025-01-20 17:00:00', NULL),
(6, 7, 15, 'no_show', 'Assente alla prenotazione di calcetto.', 48, 'pending', 'media', NULL, NULL, NULL, NULL, '2025-01-29 11:00:00', NULL),
(7, 8, 22, 'comportamento_scorretto', 'Ha occupato il campo oltre l''orario prenotato.', 49, 'pending', 'bassa', NULL, NULL, NULL, NULL, '2025-01-30 15:00:00', NULL),
(8, 11, 6, 'no_show', 'Secondo no-show consecutivo, molto scorretto.', 42, 'pending', 'alta', NULL, NULL, NULL, NULL, '2025-01-15 17:00:00', NULL),
(9, 13, 19, 'comportamento_scorretto', 'Ha discusso animatamente con altri giocatori.', 47, 'pending', 'media', NULL, NULL, NULL, NULL, '2025-01-28 17:00:00', NULL),
(10, 16, 28, 'no_show', 'Non si è presentato alla partita di calcio 7.', 50, 'pending', 'alta', NULL, NULL, NULL, NULL, '2025-01-30 16:00:00', NULL),
-- Segnalazioni RESOLVED (4)
(11, 4, 6, 'comportamento_scorretto', 'Arrivato in ritardo di 30 minuti senza avvisare.', 7, 'resolved', 'media', 1, 'penalty_points', 2, 'Assegnati 2 punti penalità per ritardo grave.', '2025-01-10 18:00:00', '2025-01-11 10:00:00'),
(12, 17, 6, 'no_show', 'Terzo no-show dell''utente, comportamento recidivo.', 42, 'resolved', 'alta', 1, 'sospensione', 5, 'Utente sospeso per 7 giorni per no-show ripetuti.', '2025-01-16 09:00:00', '2025-01-16 14:00:00'),
(13, 20, 33, 'altro', 'Ha portato cibo sul campo da tennis.', 24, 'resolved', 'bassa', 2, 'warning', 0, 'Warning verbale, nessuna penalità.', '2025-01-20 13:00:00', '2025-01-20 16:00:00'),
(14, 22, 18, 'linguaggio_offensivo', 'Insulti verso l''arbitro durante la partita.', 31, 'resolved', 'alta', 1, 'penalty_points', 3, 'Assegnati 3 punti penalità per linguaggio offensivo.', '2025-01-24 22:00:00', '2025-01-25 09:00:00'),
-- Segnalazione REJECTED (1)
(15, 30, 7, 'comportamento_scorretto', 'Giocava troppo forte.', 21, 'rejected', 'bassa', 2, 'nessuna', 0, 'Segnalazione non valida. Giocare bene non è scorretto.', '2025-01-18 14:00:00', '2025-01-18 17:00:00');

ALTER TABLE `segnalazioni` AUTO_INCREMENT = 16;


-- -----------------------------------------------------
-- USER BADGES (badges sbloccati)
-- -----------------------------------------------------
INSERT INTO `user_badges` (`id`, `user_id`, `badge_id`, `sbloccato_at`) VALUES
-- Badge "Prima Prenotazione" (badge_id=1)
(1, 4, 1, '2025-01-04 14:35:00'),
(2, 5, 1, '2025-01-05 09:05:00'),
(3, 6, 1, '2025-01-08 09:05:00'),
(4, 7, 1, '2025-01-05 16:05:00'),
(5, 8, 1, '2025-01-09 10:05:00'),
(6, 10, 1, '2025-01-06 11:05:00'),
(7, 11, 1, '2025-01-09 15:05:00'),
(8, 12, 1, '2025-01-07 08:05:00'),
(9, 13, 1, '2025-01-10 08:05:00'),
(10, 14, 1, '2025-01-07 20:05:00'),
(11, 15, 1, '2025-01-11 14:05:00'),
(12, 16, 1, '2025-01-14 08:05:00'),
(13, 17, 1, '2025-01-12 09:05:00'),
(14, 18, 1, '2025-01-22 14:05:00'),
(15, 19, 1, '2025-01-13 11:05:00'),
(16, 20, 1, '2025-01-13 19:05:00'),
(17, 21, 1, '2025-01-18 11:05:00'),
(18, 22, 1, '2025-01-14 08:05:00'),
(19, 23, 1, '2025-01-19 14:05:00'),
(20, 24, 1, '2025-01-14 14:05:00'),
(21, 25, 1, '2025-01-19 16:05:00'),
(22, 26, 1, '2025-01-15 18:05:00'),
(23, 27, 1, '2025-01-20 08:05:00'),
(24, 28, 1, '2025-01-16 10:05:00'),
(25, 30, 1, '2025-01-17 09:05:00'),
(26, 31, 1, '2025-01-18 11:05:00'),
(27, 32, 1, '2025-01-19 08:05:00'),
(28, 33, 1, '2025-01-19 14:05:00'),
-- Badge "Recensore" (badge_id=2)
(29, 4, 2, '2025-01-06 12:05:00'),
(30, 5, 2, '2025-01-06 16:05:00'),
(31, 6, 2, '2025-01-10 17:05:00'),
(32, 7, 2, '2025-01-07 20:05:00'),
(33, 8, 2, '2025-01-10 19:05:00'),
(34, 10, 2, '2025-01-08 18:05:00'),
(35, 12, 2, '2025-01-08 11:05:00'),
(36, 13, 2, '2025-01-11 12:05:00'),
-- Badge "Sportivo Attivo" (badge_id=3)
(37, 4, 3, '2025-01-21 17:55:00'),
(38, 5, 3, '2025-01-24 15:55:00'),
(39, 7, 3, '2025-01-28 19:58:00'),
-- Badge "Multisport" (badge_id=4)
(40, 5, 4, '2025-01-24 15:55:00'),
-- Badge "Campione" (badge_id=5)
(41, 7, 5, '2025-01-05 16:10:00');

ALTER TABLE `user_badges` AUTO_INCREMENT = 42;


-- -----------------------------------------------------
-- NOTIFICHE (67 notifiche)
-- -----------------------------------------------------
INSERT INTO `notifiche` (`notifica_id`, `user_id`, `tipo`, `titolo`, `messaggio`, `letta`, `link`, `created_at`, `read_at`) VALUES
-- Notifiche LETTE (vecchie)
(1, 4, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A del 06/01/2025 alle 10:00 è stata confermata.', 1, 'le-mie-prenotazioni.php', '2025-01-04 14:35:00', '2025-01-04 15:00:00'),
(2, 5, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Palestra Basket del 06/01/2025 alle 14:00 è stata confermata.', 1, 'le-mie-prenotazioni.php', '2025-01-05 09:05:00', '2025-01-05 10:00:00'),
(3, 7, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 1 del 07/01/2025 alle 18:00 è stata confermata.', 1, 'le-mie-prenotazioni.php', '2025-01-05 16:05:00', '2025-01-05 17:00:00'),
(4, 4, 'promemoria_prenotazione', 'Promemoria Prenotazione', 'Ricorda: hai una prenotazione domani per Campo Calcetto A alle 10:00.', 1, 'le-mie-prenotazioni.php', '2025-01-05 18:00:00', '2025-01-05 20:00:00'),
(5, 5, 'promemoria_prenotazione', 'Promemoria Prenotazione', 'Ricorda: hai una prenotazione domani per Palestra Basket alle 14:00.', 1, 'le-mie-prenotazioni.php', '2025-01-05 18:00:00', '2025-01-05 19:00:00'),
(6, 6, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A del 10/01/2025 alle 09:00 è stata confermata.', 1, 'le-mie-prenotazioni.php', '2025-01-08 10:05:00', '2025-01-08 11:00:00'),
(7, 6, 'no_show_warning', 'Mancata Presentazione', 'Non ti sei presentato alla prenotazione del 10/01/2025. Hai ricevuto 5 punti penalità.', 1, 'profilo.php', '2025-01-10 11:00:00', '2025-01-10 12:00:00'),
(8, 6, 'penalty_ricevuti', 'Punti Penalità Ricevuti', 'Hai ricevuto 5 punti penalità per no-show alla prenotazione del 10/01/2025.', 1, 'profilo.php', '2025-01-10 12:00:00', '2025-01-10 14:00:00'),
(9, 4, 'badge_sbloccato', 'Nuovo Badge Sbloccato!', 'Hai sbloccato il badge Recensore!', 1, 'profilo.php', '2025-01-06 12:05:00', '2025-01-06 12:10:00'),
(10, 5, 'badge_sbloccato', 'Nuovo Badge Sbloccato!', 'Hai sbloccato il badge Recensore!', 1, 'profilo.php', '2025-01-06 16:05:00', '2025-01-06 16:10:00'),
(11, 6, 'sospensione_account', 'Account Sospeso', 'Il tuo account è stato sospeso fino al 23/01/2025. Motivo: No-show ripetuti.', 1, NULL, '2025-01-16 14:00:00', '2025-01-16 14:30:00'),
(12, 7, 'badge_sbloccato', 'Nuovo Badge Sbloccato!', 'Hai sbloccato il badge Campione! Sei al livello Platinum!', 1, 'profilo.php', '2025-01-05 16:10:00', '2025-01-05 16:15:00'),
(13, 4, 'badge_sbloccato', 'Nuovo Badge Sbloccato!', 'Hai sbloccato il badge Sportivo Attivo!', 1, 'profilo.php', '2025-01-21 17:55:00', '2025-01-21 18:00:00'),
(14, 5, 'badge_sbloccato', 'Nuovo Badge Sbloccato!', 'Hai sbloccato il badge Multisport! Hai provato 5 sport diversi!', 1, 'profilo.php', '2025-01-24 15:55:00', '2025-01-24 16:00:00'),
(15, 18, 'penalty_ricevuti', 'Punti Penalità Ricevuti', 'Hai ricevuto 3 punti penalità per linguaggio offensivo.', 1, 'profilo.php', '2025-01-25 09:00:00', '2025-01-25 10:00:00'),
-- Notifiche NON LETTE (recenti)
(16, 4, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A del 03/02/2025 alle 10:00 è stata confermata.', 0, 'le-mie-prenotazioni.php', '2025-01-17 09:00:00', NULL),
(17, 4, 'promemoria_prenotazione', 'Promemoria Prenotazione', 'Ricorda: hai una prenotazione domani per Campo Calcetto A alle 10:00.', 0, 'le-mie-prenotazioni.php', '2025-02-02 18:00:00', NULL),
(18, 5, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 1 del 03/02/2025 alle 15:00 è stata confermata.', 0, 'le-mie-prenotazioni.php', '2025-01-17 14:00:00', NULL),
(19, 7, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Palestra Basket del 03/02/2025 alle 18:00 è stata confermata.', 0, 'le-mie-prenotazioni.php', '2025-01-17 16:00:00', NULL),
(20, 10, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Campo Pallavolo del 04/02/2025 alle 14:00 è stata confermata.', 0, 'le-mie-prenotazioni.php', '2025-01-18 08:00:00', NULL),
(21, 12, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 2 del 04/02/2025 alle 17:00 è stata confermata.', 0, 'le-mie-prenotazioni.php', '2025-01-18 10:00:00', NULL),
(22, 14, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto B del 05/02/2025 alle 16:00 è stata confermata.', 0, 'le-mie-prenotazioni.php', '2025-01-18 15:00:00', NULL),
(23, 16, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Sala Ping Pong del 05/02/2025 alle 13:00 è stata confermata.', 0, 'le-mie-prenotazioni.php', '2025-01-18 18:00:00', NULL),
(24, 18, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A del 06/02/2025 alle 11:00 è stata confermata.', 0, 'le-mie-prenotazioni.php', '2025-01-19 09:00:00', NULL),
(25, 21, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Campo Tennis 1 del 06/02/2025 alle 10:00 è stata confermata.', 0, 'le-mie-prenotazioni.php', '2025-01-19 11:00:00', NULL),
(26, 23, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 1 del 07/02/2025 alle 19:00 è stata confermata.', 0, 'le-mie-prenotazioni.php', '2025-01-19 14:00:00', NULL),
(27, 6, 'penalty_ricevuti', 'Punti Penalità Ricevuti', 'Hai ricevuto 2 punti penalità per ritardo grave.', 0, 'profilo.php', '2025-01-11 10:00:00', NULL),
(28, 17, 'penalty_ricevuti', 'Punti Penalità Ricevuti', 'Hai ricevuto 5 punti penalità per no-show alla prenotazione del 20/01/2025.', 0, 'profilo.php', '2025-01-20 12:00:00', NULL),
-- Inviti a prenotazioni
(29, 5, 'invito_prenotazione', 'Invito a Prenotazione', 'Mario Verdi ti ha invitato a partecipare alla prenotazione del 03/02/2025 alle 10:00 presso Campo Calcetto A.', 0, 'le-mie-prenotazioni.php', '2025-01-17 09:05:00', NULL),
(30, 10, 'invito_prenotazione', 'Invito a Prenotazione', 'Mario Verdi ti ha invitato a partecipare alla prenotazione del 03/02/2025 alle 10:00 presso Campo Calcetto A.', 0, 'le-mie-prenotazioni.php', '2025-01-17 09:05:00', NULL);

ALTER TABLE `notifiche` AUTO_INCREMENT = 31;


-- -----------------------------------------------------
-- PENALTY LOG (log delle penalità assegnate)
-- -----------------------------------------------------
INSERT INTO `penalty_log` (`log_id`, `user_id`, `punti`, `motivo`, `descrizione`, `prenotazione_id`, `segnalazione_id`, `admin_id`, `created_at`) VALUES
(1, 6, 5, 'no_show', 'No-show alla prenotazione di calcetto del 10/01/2025', 41, 1, NULL, '2025-01-10 12:00:00'),
(2, 6, 5, 'no_show', 'No-show alla prenotazione di basket del 15/01/2025', 42, 8, NULL, '2025-01-15 17:00:00'),
(3, 6, 2, 'segnalazione', 'Ritardo grave di 30 minuti', 7, 11, 1, '2025-01-11 10:00:00'),
(4, 17, 5, 'no_show', 'No-show alla prenotazione di calcetto del 20/01/2025', 43, 2, NULL, '2025-01-20 11:00:00'),
(5, 20, 5, 'no_show', 'No-show alla prenotazione di padel del 22/01/2025', 44, 4, NULL, '2025-01-22 20:00:00'),
(6, 26, 5, 'no_show', 'No-show alla prenotazione di pallavolo del 25/01/2025', 45, NULL, NULL, '2025-01-25 18:00:00'),
(7, 31, 5, 'no_show', 'No-show alla prenotazione di tennis del 27/01/2025', 46, NULL, NULL, '2025-01-27 12:00:00'),
(8, 19, 5, 'no_show', 'No-show alla prenotazione di padel del 28/01/2025', 47, NULL, NULL, '2025-01-28 17:00:00'),
(9, 15, 5, 'no_show', 'No-show alla prenotazione di calcetto del 29/01/2025', 48, 6, NULL, '2025-01-29 11:00:00'),
(10, 22, 5, 'no_show', 'No-show alla prenotazione di ping pong del 30/01/2025', 49, 7, NULL, '2025-01-30 15:00:00'),
(11, 28, 5, 'no_show', 'No-show alla prenotazione di calcio 7 del 30/01/2025', 50, 10, NULL, '2025-01-30 16:00:00'),
(12, 18, 3, 'segnalazione', 'Linguaggio offensivo durante la partita', 31, 14, 1, '2025-01-25 09:00:00');

ALTER TABLE `penalty_log` AUTO_INCREMENT = 13;


-- -----------------------------------------------------
-- SANZIONI (sospensioni/ban)
-- -----------------------------------------------------
INSERT INTO `sanzioni` (`sanzione_id`, `user_id`, `tipo`, `motivo`, `data_inizio`, `data_fine`, `admin_id`, `attiva`, `created_at`) VALUES
(1, 6, 'sospensione', 'No-show ripetuti (3 volte in un mese)', '2025-01-16 14:00:00', '2025-01-23 14:00:00', 1, 0, '2025-01-16 14:00:00'),
(2, 29, 'ban', 'Comportamento gravemente scorretto e violazione ripetuta delle regole', '2024-12-15 10:00:00', NULL, 1, 1, '2024-12-15 10:00:00');

ALTER TABLE `sanzioni` AUTO_INCREMENT = 3;

-- -----------------------------------------------------
-- BROADCAST MESSAGES
-- -----------------------------------------------------
INSERT INTO `broadcast_messages` (`broadcast_id`, `admin_id`, `oggetto`, `messaggio`, `target_type`, `target_filter`, `canale`, `scheduled_at`, `sent_at`, `num_destinatari`, `stato`, `created_at`) VALUES
(1, 1, 'Buon Anno da Campus Sports Arena!', 'Caro studente, tutto il team di Campus Sports Arena ti augura un fantastico 2025! Quest''anno abbiamo in serbo tante novità.', 'tutti', NULL, 'entrambi', NULL, '2025-01-02 10:00:00', 35, 'inviato', '2025-01-02 09:30:00'),
(2, 2, 'Manutenzione Completata - Campi Calcetto', 'Siamo lieti di comunicarti che i lavori di manutenzione sui campi da calcetto sono stati completati.', 'tutti', NULL, 'in_app', NULL, '2025-01-08 14:00:00', 35, 'inviato', '2025-01-08 13:45:00'),
(3, 1, 'Grazie per la tua fedeltà!', 'Ciao! Abbiamo notato che sei uno dei nostri utenti più attivi e volevamo ringraziarti.', 'attivi', NULL, 'entrambi', NULL, '2025-01-10 11:00:00', 28, 'inviato', '2025-01-10 10:30:00'),
(4, 2, 'Torneo di Tennis Universitario 2025', 'Sei appassionato di tennis? TORNEO DI TENNIS UNIVERSITARIO 2025 - 15-16 Febbraio 2025', 'sport', '5', 'entrambi', NULL, '2025-01-14 16:00:00', 12, 'inviato', '2025-01-14 15:30:00'),
(5, 2, 'San Valentino Sportivo', 'Festeggia San Valentino in modo diverso! PROMO COPPIA 14 FEBBRAIO', 'tutti', NULL, 'entrambi', '2025-02-12 10:00:00', NULL, 35, 'programmato', '2025-01-22 09:00:00'),
(6, 1, 'Newsletter Febbraio', 'Gentili utenti, ecco le novità di Febbraio...', 'tutti', NULL, 'email', NULL, NULL, 0, 'bozza', '2025-01-22 15:00:00');

ALTER TABLE `broadcast_messages` AUTO_INCREMENT = 7;


-- =====================================================
-- FINE FILE
-- =====================================================