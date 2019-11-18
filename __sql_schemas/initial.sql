-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Фев 01 2016 г., 12:05
-- Версия сервера: 5.5.46-0ubuntu0.14.04.2-log
-- Версия PHP: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `autoparts`
--

-- --------------------------------------------------------

--
-- Структура таблицы `brandrules`
--

CREATE TABLE IF NOT EXISTS `brandrules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) NOT NULL,
  `type` enum('delete_start','delete_end') NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=446 ;

-- --------------------------------------------------------

--
-- Структура таблицы `brands`
--

CREATE TABLE IF NOT EXISTS `brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand` varchar(255) NOT NULL,
  `brand_long` varchar(255) NOT NULL,
  `change_to` varchar(255) DEFAULT NULL,
  `change_to_short` varchar(255) DEFAULT NULL,
  `operation_id` int(11) NOT NULL,
  `tecdoc_id` int(11) NOT NULL,
  `dont_upload` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `brand` (`brand`,`operation_id`),
  KEY `change_to_short` (`change_to_short`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=182889 ;

-- --------------------------------------------------------

--
-- Структура таблицы `cars`
--

CREATE TABLE IF NOT EXISTS `cars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `brand` varchar(64) NOT NULL,
  `model` varchar(64) NOT NULL,
  `vin` varchar(64) NOT NULL,
  `engine` varchar(64) NOT NULL,
  `year` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=322 ;

-- --------------------------------------------------------

--
-- Структура таблицы `carsales`
--

CREATE TABLE IF NOT EXISTS `carsales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `manufacturer` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `modification` varchar(255) DEFAULT NULL,
  `year` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `transmission` varchar(255) DEFAULT NULL,
  `parts_number` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `volume` varchar(255) DEFAULT NULL,
  `price` varchar(64) DEFAULT NULL,
  `photo1` varchar(255) DEFAULT NULL,
  `photo2` varchar(255) DEFAULT NULL,
  `photo3` varchar(255) DEFAULT NULL,
  `status` enum('new','in_progres','done') NOT NULL DEFAULT 'new',
  `date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Структура таблицы `catalog_from_ip`
--

CREATE TABLE IF NOT EXISTS `catalog_from_ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_ip` varchar(16) NOT NULL,
  `total_count` int(11) NOT NULL DEFAULT '0',
  `last_count` int(11) NOT NULL DEFAULT '0',
  `last_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `start_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `banned` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `client_ip` (`client_ip`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17688 ;

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `tecdoc_ids` varchar(255) DEFAULT NULL,
  `level` int(3) NOT NULL,
  `sorting` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`,`tecdoc_ids`,`level`),
  KEY `slug` (`slug`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=889 ;

-- --------------------------------------------------------

--
-- Структура таблицы `clients`
--

CREATE TABLE IF NOT EXISTS `clients` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `phone` varchar(64) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(128) NOT NULL,
  `additional_phone` varchar(64) NOT NULL,
  `delivery_method_id` int(10) NOT NULL,
  `delivery_address` varchar(512) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `manager_id` int(10) NOT NULL,
  `discount_id` int(10) DEFAULT NULL,
  `comment` varchar(1024) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `activation_key` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `phone` (`phone`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=8031 ;

-- --------------------------------------------------------

--
-- Структура таблицы `client_payments`
--

CREATE TABLE IF NOT EXISTS `client_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `value` double NOT NULL,
  `comment_text` varchar(255) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=6113 ;

-- --------------------------------------------------------

--
-- Структура таблицы `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `comment` text NOT NULL,
  `date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=106 ;

-- --------------------------------------------------------

--
-- Структура таблицы `crosses`
--

CREATE TABLE IF NOT EXISTS `crosses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_id` int(11) DEFAULT NULL,
  `from_art` varchar(255) DEFAULT NULL,
  `from_brand` varchar(255) DEFAULT NULL,
  `to_id` int(11) DEFAULT NULL,
  `to_art` varchar(255) DEFAULT NULL,
  `to_brand` varchar(255) DEFAULT NULL,
  `operation_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `from_id` (`from_id`,`to_id`),
  KEY `operation_id` (`operation_id`),
  KEY `from_art` (`from_art`,`from_brand`),
  KEY `to_art` (`to_art`,`to_brand`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9688104 ;

-- --------------------------------------------------------

--
-- Структура таблицы `currencies`
--

CREATE TABLE IF NOT EXISTS `currencies` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `ratio` double NOT NULL,
  `code` enum('UAH','USD','EUR','PLN') DEFAULT 'UAH',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Структура таблицы `delivery_methods`
--

CREATE TABLE IF NOT EXISTS `delivery_methods` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Структура таблицы `discounts`
--

CREATE TABLE IF NOT EXISTS `discounts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `standart` tinyint(1) NOT NULL DEFAULT '0',
  `admin_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Структура таблицы `discount_limits`
--

CREATE TABLE IF NOT EXISTS `discount_limits` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `from` double DEFAULT NULL,
  `to` double DEFAULT NULL,
  `percentage` int(3) NOT NULL,
  `discount_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=984 ;

-- --------------------------------------------------------

--
-- Структура таблицы `find_from_ip`
--

CREATE TABLE IF NOT EXISTS `find_from_ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_ip` varchar(16) NOT NULL,
  `total_count` int(11) NOT NULL DEFAULT '0',
  `last_count` int(11) NOT NULL DEFAULT '0',
  `last_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `start_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `banned` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `client_ip` (`client_ip`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=44319 ;

-- --------------------------------------------------------

--
-- Структура таблицы `menus`
--

CREATE TABLE IF NOT EXISTS `menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `identifier` varchar(64) NOT NULL,
  `max_levels` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Структура таблицы `menu_items`
--

CREATE TABLE IF NOT EXISTS `menu_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `link_title` varchar(255) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `page_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `order_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Структура таблицы `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=45 ;

-- --------------------------------------------------------

--
-- Структура таблицы `operations`
--

CREATE TABLE IF NOT EXISTS `operations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `description` varchar(255) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=899 ;

-- --------------------------------------------------------

--
-- Структура таблицы `orderitems`
--

CREATE TABLE IF NOT EXISTS `orderitems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `article` varchar(128) NOT NULL,
  `brand` varchar(128) NOT NULL,
  `name` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL,
  `delivery_days` varchar(64) DEFAULT NULL,
  `purchase_per_unit` double NOT NULL,
  `purchase_per_unit_in_currency` double DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `delivery_price` double NOT NULL,
  `sale_per_unit` double NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `salary` tinyint(1) NOT NULL DEFAULT '0',
  `discount_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=12157 ;

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `client_id` int(11) NOT NULL,
  `delivery_method_id` int(11) NOT NULL,
  `manager_id` int(11) NOT NULL,
  `delivery_address` varchar(512) NOT NULL,
  `ttn` varchar(256) DEFAULT NULL,
  `manager_comment` varchar(512) NOT NULL,
  `client_comment` varchar(512) NOT NULL,
  `salary` tinyint(1) NOT NULL DEFAULT '0',
  `archive` tinyint(1) NOT NULL DEFAULT '0',
  `online` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=5950 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `syn` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `meta_keywords` varchar(255) NOT NULL,
  `meta_description` varchar(255) NOT NULL,
  `h1_title` varchar(255) NOT NULL,
  `params` text NOT NULL,
  `content` text NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Структура таблицы `parts`
--

CREATE TABLE IF NOT EXISTS `parts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `operation_id` int(11) DEFAULT NULL,
  `tecdoc_id` int(11) DEFAULT NULL,
  `article` varchar(255) NOT NULL,
  `article_long` varchar(255) NOT NULL,
  `brand` varchar(255) NOT NULL,
  `brand_long` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `operation_id` (`operation_id`),
  KEY `article` (`article`),
  KEY `brand` (`brand`),
  KEY `tecdoc_id` (`tecdoc_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1470778 ;

-- --------------------------------------------------------

--
-- Структура таблицы `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=52 ;

-- --------------------------------------------------------

--
-- Структура таблицы `priceitems`
--

CREATE TABLE IF NOT EXISTS `priceitems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `part_id` int(11) NOT NULL,
  `price` double NOT NULL,
  `currency_id` int(11) NOT NULL,
  `amount` varchar(64) DEFAULT NULL,
  `delivery` varchar(64) DEFAULT NULL,
  `supplier_id` int(11) NOT NULL,
  `operation_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `part_id` (`part_id`,`supplier_id`),
  KEY `operation_id` (`operation_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37048030 ;

-- --------------------------------------------------------

--
-- Структура таблицы `price_templates`
--

CREATE TABLE IF NOT EXISTS `price_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(11) NOT NULL,
  `json_data` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=93 ;

-- --------------------------------------------------------

--
-- Структура таблицы `requests`
--

CREATE TABLE IF NOT EXISTS `requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `manufacturer` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `modification` varchar(255) DEFAULT NULL,
  `year` varchar(255) DEFAULT NULL,
  `vin` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `volume` varchar(255) DEFAULT NULL,
  `status` enum('new','in_progres','done') NOT NULL DEFAULT 'new',
  `date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=251 ;

-- --------------------------------------------------------

--
-- Структура таблицы `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Структура таблицы `roles_permissions`
--

CREATE TABLE IF NOT EXISTS `roles_permissions` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `role_id` int(10) NOT NULL,
  `permission_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=3467 ;

-- --------------------------------------------------------

--
-- Структура таблицы `roles_users`
--

CREATE TABLE IF NOT EXISTS `roles_users` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `fk_role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `salaries`
--

CREATE TABLE IF NOT EXISTS `salaries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_id` int(11) NOT NULL,
  `to_id` int(11) NOT NULL,
  `percentage` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=53 ;

-- --------------------------------------------------------

--
-- Структура таблицы `seodata`
--

CREATE TABLE IF NOT EXISTS `seodata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seo_identifier` varchar(255) NOT NULL,
  `h1` varchar(255) NOT NULL,
  `description` varchar(512) NOT NULL,
  `keywords` varchar(512) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `seo_identifier` (`seo_identifier`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=466343 ;

-- --------------------------------------------------------

--
-- Структура таблицы `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_name` varchar(64) NOT NULL,
  `title` varchar(64) NOT NULL,
  `value` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Структура таблицы `states`
--

CREATE TABLE IF NOT EXISTS `states` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text_id` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `description` varchar(255) NOT NULL,
  `img` varchar(64) NOT NULL,
  `bg_color` varchar(64) NOT NULL,
  `font_color` varchar(64) NOT NULL,
  `payment` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `text_id` (`text_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Структура таблицы `suppliers`
--

CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `phone` varchar(64) NOT NULL,
  `delivery_days` varchar(16) NOT NULL,
  `currency_id` int(10) NOT NULL,
  `сomment_text` text NOT NULL,
  `price_source` varchar(255) DEFAULT NULL,
  `notice` text NOT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_count` int(11) DEFAULT '0',
  `dont_show` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('ready','error','process') NOT NULL DEFAULT 'ready',
  `error` text NOT NULL,
  `total_processed` int(11) NOT NULL,
  `total_upload` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=132 ;

-- --------------------------------------------------------

--
-- Структура таблицы `supplier_payments`
--

CREATE TABLE IF NOT EXISTS `supplier_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(11) NOT NULL,
  `date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `value` double NOT NULL,
  `comment_text` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=4877 ;

-- --------------------------------------------------------

--
-- Структура таблицы `tof_articles`
--

CREATE TABLE IF NOT EXISTS `tof_articles` (
  `id` int(11) NOT NULL DEFAULT '0',
  `article_nr` varchar(80) DEFAULT NULL,
  `art` varchar(80) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `description` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `art` (`art`),
  KEY `article_nr` (`article_nr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tof_articles_lookup`
--

CREATE TABLE IF NOT EXISTS `tof_articles_lookup` (
  `article_id` int(11) DEFAULT NULL,
  `search` varchar(105) DEFAULT NULL,
  `display` varchar(105) DEFAULT NULL,
  `article_type` smallint(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  KEY `article_id` (`article_id`),
  KEY `search` (`search`),
  KEY `article_type` (`article_type`),
  KEY `brand_id` (`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tof_article_criteria`
--

CREATE TABLE IF NOT EXISTS `tof_article_criteria` (
  `article_id` int(11) DEFAULT NULL,
  `criteria_id` int(11) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  KEY `article_id` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tof_article_info`
--

CREATE TABLE IF NOT EXISTS `tof_article_info` (
  `article_id` int(11) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `description` text,
  KEY `article_id` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tof_brands`
--

CREATE TABLE IF NOT EXISTS `tof_brands` (
  `id` int(11) NOT NULL DEFAULT '0',
  `brand` varchar(100) DEFAULT NULL,
  `brand_short` varchar(100) DEFAULT NULL,
  `brand_code` varchar(100) DEFAULT NULL,
  `mf_nr` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `brand` (`brand`),
  KEY `brand_short` (`brand_short`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tof_criteria`
--

CREATE TABLE IF NOT EXISTS `tof_criteria` (
  `id` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `unit` int(11) DEFAULT NULL,
  `type` varchar(6) DEFAULT NULL,
  `is_interval` int(5) DEFAULT NULL,
  `successor` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tof_engines`
--

CREATE TABLE IF NOT EXISTS `tof_engines` (
  `id` int(11) NOT NULL,
  `engine_code` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tof_generic_articles`
--

CREATE TABLE IF NOT EXISTS `tof_generic_articles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `standart_name` varchar(255) DEFAULT NULL,
  `assembly` varchar(255) DEFAULT NULL,
  `intended` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tof_graphics`
--

CREATE TABLE IF NOT EXISTS `tof_graphics` (
  `article_id` int(11) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `ext` varchar(4) DEFAULT NULL,
  KEY `article_id` (`article_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tof_link_article`
--

CREATE TABLE IF NOT EXISTS `tof_link_article` (
  `id` int(11) NOT NULL DEFAULT '0',
  `article_id` int(11) DEFAULT NULL,
  `generic_article_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  KEY `generic_article_id` (`generic_article_id`),
  KEY `article_id_2` (`article_id`,`generic_article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tof_link_article_criteria`
--

CREATE TABLE IF NOT EXISTS `tof_link_article_criteria` (
  `link_article_id` int(11) DEFAULT NULL,
  `criteria_id` int(11) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  KEY `link_article_id` (`link_article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tof_link_generic_article_search_tree`
--

CREATE TABLE IF NOT EXISTS `tof_link_generic_article_search_tree` (
  `search_tree_id` int(11) DEFAULT NULL,
  `generic_article_id` int(11) DEFAULT NULL,
  KEY `search_tree_id` (`search_tree_id`),
  KEY `generic_article_id` (`generic_article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tof_link_type_article`
--

CREATE TABLE IF NOT EXISTS `tof_link_type_article` (
  `type_id` int(11) DEFAULT NULL,
  `link_article_id` int(11) DEFAULT NULL,
  `generic_article_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  KEY `supplier_id` (`supplier_id`),
  KEY `generic_article_id` (`generic_article_id`),
  KEY `link_article_id` (`link_article_id`),
  KEY `type_id` (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tof_link_typ_eng`
--

CREATE TABLE IF NOT EXISTS `tof_link_typ_eng` (
  `typ_id` int(11) DEFAULT NULL,
  `eng_id` int(11) DEFAULT NULL,
  KEY `typ_id` (`typ_id`),
  KEY `eng_id` (`eng_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tof_manufacturers`
--

CREATE TABLE IF NOT EXISTS `tof_manufacturers` (
  `id` int(11) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `passenger_car` tinyint(4) DEFAULT NULL,
  `commercial_vehicle` tinyint(4) DEFAULT NULL,
  `axle` tinyint(4) DEFAULT NULL,
  `engine` tinyint(4) DEFAULT NULL,
  `engine_type` tinyint(4) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `number` smallint(6) DEFAULT NULL,
  `description` text,
  `logo` varchar(255) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tof_models`
--

CREATE TABLE IF NOT EXISTS `tof_models` (
  `id` int(11) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `manufacturer_id` int(11) DEFAULT NULL,
  `description_id` int(11) DEFAULT NULL,
  `start_date` int(6) DEFAULT NULL,
  `end_date` int(6) DEFAULT NULL,
  `passenger_car` tinyint(4) DEFAULT NULL,
  `commercial_vehicle` tinyint(4) DEFAULT NULL,
  `axle` tinyint(4) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `modified` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `manufacturer_id` (`manufacturer_id`),
  KEY `short_description` (`short_description`),
  KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tof_search_tree`
--

CREATE TABLE IF NOT EXISTS `tof_search_tree` (
  `id` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) DEFAULT NULL,
  `type` smallint(2) DEFAULT NULL,
  `level` smallint(2) DEFAULT NULL,
  `node_number` int(11) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `text` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `level` (`level`),
  KEY `sort` (`sort`),
  KEY `type` (`type`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tof_suppliers`
--

CREATE TABLE IF NOT EXISTS `tof_suppliers` (
  `id` int(11) NOT NULL DEFAULT '0',
  `brand` varchar(100) DEFAULT NULL,
  `brand_short` varchar(100) DEFAULT NULL,
  `alias` varchar(100) DEFAULT NULL,
  `supplier_nr` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `brand` (`brand`),
  KEY `brand_short` (`brand_short`),
  KEY `supplier_nr` (`supplier_nr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tof_types`
--

CREATE TABLE IF NOT EXISTS `tof_types` (
  `id` int(11) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `model_id` int(11) DEFAULT NULL,
  `start_date` int(6) DEFAULT NULL,
  `end_date` int(6) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `full_description` varchar(255) DEFAULT NULL,
  `capacity` float(5,1) DEFAULT NULL,
  `capacity_hp_from` int(5) DEFAULT NULL,
  `capacity_kw_from` int(5) DEFAULT NULL,
  `engine_type` varchar(100) DEFAULT NULL,
  `body_type` varchar(100) DEFAULT NULL,
  `drive_type` varchar(100) DEFAULT NULL,
  `modified` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `slug` (`slug`),
  KEY `id` (`id`,`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `unmatched`
--

CREATE TABLE IF NOT EXISTS `unmatched` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand` varchar(64) NOT NULL,
  `article` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` double NOT NULL,
  `currency_id` int(11) NOT NULL,
  `amount` varchar(64) DEFAULT NULL,
  `delivery` varchar(64) DEFAULT NULL,
  `supplier_id` int(11) NOT NULL,
  `operation_id` int(11) DEFAULT NULL,
  `reason` enum('bad_brand','bad_article','else') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `operation_id` (`operation_id`),
  KEY `brand` (`brand`),
  KEY `article` (`article`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12879953 ;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(127) NOT NULL,
  `username` varchar(32) NOT NULL DEFAULT '',
  `name` varchar(64) NOT NULL,
  `surname` varchar(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  `logins` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login` int(10) unsigned DEFAULT NULL,
  `dont_show_salary` tinyint(1) NOT NULL DEFAULT '0',
  `show_salary_only_me` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=48 ;

-- --------------------------------------------------------

--
-- Структура таблицы `user_tokens`
--

CREATE TABLE IF NOT EXISTS `user_tokens` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `user_agent` varchar(40) NOT NULL,
  `token` varchar(40) NOT NULL,
  `type` varchar(100) NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_token` (`token`),
  KEY `fk_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `roles_users`
--
ALTER TABLE `roles_users`
  ADD CONSTRAINT `roles_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_users_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
