DROP TABLE IF EXISTS `cases`;
CREATE TABLE `cases` (
  `id` int NOT NULL AUTO_INCREMENT,
  `manufacturer` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `port_cover` tinyint(1) NOT NULL,
  `card_holder` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` enum('KOSIK','OBJEDNANO','DORUCENO') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `cases_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
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

DROP TABLE IF EXISTS `contact_info`;
CREATE TABLE `contact_info` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `ico` varchar(50) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `map_embed` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `contact_info` (`id`, `name`, `address`, `ico`, `phone`, `email`, `map_embed`) VALUES
(1,	'Martin Tkadlec',	'Sukova třída 1556, 530 02 Pardubice',	'60919264',	'+737 314 477',	'autoskolaprima@email.cz',	'<iframe src=\"https://www.google.com/maps/embed?pb=...\" width=\"100%\" height=\"300\" style=\"border:0; border-radius:8px;\" allowfullscreen=\"\" loading=\"lazy\"></iframe>');

DROP TABLE IF EXISTS `content_sections`;
CREATE TABLE `content_sections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `section_name` varchar(50) NOT NULL,
  `content_type` varchar(50) NOT NULL,
  `content_text` text,
  `image_path` varchar(255) DEFAULT NULL,
  `ordering` int DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `section_name` (`section_name`,`content_type`,`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `content_sections` (`id`, `section_name`, `content_type`, `content_text`, `image_path`, `ordering`) VALUES
(1,	'banner',	'title',	'Odolné 3D tisknuté kryty',	NULL,	1),
(2,	'banner',	'description',	'Vytvořte si pevný a stylový kryt s pokročilou 3D tiskovou technologií a vlastním designem.',	NULL,	2),
(3,	'banner',	'image',	NULL,	'/uploads/home/2.jpg',	3),
(4,	'banner',	'button_text',	'Navrhnout kryt',	NULL,	4),
(5,	'banner',	'button_link',	'detail',	NULL,	5),
(6,	'durability',	'title',	'Odolnost navržená pro život',	NULL,	1),
(7,	'durability',	'description1',	'Naše kryty zvládnou pád, prach i dobrodružství díky precizní 3D tiskové technologii.',	NULL,	2),
(8,	'durability',	'description2',	'Vyrobeny z odolných, ekologických materiálů s perfektním přizpůsobením pro váš telefon.',	NULL,	3),
(9,	'durability',	'image',	NULL,	'/uploads/home/sekera.jpg',	4),
(10,	'customization',	'title',	'Přizpůsobte si svůj kryt',	NULL,	1),
(11,	'customization',	'feature1_title',	'Ochranná krytka portu',	NULL,	2),
(12,	'customization',	'feature1_description',	'Chraňte port před prachem a nečistotami s odnímatelnou krytkou.',	NULL,	3),
(13,	'customization',	'feature1_image',	NULL,	'/uploads/home/krytka.jpg',	4),
(14,	'customization',	'feature2_title',	'Clona přední kamery',	NULL,	5),
(15,	'customization',	'feature2_description',	'Soukromí na prvním místě',	NULL,	6),
(16,	'customization',	'feature2_image',	NULL,	'/uploads/home/zaslepka.jpg',	7),
(17,	'customization',	'feature3_title',	'Integrované měřítko',	NULL,	8),
(18,	'customization',	'feature3_description',	'Praktické měřítko pro každodenní použití přímo na krytu.',	NULL,	9),
(19,	'customization',	'feature3_image',	NULL,	'/uploads/home/pravitko.jpg',	10),
(20,	'customization',	'button_text',	'Začít navrhovat',	NULL,	11),
(21,	'customization',	'button_link',	'default',	NULL,	12);

DROP TABLE IF EXISTS `form_options`;
CREATE TABLE `form_options` (
  `id` int NOT NULL AUTO_INCREMENT,
  `field_name` varchar(50) NOT NULL,
  `option_value` varchar(100) NOT NULL,
  `option_label` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `form_options` (`id`, `field_name`, `option_value`, `option_label`, `created_at`) VALUES
(1,	'manufacturer',	'apple',	'Apple',	'2025-06-30 15:48:56'),
(2,	'manufacturer',	'samsung',	'Samsung',	'2025-06-30 15:48:56'),
(3,	'manufacturer',	'xiaomi',	'Xiaomi',	'2025-06-30 15:48:56'),
(4,	'color',	'Černá',	'Černá',	'2025-06-30 15:48:56'),
(5,	'color',	'Bílá',	'Bílá',	'2025-06-30 15:48:56'),
(6,	'color',	'Modrá',	'Modrá',	'2025-06-30 15:48:56'),
(7,	'color',	'Červená',	'Červená',	'2025-06-30 15:48:56'),
(8,	'port_cover',	'1',	'Ano',	'2025-06-30 15:48:56'),
(9,	'port_cover',	'0',	'Ne',	'2025-06-30 15:48:56'),
(10,	'card_holder',	'1 slot',	'1 slot',	'2025-06-30 15:48:56'),
(11,	'card_holder',	'2 sloty',	'2 sloty',	'2025-06-30 15:48:56'),
(12,	'card_holder',	'Žádný',	'Žádný',	'2025-06-30 15:48:56');

DROP TABLE IF EXISTS `gallery`;
CREATE TABLE `gallery` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `ordering` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `gallery` (`id`, `image`, `alt_text`, `ordering`) VALUES
(1,	'/uploads/home/pisek.png',	'Kryt 1',	1),
(2,	'/uploads/home/showcase.jpg',	'Kryt 2',	2),
(3,	'/uploads/home/showcase1.jpg',	'Kryt 3',	3),
(4,	'/uploads/home/showcase2.jpg',	'Kryt 4',	4);

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
  `state` enum('KOSIK','OBJEDNANO','DORUCENO') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment` enum('PREVOD','DOBIRKA') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `psc` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci NOT NULL,
  `firstname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci NOT NULL,
  `lastname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci NOT NULL,
  `role` enum('UZIVATEL','ADMIN') CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci NOT NULL DEFAULT 'UZIVATEL',
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `city` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `psc` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reset_code` int DEFAULT NULL,
  `reset_code_expires` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

INSERT INTO `users` (`id`, `username`, `firstname`, `lastname`, `email`, `password`, `role`, `address`, `city`, `psc`, `created_at`, `reset_code`, `reset_code_expires`) VALUES
(2,	'sima',	'simon',	'látsod',	'mail@amail.com',	'$2y$10$RD/AXZzpkhbFElMayEVHse83fvmLFMHVFkPyuvCfDkZucTEFoZ6UC',	'UZIVATEL',	'Pražská 281',	'Kolín',	NULL,	'2025-06-24 11:30:58',	NULL,	NULL),
(3,	'admin',	'administrátor',	'veliký',	'admin@mail.com',	'$2y$10$5dUIUJioPW1aleFtwM.PiOIvdYUxIVq85Di4oDuOUzAgABF402auW',	'ADMIN',	'Nádražní 23',	'Praha',	'110 03',	'2025-06-24 11:43:46',	NULL,	NULL),
(6,	'dostals',	'',	'',	'dostals64@gmail.com',	'$2y$10$RJhN6zE1eZqpGLSBTyFI/OApvmXrcaobEVeOjzfvYmLePRuqrMBRG',	'ADMIN',	NULL,	NULL,	NULL,	'2025-06-24 19:27:29',	241712,	'2025-06-30 21:04:00'),
(7,	'bakub',	'Kuba',	'Syč',	'bakua@mail.com',	'$2y$10$ZVF9RfycPsVhpryvQf50zePtoXVFCl4.6bUzZKxiSIdpdCguW4Eri',	'UZIVATEL',	NULL,	NULL,	NULL,	'2025-06-24 19:29:28',	NULL,	NULL),
(8,	'igor',	'igor',	'rucicka',	'igor@mail.com',	'$2y$10$bEiEpKsd.RXoA7yvEk9QdOZ9LC9zdlX7MpgYnGDJ7S52QVI5U5Flm',	'UZIVATEL',	NULL,	NULL,	NULL,	'2025-06-29 14:10:39',	NULL,	NULL);