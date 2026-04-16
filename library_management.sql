-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 16, 2026 at 10:13 AM
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
-- Database: `library_management`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `Authors_per_Books` ()   SELECT 
	books.book_title AS Book_Title,
    GROUP_CONCAT(CONCAT(authors.author_firstname, ' ',authors.author_lastname) SEPARATOR ' | ') AS Author
    FROM bookauthors
JOIN authors ON bookauthors.author_id = authors.author_id
JOIN books ON bookauthors.book_id = books.book_id
GROUP BY 1
ORDER BY COUNT(books.book_title) DESC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Books_per_Author` ()   SELECT 
	CONCAT(authors.author_firstname, ' ',authors.author_lastname) AS Author,
    GROUP_CONCAT(books.book_title SEPARATOR ' | ') AS Books_Written
    FROM bookauthors
JOIN authors ON bookauthors.author_id = authors.author_id
JOIN books ON bookauthors.book_id = books.book_id
GROUP BY 1
ORDER BY COUNT(books.book_title) DESC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Borrower_per_Month` ()   SELECT 
	UPPER(LEFT(MONTHNAME(loan_date),3)) AS Loan_Month,
	COUNT(borrower_id) AS Total_Borrowers
FROM loan 
GROUP BY borrower_id
ORDER BY MONTH(Loan_date)$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE `authors` (
  `author_id` int(11) NOT NULL,
  `author_firstname` varchar(255) DEFAULT NULL,
  `author_lastname` varchar(255) DEFAULT NULL,
  `author_birthyear` smallint(6) DEFAULT NULL,
  `author_nationality` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `authors`
--

INSERT INTO `authors` (`author_id`, `author_firstname`, `author_lastname`, `author_birthyear`, `author_nationality`) VALUES
(1, 'Jose', 'Rizal', 1861, 'Filipino'),
(2, 'Amado', 'Hernandez', 1903, 'Filipino'),
(3, 'F. H.', 'Batacan', 1967, 'Filipino'),
(4, 'Lualhati', 'Bautista', 1946, 'Filipino'),
(5, 'Nick', 'Joaquin', 1917, 'Filipino'),
(6, 'Bob', 'Ong', 1979, 'Filipino'),
(7, 'J.K.', 'Rowling', 1965, 'British');

-- --------------------------------------------------------

--
-- Table structure for table `bookauthors`
--

CREATE TABLE `bookauthors` (
  `baba_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookauthors`
--

INSERT INTO `bookauthors` (`baba_id`, `book_id`, `author_id`) VALUES
(1, 1, 1),
(2, 2, 1),
(6, 2, 2),
(3, 3, 2),
(4, 4, 3),
(5, 5, 4),
(7, 6, 7);

-- --------------------------------------------------------

--
-- Table structure for table `bookcopy`
--

CREATE TABLE `bookcopy` (
  `copy_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `bc_status` enum('AVAILABLE','ON_LOAN','LOST','DAMAGED','REPAIR') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookcopy`
--

INSERT INTO `bookcopy` (`copy_id`, `book_id`, `bc_status`) VALUES
(101, 1, 'AVAILABLE'),
(102, 1, 'ON_LOAN'),
(103, 1, 'AVAILABLE'),
(201, 2, 'AVAILABLE'),
(202, 2, 'AVAILABLE'),
(301, 3, 'AVAILABLE'),
(302, 3, 'REPAIR'),
(401, 4, 'ON_LOAN'),
(402, 4, 'AVAILABLE'),
(501, 5, 'AVAILABLE'),
(502, 5, 'LOST'),
(601, 6, 'AVAILABLE');

-- --------------------------------------------------------

--
-- Table structure for table `bookgenre`
--

CREATE TABLE `bookgenre` (
  `gb_id` int(11) NOT NULL,
  `genre_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookgenre`
--

INSERT INTO `bookgenre` (`gb_id`, `genre_id`, `book_id`) VALUES
(1, 1, 1),
(4, 1, 2),
(2, 2, 1),
(11, 2, 5),
(9, 3, 4),
(5, 4, 2),
(7, 4, 3),
(3, 5, 1),
(6, 5, 2),
(8, 5, 3),
(10, 5, 4),
(12, 5, 5),
(13, 6, 6);

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `book_id` int(11) NOT NULL,
  `book_title` varchar(255) DEFAULT NULL,
  `book_isbn` varchar(255) DEFAULT NULL,
  `book_publication_year` smallint(6) DEFAULT NULL,
  `book_edition` varchar(255) DEFAULT NULL,
  `book_publisher` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `book_title`, `book_isbn`, `book_publication_year`, `book_edition`, `book_publisher`) VALUES
(1, 'Noli Me Tangere', '9789710810736', 1887, 'Reprint Edition', 'National Book Store'),
(2, 'El Filibusterismo', '9789710810743', 1891, 'Reprint Edition', 'National Book Store'),
(3, 'Mga Ibong Mandaragit', '9789711000000', 1969, '1st Edition', 'Adarna House'),
(4, 'Smaller and Smaller Circles', '9789712721768', 2002, '1st Edition', 'Ateneo de Manila University Press'),
(5, 'Dekada ’70', '9789712712346', 1983, '2nd Edition', 'Ateneo de Manila University Press'),
(6, 'Harry Potter and the Philosopher\'s Stone', '9780747532743', 2007, '1st ', 'Bloomsbury');

-- --------------------------------------------------------

--
-- Table structure for table `borroweraddress`
--

CREATE TABLE `borroweraddress` (
  `ba_id` int(11) NOT NULL,
  `borrower_id` int(11) NOT NULL,
  `ba_house_number` varchar(255) DEFAULT NULL,
  `ba_street` varchar(255) DEFAULT NULL,
  `ba_barangay` varchar(255) DEFAULT NULL,
  `ba_city` varchar(255) DEFAULT NULL,
  `ba_province` varchar(255) DEFAULT NULL,
  `ba_postal_code` varchar(4) DEFAULT NULL,
  `ba_country` varchar(255) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borroweraddress`
--

INSERT INTO `borroweraddress` (`ba_id`, `borrower_id`, `ba_house_number`, `ba_street`, `ba_barangay`, `ba_city`, `ba_province`, `ba_postal_code`, `ba_country`, `is_primary`) VALUES
(1, 1, '32', 'Maharlika St.', 'Brgy. Sabang', 'Lipa City', 'Batangas', '4217', 'Philippines', 1),
(2, 2, '145', 'Rizal Ave.', 'Brgy. Balintawak', 'Quezon City', 'Metro Manila', '1100', 'Philippines', 1),
(3, 3, '8', 'Mabini St.', 'Brgy. San Roque', 'Antipolo City', 'Rizal', '1870', 'Philippines', 1),
(4, 4, '21', 'Del Pilar St.', 'Brgy. Poblacion', 'Calamba City', 'Laguna', '4027', 'Philippines', 1),
(5, 5, '77', 'Bonifacio St.', 'Brgy. Talomo', 'Davao City', 'Davao del Sur', '8000', 'Philippines', 1),
(6, 6, '19', 'JP Laurel St.', 'Brgy. Lahug', 'Cebu City', 'Cebu', '6000', 'Philippines', 1),
(7, 7, '267', 'Ball', 'San Sebastian', 'Lipa', 'Batangas', '4217', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `borrowers`
--

CREATE TABLE `borrowers` (
  `borrower_id` int(11) NOT NULL,
  `borrower_firstname` varchar(255) DEFAULT NULL,
  `borrower_lastname` varchar(255) DEFAULT NULL,
  `borrower_email` varchar(255) DEFAULT NULL,
  `borrower_phone_number` varchar(11) DEFAULT NULL,
  `borrower_member_since` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowers`
--

INSERT INTO `borrowers` (`borrower_id`, `borrower_firstname`, `borrower_lastname`, `borrower_email`, `borrower_phone_number`, `borrower_member_since`, `is_active`) VALUES
(1, 'Juan', 'Dela Cruz', 'juan.delacruz@samplemail.com', '09171234567', '2024-06-10', 1),
(2, 'Maria', 'Santos', 'maria.santos@samplemail.com', '09281234567', '2023-09-22', 1),
(3, 'Mark', 'Reyes', 'mark.reyes@samplemail.com', '09061234567', '2025-01-15', 1),
(4, 'Ana', 'Bautista', 'ana.bautista@samplemail.com', '09991234567', '2024-11-05', 1),
(5, 'Paolo', 'Garcia', 'paolo.garcia@samplemail.com', '09351234567', '2022-07-19', 0),
(6, 'Grace', 'Mendoza', 'grace.mendoza@samplemail.com', '09181234567', '2025-07-02', 1),
(7, 'Miguel', 'Go', 'miguel.go@samplegmail.com', '09991234567', '2026-04-06', 1),
(8, 'Jei', 'Pastrana', 'jei.pastrana@sampleemail.com', '09991234566', '2026-04-05', 1),
(9, 'Jhovan', 'Busita', 'jhovan.busita@samplemail.com', '09991234555', '2026-04-05', 1),
(10, 'Angela', 'Mandigma', 'angela.mandigma@samplemail.com', '09987654321', '2026-04-12', 1),
(11, 'Myka', 'Medina', 'myka.medina@sampleuser.com', '09899889988', '2026-04-12', 1),
(12, 'Carl', 'Fallaria', 'carl.fallaria@samplemail.com', '09123457777', '2026-04-09', 1);

-- --------------------------------------------------------

--
-- Table structure for table `borroweruser`
--

CREATE TABLE `borroweruser` (
  `bu_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `borrower_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borroweruser`
--

INSERT INTO `borroweruser` (`bu_id`, `user_id`, `borrower_id`) VALUES
(1, 2, 1),
(2, 3, 2),
(3, 4, 3),
(4, 5, 4),
(5, 6, 5),
(6, 7, 6),
(7, 8, 7),
(8, 9, 8),
(9, 10, 9),
(10, 11, 10),
(11, 18, 11),
(12, 19, 12);

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

CREATE TABLE `genres` (
  `genre_id` int(11) NOT NULL,
  `genre_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `genres`
--

INSERT INTO `genres` (`genre_id`, `genre_name`) VALUES
(1, 'Classic'),
(6, 'Fantasy'),
(2, 'Historical Fiction'),
(3, 'Mystery/Crime'),
(5, 'Philippine Literature'),
(4, 'Political Fiction');

-- --------------------------------------------------------

--
-- Table structure for table `loan`
--

CREATE TABLE `loan` (
  `loan_id` int(11) NOT NULL,
  `borrower_id` int(11) NOT NULL,
  `processed_by_user_id` int(11) NOT NULL,
  `loan_date` date DEFAULT NULL,
  `loan_status` enum('OPEN','CLOSED','CANCELLED') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan`
--

INSERT INTO `loan` (`loan_id`, `borrower_id`, `processed_by_user_id`, `loan_date`, `loan_status`) VALUES
(1001, 1, 1, '2025-10-03', 'CLOSED'),
(1002, 2, 1, '2025-12-12', 'CLOSED'),
(1003, 3, 1, '2026-01-10', 'OPEN'),
(1004, 4, 1, '2026-02-15', 'OPEN'),
(1005, 6, 1, '2025-08-20', 'CLOSED'),
(1006, 2, 1, '2025-03-05', 'CLOSED');

-- --------------------------------------------------------

--
-- Table structure for table `loanitem`
--

CREATE TABLE `loanitem` (
  `loan_item_id` int(11) NOT NULL,
  `loan_id` int(11) NOT NULL,
  `copy_id` int(11) NOT NULL,
  `li_duedate` date DEFAULT NULL,
  `li_returned_at` date DEFAULT NULL,
  `condition_out` enum('GOOD','DAMAGED') DEFAULT NULL,
  `condition_in` enum('GOOD','DAMAGED','NULL') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loanitem`
--

INSERT INTO `loanitem` (`loan_item_id`, `loan_id`, `copy_id`, `li_duedate`, `li_returned_at`, `condition_out`, `condition_in`) VALUES
(5001, 1001, 101, '2025-10-10', '2025-10-09', 'GOOD', 'GOOD'),
(5002, 1001, 201, '2025-10-10', '2025-10-12', 'GOOD', 'DAMAGED'),
(5003, 1002, 402, '2025-12-19', '2025-12-18', 'GOOD', 'GOOD'),
(5004, 1003, 102, '2026-01-17', NULL, 'GOOD', NULL),
(5005, 1004, 401, '2026-02-22', NULL, 'GOOD', NULL),
(5006, 1004, 301, '2026-02-22', '2026-02-20', 'GOOD', 'GOOD'),
(5007, 1005, 202, '2025-08-27', '2025-08-27', 'GOOD', 'GOOD'),
(5008, 1006, 103, '2025-03-12', '2025-03-11', 'GOOD', 'GOOD'),
(5009, 1006, 501, '2025-03-12', '2025-03-16', 'GOOD', 'GOOD');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'ADMIN'),
(2, 'BORROWER');

-- --------------------------------------------------------

--
-- Table structure for table `userroles`
--

CREATE TABLE `userroles` (
  `ur_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userroles`
--

INSERT INTO `userroles` (`ur_id`, `user_id`, `role_id`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 2),
(4, 4, 2),
(5, 5, 2),
(6, 6, 2),
(7, 7, 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `user_password_hash` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `user_password_hash`, `is_active`, `created_at`) VALUES
(1, 'admin.library@samplemail.com', '$2y$10$adminDummyHashReplaceInPHP', 1, '2025-01-01 00:00:00'),
(2, 'juan.delacruz@samplemail.com', '$2y$10$juanDummyHashReplaceInPHP', 1, '2024-06-10 01:00:00'),
(3, 'maria.santos@samplemail.com', '$2y$10$mariaDummyHashReplaceInPHP', 1, '2023-09-22 01:00:00'),
(4, 'mark.reyes@samplemail.com', '$2y$10$markDummyHashReplaceInPHP', 1, '2025-01-15 01:00:00'),
(5, 'ana.bautista@samplemail.com', '$2y$10$anaDummyHashReplaceInPHP', 1, '2024-11-05 01:00:00'),
(6, 'paolo.garcia@samplemail.com', '$2y$10$paoloDummyHashReplaceInPHP', 0, '2022-07-19 01:00:00'),
(7, 'grace.mendoza@samplemail.com', '$2y$10$graceDummyHashReplaceInPHP', 1, '2025-07-02 01:00:00'),
(8, 'miguel.go@samplegmail.com', '$2y$10$hcwmzoEC3uEhgq5NSwCXguRoKeoe4zNiyHyM/8wX8RvSXJAQGxx92', 1, '2026-04-06 10:27:17'),
(9, 'jei.pastrana@sampleemail.com', '$2y$10$rALM3eZ6WfSxnFQI77EPR.5NIzWzEvAXLobqycvMsYbHhbr4KlS16', 1, '2026-04-06 10:29:39'),
(10, 'jhovan.busita@samplemail.com', '$2y$10$PkFjYKetitGujhXCt2aghuPp9hSvlLu7hH6cA0AaS9FzkN9idhahi', 1, '2026-04-06 10:38:42'),
(11, 'angela.mandigma@samplemail.com', '$2y$10$ymTUfEsLOCZQyxqaV.taFOToiIAbgA8RR.4HGFBG3uCbopuPBaeK6', 1, '2026-04-13 08:14:34'),
(18, 'myka.medina@sampleuser.com', '$2y$10$wQS88MEHb37uhYK60/fySuv14/IkXVFPN4DsilHeRG4CJM8.7xYKG', 1, '2026-04-13 08:54:03'),
(19, 'carl.fallaria@samplemail.com', '$2y$10$IHg/ZdPxZmadmj27j70E5.Vsjgzl9gZ1S9yPzRZUGqQhupCOX6FZi', 1, '2026-04-13 08:56:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`author_id`);

--
-- Indexes for table `bookauthors`
--
ALTER TABLE `bookauthors`
  ADD PRIMARY KEY (`baba_id`),
  ADD UNIQUE KEY `author_id` (`author_id`,`book_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `bookcopy`
--
ALTER TABLE `bookcopy`
  ADD PRIMARY KEY (`copy_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `bookgenre`
--
ALTER TABLE `bookgenre`
  ADD PRIMARY KEY (`gb_id`),
  ADD UNIQUE KEY `genre_id` (`genre_id`,`book_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`),
  ADD UNIQUE KEY `book_isbn` (`book_isbn`);

--
-- Indexes for table `borroweraddress`
--
ALTER TABLE `borroweraddress`
  ADD PRIMARY KEY (`ba_id`),
  ADD KEY `borrower_id` (`borrower_id`);

--
-- Indexes for table `borrowers`
--
ALTER TABLE `borrowers`
  ADD PRIMARY KEY (`borrower_id`),
  ADD UNIQUE KEY `borrower_email` (`borrower_email`);

--
-- Indexes for table `borroweruser`
--
ALTER TABLE `borroweruser`
  ADD PRIMARY KEY (`bu_id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`borrower_id`),
  ADD KEY `borrower_id` (`borrower_id`);

--
-- Indexes for table `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`genre_id`),
  ADD UNIQUE KEY `genre_name` (`genre_name`);

--
-- Indexes for table `loan`
--
ALTER TABLE `loan`
  ADD PRIMARY KEY (`loan_id`),
  ADD KEY `borrower_id` (`borrower_id`),
  ADD KEY `processed_by_user_id` (`processed_by_user_id`);

--
-- Indexes for table `loanitem`
--
ALTER TABLE `loanitem`
  ADD PRIMARY KEY (`loan_item_id`),
  ADD UNIQUE KEY `copy_id` (`copy_id`,`loan_id`),
  ADD KEY `loan_id` (`loan_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `userroles`
--
ALTER TABLE `userroles`
  ADD PRIMARY KEY (`ur_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `authors`
--
ALTER TABLE `authors`
  MODIFY `author_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `bookauthors`
--
ALTER TABLE `bookauthors`
  MODIFY `baba_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `bookcopy`
--
ALTER TABLE `bookcopy`
  MODIFY `copy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=602;

--
-- AUTO_INCREMENT for table `bookgenre`
--
ALTER TABLE `bookgenre`
  MODIFY `gb_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `borroweraddress`
--
ALTER TABLE `borroweraddress`
  MODIFY `ba_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `borrowers`
--
ALTER TABLE `borrowers`
  MODIFY `borrower_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `borroweruser`
--
ALTER TABLE `borroweruser`
  MODIFY `bu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `genres`
--
ALTER TABLE `genres`
  MODIFY `genre_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `loan`
--
ALTER TABLE `loan`
  MODIFY `loan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1007;

--
-- AUTO_INCREMENT for table `loanitem`
--
ALTER TABLE `loanitem`
  MODIFY `loan_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5010;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `userroles`
--
ALTER TABLE `userroles`
  MODIFY `ur_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookauthors`
--
ALTER TABLE `bookauthors`
  ADD CONSTRAINT `bookauthors_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`),
  ADD CONSTRAINT `bookauthors_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `authors` (`author_id`);

--
-- Constraints for table `bookcopy`
--
ALTER TABLE `bookcopy`
  ADD CONSTRAINT `bookcopy_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`);

--
-- Constraints for table `bookgenre`
--
ALTER TABLE `bookgenre`
  ADD CONSTRAINT `bookgenre_ibfk_1` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`genre_id`),
  ADD CONSTRAINT `bookgenre_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`);

--
-- Constraints for table `borroweraddress`
--
ALTER TABLE `borroweraddress`
  ADD CONSTRAINT `borroweraddress_ibfk_1` FOREIGN KEY (`borrower_id`) REFERENCES `borrowers` (`borrower_id`);

--
-- Constraints for table `borroweruser`
--
ALTER TABLE `borroweruser`
  ADD CONSTRAINT `borroweruser_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `borroweruser_ibfk_2` FOREIGN KEY (`borrower_id`) REFERENCES `borrowers` (`borrower_id`);

--
-- Constraints for table `loan`
--
ALTER TABLE `loan`
  ADD CONSTRAINT `loan_ibfk_1` FOREIGN KEY (`borrower_id`) REFERENCES `borrowers` (`borrower_id`),
  ADD CONSTRAINT `loan_ibfk_2` FOREIGN KEY (`processed_by_user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `loanitem`
--
ALTER TABLE `loanitem`
  ADD CONSTRAINT `loanitem_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loan` (`loan_id`),
  ADD CONSTRAINT `loanitem_ibfk_2` FOREIGN KEY (`copy_id`) REFERENCES `bookcopy` (`copy_id`);

--
-- Constraints for table `userroles`
--
ALTER TABLE `userroles`
  ADD CONSTRAINT `userroles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `userroles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
