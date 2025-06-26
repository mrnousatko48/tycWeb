-- Adminer 4.8.1 MySQL 8.1.0 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `cases`;
CREATE TABLE `cases` (
  `id` int NOT NULL AUTO_INCREMENT,
  `manufacturer` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `port_cover` tinyint(1) NOT NULL,
  `card_holder` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` enum('KOSIK','OBJEDNANO','DORUCENO') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `cases_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cases` (`id`, `manufacturer`, `model`, `color`, `port_cover`, `card_holder`, `state`, `user_id`, `created_at`) VALUES
(3,	'samsung',	'a1',	'Černá',	1,	'1 slot',	'KOSIK',	NULL,	'2025-06-24 15:05:15'),
(4,	'xiaomi',	'aaaa',	'Černá',	0,	'1 slot',	'KOSIK',	NULL,	'2025-06-24 15:05:15'),
(5,	'samsung',	'S1',	'Černá',	1,	'1 slot',	'KOSIK',	NULL,	'2025-06-24 15:05:15'),
(6,	'samsung',	'asdasdasd',	'Černá',	0,	'1 slot',	'OBJEDNANO',	3,	'2025-06-24 15:05:15'),
(7,	'samsung',	'test31231',	'Černá',	1,	'Žádný',	'OBJEDNANO',	3,	'2025-06-24 15:05:15'),
(8,	'apple',	'ano',	'Černá',	0,	'2 sloty',	'OBJEDNANO',	3,	'2025-06-24 15:05:15'),
(9,	'samsung',	'a',	'Černá',	1,	'1 slot',	'OBJEDNANO',	3,	'2025-06-24 15:05:15'),
(10,	'samsung',	'ss',	'Černá',	1,	'1 slot',	'OBJEDNANO',	3,	'2025-06-24 15:31:24'),
(11,	'xiaomi',	'Redmi Note 8',	'Modrá',	0,	'2 sloty',	'OBJEDNANO',	3,	'2025-06-24 15:47:35'),
(13,	'xiaomi',	'Lite 2',	'Černá',	1,	'2 sloty',	'OBJEDNANO',	2,	'2025-06-24 17:07:25'),
(14,	'samsung',	'a',	'Černá',	0,	'Žádný',	'KOSIK',	2,	'2025-06-24 17:09:22'),
(18,	'xiaomi',	'ssss',	'Černá',	1,	'Žádný',	'OBJEDNANO',	6,	'2025-06-24 20:12:08');

DROP TABLE IF EXISTS `order_case`;
CREATE TABLE `order_case` (
  `order_id` int NOT NULL,
  `case_id` int NOT NULL,
  `quantity` int NOT NULL,
  KEY `order_id` (`order_id`),
  KEY `case_id` (`case_id`),
  CONSTRAINT `order_case_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `order_case_ibfk_2` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `order_case` (`order_id`, `case_id`, `quantity`) VALUES
(11,	11,	1),
(11,	10,	1),
(11,	6,	1),
(11,	7,	1),
(11,	8,	1),
(11,	9,	1),
(12,	13,	1),
(13,	18,	2);

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` enum('KOSIK','OBJEDNANO','DORUCENO') COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment` enum('KARTA','PREVOD','HOTOVE') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `psc` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `orders` (`id`, `user_id`, `address`, `city`, `state`, `payment`, `psc`, `created_at`) VALUES
(11,	3,	'ulice 2',	'praha',	'OBJEDNANO',	NULL,	NULL,	'2025-06-24 16:40:51'),
(12,	2,	'prablova 921',	'Ostrava',	'OBJEDNANO',	NULL,	NULL,	'2025-06-24 17:07:48'),
(13,	6,	'231',	'brno',	'OBJEDNANO',	NULL,	NULL,	'2025-06-24 20:12:18');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_czech_ci NOT NULL,
  `firstname` varchar(100) COLLATE utf8mb4_czech_ci NOT NULL,
  `lastname` varchar(100) COLLATE utf8mb4_czech_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `role` enum('UZIVATEL','ADMIN') COLLATE utf8mb4_czech_ci NOT NULL DEFAULT 'UZIVATEL',
  `address` varchar(255) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `psc` varchar(16) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

INSERT INTO `users` (`id`, `username`, `firstname`, `lastname`, `email`, `password`, `role`, `address`, `city`, `psc`, `created_at`) VALUES
(2,	'sima',	'simon',	'látsod',	'mail@amail.com',	'$2y$10$RD/AXZzpkhbFElMayEVHse83fvmLFMHVFkPyuvCfDkZucTEFoZ6UC',	'UZIVATEL',	'Pražská 281',	'Kolín',	NULL,	'2025-06-24 11:30:58'),
(3,	'admin',	'administrátorec',	'veliký',	'admin@mail.com',	'$2y$10$5dUIUJioPW1aleFtwM.PiOIvdYUxIVq85Di4oDuOUzAgABF402auW',	'ADMIN',	'Nádražní 23',	'Praha',	NULL,	'2025-06-24 11:43:46'),
(6,	'dostals',	'',	'',	'dostals64@gmail.com',	'$2y$10$0D3LrJSeww8SWbYSCBhKSuNUH1TVHUY9c/ySD0E1jRNp.MUX8FAQS',	'ADMIN',	NULL,	NULL,	NULL,	'2025-06-24 19:27:29'),
(7,	'bakub',	'Kuba',	'Syč',	'bakua@mail.com',	'$2y$10$ZVF9RfycPsVhpryvQf50zePtoXVFCl4.6bUzZKxiSIdpdCguW4Eri',	'UZIVATEL',	NULL,	NULL,	NULL,	'2025-06-24 19:29:28');

-- 2025-06-26 08:41:30
