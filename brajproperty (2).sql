-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2025 at 02:43 PM
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
-- Database: `brajproperty`
--

-- --------------------------------------------------------

--
-- Table structure for table `agent`
--

CREATE TABLE `agent` (
  `AgentID` int(11) NOT NULL,
  `LicenseNumber` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agent`
--

INSERT INTO `agent` (`AgentID`, `LicenseNumber`) VALUES
(1, 'AG123456'),
(3, 'AG654321'),
(10, 'AGENT-00010'),
(11, 'AGENT-00011'),
(12, 'AGENT-00012'),
(14, 'AGENT-00014');

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `AppointmentID` int(11) NOT NULL,
  `Date` date NOT NULL,
  `TIme` time NOT NULL,
  `ClientID` int(11) NOT NULL,
  `PropertyID` int(11) NOT NULL,
  `AgentID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`AppointmentID`, `Date`, `TIme`, `ClientID`, `PropertyID`, `AgentID`) VALUES
(8, '2025-04-28', '10:00:00', 2, 1, 1),
(9, '2025-04-29', '14:00:00', 4, 2, 3),
(12, '2025-05-02', '09:00:00', 2, 8, 3),
(15, '2025-04-29', '09:31:00', 2, 1, 1),
(17, '2025-04-24', '09:45:00', 2, 5, 1),
(18, '2025-04-24', '09:45:00', 2, 5, 1),
(19, '2025-04-26', '09:57:00', 2, 1, 1),
(20, '2025-04-25', '10:03:00', 2, 1, 1),
(21, '2025-04-25', '10:03:00', 2, 1, 1),
(22, '2025-04-25', '10:03:00', 2, 1, 1),
(23, '2025-04-25', '10:03:00', 2, 1, 1),
(24, '2025-04-25', '10:03:00', 2, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `ClientID` int(11) NOT NULL,
  `ClientType` enum('Buyer','Seller') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`ClientID`, `ClientType`) VALUES
(2, 'Buyer'),
(4, 'Seller');

-- --------------------------------------------------------

--
-- Table structure for table `commission`
--

CREATE TABLE `commission` (
  `CommissionID` int(11) NOT NULL,
  `AmountC` decimal(10,2) NOT NULL,
  `DateC` date NOT NULL,
  `AgentID` int(11) NOT NULL,
  `TransactionID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `commission`
--

INSERT INTO `commission` (`CommissionID`, `AmountC`, `DateC`, `AgentID`, `TransactionID`) VALUES
(1, 15000.00, '2024-08-16', 1, 1),
(2, 12500.00, '2024-08-21', 3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `favorite`
--

CREATE TABLE `favorite` (
  `UserID` int(11) NOT NULL,
  `PropertyID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favorite`
--

INSERT INTO `favorite` (`UserID`, `PropertyID`) VALUES
(1, 1),
(2, 5),
(2, 6);

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `ImageID` int(11) NOT NULL,
  `PropertyID` int(11) NOT NULL,
  `ImageURL` varchar(255) NOT NULL,
  `Description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`ImageID`, `PropertyID`, `ImageURL`, `Description`) VALUES
(1, 1, 'https://www.w3schools.com/w3images/lights.jpg', 'Front view of the property.'),
(2, 1, 'https://www.w3schools.com/w3images/forest.jpg', 'Interior view of the property.'),
(3, 2, 'https://www.w3schools.com/w3images/mountains.jpg', 'Exterior view of the home.'),
(4, 2, 'https://www.w3schools.com/w3images/architecture.jpg', 'Garden view of the home.');

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `LocationID` int(11) NOT NULL,
  `City` varchar(100) NOT NULL,
  `State` varchar(100) NOT NULL,
  `ZipCode` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`LocationID`, `City`, `State`, `ZipCode`) VALUES
(1, 'Port Louis', 'Port Louis', '12345'),
(2, 'Curepipe', 'Plaines Wilhems', '67890'),
(3, 'Quatres-Bornes', '', ''),
(4, 'rose-belle', '', ''),
(5, 'rose-hill', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `MessageID` int(11) NOT NULL,
  `SenderID` int(11) NOT NULL,
  `ReceiverID` int(11) NOT NULL,
  `Content` text NOT NULL,
  `TimeStamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`MessageID`, `SenderID`, `ReceiverID`, `Content`, `TimeStamp`) VALUES
(1, 1, 2, 'Hello, I would like to discuss the property details.', '2024-08-01 09:00:00'),
(2, 3, 4, 'Are you still interested in selling?', '2024-08-05 15:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `property`
--

CREATE TABLE `property` (
  `PropertyID` int(11) NOT NULL,
  `SalePrice` decimal(10,2) NOT NULL,
  `Size` int(11) NOT NULL,
  `Description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `AgentID` int(11) NOT NULL,
  `LocationID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property`
--

INSERT INTO `property` (`PropertyID`, `SalePrice`, `Size`, `Description`, `image`, `AgentID`, `LocationID`) VALUES
(1, 3500000.00, 120, 'Spacious apartment in the city center.', 'images/house.jpeg', 1, 1),
(2, 270000.00, 100, 'Cozy home in a quiet neighborhood.', 'images/apartment.jpg', 3, 2),
(5, 2500000.00, 500, 'Beautiful piece of land', 'images/land.jpeg', 1, 1),
(6, 15000000.00, 300, 'Luxury villa with a pool', 'images/villa.jpg', 3, 2),
(7, 1200000.00, 300, 'Luxurious apartment with river view', 'images/apartment2.jpeg', 10, 1),
(8, 900000.00, 200, 'Modern house with spacious garden', 'images/house2.jpeg', 3, 2),
(9, 2500000.00, 175, 'Classic villa with panoramic city view', 'images/villa2.jpeg', 10, 1),
(11, 500000.00, 175, 'cool house', 'images/house3.jpg', 1, 2),
(20, 2000000.00, 150, 'Nihal\'s Apartment', NULL, 1, 5);

-- --------------------------------------------------------

--
-- Table structure for table `propertyfeature`
--

CREATE TABLE `propertyfeature` (
  `FeatureID` int(11) NOT NULL,
  `FeatureName` varchar(100) NOT NULL,
  `FeatureValue` varchar(100) NOT NULL,
  `PropertyID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `propertyfeature`
--

INSERT INTO `propertyfeature` (`FeatureID`, `FeatureName`, `FeatureValue`, `PropertyID`) VALUES
(1, 'Bedrooms', '3', 1),
(2, 'Bathrooms', '2', 1),
(3, 'Bedrooms', '2', 2),
(4, 'Bathrooms', '1', 2);

-- --------------------------------------------------------

--
-- Table structure for table `property_requests`
--

CREATE TABLE `property_requests` (
  `RequestID` int(11) NOT NULL,
  `AgentID` int(11) NOT NULL,
  `PropertyID` int(11) DEFAULT NULL,
  `Action` enum('ADD','EDIT','DELETE') NOT NULL,
  `Payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`Payload`)),
  `Status` enum('PENDING','APPROVED','REJECTED') NOT NULL DEFAULT 'PENDING',
  `CreatedAt` datetime NOT NULL DEFAULT current_timestamp(),
  `ReviewedAt` datetime DEFAULT NULL,
  `ReviewerID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property_requests`
--

INSERT INTO `property_requests` (`RequestID`, `AgentID`, `PropertyID`, `Action`, `Payload`, `Status`, `CreatedAt`, `ReviewedAt`, `ReviewerID`) VALUES
(1, 1, NULL, 'ADD', '{\"Name\":\"Beautiful seaside house\",\"Type\":\"House\",\"Location\":\"Flic en Flac\",\"Price\":50000000}', 'REJECTED', '2025-04-23 10:51:45', '2025-04-23 10:52:19', 16),
(2, 1, NULL, 'ADD', '{\"Name\":\"Apartment near mall\",\"Type\":\"Apartment\",\"Location\":\"Quatres-Bornes\",\"Price\":3000000}', 'APPROVED', '2025-04-23 10:53:25', '2025-04-23 11:04:09', 16),
(3, 1, 5, 'EDIT', '{\"Price\":2500000,\"Description\":\"Beautiful piece of land\",\"Size\":500,\"Location\":\"Port Louis\"}', 'APPROVED', '2025-04-23 11:24:56', '2025-04-23 11:27:22', 16),
(4, 1, NULL, 'ADD', '{\"Name\":\"Beautiful seaside house\",\"Type\":\"House\",\"Location\":\"Quatres-Bornes\",\"Price\":50000000,\"Size\":30000,\"Image\":\"images\\/uploads\\/prop_6808b2465a1f98.15855974.jpg\"}', 'APPROVED', '2025-04-23 11:26:30', '2025-04-23 11:27:19', 16),
(5, 1, NULL, 'EDIT', '{\"Price\":50000000,\"Description\":\"0\",\"Size\":30000,\"Location\":\"Quatres-Bornes\",\"Image\":\"images\\/uploads\\/prop_6808b587252833.76915815.png\"}', 'APPROVED', '2025-04-23 11:40:23', '2025-04-23 11:41:19', 16),
(6, 1, NULL, 'DELETE', '{}', 'REJECTED', '2025-04-23 11:41:52', '2025-04-23 13:27:58', 16),
(7, 1, NULL, 'DELETE', '{}', 'REJECTED', '2025-04-23 12:01:22', '2025-04-23 13:27:54', 16),
(8, 1, NULL, 'DELETE', '{}', 'REJECTED', '2025-04-23 13:29:42', '2025-04-25 19:56:13', 16),
(9, 1, 11, 'EDIT', '{\"Price\":2500000,\"Description\":\"cool house\",\"Size\":175,\"Location\":\"Curepipe\",\"Image\":\"images\\/house3.jpg\"}', 'APPROVED', '2025-04-23 13:31:54', '2025-04-23 13:32:32', 16),
(10, 1, NULL, 'ADD', '{\"Name\":\"Arya\'s house\",\"Type\":\"house\",\"Location\":\"rose-belle\",\"Price\":12000000,\"Size\":3000}', 'APPROVED', '2025-04-26 09:52:33', '2025-04-26 10:27:37', 16),
(11, 1, NULL, 'ADD', '{\"Name\":\"Arya\'s house\",\"Type\":\"house\",\"Location\":\"rose-belle\",\"Price\":12000000,\"Size\":3000}', 'REJECTED', '2025-04-26 09:56:54', '2025-04-26 10:27:35', 16),
(12, 1, NULL, 'ADD', '{\"Name\":\"Arya\'s house\",\"Type\":\"house\",\"Location\":\"rose-belle\",\"Price\":12000000,\"Size\":3000}', 'REJECTED', '2025-04-26 09:56:59', '2025-04-26 10:27:32', 16),
(13, 1, NULL, 'ADD', '{\"Name\":\"Arya\'s house\",\"Type\":\"house\",\"Location\":\"rose-belle\",\"Price\":12000000,\"Size\":3000}', 'REJECTED', '2025-04-26 09:57:01', '2025-04-26 10:27:31', 16),
(14, 1, NULL, 'ADD', '{\"Name\":\"Arya\'s house\",\"Type\":\"house\",\"Location\":\"rose-belle\",\"Price\":12000000,\"Size\":3000}', 'APPROVED', '2025-04-26 09:57:04', '2025-04-26 10:27:25', 16),
(15, 1, NULL, 'ADD', '{\"Name\":\"Arya\'s house\",\"Type\":\"house\",\"Location\":\"rose-belle\",\"Price\":12000000,\"Size\":3000}', 'APPROVED', '2025-04-26 09:57:11', '2025-04-26 10:27:21', 16),
(16, 1, NULL, 'ADD', '{\"Name\":\"Arya\'s house\",\"Type\":\"House\",\"Location\":\"rose-belle\",\"Price\":12000000,\"Size\":3000}', 'APPROVED', '2025-04-26 09:58:01', '2025-04-26 10:27:16', 16),
(17, 1, NULL, 'DELETE', '{}', 'APPROVED', '2025-04-26 10:28:10', '2025-04-26 10:34:16', 16),
(18, 1, NULL, 'DELETE', '{}', 'APPROVED', '2025-04-26 10:28:15', '2025-04-26 10:34:06', 16),
(19, 1, NULL, 'DELETE', '{}', 'APPROVED', '2025-04-26 10:28:18', '2025-04-26 10:34:05', 16),
(20, 1, NULL, 'DELETE', '{}', 'APPROVED', '2025-04-26 10:28:20', '2025-04-26 10:34:04', 16),
(21, 1, NULL, 'DELETE', '{}', 'APPROVED', '2025-04-26 10:28:22', '2025-04-26 10:33:59', 16),
(22, 1, NULL, 'DELETE', '{}', 'APPROVED', '2025-04-26 10:34:46', '2025-04-26 10:35:08', 16),
(23, 1, NULL, 'DELETE', '{}', 'APPROVED', '2025-04-26 10:35:58', '2025-04-26 12:14:09', 16),
(24, 1, NULL, 'DELETE', '{}', 'APPROVED', '2025-04-26 12:14:42', '2025-04-26 12:15:39', 16),
(25, 1, 11, 'EDIT', '{\"Price\":3500000,\"Description\":\"cool house\",\"Size\":175,\"Location\":\"Curepipe\",\"Image\":\"images\\/house3.jpg\"}', 'REJECTED', '2025-04-26 13:37:51', '2025-04-26 13:38:18', 16),
(26, 1, 11, 'EDIT', '{\"Price\":3500000,\"Description\":\"cool house\",\"Size\":175,\"Location\":\"Curepipe\",\"Image\":\"images\\/house3.jpg\"}', 'REJECTED', '2025-04-26 13:38:44', '2025-04-26 13:54:53', 16),
(27, 1, 5, 'EDIT', '{\"Price\":4000000,\"Description\":\"Beautiful piece of land\",\"Size\":500,\"Location\":\"Port Louis\",\"Image\":\"images\\/land.jpeg\"}', 'APPROVED', '2025-04-26 13:38:51', '2025-04-26 13:54:49', 16),
(28, 1, 1, 'EDIT', '{\"Price\":400000,\"Description\":\"Spacious apartment in the city center.\",\"Size\":120,\"Location\":\"Port Louis\",\"Image\":\"images\\/house.jpeg\"}', 'APPROVED', '2025-04-26 13:56:28', '2025-04-26 13:56:51', 16),
(29, 1, NULL, 'ADD', '{\"Name\":\"Arya\'s house\",\"Type\":\"House\",\"Location\":\"rose-belle\",\"Price\":120000,\"Size\":3000}', 'APPROVED', '2025-04-26 13:58:29', '2025-04-26 14:01:02', 16),
(30, 1, 1, 'EDIT', '{\"Price\":400000,\"Description\":\"Spacious apartment in the city center.\",\"Size\":120,\"Location\":\"Port Louis\",\"Image\":\"images\\/house.jpeg\"}', 'APPROVED', '2025-04-26 14:06:34', '2025-04-26 14:07:15', 16),
(31, 1, NULL, 'ADD', '{\"Name\":\"Arya\'s house\",\"Type\":\"House\",\"Location\":\"rose-belle\",\"Price\":12000000,\"Size\":3000}', 'APPROVED', '2025-04-26 14:06:52', '2025-04-26 14:07:13', 16),
(32, 1, NULL, 'DELETE', '{}', 'APPROVED', '2025-04-26 14:07:37', '2025-04-26 14:08:14', 16),
(33, 1, 1, 'EDIT', '{\"Price\":0,\"Description\":\"Spacious apartment in the city center.\",\"Size\":120,\"Location\":\"Port Louis\",\"Image\":\"images\\/house.jpeg\"}', 'APPROVED', '2025-04-26 14:07:46', '2025-04-26 14:08:11', 16),
(34, 1, 11, 'EDIT', '{\"Price\":500000,\"Description\":\"cool house\",\"Size\":175,\"Location\":\"Curepipe\",\"Image\":\"images\\/house3.jpg\"}', 'APPROVED', '2025-04-26 14:09:43', '2025-04-26 14:10:21', 16),
(35, 1, 11, 'EDIT', '{\"Price\":0,\"Description\":\"cool house\",\"Size\":175,\"Location\":\"Curepipe\",\"Image\":\"images\\/house3.jpg\"}', 'REJECTED', '2025-04-26 14:14:43', '2025-04-26 14:15:10', 16),
(36, 1, NULL, 'ADD', '{\"Name\":\"Arya\'s house\",\"Type\":\"House\",\"Location\":\"rose-belle\",\"Price\":12000000,\"Size\":3000}', 'REJECTED', '2025-04-26 14:22:34', '2025-04-26 14:28:23', 16),
(37, 1, NULL, 'ADD', '{\"Name\":\"Nihal\'s Apartment\",\"Type\":\"Apartment\",\"Location\":\"rose-hill\",\"Price\":2000000,\"Size\":150}', 'APPROVED', '2025-04-26 14:23:04', '2025-04-26 14:25:34', 16),
(38, 1, 20, 'EDIT', '{\"Price\":0,\"Description\":\"Nihal\'s Apartment\",\"Size\":150,\"Location\":\"rose-hill\",\"Image\":null}', 'REJECTED', '2025-04-26 14:32:14', '2025-04-26 14:37:50', 16),
(39, 1, 1, 'EDIT', '{\"Price\":3500000,\"Description\":\"Spacious apartment in the city center.\",\"Size\":120,\"Location\":\"Port Louis\",\"Image\":\"images\\/house.jpeg\"}', 'APPROVED', '2025-04-26 14:32:26', '2025-04-26 14:37:05', 16);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `ReviewID` int(11) NOT NULL,
  `Comment` text NOT NULL,
  `Rating` int(11) NOT NULL,
  `DateR` date NOT NULL,
  `ClientID` int(11) NOT NULL,
  `AgentID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`ReviewID`, `Comment`, `Rating`, `DateR`, `ClientID`, `AgentID`) VALUES
(1, 'Great service and support throughout the process.', 5, '2024-08-25', 2, 1),
(2, 'Professional and attentive.', 4, '2024-08-28', 4, 3);

-- --------------------------------------------------------

--
-- Table structure for table `site_content`
--

CREATE TABLE `site_content` (
  `page` varchar(50) NOT NULL,
  `content_key` varchar(50) NOT NULL,
  `content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_content`
--

INSERT INTO `site_content` (`page`, `content_key`, `content`) VALUES
('about', 'main', 'This is to say that I love Fate/Zero a lot <3'),
('home', 'main', 'I love chocolates lol <3\n');

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `TransactionID` int(11) NOT NULL,
  `DateT` date NOT NULL,
  `TransactionMethod` varchar(50) NOT NULL,
  `TransactionType` enum('Sale','Rent') NOT NULL,
  `PropertyID` int(11) NOT NULL,
  `ClientID` int(11) NOT NULL,
  `AgentID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`TransactionID`, `DateT`, `TransactionMethod`, `TransactionType`, `PropertyID`, `ClientID`, `AgentID`) VALUES
(1, '2024-08-15', 'Bank Transfer', 'Sale', 1, 2, 1),
(2, '2024-08-20', 'Cash', 'Sale', 2, 4, 3);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `PhoneNumber` varchar(20) NOT NULL,
  `UserType` enum('Client','Agent','Admin') NOT NULL COMMENT 'Client, Agent, or Admin',
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`UserID`, `Name`, `Email`, `PhoneNumber`, `UserType`, `Password`) VALUES
(1, 'Arya Kumar', 'arya.kumar@gmail.com', '123-456-7890', 'Agent', '$2y$10$F/sK5sdf4FF0kKnH0ueUyOb7SDtTqjhbnsrgFuq01gA57Lq7HkJlC'),
(2, 'Rayush Krsna', 'rayush.krsna@gmail.com', '098-765-4321', 'Client', '$2y$10$pzzP86F8jbPj1y2syPxOIOACTYH49bdMCW4eGk2U6PKXjHUSa2kEW'),
(3, 'Michael Johnson', 'michael.johnson@gmail.com', '321-654-0987', 'Agent', '$2y$10$DTGUj6q/mC2TysYCUT.E1OtANJGgxp2MDdv7XLFu5ycKOTUIs1ekq'),
(4, 'Emily Davis', 'emily.davis@gmail.com', '654-321-9870', 'Client', '$2y$10$x5AEl731BI8EUwOHGHP6WO58qnMpEqQULZUspIBWPDa77mKPTTQCu'),
(8, 'jane', 'jane@gmail.com', '56749349', 'Client', '$2y$10$CaPAEqZw8FoC6tRkrXyQXeLsYX7XD2B8TnBC9sFGC817P6sAcn5Vu'),
(10, 'nihal', 'nihal@gmail.com', '5555555', 'Agent', '$2y$10$/gwt6ElxGaJJrvsveESIJuGfoPF6jeZv.puIBrPxuaX.TvXam84aO'),
(11, 'Youvan', 'youvan@gmail.com', '444444', 'Agent', '$2y$10$hraXAx9Y5DtEAubyRQk/5.R4KTWkxTWtplWzrAfVNNP5.E1UMZMc2'),
(12, 'nand', 'nand@gmail.com', '222222', 'Agent', '$2y$10$wxu0SaGzMtMEcRFmJgvsGuZe6OmIUtfqsR1dFxoZTacTyl8BPxyE6'),
(13, 'chaya', 'chaya@gmail.com', '111111', 'Client', '$2y$10$pIfQLXYU6ItoMITjdmFcn.cqcORhmKhteeVp9jMQtlF3yMfhL5FB6'),
(14, 'james', 'james@gmail.com', '22222', 'Agent', '$2y$10$E5aRWYB0e4zoUMKLTregoeP5lk0rwP6T1oiHM9PbgWLEvZIqulMKm'),
(15, 'lol', 'lol@gmail.com', '345678', 'Client', '$2y$10$rDSHlGk8KsxWm95jBZET.uHSNEzeMPf24KM/wEBarOLSBObolOsCG'),
(16, 'Super Admin', 'admin@brajproperty.com', '55555555', 'Admin', '$2y$10$5.QihGvCCQzDsQ1mnsxyTuLLk/OO/X76T1A9tcxCb.q367yu.0yK.'),
(17, 'jake', 'Jake@gmail.com', '3333333', 'Agent', '$2y$10$K8OY/qPVHMWG/FKBm.0wD.Ytiojnhv9Mu3zBLToiZKwyrjd3e.9py'),
(18, 'jean', 'jean@gmail.com', '3333333', 'Client', '$2y$10$hotAWADpABDPh9RXjEB70uOCuwRp5/t3EUOvGheFXyayd08scthrC'),
(19, 'jamy', 'jamy@gmail.com', '3333333', 'Agent', '$2y$10$PKV./m9rSUNVIuEXtfrAgel/OP5ZTR8U3T0PS2AdGUm/FvwQt53jC');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agent`
--
ALTER TABLE `agent`
  ADD PRIMARY KEY (`AgentID`),
  ADD KEY `AgentID` (`AgentID`);

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`AppointmentID`),
  ADD KEY `fk_Appointment_Client` (`ClientID`),
  ADD KEY `fk_Appointment_Property` (`PropertyID`),
  ADD KEY `fk_Appointment_Agent` (`AgentID`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`ClientID`);

--
-- Indexes for table `commission`
--
ALTER TABLE `commission`
  ADD PRIMARY KEY (`CommissionID`),
  ADD KEY `fk_Commission_Agent` (`AgentID`),
  ADD KEY `fk_Commission_Transaction` (`TransactionID`);

--
-- Indexes for table `favorite`
--
ALTER TABLE `favorite`
  ADD PRIMARY KEY (`UserID`,`PropertyID`),
  ADD KEY `fk_fav_prop` (`PropertyID`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`ImageID`),
  ADD KEY `fk_Images_Property` (`PropertyID`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`LocationID`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`MessageID`),
  ADD KEY `fk_Message_Sender` (`SenderID`),
  ADD KEY `fk_Message_Receiver` (`ReceiverID`);

--
-- Indexes for table `property`
--
ALTER TABLE `property`
  ADD PRIMARY KEY (`PropertyID`),
  ADD KEY `fk_Property_Agent` (`AgentID`),
  ADD KEY `fk_Property_Location` (`LocationID`);

--
-- Indexes for table `propertyfeature`
--
ALTER TABLE `propertyfeature`
  ADD PRIMARY KEY (`FeatureID`),
  ADD KEY `fk_PropertyFeature_Property` (`PropertyID`);

--
-- Indexes for table `property_requests`
--
ALTER TABLE `property_requests`
  ADD PRIMARY KEY (`RequestID`),
  ADD KEY `AgentID` (`AgentID`),
  ADD KEY `fk_pr_prop` (`PropertyID`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`ReviewID`),
  ADD KEY `fk_Reviews_Client` (`ClientID`),
  ADD KEY `fk_Reviews_Agent` (`AgentID`);

--
-- Indexes for table `site_content`
--
ALTER TABLE `site_content`
  ADD PRIMARY KEY (`page`,`content_key`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`TransactionID`),
  ADD KEY `fk_Transaction_Property` (`PropertyID`),
  ADD KEY `fk_Transaction_Client` (`ClientID`),
  ADD KEY `fk_Transaction_Agent` (`AgentID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agent`
--
ALTER TABLE `agent`
  MODIFY `AgentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `AppointmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `ClientID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `commission`
--
ALTER TABLE `commission`
  MODIFY `CommissionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `ImageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `LocationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `MessageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `property`
--
ALTER TABLE `property`
  MODIFY `PropertyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `propertyfeature`
--
ALTER TABLE `propertyfeature`
  MODIFY `FeatureID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `property_requests`
--
ALTER TABLE `property_requests`
  MODIFY `RequestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `ReviewID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `TransactionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `agent`
--
ALTER TABLE `agent`
  ADD CONSTRAINT `fk_Agent_User` FOREIGN KEY (`AgentID`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `fk_Appointment_Agent` FOREIGN KEY (`AgentID`) REFERENCES `agent` (`AgentID`),
  ADD CONSTRAINT `fk_Appointment_Property` FOREIGN KEY (`PropertyID`) REFERENCES `property` (`PropertyID`),
  ADD CONSTRAINT `fk_Appointment_User` FOREIGN KEY (`ClientID`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `fk_Client_User` FOREIGN KEY (`ClientID`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `commission`
--
ALTER TABLE `commission`
  ADD CONSTRAINT `fk_Commission_Agent` FOREIGN KEY (`AgentID`) REFERENCES `agent` (`AgentID`),
  ADD CONSTRAINT `fk_Commission_Transaction` FOREIGN KEY (`TransactionID`) REFERENCES `transaction` (`TransactionID`);

--
-- Constraints for table `favorite`
--
ALTER TABLE `favorite`
  ADD CONSTRAINT `fk_fav_prop` FOREIGN KEY (`PropertyID`) REFERENCES `property` (`PropertyID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_fav_user` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `fk_Images_Property` FOREIGN KEY (`PropertyID`) REFERENCES `property` (`PropertyID`);

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `fk_Message_Receiver` FOREIGN KEY (`ReceiverID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `fk_Message_Sender` FOREIGN KEY (`SenderID`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `property`
--
ALTER TABLE `property`
  ADD CONSTRAINT `fk_Property_Agent` FOREIGN KEY (`AgentID`) REFERENCES `agent` (`AgentID`),
  ADD CONSTRAINT `fk_Property_Location` FOREIGN KEY (`LocationID`) REFERENCES `location` (`LocationID`);

--
-- Constraints for table `propertyfeature`
--
ALTER TABLE `propertyfeature`
  ADD CONSTRAINT `fk_PropertyFeature_Property` FOREIGN KEY (`PropertyID`) REFERENCES `property` (`PropertyID`);

--
-- Constraints for table `property_requests`
--
ALTER TABLE `property_requests`
  ADD CONSTRAINT `fk_pr_prop` FOREIGN KEY (`PropertyID`) REFERENCES `property` (`PropertyID`) ON DELETE SET NULL,
  ADD CONSTRAINT `property_requests_ibfk_1` FOREIGN KEY (`AgentID`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_Reviews_Agent` FOREIGN KEY (`AgentID`) REFERENCES `agent` (`AgentID`),
  ADD CONSTRAINT `fk_Reviews_Client` FOREIGN KEY (`ClientID`) REFERENCES `client` (`ClientID`);

--
-- Constraints for table `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `fk_Transaction_Agent` FOREIGN KEY (`AgentID`) REFERENCES `agent` (`AgentID`),
  ADD CONSTRAINT `fk_Transaction_Client` FOREIGN KEY (`ClientID`) REFERENCES `client` (`ClientID`),
  ADD CONSTRAINT `fk_Transaction_Property` FOREIGN KEY (`PropertyID`) REFERENCES `property` (`PropertyID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
