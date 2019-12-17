-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 16, 2019 at 11:19 AM
-- Server version: 5.7.28-0ubuntu0.19.04.2
-- PHP Version: 7.2.24-0ubuntu0.19.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project3`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `username` varchar(50) NOT NULL,
  `password` varchar(65) NOT NULL,
  `verified` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `accountRoles`
--

CREATE TABLE `accountRoles` (
  `username` varchar(30) NOT NULL,
  `roleId` varchar(20) NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ActivationToken`
--

CREATE TABLE `ActivationToken` (
  `username` varchar(20) NOT NULL,
  `token` varchar(64) NOT NULL,
  `duration` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Board`
--

CREATE TABLE `Board` (
  `username` varchar(45) NOT NULL,
  `location` varchar(45) DEFAULT NULL,
  `chipId` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ChatMessage`
--

CREATE TABLE `ChatMessage` (
  `message` text NOT NULL,
  `timestamp` datetime NOT NULL,
  `username` varchar(20) NOT NULL,
  `gameId` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Chip`
--

CREATE TABLE `Chip` (
  `chipId` varchar(64) NOT NULL,
  `chipColor` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Game`
--

CREATE TABLE `Game` (
  `gameId` varchar(64) DEFAULT NULL,
  `player1` varchar(45) NOT NULL,
  `player2` varchar(45) NOT NULL,
  `challengeId` varchar(64) NOT NULL,
  `winner` varchar(45) DEFAULT NULL,
  `resetGame` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Profile`
--

CREATE TABLE `Profile` (
  `username` varchar(20) NOT NULL,
  `firstname` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `lastname` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `country` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `email` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `name` varchar(20) NOT NULL,
  `roleId` varchar(20) NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `UserLastActivity`
--

CREATE TABLE `UserLastActivity` (
  `username` varchar(45) NOT NULL,
  `timestamp` datetime NOT NULL,
  `activitykey` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `accountRoles`
--
ALTER TABLE `accountRoles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ActivationToken`
--
ALTER TABLE `ActivationToken`
  ADD PRIMARY KEY (`token`);

--
-- Indexes for table `Board`
--
ALTER TABLE `Board`
  ADD PRIMARY KEY (`chipId`);

--
-- Indexes for table `ChatMessage`
--
ALTER TABLE `ChatMessage`
  ADD PRIMARY KEY (`timestamp`);

--
-- Indexes for table `Chip`
--
ALTER TABLE `Chip`
  ADD PRIMARY KEY (`chipId`);

--
-- Indexes for table `Game`
--
ALTER TABLE `Game`
  ADD PRIMARY KEY (`challengeId`);

--
-- Indexes for table `Profile`
--
ALTER TABLE `Profile`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `UserLastActivity`
--
ALTER TABLE `UserLastActivity`
  ADD PRIMARY KEY (`activitykey`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accountRoles`
--
ALTER TABLE `accountRoles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
