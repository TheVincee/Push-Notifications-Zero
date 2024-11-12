-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 08, 2024 at 02:50 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `push`
--

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `lot_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact` varchar(15) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `message` text DEFAULT NULL,
  `notification_date` date DEFAULT NULL,
  `notification_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `lot_id`, `name`, `email`, `contact`, `status`, `message`, `notification_date`, `notification_time`) VALUES
(1, 5, 'Katell Dunlap', 'fexaqina@mailinator.com', '740', 'unread', 'New reservation made for Lot 5 by Katell Dunlap (fexaqina@mailinator.com)', '2024-11-08', '02:30:46');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `lot_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` enum('Pending','Canceled','Updated') DEFAULT 'Pending',
  `date` date NOT NULL,
  `time` time NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `lot_id`, `name`, `status`, `date`, `time`, `email`, `contact`) VALUES
(7, 5, 'Katell Dunlap', 'Pending', '2024-11-08', '02:30:46', 'fexaqina@mailinator.com', '740');

-- --------------------------------------------------------

--
-- Table structure for table `user_pass`
--

CREATE TABLE `user_pass` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `usertype` varchar(50) NOT NULL,
  `confirm_password` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_pass`
--

INSERT INTO `user_pass` (`id`, `email`, `password`, `usertype`, `confirm_password`, `name`) VALUES
(6, 'admin@gmail.com', 'admin123', 'admin', '', ''),
(7, 'user1@gmail.com', 'user1', 'user', '', ''),
(16, 'negro@gmail.com', '$2y$10$mBMRq77XtypT3KvbOiNtke8BAeAmw43OpMlXHvm8RfDilEOsqZWl2', 'user', '', 'James'),
(17, 'dr@gmail.com', '$2y$10$gY6Vb.Zq4DGwvfO5Bde1k.XSMc./7iBEXhuIWMGgLY/F2aUGWfkhe', 'user', '', 'dragonKnight');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_pass`
--
ALTER TABLE `user_pass`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_pass`
--
ALTER TABLE `user_pass`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
