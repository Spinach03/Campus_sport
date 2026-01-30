-- =====================================================
-- TABELLE MANCANTI - Campus Sports Arena
-- =====================================================
-- Eseguire DOPO schema.sql
-- Contiene le tabelle mancanti per il funzionamento completo
-- =====================================================

USE `campus_sports_arena`;

-- -----------------------------------------------------
-- Tabella `giorni_chiusura`
-- Giorni singoli in cui l'impianto è chiuso
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
    ON UPDATE CASCADE
) ENGINE = InnoDB;


-- =====================================================
-- FINE FILE
-- =====================================================