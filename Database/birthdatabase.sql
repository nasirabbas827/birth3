-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 30, 2024 at 10:28 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `birthdatabase`
--

-- --------------------------------------------------------

--
-- Table structure for table `birthrecords`
--

CREATE TABLE `birthrecords` (
  `BirthRecordID` int(11) NOT NULL,
  `ChildName` varchar(100) NOT NULL,
  `BirthDate` date NOT NULL,
  `Gender` enum('Male','Female') NOT NULL,
  `MotherNIC` varchar(13) NOT NULL,
  `FatherNIC` varchar(13) NOT NULL,
  `BirthPlace` varchar(100) NOT NULL,
  `PaymentStatus` enum('Paid','Unpaid') DEFAULT 'Unpaid',
  `Fee` decimal(10,2) NOT NULL,
  `DistrictID` int(11) DEFAULT NULL,
  `TehsilID` int(11) DEFAULT NULL,
  `UnionCouncilID` int(11) DEFAULT NULL,
  `UserID` int(11) DEFAULT NULL,
  `TransactionImage` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `birthrecords`
--

INSERT INTO `birthrecords` (`BirthRecordID`, `ChildName`, `BirthDate`, `Gender`, `MotherNIC`, `FatherNIC`, `BirthPlace`, `PaymentStatus`, `Fee`, `DistrictID`, `TehsilID`, `UnionCouncilID`, `UserID`, `TransactionImage`) VALUES
(1, 'Haider Ali', '2024-10-30', 'Male', '3240233343661', '3240233343661', 'Home', 'Paid', 2000.00, 1, 1, 2, 1, 'uploads/201_3.png');

-- --------------------------------------------------------

--
-- Table structure for table `deathrecords`
--

CREATE TABLE `deathrecords` (
  `DeathRecordID` int(11) NOT NULL,
  `DeceasedName` varchar(255) NOT NULL,
  `FatherName` varchar(255) NOT NULL,
  `FatherNIC` varchar(13) NOT NULL,
  `DeathDate` date NOT NULL,
  `CauseOfDeath` text NOT NULL,
  `NICNumber` varchar(13) NOT NULL,
  `PaymentStatus` enum('Unpaid','Paid') DEFAULT 'Unpaid',
  `DeathPlace` varchar(255) NOT NULL,
  `Fee` decimal(10,2) NOT NULL,
  `UnionCouncilID` int(11) DEFAULT NULL,
  `DistrictID` int(11) DEFAULT NULL,
  `TehsilID` int(11) DEFAULT NULL,
  `UserID` int(11) DEFAULT NULL,
  `TransactionImage` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deathrecords`
--

INSERT INTO `deathrecords` (`DeathRecordID`, `DeceasedName`, `FatherName`, `FatherNIC`, `DeathDate`, `CauseOfDeath`, `NICNumber`, `PaymentStatus`, `DeathPlace`, `Fee`, `UnionCouncilID`, `DistrictID`, `TehsilID`, `UserID`, `TransactionImage`) VALUES
(1, 'None', 'haider', '3240233343661', '2024-10-30', 'Dont know', '3240233343661', 'Paid', 'dfa', 3000.00, 2, 2, 1, 1, 'uploads/201_3.png');

-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

CREATE TABLE `districts` (
  `DistrictID` int(11) NOT NULL,
  `DistrictName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `districts`
--

INSERT INTO `districts` (`DistrictID`, `DistrictName`) VALUES
(2, 'Karachi'),
(1, 'RajanPur');

-- --------------------------------------------------------

--
-- Table structure for table `fees`
--

CREATE TABLE `fees` (
  `fee_id` int(11) NOT NULL,
  `fee_type` enum('Birth Certificate','Death Certificate') NOT NULL,
  `fee` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fees`
--

INSERT INTO `fees` (`fee_id`, `fee_type`, `fee`) VALUES
(1, 'Birth Certificate', 2000.00),
(2, 'Death Certificate', 3000.00);

-- --------------------------------------------------------

--
-- Table structure for table `pets`
--

CREATE TABLE `pets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `age` int(11) NOT NULL,
  `location` varchar(100) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `image_caption` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pets`
--

INSERT INTO `pets` (`id`, `user_id`, `name`, `type`, `description`, `age`, `location`, `image_path`, `image_caption`, `created_at`) VALUES
(2, 1, 'Pet One', 'Dog', 'dsa', 1, 'Lahore', 'images/pet_672075f236ff84.55494607.jpg', 'fda', '2024-10-29 05:14:24'),
(3, 1, 'Pet Two', 'Cat', 'da', 2, 'Lahore', 'images/6720755f0734f_cat.jpg', 'da', '2024-10-29 05:40:47'),
(4, 2, 'Pet Three', 'Bird', 'fda', 1, 'Lahore', 'images/67207ffea1a57_pexels-pixabay-36846.jpg', 'da', '2024-10-29 06:26:06');

-- --------------------------------------------------------

--
-- Table structure for table `tehsils`
--

CREATE TABLE `tehsils` (
  `TehsilID` int(11) NOT NULL,
  `TehsilName` varchar(255) NOT NULL,
  `DistrictID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tehsils`
--

INSERT INTO `tehsils` (`TehsilID`, `TehsilName`, `DistrictID`) VALUES
(1, 'Tehsil ONE', 2),
(3, 'Tehsile Two', 1);

-- --------------------------------------------------------

--
-- Table structure for table `unioncouncils`
--

CREATE TABLE `unioncouncils` (
  `UnionCouncilID` int(11) NOT NULL,
  `UnionCouncilName` varchar(255) NOT NULL,
  `TehsilID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `unioncouncils`
--

INSERT INTO `unioncouncils` (`UnionCouncilID`, `UnionCouncilName`, `TehsilID`) VALUES
(2, 'UC One', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `bio` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `usertype` enum('User','Admin') NOT NULL DEFAULT 'User'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `bio`, `created_at`, `usertype`) VALUES
(1, 'nasir12', 'nasiryt.827@gmail.com', '$2y$10$6rXsbGPoXrQURrPSPp1LceYzzgQWo28GjKfroAcZhG.K52y14ttjG', 'daeef', '2024-10-29 04:35:30', 'User'),
(2, 'Haider', 'haider@gmail.com', '$2y$10$hlEY01GUYxC4o4O1hZVxauOJ4GRHC8XEBzVp.Clqan0IqOsnSDeeq', 'fdae', '2024-10-29 06:24:58', 'User'),
(3, 'Admin', 'admin@gmail.com', '$2y$10$BY1m8k6qFQSaoGXa.b.2We83dKFwDGfcSuohgzdyDrE/keebJGo4y', 'I am Admin', '2024-10-30 08:00:29', 'Admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `birthrecords`
--
ALTER TABLE `birthrecords`
  ADD PRIMARY KEY (`BirthRecordID`),
  ADD KEY `DistrictID` (`DistrictID`),
  ADD KEY `TehsilID` (`TehsilID`),
  ADD KEY `UnionCouncilID` (`UnionCouncilID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `deathrecords`
--
ALTER TABLE `deathrecords`
  ADD PRIMARY KEY (`DeathRecordID`),
  ADD KEY `UnionCouncilID` (`UnionCouncilID`),
  ADD KEY `DistrictID` (`DistrictID`),
  ADD KEY `TehsilID` (`TehsilID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`DistrictID`),
  ADD UNIQUE KEY `DistrictName` (`DistrictName`);

--
-- Indexes for table `fees`
--
ALTER TABLE `fees`
  ADD PRIMARY KEY (`fee_id`);

--
-- Indexes for table `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tehsils`
--
ALTER TABLE `tehsils`
  ADD PRIMARY KEY (`TehsilID`),
  ADD KEY `DistrictID` (`DistrictID`);

--
-- Indexes for table `unioncouncils`
--
ALTER TABLE `unioncouncils`
  ADD PRIMARY KEY (`UnionCouncilID`),
  ADD KEY `TehsilID` (`TehsilID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `birthrecords`
--
ALTER TABLE `birthrecords`
  MODIFY `BirthRecordID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `deathrecords`
--
ALTER TABLE `deathrecords`
  MODIFY `DeathRecordID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `districts`
--
ALTER TABLE `districts`
  MODIFY `DistrictID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `fees`
--
ALTER TABLE `fees`
  MODIFY `fee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pets`
--
ALTER TABLE `pets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tehsils`
--
ALTER TABLE `tehsils`
  MODIFY `TehsilID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `unioncouncils`
--
ALTER TABLE `unioncouncils`
  MODIFY `UnionCouncilID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `birthrecords`
--
ALTER TABLE `birthrecords`
  ADD CONSTRAINT `birthrecords_ibfk_1` FOREIGN KEY (`DistrictID`) REFERENCES `districts` (`DistrictID`),
  ADD CONSTRAINT `birthrecords_ibfk_2` FOREIGN KEY (`TehsilID`) REFERENCES `tehsils` (`TehsilID`),
  ADD CONSTRAINT `birthrecords_ibfk_3` FOREIGN KEY (`UnionCouncilID`) REFERENCES `unioncouncils` (`UnionCouncilID`),
  ADD CONSTRAINT `birthrecords_ibfk_4` FOREIGN KEY (`UserID`) REFERENCES `users` (`id`);

--
-- Constraints for table `deathrecords`
--
ALTER TABLE `deathrecords`
  ADD CONSTRAINT `deathrecords_ibfk_1` FOREIGN KEY (`UnionCouncilID`) REFERENCES `unioncouncils` (`UnionCouncilID`),
  ADD CONSTRAINT `deathrecords_ibfk_2` FOREIGN KEY (`DistrictID`) REFERENCES `districts` (`DistrictID`),
  ADD CONSTRAINT `deathrecords_ibfk_3` FOREIGN KEY (`TehsilID`) REFERENCES `tehsils` (`TehsilID`),
  ADD CONSTRAINT `deathrecords_ibfk_4` FOREIGN KEY (`UserID`) REFERENCES `users` (`id`);

--
-- Constraints for table `pets`
--
ALTER TABLE `pets`
  ADD CONSTRAINT `pets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tehsils`
--
ALTER TABLE `tehsils`
  ADD CONSTRAINT `tehsils_ibfk_1` FOREIGN KEY (`DistrictID`) REFERENCES `districts` (`DistrictID`) ON DELETE CASCADE;

--
-- Constraints for table `unioncouncils`
--
ALTER TABLE `unioncouncils`
  ADD CONSTRAINT `unioncouncils_ibfk_1` FOREIGN KEY (`TehsilID`) REFERENCES `tehsils` (`TehsilID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
