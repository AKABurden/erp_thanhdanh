-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 24, 2023 at 05:10 PM
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
-- Table structure for table `tbl_request_export_products_item`
--

DROP TABLE IF EXISTS `tbl_request_export_products_item`;
CREATE TABLE IF NOT EXISTS `tbl_request_export_products_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_export_products_id` int(11) DEFAULT NULL,
  `order_item_id` int(11) DEFAULT NULL,
  `pod_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `type_item` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `quantity` double DEFAULT NULL,
  `quabtity_manufactures` double DEFAULT NULL,
  `quabtity_allow` double DEFAULT NULL,
  `quabtity_purchase` double DEFAULT NULL,
  `totalcon` double DEFAULT NULL,
  `totalkien` double DEFAULT NULL,
  `totalkg` double DEFAULT NULL,
  `totalallkien` double DEFAULT NULL,
  `timequota` double DEFAULT NULL,
  `timeregulations` double DEFAULT NULL,
  `expected_date` datetime DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
