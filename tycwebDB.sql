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
  CONSTRAINT `cases_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(18,	'xiaomi',	'ssss',	'Černá',	1,	'Žádný',	'OBJEDNANO',	6,	'2025-06-24 20:12:08'),
(19,	'samsung',	'a',	'Černá',	0,	'1 slot',	'OBJEDNANO',	3,	'2025-06-26 08:43:08'),
(20,	'samsung',	'S2',	'Černá',	1,	'1 slot',	'OBJEDNANO',	3,	'2025-06-26 08:44:34'),
(22,	'xiaomi',	'aaa',	'Černá',	1,	'1 slot',	'OBJEDNANO',	3,	'2025-06-29 13:54:43'),
(23,	'samsung',	'S1',	'Černá',	1,	'1 slot',	'OBJEDNANO',	3,	'2025-06-29 13:57:17'),
(24,	'samsung',	'ssss',	'Černá',	1,	'2 sloty',	'OBJEDNANO',	3,	'2025-06-29 13:59:34'),
(25,	'xiaomi',	'model',	'Bílá',	1,	'Žádný',	'OBJEDNANO',	3,	'2025-06-29 13:59:46'),
(26,	'samsung',	'as',	'Bílá',	1,	'1 slot',	NULL,	NULL,	'2025-06-30 14:04:01'),
(27,	'xiaomi',	'a',	'Bílá',	0,	'1 slot',	NULL,	NULL,	'2025-06-30 14:04:59'),
(28,	'samsung',	'aXXXX',	'Černá',	1,	'1 slot',	NULL,	NULL,	'2025-06-30 14:06:28'),
(29,	'samsung',	'yyyyy',	'Černá',	1,	'1 slot',	'KOSIK',	NULL,	'2025-06-30 14:09:35'),
(30,	'samsung',	'RRR',	'Černá',	1,	'1 slot',	'KOSIK',	NULL,	'2025-06-30 14:16:10'),
(31,	'samsung',	'PPPPP',	'Černá',	1,	'1 slot',	'KOSIK',	NULL,	'2025-06-30 14:18:59'),
(32,	'samsung',	'sessiomn test',	'Bílá',	1,	'1 slot',	'KOSIK',	NULL,	'2025-06-30 14:32:54'),
(33,	'xiaomi',	'CCCC',	'Bílá',	1,	'1 slot',	'KOSIK',	NULL,	'2025-06-30 14:35:54'),
(34,	'samsung',	'ahoj',	'Černá',	0,	'1 slot',	'KOSIK',	NULL,	'2025-06-30 14:36:42'),
(35,	'xiaomi',	'a',	'Černá',	1,	'1 slot',	'KOSIK',	NULL,	'2025-06-30 14:37:16'),
(36,	'samsung',	'a',	'Modrá',	1,	'1 slot',	'OBJEDNANO',	3,	'2025-06-30 15:53:42'),
(37,	'xiaomi',	'A',	'Černá',	1,	'Žádný',	'OBJEDNANO',	3,	'2025-06-30 16:00:25'),
(38,	'xiaomi',	'B',	'Černá',	0,	'Žádný',	'OBJEDNANO',	3,	'2025-06-30 16:00:32'),
(39,	'apple',	'C',	'Černá',	0,	'2 sloty',	'OBJEDNANO',	3,	'2025-06-30 16:00:42'),
(40,	'xiaomi',	'test',	'Bílá',	1,	'1 slot',	'OBJEDNANO',	3,	'2025-06-30 16:08:43');

DROP TABLE IF EXISTS `order_case`;
CREATE TABLE `order_case` (
  `order_id` int NOT NULL,
  `case_id` int NOT NULL,
  `quantity` int NOT NULL,
  KEY `order_id` (`order_id`),
  KEY `case_id` (`case_id`),
  CONSTRAINT `order_case_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `order_case_ibfk_4` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `order_case` (`order_id`, `case_id`, `quantity`) VALUES
(11,	11,	1),
(11,	10,	1),
(11,	6,	1),
(11,	7,	1),
(11,	8,	1),
(11,	9,	1),
(12,	13,	1),
(13,	18,	2),
(14,	19,	1),
(15,	20,	1),
(16,	23,	1),
(16,	22,	1),
(17,	29,	1),
(22,	34,	1),
(23,	35,	1),
(24,	25,	1),
(24,	24,	1),
(25,	36,	1),
(26,	36,	1),
(27,	39,	1),
(27,	38,	1),
(27,	37,	1),
(28,	39,	1),
(28,	38,	1),
(28,	37,	1),
(29,	39,	1),
(29,	38,	1),
(29,	37,	1),
(30,	40,	1);

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `firstname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` enum('KOSIK','OBJEDNANO','DORUCENO') COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment` enum('PREVOD','DOBIRKA') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `psc` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `orders` (`id`, `user_id`, `firstname`, `lastname`, `email`, `address`, `city`, `state`, `payment`, `psc`, `created_at`) VALUES
(11,	3,	'',	'',	'',	'ulice 2',	'praha',	'OBJEDNANO',	NULL,	NULL,	'2025-06-24 16:40:51'),
(12,	2,	'',	'',	'',	'prablova 921',	'Ostrava',	'OBJEDNANO',	NULL,	NULL,	'2025-06-24 17:07:48'),
(13,	6,	'',	'',	'',	'231',	'brno',	'OBJEDNANO',	NULL,	NULL,	'2025-06-24 20:12:18'),
(14,	3,	'',	'',	'',	'Nádražní 23',	'Praha',	'OBJEDNANO',	NULL,	NULL,	'2025-06-26 08:43:23'),
(15,	3,	'',	'',	'',	'Nádražní 23',	'Praha 3',	'OBJEDNANO',	NULL,	'110 03',	'2025-06-26 08:45:02'),
(16,	3,	'',	'',	'',	'Nádražní 23',	'Praha',	'OBJEDNANO',	NULL,	'110 03',	'2025-06-29 13:58:17'),
(17,	NULL,	'',	'',	'',	'YXXYXY',	'YYYY',	'OBJEDNANO',	NULL,	'2131',	'2025-06-30 14:12:44'),
(22,	NULL,	'asda',	'sd',	'',	'asdas',	'da',	'OBJEDNANO',	NULL,	'asd',	'2025-06-30 14:36:48'),
(23,	NULL,	'a',	'a',	'',	'a',	'a',	'OBJEDNANO',	NULL,	'a',	'2025-06-30 14:37:21'),
(24,	3,	'administrátor',	'veliký',	'',	'Nádražní 23',	'Praha',	'OBJEDNANO',	NULL,	'110 03',	'2025-06-30 14:37:31'),
(25,	3,	'administrátor',	'veliký',	'admin@mail.com',	'Nádražní 23',	'Praha',	'OBJEDNANO',	NULL,	'110 03',	'2025-06-30 15:54:56'),
(26,	3,	'administrátor',	'veliký',	'dostals64@gmail.com',	'Nádražní 23',	'Praha',	'OBJEDNANO',	NULL,	'110 03',	'2025-06-30 15:55:24'),
(27,	3,	'administrátor',	'veliký',	'dostals64@gmail.com',	'Nádražní 23',	'Praha',	'OBJEDNANO',	NULL,	'110 03',	'2025-06-30 16:00:51'),
(28,	3,	'administrátor',	'veliký',	'dostals64@gmail.com',	'Nádražní 23',	'Praha',	'OBJEDNANO',	NULL,	'110 03',	'2025-06-30 16:02:43'),
(29,	3,	'administrátor',	'veliký',	'dostals64@gmail.com',	'Nádražní 23',	'Praha',	'OBJEDNANO',	NULL,	'110 03',	'2025-06-30 16:04:34'),
(30,	3,	'administrátor',	'veliký',	'dostals64@gmail.com',	'Nádražní 23',	'Praha',	'OBJEDNANO',	NULL,	'110 03',	'2025-06-30 16:08:48');

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

INSERT INTO `users` (`id`, `username`, `firstname`, `lastname`, `email`, `password`, `role`, `address`, `city`, `psc`, `created_at`) VALUES
(2,	'sima',	'simon',	'látsod',	'mail@amail.com',	'$2y$10$RD/AXZzpkhbFElMayEVHse83fvmLFMHVFkPyuvCfDkZucTEFoZ6UC',	'UZIVATEL',	'Pražská 281',	'Kolín',	NULL,	'2025-06-24 11:30:58'),
(3,	'admin',	'administrátor',	'veliký',	'admin@mail.com',	'$2y$10$5dUIUJioPW1aleFtwM.PiOIvdYUxIVq85Di4oDuOUzAgABF402auW',	'ADMIN',	'Nádražní 23',	'Praha',	'110 03',	'2025-06-24 11:43:46'),
(6,	'dostals',	'',	'',	'dostals64@gmail.com',	'$2y$10$0D3LrJSeww8SWbYSCBhKSuNUH1TVHUY9c/ySD0E1jRNp.MUX8FAQS',	'ADMIN',	NULL,	NULL,	NULL,	'2025-06-24 19:27:29'),
(7,	'bakub',	'Kuba',	'Syč',	'bakua@mail.com',	'$2y$10$ZVF9RfycPsVhpryvQf50zePtoXVFCl4.6bUzZKxiSIdpdCguW4Eri',	'UZIVATEL',	NULL,	NULL,	NULL,	'2025-06-24 19:29:28'),
(8,	'igor',	'igor',	'rucicka',	'igor@mail.com',	'$2y$10$bEiEpKsd.RXoA7yvEk9QdOZ9LC9zdlX7MpgYnGDJ7S52QVI5U5Flm',	'UZIVATEL',	NULL,	NULL,	NULL,	'2025-06-29 14:10:39');

-- 2025-06-30 16:09:56
