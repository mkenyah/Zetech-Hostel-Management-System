-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 16, 2025 at 03:19 PM
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
-- Database: `hostel_m`
--

-- --------------------------------------------------------

--
-- Table structure for table `allocations`
--

CREATE TABLE `allocations` (
  `allocation_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `bed_id` int(11) DEFAULT NULL,
  `allocated_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `allocations_from` datetime NOT NULL,
  `allocations_to` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('Active','Expired') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `allocations`
--

INSERT INTO `allocations` (`allocation_id`, `student_id`, `bed_id`, `allocated_date`, `allocations_from`, `allocations_to`, `user_id`, `status`) VALUES
(2, 2, 2, '2025-04-04 10:26:07', '2025-04-04 13:25:00', '2025-07-04 13:25:00', 10, 'Active'),
(3, 1, 1, '2025-04-04 12:42:44', '2025-04-04 15:42:00', '2025-04-16 10:28:00', 10, 'Expired'),
(4, 3, 3, '2025-04-04 12:52:57', '2025-04-04 15:52:00', '2025-07-04 15:52:00', 10, 'Active'),
(5, 4, 4, '2025-04-04 18:03:07', '2025-04-04 21:02:00', '2025-04-14 21:02:00', 10, 'Expired'),
(6, 5, 5, '2025-04-07 14:10:23', '2025-04-07 17:10:00', '2025-07-07 17:10:00', 10, 'Active'),
(7, 8, 6, '2025-04-10 15:01:15', '2025-04-10 17:55:00', '2025-07-10 17:56:00', 10, 'Active'),
(11, 7, 7, '2025-04-10 15:12:32', '2025-04-10 18:12:00', '2025-07-10 18:12:00', 10, 'Active'),
(12, 6, 8, '2025-04-10 15:14:30', '2025-04-10 18:14:00', '2025-07-10 18:14:00', 10, 'Active'),
(13, 9, 9, '2025-04-14 08:05:15', '2025-04-14 11:04:00', '2025-07-14 11:05:00', 14, 'Active'),
(14, 13, 10, '2025-04-15 13:37:08', '2025-04-07 16:34:00', '2025-04-14 16:36:00', 14, 'Expired'),
(15, 1, 12, '2025-04-16 10:01:16', '2025-04-16 13:00:00', '2025-07-16 13:01:00', 10, 'Active');

--
-- Triggers `allocations`
--
DELIMITER $$
CREATE TRIGGER `update_bed_status_on_allocation` AFTER INSERT ON `allocations` FOR EACH ROW BEGIN
    -- Update the status of the bed to 'Occupied' after it is allocated
    UPDATE beds 
    SET status = 'Occupied'
    WHERE bed_id = NEW.bed_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `beds`
--

CREATE TABLE `beds` (
  `bed_id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `bed_number` varchar(50) NOT NULL,
  `status` enum('Available','Occupied') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `beds`
--

INSERT INTO `beds` (`bed_id`, `room_id`, `bed_number`, `status`) VALUES
(1, 1, 'zu/b/01/2023', 'Available'),
(2, 1, 'zu/b/02/2025', 'Occupied'),
(3, 1, 'zu/b/03/2025', 'Occupied'),
(4, 3, 'zu/A/01/2025', 'Available'),
(5, 1, 'zu/b/04/2025', 'Occupied'),
(6, 3, 'zu/A/05/2025', 'Occupied'),
(7, 1, 'zu/b/06/2025', 'Occupied'),
(8, 1, 'zu/b/07/2025', 'Occupied'),
(9, 3, 'zu/b/08/2025', 'Occupied'),
(10, 3, 'zu/b/09/2025', 'Available'),
(11, 3, 'zu/A/10/2025', 'Available'),
(12, 1, 'zu/b/12/2025', 'Occupied');

-- --------------------------------------------------------

--
-- Table structure for table `campus`
--

CREATE TABLE `campus` (
  `campus_id` int(11) NOT NULL,
  `campus_name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `campus`
--

INSERT INTO `campus` (`campus_id`, `campus_name`, `location`) VALUES
(1, 'TRC Campus', 'Ruiru'),
(2, 'Town Campus', 'Nairobi'),
(3, 'Mang\'u Campus', 'Weteithie'),
(4, 'kisumu Campus', 'kisumu');

-- --------------------------------------------------------

--
-- Table structure for table `hostels`
--

CREATE TABLE `hostels` (
  `hostel_id` int(11) NOT NULL,
  `hostel_name` varchar(255) NOT NULL,
  `campus_id` int(11) DEFAULT NULL,
  `capacity` int(11) NOT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hostels`
--

INSERT INTO `hostels` (`hostel_id`, `hostel_name`, `campus_id`, `capacity`, `gender`, `user_id`) VALUES
(1, 'Batian', 1, 20, 'Male', 13),
(2, 'Flamingo', 1, 40, 'Female', 14),
(3, 'Elgon', 1, 20, 'Male', 13),
(4, 'Haven', 2, 40, 'Female', 14),
(5, 'multiple', 2, 24, 'Female', 14),
(7, 'common', 3, 28, 'Male', 13),
(8, 'New_jersey', 3, 42, 'Male', 14),
(9, 'BAM', 3, 28, 'Male', 14),
(10, 'many_bees', 3, 52, 'Female', 13),
(11, 'pacific', 1, 20, 'Female', 14);

-- --------------------------------------------------------

--
-- Table structure for table `hostel_services`
--

CREATE TABLE `hostel_services` (
  `service_id` int(11) NOT NULL,
  `hostel_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hostel_services`
--

INSERT INTO `hostel_services` (`service_id`, `hostel_id`) VALUES
(6, 7),
(7, 7),
(1, 8),
(5, 8),
(7, 8),
(5, 9),
(6, 9),
(7, 9),
(1, 10),
(5, 10),
(6, 10),
(7, 10),
(1, 5),
(7, 5),
(5, 4),
(6, 4),
(7, 4),
(5, 3),
(7, 3),
(1, 2),
(6, 2),
(7, 2),
(1, 11),
(5, 11),
(7, 11),
(1, 1),
(7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` text NOT NULL,
  `log_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`log_id`, `user_id`, `action`, `log_date`) VALUES
(397, 10, 'Deleted all logs', '2025-04-15 06:19:25'),
(399, 10, 'logged out', '2025-04-15 06:20:08'),
(400, 10, 'Allocated a student', '2025-04-15 06:20:22'),
(401, 10, 'logged out', '2025-04-15 06:23:16'),
(402, 10, 'logged out', '2025-04-15 06:35:23'),
(403, 10, 'logged out', '2025-04-15 06:37:45'),
(404, 10, 'logged out', '2025-04-15 07:19:56'),
(405, 10, 'logged out', '2025-04-15 07:20:06'),
(406, 10, 'logged out', '2025-04-15 07:22:53'),
(407, 10, 'logged out', '2025-04-15 07:28:27'),
(408, 10, 'logged out', '2025-04-15 07:34:18'),
(409, 10, 'logged out', '2025-04-15 07:36:59'),
(410, 10, 'logged out', '2025-04-15 07:43:30'),
(411, 14, 'Edited allocation ID 4', '2025-04-15 08:01:54'),
(412, 14, 'logged out', '2025-04-15 08:20:35'),
(413, 14, 'logged out', '2025-04-15 08:24:21'),
(414, 10, 'logged out', '2025-04-15 08:39:01'),
(415, 10, 'Edited allocation ID 3', '2025-04-15 08:42:17'),
(416, 10, 'logged out', '2025-04-15 08:42:21'),
(417, 10, 'logged out', '2025-04-15 08:45:19'),
(418, 10, 'logged out', '2025-04-15 09:21:33'),
(419, 10, 'logged out', '2025-04-15 09:26:14'),
(420, 10, 'logged out', '2025-04-15 13:33:13'),
(421, NULL, 'Added a new bed: zu/b/09/2025', '2025-04-15 13:33:40'),
(422, 10, 'logged out', '2025-04-15 13:33:47'),
(423, 14, 'Allocated a student', '2025-04-15 13:37:08'),
(424, 10, 'logged out', '2025-04-15 13:37:37'),
(425, NULL, 'Added a new bed: zu/A/10/2025', '2025-04-15 13:41:25'),
(426, 10, 'logged out', '2025-04-15 13:41:30'),
(427, 10, 'logged out', '2025-04-15 13:58:18'),
(428, 10, 'logged out', '2025-04-15 13:59:29'),
(429, 10, 'logged out', '2025-04-16 06:15:40'),
(430, 10, 'logged out', '2025-04-16 06:17:29'),
(431, 10, 'logged out', '2025-04-16 06:18:30'),
(432, 10, 'logged out', '2025-04-16 06:20:02'),
(433, 10, 'logged out', '2025-04-16 07:24:32'),
(434, 10, 'logged out', '2025-04-16 07:31:02'),
(435, 10, 'logged out', '2025-04-16 07:46:22'),
(436, 10, 'logged out', '2025-04-16 07:46:48'),
(437, 10, 'logged out', '2025-04-16 08:52:00'),
(438, 10, 'logged out', '2025-04-16 08:52:58'),
(439, 10, 'logged out', '2025-04-16 08:53:11'),
(440, 10, 'logged out', '2025-04-16 08:55:28'),
(441, 10, 'logged out', '2025-04-16 08:57:45'),
(442, 10, 'logged out', '2025-04-16 08:59:20'),
(443, 10, 'logged out', '2025-04-16 09:01:57'),
(444, 10, 'logged out', '2025-04-16 09:24:53'),
(445, 10, 'logged out', '2025-04-16 09:52:33'),
(446, NULL, 'Added a new bed: zu/b/12/2025', '2025-04-16 09:57:58'),
(447, 10, 'logged out', '2025-04-16 09:58:11'),
(448, 10, 'logged out', '2025-04-16 09:59:59'),
(449, 10, 'Allocated a student', '2025-04-16 10:01:16'),
(450, 10, 'logged out', '2025-04-16 11:38:34'),
(451, 10, 'logged out', '2025-04-16 11:50:20'),
(452, 10, 'logged out', '2025-04-16 12:00:40'),
(453, 10, 'logged out', '2025-04-16 12:32:07'),
(454, 10, 'logged out', '2025-04-16 12:33:04'),
(455, 10, 'logged out', '2025-04-16 12:39:07'),
(456, 10, 'logged out', '2025-04-16 12:56:19'),
(457, 14, 'logged out', '2025-04-16 13:09:24'),
(458, 14, 'logged out', '2025-04-16 13:13:45');

-- --------------------------------------------------------

--
-- Table structure for table `requirements`
--

CREATE TABLE `requirements` (
  `requirement_id` int(11) NOT NULL,
  `requirement_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requirements`
--

INSERT INTO `requirements` (`requirement_id`, `requirement_name`) VALUES
(1, 'beddings'),
(2, 'toiletries'),
(3, '2basins'),
(4, 'matress');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `hostel_id` int(11) DEFAULT NULL,
  `room_number` varchar(50) NOT NULL,
  `bed_capacity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `hostel_id`, `room_number`, `bed_capacity`) VALUES
(1, 1, '1', 4),
(3, 2, 'A', 4);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `service_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `service_name`) VALUES
(1, 'cleaning'),
(5, 'swimming pool'),
(6, 'entertainment'),
(7, 'WIFI');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `admission_number` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'Student'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `full_name`, `admission_number`, `email`, `contact`, `username`, `password`, `gender`, `role`) VALUES
(1, 'Joseph kirika', 'Dse-01-8270/2023', 'kirikajoseph16@gmail.com', '0769200240', 'kirika', '$2y$10$kuSm6/SNORFL2bGYjri//O5BYLVvrKMlsekS14g/5nefTSnuvxcWe', 'male', 'Student'),
(2, 'franklin wambua', 'BIT-01-4270/2024', 'franklin@gmail.com', '0700861475', 'Franklin', '$2y$10$Nx1QSGuXeru.yckvB9sCUOFlKB5LHDEpzrL6LyKVv7EW8CnsHx4ly', 'Male', 'Student'),
(3, 'Shawn juja', 'Dse-01-8271/2023', 'jujashawn@gmail.com', '0798761861', 'Shawn', '$2y$10$l4H6iI6cUr0V9LceCDuICu7GVo/WxWsKSm1aUf80LkHA0TXzlF9Vi', 'Male', 'Student'),
(4, 'mary wambui', 'Bse-01-1720/2021', 'marywambui57@gmail.com', '0773297677', 'MARY', '$2y$10$LTNc7pP1uJQ0H5o33GeVZOopGOd1ekBxAOyEcfYz0M8XncxYyWhYe', 'Female', 'Student'),
(5, 'test Joseh', 'BIT-01-7211/2024', 'test@gmail.com', '0789456789', 'test', '$2y$10$CUVMnl4LNutTReqQ9cW65e9wO5FUViu5ywrQbmbDaYnK8/6T/g.i.', 'Male', 'Student'),
(6, 'Isaac mwaura', 'Dse-01-7281/2023', 'mwauraisaac@gmail.com', '0798761861', 'Mwaura', '$2y$10$0Qd7sNlbL4HtFCZUdoPbv.P4qQg40JIYzN7uoKtn8cp6//2g4L1Su', 'Male', 'Student'),
(7, 'earnest samba', 'Dse-01-0429/2022', 'samabaeranest@gmail.com', '0789300768', 'Earnest', '$2y$10$uD.qFHjDh0ijHnU.l5.uCOEY/K7uXPg49g0hycnlRx2GgLqU0yKMy', 'Male', 'Student'),
(8, 'sheila nyongeno', 'Dse-01-5671/2023', 'sheila@gmail.com', '0789456789', 'sheila', '$2y$10$FGv6iGpGK1ccxag5/se46eCCovVyaRCAku8Tk0XvmLsms8Hxmabh6', 'Female', 'Student'),
(9, 'Banice best', 'Dse-01-7289/2022', 'barnice@gmail.com', '0769200240', 'bernice', '$2y$10$u8V7eY3pseFSuBSrpcOKbu1z86hHpZ4WZp1QAwY1wdAKxOd6LdzzG', 'Female', 'Student'),
(10, 'joseph muroki', 'Dse-01-8274/2023', 'murokijoseph@gmail.com', '0773297677', 'muroki', '$2y$10$5c2aNDf57sQ/Z3vxV3x34eRrLusVHrFyasR6Lny.sbMtfSypSiYQe', 'Male', 'Student'),
(12, 'Evans kamau', 'BIT-01-4207/2021', 'kamauevans@gmail.com', '0798761861', 'Evans', '$2y$10$zGYaJ4vE7rrx2mMaLbSdceW2JJNDnxBgwE7Wpf7YF1iVZmlN/Q3s6', 'Male', 'Student'),
(13, 'Susan mugure', 'Dmp-01118/2025', 'muguresusan@gmail.com', '0113055875', 'susan', '$2y$10$.Y8aTYfIhIBzfLDXAipQdexsEStoBIE7rWK1UgDPF6pTvK6IUTB.G', 'Female', 'Student'),
(14, 'Vincent mucai', 'Dcs-01-4567/2025', 'mucaivincent@gmail.com', '0745321008', 'Vincent', '$2y$10$zGDKzou5/Q3ZsmqRVNaxTe8jDXtU9yBVf5PxuNZF42eqy4Vr733Re', 'Male', 'Student'),
(15, 'John mwangi', 'Bit-01-8900/2025', 'mwangijohn@gmail.com', '01167890012', 'mwangi', '$2y$10$84LfLEFlGVHQkL7RNJAYCeoAP4.AiQ0/wBfdiso5zRiB3WknB3Hhy', 'Male', 'Student'),
(16, 'george kiriga', 'Bse-01-1721/2025', 'kirigageorge@gmail.com', '0789543907', 'kiriga', '$2y$10$AWudipSjXIJdoSamj3gCO.rKtKuHT8/raxwQMk/Ma4i70QUcIf2hK', 'Male', 'Student'),
(17, 'Dennese cosby', 'Bbit-01-2345/2001', 'cosbydennese@gmail.com', '0113455987', 'dennese', '$2y$10$l4PgoycAOJ/5AgvPuVU7leaKlEAUQ.LaO9m7pu.bIed1mT/5eEPAy', 'Female', 'Student');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `created_at`) VALUES
(10, 'admin', '$2y$10$nT0QnCS0NixFGL9bMaLv8u3JY.8584vYglRtd9sD5h.j/EI7NJd/W', 'Admin', '2025-03-31 11:46:20'),
(13, 'john mwangi', '$2y$10$rTWOuQpMV1FX0XWM3COebOuyVW4ySTTo5bKLt996/Qx1gE05J13Eq', 'house_manager', '2025-04-03 11:37:02'),
(14, 'christine', '$2y$10$ed5txB177G2OG2g.IUrsR.s.CCrSLZSrGLjg.IhqwqG.rVIDN50hq', 'house_manager', '2025-04-04 17:57:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `allocations`
--
ALTER TABLE `allocations`
  ADD PRIMARY KEY (`allocation_id`);

--
-- Indexes for table `beds`
--
ALTER TABLE `beds`
  ADD PRIMARY KEY (`bed_id`),
  ADD UNIQUE KEY `bed_number` (`bed_number`),
  ADD KEY `beds_ibfk_1` (`room_id`);

--
-- Indexes for table `campus`
--
ALTER TABLE `campus`
  ADD PRIMARY KEY (`campus_id`),
  ADD UNIQUE KEY `campus_name` (`campus_name`);

--
-- Indexes for table `hostels`
--
ALTER TABLE `hostels`
  ADD PRIMARY KEY (`hostel_id`),
  ADD UNIQUE KEY `hostel_name` (`hostel_name`),
  ADD KEY `hostels_ibfk_1` (`campus_id`);

--
-- Indexes for table `hostel_services`
--
ALTER TABLE `hostel_services`
  ADD KEY `fk_service_id` (`service_id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `requirements`
--
ALTER TABLE `requirements`
  ADD PRIMARY KEY (`requirement_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD UNIQUE KEY `room_number` (`room_number`),
  ADD KEY `rooms_ibfk_1` (`hostel_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `admission_number` (`admission_number`),
  ADD UNIQUE KEY `email` (`email`);

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
-- AUTO_INCREMENT for table `allocations`
--
ALTER TABLE `allocations`
  MODIFY `allocation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `beds`
--
ALTER TABLE `beds`
  MODIFY `bed_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `campus`
--
ALTER TABLE `campus`
  MODIFY `campus_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `hostels`
--
ALTER TABLE `hostels`
  MODIFY `hostel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=459;

--
-- AUTO_INCREMENT for table `requirements`
--
ALTER TABLE `requirements`
  MODIFY `requirement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `allocations`
--
ALTER TABLE `allocations`
  ADD CONSTRAINT `allocations_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `allocations_ibfk_3` FOREIGN KEY (`bed_id`) REFERENCES `beds` (`bed_id`) ON DELETE CASCADE;

--
-- Constraints for table `beds`
--
ALTER TABLE `beds`
  ADD CONSTRAINT `beds_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON UPDATE CASCADE;

--
-- Constraints for table `hostels`
--
ALTER TABLE `hostels`
  ADD CONSTRAINT `hostels_ibfk_1` FOREIGN KEY (`campus_id`) REFERENCES `campus` (`campus_id`) ON UPDATE CASCADE;

--
-- Constraints for table `hostel_services`
--
ALTER TABLE `hostel_services`
  ADD CONSTRAINT `fk_service_id` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`hostel_id`) REFERENCES `hostels` (`hostel_id`) ON UPDATE CASCADE;

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `check_expired_allocations` ON SCHEDULE EVERY 1 HOUR STARTS '2025-04-16 11:42:41' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    -- Update allocations that are past due and still marked as Active
    UPDATE allocations a
    JOIN beds b ON a.bed_id = b.bed_id
    SET a.status = 'Expired',
        b.status = 'Available'
    WHERE a.allocations_to < NOW()
      AND a.status = 'Active';
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
