-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 24, 2023 at 05:09 PM
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
-- Table structure for table `tbl_request_calibration`
--

DROP TABLE IF EXISTS `tbl_request_calibration`;
CREATE TABLE IF NOT EXISTS `tbl_request_calibration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `reference_no` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category_calibration` int(11) DEFAULT NULL,
  `bp_calibration` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `detail_calibration` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `quantity` double DEFAULT NULL,
  `price` double DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `machines_id` int(11) DEFAULT NULL,
  `result_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `procedure` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `report` int(255) DEFAULT NULL,
  `standard` int(255) DEFAULT NULL,
  `barcode` int(100) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
