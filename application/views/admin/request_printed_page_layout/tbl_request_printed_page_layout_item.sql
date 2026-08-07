-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 21, 2023 at 05:04 PM
-- Server version: 5.7.31
-- PHP Version: 7.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `03121f_21122023`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_request_printed_page_layout_item`
--

DROP TABLE IF EXISTS `tbl_request_printed_page_layout_item`;
CREATE TABLE IF NOT EXISTS `tbl_request_printed_page_layout_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_printed_page_layout_id` int(11) DEFAULT NULL,
  `machines` int(11) DEFAULT NULL,
  `id_products` int(11) DEFAULT NULL,
  `childsheet` double DEFAULT NULL,
  `columnssheets1` double DEFAULT NULL,
  `rowssheets1` double DEFAULT NULL,
  `quantity_print_color1` double DEFAULT NULL,
  `quantity_zinc1` double DEFAULT NULL,
  `number_operations1` double DEFAULT NULL,
  `columnssheets2` double DEFAULT NULL,
  `rowssheets2` double DEFAULT NULL,
  `quantity_print_color2` double DEFAULT NULL,
  `quantity_zinc2` double DEFAULT NULL,
  `number_operations2` double DEFAULT NULL,
  `quantity_total_zinc` double DEFAULT NULL,
  `timequota` double DEFAULT NULL,
  `id_items_stages` int(11) DEFAULT NULL,
  `id_stages` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
