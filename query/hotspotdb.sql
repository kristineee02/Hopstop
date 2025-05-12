-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 11, 2025 at 06:17 PM
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
-- Database: `hotspotdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) AUTO_INCREMENT PRIMARY KEY,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
); ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `first_name`, `last_name`, `email`, `password`) VALUES
(1, 'Hop', 'Stop', 'admin@gmail.com', '$2y$10$L9pfNkpd0saf5yVFf5O8J.Uivc1hwg6pIgq7/Wg5fFEW7um4xBFp2');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE bookings (
  `booking_id` int(11) AUTO_INCREMENT PRIMARY KEY,
  `passenger_id` int(11),
  `bus_id` int(11),
  `passenger_type` enum('Regular','PWD/Senior Citizen','Student') NOT NULL,
  `seat_number` int(11) NOT NULL,
  `id_upload_path` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
  FOREIGN KEY(passenger_id) REFERENCES passenger(passenger_id),
  FOREIGN KEY(bus_id) REFERENCES buses(bus_id)
); ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `buses`
--

CREATE TABLE `buses` (
  `bus_id` int(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `bus_number` varchar(50) NOT NULL,
  `location` varchar(50) NOT NULL,
  `destination` varchar(50) NOT NULL,
  `bus_type` enum('Air-conditioned','Regular') NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `available_seats` int(11) NOT NULL DEFAULT 30,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
); ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buses`
--

INSERT INTO `buses` (`bus_id`, `bus_number`, `location`, `destination`, `bus_type`, `date`, `time`, `available_seats`, `price`, `created_at`) VALUES
(1, 'BUS1001', 'Zamboanga City', 'Pagadian City', 'Air-conditioned', '2025-05-01', '08:00:00', 30, 800.00, '2025-04-29 00:02:06'),
(2, 'BUS1002', 'Pagadian City', 'Zamboanga City', 'Regular', '2025-05-01', '09:00:00', 30, 800.00, '2025-04-29 00:02:06'),
(3, 'BUS1003', 'Zamboanga City', 'Dipolog', 'Air-conditioned', '2025-05-02', '10:00:00', 30, 900.00, '2025-04-29 00:02:06'),
(4, 'BUS1004', 'Dipolog', 'Zamboanga City', 'Regular', '2025-05-03', '11:00:00', 30, 900.00, '2025-04-29 00:02:06');

-- --------------------------------------------------------

--
-- Table structure for table `passenger`
--

CREATE TABLE `passenger` (
  `passenger_id` int(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `picture` varchar(255) NOT NULL
); ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `passenger`
--

INSERT INTO `passenger` (`passenger_id`, `first_name`, `last_name`, `email`, `password`, `picture`) VALUES
(30, 'stephanie', 'villamor', 'stephanie@gmail.com', '12345', ''),
(31, 'ashley', 'quicho', 'ashley@gmail.com', '123456', ''),
(32, 'kristie', 'sabuero', 'kris@gmail.com', '12345', ''),
(33, 'ashley', 'Quicho', 'Ash@gmail.com', '12345', ''),
(34, 'Hop', 'Stop', 'admin@gmail.com', 'Admin123', ''),
(35, 'kris', 'Tine', 'admin@gmail.com', '$2y$10$VZ5Pb8El1DrnePh94ZaXCeOFapycHO3ou78pILoDeA7', ''),
(36, 'mar', 'talaid', 'mar@gmail.com', '$2y$10$NIn0klicxoh3/n5vvbGvi.rtWF3yOfGAUn3R/fgfVo.', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `passenger_id` (`passenger_id`),
  ADD KEY `bus_id` (`bus_id`);

--
-- Indexes for table `buses`
--
ALTER TABLE `buses`
  ADD PRIMARY KEY (`bus_id`);

--
-- Indexes for table `passenger`
--
ALTER TABLE `passenger`
  ADD PRIMARY KEY (`passenger_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `buses`
--
ALTER TABLE `buses`
  MODIFY `bus_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `passenger`
--
ALTER TABLE `passenger`
  MODIFY `passenger_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`passenger_id`) REFERENCES `passenger` (`passenger_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`bus_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
