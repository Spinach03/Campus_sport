-- =====================================================
-- RECENSIONI EXTRA - Negative e Neutre
-- Campus Sports Arena
-- =====================================================
-- Eseguire DOPO data_extra.sql
-- =====================================================

USE `campus_sports_arena`;

-- Prima verifichiamo/correggiamo la struttura se necessario
-- Se la tabella usa "voto" invece di "rating_generale", adattiamo

-- -----------------------------------------------------
-- NUOVE PRENOTAZIONI COMPLETATE (per poter aggiungere recensioni)
-- Le recensioni richiedono prenotazioni uniche per user
-- -----------------------------------------------------

INSERT INTO `prenotazioni` (`prenotazione_id`, `user_id`, `campo_id`, `data_prenotazione`, `ora_inizio`, `ora_fine`, `num_partecipanti`, `stato`, `check_in_effettuato`, `ora_check_in`, `note`, `created_at`) VALUES
-- Nuove prenotazioni completate per recensioni negative
(71, 9, 1, '2025-01-05', '10:00:00', '11:00:00', 10, 'completata', 1, '2025-01-05 09:55:00', 'Calcetto', '2025-01-03 10:00:00'),
(72, 9, 2, '2025-01-08', '14:00:00', '15:00:00', 10, 'completata', 1, '2025-01-08 13:50:00', NULL, '2025-01-06 11:00:00'),
(73, 9, 7, '2025-01-10', '11:00:00', '12:00:00', 4, 'completata', 1, '2025-01-10 10:55:00', 'Tennis', '2025-01-08 09:00:00'),
(74, 16, 1, '2025-01-07', '16:00:00', '17:00:00', 10, 'completata', 1, '2025-01-07 15:52:00', NULL, '2025-01-05 14:00:00'),
(75, 16, 7, '2025-01-12', '09:00:00', '10:00:00', 4, 'completata', 1, '2025-01-12 08:55:00', 'Tennis', '2025-01-10 08:00:00'),
(76, 18, 2, '2025-01-09', '11:00:00', '12:00:00', 10, 'completata', 1, '2025-01-09 10:55:00', 'Calcetto', '2025-01-07 16:00:00'),
(77, 18, 3, '2025-01-13', '15:00:00', '16:00:00', 14, 'completata', 1, '2025-01-13 14:52:00', 'Calcio 7', '2025-01-11 10:00:00'),
(78, 19, 1, '2025-01-06', '14:00:00', '15:00:00', 10, 'completata', 1, '2025-01-06 13:50:00', NULL, '2025-01-04 12:00:00'),
(79, 19, 5, '2025-01-11', '18:00:00', '19:00:00', 12, 'completata', 1, '2025-01-11 17:55:00', 'Pallavolo', '2025-01-09 14:00:00'),
(80, 21, 7, '2025-01-09', '16:00:00', '17:00:00', 2, 'completata', 1, '2025-01-09 15:55:00', 'Tennis singolo', '2025-01-07 10:00:00'),
(81, 21, 3, '2025-01-15', '10:00:00', '11:00:00', 14, 'completata', 1, '2025-01-15 09:55:00', 'Calcio 7', '2025-01-13 08:00:00'),
(82, 22, 1, '2025-01-08', '09:00:00', '10:00:00', 10, 'completata', 1, '2025-01-08 08:55:00', NULL, '2025-01-06 15:00:00'),
(83, 23, 7, '2025-01-10', '14:00:00', '15:00:00', 4, 'completata', 1, '2025-01-10 13:52:00', 'Tennis doppio', '2025-01-08 11:00:00'),
(84, 24, 1, '2025-01-12', '17:00:00', '18:00:00', 10, 'completata', 1, '2025-01-12 16:55:00', 'Calcetto', '2025-01-10 13:00:00'),
(85, 24, 3, '2025-01-14', '14:00:00', '15:00:00', 14, 'completata', 1, '2025-01-14 13:50:00', NULL, '2025-01-12 09:00:00'),
(86, 25, 7, '2025-01-11', '10:00:00', '11:00:00', 2, 'completata', 1, '2025-01-11 09:55:00', 'Tennis', '2025-01-09 16:00:00'),
(87, 26, 1, '2025-01-13', '11:00:00', '12:00:00', 10, 'completata', 1, '2025-01-13 10:55:00', NULL, '2025-01-11 14:00:00'),
(88, 26, 4, '2025-01-16', '19:00:00', '20:00:00', 10, 'completata', 1, '2025-01-16 18:52:00', 'Basket', '2025-01-14 10:00:00'),
(89, 27, 7, '2025-01-14', '11:00:00', '12:00:00', 4, 'completata', 1, '2025-01-14 10:55:00', 'Tennis', '2025-01-12 15:00:00'),
(90, 28, 1, '2025-01-15', '10:00:00', '11:00:00', 10, 'completata', 1, '2025-01-15 09:55:00', 'Calcetto', '2025-01-13 11:00:00'),
(91, 30, 3, '2025-01-17', '16:00:00', '17:00:00', 14, 'completata', 1, '2025-01-17 15:52:00', 'Calcio 7', '2025-01-15 09:00:00'),
(92, 31, 1, '2025-01-16', '14:00:00', '15:00:00', 10, 'completata', 1, '2025-01-16 13:50:00', NULL, '2025-01-14 16:00:00'),
(93, 32, 7, '2025-01-18', '09:00:00', '10:00:00', 2, 'completata', 1, '2025-01-18 08:55:00', 'Tennis', '2025-01-16 08:00:00'),
(94, 33, 1, '2025-01-17', '18:00:00', '19:00:00', 10, 'completata', 1, '2025-01-17 17:55:00', 'Calcetto', '2025-01-15 12:00:00'),
(95, 33, 5, '2025-01-19', '14:00:00', '15:00:00', 12, 'completata', 1, '2025-01-19 13:55:00', 'Pallavolo', '2025-01-17 10:00:00');

-- -----------------------------------------------------
-- RECENSIONI NEGATIVE E NEUTRE
-- Rating: 1, 2, 3 stelle
-- -----------------------------------------------------

-- Prima elimino eventuali recensioni esistenti con stesso user_id/prenotazione_id per evitare duplicati
-- (se la tabella usa "voto" invece di "rating_generale", questa query potrebbe fallire)

-- Provo con la struttura dello schema.sql (rating_generale, rating_condizioni, ecc.)
INSERT INTO `recensioni` (`recensione_id`, `prenotazione_id`, `user_id`, `campo_id`, `rating_generale`, `rating_condizioni`, `rating_pulizia`, `rating_illuminazione`, `commento`, `created_at`) VALUES

-- ====== RECENSIONI 1 STELLA (molto negative) ======

-- Campo Calcetto A (campo_id=1) - Recensioni negative
(36, 71, 9, 1, 1, 1, 2, 1, 'Esperienza pessima. Il campo era in condizioni terribili, pieno di buche e l\'erba sintetica completamente rovinata. Illuminazione quasi inesistente. Mai più!', '2025-01-05 12:30:00'),
(37, 74, 16, 1, 1, 1, 1, 2, 'Deluso totalmente. Ho pagato per un campo che sembrava abbandonato. Spogliatoi sporchi e maleodoranti. Vergognoso.', '2025-01-07 18:00:00'),
(38, 78, 19, 1, 1, 2, 1, 1, 'Il peggior campo da calcetto in cui abbia mai giocato. L\'erba si staccava, le porte erano arrugginite. Soldi buttati.', '2025-01-06 16:00:00'),

-- Campo Tennis 2 (campo_id=7) - Recensioni negative (in manutenzione, quindi giustificate)
(39, 73, 9, 7, 1, 1, 1, 2, 'Campo completamente inadatto al gioco. La superficie era irregolare e pericolosa. Ho rischiato di farmi male. Scandaloso!', '2025-01-10 13:00:00'),
(40, 75, 16, 7, 2, 2, 1, 2, 'Rete da tennis rotta in più punti. Dovevo continuamente recuperare le palle che passavano sotto. Una perdita di tempo.', '2025-01-12 11:00:00'),
(41, 80, 21, 7, 1, 1, 2, 1, 'Non capisco come possiate far prenotare un campo in queste condizioni. Linee cancellate, illuminazione insufficiente. Pessimo!', '2025-01-09 18:00:00'),

-- ====== RECENSIONI 2 STELLE (negative) ======

-- Campo Calcetto B (campo_id=2) - Recensioni negative
(42, 72, 9, 2, 2, 2, 2, 3, 'Campo mediocre. L\'erba è consumata in molti punti e ci sono avvallamenti pericolosi. Non vale il prezzo.', '2025-01-08 16:00:00'),
(43, 76, 18, 2, 2, 2, 3, 2, 'Aspettative deluse. Il campo B è molto peggio del campo A. Illuminazione scarsa per le partite serali.', '2025-01-09 13:00:00'),

-- Campo Calcio 7 (campo_id=3) - Recensioni negative
(44, 77, 18, 3, 2, 2, 2, 2, 'Campo grande ma mal tenuto. L\'erba naturale era troppo alta in alcuni punti e secca in altri. Esperienza deludente.', '2025-01-13 17:00:00'),
(45, 81, 21, 3, 2, 3, 2, 2, 'Mi aspettavo di meglio per un campo da calcio a 7. Porte storte, linee sbiadite. Non lo consiglio.', '2025-01-15 12:00:00'),
(46, 85, 24, 3, 2, 2, 2, 3, 'Campo troppo piccolo rispetto alle dimensioni pubblicizzate. Parcheggio lontanissimo. Deludente.', '2025-01-14 16:00:00'),

-- Campo Tennis 2 (campo_id=7) - Altre recensioni negative
(47, 83, 23, 7, 2, 2, 3, 2, 'Il campo ha bisogno urgente di manutenzione. La superficie è sconnessa e la rete è in pessime condizioni.', '2025-01-10 16:00:00'),
(48, 86, 25, 7, 2, 2, 2, 2, 'Non tornerò su questo campo. Troppo degradato. Sembra abbandonato da mesi.', '2025-01-11 12:00:00'),
(49, 89, 27, 7, 2, 3, 2, 2, 'Campo non all\'altezza. Ho prenotato pensando fosse in condizioni migliori. Peccato.', '2025-01-14 13:00:00'),

-- ====== RECENSIONI 3 STELLE (neutre) ======

-- Campo Calcetto A (campo_id=1) - Recensioni neutre
(50, 82, 22, 1, 3, 3, 3, 3, 'Niente di speciale. Campo nella media, nulla di eccezionale ma nemmeno terribile. Sufficiente.', '2025-01-08 11:00:00'),
(51, 84, 24, 1, 3, 3, 4, 3, 'Campo discreto. Qualche difetto qua e là ma nel complesso accettabile. Rapporto qualità prezzo nella media.', '2025-01-12 19:00:00'),
(52, 87, 26, 1, 3, 4, 3, 2, 'Esperienza ok. Il campo è decente, ma l\'illuminazione serale lascia a desiderare. Migliorabile.', '2025-01-13 13:00:00'),
(53, 90, 28, 1, 3, 3, 3, 3, 'Campo standard, nulla di più. Ho giocato in posti migliori ma anche peggiori. Nella media.', '2025-01-15 12:00:00'),
(54, 92, 31, 1, 3, 3, 3, 4, 'Sufficiente. Il campo è ok per partite tra amici, non per chi cerca qualità. Prezzo giusto.', '2025-01-16 16:00:00'),
(55, 94, 33, 1, 3, 2, 3, 3, 'Giudizio misto. Alcune cose vanno bene, altre no. Potrebbero curare meglio la manutenzione.', '2025-01-17 20:00:00'),

-- Campo Calcio 7 (campo_id=3) - Recensioni neutre
(56, 91, 30, 3, 3, 3, 3, 2, 'Campo nella norma. Per una partitella va bene, ma non lo sceglierei per partite importanti.', '2025-01-17 18:00:00'),

-- Campo Tennis 2 (campo_id=7) - Recensioni neutre
(57, 93, 32, 7, 3, 3, 3, 3, 'Campo mediocre. Ha i suoi problemi ma si può giocare. Spero venga sistemato presto.', '2025-01-18 11:00:00'),

-- Campo Pallavolo (campo_id=5) - Recensioni neutre
(58, 79, 19, 5, 3, 3, 4, 3, 'Palestra ok. Niente di entusiasmante ma funzionale. Il parquet andrebbe lucidato.', '2025-01-11 20:00:00'),
(59, 95, 33, 5, 3, 4, 3, 3, 'Campo di pallavolo nella media. La rete è un po\' vecchia ma si gioca bene. Sufficiente.', '2025-01-19 16:00:00'),

-- Campo Basket (campo_id=4) - Una recensione negativa per bilanciare
(60, 88, 26, 4, 3, 3, 4, 3, 'Palestra discreta. Mi aspettavo di più vista la fama. Canestri ok, pavimento un po\' scivoloso.', '2025-01-16 21:00:00');

-- Aggiorna AUTO_INCREMENT
ALTER TABLE `prenotazioni`
  MODIFY `prenotazione_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

ALTER TABLE `recensioni`
  MODIFY `recensione_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

-- Aggiorna statistiche campi (num_recensioni e rating_medio)
UPDATE `campi_sportivi` c SET 
    `num_recensioni` = (SELECT COUNT(*) FROM `recensioni` WHERE `campo_id` = c.`campo_id`),
    `rating_medio` = (SELECT ROUND(AVG(`rating_generale`), 1) FROM `recensioni` WHERE `campo_id` = c.`campo_id`)
WHERE c.`campo_id` IN (1, 2, 3, 4, 5, 7);

-- =====================================================
-- RECENSIONI AGGIUNTE:
-- =====================================================
-- 1 STELLA: 6 recensioni (ID 36-41)
-- 2 STELLE: 8 recensioni (ID 42-49)
-- 3 STELLE: 11 recensioni (ID 50-60)
-- TOTALE: 25 nuove recensioni negative/neutre
-- =====================================================