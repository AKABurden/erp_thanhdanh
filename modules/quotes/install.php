<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

$CI->db->query("CREATE TABLE IF NOT EXISTS `tbl_quotes` (
	`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`parent_id` int(11) DEFAULT 0,
	`date` datetime NOT NULL,
	`reference_no` varchar(255) COLLATE utf8_unicode_ci NOT NULL UNIQUE,
	`customer_id` int(11) NOT NULL,
	`validity` datetime DEFAULT NULL,
	`freight_insirance` double DEFAULT 0,
	`count_items` int(11) DEFAULT 0,
	`total` double DEFAULT 0,
	`count_charge` int(11) DEFAULT 0,
	`total_quantity` double DEFAULT 0,
	`total_charge` double DEFAULT 0,
	`total_quantity_charge` double DEFAULT 0,
	`grand_total` double DEFAULT 0,
	`parts_origin` text COLLATE utf8_unicode_ci DEFAULT NULL,
	`date_created` datetime DEFAULT NULL,
	`created_by` int(11) DEFAULT NULL,
	`date_updated` datetime DEFAULT NULL,
	`updated_by` int(11) DEFAULT NULL,
	`status` text COLLATE utf8_unicode_ci DEFAULT NULL,
	`user_status` int(11) DEFAULT NULL,
	`date_status` datetime DEFAULT NULL,
	`update_on_items` int(11) DEFAULT 0,
	`note` text COLLATE utf8_unicode_ci DEFAULT NULL,
	`delivery` text COLLATE utf8_unicode_ci DEFAULT NULL,
	`installation_cost` text COLLATE utf8_unicode_ci DEFAULT NULL,
	`order_id` int(11) DEFAULT 0
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

$CI->db->query("CREATE TABLE IF NOT EXISTS `tbl_quote_items` (
	`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`quote_id` int(11) NOT NULL,
	`type_item` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
	`item_id` int(11) NOT NULL,
	`item_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
	`item_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
	`origin` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
	`quantity` double DEFAULT 0,
	`unit_price` double DEFAULT 0,
	`total_amount` double DEFAULT 0,
	`note_item` text COLLATE utf8_unicode_ci DEFAULT NULL,
	`lead_time` float DEFAULT 0,
	`info` text COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");


$CI->db->query("CREATE TABLE IF NOT EXISTS `tbl_quote_charges` (
	`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`quote_id` int(11) NOT NULL,
	`name_charge` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
	`quantity_charge` double DEFAULT 0,
	`price_charge` double DEFAULT 0,
	`total_amount_charge` double DEFAULT 0
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

$CI->db->query("CREATE TABLE IF NOT EXISTS `tbl_quote_payments` (
	`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`quote_id` int(11) NOT NULL,
	`name` varchar(555) COLLATE utf8_unicode_ci DEFAULT NULL,
	`number` float DEFAULT 0
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");