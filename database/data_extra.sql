-- =====================================================
-- DATI EXTRA - Campus Sports Arena
-- =====================================================
-- Eseguire DOPO aver eseguito data.sql
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

-- Fine gennaio / inizio febbraio (ultime passate)
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
(41, 6, 1, '2025-01-10', '11:00:00', '12:00:00', 10, 'no_show', 0, NULL, NULL, '2025-01-08 09:00:00'),
(42, 6, 4, '2025-01-15', '16:00:00', '17:00:00', 8, 'no_show', 0, NULL, NULL, '2025-01-13 14:00:00'),
(43, 17, 2, '2025-01-20', '10:00:00', '11:00:00', 10, 'no_show', 0, NULL, NULL, '2025-01-18 11:00:00'),
(44, 20, 8, '2025-01-22', '19:00:00', '20:00:00', 4, 'no_show', 0, NULL, NULL, '2025-01-20 15:00:00'),
(45, 26, 5, '2025-01-25', '17:00:00', '18:00:00', 12, 'no_show', 0, NULL, NULL, '2025-01-23 10:00:00'),
(46, 31, 6, '2025-01-27', '11:00:00', '12:00:00', 2, 'no_show', 0, NULL, NULL, '2025-01-25 08:00:00'),
(47, 19, 9, '2025-01-28', '16:00:00', '17:00:00', 4, 'no_show', 0, NULL, NULL, '2025-01-26 12:00:00'),
(48, 15, 1, '2025-01-29', '10:00:00', '11:00:00', 10, 'no_show', 0, NULL, NULL, '2025-01-27 09:00:00'),
(49, 22, 10, '2025-01-30', '14:00:00', '15:00:00', 4, 'no_show', 0, NULL, NULL, '2025-01-28 16:00:00'),
(50, 28, 3, '2025-01-30', '15:00:00', '16:00:00', 14, 'no_show', 0, NULL, NULL, '2025-01-28 18:00:00'),

-- Prenotazioni CANCELLATE (5)
(51, 4, 8, '2025-01-15', '20:00:00', '21:00:00', 4, 'cancellata', 0, NULL, 'Impegno improvviso', '2025-01-13 10:00:00'),
(52, 12, 5, '2025-01-18', '18:00:00', '19:00:00', 12, 'cancellata', 0, NULL, 'Maltempo', '2025-01-16 09:00:00'),
(53, 24, 1, '2025-01-22', '17:00:00', '18:00:00', 10, 'cancellata', 0, NULL, NULL, '2025-01-20 14:00:00'),
(54, 30, 4, '2025-01-25', '19:00:00', '20:00:00', 10, 'cancellata', 0, NULL, 'Problemi personali', '2025-01-23 11:00:00'),
(55, 33, 6, '2025-01-28', '10:00:00', '11:00:00', 4, 'cancellata', 0, NULL, NULL, '2025-01-26 08:00:00'),

-- Prenotazioni CONFERMATE FUTURE (15 - da oggi in poi)
(56, 4, 1, '2025-01-19', '10:00:00', '11:00:00', 10, 'confermata', 0, NULL, 'Partita domenicale', '2025-01-17 09:00:00'),
(57, 5, 8, '2025-01-19', '15:00:00', '16:00:00', 4, 'confermata', 0, NULL, NULL, '2025-01-17 14:00:00'),
(58, 7, 4, '2025-01-19', '18:00:00', '19:00:00', 10, 'confermata', 0, NULL, 'Basket', '2025-01-17 16:00:00'),
(59, 10, 5, '2025-01-20', '16:00:00', '17:00:00', 12, 'confermata', 0, NULL, NULL, '2025-01-18 08:00:00'),
(60, 12, 9, '2025-01-20', '17:00:00', '18:00:00', 4, 'confermata', 0, NULL, 'Padel', '2025-01-18 10:00:00'),
(61, 14, 2, '2025-01-21', '18:00:00', '19:00:00', 10, 'confermata', 0, NULL, NULL, '2025-01-18 15:00:00'),
(62, 16, 10, '2025-01-21', '13:00:00', '14:00:00', 4, 'confermata', 0, NULL, 'Ping pong', '2025-01-18 18:00:00'),
(63, 18, 1, '2025-01-22', '11:00:00', '12:00:00', 10, 'confermata', 0, NULL, NULL, '2025-01-19 09:00:00'),
(64, 21, 6, '2025-01-22', '10:00:00', '11:00:00', 2, 'confermata', 0, NULL, 'Tennis', '2025-01-19 11:00:00'),
(65, 23, 8, '2025-01-23', '19:00:00', '20:00:00', 4, 'confermata', 0, NULL, NULL, '2025-01-19 14:00:00'),
(66, 25, 3, '2025-01-24', '15:00:00', '16:00:00', 14, 'confermata', 0, NULL, 'Calcio 7', '2025-01-19 16:00:00'),
(67, 27, 5, '2025-01-25', '16:00:00', '17:00:00', 12, 'confermata', 0, NULL, NULL, '2025-01-20 08:00:00'),
(68, 8, 4, '2025-01-25', '20:00:00', '21:00:00', 10, 'confermata', 0, NULL, 'Basket serale', '2025-01-20 10:00:00'),
(69, 11, 9, '2025-01-26', '11:00:00', '12:00:00', 4, 'confermata', 0, NULL, NULL, '2025-01-20 12:00:00'),
(70, 13, 1, '2025-01-26', '17:00:00', '18:00:00', 10, 'confermata', 0, NULL, 'Calcetto', '2025-01-20 15:00:00');

ALTER TABLE `prenotazioni`
  MODIFY `prenotazione_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;


-- -----------------------------------------------------
-- RECENSIONI (35 recensioni sulle prenotazioni completate)
-- -----------------------------------------------------
INSERT INTO `recensioni` (`recensione_id`, `user_id`, `campo_id`, `prenotazione_id`, `rating_generale`, `rating_condizioni`, `rating_pulizia`, `rating_illuminazione`, `commento`, `voti_utili`, `created_at`) VALUES
(1, 4, 1, 1, 5, 5, 5, 4, 'Campo eccellente! Erba sintetica in ottime condizioni. Consigliato!', 8, '2025-01-06 12:00:00'),
(2, 5, 4, 2, 5, 5, 5, 5, 'Palestra fantastica, parquet perfetto per giocare a basket.', 12, '2025-01-06 16:00:00'),
(3, 7, 8, 3, 4, 4, 5, 4, 'Ottimo campo da padel, un po'' affollato nel weekend.', 5, '2025-01-07 20:00:00'),
(4, 10, 2, 4, 4, 4, 4, 5, 'Buon campo, illuminazione eccellente per partite serali.', 3, '2025-01-08 18:00:00'),
(5, 12, 6, 5, 5, 5, 4, NULL, 'Terra battuta perfetta, mi sono sentito un professionista!', 7, '2025-01-08 11:00:00'),
(6, 14, 1, 6, 4, 4, 4, 4, 'Sempre affidabile questo campo, ottimo per calcetto.', 4, '2025-01-09 20:00:00'),
(7, 6, 3, 7, 3, 3, 4, NULL, 'Campo grande ma erba naturale un po'' irregolare in alcuni punti.', 2, '2025-01-10 17:00:00'),
(8, 8, 5, 8, 5, 5, 5, 5, 'Campo da pallavolo perfetto, rete nuova e ottima illuminazione.', 9, '2025-01-10 19:00:00'),
(9, 11, 10, 9, 4, 4, 3, 4, 'Tavoli buoni ma spogliatoi assenti. Portate il cambio!', 6, '2025-01-11 16:00:00'),
(10, 13, 9, 10, 5, 5, 5, 5, 'Padel outdoor fantastico, vista panoramica e campo perfetto.', 11, '2025-01-11 12:00:00'),

(11, 15, 8, 11, 4, 4, 5, 4, 'Campo coperto, ideale anche con brutto tempo.', 4, '2025-01-13 21:00:00'),
(12, 17, 4, 12, 5, 5, 5, 5, 'Miglior campo da basket della zona, niente da dire!', 15, '2025-01-13 22:00:00'),
(13, 4, 6, 13, 4, 4, 4, NULL, 'Tennis sempre al top, terra battuta ben mantenuta.', 3, '2025-01-14 13:00:00'),
(14, 19, 1, 14, 5, 5, 4, 5, 'Calcetto perfetto, ci torniamo sicuramente.', 6, '2025-01-14 19:00:00'),
(15, 20, 5, 15, 4, 4, 4, 5, 'Pallavolo divertente, campo ben illuminato.', 2, '2025-01-15 20:00:00'),
(16, 22, 2, 16, 4, 4, 5, 4, 'Buona esperienza, spogliatoi pulitissimi.', 5, '2025-01-15 17:00:00'),
(17, 24, 9, 17, 5, 5, 5, 5, 'Padel esterno eccezionale, erba sintetica nuova.', 8, '2025-01-16 18:00:00'),
(18, 5, 3, 18, 3, 3, 3, NULL, 'Campo da calcio 7 discreto, erba da sistemare.', 1, '2025-01-16 12:00:00'),
(19, 26, 10, 19, 4, 4, 4, 4, 'Ping pong divertente, tavoli in buone condizioni.', 3, '2025-01-17 15:00:00'),
(20, 28, 4, 20, 5, 5, 5, 5, 'Basket serale fantastico, atmosfera perfetta!', 10, '2025-01-17 21:00:00'),

(21, 7, 1, 21, 5, 5, 5, 4, 'Sempre il miglior campo per calcetto, top!', 7, '2025-01-18 13:00:00'),
(22, 30, 8, 22, 4, 4, 4, 5, 'Padel coperto ottimo, un po'' caldo d''estate.', 2, '2025-01-18 19:00:00'),
(23, 31, 5, 23, 5, 5, 5, 5, 'Pallavolo eccellente, squadra fantastica!', 4, '2025-01-20 16:00:00'),
(24, 32, 6, 24, 4, 4, 4, NULL, 'Tennis buono, campo in cemento veloce.', 1, '2025-01-20 12:00:00'),
(25, 33, 2, 25, 4, 4, 5, 5, 'Calcetto sempre una garanzia qui.', 5, '2025-01-21 18:00:00'),
(26, 4, 9, 26, 5, 5, 5, 5, 'Padel esterno bellissimo, torneremo!', 9, '2025-01-21 20:00:00'),
(27, 10, 4, 27, 5, 5, 5, 5, 'Basket notturno incredibile, luci perfette.', 6, '2025-01-22 22:00:00'),
(28, 12, 1, 28, 4, 4, 4, 4, 'Calcetto mattutino, poca gente e campo libero.', 2, '2025-01-22 11:00:00'),
(29, 14, 10, 29, 4, 4, 4, 4, 'Ping pong rilassante, ottimo per pause studio.', 3, '2025-01-23 17:00:00'),
(30, 16, 3, 30, 3, 3, 4, NULL, 'Campo grande ma servirebbe manutenzione.', 0, '2025-01-23 13:00:00'),

(31, 18, 8, 31, 5, 5, 5, 5, 'Padel serale fantastico, campo illuminato benissimo!', 8, '2025-01-24 21:00:00'),
(32, 5, 5, 32, 5, 5, 5, 5, 'Pallavolo top, pavimento perfetto.', 11, '2025-01-24 18:00:00'),
(33, 21, 2, 33, 4, 4, 4, 4, 'Calcetto del sabato mattina, classico!', 4, '2025-01-25 12:00:00'),
(34, 23, 6, 34, 5, 5, 5, NULL, 'Tennis doppio divertentissimo, campo eccellente.', 6, '2025-01-25 16:00:00'),
(35, 25, 1, 35, 5, 5, 5, 5, 'Campo preferito per calcetto, sempre perfetto!', 13, '2025-01-27 19:00:00');

ALTER TABLE `recensioni`
  MODIFY `recensione_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;


-- -----------------------------------------------------
-- SEGNALAZIONI (15 segnalazioni)
-- -----------------------------------------------------
INSERT INTO `segnalazioni` (`segnalazione_id`, `user_segnalante_id`, `user_segnalato_id`, `tipo`, `descrizione`, `prenotazione_id`, `stato`, `priorita`, `admin_id`, `azione_intrapresa`, `penalty_assegnati`, `note_risoluzione`, `created_at`, `resolved_at`) VALUES
-- Segnalazioni PENDING (7)
(1, 4, 6, 'no_show', 'L''utente non si è presentato alla partita di calcetto, eravamo rimasti in 9.', 41, 'pending', 'media', NULL, NULL, NULL, NULL, '2025-01-10 13:00:00', NULL),
(2, 10, 17, 'no_show', 'Assente senza preavviso, abbiamo dovuto annullare la partita.', 43, 'pending', 'alta', NULL, NULL, NULL, NULL, '2025-01-20 12:00:00', NULL),
(3, 12, 26, 'comportamento_scorretto', 'Ha lasciato il campo in disordine e non ha raccolto le bottiglie.', 19, 'pending', 'bassa', NULL, NULL, NULL, NULL, '2025-01-17 16:00:00', NULL),
(4, 14, 20, 'no_show', 'Non si è presentato al padel, ho aspettato 20 minuti.', 44, 'pending', 'media', NULL, NULL, NULL, NULL, '2025-01-22 20:00:00', NULL),
(5, 5, 31, 'linguaggio_offensivo', 'Ha usato linguaggio inappropriato durante la partita.', 23, 'pending', 'alta', NULL, NULL, NULL, NULL, '2025-01-20 17:00:00', NULL),
(6, 7, 15, 'no_show', 'Assente alla prenotazione di calcetto.', 48, 'pending', 'media', NULL, NULL, NULL, NULL, '2025-01-29 11:00:00', NULL),
(7, 8, 22, 'comportamento_scorretto', 'Ha occupato il campo oltre l''orario prenotato.', 49, 'pending', 'bassa', NULL, NULL, NULL, NULL, '2025-01-30 15:00:00', NULL),

-- Segnalazioni IN_REVIEW (3)
(8, 11, 6, 'no_show', 'Secondo no-show consecutivo, molto scorretto.', 42, 'in_review', 'alta', 1, NULL, NULL, NULL, '2025-01-15 17:00:00', NULL),
(9, 13, 19, 'comportamento_scorretto', 'Ha discusso animatamente con altri giocatori.', 47, 'in_review', 'media', 2, NULL, NULL, NULL, '2025-01-28 17:00:00', NULL),
(10, 16, 28, 'no_show', 'Non si è presentato alla partita di calcio 7.', 50, 'in_review', 'alta', 1, NULL, NULL, NULL, '2025-01-30 16:00:00', NULL),

-- Segnalazioni RESOLVED (4)
(11, 4, 6, 'comportamento_scorretto', 'Arrivato in ritardo di 30 minuti senza avvisare.', 7, 'resolved', 'media', 1, 'penalty_points', 2, 'Assegnati 2 punti penalità per ritardo grave.', '2025-01-10 18:00:00', '2025-01-11 10:00:00'),
(12, 17, 6, 'no_show', 'Terzo no-show dell''utente, comportamento recidivo.', 42, 'resolved', 'alta', 1, 'sospensione', 5, 'Utente sospeso per 7 giorni per no-show ripetuti.', '2025-01-16 09:00:00', '2025-01-16 14:00:00'),
(13, 20, 33, 'altro', 'Ha portato cibo sul campo da tennis.', 24, 'resolved', 'bassa', 2, 'warning', 0, 'Warning verbale, nessuna penalità.', '2025-01-20 13:00:00', '2025-01-20 16:00:00'),
(14, 22, 18, 'linguaggio_offensivo', 'Insulti verso l''arbitro durante la partita.', 31, 'resolved', 'alta', 1, 'penalty_points', 3, 'Assegnati 3 punti penalità per linguaggio offensivo.', '2025-01-24 22:00:00', '2025-01-25 09:00:00'),

-- Segnalazione REJECTED (1)
(15, 30, 7, 'comportamento_scorretto', 'Giocava troppo forte.', 21, 'rejected', 'bassa', 2, 'nessuna', 0, 'Segnalazione non valida. Giocare bene non è scorretto.', '2025-01-18 14:00:00', '2025-01-18 17:00:00');

ALTER TABLE `segnalazioni`
  MODIFY `segnalazione_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;


-- -----------------------------------------------------
-- USER BADGES (badges sbloccati)
-- -----------------------------------------------------
INSERT INTO `user_badges` (`id`, `user_id`, `badge_id`, `sbloccato_at`) VALUES
-- Badge "Prima Prenotazione" (badge_id=1) - molti utenti
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
(23, 27, 1, '2025-01-26 09:05:00'),
(24, 28, 1, '2025-01-16 10:05:00'),
(25, 30, 1, '2025-01-17 09:05:00'),
(26, 31, 1, '2025-01-18 11:05:00'),
(27, 32, 1, '2025-01-19 08:05:00'),
(28, 33, 1, '2025-01-19 14:05:00'),

-- Badge "Recensore" (badge_id=5) - chi ha scritto almeno 1 recensione
(29, 4, 5, '2025-01-06 12:05:00'),
(30, 5, 5, '2025-01-06 16:05:00'),
(31, 7, 5, '2025-01-07 20:05:00'),
(32, 10, 5, '2025-01-08 18:05:00'),
(33, 12, 5, '2025-01-08 11:05:00'),
(34, 14, 5, '2025-01-09 20:05:00'),
(35, 8, 5, '2025-01-10 19:05:00'),
(36, 11, 5, '2025-01-11 16:05:00'),
(37, 13, 5, '2025-01-11 12:05:00'),
(38, 15, 5, '2025-01-13 21:05:00'),
(39, 17, 5, '2025-01-13 22:05:00'),
(40, 19, 5, '2025-01-14 19:05:00'),
(41, 20, 5, '2025-01-15 20:05:00'),
(42, 22, 5, '2025-01-15 17:05:00'),
(43, 24, 5, '2025-01-16 18:05:00'),
(44, 26, 5, '2025-01-17 15:05:00'),
(45, 28, 5, '2025-01-17 21:05:00'),
(46, 30, 5, '2025-01-18 19:05:00'),
(47, 31, 5, '2025-01-20 16:05:00'),
(48, 32, 5, '2025-01-20 12:05:00'),
(49, 33, 5, '2025-01-21 18:05:00'),
(50, 21, 5, '2025-01-25 12:05:00'),
(51, 23, 5, '2025-01-25 16:05:00'),
(52, 25, 5, '2025-01-27 19:05:00'),
(53, 18, 5, '2025-01-24 21:05:00'),
(54, 6, 5, '2025-01-10 17:05:00'),
(55, 16, 5, '2025-01-23 13:05:00'),

-- Badge "Sportivo Attivo" (badge_id=2) - utenti con 10+ prenotazioni
(56, 4, 2, '2025-01-21 17:55:00'),
(57, 5, 2, '2025-01-24 15:50:00'),
(58, 7, 2, '2025-01-28 19:58:00'),

-- Badge "Puntuale" (badge_id=7) - utenti con 10+ check-in puntuali
(59, 4, 7, '2025-01-21 17:55:00'),
(60, 5, 7, '2025-01-24 15:50:00'),

-- Badge "Multisport" (badge_id=9) - utenti che hanno provato 5+ sport diversi
(61, 5, 9, '2025-01-24 15:55:00'),

-- Badge "Campione" (badge_id=10) - utente livello Platinum
(62, 7, 10, '2025-01-05 16:10:00');

ALTER TABLE `user_badges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;


-- -----------------------------------------------------
-- NOTIFICHE (40 notifiche)
-- -----------------------------------------------------
INSERT INTO `notifiche` (`notifica_id`, `user_id`, `tipo`, `titolo`, `messaggio`, `letta`, `link`, `created_at`, `read_at`) VALUES
-- Notifiche LETTE
(1, 4, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A del 06/01/2025 alle 10:00 è stata confermata.', 1, 'le-mie-prenotazioni.php', '2025-01-04 14:30:00', '2025-01-04 14:35:00'),
(2, 4, 'badge_sbloccato', 'Nuovo Badge Sbloccato!', 'Hai sbloccato il badge Prima Prenotazione!', 1, 'profilo.php', '2025-01-04 14:35:00', '2025-01-04 14:40:00'),
(3, 5, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Palestra Basket del 06/01/2025 alle 14:00 è stata confermata.', 1, 'le-mie-prenotazioni.php', '2025-01-05 09:00:00', '2025-01-05 09:10:00'),
(4, 5, 'badge_sbloccato', 'Nuovo Badge Sbloccato!', 'Hai sbloccato il badge Prima Prenotazione!', 1, 'profilo.php', '2025-01-05 09:05:00', '2025-01-05 09:15:00'),
(5, 7, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 1 del 07/01/2025 alle 18:00 è stata confermata.', 1, 'le-mie-prenotazioni.php', '2025-01-05 16:00:00', '2025-01-05 16:05:00'),
(6, 4, 'promemoria_prenotazione', 'Promemoria Prenotazione', 'Ricorda: hai una prenotazione domani per Campo Calcetto A alle 10:00.', 1, 'le-mie-prenotazioni.php', '2025-01-05 18:00:00', '2025-01-05 18:30:00'),
(7, 5, 'promemoria_prenotazione', 'Promemoria Prenotazione', 'Ricorda: hai una prenotazione domani per Palestra Basket alle 14:00.', 1, 'le-mie-prenotazioni.php', '2025-01-05 18:00:00', '2025-01-05 19:00:00'),
(8, 6, 'penalty_ricevuti', 'Punti Penalità Ricevuti', 'Hai ricevuto 5 punti penalità per no-show alla prenotazione del 10/01/2025.', 1, 'profilo.php', '2025-01-10 12:00:00', '2025-01-10 14:00:00'),
(9, 4, 'badge_sbloccato', 'Nuovo Badge Sbloccato!', 'Hai sbloccato il badge Recensore!', 1, 'profilo.php', '2025-01-06 12:05:00', '2025-01-06 12:10:00'),
(10, 5, 'badge_sbloccato', 'Nuovo Badge Sbloccato!', 'Hai sbloccato il badge Recensore!', 1, 'profilo.php', '2025-01-06 16:05:00', '2025-01-06 16:10:00'),

(11, 6, 'sospensione_account', 'Account Sospeso', 'Il tuo account è stato sospeso fino al 23/01/2025. Motivo: No-show ripetuti.', 1, NULL, '2025-01-16 14:00:00', '2025-01-16 14:30:00'),
(12, 7, 'badge_sbloccato', 'Nuovo Badge Sbloccato!', 'Hai sbloccato il badge Campione! Sei al livello Platinum!', 1, 'profilo.php', '2025-01-05 16:10:00', '2025-01-05 16:15:00'),
(13, 4, 'badge_sbloccato', 'Nuovo Badge Sbloccato!', 'Hai sbloccato il badge Sportivo Attivo!', 1, 'profilo.php', '2025-01-21 17:55:00', '2025-01-21 18:00:00'),
(14, 5, 'badge_sbloccato', 'Nuovo Badge Sbloccato!', 'Hai sbloccato il badge Multisport! Hai provato 5 sport diversi!', 1, 'profilo.php', '2025-01-24 15:55:00', '2025-01-24 16:00:00'),
(15, 18, 'penalty_ricevuti', 'Punti Penalità Ricevuti', 'Hai ricevuto 3 punti penalità per linguaggio offensivo.', 1, 'profilo.php', '2025-01-25 09:00:00', '2025-01-25 10:00:00'),

-- Notifiche NON LETTE (recenti)
(16, 4, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A del 19/01/2025 alle 10:00 è stata confermata.', 0, 'le-mie-prenotazioni.php', '2025-01-17 09:00:00', NULL),
(17, 4, 'promemoria_prenotazione', 'Promemoria Prenotazione', 'Ricorda: hai una prenotazione domani per Campo Calcetto A alle 10:00.', 0, 'le-mie-prenotazioni.php', '2025-01-18 18:00:00', NULL),
(18, 5, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 1 del 19/01/2025 alle 15:00 è stata confermata.', 0, 'le-mie-prenotazioni.php', '2025-01-17 14:00:00', NULL),
(19, 5, 'promemoria_prenotazione', 'Promemoria Prenotazione', 'Ricorda: hai una prenotazione domani per Campo Padel 1 alle 15:00.', 0, 'le-mie-prenotazioni.php', '2025-01-18 18:00:00', NULL),
(20, 7, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Palestra Basket del 19/01/2025 alle 18:00 è stata confermata.', 0, 'le-mie-prenotazioni.php', '2025-01-17 16:00:00', NULL),
(21, 7, 'promemoria_prenotazione', 'Promemoria Prenotazione', 'Ricorda: hai una prenotazione domani per Palestra Basket alle 18:00.', 0, 'le-mie-prenotazioni.php', '2025-01-18 18:00:00', NULL),
(22, 10, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Campo Pallavolo del 20/01/2025 alle 14:00 è stata confermata.', 0, 'le-mie-prenotazioni.php', '2025-01-18 08:00:00', NULL),
(23, 12, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 2 del 20/01/2025 alle 17:00 è stata confermata.', 0, 'le-mie-prenotazioni.php', '2025-01-18 10:00:00', NULL),
(24, 14, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto B del 21/01/2025 alle 16:00 è stata confermata.', 0, 'le-mie-prenotazioni.php', '2025-01-18 15:00:00', NULL),
(25, 16, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Sala Ping Pong del 21/01/2025 alle 13:00 è stata confermata.', 0, 'le-mie-prenotazioni.php', '2025-01-18 18:00:00', NULL),

(26, 18, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A del 22/01/2025 alle 11:00 è stata confermata.', 0, 'le-mie-prenotazioni.php', '2025-01-19 09:00:00', NULL),
(27, 21, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Campo Tennis 1 del 22/01/2025 alle 10:00 è stata confermata.', 0, 'le-mie-prenotazioni.php', '2025-01-19 11:00:00', NULL),
(28, 23, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 1 del 23/01/2025 alle 19:00 è stata confermata.', 0, 'le-mie-prenotazioni.php', '2025-01-19 14:00:00', NULL),
(29, 25, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcio 7 del 24/01/2025 alle 15:00 è stata confermata.', 0, 'le-mie-prenotazioni.php', '2025-01-19 16:00:00', NULL),
(30, 27, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Campo Pallavolo del 25/01/2025 alle 16:00 è stata confermata.', 0, 'le-mie-prenotazioni.php', '2025-01-20 08:00:00', NULL),

(31, 8, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Palestra Basket del 25/01/2025 alle 20:00 è stata confermata.', 0, 'le-mie-prenotazioni.php', '2025-01-20 10:00:00', NULL),
(32, 11, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Campo Padel 2 del 26/01/2025 alle 11:00 è stata confermata.', 0, 'le-mie-prenotazioni.php', '2025-01-20 12:00:00', NULL),
(33, 13, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per Campo Calcetto A del 26/01/2025 alle 17:00 è stata confermata.', 0, 'le-mie-prenotazioni.php', '2025-01-20 15:00:00', NULL),
(34, 6, 'penalty_ricevuti', 'Punti Penalità Ricevuti', 'Hai ricevuto 2 punti penalità per ritardo grave.', 0, 'profilo.php', '2025-01-11 10:00:00', NULL),
(35, 17, 'penalty_ricevuti', 'Punti Penalità Ricevuti', 'Hai ricevuto 5 punti penalità per no-show alla prenotazione del 20/01/2025.', 0, 'profilo.php', '2025-01-20 12:00:00', NULL),

-- Inviti a prenotazioni
(36, 5, 'invito_prenotazione', 'Invito a Prenotazione', 'Mario Verdi ti ha invitato a partecipare alla prenotazione del 19/01/2025 alle 10:00 presso Campo Calcetto A.', 0, 'le-mie-prenotazioni.php', '2025-01-17 09:05:00', NULL),
(37, 10, 'invito_prenotazione', 'Invito a Prenotazione', 'Mario Verdi ti ha invitato a partecipare alla prenotazione del 19/01/2025 alle 10:00 presso Campo Calcetto A.', 0, 'le-mie-prenotazioni.php', '2025-01-17 09:05:00', NULL),
(38, 12, 'invito_prenotazione', 'Invito a Prenotazione', 'Mario Verdi ti ha invitato a partecipare alla prenotazione del 19/01/2025 alle 10:00 presso Campo Calcetto A.', 0, 'le-mie-prenotazioni.php', '2025-01-17 09:05:00', NULL),
(39, 14, 'invito_prenotazione', 'Invito a Prenotazione', 'Sara Blu ti ha invitato a partecipare alla prenotazione del 19/01/2025 alle 18:00 presso Palestra Basket.', 0, 'le-mie-prenotazioni.php', '2025-01-17 16:05:00', NULL),
(40, 8, 'invito_prenotazione', 'Invito a Prenotazione', 'Sara Blu ti ha invitato a partecipare alla prenotazione del 19/01/2025 alle 18:00 presso Palestra Basket.', 0, 'le-mie-prenotazioni.php', '2025-01-17 16:05:00', NULL);

ALTER TABLE `notifiche`
  MODIFY `notifica_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;


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

ALTER TABLE `penalty_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;


-- -----------------------------------------------------
-- SANZIONI (sospensioni/ban)
-- -----------------------------------------------------
INSERT INTO `sanzioni` (`sanzione_id`, `user_id`, `tipo`, `motivo`, `data_inizio`, `data_fine`, `admin_id`, `attiva`, `created_at`) VALUES
(1, 6, 'sospensione', 'No-show ripetuti (3 volte in un mese)', '2025-01-16 14:00:00', '2025-01-23 14:00:00', 1, 0, '2025-01-16 14:00:00'),
(2, 29, 'ban', 'Comportamento gravemente scorretto e violazione ripetuta delle regole', '2024-12-15 10:00:00', NULL, 1, 1, '2024-12-15 10:00:00');

ALTER TABLE `sanzioni`
  MODIFY `sanzione_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;