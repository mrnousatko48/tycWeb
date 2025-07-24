DROP TABLE IF EXISTS `banner`;
CREATE TABLE `banner` (
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
(3,	'image',	NULL,	'/www/uploads/home/6873e208e7adb_Obrazek-WhatsApp-2025-07-12-v-07.06.31-ae69212d.webp',	3),
(4,	'button_text',	'Navrhnout kryt',	NULL,	4),
(5,	'button_link',	'configurator',	NULL,	5);

DROP TABLE IF EXISTS `cases`;
CREATE TABLE `cases` (
  `id` int NOT NULL AUTO_INCREMENT,
  `manufacturer` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_price` decimal(10,2) DEFAULT '0.00',
  `features` json DEFAULT NULL,
  `state` enum('KOSIK','OBJEDNANO','ZAPLACENO','ODESLANO','DORUCENO','VYZVEDNUTO') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_upload_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `cases_user_upload_fk` (`user_upload_id`),
  CONSTRAINT `cases_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `cases_user_upload_fk` FOREIGN KEY (`user_upload_id`) REFERENCES `user_uploads` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cases` (`id`, `manufacturer`, `model`, `color`, `total_price`, `features`, `state`, `user_id`, `created_at`, `user_upload_id`) VALUES
(45,	'Apple',	'iPhone 13',	'Bílá',	634.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"2 Sloty\\\",\\\"clona_přední_kamery\\\":\\\"Ano\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\"}\"}',	'OBJEDNANO',	10,	'2025-07-20 15:29:28',	NULL),
(46,	'Apple',	'iPhone 13',	'Černá',	634.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"2 Sloty\\\",\\\"clona_přední_kamery\\\":\\\"Ano\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\"}\"}',	'OBJEDNANO',	10,	'2025-07-20 15:49:28',	NULL),
(47,	'Apple',	'iPhone 13',	'Bílá',	634.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"2 Sloty\\\",\\\"clona_přední_kamery\\\":\\\"Ano\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\"}\"}',	'OBJEDNANO',	10,	'2025-07-20 16:01:57',	NULL),
(48,	'Apple',	'iPhone 13',	'Bílá',	634.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"2 Sloty\\\",\\\"clona_přední_kamery\\\":\\\"Ano\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\"}\"}',	'ZAPLACENO',	10,	'2025-07-20 16:07:59',	NULL),
(49,	'Apple',	'iPhone 13',	'Bílá',	634.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"2 Sloty\\\",\\\"clona_přední_kamery\\\":\\\"Ano\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\"}\"}',	'KOSIK',	NULL,	'2025-07-21 09:03:46',	NULL),
(50,	'Apple',	'iPhone 13',	'Černá',	634.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"2 Sloty\\\",\\\"clona_přední_kamery\\\":\\\"Ano\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\"}\"}',	'KOSIK',	NULL,	'2025-07-21 12:09:58',	NULL),
(51,	'Apple',	'iPhone 13',	'Bílá',	634.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"2 Sloty\\\",\\\"clona_přední_kamery\\\":\\\"Ano\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\"}\"}',	'KOSIK',	NULL,	'2025-07-21 13:21:34',	NULL),
(52,	'Apple',	'iPhone 13',	'Bílá',	634.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"2 Sloty\\\",\\\"clona_přední_kamery\\\":\\\"Ano\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\"}\"}',	'KOSIK',	NULL,	'2025-07-21 15:05:14',	NULL),
(53,	'Apple',	'iPhone 13',	'Bílá',	634.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"2 Sloty\\\",\\\"clona_přední_kamery\\\":\\\"Ano\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\"}\"}',	'OBJEDNANO',	9,	'2025-07-21 15:41:19',	NULL),
(54,	'Apple',	'iPhone 13',	'Bílá',	634.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"2 Sloty\\\",\\\"clona_přední_kamery\\\":\\\"Ano\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\"}\"}',	'OBJEDNANO',	9,	'2025-07-21 15:48:49',	NULL),
(55,	'Apple',	'iPhone 13',	'Bílá',	634.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"2 Sloty\\\",\\\"clona_přední_kamery\\\":\\\"Ano\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\"}\"}',	'OBJEDNANO',	9,	'2025-07-21 15:52:13',	NULL),
(56,	'Apple',	'iPhone 13',	'Bílá',	634.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"2 Sloty\\\",\\\"clona_přední_kamery\\\":\\\"Ano\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\"}\"}',	'OBJEDNANO',	9,	'2025-07-21 16:24:03',	NULL),
(57,	'Apple',	'iPhone 13',	'Bílá',	634.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"2 Sloty\\\",\\\"clona_přední_kamery\\\":\\\"Ano\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\"}\"}',	'OBJEDNANO',	9,	'2025-07-21 16:44:30',	NULL),
(58,	'Apple',	'iPhone 13',	'Bílá',	589.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"Žádný\\\",\\\"clona_přední_kamery\\\":\\\"Ano\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\"}\"}',	'OBJEDNANO',	9,	'2025-07-21 16:46:32',	NULL),
(64,	'Apple',	'iPhone 13',	'Bílá',	559.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"Žádný\\\",\\\"clona_přední_kamery\\\":\\\"Ne\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\"}\"}',	'KOSIK',	NULL,	'2025-07-22 08:38:52',	NULL),
(65,	'Samsung',	'Galaxy S22',	'Černá',	500.00,	'{\"features\": \"{\\\"clona_přední_kamery\\\":\\\"Ne\\\",\\\"vlastní_motiv\\\":\\\"Ne\\\"}\"}',	'KOSIK',	NULL,	'2025-07-22 09:08:14',	NULL),
(67,	'Apple',	'iPhone 13',	'Černá',	589.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"Žádný\\\",\\\"clona_přední_kamery\\\":\\\"Ano\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\"}\"}',	'KOSIK',	NULL,	'2025-07-23 15:11:08',	NULL),
(69,	'Apple',	'iPhone 13',	'Bílá',	559.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"Žádný\\\",\\\"clona_přední_kamery\\\":\\\"Ne\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\"}\"}',	'KOSIK',	NULL,	'2025-07-24 12:41:53',	NULL),
(70,	'Apple',	'iPhone 13',	'Bílá',	634.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"2 Sloty\\\",\\\"clona_přední_kamery\\\":\\\"Ano\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\"}\"}',	'KOSIK',	NULL,	'2025-07-24 16:28:15',	NULL),
(71,	'Apple',	'iPhone 13',	'Bílá',	634.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"2 Sloty\\\",\\\"clona_přední_kamery\\\":\\\"Ano\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\"}\"}',	'KOSIK',	NULL,	'2025-07-24 16:34:08',	NULL),
(72,	'Apple',	'iPhone 13',	'Černá',	689.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"2 Sloty\\\",\\\"clona_přední_kamery\\\":\\\"Ne\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\",\\\"vlastní_motiv\\\":\\\"Ano\\\"}\"}',	'OBJEDNANO',	9,	'2025-07-24 16:36:08',	NULL),
(73,	'Apple',	'iPhone 13',	'Černá',	689.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"2 Sloty\\\",\\\"clona_přední_kamery\\\":\\\"Ne\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\",\\\"vlastní_motiv\\\":\\\"Ano\\\"}\"}',	'OBJEDNANO',	9,	'2025-07-24 16:39:34',	NULL),
(74,	'Apple',	'iPhone 13',	'Černá',	689.99,	'{\"features\": \"{\\\"držák_karet\\\":\\\"2 Sloty\\\",\\\"clona_přední_kamery\\\":\\\"Ne\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\",\\\"vlastní_motiv\\\":\\\"Ano\\\"}\"}',	'OBJEDNANO',	9,	'2025-07-24 17:13:56',	NULL);

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
(1,	'Karel Dvořák.',	'shrekova bazina 13',	'12345678',	'123 456 789',	'email@email.cz',	'<iframe src=\"https://www.google.com/maps/embed?pb=...\" width=\"100%\" height=\"300\" style=\"border:0; border-radius:8px;\" allowfullscreen=\"\" loading=\"lazy\"></iframe>');

DROP TABLE IF EXISTS `customization`;
CREATE TABLE `customization` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `ordering` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `customization` (`id`, `title`, `description`, `image_path`, `ordering`) VALUES
(8,	'test',	'dassda',	'/www/uploads/home/6873e436bbb5b_r6bwd7wd.webp',	1);

DROP TABLE IF EXISTS `default_images`;
CREATE TABLE `default_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `image_path` (`image_path`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `default_images` (`id`, `image_path`, `created_at`) VALUES
(6,	'/www/uploads/default/68791276ce1ab_zidle.webp',	'2025-07-17 15:10:47'),
(7,	'/www/uploads/default/6879127c01a36_holka-ridicak.webp',	'2025-07-17 15:10:55');

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
(4,	'image',	NULL,	'/www/uploads/home/6873e220ce872_Yellow-Black-Simple-Creative-Agency-Logo.webp',	4);

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
(21,	'new_order',	'Nová objednávka č. {$order->id}',	'<!DOCTYPE html>\n<html lang=\"cs\">\n<head>\n    <meta charset=\"UTF-8\">\n    <title>Nová objednávka</title>\n</head>\n<body>\n    <h1>Nová objednávka č. {$order->id}</h1>\n    <p>Jméno odběratele: {$recipient}</p>\n    <table style=\"border-collapse: collapse; width: 100%;\">\n        <thead>\n            <tr>\n                <th style=\"border: 1px solid #999; padding: 6px;\">#</th>\n                <th style=\"border: 1px solid #999; padding: 6px;\">ID</th>\n                <th style=\"border: 1px solid #999; padding: 6px;\">Výrobce</th>\n                <th style=\"border: 1px solid #999; padding: 6px;\">Model</th>\n                <th style=\"border: 1px solid #999; padding: 6px;\">Barva</th>\n                <th style=\"border: 1px solid #999; padding: 6px;\">Vlastnosti</th>\n                <th style=\"border: 1px solid #999; padding: 6px;\">Množství</th>\n                <th style=\"border: 1px solid #999; padding: 6px;\">Cena za kus (Kč)</th>\n                <th style=\"border: 1px solid #999; padding: 6px;\">Celkem (Kč)</th>\n            </tr>\n        </thead>\n        <tbody>\n            {foreach $items as $i => $item}\n                <tr>\n                    <td style=\"border: 1px solid #999; padding: 6px;\">{$i + 1}</td>\n                    <td style=\"border: 1px solid #999; padding: 6px;\">{$item->id}</td>\n                    <td style=\"border: 1px solid #999; padding: 6px;\">{$item->manufacturer}</td>\n                    <td style=\"border: 1px solid #999; padding: 6px;\">{$item->model}</td>\n                    <td style=\"border: 1px solid #999; padding: 6px;\">{$item->color}</td>\n                    <td style=\"border: 1px solid #999; padding: 6px;\">\n                        {foreach $item->features as $key => $value}\n                            {$key}: {$value}<br>\n                        {/foreach}\n                    </td>\n                    <td style=\"border: 1px solid #999; padding: 6px;\">{$item->quantity}</td>\n                    <td style=\"border: 1px solid #999; padding: 6px;\">{number_format($item->total_price, 2, \",\", \"\")}</td>\n                    <td style=\"border: 1px solid #999; padding: 6px;\">{number_format($item->total_price * $item->quantity, 2, \",\", \"\")}</td>\n                </tr>\n            {/foreach}\n        </tbody>\n    </table>\n    <p>Mezisoučet položek: {number_format($itemsSubtotal, 2, \",\", \"\")} Kč</p>\n    <p>Doprava ({$vendorName}): {number_format($shippingCost, 2, \",\", \"\")} Kč</p>\n    <p>Platba ({if $order->payment == \"DOBIRKA\"}Dobírka{else}Bankovní převod{/if}): {number_format($paymentCost, 2, \",\", \"\")} Kč</p>\n    <p><strong>Celkem: {number_format($total, 2, \",\", \"\")} Kč</strong></p>\n</body>\n</html>',	'opnx3d@seznam.cz',	NULL,	'2025-07-20 16:06:46',	'[]',	'2025-07-21 16:43:39'),
(22,	'registration',	'Vítejte! Registrace byla úspěšná',	'<!DOCTYPE html>\r\n<html lang=\"cs\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n    <title>Potvrzení registrace</title>\r\n</head>\r\n<body style=\"margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 16px; color: #333333; background-color: #f4f4f4;\">\r\n    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);\">\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #2563EB; border-radius: 8px 8px 0 0;\">\r\n                <h1 style=\"color: #ffffff; margin: 0; font-size: 24px;\">Vítejte, {$username}!</h1>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px;\">\r\n                <p style=\"margin: 0 0 16px;\">Dobrý den,</p>\r\n                <p style=\"margin: 0 0 16px;\">Děkujeme za vaši registraci do systému <strong>3D kryty</strong>.</p>\r\n                <p style=\"margin: 0 0 16px;\"><strong>Email:</strong> {$email}</p>\r\n                <p style=\"margin: 0 0 16px;\">Těšíme se na vaši objednávku! Pokud máte jakékoli dotazy, kontaktujte nás na <a href=\"mailto:opnx3d@seznam.cz\" style=\"color: #2563EB; text-decoration: none;\">opnx3d@seznam.cz</a>.</p>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #f8f8f8; border-radius: 0 0 8px 8px;\">\r\n                <p style=\"margin: 0; font-size: 12px; color: #666666;\">3D kryty | E-mail: opnx3d@seznam.cz | Telefon: +420 123 456 789</p>\r\n            </td>\r\n        </tr>\r\n    </table>\r\n</body>\r\n</html>',	NULL,	NULL,	'2025-07-20 16:12:27',	'[]',	'2025-07-20 16:12:27'),
(23,	'new_user',	'Nová registrace uživatele',	'<!DOCTYPE html>\r\n<html lang=\"cs\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n    <title>Nová registrace</title>\r\n</head>\r\n<body style=\"margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 16px; color: #333333; background-color: #f4f4f4;\">\r\n    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);\">\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #2563EB; border-radius: 8px 8px 0 0;\">\r\n                <h1 style=\"color: #ffffff; margin: 0; font-size: 24px;\">Nová registrace uživatele</h1>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px;\">\r\n                <p style=\"margin: 0 0 16px;\">Dobrý den,</p>\r\n                <p style=\"margin: 0 0 16px;\">Nový uživatel <strong>{$username}</strong> se zaregistroval do systému <strong>3D kryty</strong>.</p>\r\n                <p style=\"margin: 0 0 16px;\"><strong>Email:</strong> {$email}</p>\r\n                <p style=\"margin: 0 0 16px;\">Prosím, zkontrolujte registraci v administraci.</p>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #f8f8f8; border-radius: 0 0 8px 8px;\">\r\n                <p style=\"margin: 0; font-size: 12px; color: #666666;\">3D kryty | E-mail: opnx3d@seznam.cz | Telefon: +420 123 456 789</p>\r\n            </td>\r\n        </tr>\r\n    </table>\r\n</body>\r\n</html>',	'opnx3d@seznam.cz',	NULL,	'2025-07-20 16:12:27',	'[]',	'2025-07-20 16:12:27'),
(24,	'invoice',	'Faktura za vaši objednávku č. {$order->id}',	'<style>\r\n        body {\r\n            font-family: DejaVu Sans, sans-serif;\r\n            font-size: 12px;\r\n            color: #333;\r\n        }\r\n        .header {\r\n            text-align: center;\r\n            margin-bottom: 20px;\r\n        }\r\n        .details, .items, .summary {\r\n            width: 100%;\r\n            margin-bottom: 20px;\r\n            border-collapse: collapse;\r\n        }\r\n        .details td, .summary td {\r\n            padding: 5px;\r\n            vertical-align: top;\r\n        }\r\n        .items th, .items td {\r\n            border: 1px solid #999;\r\n            padding: 6px;\r\n            text-align: left;\r\n        }\r\n        .items th {\r\n            background-color: #f2f2f2;\r\n        }\r\n        .summary th {\r\n            background-color: #f2f2f2;\r\n            padding: 6px;\r\n            text-align: left;\r\n        }\r\n        h1 {\r\n            font-size: 20px;\r\n            margin-bottom: 5px;\r\n        }\r\n        hr {\r\n            margin: 20px 0;\r\n        }\r\n        .payment-details {\r\n            margin-top: 20px;\r\n        }\r\n    </style>\r\n    <div class=\"header\">\r\n        <h1>Faktura č. {$order->id}</h1>\r\n        <p>Datum vystavení: {$order->created_at|date:\"d.m.Y\"}</p>\r\n    </div>\r\n    <table class=\"details\">\r\n        <tr>\r\n            <td><strong>Odběratel:</strong><br>{$recipient}</td>\r\n            <td>\r\n                <strong>Adresa:</strong><br>\r\n                {$order->address}<br>\r\n                {$order->city}, {$order->psc}\r\n            </td>\r\n            <td>\r\n                <strong>Kontaktní údaje:</strong><br>\r\n                E-mail: {$order->email}<br>\r\n                Telefon: {$order->phone}\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td><strong>Způsob dopravy:</strong><br>{$vendorName}</td>\r\n            <td><strong>Místo doručení:</strong><br>{$order->delivery_point ?: \"Není zadáno\"}</td>\r\n            <td><strong>Způsob platby:</strong><br>{if $order->payment == \"DOBIRKA\"}Dobírka{else}Bankovní převod{/if}</td>\r\n        </tr>\r\n    </table>\r\n    <hr>\r\n    <table class=\"items\">\r\n        <thead>\r\n            <tr>\r\n                <th>#</th>\r\n                <th>ID</th>\r\n                <th>Výrobce</th>\r\n                <th>Model</th>\r\n                <th>Barva</th>\r\n                <th>Vlastnosti</th>\r\n                <th>Množství</th>\r\n                <th>Cena za kus (Kč)</th>\r\n                <th>Celkem (Kč)</th>\r\n            </tr>\r\n        </thead>\r\n        <tbody>\r\n            {foreach $items as $i => $item}\r\n                <tr>\r\n                    <td>{$i + 1}</td>\r\n                    <td>{$item->id}</td>\n                    <td>{$item->manufacturer}</td>\n                    <td>{$item->model}</td>\n                    <td>{$item->color}</td>\n                    <td>\n                        {foreach $item->features as $key => $value}\n                            {$key}: {$value}<br>\n                        {/foreach}\n                    </td>\n                    <td>{$item->quantity}</td>\n                    <td>{number_format($item->total_price, 2, \",\", \"\")}</td>\n                    <td>{number_format($item->total_price * $item->quantity, 2, \",\", \"\")}</td>\n                </tr>\n            {/foreach}\n        </tbody>\n    </table>\n    <table class=\"summary\">\n        <thead>\n            <tr>\n                <th colspan=\"2\">Shrnutí nákladů</th>\n            </tr>\n        </thead>\n        <tbody>\n            <tr>\n                <td>Mezisoučet položek:</td>\n                <td>{number_format($itemsSubtotal, 2, \",\", \"\")} Kč</td>\n            </tr>\n            <tr>\n                <td>Doprava ({$vendorName}):</td>\n                <td>{number_format($shippingCost, 2, \",\", \"\")} Kč</td>\n            </tr>\n            <tr>\n                <td>Platba ({if $order->payment == \"DOBIRKA\"}Dobírka{else}Bankovní převod{/if}):</td>\n                <td>{number_format($paymentCost, 2, \",\", \"\")} Kč</td>\n            </tr>\n            <tr>\n                <td><strong>Celkem:</strong></td>\n                <td><strong>{number_format($total, 2, \",\", \"\")} Kč</strong></td>\n            </tr>\n        </tbody>\n    </table>\n    <div class=\"payment-details\">\n        <h2>Platební údaje</h2>\n        {if $order->payment == \"PREVOD\"}\n            <p>\n                <strong>Bankovní účet:</strong> 111222333<br>\n                <strong>Variabilní symbol:</strong> {$order->variable_symbol}<br>\n                <strong>Částka:</strong> {number_format($total, 2, \",\", \"\")} Kč<br>\n            </p>\n        {else}\n            <p>\n                <strong>Platba při převzetí:</strong><br>\n                Částka: {number_format($total, 2, \",\", \"\")} Kč<br>\n                Prosím, připravte si uvedenou částku v hotovosti při převzetí zásilky.\n            </p>\n        {/if}\n    </div>',	NULL,	NULL,	'2025-07-20 16:12:27',	'[]',	'2025-07-21 16:43:39'),
(25,	'invoice_email',	'Faktura za vaši objednávku č. {$orderId}',	'<!DOCTYPE html>\r\n<html lang=\"cs\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <title>Faktura</title>\r\n</head>\r\n<body>\r\n    <h1>Dobrý den, {$recipient}</h1>\r\n    <p>V příloze naleznete fakturu za vaši objednávku č. {$orderId}.</p>\r\n    <p>Děkujeme za váš nákup!</p>\r\n</body>\r\n</html>',	NULL,	NULL,	'2025-07-20 16:12:27',	'[]',	'2025-07-20 16:12:27'),
(26,	'password_reset',	'Reset hesla',	'<!DOCTYPE html>\r\n<html lang=\"cs\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n    <title>Reset hesla</title>\r\n</head>\r\n<body style=\"margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 16px; color: #333333; background-color: #f4f4f4;\">\r\n    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);\">\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #2563EB; border-radius: 8px 8px 0 0;\">\r\n                <h1 style=\"color: #ffffff; margin: 0; font-size: 24px;\">Reset hesla</h1>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px;\">\r\n                <p style=\"margin: 0 0 16px;\">Dobrý den,</p>\r\n                <p style=\"margin: 0 0 16px;\">Pro resetování vašeho hesla použijte následující kód:</p>\r\n                <p style=\"margin: 0 0 16px; font-size: 18px; font-weight: bold; color: #2563EB;\">{$resetCode}</p>\r\n                <p style=\"margin: 0 0 16px;\">Zadejte tento kód na stránce pro obnovu hesla. Pokud jste reset nevyžádali, ignorujte tento e-mail.</p>\r\n                <p style=\"margin: 0 0 16px;\">Pokud potřebujete pomoc, kontaktujte nás na <a href=\"mailto:opnx3d@seznam.cz\" style=\"color: #2563EB; text-decoration: none;\">opnx3d@seznam.cz</a>.</p>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #f8f8f8; border-radius: 0 0 8px 8px;\">\r\n                <p style=\"margin: 0; font-size: 12px; color: #666666;\">3D kryty | E-mail: opnx3d@seznam.cz | Telefon: +420 123 456 789</p>\r\n            </td>\r\n        </tr>\r\n    </table>\r\n</body>\r\n</html>',	NULL,	NULL,	'2025-07-20 16:12:27',	'[]',	'2025-07-20 16:12:27'),
(27,	'payment_confirmation',	'Potvrzení přijetí platby za objednávku č. {$orderId}',	'<!DOCTYPE html>\r\n<html lang=\"cs\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n    <title>Potvrzení platby</title>\r\n</head>\r\n<body style=\"margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 16px; color: #333333; background-color: #f4f4f4;\">\r\n    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);\">\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #2563EB; border-radius: 8px 8px 0 0;\">\r\n                <h1 style=\"color: #ffffff; margin: 0; font-size: 24px;\">Potvrzení platby</h1>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px;\">\r\n                <p style=\"margin: 0 0 16px;\">Dobrý den, {$recipient},</p>\r\n                <p style=\"margin: 0 0 16px;\">Potvrzujeme přijetí platby za objednávku č. <strong>{$orderId}</strong>.</p>\r\n                <p style=\"margin: 0 0 16px;\">Na expedici vaší zásilky již pracujeme. O dalším pohybu vás budeme informovat.</p>\r\n                <p style=\"margin: 0 0 16px;\">Děkujeme za váš nákup! Pokud máte dotazy, kontaktujte nás na <a href=\"mailto:opnx3d@seznam.cz\" style=\"color: #2563EB; text-decoration: none;\">opnx3d@seznam.cz</a>.</p>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #f8f8f8; border-radius: 0 0 8px 8px;\">\r\n                <p style=\"margin: 0; font-size: 12px; color: #666666;\">3D kryty | E-mail: opnx3d@seznam.cz | Telefon: +420 123 456 789</p>\r\n            </td>\r\n        </tr>\r\n    </table>\r\n</body>\r\n</html>',	NULL,	NULL,	'2025-07-20 16:12:27',	'[]',	'2025-07-20 16:12:27'),
(28,	'shipped',	'Vaše objednávka č. {$orderId} byla odeslána',	'<!DOCTYPE html>\r\n<html lang=\"cs\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n    <title>Objednávka odeslána</title>\r\n</head>\r\n<body style=\"margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 16px; color: #333333; background-color: #f4f4f4;\">\r\n    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);\">\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #2563EB; border-radius: 8px 8px 0 0;\">\r\n                <h1 style=\"color: #ffffff; margin: 0; font-size: 24px;\">Objednávka odeslána</h1>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px;\">\r\n                <p style=\"margin: 0 0 16px;\">Dobrý den, {$recipient},</p>\r\n                <p style=\"margin: 0 0 16px;\">Vaše objednávka č. <strong>{$orderId}</strong> byla odeslána.</p>\r\n                <p style=\"margin: 0 0 16px;\">Brzy dorazí na vámi zvolené místo doručení. Děkujeme za váš nákup!</p>\r\n                <p style=\"margin: 0 0 16px;\">Pokud máte dotazy, kontaktujte nás na <a href=\"mailto:opnx3d@seznam.cz\" style=\"color: #2563EB; text-decoration: none;\">opnx3d@seznam.cz</a>.</p>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #f8f8f8; border-radius: 0 0 8px 8px;\">\r\n                <p style=\"margin: 0; font-size: 12px; color: #666666;\">3D kryty | E-mail: opnx3d@seznam.cz | Telefon: +420 123 456 789</p>\r\n            </td>\r\n        </tr>\r\n    </table>\r\n</body>\r\n</html>',	NULL,	NULL,	'2025-07-20 16:12:27',	'[]',	'2025-07-20 16:12:27'),
(29,	'ready_for_pickup',	'Vaše objednávka č. {$orderId} je připravena k vyzvednutí',	'<!DOCTYPE html>\r\n<html lang=\"cs\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n    <title>Objednávka připravena k vyzvednutí</title>\r\n</head>\r\n<body style=\"margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 16px; color: #333333; background-color: #f4f4f4;\">\r\n    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);\">\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #2563EB; border-radius: 8px 8px 0 0;\">\r\n                <h1 style=\"color: #ffffff; margin: 0; font-size: 24px;\">Objednávka připravena k vyzvednutí</h1>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px;\">\r\n                <p style=\"margin: 0 0 16px;\">Dobrý den, {$recipient},</p>\r\n                <p style=\"margin: 0 0 16px;\">Vaše objednávka č. <strong>{$orderId}</strong> je připravena k vyzvednutí na místě:</p>\r\n                <p style=\"margin: 0 0 16px; font-size: 18px; font-weight: bold; color: #2563EB;\">{$deliveryPoint}</p>\r\n                <p style=\"margin: 0 0 16px;\">Prosím, vyzvedněte si ji co nejdříve. Děkujeme za váš nákup!</p>\r\n                <p style=\"margin: 0 0 16px;\">Pokud máte dotazy, kontaktujte nás na <a href=\"mailto:opnx3d@seznam.cz\" style=\"color: #2563EB; text-decoration: none;\">opnx3d@seznam.cz</a>.</p>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #f8f8f8; border-radius: 0 0 8px 8px;\">\r\n                <p style=\"margin: 0; font-size: 12px; color: #666666;\">3D kryty | E-mail: opnx3d@seznam.cz | Telefon: +420 123 456 789</p>\r\n            </td>\r\n        </tr>\r\n    </table>\r\n</body>\r\n</html>',	NULL,	NULL,	'2025-07-20 16:12:27',	'[]',	'2025-07-20 16:12:27'),
(30,	'picked_up',	'Vaše objednávka č. {$orderId} byla vyzvednuta',	'<!DOCTYPE html>\r\n<html lang=\"cs\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n    <title>Objednávka vyzvednuta</title>\r\n</head>\r\n<body style=\"margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 16px; color: #333333; background-color: #f4f4f4;\">\r\n    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);\">\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #2563EB; border-radius: 8px 8px 0 0;\">\r\n                <h1 style=\"color: #ffffff; margin: 0; font-size: 24px;\">Objednávka vyzvednuta</h1>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px;\">\r\n                <p style=\"margin: 0 0 16px;\">Dobrý den, {$recipient},</p>\r\n                <p style=\"margin: 0 0 16px;\">Vaše objednávka č. <strong>{$orderId}</strong> byla úspěšně vyzvednuta.</p>\r\n                <p style=\"margin: 0 0 16px;\">Děkujeme za váš nákup a těšíme se na váš další nákup!</p>\r\n                <p style=\"margin: 0 0 16px;\">Pokud máte dotazy, kontaktujte nás na <a href=\"mailto:opnx3d@seznam.cz\" style=\"color: #2563EB; text-decoration: none;\">opnx3d@seznam.cz</a>.</p>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #f8f8f8; border-radius: 0 0 8px 8px;\">\r\n                <p style=\"margin: 0; font-size: 12px; color: #666666;\">3D kryty | E-mail: opnx3d@seznam.cz | Telefon: +420 123 456 789</p>\r\n            </td>\r\n        </tr>\r\n    </table>\r\n</body>\r\n</html>',	NULL,	NULL,	'2025-07-20 16:12:27',	'[]',	'2025-07-20 16:12:27');

DROP TABLE IF EXISTS `feature_options`;
CREATE TABLE `feature_options` (
  `id` int NOT NULL AUTO_INCREMENT,
  `feature_id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `price` decimal(10,2) DEFAULT '0.00',
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `allow_user_upload` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_feature_option` (`feature_id`,`name`),
  KEY `feature_id` (`feature_id`),
  CONSTRAINT `feature_options_ibfk_1` FOREIGN KEY (`feature_id`) REFERENCES `features` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `feature_options` (`id`, `feature_id`, `name`, `price`, `image_path`, `created_at`, `allow_user_upload`) VALUES
(16,	16,	'Ano',	30.00,	'/uploads/features/camera_cover_yes.jpg',	'2025-07-09 19:46:47',	0),
(18,	16,	'Ne',	0.00,	'/uploads/features/camera_cover_no.jpg',	'2025-07-09 19:46:50',	0),
(24,	12,	'2 Sloty',	45.00,	'/uploads/features/card_holder_2_slots.jpg',	'2025-07-09 19:47:06',	0),
(25,	12,	'Žádný',	0.00,	'/uploads/features/no_card_holder.jpg',	'2025-07-09 19:47:37',	0),
(30,	18,	'Ano',	30.00,	'/uploads/features/charging_port_cover_yes.jpg',	'2025-07-09 20:05:52',	0),
(31,	18,	'Ne',	0.00,	'/uploads/features/charging_port_cover_no.jpg',	'2025-07-09 20:05:58',	0),
(38,	22,	'Ano',	85.00,	NULL,	'2025-07-15 09:44:06',	1),
(39,	22,	'Ne',	0.00,	NULL,	'2025-07-15 09:44:11',	0);

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
(22,	'Vlastní motiv',	'2025-07-15 09:43:43');

DROP TABLE IF EXISTS `gallery`;
CREATE TABLE `gallery` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `ordering` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `gallery` (`id`, `image`, `alt_text`, `ordering`) VALUES
(15,	'/www/uploads/gallery/687945b800dec_holka-ridicak.webp',	'Autoškola Prima',	4),
(16,	'/www/uploads/gallery/687945c42eeae_zidle.webp',	'adsads',	3),
(17,	'/www/uploads/gallery/687945cd88555_kluk-s-autem.webp',	'asdasdfg',	2),
(18,	'/www/uploads/gallery/687945d87ac49_ucebna.webp',	'gfsdf',	1);

DROP TABLE IF EXISTS `legal_pages`;
CREATE TABLE `legal_pages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `section_name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `section_name` (`section_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `legal_pages` (`id`, `section_name`, `title`, `content`, `updated_at`) VALUES
(1,	'obchodni-podminky',	'Obchodní podmínky',	'<h2>Obchodní podmínky</h2><p>Toto jsou testovací obchodní podmínky pro OPNX3D. Všechny nákupy podléhají těmto pravidlům. Prosím, přečtěte si pečlivě.</p><p>1. <strong>Předmět smlouvy</strong>: Prodáváme obaly na telefony dle specifikací zákazníka.</p><p>2. <strong>Ceny</strong>: Všechny ceny jsou uvedeny v Kč včetně DPH.</p>',	'2025-07-17 09:46:17'),
(2,	'ochrana-osobnich-udaju',	'Ochrana osobních údajů',	'<h2>Ochrana osobních údajů</h2><p>OPNX3D chrání vaše osobní údaje dle GDPR. Zde je testovací obsah.</p><p>1. <strong>Shromažďování údajů</strong>: Shromažďujeme pouze nezbytné údaje pro zpracování objednávek.</p><p>2. <strong>Použití údajů</strong>: Údaje jsou použity výhradně pro účely doručení a komunikace.</p>',	'2025-07-17 09:08:46'),
(3,	'reklamacni-rad',	'Reklamační řád',	'<h2>Reklamační řád</h2><p>Tento testovací reklamační řád popisuje postup při reklamaci zboží.</p><p>1. <strong>Lhůta pro reklamaci</strong>: Zboží lze reklamovat do 24 měsíců od nákupu.</p><p>2. <strong>Postup</strong>: Kontaktujte nás na opnx3d@gmail.com.</p>',	'2025-07-17 09:08:46'),
(4,	'odstoupeni-od-smlouvy',	'Odstoupení od smlouvy',	'<h2>Odstoupení od smlouvy</h2><p>Testovací obsah pro odstoupení od smlouvy.</p><p>1. <strong>Lhůta</strong>: Od smlouvy lze odstoupit do 14 dnů bez udání důvodu.</p><p>2. <strong>Vrácení zboží</strong>: Zboží musí být vráceno nepoškozené na naši adresu.</p>',	'2025-07-17 09:08:46');

DROP TABLE IF EXISTS `logos`;
CREATE TABLE `logos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `theme` varchar(10) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `logos` (`id`, `theme`, `image_path`) VALUES
(1,	'light',	'/www/uploads/logo/6873f439c6b5a_logoLight.webp'),
(2,	'dark',	'/www/uploads/logo/logoDark.png');

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
(2,	'Samsung',	'2025-07-03 16:52:35');

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
(1,	22,	39),
(3,	22,	39);

DROP TABLE IF EXISTS `model_images`;
CREATE TABLE `model_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `model_id` int NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_model_id` (`model_id`),
  CONSTRAINT `model_images_ibfk_1` FOREIGN KEY (`model_id`) REFERENCES `models` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `model_images` (`id`, `model_id`, `image_path`, `created_at`) VALUES
(16,	3,	'/www/uploads/models/3/6875719a67e48_logoLight.webp',	'2025-07-14 21:07:38');

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
(4,	2,	'Galaxy S23',	'2025-07-03 16:52:35',	510.00);

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
(59,	73,	1),
(59,	72,	1),
(60,	74,	1);

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
  `payment` int NOT NULL,
  `shipping` int NOT NULL,
  `delivery_point` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `additional_cost` decimal(10,2) DEFAULT '0.00',
  `state` enum('KOSIK','OBJEDNANO','ZAPLACENO','ODESLANO','DORUCENO','VYZVEDNUTO') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `variable_symbol` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `variable_symbol` (`variable_symbol`),
  KEY `user_id` (`user_id`),
  KEY `orders_shipping_fk` (`shipping`),
  KEY `orders_payment_fk` (`payment`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_payment_fk` FOREIGN KEY (`payment`) REFERENCES `vendor_payment_methods` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `orders_shipping_fk` FOREIGN KEY (`shipping`) REFERENCES `shipping_options` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `orders` (`id`, `user_id`, `firstname`, `lastname`, `email`, `phone`, `address`, `city`, `psc`, `payment`, `shipping`, `delivery_point`, `additional_cost`, `state`, `created_at`, `variable_symbol`) VALUES
(59,	9,	'Martin',	'Burda',	'burdadko.cz@gmail.com',	'111222333',	'shrekova bazina 13',	'Praha',	'53803',	7,	11,	'sokolská 799 hermanuv mestec',	91.00,	'OBJEDNANO',	'2025-07-24 16:39:56',	'202507247157'),
(60,	9,	'Martin',	'Burda',	'burdadko.cz@gmail.com',	'111222333',	'shrekova bazina 13',	'Praha',	'53803',	6,	11,	'sokolská 799 hermanuv mestec',	79.00,	'OBJEDNANO',	'2025-07-24 17:14:25',	'202507249841');

DROP TABLE IF EXISTS `shipping_options`;
CREATE TABLE `shipping_options` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vendor_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `vendor_id` (`vendor_id`),
  CONSTRAINT `shipping_options_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `shipping_options` (`id`, `vendor_id`, `name`, `cost`) VALUES
(9,	7,	'Na pobočku',	89.00),
(10,	7,	'Na adresu',	110.00),
(11,	6,	'Na pobočku',	79.00),
(12,	6,	'Na adresu',	100.00);

DROP TABLE IF EXISTS `shipping_payment_methods`;
CREATE TABLE `shipping_payment_methods` (
  `id` int NOT NULL AUTO_INCREMENT,
  `shipping_option_id` int NOT NULL,
  `payment_method_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `shipping_option_id` (`shipping_option_id`),
  KEY `payment_method_id` (`payment_method_id`),
  CONSTRAINT `shipping_payment_methods_ibfk_1` FOREIGN KEY (`shipping_option_id`) REFERENCES `shipping_options` (`id`) ON DELETE CASCADE,
  CONSTRAINT `shipping_payment_methods_ibfk_2` FOREIGN KEY (`payment_method_id`) REFERENCES `vendor_payment_methods` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `shipping_payment_methods` (`id`, `shipping_option_id`, `payment_method_id`) VALUES
(8,	9,	5),
(9,	10,	5),
(10,	11,	6),
(11,	12,	6),
(12,	9,	4),
(13,	10,	4),
(14,	11,	7),
(15,	12,	7);

DROP TABLE IF EXISTS `user_uploads`;
CREATE TABLE `user_uploads` (
  `id` int NOT NULL AUTO_INCREMENT,
  `model_id` int NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  CONSTRAINT `user_uploads_ibfk_1` FOREIGN KEY (`model_id`) REFERENCES `models` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `user_uploads` (`id`, `model_id`, `file_path`, `original_filename`, `created_at`) VALUES
(1,	1,	'/www/uploads/user_uploads/1/688269a7873d3.gltf',	'Xi-Redmi-No-13-s-redukci.gltf',	'2025-07-24 17:13:11');

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
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

INSERT INTO `users` (`id`, `username`, `firstname`, `lastname`, `email`, `password`, `role`, `address`, `city`, `psc`, `created_at`, `reset_code`, `reset_code_expires`, `phone`) VALUES
(2,	'sima',	'simon',	'látsod',	'mail@amail.com',	'$2y$10$RD/AXZzpkhbFElMayEVHse83fvmLFMHVFkPyuvCfDkZucTEFoZ6UC',	'UZIVATEL',	'Pražská 281',	'Kolín',	NULL,	'2025-06-24 11:30:58',	NULL,	NULL,	NULL),
(3,	'admin',	'administrátor',	'veliký',	'admin@mail.com',	'$2y$10$5dUIUJioPW1aleFtwM.PiOIvdYUxIVq85Di4oDuOUzAgABF402auW',	'ADMIN',	'Nádražní 23',	'Praha',	'110 03',	'2025-06-24 11:43:46',	NULL,	NULL,	NULL),
(6,	'dostals',	'',	'',	'dostals64@gmail.com',	'$2y$10$RJhN6zE1eZqpGLSBTyFI/OApvmXrcaobEVeOjzfvYmLePRuqrMBRG',	'ADMIN',	NULL,	NULL,	NULL,	'2025-06-24 19:27:29',	241712,	'2025-06-30 21:04:00',	NULL),
(7,	'bakub',	'Kuba',	'Syč',	'bakua@mail.com',	'$2y$10$ZVF9RfycPsVhpryvQf50zePtoXVFCl4.6bUzZKxiSIdpdCguW4Eri',	'UZIVATEL',	NULL,	NULL,	NULL,	'2025-06-24 19:29:28',	NULL,	NULL,	NULL),
(8,	'igor',	'igor',	'rucicka',	'igor@mail.com',	'$2y$10$bEiEpKsd.RXoA7yvEk9QdOZ9LC9zdlX7MpgYnGDJ7S52QVI5U5Flm',	'UZIVATEL',	NULL,	NULL,	NULL,	'2025-06-29 14:10:39',	NULL,	NULL,	NULL),
(9,	'martin',	'Martin',	'Burda',	'burdadko.cz@gmail.com',	'$2y$10$GWyEYwqs8lnAKXVZaQNLpuYPZXCFmFsTNPNe2YWkhogfgg5NNBLUK',	'ADMIN',	'shrekova bazina 13',	'Praha',	'53803',	'2025-07-03 16:48:05',	NULL,	NULL,	'111222333'),
(10,	'testdb',	'Sima',	'Jegej',	'xmanmartinburda@seznam.cz',	'$2y$10$D8K7ICCAGIu6oJn8qihiWOpyJHj/gK4a4Xnj.hP/2UFTgLHTXXoD.',	'UZIVATEL',	NULL,	NULL,	NULL,	'2025-07-20 15:23:34',	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `vendor_payment_methods`;
CREATE TABLE `vendor_payment_methods` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vendor_id` int NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `vendor_id` (`vendor_id`),
  CONSTRAINT `vendor_payment_methods_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `vendor_payment_methods` (`id`, `vendor_id`, `code`, `name`, `price`) VALUES
(4,	7,	'prevod',	'převod',	0.00),
(5,	7,	'dobirka',	'dobírka',	23.00),
(6,	6,	'prevod',	'převod',	0.00),
(7,	6,	'dobirka',	'dobírka',	12.00);

DROP TABLE IF EXISTS `vendors`;
CREATE TABLE `vendors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `vendors` (`id`, `name`) VALUES
(6,	'Balíkovna'),
(7,	'Zásilkovna');