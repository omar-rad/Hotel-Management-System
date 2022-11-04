-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 27, 2022 at 08:00 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.0.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hms`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `confirmation` char(6) NOT NULL,
  `email` varchar(254) NOT NULL,
  `number` smallint(5) UNSIGNED NOT NULL,
  `cin` date NOT NULL,
  `cout` date NOT NULL,
  `guests` tinyint(3) UNSIGNED NOT NULL,
  `tprice` float UNSIGNED NOT NULL,
  `ccn` char(16) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `#` int(10) UNSIGNED NOT NULL,
  `email` varchar(254) NOT NULL,
  `booking` char(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `number` smallint(5) UNSIGNED NOT NULL,
  `floor` tinyint(3) UNSIGNED NOT NULL,
  `type` enum('normal','studio','suite') NOT NULL,
  `capacity` tinyint(3) UNSIGNED NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `wifi` tinyint(1) NOT NULL,
  `breakfast` tinyint(1) NOT NULL,
  `smoking` tinyint(1) NOT NULL,
  `description` text NOT NULL,
  `dir` tinyint(3) UNSIGNED NOT NULL,
  `available` tinyint(1) NOT NULL DEFAULT 1
) ;

-- --------------------------------------------------------

--
-- Table structure for table `support`
--

CREATE TABLE `support` (
  `casen` int(10) UNSIGNED NOT NULL,
  `email` varchar(254) NOT NULL,
  `message` text NOT NULL,
  `open` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `email` varchar(254) NOT NULL,
  `fname` varchar(35) NOT NULL,
  `lname` varchar(35) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `birth` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`confirmation`),
  ADD KEY `email` (`email`),
  ADD KEY `number` (`number`) USING BTREE;

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`#`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`number`);

--
-- Indexes for table `support`
--
ALTER TABLE `support`
  ADD PRIMARY KEY (`casen`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`email`),
  ADD UNIQUE KEY `password` (`password`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `#` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support`
--
ALTER TABLE `support`
  MODIFY `casen` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`email`) REFERENCES `user` (`email`),
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`number`) REFERENCES `room` (`number`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`email`) REFERENCES `user` (`email`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
