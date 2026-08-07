<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

$CI->db->query("CREATE TABLE IF NOT EXISTS `tblseries` (
	`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`id_export_warehouses` int(11) NOT NULL COMMENT 'id xuất kho',
  	`id_export_warehouse_item_id` int(11) DEFAULT 0,
  	`date_export_warehouses` datetime NOT NULL COMMENT 'ngày xuất kho',
  	`id_customer` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'id khách hàng',
  	`type_item` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'loại sản phẩm',
  	`id_item` int(11) NOT NULL COMMENT 'id sản phẩm',
  	`code_item` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'mã sản phẩm',
  	`name_item` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'tên mặt hàng',
  	`series` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'số series',
  	`date_create` date NOT NULL,
  	`staff_create` int(11) NOT NULL,
    `add_new_by_warranty` int(11) DEFAULT 0
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

$CI->db->query("CREATE TABLE IF NOT EXISTS `tblwarranty` (
	`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`id_warranty_receive` int(11) NOT NULL,
  	`code` text COLLATE utf8_unicode_ci NOT NULL,
  	`employees_id` text COLLATE utf8_unicode_ci DEFAULT NULL,
  	`localtion_warranty` text COLLATE utf8_unicode_ci DEFAULT NULL,
    `localtion_warehouse` int(11) DEFAULT NULL,
  	`date_create` datetime NOT NULL,
  	`staff_create` int(11) NOT NULL,
  	`status` int(11) NOT NULL DEFAULT 0,
  	`status_done` int(11) NOT NULL DEFAULT 0 COMMENT 'hoàn thành',
    `type_status_done` int(11) NOT NULL DEFAULT 0 COMMENT 'loại hoàn thành',
  	`staff_status_done` int(11) DEFAULT NULL COMMENT 'người duyệt hoàn thành',
  	`date_status` datetime DEFAULT NULL,
  	`staff_status` int(11) DEFAULT NULL,
  	`not_new_by_staff` text COLLATE utf8_unicode_ci DEFAULT NULL,
    `warehouseman_id` int(11) DEFAULT NULL COMMENT 'id người duyệt kho',
    `warehouseman_date` datetime DEFAULT NULL COMMENT 'ngày duyệt kho'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

$CI->db->query("CREATE TABLE IF NOT EXISTS `tblwarranty_evaluate` (
	`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`id_warranty` int(11) NOT NULL,
  	`points` int(11) NOT NULL COMMENT '1: not happy, 2: happy, 3: very happy',
  	`note` text COLLATE utf8_unicode_ci NOT NULL,
  	`staff_create` int(11) NOT NULL,
  	`date_create` date NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

$CI->db->query("CREATE TABLE IF NOT EXISTS `tblwarranty_expenses` (
	`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`id_warranty` int(11) NOT NULL,
  	`name` text COLLATE utf8_unicode_ci DEFAULT NULL,
  	`type` int(11) NOT NULL DEFAULT 1 COMMENT '1: khách chịu | 2: công ty chịu',
  	`amount` decimal(25,0) NOT NULL DEFAULT 0
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

$CI->db->query("CREATE TABLE IF NOT EXISTS `tblwarranty_export_supplies` (
	`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`id_warranty` int(11) NOT NULL,
  	`date` datetime NOT NULL,
  	`date_deadline` date DEFAULT NULL,
  	`code` text COLLATE utf8_unicode_ci NOT NULL,
  	`name` text COLLATE utf8_unicode_ci NOT NULL,
  	`note` text COLLATE utf8_unicode_ci DEFAULT NULL,
  	`date_create` datetime NOT NULL,
  	`staff_create` int(11) NOT NULL,
  	`status` int(11) NOT NULL DEFAULT 0,
  	`date_status` datetime NOT NULL,
  	`staff_status` int(11) NOT NULL,
  	`not_new_by_staff` text COLLATE utf8_unicode_ci DEFAULT NULL,
  	`id_purchases` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'ds chứa id mua hàng'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

$CI->db->query("CREATE TABLE IF NOT EXISTS `tblwarranty_file` (
	`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`id_warranty_issue` int(11) NOT NULL,
  	`staff_create` int(11) NOT NULL,
  	`date_create` datetime NOT NULL,
  	`name` text COLLATE utf8_unicode_ci NOT NULL,
  	`type` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

$CI->db->query("CREATE TABLE IF NOT EXISTS `tblwarranty_issue` (
	`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`id_warranty_item` int(11) NOT NULL,
  	`id_warranty` int(11) NOT NULL,
  	`id_issue` int(11) DEFAULT NULL,
  	`solution` text COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

$CI->db->query("CREATE TABLE IF NOT EXISTS `tblwarranty_items` (
	`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`id_warranty_receive` int(11) NOT NULL,
  `id_series` int(11) NOT NULL COMMENT 'id table series',
  `warehouse_localtion` int(11) NOT NULL COMMENT 'vị trí kho'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

$CI->db->query("CREATE TABLE IF NOT EXISTS `tblwarranty_number` (
	`warranty_receive` int(11) NOT NULL DEFAULT 1,
  	`warranty` int(11) NOT NULL DEFAULT 1,
  	`export_supplies` int(11) NOT NULL DEFAULT 1,
  	`date` date NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

$CI->db->query("CREATE TABLE IF NOT EXISTS `tblwarranty_receive` (
	`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`code` text COLLATE utf8_unicode_ci NOT NULL,
  	`date` datetime NOT NULL,
  	`date_create` date NOT NULL,
  	`staff_create` int(11) NOT NULL,
  	`customer_id` int(11) NOT NULL,
  	`name_of_machine` text COLLATE utf8_unicode_ci NOT NULL,
  	`service_type` text COLLATE utf8_unicode_ci NOT NULL,
  	`employees_id` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'nhân viên phụ trách',
  	`localtion_warranty` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'bảo hành tại',
  	`warranty_end_date` datetime DEFAULT NULL,
  	`engineer_start_date` datetime DEFAULT NULL,
  	`finish_date` datetime DEFAULT NULL,
  	`feedback` text COLLATE utf8_unicode_ci DEFAULT NULL,
  	`satisfaction` text COLLATE utf8_unicode_ci DEFAULT NULL,
  	`status` int(11) NOT NULL DEFAULT 0,
  	`staff_status` int(11) DEFAULT NULL,
  	`date_status` datetime DEFAULT NULL,
  	`not_new_by_staff` text COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

$CI->db->query("CREATE TABLE IF NOT EXISTS `tblwarranty_supplies` (
	`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`id_warranty` int(11) NOT NULL,
  	`type_item` text COLLATE utf8_unicode_ci NOT NULL,
  	`id_item` int(11) NOT NULL,
  	`quantity` decimal(25,0) NOT NULL DEFAULT 0,
  	`type_amount` int(11) NOT NULL DEFAULT 2 COMMENT '1: hỗ trợ || 2: tính phí',
  	`amount` decimal(25,0) NOT NULL DEFAULT 0,
  	`total` decimal(25,0) NOT NULL DEFAULT 0,
  	`note` text COLLATE utf8_unicode_ci DEFAULT NULL,
    `additional_supplies` int(11) NOT NULL DEFAULT 0 COMMENT 'vật tư bổ sung',
    `export_warehouse` int(11) NOT NULL DEFAULT 0 COMMENT '0: chưa xuất kho | >0: sl đã xuất kho'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

$CI->db->query("CREATE TABLE IF NOT EXISTS `tblissue` (
	`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`name` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");


$CI->db->query("ALTER TABLE tblexport_different ADD id_warranty_export_supplies INT(11) NULL DEFAULT NULL AFTER warehouseman_date");
$CI->db->query("ALTER TABLE tblexport_different ADD id_warranty_export_supplies_done INT(11) NULL DEFAULT NULL AFTER id_warranty_export_supplies");

$CI->db->query("ALTER TABLE tbl_products ADD warranty INT(11) NOT NULL DEFAULT '0' COMMENT 'số tháng bảo hàng' AFTER calculated_on_sales");

$CI->db->query("ALTER TABLE tblwarehouse_product ADD series INT(11) NOT NULL COMMENT 'id của series' AFTER type_transfer");
$CI->db->query("ALTER TABLE tblwarehouse_items ADD series INT(11) NOT NULL AFTER type_items");
$CI->db->query("ALTER TABLE tblwarehouse_items ADD type_series INT(11) NULL DEFAULT NULL COMMENT '1: tiếp nhận | 2: thu hồi' AFTER series");
$CI->db->query("ALTER TABLE `01419f_server_new`.`tblwarehouse_product` DROP INDEX `import_id`, ADD UNIQUE `import_id` (`import_id`, `product_id`, `type_export`, `type_items`, `localtion`, `type_transfer`, `series`) USING BTREE");