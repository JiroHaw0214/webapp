-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 31, 2023 at 06:38 AM
-- Server version: 8.0.31
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jirohaw`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `description` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `category_name`, `description`) VALUES
(4, 'Clothing', 'Category Used To Save Clothing Products'),
(5, 'Pants', 'Category Used To Save Pant Products');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
CREATE TABLE IF NOT EXISTS `contacts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `email` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `message` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `password` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `first_name` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `last_name` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `email` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `gender` enum('Male','Female','Other') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `date_of_birth` date NOT NULL,
  `registration_datetime` datetime NOT NULL,
  `account_status` enum('Active','Inactive') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `image` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `username`, `password`, `first_name`, `last_name`, `email`, `gender`, `date_of_birth`, `registration_datetime`, `account_status`, `image`) VALUES
(31, 'JiroHaw0214', '$2y$10$CD.0w1huL4fHBH1ywEL3qOWCyYI71OYcTdbb.ZWqWvzhofMcxXB6.', 'Haw', 'EngHong', 'hawenghong0214@e.newera.edu.my', 'Male', '2002-02-14', '2023-08-27 19:51:49', 'Active', 'b9c5db97bceed0143ee0194f5aea9e648bc4b686-hawenghong.jpg'),
(23, 'Enicia1115', '$2y$10$R9LtTN.j5l/cwG8VkrpXWu5HYKwpwwNnw4avM6TFGEPsb095cGey.', 'Tan', 'Xing Yu', 'eniciatan1115@gmail.com', 'Female', '2001-11-15', '2023-08-22 09:36:16', 'Active', 'fb2fad32c7cd4bc4e8def84b38e6975692c581ee-TanXingYu.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
CREATE TABLE IF NOT EXISTS `files` (
  `id` int NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `file_path` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

DROP TABLE IF EXISTS `order_details`;
CREATE TABLE IF NOT EXISTS `order_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=215 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`) VALUES
(197, 101, 51, 4),
(196, 101, 50, 5),
(195, 101, 53, 3),
(194, 101, 54, 2),
(193, 101, 55, 1),
(208, 102, 50, 1),
(214, 105, 51, 2);

-- --------------------------------------------------------

--
-- Table structure for table `order_summary`
--

DROP TABLE IF EXISTS `order_summary`;
CREATE TABLE IF NOT EXISTS `order_summary` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `order_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=106 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `order_summary`
--

INSERT INTO `order_summary` (`id`, `customer_id`, `order_date`) VALUES
(101, 31, '2023-08-27 23:17:29'),
(102, 23, '2023-08-28 04:48:17'),
(105, 31, '2023-08-28 14:31:41');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `price` double NOT NULL,
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `promotion_price` decimal(10,2) DEFAULT NULL,
  `manufacture_date` date DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `created`, `modified`, `promotion_price`, `manufacture_date`, `expired_date`, `category_id`, `image`) VALUES
(50, 'Men\'s Printed Short Sleeve T-Shirt', 'Short Sleeve T Shirt Men Street Summer Anime Loose Round Collar Casual Top', 6.49, '2023-08-25 09:07:35', '2023-08-25 09:07:35', NULL, '2023-08-25', NULL, 4, 'f1983943ca4de444a428f93134f4458fe47b751c-Product1.jpeg'),
(51, 'Custom Made T-Shirt', 'Embroidery/Print Shirt | Polo Shirt | Shirt Order | Comoany TShirt', 7, '2023-08-25 09:13:13', '2023-08-27 11:00:26', '0.00', '2023-08-25', NULL, 4, 'f4d2fcd5afc0d559fd05fbb0e51ef4d0aa580dd4-product2.jpeg'),
(53, 'New Oversize Cartoon Pattern T-Shirt', 'women\'s clothing, curvy girls fashion Korean style loose vintage shirt', 3.99, '2023-08-25 09:18:59', '2023-08-27 10:59:52', NULL, '2023-08-25', NULL, 4, 'e28275f898ed56409b305b70a3cd464c8f9c9628-product3.jpeg'),
(54, 'PRIA Korean T-SHIRT', 'NCT Dream Logo KPOP Shirt || Cotton Combed T-Shirt Men &amp; Women Short Sleeve (NCT Green) Tee | Kaos KOREA T', 9.5, '2023-08-25 09:20:50', '2023-08-27 10:59:05', '0.00', '2023-08-25', NULL, 4, '3a58867d0119a5fccaa39aaa39a2d9087eafb306-product4.jpeg'),
(55, 'Shorts Thin Section', 'Thin Casual Shorts Men\'s Quarter Pants Trend Quick-Drying Pants Majine', 16, '2023-08-25 09:25:54', '2023-08-27 10:57:54', '0.00', '2023-08-25', NULL, 5, 'd4ce9a120083ce4818391581edeb1421ee6d4908-product5.jpeg'),
(56, 'Men\'s Summer Shorts', 'Thin Casual Sports Loose Beach Pants Breeches Men\'s Trendy Pants', 14.63, '2023-08-25 09:28:43', '2023-08-25 09:28:43', NULL, '2023-08-25', NULL, 4, '765f60d92bb6dfef7225231f2defe19c781ca644-product6.jpeg'),
(57, 'Men\'s Spring and Autumn Waffle Casual Pants', '2022 Trendy Sports Trousers Loose Banded Foot Labeled Sweatpants 2.25', 11.45, '2023-08-25 09:33:51', '2023-08-28 06:50:17', '0.00', '2023-08-25', NULL, 5, '552890c13deb32ed9afc3c8ed300c17b42fea628-product7.jpeg');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
