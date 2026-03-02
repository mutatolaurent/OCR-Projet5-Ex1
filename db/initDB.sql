CREATE DATABASE IF NOT EXISTS carnet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE carnet;

CREATE TABLE IF NOT EXISTS `carnet`.`contact` 
(
    `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Identifiant unique du contact' , 
    `name` VARCHAR(128) NOT NULL COMMENT 'Nom du contact' , 
    `email` VARCHAR(256) NOT NULL COMMENT 'Email du contact' , 
    `phone_number` VARCHAR(32) NOT NULL COMMENT 'Numéro de téléphone du contact' ,
    PRIMARY KEY (`id`)
) 
ENGINE = InnoDB COMMENT = 'Liste des contacts';