CREATE TABLE `banner` (
  `id` int NOT NULL AUTO_INCREMENT,
  `content_type` enum('title','description','image','button_text','button_link') NOT NULL,
  `content_text` text,
  `content_text_en` text,
  `image_path` varchar(255) DEFAULT NULL,
  `ordering` int DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `content_type_ordering` (`content_type`,`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `banner` (`id`, `content_type`, `content_text`, `content_text_en`, `image_path`, `ordering`) VALUES
(1,	'title',	'Odolné 3D tisknuté kryty',	'Durable 3D Printed Cases.',	NULL,	1),
(2,	'description',	'Vytvořte si pevný a stylový kryt s pokročilou 3D tiskovou technologií a vlastním designem.',	'Create a sturdy and stylish case with advanced 3D printing technology and your own design.',	NULL,	2),
(3,	'image',	NULL,	NULL,	'/www/uploads/home/6873e208e7adb_Obrazek-WhatsApp-2025-07-12-v-07.06.31-ae69212d.webp',	3),
(4,	'button_text',	'Navrhnout kryt',	'Design Your Case',	NULL,	4),
(5,	'button_link',	'configurator',	'co',	NULL,	5);

DROP TABLE IF EXISTS `cases`;
CREATE TABLE `cases` (
  `id` int NOT NULL AUTO_INCREMENT,
  `manufacturer` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_price` decimal(10,2) DEFAULT '0.00',
  `total_price_eur` decimal(10,2) DEFAULT '0.00' COMMENT 'Total price of the case in EUR',
  `features` json DEFAULT NULL,
  `state` enum('KOSIK','OBJEDNANO','ZAPLACENO','ODESLANO','DORUCENO','VYZVEDNUTO') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_upload_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `cases_user_uploads_ibfk` (`user_upload_id`),
  CONSTRAINT `cases_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cases` (`id`, `manufacturer`, `model`, `color`, `total_price`, `total_price_eur`, `features`, `state`, `user_id`, `created_at`, `user_upload_id`) VALUES
(150,	'Apple',	'iPhone 13',	'Black',	22.40,	0.00,	'{\"features\": \"{\\\"card_holder\\\":\\\"None\\\",\\\"front_camera_cover\\\":\\\"No\\\",\\\"charging_port_cover\\\":\\\"Yes\\\",\\\"custom_design\\\":\\\"No\\\"}\"}',	'KOSIK',	NULL,	'2025-08-10 11:43:33',	NULL),
(151,	'Apple',	'iPhone 13',	'Černá',	529.99,	0.00,	'{\"features\": \"{\\\"držák_karet\\\":\\\"Žádný\\\",\\\"clona_přední_kamery\\\":\\\"Ne\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ne\\\",\\\"vlastní_motiv\\\":\\\"Ne\\\"}\"}',	'KOSIK',	NULL,	'2025-08-10 12:28:06',	NULL),
(152,	'Apple',	'iPhone 13',	'Black',	529.99,	21.20,	'{\"features\": \"{\\\"card_holder\\\":\\\"None\\\",\\\"front_camera_cover\\\":\\\"No\\\",\\\"charging_port_cover\\\":\\\"No\\\",\\\"custom_design\\\":\\\"No\\\"}\"}',	'KOSIK',	NULL,	'2025-08-10 13:12:56',	NULL),
(153,	'Apple',	'iPhone 13',	'Purple',	529.99,	21.20,	'{\"features\": \"{\\\"card_holder\\\":\\\"None\\\",\\\"front_camera_cover\\\":\\\"No\\\",\\\"charging_port_cover\\\":\\\"No\\\",\\\"custom_design\\\":\\\"No\\\"}\"}',	'KOSIK',	NULL,	'2025-08-10 13:13:28',	NULL),
(154,	'Samsung',	'Galaxy S23',	'Black',	510.00,	20.40,	'{\"features\": \"{\\\"card_holder\\\":\\\"None\\\",\\\"front_camera_cover\\\":\\\"No\\\"}\"}',	'KOSIK',	NULL,	'2025-08-10 14:25:05',	NULL),
(155,	'Apple',	'iPhone 13',	'Black',	529.99,	21.20,	'{\"features\": \"{\\\"card_holder\\\":\\\"None\\\",\\\"front_camera_cover\\\":\\\"No\\\",\\\"charging_port_cover\\\":\\\"No\\\",\\\"custom_design\\\":\\\"No\\\"}\"}',	'OBJEDNANO',	9,	'2025-08-10 15:30:20',	NULL),
(156,	'Apple',	'iPhone 13',	'Black',	529.99,	21.20,	'{\"features\": \"{\\\"card_holder\\\":\\\"None\\\",\\\"front_camera_cover\\\":\\\"No\\\",\\\"charging_port_cover\\\":\\\"No\\\",\\\"custom_design\\\":\\\"No\\\"}\"}',	'OBJEDNANO',	9,	'2025-08-10 15:40:02',	NULL),
(157,	'Apple',	'iPhone 13',	'Černá',	674.99,	27.00,	'{\"features\": \"{\\\"držák_karet\\\":\\\"Žádný\\\",\\\"clona_přední_kamery\\\":\\\"Ne\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ano\\\",\\\"vlastní_motiv\\\":\\\"Ne\\\"}\"}',	'KOSIK',	NULL,	'2025-08-10 16:09:59',	NULL),
(158,	'Samsung',	'Galaxy S23',	'Black',	540.00,	21.60,	'{\"features\": \"{\\\"card_holder\\\":\\\"None\\\",\\\"front_camera_cover\\\":\\\"Yes\\\"}\"}',	'ODESLANO',	9,	'2025-08-10 17:35:55',	NULL),
(159,	'Apple',	'iPhone 13',	'Purple',	674.99,	27.00,	'{\"features\": \"{\\\"card_holder\\\":\\\"None\\\",\\\"front_camera_cover\\\":\\\"No\\\",\\\"charging_port_cover\\\":\\\"Yes\\\",\\\"custom_design\\\":\\\"No\\\"}\"}',	'OBJEDNANO',	9,	'2025-08-10 20:18:00',	NULL),
(164,	'Apple',	'iPhone 13',	'Černá',	529.99,	21.20,	'{\"features\": \"{\\\"držák_karet\\\":\\\"Žádný\\\",\\\"clona_přední_kamery\\\":\\\"Ne\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ne\\\",\\\"vlastní_motiv\\\":\\\"Ne\\\"}\"}',	'KOSIK',	NULL,	'2025-08-11 16:27:06',	NULL),
(168,	'Apple',	'iPhone 13',	'Černá',	529.99,	21.20,	'{\"features\": \"{\\\"držák_karet\\\":\\\"Žádný\\\",\\\"clona_přední_kamery\\\":\\\"Ne\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ne\\\",\\\"vlastní_motiv\\\":\\\"Ne\\\"}\"}',	'KOSIK',	NULL,	'2025-08-11 16:56:38',	NULL),
(169,	'Apple',	'iPhone 13',	'fialova',	529.99,	21.20,	'{\"features\": \"{\\\"držák_karet\\\":\\\"Žádný\\\",\\\"clona_přední_kamery\\\":\\\"Ne\\\",\\\"krytka_nabíjecího_portu\\\":\\\"Ne\\\",\\\"vlastní_motiv\\\":\\\"Ne\\\"}\"}',	'KOSIK',	NULL,	'2025-08-11 16:56:43',	NULL),
(170,	'Apple',	'iPhone 13',	'Černá',	529.99,	21.20,	'{\"features\": \"{\\\"držák_karet\\\":\\\"Žádný\\\",\\\"clona_přední_kamery\\\":\\\"Ne\\\"}\"}',	'OBJEDNANO',	9,	'2025-09-07 10:46:51',	NULL),
(171,	'Apple',	'iPhone 13',	'Černá',	529.99,	21.20,	'{\"features\": \"{\\\"držák_karet\\\":\\\"Žádný\\\",\\\"clona_přední_kamery\\\":\\\"Ne\\\"}\"}',	'KOSIK',	NULL,	'2025-09-10 19:25:40',	NULL);

DROP TABLE IF EXISTS `colors`;
CREATE TABLE `colors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `name_cs` varchar(50) NOT NULL,
  `name_en` varchar(50) NOT NULL,
  `hex_code` varchar(7) DEFAULT NULL COMMENT 'Optional hex color code (e.g., #FF0000 for red)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `colors` (`id`, `name`, `name_cs`, `name_en`, `hex_code`, `created_at`) VALUES
(1,	'black',	'Černá',	'Black',	'#000000',	'2025-07-09 14:37:00'),
(2,	'white',	'Bílá',	'White',	'#FFFFFF',	'2025-07-09 14:37:00'),
(3,	'blue',	'Modrá',	'Blue',	'#0000FF',	'2025-07-09 14:37:00'),
(4,	'red',	'Červená',	'Red',	'#FF0000',	'2025-07-09 14:37:00'),
(5,	'green',	'Zelená',	'Green',	'#00FF00',	'2025-07-09 14:37:00'),
(6,	'orange',	'oranžová',	'Orange',	'#FFAC1C',	'2025-07-09 12:39:45'),
(10,	'purple',	'fialova',	'Purple',	'#bd00ff',	'2025-07-10 15:01:31');

DROP TABLE IF EXISTS `contact_info`;
CREATE TABLE `contact_info` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `address_en` varchar(255) DEFAULT NULL,
  `ico` varchar(50) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `map_embed` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `contact_info` (`id`, `name`, `name_en`, `address`, `address_en`, `ico`, `phone`, `email`, `map_embed`) VALUES
(1,	'Karel Dvořák.',	'Karel Dvořák',	'shrekova bazina 13',	'Shrekova Bazina 13',	'12345678',	'123 456 789',	'email@email.cz',	'<iframe src=\"https://www.google.com/maps/embed?pb=...\" width=\"100%\" height=\"300\" style=\"border:0; border-radius:8px;\" allowfullscreen=\"\" loading=\"lazy\"></iframe>');

DROP TABLE IF EXISTS `customization`;
CREATE TABLE `customization` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `title_en` text,
  `description` text NOT NULL,
  `description_en` text,
  `image_path` varchar(255) NOT NULL,
  `ordering` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `customization` (`id`, `title`, `title_en`, `description`, `description_en`, `image_path`, `ordering`) VALUES
(8,	'test',	'Test',	'XAXAXA',	'Test description',	'/www/uploads/home/6873e436bbb5b_r6bwd7wd.webp',	1),
(9,	'test',	'Enlihss',	'jabaja',	'asdasdasdadsa',	'/www/Uploads/home/6899d48378dd1_ApplicationFrameHost-CO2sH8N1VU.webp',	2);

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
  `content_text_en` text,
  `image_path` varchar(255) DEFAULT NULL,
  `ordering` int DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `content_type_ordering` (`content_type`,`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `durability` (`id`, `content_type`, `content_text`, `content_text_en`, `image_path`, `ordering`) VALUES
(1,	'title',	'Odolnost navržená pro život',	'Durability Designed for Life',	NULL,	1),
(2,	'description1',	'Naše kryty zvládnou pád, prach i dobrodružství díky precizní 3D tiskové technologii',	'Our cases withstand drops, dust, and adventures thanks to precise 3D printing technology.',	NULL,	2),
(3,	'description2',	'Vyrobeny z odolných, ekologických materiálů s perfektním přizpůsobením pro váš telefon.',	'Made from durable, eco-friendly materials with perfect fit for your phone.',	NULL,	3),
(4,	'image',	NULL,	NULL,	'/www/Uploads/home/6899d46517402_msedge-936H8cO1Qo.webp',	4);

DROP TABLE IF EXISTS `email_templates`;
CREATE TABLE `email_templates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `lang` varchar(2) NOT NULL DEFAULT 'cs',
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `recipient_email` varchar(255) DEFAULT NULL,
  `admin_phone` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `pdf_paths` text,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `email_templates` (`id`, `name`, `lang`, `subject`, `body`, `recipient_email`, `admin_phone`, `created_at`, `pdf_paths`, `updated_at`) VALUES
(21,	'new_order',	'cs',	'Nová objednávka č. {$order->id}',	'<!DOCTYPE html>\n<html lang=\"cs\">\n<head>\n    <meta charset=\"UTF-8\">\n    <title>Nová objednávka</title>\n</head>\n<body>\n    <h1>Nová objednávka č. {$order->id}</h1>\n    <p>Jméno odběratele: {$recipient}</p>\n    <table style=\"border-collapse: collapse; width: 100%;\">\n        <thead>\n            <tr>\n                <th style=\"border: 1px solid #999; padding: 6px;\">#</th>\n                <th style=\"border: 1px solid #999; padding: 6px;\">ID</th>\n                <th style=\"border: 1px solid #999; padding: 6px;\">Výrobce</th>\n                <th style=\"border: 1px solid #999; padding: 6px;\">Model</th>\n                <th style=\"border: 1px solid #999; padding: 6px;\">Barva</th>\n                <th style=\"border: 1px solid #999; padding: 6px;\">Vlastnosti</th>\n                <th style=\"border: 1px solid #999; padding: 6px;\">Množství</th>\n                <th style=\"border: 1px solid #999; padding: 6px;\">Cena za kus (Kč)</th>\n                <th style=\"border: 1px solid #999; padding: 6px;\">Celkem (Kč)</th>\n            </tr>\n        </thead>\n        <tbody>\n            {foreach $items as $i => $item}\n                <tr>\n                    <td style=\"border: 1px solid #999; padding: 6px;\">{$i + 1}</td>\n                    <td style=\"border: 1px solid #999; padding: 6px;\">{$item->id}</td>\n                    <td style=\"border: 1px solid #999; padding: 6px;\">{$item->manufacturer}</td>\n                    <td style=\"border: 1px solid #999; padding: 6px;\">{$item->model}</td>\n                    <td style=\"border: 1px solid #999; padding: 6px;\">{$item->color}</td>\n                    <td style=\"border: 1px solid #999; padding: 6px;\">\n                        {foreach $item->features as $key => $value}\n                            {$key}: {$value}<br>\n                        {/foreach}\n                    </td>\n                    <td style=\"border: 1px solid #999; padding: 6px;\">{$item->quantity}</td>\n                    <td style=\"border: 1px solid #999; padding: 6px;\">{number_format($item->total_price, 2, \",\", \"\")}</td>\n                    <td style=\"border: 1px solid #999; padding: 6px;\">{number_format($item->total_price * $item->quantity, 2, \",\", \"\")}</td>\n                </tr>\n            {/foreach}\n        </tbody>\n    </table>\n    <p>Mezisoučet položek: {number_format($itemsSubtotal, 2, \",\", \"\")} Kč</p>\n    <p>Doprava ({$vendorName}): {number_format($shippingCost, 2, \",\", \"\")} Kč</p>\n    <p>Platba ({if $order->payment == \"DOBIRKA\"}Dobírka{else}Bankovní převod{/if}): {number_format($paymentCost, 2, \",\", \"\")} Kč</p>\n    <p><strong>Celkem: {number_format($total, 2, \",\", \"\")} Kč</strong></p>\n</body>\n</html>',	'opnx3d@seznam.cz',	NULL,	'2025-07-20 16:06:46',	'[]',	'2025-07-21 16:43:39'),
(22,	'registration',	'cs',	'Vítejte! Registrace byla úspěšná',	'<!DOCTYPE html>\r\n<html lang=\"cs\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n    <title>Potvrzení registrace</title>\r\n</head>\r\n<body style=\"margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 16px; color: #333333; background-color: #f4f4f4;\">\r\n    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);\">\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #2563EB; border-radius: 8px 8px 0 0;\">\r\n                <h1 style=\"color: #ffffff; margin: 0; font-size: 24px;\">Vítejte, {$username}!</h1>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px;\">\r\n                <p style=\"margin: 0 0 16px;\">Dobrý den,</p>\r\n                <p style=\"margin: 0 0 16px;\">Děkujeme za vaši registraci do systému <strong>3D kryty</strong>.</p>\r\n                <p style=\"margin: 0 0 16px;\"><strong>Email:</strong> {$email}</p>\r\n                <p style=\"margin: 0 0 16px;\">Těšíme se na vaši objednávku! Pokud máte jakékoli dotazy, kontaktujte nás na <a href=\"mailto:opnx3d@seznam.cz\" style=\"color: #2563EB; text-decoration: none;\">opnx3d@seznam.cz</a>.</p>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #f8f8f8; border-radius: 0 0 8px 8px;\">\r\n                <p style=\"margin: 0; font-size: 12px; color: #666666;\">3D kryty | E-mail: opnx3d@seznam.cz | Telefon: +420 123 456 789</p>\r\n            </td>\r\n        </tr>\r\n    </table>\r\n</body>\r\n</html>',	NULL,	NULL,	'2025-07-20 16:12:27',	'[]',	'2025-07-20 16:12:27'),
(23,	'new_user',	'cs',	'Nová registrace uživatele',	'<!DOCTYPE html>\r\n<html lang=\"cs\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n    <title>Nová registrace</title>\r\n</head>\r\n<body style=\"margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 16px; color: #333333; background-color: #f4f4f4;\">\r\n    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);\">\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #2563EB; border-radius: 8px 8px 0 0;\">\r\n                <h1 style=\"color: #ffffff; margin: 0; font-size: 24px;\">Nová registrace uživatele</h1>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px;\">\r\n                <p style=\"margin: 0 0 16px;\">Dobrý den,</p>\r\n                <p style=\"margin: 0 0 16px;\">Nový uživatel <strong>{$username}</strong> se zaregistroval do systému <strong>3D kryty</strong>.</p>\r\n                <p style=\"margin: 0 0 16px;\"><strong>Email:</strong> {$email}</p>\r\n                <p style=\"margin: 0 0 16px;\">Prosím, zkontrolujte registraci v administraci.</p>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #f8f8f8; border-radius: 0 0 8px 8px;\">\r\n                <p style=\"margin: 0; font-size: 12px; color: #666666;\">3D kryty | E-mail: opnx3d@seznam.cz | Telefon: +420 123 456 789</p>\r\n            </td>\r\n        </tr>\r\n    </table>\r\n</body>\r\n</html>',	'opnx3d@seznam.cz',	NULL,	'2025-07-20 16:12:27',	'[]',	'2025-07-20 16:12:27'),
(24,	'invoice',	'cs',	'Faktura za vaši objednávku č. {$order->id}',	'<style>\r\n        body {\r\n            font-family: DejaVu Sans, sans-serif;\r\n            font-size: 12px;\r\n            color: #333;\r\n        }\r\n        .header {\r\n            text-align: center;\r\n            margin-bottom: 20px;\r\n        }\r\n        .details, .items, .summary {\r\n            width: 100%;\r\n            margin-bottom: 20px;\r\n            border-collapse: collapse;\r\n        }\r\n        .details td, .summary td {\r\n            padding: 5px;\r\n            vertical-align: top;\r\n        }\r\n        .items th, .items td {\r\n            border: 1px solid #999;\r\n            padding: 6px;\r\n            text-align: left;\r\n        }\r\n        .items th {\r\n            background-color: #f2f2f2;\r\n        }\r\n        .summary th {\r\n            background-color: #f2f2f2;\r\n            padding: 6px;\r\n            text-align: left;\r\n        }\r\n        h1 {\r\n            font-size: 20px;\r\n            margin-bottom: 5px;\r\n        }\r\n        hr {\r\n            margin: 20px 0;\r\n        }\r\n        .payment-details {\r\n            margin-top: 20px;\r\n        }\r\n    </style>\r\n    <div class=\"header\">\r\n        <h1>Faktura č. {$order->id}</h1>\r\n        <p>Datum vystavení: {$order->created_at|date:\"d.m.Y\"}</p>\r\n    </div>\r\n    <table class=\"details\">\r\n        <tr>\r\n            <td><strong>Odběratel:</strong><br>{$recipient}</td>\r\n            <td>\r\n                <strong>Adresa:</strong><br>\r\n                {$order->address}<br>\r\n                {$order->city}, {$order->psc}\r\n            </td>\r\n            <td>\r\n                <strong>Kontaktní údaje:</strong><br>\r\n                E-mail: {$order->email}<br>\r\n                Telefon: {$order->phone}\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td><strong>Způsob dopravy:</strong><br>{$vendorName}</td>\r\n            <td><strong>Místo doručení:</strong><br>{$order->delivery_point ?: \"Není zadáno\"}</td>\r\n            <td><strong>Způsob platby:</strong><br>{if $order->payment == \"DOBIRKA\"}Dobírka{else}Bankovní převod{/if}</td>\r\n        </tr>\r\n    </table>\r\n    <hr>\r\n    <table class=\"items\">\r\n        <thead>\r\n            <tr>\r\n                <th>#</th>\r\n                <th>ID</th>\r\n                <th>Výrobce</th>\r\n                <th>Model</th>\r\n                <th>Barva</th>\r\n                <th>Vlastnosti</th>\r\n                <th>Množství</th>\r\n                <th>Cena za kus (Kč)</th>\r\n                <th>Celkem (Kč)</th>\r\n            </tr>\r\n        </thead>\r\n        <tbody>\r\n            {foreach $items as $i => $item}\r\n                <tr>\r\n                    <td>{$i + 1}</td>\r\n                    <td>{$item->id}</td>\n                    <td>{$item->manufacturer}</td>\n                    <td>{$item->model}</td>\n                    <td>{$item->color}</td>\n                    <td>\n                        {foreach $item->features as $key => $value}\n                            {$key}: {$value}<br>\n                        {/foreach}\n                    </td>\n                    <td>{$item->quantity}</td>\n                    <td>{number_format($item->total_price, 2, \",\", \"\")}</td>\n                    <td>{number_format($item->total_price * $item->quantity, 2, \",\", \"\")}</td>\n                </tr>\n            {/foreach}\n        </tbody>\n    </table>\n    <table class=\"summary\">\n        <thead>\n            <tr>\n                <th colspan=\"2\">Shrnutí nákladů</th>\n            </tr>\n        </thead>\n        <tbody>\n            <tr>\n                <td>Mezisoučet položek:</td>\n                <td>{number_format($itemsSubtotal, 2, \",\", \"\")} Kč</td>\n            </tr>\n            <tr>\n                <td>Doprava ({$vendorName}):</td>\n                <td>{number_format($shippingCost, 2, \",\", \"\")} Kč</td>\n            </tr>\n            <tr>\n                <td>Platba ({if $order->payment == \"DOBIRKA\"}Dobírka{else}Bankovní převod{/if}):</td>\n                <td>{number_format($paymentCost, 2, \",\", \"\")} Kč</td>\n            </tr>\n            <tr>\n                <td><strong>Celkem:</strong></td>\n                <td><strong>{number_format($total, 2, \",\", \"\")} Kč</strong></td>\n            </tr>\n        </tbody>\n    </table>\n    <div class=\"payment-details\">\n        <h2>Platební údaje</h2>\n        {if $order->payment == \"PREVOD\"}\n            <p>\n                <strong>Bankovní účet:</strong> 111222333<br>\n                <strong>Variabilní symbol:</strong> {$order->variable_symbol}<br>\n                <strong>Částka:</strong> {number_format($total, 2, \",\", \"\")} Kč<br>\n            </p>\n        {else}\n            <p>\n                <strong>Platba při převzetí:</strong><br>\n                Částka: {number_format($total, 2, \",\", \"\")} Kč<br>\n                Prosím, připravte si uvedenou částku v hotovosti při převzetí zásilky.\n            </p>\n        {/if}\n    </div>',	NULL,	NULL,	'2025-07-20 16:12:27',	'[]',	'2025-07-21 16:43:39'),
(25,	'invoice_email',	'cs',	'Faktura za vaši objednávku č. {$orderId}',	'<!DOCTYPE html>\r\n<html lang=\"cs\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <title>Faktura</title>\r\n</head>\r\n<body>\r\n    <h1>Dobrý den, {$recipient}</h1>\r\n    <p>V příloze naleznete fakturu za vaši objednávku č. {$orderId}.</p>\r\n    <p>Děkujeme za váš nákup!</p>\r\n</body>\r\n</html>',	NULL,	NULL,	'2025-07-20 16:12:27',	'[]',	'2025-07-20 16:12:27'),
(26,	'password_reset',	'cs',	'Reset hesla',	'<!DOCTYPE html>\r\n<html lang=\"cs\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n    <title>Reset hesla</title>\r\n</head>\r\n<body style=\"margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 16px; color: #333333; background-color: #f4f4f4;\">\r\n    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);\">\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #2563EB; border-radius: 8px 8px 0 0;\">\r\n                <h1 style=\"color: #ffffff; margin: 0; font-size: 24px;\">Reset hesla</h1>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px;\">\r\n                <p style=\"margin: 0 0 16px;\">Dobrý den,</p>\r\n                <p style=\"margin: 0 0 16px;\">Pro resetování vašeho hesla použijte následující kód:</p>\r\n                <p style=\"margin: 0 0 16px; font-size: 18px; font-weight: bold; color: #2563EB;\">{$resetCode}</p>\r\n                <p style=\"margin: 0 0 16px;\">Zadejte tento kód na stránce pro obnovu hesla. Pokud jste reset nevyžádali, ignorujte tento e-mail.</p>\r\n                <p style=\"margin: 0 0 16px;\">Pokud potřebujete pomoc, kontaktujte nás na <a href=\"mailto:opnx3d@seznam.cz\" style=\"color: #2563EB; text-decoration: none;\">opnx3d@seznam.cz</a>.</p>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #f8f8f8; border-radius: 0 0 8px 8px;\">\r\n                <p style=\"margin: 0; font-size: 12px; color: #666666;\">3D kryty | E-mail: opnx3d@seznam.cz | Telefon: +420 123 456 789</p>\r\n            </td>\r\n        </tr>\r\n    </table>\r\n</body>\r\n</html>',	NULL,	NULL,	'2025-07-20 16:12:27',	'[]',	'2025-07-20 16:12:27'),
(27,	'payment_confirmation',	'cs',	'Potvrzení přijetí platby za objednávku č. {$orderId}',	'<!DOCTYPE html>\r\n<html lang=\"cs\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n    <title>Potvrzení platby</title>\r\n</head>\r\n<body style=\"margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 16px; color: #333333; background-color: #f4f4f4;\">\r\n    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);\">\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #2563EB; border-radius: 8px 8px 0 0;\">\r\n                <h1 style=\"color: #ffffff; margin: 0; font-size: 24px;\">Potvrzení platby</h1>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px;\">\r\n                <p style=\"margin: 0 0 16px;\">Dobrý den, {$recipient},</p>\r\n                <p style=\"margin: 0 0 16px;\">Potvrzujeme přijetí platby za objednávku č. <strong>{$orderId}</strong>.</p>\r\n                <p style=\"margin: 0 0 16px;\">Na expedici vaší zásilky již pracujeme. O dalším pohybu vás budeme informovat.</p>\r\n                <p style=\"margin: 0 0 16px;\">Děkujeme za váš nákup! Pokud máte dotazy, kontaktujte nás na <a href=\"mailto:opnx3d@seznam.cz\" style=\"color: #2563EB; text-decoration: none;\">opnx3d@seznam.cz</a>.</p>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #f8f8f8; border-radius: 0 0 8px 8px;\">\r\n                <p style=\"margin: 0; font-size: 12px; color: #666666;\">3D kryty | E-mail: opnx3d@seznam.cz | Telefon: +420 123 456 789</p>\r\n            </td>\r\n        </tr>\r\n    </table>\r\n</body>\r\n</html>',	NULL,	NULL,	'2025-07-20 16:12:27',	'[]',	'2025-07-20 16:12:27'),
(28,	'shipped',	'cs',	'Vaše objednávka č. {$orderId} byla odeslána',	'<!DOCTYPE html>\r\n<html lang=\"cs\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n    <title>Objednávka odeslána</title>\r\n</head>\r\n<body style=\"margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 16px; color: #333333; background-color: #f4f4f4;\">\r\n    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);\">\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #2563EB; border-radius: 8px 8px 0 0;\">\r\n                <h1 style=\"color: #ffffff; margin: 0; font-size: 24px;\">Objednávka odeslána</h1>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px;\">\r\n                <p style=\"margin: 0 0 16px;\">Dobrý den, {$recipient},</p>\r\n                <p style=\"margin: 0 0 16px;\">Vaše objednávka č. <strong>{$orderId}</strong> byla odeslána.</p>\r\n                <p style=\"margin: 0 0 16px;\">Brzy dorazí na vámi zvolené místo doručení. Děkujeme za váš nákup!</p>\r\n                <p style=\"margin: 0 0 16px;\">Pokud máte dotazy, kontaktujte nás na <a href=\"mailto:opnx3d@seznam.cz\" style=\"color: #2563EB; text-decoration: none;\">opnx3d@seznam.cz</a>.</p>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #f8f8f8; border-radius: 0 0 8px 8px;\">\r\n                <p style=\"margin: 0; font-size: 12px; color: #666666;\">3D kryty | E-mail: opnx3d@seznam.cz | Telefon: +420 123 456 789</p>\r\n            </td>\r\n        </tr>\r\n    </table>\r\n</body>\r\n</html>',	NULL,	NULL,	'2025-07-20 16:12:27',	'[]',	'2025-07-20 16:12:27'),
(29,	'ready_for_pickup',	'cs',	'Vaše objednávka č. {$orderId} je připravena k vyzvednutí',	'<!DOCTYPE html>\r\n<html lang=\"cs\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n    <title>Objednávka připravena k vyzvednutí</title>\r\n</head>\r\n<body style=\"margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 16px; color: #333333; background-color: #f4f4f4;\">\r\n    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);\">\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #2563EB; border-radius: 8px 8px 0 0;\">\r\n                <h1 style=\"color: #ffffff; margin: 0; font-size: 24px;\">Objednávka připravena k vyzvednutí</h1>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px;\">\r\n                <p style=\"margin: 0 0 16px;\">Dobrý den, {$recipient},</p>\r\n                <p style=\"margin: 0 0 16px;\">Vaše objednávka č. <strong>{$orderId}</strong> je připravena k vyzvednutí na místě:</p>\r\n                <p style=\"margin: 0 0 16px; font-size: 18px; font-weight: bold; color: #2563EB;\">{$deliveryPoint}</p>\r\n                <p style=\"margin: 0 0 16px;\">Prosím, vyzvedněte si ji co nejdříve. Děkujeme za váš nákup!</p>\r\n                <p style=\"margin: 0 0 16px;\">Pokud máte dotazy, kontaktujte nás na <a href=\"mailto:opnx3d@seznam.cz\" style=\"color: #2563EB; text-decoration: none;\">opnx3d@seznam.cz</a>.</p>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #f8f8f8; border-radius: 0 0 8px 8px;\">\r\n                <p style=\"margin: 0; font-size: 12px; color: #666666;\">3D kryty | E-mail: opnx3d@seznam.cz | Telefon: +420 123 456 789</p>\r\n            </td>\r\n        </tr>\r\n    </table>\r\n</body>\r\n</html>',	NULL,	NULL,	'2025-07-20 16:12:27',	'[]',	'2025-07-20 16:12:27'),
(30,	'picked_up',	'cs',	'Vaše objednávka č. {$orderId} byla vyzvednuta',	'<!DOCTYPE html>\r\n<html lang=\"cs\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n    <title>Objednávka vyzvednuta</title>\r\n</head>\r\n<body style=\"margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 16px; color: #333333; background-color: #f4f4f4;\">\r\n    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);\">\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #2563EB; border-radius: 8px 8px 0 0;\">\r\n                <h1 style=\"color: #ffffff; margin: 0; font-size: 24px;\">Objednávka vyzvednuta</h1>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px;\">\r\n                <p style=\"margin: 0 0 16px;\">Dobrý den, {$recipient},</p>\r\n                <p style=\"margin: 0 0 16px;\">Vaše objednávka č. <strong>{$orderId}</strong> byla úspěšně vyzvednuta.</p>\r\n                <p style=\"margin: 0 0 16px;\">Děkujeme za váš nákup a těšíme se na váš další nákup!</p>\r\n                <p style=\"margin: 0 0 16px;\">Pokud máte dotazy, kontaktujte nás na <a href=\"mailto:opnx3d@seznam.cz\" style=\"color: #2563EB; text-decoration: none;\">opnx3d@seznam.cz</a>.</p>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td style=\"padding: 20px; text-align: center; background-color: #f8f8f8; border-radius: 0 0 8px 8px;\">\r\n                <p style=\"margin: 0; font-size: 12px; color: #666666;\">3D kryty | E-mail: opnx3d@seznam.cz | Telefon: +420 123 456 789</p>\r\n            </td>\r\n        </tr>\r\n    </table>\r\n</body>\r\n</html>',	NULL,	NULL,	'2025-07-20 16:12:27',	'[]',	'2025-07-20 16:12:27'),
(31,	'new_order',	'en',	'New Order No. {$order->id}',	'<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n    <meta charset=\"UTF-8\">\n    <title>New Order</title>\n</head>\n<body>\n    <h1>New Order No. {$order->id}</h1>\n    <p>Customer Name: {$recipient}</p>\n    <table style=\"border-collapse: collapse; width: 100%;\">\n        <thead>\n            <tr>\n                <th style=\"border: 1px solid #999; padding: 6px;\">#</th>\n                <th style=\"border: 1px solid #999; padding: 6px;\">ID</th>\n                <th style=\"border: 1px solid #999; padding: 6px;\">Manufacturer</th>\n                <th style=\"border: 1px solid #999; padding: 6px;\">Model</th>\n                <th style=\"border: 1px solid #999; padding: 6px;\">Color</th>\n                <th style=\"border: 1px solid #999; padding: 6px;\">Features</th>\n                <th style=\"border: 1px solid #999; padding: 6px;\">Quantity</th>\n                <th style=\"border: 1px solid #999; padding: 6px;\">Price per Unit (€)</th>\n                <th style=\"border: 1px solid #999; padding: 6px;\">Total (€)</th>\n            </tr>\n        </thead>\n        <tbody>\n            {foreach $items as $i => $item}\n                <tr>\n                    <td style=\"border: 1px solid #999; padding: 6px;\">{$i + 1}</td>\n                    <td style=\"border: 1px solid #999; padding: 6px;\">{$item->id}</td>\n                    <td style=\"border: 1px solid #999; padding: 6px;\">{$item->manufacturer}</td>\n                    <td style=\"border: 1px solid #999; padding: 6px;\">{$item->model}</td>\n                    <td style=\"border: 1px solid #999; padding: 6px;\">{$item->color}</td>\n                    <td style=\"border: 1px solid #999; padding: 6px;\">\n                        {foreach $item->features as $key => $value}\n                            {$key}: {$value}<br>\n                        {/foreach}\n                    </td>\n                    <td style=\"border: 1px solid #999; padding: 6px;\">{$item->quantity}</td>\n                    <td style=\"border: 1px solid #999; padding: 6px;\">{number_format($item->total_price_eur, 2, \".\", \"\")}</td>\n                    <td style=\"border: 1px solid #999; padding: 6px;\">{number_format($item->total_price_eur * $item->quantity, 2, \".\", \"\")}</td>\n                </tr>\n            {/foreach}\n        </tbody>\n    </table>\n    <p>Items Subtotal: {number_format($itemsSubtotal, 2, \".\", \"\")} €</p>\n    <p>Shipping ({$vendorName}): {number_format($shippingCost, 2, \".\", \"\")} €</p>\n    <p>Payment ({if $order->payment == \"DOBIRKA\"}Cash on Delivery{else}Bank Transfer{/if}): {number_format($paymentCost, 2, \".\", \"\")} €</p>\n    <p><strong>Total: {number_format($total, 2, \".\", \"\")} €</strong></p>\n</body>\n</html>',	'opnx3d@seznam.cz',	NULL,	'2025-08-10 16:50:00',	'[]',	'2025-08-10 16:50:00'),
(32,	'registration',	'en',	'Welcome! Your Registration Was Successful',	'<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n    <meta charset=\"UTF-8\">\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n    <title>Registration Confirmation</title>\n</head>\n<body style=\"margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 16px; color: #333333; background-color: #f4f4f4;\">\n    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);\">\n        <tr>\n            <td style=\"padding: 20px; text-align: center; background-color: #2563EB; border-radius: 8px 8px 0 0;\">\n                <h1 style=\"color: #ffffff; margin: 0; font-size: 24px;\">Welcome, {$username}!</h1>\n            </td>\n        </tr>\n        <tr>\n            <td style=\"padding: 20px;\">\n                <p style=\"margin: 0 0 16px;\">Hello,</p>\n                <p style=\"margin: 0 0 16px;\">Thank you for registering with <strong>3D Cases</strong>.</p>\n                <p style=\"margin: 0 0 16px;\"><strong>Email:</strong> {$email}</p>\n                <p style=\"margin: 0 0 16px;\">We look forward to your order! If you have any questions, contact us at <a href=\"mailto:opnx3d@seznam.cz\" style=\"color: #2563EB; text-decoration: none;\">opnx3d@seznam.cz</a>.</p>\n            </td>\n        </tr>\n        <tr>\n            <td style=\"padding: 20px; text-align: center; background-color: #f8f8f8; border-radius: 0 0 8px 8px;\">\n                <p style=\"margin: 0; font-size: 12px; color: #666666;\">3D Cases | Email: opnx3d@seznam.cz | Phone: +420 123 456 789</p>\n            </td>\n        </tr>\n    </table>\n</body>\n</html>',	NULL,	NULL,	'2025-08-10 16:50:00',	'[]',	'2025-08-10 16:50:00'),
(33,	'new_user',	'en',	'New User Registration',	'<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n    <meta charset=\"UTF-8\">\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n    <title>New User Registration</title>\n</head>\n<body style=\"margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 16px; color: #333333; background-color: #f4f4f4;\">\n    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);\">\n        <tr>\n            <td style=\"padding: 20px; text-align: center; background-color: #2563EB; border-radius: 8px 8px 0 0;\">\n                <h1 style=\"color: #ffffff; margin: 0; font-size: 24px;\">New User Registration</h1>\n            </td>\n        </tr>\n        <tr>\n            <td style=\"padding: 20px;\">\n                <p style=\"margin: 0 0 16px;\">Hello,</p>\n                <p style=\"margin: 0 0 16px;\">A new user <strong>{$username}</strong> has registered with <strong>3D Cases</strong>.</p>\n                <p style=\"margin: 0 0 16px;\"><strong>Email:</strong> {$email}</p>\n                <p style=\"margin: 0 0 16px;\">Please review the registration in the admin panel.</p>\n            </td>\n        </tr>\n        <tr>\n            <td style=\"padding: 20px; text-align: center; background-color: #f8f8f8; border-radius: 0 0 8px 8px;\">\n                <p style=\"margin: 0; font-size: 12px; color: #666666;\">3D Cases | Email: opnx3d@seznam.cz | Phone: +420 123 456 789</p>\n            </td>\n        </tr>\n    </table>\n</body>\n</html>',	'opnx3d@seznam.cz',	NULL,	'2025-08-10 16:50:00',	'[]',	'2025-08-10 16:50:00'),
(34,	'invoice',	'en',	'Invoice for Your Order No. {$order->id}',	'<style>\n        body {\n            font-family: DejaVu Sans, sans-serif;\n            font-size: 12px;\n            color: #333;\n        }\n        .header {\n            text-align: center;\n            margin-bottom: 20px;\n        }\n        .details, .items, .summary {\n            width: 100%;\n            margin-bottom: 20px;\n            border-collapse: collapse;\n        }\n        .details td, .summary td {\n            padding: 5px;\n            vertical-align: top;\n        }\n        .items th, .items td {\n            border: 1px solid #999;\n            padding: 6px;\n            text-align: left;\n        }\n        .items th {\n            background-color: #f2f2f2;\n        }\n        .summary th {\n            background-color: #f2f2f2;\n            padding: 6px;\n            text-align: left;\n        }\n        h1 {\n            font-size: 20px;\n            margin-bottom: 5px;\n        }\n        hr {\n            margin: 20px 0;\n        }\n        .payment-details {\n            margin-top: 20px;\n        }\n    </style>\n    <div class=\"header\">\n        <h1>Invoice No. {$order->id}</h1>\n        <p>Issue Date: {$order->created_at|date:\"d.m.Y\"}</p>\n    </div>\n    <table class=\"details\">\n        <tr>\n            <td><strong>Customer:</strong><br>{$recipient}</td>\n            <td>\n                <strong>Address:</strong><br>\n                {$order->address}<br>\n                {$order->city}, {$order->psc}\n            </td>\n            <td>\n                <strong>Contact Details:</strong><br>\n                Email: {$order->email}<br>\n                Phone: {$order->phone}\n            </td>\n        </tr>\n        <tr>\n            <td><strong>Shipping Method:</strong><br>{$vendorName}</td>\n            <td><strong>Delivery Point:</strong><br>{$order->delivery_point ?: \"Not specified\"}</td>\n            <td><strong>Payment Method:</strong><br>{if $order->payment == \"DOBIRKA\"}Cash on Delivery{else}Bank Transfer{/if}</td>\n        </tr>\n    </table>\n    <hr>\n    <table class=\"items\">\n        <thead>\n            <tr>\n                <th>#</th>\n                <th>ID</th>\n                <th>Manufacturer</th>\n                <th>Model</th>\n                <th>Color</th>\n                <th>Features</th>\n                <th>Quantity</th>\n                <th>Price per Unit (€)</th>\n                <th>Total (€)</th>\n            </tr>\n        </thead>\n        <tbody>\n            {foreach $items as $i => $item}\n                <tr>\n                    <td>{$i + 1}</td>\n                    <td>{$item->id}</td>\n                    <td>{$item->manufacturer}</td>\n                    <td>{$item->model}</td>\n                    <td>{$item->color}</td>\n                    <td>\n                        {foreach $item->features as $key => $value}\n                            {$key}: {$value}<br>\n                        {/foreach}\n                    </td>\n                    <td>{$item->quantity}</td>\n                    <td>{number_format($item->total_price_eur, 2, \".\", \"\")}</td>\n                    <td>{number_format($item->total_price_eur * $item->quantity, 2, \".\", \"\")}</td>\n                </tr>\n            {/foreach}\n        </tbody>\n    </table>\n    <table class=\"summary\">\n        <thead>\n            <tr>\n                <th colspan=\"2\">Cost Summary</th>\n            </tr>\n        </thead>\n        <tbody>\n            <tr>\n                <td>Items Subtotal:</td>\n                <td>{number_format($itemsSubtotal, 2, \".\", \"\")} €</td>\n            </tr>\n            <tr>\n                <td>Shipping ({$vendorName}):</td>\n                <td>{number_format($shippingCost, 2, \".\", \"\")} €</td>\n            </tr>\n            <tr>\n                <td>Payment ({if $order->payment == \"DOBIRKA\"}Cash on Delivery{else}Bank Transfer{/if}):</td>\n                <td>{number_format($paymentCost, 2, \".\", \"\")} €</td>\n            </tr>\n            <tr>\n                <td><strong>Total:</strong></td>\n                <td><strong>{number_format($total, 2, \".\", \"\")} €</strong></td>\n            </tr>\n        </tbody>\n    </table>\n    <div class=\"payment-details\">\n        <h2>Payment Details</h2>\n        {if $order->payment == \"PREVOD\"}\n            <p>\n                <strong>Bank Account:</strong> 111222333<br>\n                <strong>Variable Symbol:</strong> {$order->variable_symbol}<br>\n                <strong>Amount:</strong> {number_format($total, 2, \".\", \"\")} €<br>\n            </p>\n        {else}\n            <p>\n                <strong>Payment on Delivery:</strong><br>\n                Amount: {number_format($total, 2, \".\", \"\")} €<br>\n                Please prepare the specified amount in cash upon receipt of the shipment.\n            </p>\n        {/if}\n    </div>',	NULL,	NULL,	'2025-08-10 16:50:00',	'[]',	'2025-08-10 16:50:00'),
(35,	'invoice_email',	'en',	'Invoice for Your Order No. {$orderId}',	'<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n    <meta charset=\"UTF-8\">\n    <title>Invoice</title>\n</head>\n<body>\n    <h1>Hello, {$recipient}</h1>\n    <p>Attached is the invoice for your order no. {$orderId}.</p>\n    <p>Thank you for your purchase!</p>\n</body>\n</html>',	NULL,	NULL,	'2025-08-10 16:50:00',	'[]',	'2025-08-10 16:50:00'),
(36,	'password_reset',	'en',	'Password Reset',	'<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n    <meta charset=\"UTF-8\">\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n    <title>Password Reset</title>\n</head>\n<body style=\"margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 16px; color: #333333; background-color: #f4f4f4;\">\n    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);\">\n        <tr>\n            <td style=\"padding: 20px; text-align: center; background-color: #2563EB; border-radius: 8px 8px 0 0;\">\n                <h1 style=\"color: #ffffff; margin: 0; font-size: 24px;\">Password Reset</h1>\n            </td>\n        </tr>\n        <tr>\n            <td style=\"padding: 20px;\">\n                <p style=\"margin: 0 0 16px;\">Hello,</p>\n                <p style=\"margin: 0 0 16px;\">To reset your password, please use the following code:</p>\n                <p style=\"margin: 0 0 16px; font-size: 18px; font-weight: bold; color: #2563EB;\">{$resetCode}</p>\n                <p style=\"margin: 0 0 16px;\">Enter this code on the password reset page. If you did not request a reset, please ignore this email.</p>\n                <p style=\"margin: 0 0 16px;\">If you need assistance, contact us at <a href=\"mailto:opnx3d@seznam.cz\" style=\"color: #2563EB; text-decoration: none;\">opnx3d@seznam.cz</a>.</p>\n            </td>\n        </tr>\n        <tr>\n            <td style=\"padding: 20px; text-align: center; background-color: #f8f8f8; border-radius: 0 0 8px 8px;\">\n                <p style=\"margin: 0; font-size: 12px; color: #666666;\">3D Cases | Email: opnx3d@seznam.cz | Phone: +420 123 456 789</p>\n            </td>\n        </tr>\n    </table>\n</body>\n</html>',	NULL,	NULL,	'2025-08-10 16:50:00',	'[]',	'2025-08-10 16:50:00'),
(37,	'payment_confirmation',	'en',	'Payment Confirmation for Order No. {$orderId}',	'<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n    <meta charset=\"UTF-8\">\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n    <title>Payment Confirmation</title>\n</head>\n<body style=\"margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 16px; color: #333333; background-color: #f4f4f4;\">\n    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);\">\n        <tr>\n            <td style=\"padding: 20px; text-align: center; background-color: #2563EB; border-radius: 8px 8px 0 0;\">\n                <h1 style=\"color: #ffffff; margin: 0; font-size: 24px;\">Payment Confirmation</h1>\n            </td>\n        </tr>\n        <tr>\n            <td style=\"padding: 20px;\">\n                <p style=\"margin: 0 0 16px;\">Hello, {$recipient},</p>\n                <p style=\"margin: 0 0 16px;\">We confirm receipt of payment for order no. <strong>{$orderId}</strong>.</p>\n                <p style=\"margin: 0 0 16px;\">We are working on dispatching your shipment. We will inform you about further progress.</p>\n                <p style=\"margin: 0 0 16px;\">Thank you for your purchase! If you have any questions, contact us at <a href=\"mailto:opnx3d@seznam.cz\" style=\"color: #2563EB; text-decoration: none;\">opnx3d@seznam.cz</a>.</p>\n            </td>\n        </tr>\n        <tr>\n            <td style=\"padding: 20px; text-align: center; background-color: #f8f8f8; border-radius: 0 0 8px 8px;\">\n                <p style=\"margin: 0; font-size: 12px; color: #666666;\">3D Cases | Email: opnx3d@seznam.cz | Phone: +420 123 456 789</p>\n            </td>\n        </tr>\n    </table>\n</body>\n</html>',	NULL,	NULL,	'2025-08-10 16:50:00',	'[]',	'2025-08-10 16:50:00'),
(38,	'shipped',	'en',	'Your Order No. {$orderId} Has Been Shipped',	'<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n    <meta charset=\"UTF-8\">\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n    <title>Order Shipped</title>\n</head>\n<body style=\"margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 16px; color: #333333; background-color: #f4f4f4;\">\n    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);\">\n        <tr>\n            <td style=\"padding: 20px; text-align: center; background-color: #2563EB; border-radius: 8px 8px 0 0;\">\n                <h1 style=\"color: #ffffff; margin: 0; font-size: 24px;\">Order Shipped</h1>\n            </td>\n        </tr>\n        <tr>\n            <td style=\"padding: 20px;\">\n                <p style=\"margin: 0 0 16px;\">Hello, {$recipient},</p>\n                <p style=\"margin: 0 0 16px;\">Your order no. <strong>{$orderId}</strong> has been shipped.</p>\n                <p style=\"margin: 0 0 16px;\">It will soon arrive at your chosen delivery point. Thank you for your purchase!</p>\n                <p style=\"margin: 0 0 16px;\">If you have any questions, contact us at <a href=\"mailto:opnx3d@seznam.cz\" style=\"color: #2563EB; text-decoration: none;\">opnx3d@seznam.cz</a>.</p>\n            </td>\n        </tr>\n        <tr>\n            <td style=\"padding: 20px; text-align: center; background-color: #f8f8f8; border-radius: 0 0 8px 8px;\">\n                <p style=\"margin: 0; font-size: 12px; color: #666666;\">3D Cases | Email: opnx3d@seznam.cz | Phone: +420 123 456 789</p>\n            </td>\n        </tr>\n    </table>\n</body>\n</html>',	NULL,	NULL,	'2025-08-10 16:50:00',	'[]',	'2025-08-10 16:50:00'),
(39,	'ready_for_pickup',	'en',	'Your Order No. {$orderId} Is Ready for Pickup',	'<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n    <meta charset=\"UTF-8\">\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n    <title>Order Ready for Pickup</title>\n</head>\n<body style=\"margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 16px; color: #333333; background-color: #f4f4f4;\">\n    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);\">\n        <tr>\n            <td style=\"padding: 20px; text-align: center; background-color: #2563EB; border-radius: 8px 8px 0 0;\">\n                <h1 style=\"color: #ffffff; margin: 0; font-size: 24px;\">Order Ready for Pickup</h1>\n            </td>\n        </tr>\n        <tr>\n            <td style=\"padding: 20px;\">\n                <p style=\"margin: 0 0 16px;\">Hello, {$recipient},</p>\n                <p style=\"margin: 0 0 16px;\">Your order no. <strong>{$orderId}</strong> is ready for pickup at:</p>\n                <p style=\"margin: 0 0 16px; font-size: 18px; font-weight: bold; color: #2563EB;\">{$deliveryPoint}</p>\n                <p style=\"margin: 0 0 16px;\">Please pick it up as soon as possible. Thank you for your purchase!</p>\n                <p style=\"margin: 0 0 16px;\">If you have any questions, contact us at <a href=\"mailto:opnx3d@seznam.cz\" style=\"color: #2563EB; text-decoration: none;\">opnx3d@seznam.cz</a>.</p>\n            </td>\n        </tr>\n        <tr>\n            <td style=\"padding: 20px; text-align: center; background-color: #f8f8f8; border-radius: 0 0 8px 8px;\">\n                <p style=\"margin: 0; font-size: 12px; color: #666666;\">3D Cases | Email: opnx3d@seznam.cz | Phone: +420 123 456 789</p>\n            </td>\n        </tr>\n    </table>\n</body>\n</html>',	NULL,	NULL,	'2025-08-10 16:50:00',	'[]',	'2025-08-10 16:50:00'),
(40,	'picked_up',	'en',	'Your Order No. {$orderId} Has Been Picked Up',	'<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n    <meta charset=\"UTF-8\">\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n    <title>Order Picked Up</title>\n</head>\n<body style=\"margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 16px; color: #333333; background-color: #f4f4f4;\">\n    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);\">\n        <tr>\n            <td style=\"padding: 20px; text-align: center; background-color: #2563EB; border-radius: 8px 8px 0 0;\">\n                <h1 style=\"color: #ffffff; margin: 0; font-size: 24px;\">Order Picked Up</h1>\n            </td>\n        </tr>\n        <tr>\n            <td style=\"padding: 20px;\">\n                <p style=\"margin: 0 0 16px;\">Hello, {$recipient},</p>\n                <p style=\"margin: 0 0 16px;\">Your order no. <strong>{$orderId}</strong> has been successfully picked up.</p>\n                <p style=\"margin: 0 0 16px;\">Thank you for your purchase, and we look forward to your next order!</p>\n                <p style=\"margin: 0 0 16px;\">If you have any questions, contact us at <a href=\"mailto:opnx3d@seznam.cz\" style=\"color: #2563EB; text-decoration: none;\">opnx3d@seznam.cz</a>.</p>\n            </td>\n        </tr>\n        <tr>\n            <td style=\"padding: 20px; text-align: center; background-color: #f8f8f8; border-radius: 0 0 8px 8px;\">\n                <p style=\"margin: 0; font-size: 12px; color: #666666;\">3D Cases | Email: opnx3d@seznam.cz | Phone: +420 123 456 789</p>\n            </td>\n        </tr>\n    </table>\n</body>\n</html>',	NULL,	NULL,	'2025-08-10 16:50:00',	'[]',	'2025-08-10 16:50:00');

DROP TABLE IF EXISTS `feature_options`;
CREATE TABLE `feature_options` (
  `id` int NOT NULL AUTO_INCREMENT,
  `feature_id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `name_cs` varchar(50) NOT NULL,
  `name_en` varchar(50) NOT NULL,
  `price` decimal(10,2) DEFAULT '0.00',
  `price_eur` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Price of the feature option in EUR',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `allow_user_upload` tinyint(1) NOT NULL DEFAULT '0',
  `mesh_name` varchar(50) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_feature_option` (`feature_id`,`name`),
  KEY `feature_id` (`feature_id`),
  CONSTRAINT `feature_options_ibfk_1` FOREIGN KEY (`feature_id`) REFERENCES `features` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `feature_options` (`id`, `feature_id`, `name`, `name_cs`, `name_en`, `price`, `price_eur`, `created_at`, `allow_user_upload`, `mesh_name`, `visible`) VALUES
(16,	16,	'Ano',	'Ano',	'Yes',	30.00,	1.20,	'2025-07-09 19:46:47',	0,	'clona',	1),
(18,	16,	'Ne',	'Ne',	'No',	0.00,	0.00,	'2025-07-09 19:46:50',	0,	NULL,	NULL),
(24,	12,	'2 Sloty',	'2 Sloty',	'2 Slots',	45.00,	1.80,	'2025-07-09 19:47:06',	0,	'drzak',	1),
(25,	12,	'Žádný',	'Žádný',	'None',	0.00,	0.00,	'2025-07-09 19:47:37',	0,	'drzak',	0),
(30,	18,	'Ano',	'Ano',	'Yes',	30.00,	1.20,	'2025-07-09 20:05:52',	0,	NULL,	NULL),
(31,	18,	'Ne',	'Ne',	'No',	0.00,	0.00,	'2025-07-09 20:05:58',	0,	NULL,	NULL),
(38,	22,	'Ano',	'Ano',	'Yes',	85.00,	3.40,	'2025-07-15 09:44:06',	1,	NULL,	NULL),
(39,	22,	'Ne',	'Ne',	'No',	0.00,	0.00,	'2025-07-15 09:44:11',	0,	NULL,	NULL),
(43,	25,	'ANO',	'ANO',	'ANO',	111.00,	12.00,	'2025-09-11 14:16:01',	0,	'testovaci',	1),
(44,	25,	'NE',	'NE',	'NO',	0.00,	0.00,	'2025-09-11 14:16:16',	0,	'testovaci',	1);

DROP TABLE IF EXISTS `features`;
CREATE TABLE `features` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `name_en` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `explanation_mark_enabled` tinyint(1) DEFAULT '0',
  `explanation_mark_cs` text,
  `explanation_mark_en` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `features` (`id`, `name`, `name_en`, `created_at`, `explanation_mark_enabled`, `explanation_mark_cs`, `explanation_mark_en`) VALUES
(12,	'Držák karet',	'Card Holder',	'2025-07-09 19:45:56',	0,	NULL,	NULL),
(16,	'Clona přední kamery',	'Front Camera Cover',	'2025-07-09 19:46:07',	1,	'Clona přední kamery zakryje kameru takže bude zakrytá',	'The front camera cover will cover the camera so it is hidden'),
(18,	'Krytka nabíjecího portu',	'Charging Port Cover',	'2025-07-09 19:46:27',	0,	NULL,	NULL),
(22,	'Vlastní motiv',	'Custom Design',	'2025-07-15 09:43:43',	0,	NULL,	NULL),
(25,	'TESTOVACI',	'TESTOVACI',	'2025-09-11 14:15:37',	0,	'',	'');

DROP TABLE IF EXISTS `gallery`;
CREATE TABLE `gallery` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `alt_text_en` text,
  `ordering` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `gallery` (`id`, `image`, `alt_text`, `alt_text_en`, `ordering`) VALUES
(15,	'/www/uploads/gallery/687945b800dec_holka-ridicak.webp',	'Autoškola Prima',	NULL,	5),
(16,	'/www/uploads/gallery/687945c42eeae_zidle.webp',	'adsads',	NULL,	3),
(17,	'/www/uploads/gallery/687945cd88555_kluk-s-autem.webp',	'asdasdfg',	NULL,	2),
(18,	'/www/uploads/gallery/687945d87ac49_ucebna.webp',	'gfsdf',	NULL,	1),
(19,	'/www/Uploads/gallery/6899d49ea4cf8_brave-TqmX7IcFMY.webp',	'test',	'eng',	4);

DROP TABLE IF EXISTS `legal_pages`;
CREATE TABLE `legal_pages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `section_name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `title_en` varchar(255) DEFAULT NULL,
  `content_en` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `section_name` (`section_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `legal_pages` (`id`, `section_name`, `title`, `content`, `updated_at`, `title_en`, `content_en`) VALUES
(1,	'obchodni-podminky',	'Obchodní podmínky',	'<h2>Obchodní podmínky</h2><p>Toto jsou testovací obchodní podmínky pro OPNX3D. Všechny nákupy podléhají těmto pravidlům. Prosím, přečtěte si pečlivě.</p><p>1. <strong>Předmět smlouvy</strong>: Prodáváme obaly na telefony dle specifikací zákazníka.</p><p>2. <strong>Ceny</strong>: Všechny ceny jsou uvedeny v Kč včetně DPH.</p>',	'2025-08-11 12:17:05',	'Terms and conditions',	'<h2>Terms and Conditions</h2><p>These are the test terms and conditions for OPNX3D. All purchases are subject to these rules. Please read carefully.</p><p>1. <strong>Subject of the contract</strong>: We sell phone cases according to customer specifications.</p><p>2. <strong>Prices</strong>: All prices are in CZK including VAT.</p>'),
(2,	'ochrana-osobnich-udaju',	'Ochrana osobních údajů',	'<h2>Ochrana osobních údajů</h2><p>OPNX3D chrání vaše osobní údaje dle GDPR. Zde je testovací obsah.</p><p>1. <strong>Shromažďování údajů</strong>: Shromažďujeme pouze nezbytné údaje pro zpracování objednávek.</p><p>2. <strong>Použití údajů</strong>: Údaje jsou použity výhradně pro účely doručení a komunikace.</p>',	'2025-07-17 09:08:46',	'Privacy Policy',	'<h2>Privacy Policy</h2><p>OPNX3D protects your personal data according to GDPR. Here is the test content.</p><p>1. <strong>Data Collection</strong>: We only collect the necessary data to process orders.</p><p>2. <strong>Data Use</strong>: The data is used exclusively for delivery and communication purposes.</p>'),
(3,	'reklamacni-rad',	'Reklamační řád',	'<h2>Reklamační řád</h2><p>Tento testovací reklamační řád popisuje postup při reklamaci zboží.</p><p>1. <strong>Lhůta pro reklamaci</strong>: Zboží lze reklamovat do 24 měsíců od nákupu.</p><p>2. <strong>Postup</strong>: Kontaktujte nás na opnx3d@gmail.com.</p>',	'2025-07-17 09:08:46',	'Returns Policy',	'<h2>Complaints Policy</h2><p>This test complaint policy describes the procedure for claiming goods.</p><p>1. <strong>Complaint period</strong>: Goods can be claimed within 24 months of purchase.</p><p>2. <strong>Procedure</strong>: Contact us at opnx3d@gmail.com.</p>'),
(4,	'odstoupeni-od-smlouvy',	'Odstoupení od smlouvy',	'<h2>Odstoupení od smlouvy</h2><p>Testovací obsah pro odstoupení od smlouvy.</p><p>1. <strong>Lhůta</strong>: Od smlouvy lze odstoupit do 14 dnů bez udání důvodu.</p><p>2. <strong>Vrácení zboží</strong>: Zboží musí být vráceno nepoškozené na naši adresu.</p>',	'2025-07-17 09:08:46',	'Contract Withdrawal',	'<h2>Withdrawal from the contract</h2><p>Test content for withdrawal from the contract.</p><p>1. <strong>Term</strong>: You can withdraw from the contract within 14 days without giving any reason.</p><p>2. <strong>Return of goods</strong>: The goods must be returned undamaged to our address.</p>');

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
(18,	'ŠAOMI',	'2025-09-11 14:16:39');

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
(24,	1),
(25,	1),
(1,	2),
(1,	3),
(24,	3),
(25,	3),
(1,	4),
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
(25,	16,	16),
(1,	16,	18),
(24,	16,	18),
(1,	12,	25),
(24,	12,	25),
(25,	25,	43);

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


DROP TABLE IF EXISTS `models`;
CREATE TABLE `models` (
  `id` int NOT NULL AUTO_INCREMENT,
  `manufacturer_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Base price of the model',
  `price_eur` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Base price of the model in EUR',
  `model_3d_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `manufacturer_id` (`manufacturer_id`),
  CONSTRAINT `models_ibfk_1` FOREIGN KEY (`manufacturer_id`) REFERENCES `manufacturers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `models` (`id`, `manufacturer_id`, `name`, `created_at`, `price`, `price_eur`, `model_3d_path`) VALUES
(1,	1,	'iPhone 13',	'2025-07-03 16:52:35',	529.99,	21.20,	'/www/uploads/models/1/3Dfile.gltf'),
(24,	1,	'Iphone 12',	'2025-09-04 22:22:34',	100.00,	12.00,	'/www/uploads/models/24/3Dfile.gltf'),
(25,	18,	'Redmi note 13',	'2025-09-11 14:17:05',	111.00,	12.00,	NULL);

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
(66,	155,	1),
(67,	156,	2),
(68,	156,	2),
(69,	156,	2),
(70,	157,	1),
(71,	158,	1),
(72,	159,	2),
(73,	170,	1),
(74,	171,	1);

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
  `lang` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cs',
  `total_price` decimal(10,2) DEFAULT '0.00' COMMENT 'Total price of the order in CZK',
  `total_price_eur` decimal(10,2) DEFAULT '0.00' COMMENT 'Total price of the order in EUR',
  PRIMARY KEY (`id`),
  UNIQUE KEY `variable_symbol` (`variable_symbol`),
  KEY `user_id` (`user_id`),
  KEY `orders_shipping_fk` (`shipping`),
  KEY `orders_payment_fk` (`payment`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_payment_fk` FOREIGN KEY (`payment`) REFERENCES `vendor_payment_methods` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `orders_shipping_fk` FOREIGN KEY (`shipping`) REFERENCES `shipping_options` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `orders` (`id`, `user_id`, `firstname`, `lastname`, `email`, `phone`, `address`, `city`, `psc`, `payment`, `shipping`, `delivery_point`, `additional_cost`, `state`, `created_at`, `variable_symbol`, `lang`, `total_price`, `total_price_eur`) VALUES
(59,	9,	'Martin',	'Burda',	'burdadko.cz@gmail.com',	'111222333',	'shrekova bazina 13',	'Praha',	'53803',	7,	11,	'sokolská 799 hermanuv mestec',	91.00,	'ZAPLACENO',	'2025-07-24 16:39:56',	'202507247157',	'cs',	0.00,	0.00),
(60,	9,	'Martin',	'Burda',	'burdadko.cz@gmail.com',	'111222333',	'shrekova bazina 13',	'Praha',	'53803',	6,	11,	'sokolská 799 hermanuv mestec',	79.00,	'OBJEDNANO',	'2025-07-24 17:14:25',	'202507249841',	'cs',	0.00,	0.00),
(61,	NULL,	'Martin',	'Burda',	'burdadko.cz@gmail.com',	'666777888',	'shrekova bazina 13',	'Praha',	'53803',	6,	11,	'sokolská 799 hermanuv mestec',	79.00,	'OBJEDNANO',	'2025-07-24 20:28:15',	'202507245394',	'cs',	0.00,	0.00),
(62,	9,	'Martin',	'Burda',	'burdadko.cz@gmail.com',	'111222333',	'shrekova bazina 13',	'Praha',	'53803',	6,	11,	'sokolská 799 hermanuv mestec',	79.00,	'OBJEDNANO',	'2025-07-24 20:30:13',	'202507241828',	'cs',	0.00,	0.00),
(63,	NULL,	'Martin',	'Burda',	'burdadko.cz@gmail.com',	'666777888',	'shrekova bazina 13',	'Praha',	'53803',	6,	11,	'sokolská 799 hermanuv mestec',	79.00,	'OBJEDNANO',	'2025-07-25 13:50:33',	'202507256077',	'cs',	0.00,	0.00),
(64,	9,	'Martin',	'Burda',	'burdadko.cz@gmail.com',	'111222333',	'shrekova bazina 13',	'Praha',	'53803',	6,	11,	'sokolská 799 hermanuv mestec',	79.00,	'OBJEDNANO',	'2025-07-25 14:30:50',	'202507259712',	'cs',	0.00,	0.00),
(65,	NULL,	'Martin',	'Burda',	'burdadko.cz@gmail.com',	'666777888',	'shrekova bazina 13',	'Praha',	'53803',	6,	11,	'sokolská 799 hermanuv mestec',	79.00,	'OBJEDNANO',	'2025-08-10 08:53:35',	'202508103994',	'cs',	0.00,	0.00),
(66,	9,	'Martin',	'Burda',	'burdadko.cz@gmail.com',	'111222333',	'shrekova bazina 13',	'Praha',	'53803',	8,	14,	'sokolská 799 hermanuv mestec',	500.00,	'OBJEDNANO',	'2025-08-10 15:30:34',	'202508103936',	'cs',	0.00,	0.00),
(67,	9,	'Martin',	'Burda',	'burdadko.cz@gmail.com',	'111222333',	'shrekova bazina 13',	'Praha',	'53803',	8,	13,	'sokolská 799 hermanuv mestec',	250.00,	'OBJEDNANO',	'2025-08-10 15:40:20',	'202508103632',	'cs',	0.00,	0.00),
(68,	9,	'Martin',	'Burda',	'burdadko.cz@gmail.com',	'111222333',	'shrekova bazina 13',	'Praha',	'53803',	8,	13,	'sokolská 799 hermanuv mestec',	250.00,	'OBJEDNANO',	'2025-08-10 15:47:23',	'202508105487',	'cs',	0.00,	0.00),
(69,	9,	'Martin',	'Burda',	'burdadko.cz@gmail.com',	'111222333',	'shrekova bazina 13',	'Praha',	'53803',	8,	13,	'sokolská 799 hermanuv mestec',	250.00,	'OBJEDNANO',	'2025-08-10 15:51:03',	'202508101706',	'cs',	0.00,	0.00),
(70,	NULL,	'Martin',	'Burda',	'burdadko.cz@gmail.com',	'666777888',	'shrekova bazina 13',	'Praha',	'53803',	6,	11,	'sokolská 799 hermanuv mestec',	79.00,	'OBJEDNANO',	'2025-08-10 16:10:16',	'202508106341',	'cs',	0.00,	0.00),
(71,	9,	'Martin',	'Burda',	'burdadko.cz@gmail.com',	'111222333',	'shrekova bazina 13',	'Praha',	'53803',	9,	13,	'sokolská 799 hermanuv mestec',	12.00,	'ODESLANO',	'2025-08-10 17:36:16',	'202508107471',	'en',	0.00,	0.00),
(72,	9,	'Martin',	'Burda',	'burdadko.cz@gmail.com',	'111222333',	'shrekova bazina 13',	'Praha',	'53803',	8,	13,	'sokolská 799 hermanuv mestec',	10.00,	'OBJEDNANO',	'2025-08-11 08:54:42',	'202508117062',	'en',	1359.98,	64.00),
(73,	9,	'Martin',	'Burda',	'burdadko.cz@gmail.com',	'111222333',	'shrekova bazina 13',	'Praha',	'53803',	6,	11,	'sokolská 799 hermanuv mestec',	79.00,	'OBJEDNANO',	'2025-09-07 10:47:33',	'202509074522',	'cs',	608.99,	100.20),
(74,	NULL,	'Martin',	'Burda',	'burdadko.cz@gmail.com',	'666777888',	'shrekova bazina 13',	'Praha',	'53803',	6,	11,	'sokolská 799 hermanuv mestec',	79.00,	'OBJEDNANO',	'2025-09-10 19:25:58',	'202509104276',	'cs',	608.99,	100.20);

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `name` varchar(50) COLLATE utf8mb4_czech_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

INSERT INTO `settings` (`name`, `value`) VALUES
('shutdown',	'0');

DROP TABLE IF EXISTS `shipping_options`;
CREATE TABLE `shipping_options` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vendor_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cost_eur` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Cost in EUR',
  PRIMARY KEY (`id`),
  KEY `vendor_id` (`vendor_id`),
  CONSTRAINT `shipping_options_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `shipping_options` (`id`, `vendor_id`, `name`, `cost`, `cost_eur`) VALUES
(9,	7,	'Na pobočku',	89.00,	0.00),
(10,	7,	'Na adresu',	110.00,	0.00),
(11,	6,	'Na pobočku',	79.00,	0.00),
(12,	6,	'Na adresu',	100.00,	0.00),
(13,	8,	'Standard International',	250.00,	10.00),
(14,	8,	'Express International',	500.00,	20.00);

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
(15,	12,	7),
(16,	13,	8),
(17,	13,	9),
(18,	14,	8),
(19,	14,	9);

DROP TABLE IF EXISTS `user_uploads`;
CREATE TABLE `user_uploads` (
  `id` int NOT NULL AUTO_INCREMENT,
  `file_path` varchar(255) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `user_uploads` (`id`, `file_path`, `original_filename`, `created_at`) VALUES
(1,	'/www/uploads/user_uploads/6883597040cbf.gltf',	'Xi-Redmi-No-13-s-redukci.gltf',	'2025-07-25 10:16:16'),
(2,	'/www/uploads/user_uploads/688359f6b948e.gltf',	'Xi-Redmi-No-13-bez-redukce.gltf',	'2025-07-25 10:18:30'),
(3,	'/www/uploads/user_uploads/68835c3b403bf.gltf',	'Xi-Redmi-No-13-bez-redukce.gltf',	'2025-07-25 10:28:11'),
(4,	'/www/uploads/user_uploads/68835e2bcd675.gltf',	'Xi-Redmi-No-13-bez-redukce.gltf',	'2025-07-25 10:36:27'),
(5,	'/www/uploads/user_uploads/688361858e054.gltf',	'Xi-Redmi-No-13-bez-redukce.gltf',	'2025-07-25 10:50:45'),
(6,	'/www/uploads/user_uploads/6883625fb40e9.gltf',	'Xi-Redmi-No-13-bez-redukce.gltf',	'2025-07-25 10:54:23'),
(7,	'/www/uploads/user_uploads/688364574652f.gltf',	'Xi-Redmi-No-13-bez-redukce.gltf',	'2025-07-25 11:02:47'),
(8,	'/www/uploads/user_uploads/68836482c5767.gltf',	'Xi-Redmi-No-13-s-redukci.gltf',	'2025-07-25 11:03:30'),
(9,	'/www/uploads/user_uploads/68836564a4d52.gltf',	'Xi-Redmi-No-13-bez-redukce.gltf',	'2025-07-25 11:07:16'),
(10,	'/www/uploads/user_uploads/688365a819a8c.gltf',	'Xi-Redmi-No-13-bez-redukce.gltf',	'2025-07-25 11:08:24'),
(11,	'/www/uploads/user_uploads/688365d71887f.gltf',	'Xi-Redmi-No-13-bez-redukce.gltf',	'2025-07-25 11:09:11'),
(12,	'/www/uploads/user_uploads/6883665e7711a.gltf',	'Xi-Redmi-No-13-bez-redukce.gltf',	'2025-07-25 11:11:26'),
(13,	'/www/uploads/user_uploads/6883743a9660f.gltf',	'Xi-Redmi-No-13-bez-redukce.gltf',	'2025-07-25 12:10:34'),
(14,	'/www/uploads/user_uploads/688374fd3f9ab.gltf',	'Xi-Redmi-No-13-bez-redukce.gltf',	'2025-07-25 12:13:49'),
(15,	'/www/uploads/user_uploads/6883784b3a668.gltf',	'Xi-Redmi-No-13-bez-redukce.gltf',	'2025-07-25 12:27:55'),
(16,	'/www/uploads/user_uploads/688394d9ee36b.gltf',	'Xi-Redmi-No-13-bez-redukce.gltf',	'2025-07-25 14:29:45'),
(17,	'/www/uploads/user_uploads/688394fd07e02.gltf',	'Xi-Redmi-No-13-bez-redukce.gltf',	'2025-07-25 14:30:21'),
(18,	'/www/uploads/user_uploads/6883950b7bd07.gltf',	'Xi-Redmi-No-13-s-redukci.gltf',	'2025-07-25 14:30:35'),
(19,	'/www/uploads/user_uploads/6883eaef0f04d.png',	'Yellow-Black-Simple-Creative-Agency-Logo.png',	'2025-07-25 20:37:03'),
(20,	'/www/uploads/user_uploads/6883ecaaeb186.jpeg',	'holka-ridicak.jpeg',	'2025-07-25 20:44:26'),
(21,	'/www/uploads/user_uploads/6883ee713351b.png',	'logoLight.png',	'2025-07-25 20:52:01'),
(22,	'/www/uploads/user_uploads/6883f0661cc19.jpeg',	'ridicak.jpeg',	'2025-07-25 21:00:22'),
(23,	'/www/uploads/user_uploads/6883f07aed0ac.jpeg',	'Obrazek-WhatsApp-2025-07-12-v-07.06.31-ae69212d.jpeg',	'2025-07-25 21:00:42'),
(24,	'/www/uploads/user_uploads/68af41231dce9.jpeg',	'Skoda-Felicia-tmavomodra-2012.jpeg',	'2025-08-27 17:32:19'),
(25,	'/www/uploads/user_uploads/68af413087a6f.jpeg',	'Skoda-Felicia-tmavomodra-2012.jpeg',	'2025-08-27 17:32:32'),
(26,	'/www/uploads/user_uploads/68af4130934a3.jpeg',	'Skoda-Felicia-tmavomodra-2012.jpeg',	'2025-08-27 17:32:32'),
(27,	'/www/uploads/user_uploads/68af58d48c56c.jpeg',	'Skoda-Felicia-tmavomodra-2012.jpeg',	'2025-08-27 19:13:24'),
(28,	'/www/uploads/user_uploads/68af58d499e66.jpeg',	'Skoda-Felicia-tmavomodra-2012.jpeg',	'2025-08-27 19:13:24'),
(29,	'/www/uploads/user_uploads/68af58e1560f5.jpeg',	'4b3b2c41-c91e-4e17-bb40-970452e47564.jpeg',	'2025-08-27 19:13:37'),
(30,	'/www/uploads/user_uploads/68af58e19f220.jpeg',	'4b3b2c41-c91e-4e17-bb40-970452e47564.jpeg',	'2025-08-27 19:13:37'),
(31,	'/www/uploads/user_uploads/68af5a39a3dc8.jpeg',	'Skoda-Felicia-tmavomodra-2012.jpeg',	'2025-08-27 19:19:21'),
(32,	'/www/uploads/user_uploads/68af5a39b21a1.jpeg',	'Skoda-Felicia-tmavomodra-2012.jpeg',	'2025-08-27 19:19:21'),
(33,	'/www/uploads/user_uploads/68af5ad90e29d.jpeg',	'Skoda-Felicia-tmavomodra-2012.jpeg',	'2025-08-27 19:22:01'),
(34,	'/www/uploads/user_uploads/68af5ad91a10f.jpeg',	'Skoda-Felicia-tmavomodra-2012.jpeg',	'2025-08-27 19:22:01'),
(35,	'/www/uploads/user_uploads/68af5c1f1c561.jpeg',	'4b3b2c41-c91e-4e17-bb40-970452e47564.jpeg',	'2025-08-27 19:27:27'),
(36,	'/www/uploads/user_uploads/68af5c1f28ab8.jpeg',	'4b3b2c41-c91e-4e17-bb40-970452e47564.jpeg',	'2025-08-27 19:27:27'),
(37,	'/www/uploads/user_uploads/68af5d1a122b5.jpeg',	'Skoda-Felicia-tmavomodra-2012.jpeg',	'2025-08-27 19:31:38'),
(38,	'/www/uploads/user_uploads/68af5d1a24560.jpeg',	'Skoda-Felicia-tmavomodra-2012.jpeg',	'2025-08-27 19:31:38'),
(39,	'/www/uploads/user_uploads/68af5f6b489a5.jpeg',	'Skoda-Felicia-tmavomodra-2012.jpeg',	'2025-08-27 19:41:31'),
(41,	'/www/uploads/user_uploads/68af6140a4f26.jpeg',	'4b3b2c41-c91e-4e17-bb40-970452e47564.jpeg',	'2025-08-27 19:49:20');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci NOT NULL,
  `firstname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci NOT NULL,
  `lastname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci NOT NULL,
  `role` enum('UZIVATEL','ADMIN','DEVELOPER') CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci NOT NULL DEFAULT 'UZIVATEL',
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
(9,	'martin',	'Martin',	'Burda',	'burdadko.cz@gmail.com',	'$2y$10$GWyEYwqs8lnAKXVZaQNLpuYPZXCFmFsTNPNe2YWkhogfgg5NNBLUK',	'ADMIN',	'shrekova bazina 13',	'Praha',	'53803',	'2025-07-03 16:48:05',	146472,	'2025-08-11 10:40:44',	'111222333'),
(18,	'repair_david',	'Martin',	'Burda',	'xmanmartinburda@seznam.cz',	'$2y$10$./dag8joMFDQREOVQ0kEIuWsMI8C1o9XdlCj7t2wIpGNjlxRcm57m',	'UZIVATEL',	NULL,	NULL,	NULL,	'2025-08-10 17:24:30',	NULL,	NULL,	NULL),
(19,	'martinj569',	'karel',	'Jegej',	'john.s@gmail.com',	'$2y$10$nRHzitWC/nzSvtcwyUBzTOUyp6stFhi.bm6RSOhcxkxC3RF7tQXcO',	'UZIVATEL',	NULL,	NULL,	NULL,	'2025-08-11 10:26:28',	NULL,	NULL,	NULL),
(20,	'kkv1s',	'Karel',	'Novák',	'superadmin@superadmin.cz',	'$2y$10$GWyEYwqs8lnAKXVZaQNLpuYPZXCFmFsTNPNe2YWkhogfgg5NNBLUK',	'DEVELOPER',	'123',	'456',	'111111',	'2025-06-24 11:30:58',	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `vendor_payment_methods`;
CREATE TABLE `vendor_payment_methods` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vendor_id` int NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `price_eur` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Price in EUR',
  PRIMARY KEY (`id`),
  KEY `vendor_id` (`vendor_id`),
  CONSTRAINT `vendor_payment_methods_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `vendor_payment_methods` (`id`, `vendor_id`, `code`, `name`, `price`, `price_eur`) VALUES
(4,	7,	'prevod',	'převod',	0.00,	0.00),
(5,	7,	'dobirka',	'dobírka',	23.00,	0.00),
(6,	6,	'prevod',	'převod',	0.00,	0.00),
(7,	6,	'dobirka',	'dobírka',	12.00,	0.00),
(8,	8,	'credit_card',	'Credit Card',	0.00,	0.00),
(9,	8,	'paypal',	'PayPal',	50.00,	2.00);

DROP TABLE IF EXISTS `vendors`;
CREATE TABLE `vendors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `supported_lang` varchar(5) DEFAULT 'cs' COMMENT 'Supported languages: cs, en, or both (comma-separated)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `vendors` (`id`, `name`, `supported_lang`) VALUES
(6,	'Balíkovna',	'cs'),
(7,	'Zásilkovna',	'cs'),
(8,	'DHL International',	'en'),
(11,	'packeta',	'en'),
(12,	'test',	'en');