-- =====================================================
-- DATI AGGIUNTIVI - Campus Sports Arena
-- =====================================================
-- Eseguire DOPO data_extra_fixed.sql
-- Contiene: recensioni aggiuntive, segnalazioni, broadcast, notifiche
-- =====================================================

USE `campus_sports_arena`;

-- =====================================================
-- RECENSIONI AGGIUNTIVE (25 recensioni)
-- Con rating dettagliati, mix positive/negative
-- =====================================================

INSERT INTO `recensioni` (`recensione_id`, `prenotazione_id`, `user_id`, `campo_id`, `rating_generale`, `rating_condizioni`, `rating_pulizia`, `rating_illuminazione`, `commento`, `voti_utili`, `created_at`) VALUES
-- Recensioni Sala Ping Pong (campo_id=10) - mancavano
(36, 9, 11, 10, 4, 4, 5, 4, 'Tavoli in buone condizioni, ambiente pulito. Ottimo per rilassarsi tra le lezioni.', 3, '2025-01-11 16:00:00'),
(37, 19, 26, 10, 3, 3, 3, 4, 'Sala carina ma tavoli un po'' usurati. Racchette da sostituire.', 1, '2025-01-17 15:00:00'),
(38, 29, 14, 10, 5, 5, 5, 5, 'Perfetto! Tavoli professionali, ambiente climatizzato. Top!', 8, '2025-01-23 17:00:00'),
(39, 38, 8, 10, 4, 4, 4, 5, 'Buona sala, illuminazione eccellente. Consigliata.', 2, '2025-01-28 15:00:00'),

-- Recensioni Campo Calcetto A - NEGATIVE (per bilanciare)
(40, 14, 19, 1, 2, 2, 2, 3, 'Campo in pessime condizioni oggi. Erba sintetica rovinata in più punti, pericoloso.', 5, '2025-01-14 19:00:00'),

-- Recensioni aggiuntive con rating dettagliati - Campo Calcetto B
(41, 48, 15, 2, 1, 1, 2, 2, 'Esperienza terribile. Campo bagnato, nessun avviso. Mi sono fatto male.', 12, '2025-01-29 12:00:00'),

-- Recensioni Campo Calcio 7 - MIX
(42, 50, 28, 3, 2, 2, 3, 3, 'Campo in condizioni scadenti. Erba alta e buche ovunque. Deludente.', 7, '2025-01-30 17:00:00'),

-- Recensioni Palestra Basket - DETTAGLIATE
(43, 42, 6, 4, 3, 4, 2, 4, 'Palestra bella ma spogliatoi sporchi. Docce non funzionanti.', 4, '2025-01-15 18:00:00'),

-- Recensioni Campo Pallavolo - MIX
(44, 45, 26, 5, 2, 2, 2, 3, 'Rete allentata, pavimento scivoloso. Rischiato infortunio.', 6, '2025-01-25 19:00:00'),

-- Recensioni Campo Tennis - DETTAGLIATE POSITIVE
(45, 46, 31, 6, 5, 5, 5, 4, 'Campo perfetto! Terra battuta ben mantenuta, rimbalzo regolare.', 9, '2025-01-27 13:00:00'),

-- Recensioni Campo Padel 1 - MOLTO POSITIVE
(46, 44, 20, 8, 5, 5, 5, 5, 'Miglior campo padel mai provato! Vetri pulitissimi, illuminazione LED perfetta.', 15, '2025-01-22 21:00:00'),

-- Recensioni Campo Padel 2 - NEGATIVE
(47, 47, 19, 9, 2, 2, 3, 2, 'Campo trascurato. Erba sintetica consumata, vetri sporchi.', 4, '2025-01-28 18:00:00'),

-- Recensioni aggiuntive varie - per aumentare il volume
(48, 43, 17, 1, 3, 3, 4, 3, 'Niente di speciale. Campo nella media, potrebbe essere tenuto meglio.', 1, '2025-01-20 12:00:00'),

-- Recensioni con solo rating generale (senza dettagli)
(49, 41, 6, 1, 1, NULL, NULL, NULL, 'Mai più. Campo impraticabile.', 3, '2025-01-10 11:00:00'),
(50, 49, 22, 10, 4, NULL, NULL, NULL, 'Bella sala, ci torno volentieri.', 0, '2025-01-30 16:00:00');

ALTER TABLE `recensioni` AUTO_INCREMENT = 51;


-- =====================================================
-- SEGNALAZIONI AGGIUNTIVE (15 segnalazioni)
-- Tutti i tipi, tutti gli stati
-- =====================================================

INSERT INTO `segnalazioni` (`segnalazione_id`, `user_segnalante_id`, `user_segnalato_id`, `tipo`, `descrizione`, `prenotazione_id`, `stato`, `priorita`, `admin_id`, `azione_intrapresa`, `penalty_assegnati`, `note_risoluzione`, `created_at`, `resolved_at`) VALUES

-- SEGNALAZIONI PENDING - Vari tipi
(16, 21, 9, 'violenza', 'L''utente mi ha spinto durante la partita e ha minacciato di picchiarmi. Ho testimoni.', 33, 'pending', 'alta', NULL, NULL, NULL, NULL, '2025-01-25 15:00:00', NULL),
(17, 23, 24, 'linguaggio_offensivo', 'Ha insultato pesantemente me e i miei compagni di squadra con epiteti razzisti.', 34, 'pending', 'alta', NULL, NULL, NULL, NULL, '2025-01-25 18:00:00', NULL),
(18, 25, 16, 'comportamento_scorretto', 'Ha danneggiato intenzionalmente la rete del campo.', 35, 'pending', 'media', NULL, NULL, NULL, NULL, '2025-01-27 20:00:00', NULL),
(19, 27, 29, 'altro', 'Ho visto l''utente fumare all''interno della struttura sportiva.', NULL, 'pending', 'bassa', NULL, NULL, NULL, NULL, '2025-01-28 10:00:00', NULL),
(20, 32, 9, 'no_show', 'Quarta volta che non si presenta. Impossibile organizzare partite con lui.', 24, 'pending', 'alta', NULL, NULL, NULL, NULL, '2025-01-20 14:00:00', NULL),

-- SEGNALAZIONI RESOLVED - Varie azioni
(21, 14, 29, 'violenza', 'Mi ha aggredito verbalmente e fisicamente dopo una discussione sul punteggio.', 10, 'resolved', 'alta', 1, 'ban', 0, 'Comportamento inaccettabile. Utente bannato permanentemente dalla piattaforma.', '2025-01-11 14:00:00', '2025-01-12 09:00:00'),
(22, 17, 9, 'comportamento_scorretto', 'Ha rubato palloni dalla struttura.', 12, 'resolved', 'alta', 2, 'sospensione', 10, 'Sospeso 14 giorni e obbligato a restituire il materiale.', '2025-01-13 16:00:00', '2025-01-14 10:00:00'),
(23, 19, 16, 'linguaggio_offensivo', 'Insulti sessisti rivolti a una ragazza del nostro gruppo.', 14, 'resolved', 'alta', 1, 'penalty_points', 5, 'Comportamento grave. 5 punti penalità e avvertimento formale.', '2025-01-14 20:00:00', '2025-01-15 11:00:00'),
(24, 8, 24, 'no_show', 'Secondo no-show questo mese.', 17, 'resolved', 'media', 2, 'penalty_points', 3, 'Assegnati 3 punti penalità. Prossimo no-show = sospensione.', '2025-01-16 19:00:00', '2025-01-17 09:00:00'),
(25, 10, 21, 'altro', 'Ha portato alcolici in struttura.', 33, 'resolved', 'media', 1, 'warning', 0, 'Primo avvertimento. Informato del regolamento.', '2025-01-25 22:00:00', '2025-01-26 10:00:00'),
(26, 12, 18, 'comportamento_scorretto', 'Ha lasciato rifiuti ovunque e non ha pulito.', 31, 'resolved', 'bassa', 2, 'nessuna', 0, 'L''utente si è scusato e ha pulito il giorno dopo. Nessuna sanzione.', '2025-01-24 23:00:00', '2025-01-25 14:00:00'),

-- SEGNALAZIONI REJECTED
(27, 15, 7, 'comportamento_scorretto', 'Gioca sempre troppo forte e fa male agli altri.', 3, 'rejected', 'bassa', 1, 'nessuna', 0, 'Segnalazione infondata. Giocare bene non è comportamento scorretto.', '2025-01-07 22:00:00', '2025-01-08 10:00:00'),
(28, 18, 5, 'altro', 'Parla sempre al telefono durante le partite.', 32, 'rejected', 'bassa', 2, 'nessuna', 0, 'Non costituisce violazione del regolamento.', '2025-01-24 19:00:00', '2025-01-25 09:00:00'),
(29, 26, 12, 'linguaggio_offensivo', 'Ha detto una parolaccia.', 5, 'rejected', 'bassa', 1, 'nessuna', 0, 'Contesto sportivo normale. Nessun insulto diretto.', '2025-01-08 12:00:00', '2025-01-09 10:00:00'),
(30, 28, 4, 'no_show', 'Non si è presentato ma aveva avvisato.', 1, 'rejected', 'bassa', 2, 'nessuna', 0, 'L''utente aveva comunicato l''assenza con 24h di anticipo. Procedura corretta.', '2025-01-06 14:00:00', '2025-01-07 09:00:00');

ALTER TABLE `segnalazioni` AUTO_INCREMENT = 31;


-- =====================================================
-- BROADCAST AGGIUNTIVI (14 broadcast)
-- Tutti i tipi e stati
-- =====================================================

INSERT INTO `broadcast_messages` (`broadcast_id`, `admin_id`, `oggetto`, `messaggio`, `target_type`, `target_filter`, `canale`, `scheduled_at`, `sent_at`, `num_destinatari`, `stato`, `created_at`) VALUES

-- INVIATI - Vari target
(7, 1, 'Nuovi Orari Invernali', 'A partire dal 1 Febbraio, gli orari dei campi outdoor cambieranno. Consulta il sito per i dettagli.', 'tutti', NULL, 'entrambi', NULL, '2025-01-20 09:00:00', 30, 'inviato', '2025-01-19 16:00:00'),
(8, 2, 'Torneo di Calcetto Universitario', 'Iscrizioni aperte per il torneo di calcetto! 8 squadre, premi per i vincitori. Iscriviti entro il 15/02.', 'sport', '1', 'entrambi', NULL, '2025-01-21 10:00:00', 18, 'inviato', '2025-01-20 14:00:00'),
(9, 1, 'Offerta Speciale Studenti Ingegneria', 'Solo per te: 20% di sconto su tutte le prenotazioni fino a fine mese!', 'corso', '1', 'email', NULL, '2025-01-22 11:00:00', 8, 'inviato', '2025-01-21 17:00:00'),
(10, 3, 'Congratulazioni Membri Gold!', 'Hai raggiunto il livello Gold! Ecco i tuoi vantaggi esclusivi...', 'livello', '3', 'in_app', NULL, '2025-01-23 14:00:00', 5, 'inviato', '2025-01-22 18:00:00'),
(11, 2, 'Manutenzione Campo Tennis 2', 'Il Campo Tennis 2 sarà chiuso per manutenzione dal 25/01 al 31/01. Ci scusiamo per il disagio.', 'sport', '5', 'entrambi', NULL, '2025-01-24 08:00:00', 12, 'inviato', '2025-01-23 15:00:00'),
(12, 1, 'Sondaggio Soddisfazione', 'Aiutaci a migliorare! Compila il sondaggio e ricevi 50 XP bonus.', 'attivi', NULL, 'entrambi', NULL, '2025-01-25 10:00:00', 25, 'inviato', '2025-01-24 16:00:00'),
(13, 3, 'Benvenuto ai Nuovi Iscritti', 'Ciao! Benvenuto su Campus Sports Arena. Ecco come iniziare...', 'livello', '1', 'entrambi', NULL, '2025-01-26 09:00:00', 15, 'inviato', '2025-01-25 14:00:00'),

-- PROGRAMMATI
(14, 1, 'Torneo Padel Marzo 2025', 'Preparati per il grande torneo di Padel! Iscrizioni dal 1 Marzo.', 'sport', '6', 'entrambi', '2025-02-25 10:00:00', NULL, 14, 'programmato', '2025-01-26 11:00:00'),
(15, 2, 'Festa di Primavera', 'Ti aspettiamo il 21 Marzo per la grande festa di primavera sportiva!', 'tutti', NULL, 'entrambi', '2025-03-15 09:00:00', NULL, 30, 'programmato', '2025-01-27 10:00:00'),
(16, 3, 'Sconto Pasqua', 'Approfitta dello sconto del 30% per il weekend di Pasqua!', 'tutti', NULL, 'email', '2025-04-10 10:00:00', NULL, 30, 'programmato', '2025-01-28 14:00:00'),

-- BOZZE
(17, 1, 'Nuovi Campi in Arrivo', 'Stiamo lavorando per voi! Presto nuovi campi da...', 'tutti', NULL, 'entrambi', NULL, NULL, 0, 'bozza', '2025-01-27 15:00:00'),
(18, 2, 'Regolamento Aggiornato', 'Vi informiamo che il regolamento è stato aggiornato...', 'tutti', NULL, 'entrambi', NULL, NULL, 0, 'bozza', '2025-01-28 09:00:00'),
(19, 3, 'Cercasi Volontari', 'Cerchiamo volontari per l''organizzazione dei tornei...', 'attivi', NULL, 'in_app', NULL, NULL, 0, 'bozza', '2025-01-29 11:00:00'),

-- FALLITO
(20, 1, 'Test Invio Email', 'Questo è un test del sistema di invio email.', 'tutti', NULL, 'email', NULL, NULL, 0, 'fallito', '2025-01-15 08:00:00');

ALTER TABLE `broadcast_messages` AUTO_INCREMENT = 21;


-- =====================================================
-- NOTIFICHE AGGIUNTIVE (per messaggi diretti e sistema)
-- =====================================================

INSERT INTO `notifiche` (`notifica_id`, `user_id`, `tipo`, `titolo`, `messaggio`, `letta`, `link`, `created_at`, `read_at`) VALUES

-- Notifiche da messaggi diretti admin
(31, 9, 'messaggio_admin', 'Avviso Importante', 'Gentile utente, abbiamo notato comportamenti non conformi al regolamento. Ti preghiamo di rispettare le regole.', 1, NULL, '2025-01-14 10:00:00', '2025-01-14 12:00:00'),
(32, 16, 'messaggio_admin', 'Richiamo Formale', 'A seguito delle segnalazioni ricevute, ti comunichiamo un richiamo formale. Ulteriori violazioni comporteranno sanzioni.', 1, NULL, '2025-01-15 11:00:00', '2025-01-15 14:00:00'),
(33, 29, 'messaggio_admin', 'Account Sospeso', 'Il tuo account è stato sospeso per 14 giorni a causa di comportamento scorretto ripetuto.', 1, NULL, '2025-01-12 09:00:00', '2025-01-12 10:00:00'),
(34, 24, 'messaggio_admin', 'Ultimo Avvertimento', 'Questo è il tuo ultimo avvertimento prima della sospensione. Rispetta il regolamento.', 0, NULL, '2025-01-17 09:00:00', NULL),

-- Notifiche segnalazioni risolte
(35, 14, 'segnalazione_risolta', 'Segnalazione Elaborata', 'La tua segnalazione è stata esaminata. L''utente ha ricevuto un ban permanente.', 1, NULL, '2025-01-12 09:30:00', '2025-01-12 11:00:00'),
(36, 17, 'segnalazione_risolta', 'Segnalazione Elaborata', 'La tua segnalazione è stata esaminata. L''utente è stato sospeso per 14 giorni.', 1, NULL, '2025-01-14 10:30:00', '2025-01-14 12:00:00'),
(37, 19, 'segnalazione_risolta', 'Segnalazione Elaborata', 'La tua segnalazione è stata esaminata. Sono stati assegnati 5 punti penalità all''utente.', 0, NULL, '2025-01-15 11:30:00', NULL),

-- Notifiche sistema
(38, 4, 'sistema', 'Aggiornamento Termini di Servizio', 'I nostri Termini di Servizio sono stati aggiornati. Leggi le modifiche.', 0, 'termini.php', '2025-01-20 10:00:00', NULL),
(39, 5, 'sistema', 'Aggiornamento Termini di Servizio', 'I nostri Termini di Servizio sono stati aggiornati. Leggi le modifiche.', 1, 'termini.php', '2025-01-20 10:00:00', '2025-01-20 15:00:00'),
(40, 7, 'sistema', 'Aggiornamento Termini di Servizio', 'I nostri Termini di Servizio sono stati aggiornati. Leggi le modifiche.', 0, 'termini.php', '2025-01-20 10:00:00', NULL),

-- Notifiche promozione livello
(41, 12, 'livello_up', 'Sei salito di livello!', 'Congratulazioni! Hai raggiunto il livello Silver! Continua così!', 1, 'profilo.php', '2025-01-18 16:00:00', '2025-01-18 17:00:00'),
(42, 20, 'livello_up', 'Sei salito di livello!', 'Congratulazioni! Hai raggiunto il livello Gold! Ora hai accesso a vantaggi esclusivi!', 1, 'profilo.php', '2025-01-22 14:00:00', '2025-01-22 15:00:00'),

-- Notifiche risposta recensione
(43, 4, 'risposta_recensione', 'Risposta alla tua Recensione', 'L''amministratore ha risposto alla tua recensione del Campo Calcetto A.', 0, 'recensioni.php', '2025-01-25 10:00:00', NULL),
(44, 7, 'risposta_recensione', 'Risposta alla tua Recensione', 'L''amministratore ha risposto alla tua recensione del Campo Padel 1.', 1, 'recensioni.php', '2025-01-26 11:00:00', '2025-01-26 14:00:00'),

-- Notifiche badge
(45, 10, 'badge_sbloccato', 'Nuovo Badge Sbloccato!', 'Hai sbloccato il badge "Sportivo Attivo"! Hai completato 5 prenotazioni.', 1, 'profilo.php', '2025-01-22 20:00:00', '2025-01-22 21:00:00'),
(46, 17, 'badge_sbloccato', 'Nuovo Badge Sbloccato!', 'Hai sbloccato il badge "Recensore"! Grazie per il tuo feedback.', 0, 'profilo.php', '2025-01-27 15:00:00', NULL),

-- Notifiche promemoria prenotazione
(47, 25, 'promemoria', 'Promemoria Prenotazione', 'Ricorda: domani hai una prenotazione alle 15:00 presso Campo Calcio 7.', 0, 'le-mie-prenotazioni.php', '2025-02-07 18:00:00', NULL),
(48, 27, 'promemoria', 'Promemoria Prenotazione', 'Ricorda: domani hai una prenotazione alle 16:00 presso Campo Pallavolo.', 0, 'le-mie-prenotazioni.php', '2025-02-08 18:00:00', NULL),

-- Notifiche cancellazione admin
(49, 8, 'prenotazione_cancellata', 'Prenotazione Cancellata', 'La tua prenotazione del 09/02 è stata cancellata dall''amministratore. Motivo: Manutenzione straordinaria.', 0, 'le-mie-prenotazioni.php', '2025-02-05 10:00:00', NULL),

-- Notifiche welcome
(50, 33, 'benvenuto', 'Benvenuto su Campus Sports Arena!', 'Ciao! Siamo felici di averti con noi. Inizia a prenotare i tuoi campi preferiti!', 1, 'index.php', '2025-01-19 14:05:00', '2025-01-19 14:10:00');

ALTER TABLE `notifiche` AUTO_INCREMENT = 51;


-- =====================================================
-- RISPOSTE ALLE RECENSIONI
-- =====================================================

INSERT INTO `recensione_risposte` (`risposta_id`, `recensione_id`, `admin_id`, `testo`, `created_at`) VALUES
(1, 1, 1, 'Grazie mille per il tuo feedback positivo! Siamo felici che ti sia trovato bene. Ti aspettiamo presto!', '2025-01-07 10:00:00'),
(2, 27, 2, 'Grazie per la recensione! Siamo orgogliosi del nostro campo padel. A presto!', '2025-01-08 11:00:00'),
(3, 40, 1, 'Ci scusiamo per l''inconveniente. Abbiamo già provveduto alla riparazione dell''erba sintetica. Ti invitiamo a tornare.', '2025-01-15 09:00:00'),
(4, 41, 2, 'Siamo molto dispiaciuti per l''accaduto. Ti abbiamo inviato un voucher per una prenotazione gratuita. Speriamo di rivederti.', '2025-01-30 10:00:00'),
(5, 44, 1, 'Grazie per la segnalazione. La rete è stata riparata e il pavimento trattato. Ci scusiamo per il disagio.', '2025-01-26 09:00:00'),
(6, 14, 2, 'Grazie mille! La palestra basket è il nostro orgoglio. Ti aspettiamo!', '2025-01-08 14:00:00'),
(7, 38, 3, 'Grazie per il feedback entusiastico! Ci fa piacere che apprezzi la qualità della sala.', '2025-01-24 10:00:00'),
(8, 46, 3, 'Grazie! Il nostro campo padel è curato con attenzione. A presto!', '2025-01-23 15:00:00');

ALTER TABLE `recensione_risposte` AUTO_INCREMENT = 9;


-- =====================================================
-- PENALTY LOG AGGIUNTIVI
-- =====================================================

INSERT INTO `penalty_log` (`log_id`, `user_id`, `punti`, `motivo`, `descrizione`, `prenotazione_id`, `segnalazione_id`, `admin_id`, `created_at`) VALUES
(13, 29, 10, 'segnalazione', 'Furto materiale sportivo', 12, 22, 2, '2025-01-14 10:00:00'),
(14, 16, 5, 'segnalazione', 'Linguaggio sessista', 14, 23, 1, '2025-01-15 11:00:00'),
(15, 24, 3, 'segnalazione', 'Secondo no-show del mese', 17, 24, 2, '2025-01-17 09:00:00'),
(16, 9, 5, 'admin_add', 'Comportamento recidivo - warning finale', NULL, NULL, 1, '2025-01-20 14:00:00');

ALTER TABLE `penalty_log` AUTO_INCREMENT = 17;


-- =====================================================
-- SANZIONI AGGIUNTIVE
-- =====================================================

INSERT INTO `sanzioni` (`sanzione_id`, `user_id`, `tipo`, `motivo`, `data_inizio`, `data_fine`, `admin_id`, `attiva`, `created_at`) VALUES
(3, 9, 'sospensione', 'Furto materiale sportivo e comportamento recidivo', '2025-01-14 10:00:00', '2025-01-28 10:00:00', 2, 0, '2025-01-14 10:00:00');

ALTER TABLE `sanzioni` AUTO_INCREMENT = 4;


-- =====================================================
-- FINE FILE
-- =====================================================