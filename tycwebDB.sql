REATE TABLE `banner` (
  `id` int NOT NULL AUTO_INCREMENT,
  `content_type` enum('title','description','image','button_text','button_link') NOT NULL,
  `content_text` text,
  `image_path` varchar(255) DEFAULT NULL,
  `ordering` int DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `content_type_ordering` (`content_type`,`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `banner` (`id`, `content_type`, `content_text`, `image_path`, `ordering`) VALUES
(1,	'title',	'Odolné 3D tisknuté kryty',	NULL,	1),
(2,	'description',	'Vytvořte si pevný a stylový kryt s pokročilou 3D tiskovou technologií a vlastním designem.',	NULL,	2),
(3,	'image',	NULL,	'/uploads/home/2.jpg',	3),
(4,	'button_text',	'Navrhnout kryt',	NULL,	4),
(5,	'button_link',	'detail',	NULL,	5);

DROP TABLE IF EXISTS `cases`;
CREATE TABLE `cases` (
  `id` int NOT NULL AUTO_INCREMENT,
  `manufacturer` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_price` decimal(10,2) DEFAULT '0.00',
  `features` json DEFAULT NULL,
  `state` enum('KOSIK','OBJEDNANO','DORUCENO') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `cases_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cases` (`id`, `manufacturer`, `model`, `color`, `total_price`, `features`, `state`, `user_id`, `created_at`) VALUES
(20,	'Apple',	'iPhone 13',	'Bílá',	634.99,	'\"{\\\"držák_karet\\\":\\\"2 Sloty\\\",\\\"clona_přední_kamery\\\":\\\"Ano\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\",\\\"test\\\":\\\"1\\\"}\"',	'OBJEDNANO',	9,	'2025-07-10 19:20:13'),
(22,	'Apple',	'iPhone 13',	'fialova',	634.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"2 Sloty\\\",\\\"clona_přední_kamery\\\":\\\"Ano\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\",\\\"test\\\":\\\"1\\\"}\"}',	'OBJEDNANO',	9,	'2025-07-10 21:47:52'),
(23,	'Apple',	'iPhone 13',	'Modrá',	559.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"Žádný\\\",\\\"clona_přední_kamery\\\":\\\"Ano\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ne\\\",\\\"test\\\":\\\"2\\\"}\"}',	'OBJEDNANO',	9,	'2025-07-10 22:00:17'),
(24,	'Apple',	'iPhone 13',	'Modrá',	604.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"2 Sloty\\\",\\\"clona_přední_kamery\\\":\\\"Ano\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ne\\\",\\\"test\\\":\\\"2\\\"}\"}',	'OBJEDNANO',	9,	'2025-07-10 22:06:16'),
(25,	'Apple',	'iPhone 13',	'Modrá',	634.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"2 Sloty\\\",\\\"clona_přední_kamery\\\":\\\"Ano\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\",\\\"test\\\":\\\"1\\\"}\"}',	'OBJEDNANO',	9,	'2025-07-10 22:10:02'),
(26,	'Apple',	'iPhone 13',	'Bílá',	559.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"Žádný\\\",\\\"clona_přední_kamery\\\":\\\"Ne\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\",\\\"test\\\":\\\"2\\\"}\"}',	'KOSIK',	NULL,	'2025-07-10 22:17:02'),
(27,	'Apple',	'iPhone 13',	'Modrá',	559.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"Žádný\\\",\\\"clona_přední_kamery\\\":\\\"Ne\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\",\\\"test\\\":\\\"2\\\"}\"}',	'OBJEDNANO',	9,	'2025-07-11 06:06:10'),
(28,	'Apple',	'iPhone 13',	'Červená',	559.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"Žádný\\\",\\\"clona_přední_kamery\\\":\\\"Ano\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ne\\\",\\\"test\\\":\\\"2\\\"}\"}',	'OBJEDNANO',	9,	'2025-07-11 06:29:43'),
(29,	'Apple',	'iPhone 13',	'Červená',	529.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"Žádný\\\",\\\"clona_přední_kamery\\\":\\\"Ne\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ne\\\",\\\"test\\\":\\\"2\\\"}\"}',	'KOSIK',	NULL,	'2025-07-11 06:41:21'),
(30,	'Apple',	'iPhone 13',	'Modrá',	589.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"Žádný\\\",\\\"clona_přední_kamery\\\":\\\"Ano\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\",\\\"test\\\":\\\"1\\\"}\"}',	'OBJEDNANO',	9,	'2025-07-11 07:33:47'),
(31,	'Apple',	'iPhone 13',	'fialova',	634.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"2 Sloty\\\",\\\"clona_přední_kamery\\\":\\\"Ano\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\",\\\"test\\\":\\\"1\\\"}\"}',	'OBJEDNANO',	9,	'2025-07-11 07:35:26'),
(32,	'Apple',	'iPhone 13',	'Černá',	634.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"2 Sloty\\\",\\\"clona_přední_kamery\\\":\\\"Ano\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\",\\\"test\\\":\\\"1\\\"}\"}',	'OBJEDNANO',	9,	'2025-07-11 07:49:40');

DROP TABLE IF EXISTS `colors`;
CREATE TABLE `colors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `hex_code` varchar(7) DEFAULT NULL COMMENT 'Optional hex color code (e.g., #FF0000 for red)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `colors` (`id`, `name`, `hex_code`, `created_at`) VALUES
(1,	'Černá',	'#000000',	'2025-07-09 14:37:00'),
(2,	'Bílá',	'#FFFFFF',	'2025-07-09 14:37:00'),
(3,	'Modrá',	'#0000FF',	'2025-07-09 14:37:00'),
(4,	'Červená',	'#FF0000',	'2025-07-09 14:37:00'),
(5,	'Zelená',	'#00FF00',	'2025-07-09 14:37:00'),
(6,	'oranžová',	'#FFAC1C',	'2025-07-09 12:39:45'),
(10,	'fialova',	'#bd00ff',	'2025-07-10 15:01:31');

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
(1,	'Karel Dvořák',	'shrekova bazina 13',	'12345678',	'123 456 789',	'email@email.cz',	'<iframe src=\"https://www.google.com/maps/embed?pb=...\" width=\"100%\" height=\"300\" style=\"border:0; border-radius:8px;\" allowfullscreen=\"\" loading=\"lazy\"></iframe>');

DROP TABLE IF EXISTS `customization`;
CREATE TABLE `customization` (
  `id` int NOT NULL AUTO_INCREMENT,
  `content_type` varchar(50) NOT NULL,
  `content_text` text,
  `image_path` varchar(255) DEFAULT NULL,
  `ordering` int DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `content_type_ordering` (`content_type`,`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `customization` (`id`, `content_type`, `content_text`, `image_path`, `ordering`) VALUES
(1,	'title',	'Přizpůsobte si svůj kryt',	NULL,	1),
(2,	'feature1_title',	'Ochranná krytka portu',	NULL,	2),
(3,	'feature1_description',	'Chraňte port před prachem a nečistotami s odnímatelnou krytkou.',	NULL,	3),
(4,	'feature1_image',	NULL,	'/uploads/home/krytka.jpg',	4),
(5,	'feature2_title',	'Clona přední kamery',	NULL,	5),
(6,	'feature2_description',	'Soukromí na prvním místě',	NULL,	6),
(7,	'feature2_image',	NULL,	'/uploads/home/zaslepka.jpg',	7),
(8,	'feature3_title',	'Integrované měřítko',	NULL,	8),
(9,	'feature3_description',	'Praktické měřítko pro každodenní použití přímo na krytu.',	NULL,	9),
(10,	'feature3_image',	NULL,	'/uploads/home/pravitko.jpg',	10),
(11,	'button_text',	'Začít navrhovat',	NULL,	11),
(12,	'button_link',	'detail',	NULL,	12);

DROP TABLE IF EXISTS `durability`;
CREATE TABLE `durability` (
  `id` int NOT NULL AUTO_INCREMENT,
  `content_type` enum('title','description1','description2','image') NOT NULL,
  `content_text` text,
  `image_path` varchar(255) DEFAULT NULL,
  `ordering` int DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `content_type_ordering` (`content_type`,`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `durability` (`id`, `content_type`, `content_text`, `image_path`, `ordering`) VALUES
(1,	'title',	'Odolnost navržená pro život',	NULL,	1),
(2,	'description1',	'Naše kryty zvládnou pád, prach i dobrodružství díky precizní 3D tiskové technologii',	NULL,	2),
(3,	'description2',	'Vyrobeny z odolných, ekologických materiálů s perfektním přizpůsobením pro váš telefon.',	NULL,	3),
(4,	'image',	NULL,	'/uploads/home/sekera.jpg',	4);

DROP TABLE IF EXISTS `email_templates`;
CREATE TABLE `email_templates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `recipient_email` varchar(255) DEFAULT NULL,
  `admin_phone` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `pdf_paths` text,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `email_templates` (`id`, `name`, `subject`, `body`, `recipient_email`, `admin_phone`, `created_at`, `pdf_paths`, `updated_at`) VALUES
(11,	'registration',	'Vítejte! Registrace byla úspěšná',	'<!DOCTYPE html>\r\n<html lang=\"cs\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <title>Registrace na stránky 3D kryty</title>\r\n</head>\r\n<body>\r\n    <h1>Zdravím, {$username}!</h1>\r\n    <p>Vaše registrace do 3D kryty proběhla úspěšně.</p>    \r\n</body>\r\n</html>',	NULL,	NULL,	'2025-07-10 21:54:32',	NULL,	'2025-07-10 21:54:32'),
(12,	'new_user',	'Nová registrace uživatele',	'<!DOCTYPE html>\r\n<html lang=\"cs\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <title>Nová registrace</title>\r\n</head>\r\n<body>\r\n    <h1>Nová registrace uživatele {$username}</h1>\r\n    <p>Do systému 3D kryty byl zaregistrován nový uživatel {$username}</p>\r\n    <p>Email: {$email}</p>\r\n</body>\r\n</html>',	'okurkyvmalinovce@seznam.cz',	NULL,	'2025-07-10 21:54:32',	NULL,	'2025-07-10 21:54:32'),
(13,	'password_reset',	'Reset hesla',	'<!DOCTYPE html>\r\n<html lang=\"cs\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <title>Obnova hesla</title>\r\n</head>\r\n<body>\r\n    <h1>Obnova hesla</h1>\r\n    <p>Dobrý den,</p>\r\n    <p>Váš ověřovací kód: <strong>{$resetCode}</strong></p>\r\n    <p>Zadejte tento kód na stránce pro obnovu hesla. Je platný po dobu 10 minut.</p>\r\n    <p>Pokud jste o reset nežádali, tento email ignorujte.</p>\r\n</body>\r\n</html>',	NULL,	NULL,	'2025-07-10 21:54:32',	NULL,	'2025-07-10 21:54:32'),
(15,	'invoice',	'Faktura č. {$order.id}',	'<style>\r\n    body {\r\n        font-family: DejaVu Sans, sans-serif;\r\n        font-size: 12px;\r\n        color: #333;\r\n    }\r\n    .header {\r\n        text-align: center;\r\n        margin-bottom: 20px;\r\n    }\r\n    .details, .items, .costs {\r\n        width: 100%;\r\n        margin-bottom: 20px;\r\n        border-collapse: collapse;\r\n    }\r\n    .details td, .costs td {\r\n        padding: 5px;\r\n        vertical-align: top;\r\n    }\r\n    .items th, .items td {\r\n        border: 1px solid #999;\r\n        padding: 6px;\r\n        text-align: left;\r\n    }\r\n    .items th {\r\n        background-color: #f2f2f2;\r\n    }\r\n    h1 {\r\n        font-size: 20px;\r\n        margin-bottom: 5px;\r\n    }\r\n    hr {\r\n        margin: 20px 0;\r\n    }\r\n</style>\r\n\r\n<div class=\"header\">\r\n    <h1>Faktura č. {$order.id}</h1>\r\n    <p>Datum vystavení: {$created_at}</p>\r\n</div>\r\n\r\n<table class=\"details\">\r\n    <tr>\r\n        <td><strong>Odběratel:</strong><br>{$recipient}</td>\r\n        <td>\r\n            <strong>Adresa:</strong><br>\r\n            {$order.address}<br>\r\n            {$order.city}, {$order.psc}<br>\r\n            {if $order.delivery_point}Výdejní místo: {$order.delivery_point}{/if}\r\n        </td>\r\n    </tr>\r\n</table>\r\n\r\n<hr>\r\n\r\n<table class=\"items\">\r\n    <thead>\r\n        <tr>\r\n            <th>#</th>\r\n            <th>ID</th>\r\n            <th>Výrobce</th>\r\n            <th>Model</th>\r\n            <th>Barva</th>\r\n            <th>Vlastnosti</th>\r\n            <th>Množství</th>\r\n            <th>Cena za kus</th>\r\n            <th>Celkem</th>\r\n        </tr>\r\n    </thead>\r\n    <tbody>\r\n        {foreach $items as $i => $item}\r\n            <tr>\r\n                <td>{$i + 1}</td>\r\n                <td>{$item.id}</td>\r\n                <td>{$item.manufacturer}</td>\r\n                <td>{$item.model}</td>\r\n                <td>{$item.color}</td>\r\n                <td>\r\n                    {foreach $item.features as $key => $value}\r\n                        {$key}: {$value}<br>\r\n                    {/foreach}\r\n                </td>\r\n                <td>{$item.quantity}</td>\r\n                <td>{$item.total_price|number:2,\",\",\" \"} Kč</td>\r\n                <td>{($item.total_price * $item.quantity)|number:2,\",\",\" \"} Kč</td>\r\n            </tr>\r\n        {/foreach}\r\n    </tbody>\r\n</table>\r\n\r\n<table class=\"costs\">\r\n    <tr>\r\n        <td><strong>Mezisoučet položek:</strong></td>\r\n        <td>{$itemsSubtotal|number:2,\",\",\" \"} Kč</td>\r\n    </tr>\r\n    <tr>\r\n        <td><strong>Doprava ({$order.shipping}):</strong></td>\r\n        <td>{$shippingCost|number:2,\",\",\" \"} Kč</td>\r\n    </tr>\r\n    <tr>\r\n        <td><strong>Platba ({$order.payment}):</strong></td>\r\n        <td>{$paymentCost|number:2,\",\",\" \"} Kč</td>\r\n    </tr>\r\n    <tr>\r\n        <td><strong>Celková částka:</strong></td>\r\n        <td>{($itemsSubtotal + $order.additional_cost)|number:2,\",\",\" \"} Kč</td>\r\n    </tr>\r\n</table>',	NULL,	NULL,	'2025-07-10 21:54:32',	NULL,	'2025-07-10 22:17:52'),
(16,	'invoice_email',	'Faktura za vaši objednávku č. {$orderId}',	'<!DOCTYPE html>\r\n<html lang=\"cs\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <title>Faktura za vaši objednávku č. {$orderId}</title>\r\n</head>\r\n<body style=\"font-family: DejaVu Sans, sans-serif; font-size: 14px; color: #333;\">\r\n    <h1 style=\"font-size: 20px; margin-bottom: 10px;\">Dobrý den, {$recipient}!</h1>\r\n    <p style=\"margin-bottom: 10px;\">Vaše objednávka č. {$orderId} byla přijata.</p>\r\n    <p style=\"margin-bottom: 20px;\">V příloze naleznete fakturu s detaily vaší objednávky.</p>\r\n    <h2 style=\"font-size: 16px; font-weight: bold; margin-bottom: 10px;\">Shrnutí objednávky</h2>\r\n    <ul style=\"list-style: none; padding: 0; margin-bottom: 20px;\">\r\n        <li style=\"margin-bottom: 5px;\"><strong>Mezisoučet položek:</strong> {$itemsSubtotal|number:2,\",\",\" \"} Kč</li>\r\n        <li style=\"margin-bottom: 5px;\"><strong>Doprava ({$order.shipping}):</strong> {$shippingCost|number:2,\",\",\" \"} Kč</li>\r\n        <li style=\"margin-bottom: 5px;\"><strong>Platba ({$order.payment}):</strong> {$paymentCost|number:2,\",\",\" \"} Kč</li>\r\n        <li style=\"margin-bottom: 5px;\"><strong>Celková částka:</strong> {($itemsSubtotal + $order.additional_cost)|number:2,\",\",\" \"} Kč</li>\r\n    </ul>\r\n    <p style=\"margin-bottom: 10px;\">Děkujeme za váš nákup!</p>\r\n    <p style=\"font-size: 12px; color: #666;\">Pokud máte jakékoli dotazy, kontaktujte nás na <a href=\"mailto:okurkyvmalinovce@seznam.cz\" style=\"color: #007bff;\">okurkyvmalinovce@seznam.cz</a>.</p>\r\n</body>\r\n</html>',	NULL,	NULL,	'2025-07-10 21:54:32',	NULL,	'2025-07-10 21:54:32');

DROP TABLE IF EXISTS `feature_options`;
CREATE TABLE `feature_options` (
  `id` int NOT NULL AUTO_INCREMENT,
  `feature_id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `price` decimal(10,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_feature_option` (`feature_id`,`name`),
  KEY `feature_id` (`feature_id`),
  CONSTRAINT `feature_options_ibfk_1` FOREIGN KEY (`feature_id`) REFERENCES `features` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `feature_options` (`id`, `feature_id`, `name`, `price`, `created_at`) VALUES
(16,	16,	'Ano',	30.00,	'2025-07-09 19:46:47'),
(18,	16,	'Ne',	0.00,	'2025-07-09 19:46:50'),
(24,	12,	'2 Sloty',	45.00,	'2025-07-09 19:47:06'),
(25,	12,	'Žádný',	0.00,	'2025-07-09 19:47:37'),
(30,	18,	'Ano',	30.00,	'2025-07-09 20:05:52'),
(31,	18,	'Ne',	0.00,	'2025-07-09 20:05:58'),
(36,	21,	'1',	0.00,	'2025-07-10 18:07:59'),
(37,	21,	'2',	0.00,	'2025-07-10 18:08:02');

DROP TABLE IF EXISTS `features`;
CREATE TABLE `features` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `features` (`id`, `name`, `created_at`) VALUES
(12,	'Držák karet',	'2025-07-09 19:45:56'),
(16,	'Clona přední kamery',	'2025-07-09 19:46:07'),
(18,	'Krytka nabíjecího portu',	'2025-07-09 19:46:27'),
(21,	'test',	'2025-07-10 18:07:49');

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

DROP TABLE IF EXISTS `manufacturers`;
CREATE TABLE `manufacturers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `manufacturers` (`id`, `name`, `created_at`) VALUES
(1,	'Apple',	'2025-07-03 16:52:35'),
(2,	'Samsung',	'2025-07-03 16:52:35'),
(3,	'Xiaomi',	'2025-07-03 16:52:35'),
(4,	'Lenovo',	'2025-07-08 20:36:34'),
(6,	'hujavej',	'2025-07-08 20:36:51'),
(7,	'test',	'2025-07-09 13:11:16');

DROP TABLE IF EXISTS `model_colors`;
CREATE TABLE `model_colors` (
  `model_id` int NOT NULL,
  `color_id` int NOT NULL,
  PRIMARY KEY (`model_id`,`color_id`),
  KEY `color_id` (`color_id`),
  CONSTRAINT `model_colors_ibfk_1` FOREIGN KEY (`model_id`) REFERENCES `models` (`id`) ON DELETE CASCADE,
  CONSTRAINT `model_colors_ibfk_2` FOREIGN KEY (`color_id`) REFERENCES `colors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `model_colors` (`model_id`, `color_id`) VALUES
(1,	1),
(2,	1),
(3,	1),
(4,	1),
(5,	1),
(1,	2),
(2,	2),
(3,	2),
(4,	2),
(1,	3),
(2,	3),
(3,	3),
(4,	3),
(1,	4),
(2,	4),
(5,	4),
(1,	10);

DROP TABLE IF EXISTS `model_features`;
CREATE TABLE `model_features` (
  `model_id` int NOT NULL,
  `feature_id` int NOT NULL,
  `feature_option_id` int DEFAULT NULL,
  PRIMARY KEY (`model_id`,`feature_id`),
  KEY `feature_id` (`feature_id`),
  KEY `model_features_ibfk_3` (`feature_option_id`),
  CONSTRAINT `model_features_ibfk_1` FOREIGN KEY (`model_id`) REFERENCES `models` (`id`) ON DELETE CASCADE,
  CONSTRAINT `model_features_ibfk_2` FOREIGN KEY (`feature_id`) REFERENCES `features` (`id`) ON DELETE CASCADE,
  CONSTRAINT `model_features_ibfk_3` FOREIGN KEY (`feature_option_id`) REFERENCES `feature_options` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `model_features` (`model_id`, `feature_id`, `feature_option_id`) VALUES
(1,	16,	18),
(3,	16,	18),
(4,	16,	18),
(1,	12,	25),
(4,	12,	25),
(1,	18,	31),
(1,	21,	37);

DROP TABLE IF EXISTS `models`;
CREATE TABLE `models` (
  `id` int NOT NULL AUTO_INCREMENT,
  `manufacturer_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Base price of the model',
  PRIMARY KEY (`id`),
  KEY `manufacturer_id` (`manufacturer_id`),
  CONSTRAINT `models_ibfk_1` FOREIGN KEY (`manufacturer_id`) REFERENCES `manufacturers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `models` (`id`, `manufacturer_id`, `name`, `created_at`, `price`) VALUES
(1,	1,	'iPhone 13',	'2025-07-03 16:52:35',	529.99),
(2,	1,	'iPhone 14',	'2025-07-03 16:52:35',	617.00),
(3,	2,	'Galaxy S22',	'2025-07-03 16:52:35',	500.00),
(4,	2,	'Galaxy S23',	'2025-07-03 16:52:35',	510.00),
(5,	3,	'Mi 12',	'2025-07-03 16:52:35',	199.99);

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
(36,	28,	1),
(37,	30,	1),
(38,	31,	1),
(39,	32,	1);

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `firstname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `psc` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment` enum('PREVOD','DOBIRKA') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_point` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `additional_cost` decimal(10,2) DEFAULT '0.00',
  `state` enum('KOSIK','OBJEDNANO','DORUCENO') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `variable_symbol` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `variable_symbol` (`variable_symbol`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `orders` (`id`, `user_id`, `firstname`, `lastname`, `email`, `phone`, `address`, `city`, `psc`, `payment`, `shipping`, `delivery_point`, `additional_cost`, `state`, `created_at`, `variable_symbol`) VALUES
(36,	9,	'Martin',	'Burda',	'burdadko.cz@gmail.com',	'666777888',	'shrekova bazina 13',	'Praha',	'53803',	'PREVOD',	'ZASILKOVNA',	'sokolská 799 hermanuv mestec',	70.00,	'OBJEDNANO',	'2025-07-11 07:31:53',	'202507110823'),
(37,	9,	'Martin',	'Burda',	'burdadko.cz@gmail.com',	'666777888',	'shrekova bazina 13',	'Praha',	'53803',	'DOBIRKA',	'ZASILKOVNA',	'sokolská 799 hermanuv mestec',	110.00,	'OBJEDNANO',	'2025-07-11 07:34:07',	'202507112143'),
(38,	9,	'Martin',	'Burda',	'burdadko.cz@gmail.com',	'666777888',	'shrekova bazina 13',	'Praha',	'53803',	'DOBIRKA',	'ZASILKOVNA',	'sokolská 799 hermanuv mestec',	110.00,	'OBJEDNANO',	'2025-07-11 07:35:49',	'202507117212'),
(39,	9,	'Martin',	'Burda',	'burdadko.cz@gmail.com',	'666777888',	'shrekova bazina 13',	'Praha',	'53803',	'PREVOD',	'ZASILKOVNA',	'sokolská 799 hermanuv mestec',	70.00,	'OBJEDNANO',	'2025-07-11 07:49:56',	'202507112228');

DROP TABLE IF EXISTS `shipping`;
CREATE TABLE `shipping` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `shipping` (`id`, `code`, `name`, `cost`, `description`, `created_at`) VALUES
(1,	'CESKA_POSTA',	'Česká pošta',	100.00,	'Doručení prostřednictvím České pošty',	'2025-07-10 21:06:00'),
(2,	'ZASILKOVNA',	'Zásilkovna',	70.00,	'Doručení na výdejní místo Zásilkovny',	'2025-07-10 21:06:00'),
(3,	'BALIKOVNA',	'Balíkovna',	65.00,	'Doručení na výdejní místo Balíkovny',	'2025-07-10 21:06:00');

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
(8,	'igor',	'igor',	'rucicka',	'igor@mail.com',	'$2y$10$bEiEpKsd.RXoA7yvEk9QdOZ9LC9zdlX7MpgYnGDJ7S52QVI5U5Flm',	'UZIVATEL',	NULL,	NULL,	NULL,	'2025-06-29 14:10:39',	NULL,	NULL),
(9,	'martin',	'Martin',	'Burda',	'burdadko.cz@gmail.com',	'$2y$10$IL5Fv2uV.ltlOl7k61KAh.BzQnm4tCrgGaCBuPO/qlm36uoHDyItC',	'ADMIN',	'shrekova bazina 13',	'Praha',	'53803',	'2025-07-03 16:48:05',	NULL,	NULL);
