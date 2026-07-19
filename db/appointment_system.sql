-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 19, 2026 at 07:13 AM
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
-- Database: `appointment_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `AppointmentID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `TransactionID` int(11) NOT NULL,
  `ApptDate` date NOT NULL,
  `ApptTime` time NOT NULL,
  `Status` enum('Pending','Confirmed','Pending_Reschedule','Cancelled','Completed') NOT NULL DEFAULT 'Pending',
  `CancelReason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`AppointmentID`, `UserID`, `TransactionID`, `ApptDate`, `ApptTime`, `Status`, `CancelReason`) VALUES
(1, 3, 1, '2026-07-22', '09:00:00', 'Cancelled', 'nani'),
(2, 3, 2, '2026-07-22', '09:00:00', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `blockeddates`
--

CREATE TABLE `blockeddates` (
  `BlockedID` int(11) NOT NULL,
  `BlockedDate` date NOT NULL,
  `Reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `TransactionID` int(11) NOT NULL,
  `TransactionName` varchar(100) NOT NULL,
  `Requirements` text DEFAULT NULL,
  `DailyQuota` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`TransactionID`, `TransactionName`, `Requirements`, `DailyQuota`) VALUES
(1, 'RPT', 'Latest Tax Declaration, Previous Year Receipt', 50),
(2, 'Transfer Tax', 'Deed of Sale, Certified True Copy of Title, Tax Clearance', 30),
(3, 'Business Tax', 'Barangay Clearance, DTI/SEC Registration, Previous Year Gross Sales', 100),
(4, 'Tax Clearance', 'Valid ID, Authorization Letter (if representative)', 150),
(5, 'Posting Certification', 'Request Letter, Valid ID', 20);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `Role` enum('User','Admin') NOT NULL DEFAULT 'User',
  `CreatedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `Email`, `PasswordHash`, `Role`, `CreatedAt`) VALUES
(1, 'admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', '2026-07-19 12:18:16'),
(2, 'admin2@test.com', '0192023a7bbd73250516f069df18b500', 'Admin', '2026-07-19 12:35:35'),
(3, 'mark@gmail.com', '$2y$10$ZCtQLAjYltA16hlAEzPgaea0q4FsaKXPFZG6SKJTQ2OYIFmi8qy/G', 'User', '2026-07-19 12:48:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`AppointmentID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `idx_appt_date_status` (`ApptDate`,`Status`),
  ADD KEY `idx_transaction_date` (`TransactionID`,`ApptDate`);

--
-- Indexes for table `blockeddates`
--
ALTER TABLE `blockeddates`
  ADD PRIMARY KEY (`BlockedID`),
  ADD UNIQUE KEY `BlockedDate` (`BlockedDate`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`TransactionID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `AppointmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `blockeddates`
--
ALTER TABLE `blockeddates`
  MODIFY `BlockedID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `TransactionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`TransactionID`) REFERENCES `transactions` (`TransactionID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
