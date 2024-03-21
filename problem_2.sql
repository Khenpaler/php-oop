-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 21, 2024 at 01:24 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `problem_2`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders_table`
--

CREATE TABLE `orders_table` (
  `orderid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `customer` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders_table`
--

INSERT INTO `orders_table` (`orderid`, `name`, `price`, `status`, `customer`) VALUES
(3, 'Android', '10000', 0, 'Kent'),
(4, 'Samsung', '10000', 0, 'Justine'),
(5, 'Iphone', '20000', 0, 'Juan'),
(6, 'Iphone', '20000', 0, 'Kent');

-- --------------------------------------------------------

--
-- Table structure for table `roles_table`
--

CREATE TABLE `roles_table` (
  `roleid` int(11) NOT NULL,
  `role` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles_table`
--

INSERT INTO `roles_table` (`roleid`, `role`, `description`) VALUES
(1, 'Admin', NULL),
(2, 'Cashier', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_table`
--

CREATE TABLE `users_table` (
  `userid` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `roleid` int(11) NOT NULL DEFAULT 1,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `is_logged_in` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_table`
--

INSERT INTO `users_table` (`userid`, `username`, `password`, `roleid`, `status`, `is_logged_in`) VALUES
(1, 'admin', 'admin', 1, 1, 0),
(2, 'cashier', 'cashier', 2, 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders_table`
--
ALTER TABLE `orders_table`
  ADD PRIMARY KEY (`orderid`);

--
-- Indexes for table `roles_table`
--
ALTER TABLE `roles_table`
  ADD PRIMARY KEY (`roleid`);

--
-- Indexes for table `users_table`
--
ALTER TABLE `users_table`
  ADD PRIMARY KEY (`userid`),
  ADD KEY `roleid` (`roleid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders_table`
--
ALTER TABLE `orders_table`
  MODIFY `orderid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `roles_table`
--
ALTER TABLE `roles_table`
  MODIFY `roleid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users_table`
--
ALTER TABLE `users_table`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
