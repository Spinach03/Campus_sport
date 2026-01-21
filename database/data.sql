-- =====================================================
-- DATI DI ESEMPIO - Campus Sports Arena
-- =====================================================
-- Eseguire DOPO aver eseguito schema.sql
-- =====================================================

USE `campus_sports_arena`;

-- -----------------------------------------------------
-- Dati LIVELLI
-- -----------------------------------------------------
INSERT INTO `livelli` (`livello_id`, `nome`, `xp_minimo`, `xp_massimo`, `max_prenotazioni_simultanee`, `max_ore_settimanali`, `giorni_anticipo_prenotazione`) VALUES
(1, 'Bronze', 0, 499, 3, 4, 7),
(2, 'Silver', 500, 1499, 4, 5, 10),
(3, 'Gold', 1500, 3999, 5, 6, 14),
(4, 'Platinum', 4000, 999999, 6, 8, 21);

ALTER TABLE `livelli`
  MODIFY `livello_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;


-- -----------------------------------------------------
-- Dati CORSI DI LAUREA
-- -----------------------------------------------------
INSERT INTO `corsi_laurea` (`corso_id`, `nome`, `facolta`, `attivo`) VALUES
(1, 'Informatica', 'Scienze e Tecnologie', 1),
(2, 'Ingegneria Informatica', 'Ingegneria', 1),
(3, 'Economia Aziendale', 'Economia', 1),
(4, 'Giurisprudenza', 'Giurisprudenza', 1),
(5, 'Scienze Motorie', 'Scienze Motorie', 1),
(6, 'Medicina e Chirurgia', 'Medicina', 1),
(7, 'Architettura', 'Architettura', 1),
(8, 'Lettere Moderne', 'Lettere e Filosofia', 1);

ALTER TABLE `corsi_laurea`
  MODIFY `corso_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;


-- -----------------------------------------------------
-- Dati SPORT
-- -----------------------------------------------------
INSERT INTO `sport` (`sport_id`, `nome`, `descrizione`, `num_giocatori_standard`, `icona`, `attivo`) VALUES
(1, 'Calcio a 5', 'Calcio a 5 giocatori per squadra', 10, 'calcio5.png', 1),
(2, 'Calcio a 7', 'Calcio a 7 giocatori per squadra', 14, 'calcio7.png', 1),
(3, 'Basket', 'Pallacanestro 5 contro 5', 10, 'basket.png', 1),
(4, 'Pallavolo', 'Pallavolo 6 contro 6', 12, 'pallavolo.png', 1),
(5, 'Tennis', 'Tennis singolo o doppio', 4, 'tennis.png', 1),
(6, 'Padel', 'Padel in doppio', 4, 'padel.png', 1),
(7, 'Badminton', 'Badminton singolo o doppio', 4, 'badminton.png', 1),
(8, 'Ping Pong', 'Tennis tavolo', 4, 'pingpong.png', 1);

ALTER TABLE `sport`
  MODIFY `sport_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;


-- -----------------------------------------------------
-- Dati BADGES
-- -----------------------------------------------------
INSERT INTO `badges` (`badge_id`, `nome`, `descrizione`, `icona`, `criterio_tipo`, `criterio_valore`, `xp_reward`, `categoria`, `rarita`, `attivo`) VALUES
(1, 'Prima Prenotazione', 'Hai effettuato la tua prima prenotazione', 'badge_first.png', 'prenotazioni_totali', 1, 50, 'prenotazioni', 'comune', 1),
(2, 'Sportivo Attivo', 'Hai completato 10 prenotazioni', 'badge_active.png', 'prenotazioni_totali', 10, 100, 'prenotazioni', 'comune', 1),
(3, 'Veterano', 'Hai completato 50 prenotazioni', 'badge_veteran.png', 'prenotazioni_totali', 50, 250, 'prenotazioni', 'raro', 1),
(4, 'Leggenda', 'Hai completato 100 prenotazioni', 'badge_legend.png', 'prenotazioni_totali', 100, 500, 'prenotazioni', 'epico', 1),
(5, 'Recensore', 'Hai scritto la tua prima recensione', 'badge_review.png', 'recensioni_totali', 1, 30, 'recensioni', 'comune', 1),
(6, 'Critico Esperto', 'Hai scritto 20 recensioni', 'badge_critic.png', 'recensioni_totali', 20, 150, 'recensioni', 'raro', 1),
(7, 'Puntuale', 'Hai effettuato 10 check-in puntuali', 'badge_punctual.png', 'checkin_puntuali', 10, 100, 'comportamento', 'comune', 1),
(8, 'Sempre Presente', 'Nessun no-show in 30 prenotazioni', 'badge_reliable.png', 'prenotazioni_senza_noshow', 30, 200, 'comportamento', 'raro', 1),
(9, 'Multisport', 'Hai prenotato campi di 5 sport diversi', 'badge_multi.png', 'sport_diversi', 5, 150, 'varieta', 'raro', 1),
(10, 'Campione', 'Hai raggiunto il livello Platinum', 'badge_champion.png', 'livello_raggiunto', 4, 300, 'livello', 'leggendario', 1);

ALTER TABLE `badges`
  MODIFY `badge_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;


-- -----------------------------------------------------
-- Dati USERS - ADMIN
-- -----------------------------------------------------
INSERT INTO `users` (`user_id`, `email`, `password_hash`, `nome`, `cognome`, `telefono`, `ruolo`, `stato`, `ultimo_accesso`) VALUES
(1, 'marco.rossi@unibo.it', 'MarcoRossi_01!', 'Marco', 'Rossi', '3331234567', 'admin', 'attivo', NOW()),
(2, 'laura.bianchi@unibo.it', 'LauraBianchi_02!', 'Laura', 'Bianchi', '3339876543', 'admin', 'attivo', NOW()),
(3, 'giuseppe.ferrari@unibo.it', 'GiuseppeFerrari_03!', 'Giuseppe', 'Ferrari', '3351239876', 'admin', 'attivo', NOW());

-- -----------------------------------------------------
-- Dati ADMINS (tabella figlia)
-- -----------------------------------------------------
INSERT INTO `admins` (`user_id`) VALUES
(1),
(2),
(3);


-- -----------------------------------------------------
-- Dati USERS - UTENTI STANDARD
-- -----------------------------------------------------
INSERT INTO `users` (`user_id`, `email`, `password_hash`, `nome`, `cognome`, `telefono`, `ruolo`, `stato`, `ultimo_accesso`) VALUES
(4, 'mario.verdi@studio.unibo.it', 'MarioVerdi_04!', 'Mario', 'Verdi', '3401112233', 'user', 'attivo', NOW()),
(5, 'giulia.neri@studio.unibo.it', 'GiuliaNeri_05!', 'Giulia', 'Neri', '3402223344', 'user', 'attivo', NOW()),
(6, 'luca.gialli@studio.unibo.it', 'LucaGialli_06!', 'Luca', 'Gialli', '3403334455', 'user', 'attivo', NOW()),
(7, 'sara.blu@studio.unibo.it', 'SaraBlu_07!', 'Sara', 'Blu', '3404445566', 'user', 'attivo', NOW()),
(8, 'andrea.rosa@studio.unibo.it', 'AndreaRosa_08!', 'Andrea', 'Rosa', '3405556677', 'user', 'attivo', NOW()),
(9, 'francesca.viola@studio.unibo.it', 'FrancescaViola_09!', 'Francesca', 'Viola', '3406667788', 'user', 'sospeso', NULL),
(10, 'alessandro.romano@studio.unibo.it', 'AlessandroRomano_10!', 'Alessandro', 'Romano', '3407778899', 'user', 'attivo', NOW()),
(11, 'chiara.costa@studio.unibo.it', 'ChiaraCosta_11!', 'Chiara', 'Costa', '3408889900', 'user', 'attivo', NOW()),
(12, 'matteo.greco@studio.unibo.it', 'MatteoGreco_12!', 'Matteo', 'Greco', '3409990011', 'user', 'attivo', NOW()),
(13, 'valentina.marino@studio.unibo.it', 'ValentinaMarino_13!', 'Valentina', 'Marino', '3410001122', 'user', 'attivo', NOW()),
(14, 'federico.ricci@studio.unibo.it', 'FedericoRicci_14!', 'Federico', 'Ricci', '3411112233', 'user', 'attivo', NOW()),
(15, 'elena.gallo@studio.unibo.it', 'ElenaGallo_15!', 'Elena', 'Gallo', '3412223344', 'user', 'attivo', NOW()),
(16, 'davide.conti@studio.unibo.it', 'DavideConti_16!', 'Davide', 'Conti', '3413334455', 'user', 'attivo', NOW()),
(17, 'martina.delucia@studio.unibo.it', 'MartinaDeluca_17!', 'Martina', 'De Luca', '3414445566', 'user', 'attivo', NOW()),
(18, 'simone.mancini@studio.unibo.it', 'SimoneMancini_18!', 'Simone', 'Mancini', '3415556677', 'user', 'attivo', NOW()),
(19, 'anna.moretti@studio.unibo.it', 'AnnaMoretti_19!', 'Anna', 'Moretti', '3416667788', 'user', 'attivo', NOW()),
(20, 'lorenzo.barbieri@studio.unibo.it', 'LorenzoBarbieri_20!', 'Lorenzo', 'Barbieri', '3417778899', 'user', 'attivo', NOW()),
(21, 'silvia.fontana@studio.unibo.it', 'SilviaFontana_21!', 'Silvia', 'Fontana', '3418889900', 'user', 'attivo', NOW()),
(22, 'michele.santoro@studio.unibo.it', 'MicheleSantoro_22!', 'Michele', 'Santoro', '3419990011', 'user', 'attivo', NOW()),
(23, 'giorgia.mariani@studio.unibo.it', 'GiorgiaMariani_23!', 'Giorgia', 'Mariani', '3420001122', 'user', 'attivo', NOW()),
(24, 'riccardo.ferrara@studio.unibo.it', 'RiccardoFerrara_24!', 'Riccardo', 'Ferrara', '3421112233', 'user', 'attivo', NOW()),
(25, 'elisa.bruno@studio.unibo.it', 'ElisaBruno_25!', 'Elisa', 'Bruno', '3422223344', 'user', 'attivo', NOW()),
(26, 'giacomo.pellegrini@studio.unibo.it', 'GiacomoPellegrini_26!', 'Giacomo', 'Pellegrini', '3423334455', 'user', 'attivo', NOW()),
(27, 'roberta.sanna@studio.unibo.it', 'RobertaSanna_27!', 'Roberta', 'Sanna', '3424445566', 'user', 'attivo', NOW()),
(28, 'stefano.fabbri@studio.unibo.it', 'StefanoFabbri_28!', 'Stefano', 'Fabbri', '3425556677', 'user', 'attivo', NOW()),
(29, 'claudia.rinaldi@studio.unibo.it', 'ClaudiaRinaldi_29!', 'Claudia', 'Rinaldi', '3426667788', 'user', 'bannato', NULL),
(30, 'paolo.caruso@studio.unibo.it', 'PaoloCaruso_30!', 'Paolo', 'Caruso', '3427778899', 'user', 'attivo', NOW()),
(31, 'monica.leone@studio.unibo.it', 'MonicaLeone_31!', 'Monica', 'Leone', '3428889900', 'user', 'attivo', NOW()),
(32, 'nicola.longo@studio.unibo.it', 'NicolaLongo_32!', 'Nicola', 'Longo', '3429990011', 'user', 'attivo', NOW()),
(33, 'federica.marchetti@studio.unibo.it', 'FedericaMarchetti_33!', 'Federica', 'Marchetti', '3430001122', 'user', 'attivo', NOW());

ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;


-- -----------------------------------------------------
-- Dati UTENTI STANDARD (tabella figlia)
-- -----------------------------------------------------
INSERT INTO `utenti_standard` (`user_id`, `corso_laurea_id`, `anno_iscrizione`, `data_nascita`, `indirizzo`, `penalty_points`, `xp_points`, `livello_id`) VALUES
(4, 1, 2022, '2001-05-15', 'Via Roma 10, Bologna', 0, 750, 2),
(5, 2, 2021, '2000-08-22', 'Via Dante 25, Bologna', 0, 1600, 3),
(6, 5, 2023, '2002-03-10', 'Via Manzoni 5, Bologna', 2, 200, 1),
(7, 3, 2022, '2001-11-30', 'Via Verdi 18, Bologna', 0, 4500, 4),
(8, 1, 2023, '2003-01-25', 'Via Garibaldi 7, Bologna', 0, 50, 1),
(9, 4, 2021, '2000-06-12', 'Via Cavour 33, Bologna', 15, 300, 1),
(10, 2, 2022, '2001-09-18', 'Via Marconi 12, Bologna', 0, 520, 2),
(11, 1, 2023, '2002-04-05', 'Via Farini 8, Bologna', 0, 180, 1),
(12, 3, 2021, '2000-12-28', 'Via Indipendenza 45, Bologna', 0, 2200, 3),
(13, 5, 2022, '2001-07-14', 'Via Rizzoli 22, Bologna', 0, 890, 2),
(14, 6, 2020, '1999-02-19', 'Via Ugo Bassi 15, Bologna', 0, 4100, 4),
(15, 2, 2023, '2003-05-30', 'Via Oberdan 9, Bologna', 0, 75, 1),
(16, 4, 2022, '2001-10-08', 'Via Irnerio 28, Bologna', 3, 420, 1),
(17, 1, 2021, '2000-03-22', 'Via Zamboni 33, Bologna', 0, 1850, 3),
(18, 7, 2023, '2002-08-16', 'Via Belle Arti 6, Bologna', 0, 95, 1),
(19, 3, 2022, '2001-01-11', 'Via Mascarella 19, Bologna', 0, 670, 2),
(20, 5, 2021, '2000-06-25', 'Via Petroni 11, Bologna', 0, 3500, 3),
(21, 8, 2023, '2002-11-03', 'Via San Felice 27, Bologna', 0, 45, 1),
(22, 2, 2022, '2001-04-17', 'Via Saragozza 88, Bologna', 0, 980, 2),
(23, 1, 2021, '2000-09-09', 'Via Andrea Costa 52, Bologna', 0, 1450, 2),
(24, 6, 2020, '1999-07-21', 'Via Saffi 16, Bologna', 5, 2800, 3),
(25, 4, 2023, '2002-12-14', 'Via San Donato 34, Bologna', 0, 120, 1),
(26, 3, 2022, '2001-02-28', 'Via Massarenti 41, Bologna', 0, 550, 2),
(27, 7, 2021, '2000-05-06', 'Via Mazzini 23, Bologna', 0, 1680, 3),
(28, 1, 2023, '2003-03-19', 'Via San Vitale 67, Bologna', 0, 85, 1),
(29, 5, 2020, '1999-10-31', 'Via Castiglione 29, Bologna', 25, 1200, 2),
(30, 2, 2022, '2001-08-08', 'Via Santo Stefano 14, Bologna', 0, 720, 2),
(31, 8, 2023, '2002-06-22', 'Via Maggiore 56, Bologna', 0, 60, 1),
(32, 6, 2021, '2000-01-15', 'Via Galliera 39, Bologna', 0, 2100, 3),
(33, 4, 2022, '2001-12-05', 'Via del Pratello 71, Bologna', 0, 480, 1);


-- -----------------------------------------------------
-- Dati CAMPI SPORTIVI
-- -----------------------------------------------------
INSERT INTO `campi_sportivi` (`campo_id`, `nome`, `sport_id`, `location`, `descrizione`, `capienza_max`, `tipo_superficie`, `tipo_campo`, `lunghezza_m`, `larghezza_m`, `orario_apertura`, `orario_chiusura`, `stato`, `rating_medio`, `num_recensioni`, `created_by`) VALUES
(1, 'Campo Calcetto A', 1, 'Edificio Sport - Piano Terra', 'Campo da calcio a 5 con erba sintetica di ultima generazione', 10, 'erba_sintetica', 'indoor', 40.00, 20.00, '08:00:00', '22:00:00', 'disponibile', 4.5, 12, 1),
(2, 'Campo Calcetto B', 1, 'Edificio Sport - Piano Terra', 'Campo da calcio a 5 esterno', 10, 'erba_sintetica', 'outdoor', 40.00, 20.00, '08:00:00', '20:00:00', 'disponibile', 4.2, 8, 1),
(3, 'Campo Calcio 7', 2, 'Area Sportiva Nord', 'Campo da calcio a 7 regolamentare', 14, 'erba_naturale', 'outdoor', 60.00, 40.00, '09:00:00', '19:00:00', 'disponibile', 4.0, 5, 1),
(4, 'Palestra Basket', 3, 'Palazzetto dello Sport', 'Campo da basket indoor con parquet', 10, 'parquet', 'indoor', 28.00, 15.00, '08:00:00', '22:00:00', 'disponibile', 4.8, 20, 2),
(5, 'Campo Pallavolo', 4, 'Palazzetto dello Sport', 'Campo da pallavolo regolamentare', 12, 'parquet', 'indoor', 18.00, 9.00, '08:00:00', '22:00:00', 'disponibile', 4.6, 15, 2),
(6, 'Campo Tennis 1', 5, 'Area Tennis', 'Campo da tennis in terra battuta', 4, 'terra_battuta', 'outdoor', 23.77, 10.97, '08:00:00', '20:00:00', 'disponibile', 4.3, 10, 2),
(7, 'Campo Tennis 2', 5, 'Area Tennis', 'Campo da tennis in cemento', 4, 'cemento', 'outdoor', 23.77, 10.97, '08:00:00', '20:00:00', 'manutenzione', 3.9, 7, 3),
(8, 'Campo Padel 1', 6, 'Area Padel', 'Campo da padel coperto', 4, 'erba_sintetica', 'indoor', 20.00, 10.00, '08:00:00', '23:00:00', 'disponibile', 4.7, 18, 3),
(9, 'Campo Padel 2', 6, 'Area Padel', 'Campo da padel esterno', 4, 'erba_sintetica', 'outdoor', 20.00, 10.00, '08:00:00', '21:00:00', 'disponibile', 4.4, 11, 3),
(10, 'Sala Ping Pong', 8, 'Edificio Sport - Primo Piano', 'Sala con 4 tavoli da ping pong', 8, 'parquet', 'indoor', 14.00, 7.00, '08:00:00', '22:00:00', 'disponibile', 4.1, 6, 3);

ALTER TABLE `campi_sportivi`
  MODIFY `campo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;


-- -----------------------------------------------------
-- Dati CAMPO SERVIZI
-- -----------------------------------------------------
INSERT INTO `campo_servizi` (`campo_id`, `illuminazione_notturna`, `spogliatoi`, `docce`, `parcheggio`, `distributori`, `noleggio_attrezzatura`, `bar_ristoro`) VALUES
(1, 1, 1, 1, 1, 1, 1, 0),
(2, 1, 1, 1, 1, 1, 1, 0),
(3, 0, 1, 1, 1, 0, 0, 0),
(4, 1, 1, 1, 1, 1, 1, 1),
(5, 1, 1, 1, 1, 1, 1, 1),
(6, 0, 1, 1, 1, 1, 1, 0),
(7, 0, 1, 1, 1, 1, 1, 0),
(8, 1, 1, 1, 1, 1, 1, 1),
(9, 1, 1, 1, 1, 1, 1, 0),
(10, 1, 0, 0, 1, 1, 1, 0);


-- -----------------------------------------------------
-- Dati CAMPO FOTO
-- -----------------------------------------------------
INSERT INTO `campo_foto` (`foto_id`, `campo_id`, `path_foto`, `is_principale`, `ordine`) VALUES
(1, 1, 'calcetto_a_1.jpg', 1, 1),
(2, 1, 'calcetto_a_2.jpg', 0, 2),
(3, 2, 'calcetto_b_1.jpg', 1, 1),
(4, 3, 'calcio7_1.jpg', 1, 1),
(5, 4, 'basket_1.jpg', 1, 1),
(6, 4, 'basket_2.jpg', 0, 2),
(7, 5, 'pallavolo_1.jpg', 1, 1),
(8, 6, 'tennis1_1.jpg', 1, 1),
(9, 7, 'tennis2_1.jpg', 1, 1),
(10, 8, 'padel1_1.jpg', 1, 1),
(11, 8, 'padel1_2.jpg', 0, 2),
(12, 9, 'padel2_1.jpg', 1, 1),
(13, 10, 'pingpong_1.jpg', 1, 1);

ALTER TABLE `campo_foto`
  MODIFY `foto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;


-- -----------------------------------------------------
-- Dati USER SPORT PREFERITI
-- -----------------------------------------------------
INSERT INTO `user_sport_preferiti` (`id`, `user_id`, `sport_id`) VALUES
(1, 4, 1),
(2, 4, 3),
(3, 5, 1),
(4, 5, 6),
(5, 5, 5),
(6, 6, 3),
(7, 6, 4),
(8, 7, 6),
(9, 7, 8),
(10, 8, 1),
(11, 10, 1),
(12, 10, 2),
(13, 11, 3),
(14, 12, 6),
(15, 12, 5),
(16, 13, 4),
(17, 13, 3),
(18, 14, 1),
(19, 14, 6),
(20, 15, 5),
(21, 16, 8),
(22, 17, 1),
(23, 17, 3),
(24, 18, 7),
(25, 19, 6),
(26, 20, 4),
(27, 20, 1),
(28, 21, 8),
(29, 22, 2),
(30, 22, 1),
(31, 23, 6),
(32, 24, 3),
(33, 25, 5),
(34, 26, 1),
(35, 27, 4),
(36, 28, 6),
(37, 29, 1),
(38, 30, 3),
(39, 31, 8),
(40, 32, 2),
(41, 33, 5);

ALTER TABLE `user_sport_preferiti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;


-- -----------------------------------------------------
-- Dati SYSTEM CONFIG
-- -----------------------------------------------------
INSERT INTO `system_config` (`config_id`, `chiave`, `valore`, `tipo`, `descrizione`, `updated_by`) VALUES
(1, 'max_ore_settimanali_default', '4', 'int', 'Ore massime prenotabili a settimana per utenti base', 1),
(2, 'giorni_anticipo_prenotazione', '7', 'int', 'Giorni di anticipo massimo per prenotare', 1),
(3, 'durata_slot_minuti', '60', 'int', 'Durata standard di uno slot in minuti', 1),
(4, 'ore_anticipo_cancellazione', '24', 'int', 'Ore minime di anticipo per cancellare senza penalita', 1),
(5, 'penalty_no_show', '5', 'int', 'Punti penalita per no-show', 1),
(6, 'penalty_cancellazione_tardiva', '2', 'int', 'Punti penalita per cancellazione tardiva', 1),
(7, 'soglia_penalty_sospensione', '20', 'int', 'Soglia punti per sospensione automatica', 1),
(8, 'check_in_window_minuti_prima', '15', 'int', 'Minuti prima dell inizio per effettuare check-in', 1),
(9, 'check_in_window_minuti_dopo', '15', 'int', 'Minuti dopo l inizio per effettuare check-in', 1),
(10, 'xp_per_prenotazione', '10', 'int', 'XP guadagnati per ogni prenotazione completata', 1),
(11, 'xp_per_recensione', '5', 'int', 'XP guadagnati per ogni recensione', 1);

ALTER TABLE `system_config`
  MODIFY `config_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;


-- -----------------------------------------------------
-- Dati NOTIFICATION TEMPLATES
-- -----------------------------------------------------
INSERT INTO `notification_templates` (`template_id`, `tipo`, `titolo_template`, `messaggio_template`, `canale`, `attivo`, `updated_by`) VALUES
(1, 'prenotazione_confermata', 'Prenotazione Confermata', 'La tua prenotazione per {{campo}} del {{data}} alle {{ora}} e stata confermata.', 'entrambi', 1, 1),
(2, 'prenotazione_cancellata', 'Prenotazione Cancellata', 'La tua prenotazione per {{campo}} del {{data}} e stata cancellata.', 'entrambi', 1, 1),
(3, 'promemoria_prenotazione', 'Promemoria Prenotazione', 'Ricorda: hai una prenotazione domani per {{campo}} alle {{ora}}.', 'entrambi', 1, 1),
(4, 'penalty_ricevuti', 'Punti Penalita Ricevuti', 'Hai ricevuto {{punti}} punti penalita per {{motivo}}.', 'in_app', 1, 1),
(5, 'livello_raggiunto', 'Nuovo Livello Raggiunto!', 'Congratulazioni! Hai raggiunto il livello {{livello}}.', 'entrambi', 1, 1),
(6, 'badge_sbloccato', 'Nuovo Badge Sbloccato!', 'Hai sbloccato il badge {{badge}}!', 'in_app', 1, 1),
(7, 'sospensione_account', 'Account Sospeso', 'Il tuo account e stato sospeso fino al {{data_fine}}. Motivo: {{motivo}}', 'entrambi', 1, 1),
(8, 'invito_prenotazione', 'Invito a Prenotazione', '{{utente}} ti ha invitato a partecipare alla prenotazione del {{data}} alle {{ora}} presso {{campo}}.', 'entrambi', 1, 1);

ALTER TABLE `notification_templates`
  MODIFY `template_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

