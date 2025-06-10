-- Adminer 4.8.1 MySQL 9.1.0 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE DATABASE `dynamic` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `dynamic`;

DROP TABLE IF EXISTS `cities`;
CREATE TABLE `cities` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `country_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `country_id` (`country_id`),
  CONSTRAINT `cities_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `cities` (`id`, `name`, `country_id`) VALUES
(1,	'New York',	1),
(2,	'Los Angeles',	1),
(3,	'Chicago',	1),
(4,	'Toronto',	2),
(5,	'Vancouver',	2),
(6,	'Montreal',	2),
(7,	'Paris',	3),
(8,	'Lyon',	3),
(9,	'Marseille',	3);

DROP TABLE IF EXISTS `countries`;
CREATE TABLE `countries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `countries` (`id`, `name`) VALUES
(2,	'Canada'),
(3,	'France'),
(4,	'Germany'),
(1,	'United States');

DROP TABLE IF EXISTS `streets`;
CREATE TABLE `streets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `city_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `city_id` (`city_id`),
  CONSTRAINT `streets_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `streets` (`id`, `name`, `city_id`) VALUES
(13,	'Broadway',	1),
(14,	'Wall Street',	1),
(15,	'Fifth Avenue',	1),
(16,	'Hollywood Boulevard',	2),
(17,	'Sunset Boulevard',	2),
(18,	'Wilshire Boulevard',	2),
(19,	'Michigan Avenue',	3),
(20,	'State Street',	3),
(21,	'Lake Shore Drive',	3),
(22,	'Yonge Street',	4),
(23,	'Bay Street',	4),
(24,	'Queen Street',	4),
(25,	'Granville Street',	5),
(26,	'Robson Street',	5),
(27,	'Main Street',	5),
(28,	'Saint Catherine Street',	6),
(29,	'Sherbrooke Street',	6),
(30,	'René-Lévesque Boulevard',	6),
(31,	'Champs-Élysées',	7),
(32,	'Rue de Rivoli',	7),
(33,	'Boulevard Saint-Michel',	7),
(34,	'Rue de la République',	8),
(35,	'Cours Charlemagne',	8),
(36,	'Rue Victor Hugo',	8),
(37,	'La Canebière',	9),
(38,	'Rue Saint-Ferréol',	9),
(39,	'Cours Julien',	9);

DROP TABLE IF EXISTS `travel`;
CREATE TABLE `travel` (
  `id` int NOT NULL AUTO_INCREMENT,
  `country_id` int NOT NULL,
  `city_id` int NOT NULL,
  `street_id` int NOT NULL,
  `created_at` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- 2025-01-19 23:27:02