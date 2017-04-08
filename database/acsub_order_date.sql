-- phpMyAdmin SQL Dump
-- version 4.4.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 08, 2017 at 11:38 AM
-- Server version: 5.6.25
-- PHP Version: 5.6.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aroma_new`
--

-- --------------------------------------------------------

--
-- Table structure for table `acsub_order_date`
--

DROP TABLE IF EXISTS `acsub_order_date`;
CREATE TABLE IF NOT EXISTS `acsub_order_date` (
  `id` bigint(20) NOT NULL,
  `eav_sub_id` bigint(20) NOT NULL,
  `ship_date` datetime NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `acsub_order_date`
--
ALTER TABLE `acsub_order_date`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `acsub_order_date`
--
ALTER TABLE `acsub_order_date`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
