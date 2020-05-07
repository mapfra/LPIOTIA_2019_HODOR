-- -----------------------------------------------------
-- Table `users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `users` ;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(20) NOT NULL,
  `password` VARCHAR(100) NOT NULL,
  `email` VARCHAR(30) NOT NULL,
  `num_mobile` VARCHAR(15) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `status` TINYINT(1) NULL,
  `role` TINYINT(1) NULL,
  `dt_start_access` DATETIME NULL,
  `dt_end_access` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `role_idx` (`id` ASC)
  )
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `role`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `role` ;

CREATE TABLE IF NOT EXISTS `role` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  `description` VARCHAR(255) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB

-- -----------------------------------------------------
-- Table `permission`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `permission` ;

CREATE TABLE IF NOT EXISTS `permission` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(50) NOT NULL,
  `libelle` VARCHAR(255) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `key_UNIQUE` (`key` ASC))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `role_permission`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `role_permission` ;

CREATE TABLE IF NOT EXISTS `role_permission` (
  `id_role` INT NOT NULL,
  `id_permission` INT NOT NULL,
  PRIMARY KEY (`id_role`, `id_permission`),
  INDEX `permission_idx` (`id_permission` ASC),
  CONSTRAINT `rp_FK_role`
    FOREIGN KEY (`id_role`)
    REFERENCES `role` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `rp_FK_permission`
    FOREIGN KEY (`id_permission`)
    REFERENCES `permission` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;