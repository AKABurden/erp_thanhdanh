-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 26, 2023 at 04:17 PM
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
-- Table structure for table `tbl_request_client_complaints`
--

DROP TABLE IF EXISTS `tbl_request_client_complaints`;
CREATE TABLE IF NOT EXISTS `tbl_request_client_complaints` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `employees` int(11) DEFAULT NULL,
  `category_complaints` int(11) DEFAULT NULL,
  `detail_complaints` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `staff_tn` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `timequota` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `causal` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `processing_procedures` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prevention_procedures` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `result_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `barcode` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
