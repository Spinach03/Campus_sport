-- =====================================================
-- CAMPUS SPORTS ARENA - SCHEMA COMPLETO
-- =====================================================
-- File unico con tutte le tabelle del database
-- Eseguire PRIMA di dati_completi.sql
-- =====================================================

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema campus_sports_arena
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `campus_sports_arena`;
CREATE SCHEMA IF NOT EXISTS `campus_sports_arena` DEFAULT CHARACTER SET utf8mb4;
USE `campus_sports_arena`;

-- -----------------------------------------------------
-- Table `corsi_laurea`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `corsi_laurea` (
  `corso_id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(150) NOT NULL,
  `facolta` VARCHAR(150) NULL,
  `attivo` TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (`corso_id`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `sport`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sport` (
  `sport_id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(50) NOT NULL,
  `descrizione` TEXT NULL,
  `num_giocatori_standard` INT NULL,
  `icona` VARCHAR(255) NULL,
  `attivo` TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (`sport_id`),
  UNIQUE INDEX `nome_UNIQUE` (`nome` ASC))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `livelli`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `livelli` (
  `livello_id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(50) NOT NULL,
  `xp_minimo` INT NOT NULL,
  `xp_massimo` INT NOT NULL,
  `max_prenotazioni_simultanee` INT NOT NULL DEFAULT 3,
  `max_ore_settimanali` INT NOT NULL DEFAULT 4,
  `giorni_anticipo_prenotazione` INT NOT NULL DEFAULT 7,
  PRIMARY KEY (`livello_id`),
  UNIQUE INDEX `nome_UNIQUE` (`nome` ASC))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `badges`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `badges` (
  `badge_id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `descrizione` TEXT NOT NULL,
  `icona` VARCHAR(255) NULL,
  `criterio_tipo` VARCHAR(50) NOT NULL,
  `criterio_valore` INT NOT NULL,
  `xp_reward` INT NOT NULL DEFAULT 0,
  `categoria` VARCHAR(50) NULL,
  `rarita` ENUM('comune', 'non_comune', 'raro', 'epico', 'leggendario') NOT NULL DEFAULT 'comune',
  `attivo` TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (`badge_id`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `nome` VARCHAR(100) NOT NULL,
  `cognome` VARCHAR(100) NOT NULL,
  `telefono` VARCHAR(20) NULL,
  `ruolo` ENUM('user', 'admin') NOT NULL,
  `stato` ENUM('attivo', 'sospeso', 'bannato') NOT NULL DEFAULT 'attivo',
  `ultimo_accesso` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `admins`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `admins` (
  `user_id` INT NOT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `fk_admins_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `utenti_standard`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `utenti_standard` (
  `user_id` INT NOT NULL,
  `corso_laurea_id` INT NULL,
  `anno_iscrizione` INT NULL,
  `data_nascita` DATE NULL,
  `indirizzo` VARCHAR(255) NULL,
  `penalty_points` INT NOT NULL DEFAULT 0,
  `xp_points` INT NOT NULL DEFAULT 0,
  `livello_id` INT NULL,
  PRIMARY KEY (`user_id`),
  INDEX `fk_utenti_standard_corsi_laurea_idx` (`corso_laurea_id` ASC),
  INDEX `fk_utenti_standard_livelli_idx` (`livello_id` ASC),
  CONSTRAINT `fk_utenti_standard_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_utenti_standard_corsi_laurea`
    FOREIGN KEY (`corso_laurea_id`)
    REFERENCES `corsi_laurea` (`corso_id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_utenti_standard_livelli`
    FOREIGN KEY (`livello_id`)
    REFERENCES `livelli` (`livello_id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `campi_sportivi`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `campi_sportivi` (
  `campo_id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `sport_id` INT NOT NULL,
  `location` VARCHAR(255) NOT NULL,
  `descrizione` TEXT NULL,
  `capienza_max` INT NOT NULL,
  `tipo_superficie` ENUM('erba_naturale', 'erba_sintetica', 'parquet', 'cemento', 'terra_battuta', 'resina', 'tartan') NOT NULL,
  `tipo_campo` ENUM('indoor', 'outdoor') NOT NULL,
  `lunghezza_m` DECIMAL(5,2) NULL,
  `larghezza_m` DECIMAL(5,2) NULL,
  `orario_apertura` TIME NOT NULL,
  `orario_chiusura` TIME NOT NULL,
  `stato` ENUM('disponibile', 'manutenzione', 'chiuso') NOT NULL DEFAULT 'disponibile',
  `rating_medio` DECIMAL(2,1) NOT NULL DEFAULT 0,
  `num_recensioni` INT NOT NULL DEFAULT 0,
  `created_by` INT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`campo_id`),
  UNIQUE INDEX `nome_UNIQUE` (`nome` ASC),
  INDEX `fk_campi_sportivi_sport_idx` (`sport_id` ASC),
  INDEX `fk_campi_sportivi_admins_idx` (`created_by` ASC),
  CONSTRAINT `fk_campi_sportivi_sport`
    FOREIGN KEY (`sport_id`)
    REFERENCES `sport` (`sport_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_campi_sportivi_admins`
    FOREIGN KEY (`created_by`)
    REFERENCES `admins` (`user_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `campo_foto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `campo_foto` (
  `foto_id` INT NOT NULL AUTO_INCREMENT,
  `campo_id` INT NOT NULL,
  `path_foto` VARCHAR(255) NOT NULL,
  `is_principale` TINYINT NOT NULL DEFAULT 0,
  `ordine` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`foto_id`),
  INDEX `fk_campo_foto_campi_sportivi_idx` (`campo_id` ASC),
  CONSTRAINT `fk_campo_foto_campi_sportivi`
    FOREIGN KEY (`campo_id`)
    REFERENCES `campi_sportivi` (`campo_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `campo_servizi`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `campo_servizi` (
  `campo_id` INT NOT NULL,
  `illuminazione_notturna` TINYINT NOT NULL DEFAULT 0,
  `spogliatoi` TINYINT NOT NULL DEFAULT 0,
  `docce` TINYINT NOT NULL DEFAULT 0,
  `parcheggio` TINYINT NOT NULL DEFAULT 0,
  `distributori` TINYINT NOT NULL DEFAULT 0,
  `noleggio_attrezzatura` TINYINT NOT NULL DEFAULT 0,
  `bar_ristoro` TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`campo_id`),
  CONSTRAINT `fk_campo_servizi_campi_sportivi`
    FOREIGN KEY (`campo_id`)
    REFERENCES `campi_sportivi` (`campo_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `campo_disponibilita_giorni`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `campo_disponibilita_giorni` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `campo_id` INT NOT NULL,
  `giorno_settimana` ENUM('lunedi', 'martedi', 'mercoledi', 'giovedi', 'venerdi', 'sabato', 'domenica') NOT NULL,
  `disponibile` TINYINT NOT NULL DEFAULT 1,
  `orario_apertura_custom` TIME NULL,
  `orario_chiusura_custom` TIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `campo_giorno_UNIQUE` (`campo_id` ASC, `giorno_settimana` ASC),
  CONSTRAINT `fk_campo_disponibilita_campi_sportivi`
    FOREIGN KEY (`campo_id`)
    REFERENCES `campi_sportivi` (`campo_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `blocchi_manutenzione`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `blocchi_manutenzione` (
  `blocco_id` INT NOT NULL AUTO_INCREMENT,
  `campo_id` INT NOT NULL,
  `data_inizio` DATE NOT NULL,
  `ora_inizio` TIME NOT NULL,
  `data_fine` DATE NOT NULL,
  `ora_fine` TIME NOT NULL,
  `tipo_blocco` ENUM('manutenzione_ordinaria', 'riparazione_urgente', 'evento_speciale', 'meteo', 'altro') NOT NULL,
  `motivo` TEXT NOT NULL,
  `created_by` INT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`blocco_id`),
  INDEX `fk_blocchi_manutenzione_campi_sportivi_idx` (`campo_id` ASC),
  INDEX `fk_blocchi_manutenzione_admins_idx` (`created_by` ASC),
  CONSTRAINT `fk_blocchi_manutenzione_campi_sportivi`
    FOREIGN KEY (`campo_id`)
    REFERENCES `campi_sportivi` (`campo_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_blocchi_manutenzione_admins`
    FOREIGN KEY (`created_by`)
    REFERENCES `admins` (`user_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `campo_storico_modifiche`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `campo_storico_modifiche` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `campo_id` INT NOT NULL,
  `admin_id` INT NOT NULL,
  `campo_modificato` VARCHAR(100) NOT NULL,
  `valore_precedente` TEXT NULL,
  `valore_nuovo` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_campo_storico_campi_sportivi_idx` (`campo_id` ASC),
  INDEX `fk_campo_storico_admins_idx` (`admin_id` ASC),
  CONSTRAINT `fk_campo_storico_campi_sportivi`
    FOREIGN KEY (`campo_id`)
    REFERENCES `campi_sportivi` (`campo_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_campo_storico_admins`
    FOREIGN KEY (`admin_id`)
    REFERENCES `admins` (`user_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `prenotazioni`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prenotazioni` (
  `prenotazione_id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `campo_id` INT NOT NULL,
  `data_prenotazione` DATE NOT NULL,
  `ora_inizio` TIME NOT NULL,
  `ora_fine` TIME NOT NULL,
  `num_partecipanti` INT NOT NULL,
  `stato` ENUM('confermata', 'cancellata', 'completata', 'no_show', 'in_attesa') NOT NULL DEFAULT 'confermata',
  `check_in_effettuato` TINYINT NOT NULL DEFAULT 0,
  `ora_check_in` DATETIME NULL,
  `note` TEXT NULL,
  `cancellazione_tardiva` TINYINT NOT NULL DEFAULT 0,
  `motivo_cancellazione` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `cancelled_at` DATETIME NULL,
  PRIMARY KEY (`prenotazione_id`),
  UNIQUE INDEX `campo_data_ora_UNIQUE` (`campo_id` ASC, `data_prenotazione` ASC, `ora_inizio` ASC),
  INDEX `fk_prenotazioni_utenti_standard_idx` (`user_id` ASC),
  INDEX `fk_prenotazioni_campi_sportivi_idx` (`campo_id` ASC),
  CONSTRAINT `fk_prenotazioni_utenti_standard`
    FOREIGN KEY (`user_id`)
    REFERENCES `utenti_standard` (`user_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_prenotazioni_campi_sportivi`
    FOREIGN KEY (`campo_id`)
    REFERENCES `campi_sportivi` (`campo_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `prenotazione_inviti`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prenotazione_inviti` (
  `invito_id` INT NOT NULL AUTO_INCREMENT,
  `prenotazione_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `stato` ENUM('pending', 'accepted', 'declined') NOT NULL DEFAULT 'pending',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `responded_at` DATETIME NULL,
  PRIMARY KEY (`invito_id`),
  UNIQUE INDEX `prenotazione_user_UNIQUE` (`prenotazione_id` ASC, `user_id` ASC),
  INDEX `fk_prenotazione_inviti_prenotazioni_idx` (`prenotazione_id` ASC),
  INDEX `fk_prenotazione_inviti_utenti_standard_idx` (`user_id` ASC),
  CONSTRAINT `fk_prenotazione_inviti_prenotazioni`
    FOREIGN KEY (`prenotazione_id`)
    REFERENCES `prenotazioni` (`prenotazione_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_prenotazione_inviti_utenti_standard`
    FOREIGN KEY (`user_id`)
    REFERENCES `utenti_standard` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `recensioni`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `recensioni` (
  `recensione_id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `campo_id` INT NOT NULL,
  `prenotazione_id` INT NOT NULL,
  `rating_generale` INT NOT NULL,
  `rating_condizioni` INT NULL,
  `rating_pulizia` INT NULL,
  `rating_illuminazione` INT NULL,
  `commento` TEXT NULL,
  `voti_utili` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`recensione_id`),
  UNIQUE INDEX `user_prenotazione_UNIQUE` (`user_id` ASC, `prenotazione_id` ASC),
  INDEX `fk_recensioni_utenti_standard_idx` (`user_id` ASC),
  INDEX `fk_recensioni_campi_sportivi_idx` (`campo_id` ASC),
  INDEX `fk_recensioni_prenotazioni_idx` (`prenotazione_id` ASC),
  CONSTRAINT `fk_recensioni_utenti_standard`
    FOREIGN KEY (`user_id`)
    REFERENCES `utenti_standard` (`user_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_recensioni_campi_sportivi`
    FOREIGN KEY (`campo_id`)
    REFERENCES `campi_sportivi` (`campo_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_recensioni_prenotazioni`
    FOREIGN KEY (`prenotazione_id`)
    REFERENCES `prenotazioni` (`prenotazione_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `recensione_foto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `recensione_foto` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `recensione_id` INT NOT NULL,
  `path_foto` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_recensione_foto_recensioni_idx` (`recensione_id` ASC),
  CONSTRAINT `fk_recensione_foto_recensioni`
    FOREIGN KEY (`recensione_id`)
    REFERENCES `recensioni` (`recensione_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `recensione_risposte`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `recensione_risposte` (
  `risposta_id` INT NOT NULL AUTO_INCREMENT,
  `recensione_id` INT NOT NULL,
  `admin_id` INT NOT NULL,
  `testo` TEXT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`risposta_id`),
  INDEX `fk_recensione_risposte_recensioni_idx` (`recensione_id` ASC),
  INDEX `fk_recensione_risposte_admins_idx` (`admin_id` ASC),
  CONSTRAINT `fk_recensione_risposte_recensioni`
    FOREIGN KEY (`recensione_id`)
    REFERENCES `recensioni` (`recensione_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_recensione_risposte_admins`
    FOREIGN KEY (`admin_id`)
    REFERENCES `admins` (`user_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `recensione_voti_utili`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `recensione_voti_utili` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `recensione_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `recensione_user_UNIQUE` (`recensione_id` ASC, `user_id` ASC),
  INDEX `fk_recensione_voti_recensioni_idx` (`recensione_id` ASC),
  INDEX `fk_recensione_voti_utenti_standard_idx` (`user_id` ASC),
  CONSTRAINT `fk_recensione_voti_recensioni`
    FOREIGN KEY (`recensione_id`)
    REFERENCES `recensioni` (`recensione_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_recensione_voti_utenti_standard`
    FOREIGN KEY (`user_id`)
    REFERENCES `utenti_standard` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `segnalazioni`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `segnalazioni` (
  `segnalazione_id` INT NOT NULL AUTO_INCREMENT,
  `user_segnalante_id` INT NOT NULL,
  `user_segnalato_id` INT NOT NULL,
  `tipo` ENUM('no_show', 'comportamento_scorretto', 'linguaggio_offensivo', 'violenza', 'altro') NOT NULL,
  `descrizione` TEXT NOT NULL,
  `prenotazione_id` INT NULL,
  `stato` ENUM('pending', 'resolved', 'rejected') NOT NULL DEFAULT 'pending',
  `priorita` ENUM('bassa', 'media', 'alta') NOT NULL DEFAULT 'media',
  `admin_id` INT NULL,
  `azione_intrapresa` ENUM('nessuna', 'warning', 'penalty_points', 'sospensione', 'ban') NULL,
  `penalty_assegnati` INT NULL,
  `note_risoluzione` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `resolved_at` DATETIME NULL,
  PRIMARY KEY (`segnalazione_id`),
  INDEX `fk_segnalazioni_segnalante_idx` (`user_segnalante_id` ASC),
  INDEX `fk_segnalazioni_segnalato_idx` (`user_segnalato_id` ASC),
  INDEX `fk_segnalazioni_prenotazioni_idx` (`prenotazione_id` ASC),
  INDEX `fk_segnalazioni_admins_idx` (`admin_id` ASC),
  CONSTRAINT `fk_segnalazioni_segnalante`
    FOREIGN KEY (`user_segnalante_id`)
    REFERENCES `utenti_standard` (`user_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_segnalazioni_segnalato`
    FOREIGN KEY (`user_segnalato_id`)
    REFERENCES `utenti_standard` (`user_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_segnalazioni_prenotazioni`
    FOREIGN KEY (`prenotazione_id`)
    REFERENCES `prenotazioni` (`prenotazione_id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_segnalazioni_admins`
    FOREIGN KEY (`admin_id`)
    REFERENCES `admins` (`user_id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `penalty_log`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `penalty_log` (
  `log_id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `punti` INT NOT NULL,
  `motivo` ENUM('no_show', 'cancellazione_tardiva', 'segnalazione', 'manuale_add', 'manuale_remove', 'reset', 'admin_add', 'admin_remove') NOT NULL,
  `descrizione` TEXT NULL,
  `prenotazione_id` INT NULL,
  `segnalazione_id` INT NULL,
  `admin_id` INT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  INDEX `fk_penalty_log_utenti_standard_idx` (`user_id` ASC),
  INDEX `fk_penalty_log_prenotazioni_idx` (`prenotazione_id` ASC),
  INDEX `fk_penalty_log_segnalazioni_idx` (`segnalazione_id` ASC),
  INDEX `fk_penalty_log_admins_idx` (`admin_id` ASC),
  CONSTRAINT `fk_penalty_log_utenti_standard`
    FOREIGN KEY (`user_id`)
    REFERENCES `utenti_standard` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_penalty_log_prenotazioni`
    FOREIGN KEY (`prenotazione_id`)
    REFERENCES `prenotazioni` (`prenotazione_id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_penalty_log_segnalazioni`
    FOREIGN KEY (`segnalazione_id`)
    REFERENCES `segnalazioni` (`segnalazione_id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_penalty_log_admins`
    FOREIGN KEY (`admin_id`)
    REFERENCES `admins` (`user_id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `sanzioni`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sanzioni` (
  `sanzione_id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `tipo` ENUM('warning', 'sospensione', 'ban') NOT NULL,
  `motivo` TEXT NOT NULL,
  `data_inizio` DATETIME NOT NULL,
  `data_fine` DATETIME NULL,
  `admin_id` INT NOT NULL,
  `attiva` TINYINT NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sanzione_id`),
  INDEX `fk_sanzioni_utenti_standard_idx` (`user_id` ASC),
  INDEX `fk_sanzioni_admins_idx` (`admin_id` ASC),
  CONSTRAINT `fk_sanzioni_utenti_standard`
    FOREIGN KEY (`user_id`)
    REFERENCES `utenti_standard` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_sanzioni_admins`
    FOREIGN KEY (`admin_id`)
    REFERENCES `admins` (`user_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `user_badges`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_badges` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `badge_id` INT NOT NULL,
  `sbloccato_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `user_badge_UNIQUE` (`user_id` ASC, `badge_id` ASC),
  INDEX `fk_user_badges_utenti_standard_idx` (`user_id` ASC),
  INDEX `fk_user_badges_badges_idx` (`badge_id` ASC),
  CONSTRAINT `fk_user_badges_utenti_standard`
    FOREIGN KEY (`user_id`)
    REFERENCES `utenti_standard` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_user_badges_badges`
    FOREIGN KEY (`badge_id`)
    REFERENCES `badges` (`badge_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `user_sport_preferiti`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_sport_preferiti` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `sport_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `user_sport_UNIQUE` (`user_id` ASC, `sport_id` ASC),
  INDEX `fk_user_sport_preferiti_utenti_standard_idx` (`user_id` ASC),
  INDEX `fk_user_sport_preferiti_sport_idx` (`sport_id` ASC),
  CONSTRAINT `fk_user_sport_preferiti_utenti_standard`
    FOREIGN KEY (`user_id`)
    REFERENCES `utenti_standard` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_user_sport_preferiti_sport`
    FOREIGN KEY (`sport_id`)
    REFERENCES `sport` (`sport_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `notifiche`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `notifiche` (
  `notifica_id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `tipo` VARCHAR(50) NOT NULL,
  `titolo` VARCHAR(255) NOT NULL,
  `messaggio` TEXT NOT NULL,
  `letta` TINYINT NOT NULL DEFAULT 0,
  `link` VARCHAR(255) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `read_at` DATETIME NULL,
  PRIMARY KEY (`notifica_id`),
  INDEX `fk_notifiche_users_idx` (`user_id` ASC),
  CONSTRAINT `fk_notifiche_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `notification_templates`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `notification_templates` (
  `template_id` INT NOT NULL AUTO_INCREMENT,
  `tipo` VARCHAR(50) NOT NULL,
  `titolo_template` VARCHAR(255) NOT NULL,
  `messaggio_template` TEXT NOT NULL,
  `canale` ENUM('in_app', 'email', 'entrambi') NOT NULL DEFAULT 'entrambi',
  `attivo` TINYINT NOT NULL DEFAULT 1,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` INT NULL,
  PRIMARY KEY (`template_id`),
  UNIQUE INDEX `tipo_UNIQUE` (`tipo` ASC),
  INDEX `fk_notification_templates_admins_idx` (`updated_by` ASC),
  CONSTRAINT `fk_notification_templates_admins`
    FOREIGN KEY (`updated_by`)
    REFERENCES `admins` (`user_id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `broadcast_messages`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `broadcast_messages` (
  `broadcast_id` INT NOT NULL AUTO_INCREMENT,
  `admin_id` INT NOT NULL,
  `oggetto` VARCHAR(255) NOT NULL,
  `messaggio` TEXT NOT NULL,
  `target_type` ENUM('tutti', 'attivi', 'corso', 'sport', 'livello', 'custom') NOT NULL,
  `target_filter` JSON NULL,
  `canale` ENUM('in_app', 'email', 'entrambi') NOT NULL DEFAULT 'entrambi',
  `scheduled_at` DATETIME NULL,
  `sent_at` DATETIME NULL,
  `num_destinatari` INT NOT NULL DEFAULT 0,
  `stato` ENUM('bozza', 'programmato', 'inviato', 'fallito') NOT NULL DEFAULT 'bozza',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`broadcast_id`),
  INDEX `fk_broadcast_messages_admins_idx` (`admin_id` ASC),
  CONSTRAINT `fk_broadcast_messages_admins`
    FOREIGN KEY (`admin_id`)
    REFERENCES `admins` (`user_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `audit_log`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `audit_log` (
  `log_id` BIGINT NOT NULL AUTO_INCREMENT,
  `tipo_operazione` ENUM('CREATE', 'UPDATE', 'DELETE', 'LOGIN', 'LOGOUT', 'PERMISSION_CHANGE') NOT NULL,
  `entita` VARCHAR(50) NOT NULL,
  `entita_id` INT NULL,
  `descrizione` TEXT NOT NULL,
  `admin_id` INT NULL,
  `user_id` INT NULL,
  `ip_address` VARCHAR(45) NULL,
  `dati_before` JSON NULL,
  `dati_after` JSON NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  INDEX `fk_audit_log_admins_idx` (`admin_id` ASC),
  INDEX `fk_audit_log_users_idx` (`user_id` ASC),
  INDEX `idx_audit_log_created_at` (`created_at` ASC),
  INDEX `idx_audit_log_entita` (`entita` ASC, `entita_id` ASC),
  CONSTRAINT `fk_audit_log_admins`
    FOREIGN KEY (`admin_id`)
    REFERENCES `admins` (`user_id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_audit_log_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`user_id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `system_config`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `system_config` (
  `config_id` INT NOT NULL AUTO_INCREMENT,
  `chiave` VARCHAR(100) NOT NULL,
  `valore` TEXT NOT NULL,
  `tipo` ENUM('string', 'int', 'boolean', 'json') NOT NULL DEFAULT 'string',
  `descrizione` TEXT NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` INT NULL,
  PRIMARY KEY (`config_id`),
  UNIQUE INDEX `chiave_UNIQUE` (`chiave` ASC),
  INDEX `fk_system_config_admins_idx` (`updated_by` ASC),
  CONSTRAINT `fk_system_config_admins`
    FOREIGN KEY (`updated_by`)
    REFERENCES `admins` (`user_id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `user_sessions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_sessions` (
  `session_id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `ip_address` VARCHAR(45) NULL,
  `device_type` ENUM('desktop', 'mobile', 'tablet') NULL,
  `browser` VARCHAR(100) NULL,
  `login_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_activity` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `attiva` TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (`session_id`),
  UNIQUE INDEX `token_UNIQUE` (`token` ASC),
  INDEX `fk_user_sessions_users_idx` (`user_id` ASC),
  CONSTRAINT `fk_user_sessions_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `login_attempts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `attempt_id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `motivo_fallimento` ENUM('password_errata', 'utente_non_esiste', 'account_sospeso', 'account_bannato') NULL,
  `successo` TINYINT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`attempt_id`),
  INDEX `idx_login_attempts_email` (`email` ASC),
  INDEX `idx_login_attempts_ip` (`ip_address` ASC),
  INDEX `idx_login_attempts_created_at` (`created_at` ASC))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `ip_blacklist`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ip_blacklist` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `ip_address` VARCHAR(45) NOT NULL,
  `motivo` TEXT NULL,
  `permanente` TINYINT NOT NULL DEFAULT 0,
  `scadenza` DATETIME NULL,
  `created_by` INT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `ip_address_UNIQUE` (`ip_address` ASC),
  INDEX `fk_ip_blacklist_admins_idx` (`created_by` ASC),
  CONSTRAINT `fk_ip_blacklist_admins`
    FOREIGN KEY (`created_by`)
    REFERENCES `admins` (`user_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `giorni_chiusura`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `giorni_chiusura` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `data` DATE NOT NULL,
  `motivo` VARCHAR(255) NULL,
  `created_by` INT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `data_UNIQUE` (`data` ASC),
  INDEX `fk_giorni_chiusura_admin_idx` (`created_by` ASC),
  CONSTRAINT `fk_giorni_chiusura_admin`
    FOREIGN KEY (`created_by`)
    REFERENCES `users` (`user_id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- =====================================================
-- FINE SCHEMA - 34 TABELLE TOTALI
-- =====================================================