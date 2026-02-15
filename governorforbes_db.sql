-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 15, 2026 at 10:41 AM
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
-- Database: `governorforbes_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `amenity`
--

CREATE TABLE `amenity` (
  `AmenityID` varchar(255) NOT NULL,
  `AmenityName` varchar(255) NOT NULL,
  `Description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `billing_invoice`
--

CREATE TABLE `billing_invoice` (
  `InvoiceID` varchar(255) NOT NULL,
  `ReservationID` varchar(255) NOT NULL,
  `InvoiceDate` datetime NOT NULL,
  `TotalDue` float NOT NULL,
  `TotalPaid` float NOT NULL,
  `Balance` float NOT NULL,
  `InvoiceStatus` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `CustomerID` varchar(255) NOT NULL,
  `LastName` varchar(255) NOT NULL,
  `FirstName` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `PhoneNumber` varchar(255) NOT NULL,
  `RegistrationDate` datetime(6) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `DataPrivacyConsent` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`CustomerID`, `LastName`, `FirstName`, `Email`, `PhoneNumber`, `RegistrationDate`, `PasswordHash`, `DataPrivacyConsent`) VALUES
('cust_698b975857b68', 'Doe', 'John', 'johndoe@gmail.com', '09123456789', '2026-02-11 04:38:48.000000', '$2y$10$JBOf.YOkBsHH85lYKAnJZ.xNvHqvagdcPpa4.zw74SoaFtbgTG9V6', 1),
('cust_698c0949b059c', 'Mendiola', 'Sherisse Nicolle', 'mendiola@gmail.com', '09617688998', '2026-02-11 12:44:57.000000', '$2y$10$CVhjRMFSPhAT9IIVki0wouUsxb/np9FzQ4A8OTaWmM8C6bVYMIQBm', 1);

-- --------------------------------------------------------

--
-- Table structure for table `extra_items`
--

CREATE TABLE `extra_items` (
  `ItemID` varchar(255) NOT NULL,
  `ItemName` varchar(255) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `UnitPrice` varchar(255) NOT NULL,
  `ItemType` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `PaymentID` varchar(255) NOT NULL,
  `ReservationID` varchar(255) NOT NULL,
  `PaymentDate` datetime NOT NULL,
  `AmountPaid` float NOT NULL,
  `PaymentMethod` varchar(255) NOT NULL,
  `TransactionRefNumber` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promotion`
--

CREATE TABLE `promotion` (
  `PromoID` varchar(255) NOT NULL,
  `PromoCode` varchar(255) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `DiscountPercentage` float NOT NULL,
  `StaffDate` datetime NOT NULL,
  `Status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservation_room`
--

CREATE TABLE `reservation_room` (
  `ReservationRoomID` varchar(255) NOT NULL,
  `ReservationID` varchar(255) NOT NULL,
  `RoomID` varchar(255) NOT NULL,
  `PriceAtBooking` float NOT NULL,
  `CheckInTime` datetime NOT NULL,
  `CheckOutTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservation_room`
--

INSERT INTO `reservation_room` (`ReservationRoomID`, `ReservationID`, `RoomID`, `PriceAtBooking`, `CheckInTime`, `CheckOutTime`) VALUES
('RR698c02649ea34', 'RES698c02649c8ba', 'R203', 1999, '0000-00-00 00:00:00', '2026-02-15 00:00:00'),
('RR698c0bd50e914', 'RES698c0bd50db8d', 'R205', 1999, '0000-00-00 00:00:00', '2026-02-14 00:00:00'),
('RR698c0d0d5d393', 'RES698c0d0d5c22a', 'R207', 1999, '0000-00-00 00:00:00', '2026-02-15 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `RoomID` varchar(255) NOT NULL,
  `RoomNumber` int(11) NOT NULL,
  `RoomTypeID` varchar(255) NOT NULL,
  `BasePrice` float NOT NULL,
  `MaxCapacity` int(11) NOT NULL,
  `Status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`RoomID`, `RoomNumber`, `RoomTypeID`, `BasePrice`, `MaxCapacity`, `Status`) VALUES
('R201', 201, 'TWIN', 1899, 2, 'Available'),
('R202', 202, 'SUPERIOR', 2299, 2, 'Available'),
('R203', 203, 'FAMILY', 1999, 3, 'Booked'),
('R204', 204, 'QUADRUPLE', 2099, 4, 'Available'),
('R205', 205, 'FAMILY', 1999, 3, 'Booked'),
('R206', 206, 'QUADRUPLE', 2099, 4, 'Available'),
('R207', 207, 'FAMILY', 1999, 3, 'Booked'),
('R208', 208, 'QUADRUPLE', 2099, 4, 'Available'),
('R209', 209, 'FAMILY', 1999, 3, 'Available'),
('R210', 210, 'QUADRUPLE', 2099, 4, 'Available'),
('R211', 211, 'FAMILY', 1999, 3, 'Available'),
('R301', 301, 'TWIN', 1899, 2, 'Available'),
('R303', 303, 'FAMILY', 1999, 3, 'Available'),
('R304', 304, 'QUADRUPLE', 2099, 4, 'Available'),
('R305', 305, 'FAMILY', 1999, 3, 'Available'),
('R306', 306, 'QUADRUPLE', 2099, 4, 'Available'),
('R307', 307, 'FAMILY', 1999, 3, 'Available'),
('R308', 308, 'QUADRUPLE', 2099, 4, 'Available'),
('R309', 309, 'FAMILY', 1999, 3, 'Available'),
('R310', 310, 'QUADRUPLE', 2099, 4, 'Available'),
('R311', 311, 'FAMILY', 1999, 3, 'Available'),
('R401', 401, 'TWIN', 1899, 2, 'Available'),
('R402', 402, 'SUPERIOR', 2299, 2, 'Available'),
('R403', 403, 'FAMILY', 1999, 3, 'Available'),
('R404', 404, 'QUADRUPLE', 2099, 4, 'Available'),
('R405', 405, 'FAMILY', 1999, 3, 'Available'),
('R406', 406, 'QUADRUPLE', 2099, 4, 'Available'),
('R407', 407, 'FAMILY', 1999, 3, 'Available'),
('R408', 408, 'QUADRUPLE', 2099, 4, 'Available'),
('R409', 409, 'FAMILY', 1999, 3, 'Available'),
('R410', 410, 'QUADRUPLE', 2099, 4, 'Available'),
('R411', 411, 'FAMILY', 1999, 3, 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `room_amenity`
--

CREATE TABLE `room_amenity` (
  `RoomTypeID` varchar(255) NOT NULL,
  `AmenityID` varchar(255) NOT NULL,
  `Description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `room_type`
--

CREATE TABLE `room_type` (
  `RoomTypeID` varchar(255) NOT NULL,
  `TypeName` varchar(255) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `BasePrice` float NOT NULL,
  `MaxCapacity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_type`
--

INSERT INTO `room_type` (`RoomTypeID`, `TypeName`, `Description`, `BasePrice`, `MaxCapacity`) VALUES
('FAMILY', 'Family Room', 'Good for 3 pax. Includes 1 queen bed and 2 single beds.', 1999, 3),
('QUADRUPLE', 'Quadruple Room', 'Good for 4 pax. Includes 2 double beds.', 2099, 4),
('SUPERIOR', 'Superior Room', 'Good for 2 pax. Includes 1 queen bed, mini refrigerator, and 2-seater couch.', 2299, 2),
('TWIN', 'Twin Room', 'Good for 2 pax. Includes 2 single beds.', 1899, 2);

-- --------------------------------------------------------

--
-- Table structure for table `service_request`
--

CREATE TABLE `service_request` (
  `RequestID` varchar(255) NOT NULL,
  `ReservationRoomID` varchar(255) NOT NULL,
  `ItemID` varchar(255) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `RequestTime` time NOT NULL,
  `FulfilledByStaffID` varchar(255) NOT NULL,
  `Status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `StaffID` varchar(255) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `Role` varchar(255) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `ReservationID` varchar(255) NOT NULL,
  `CustomerID` varchar(255) NOT NULL,
  `CheckInDate` datetime(6) NOT NULL,
  `CheckOutDate` datetime(6) NOT NULL,
  `TotalAmount` float NOT NULL,
  `DownPayment` float NOT NULL,
  `PaymentStatus` varchar(255) NOT NULL,
  `ReservationStatus` varchar(255) NOT NULL,
  `BookingDate` datetime(6) NOT NULL,
  `Discount` varchar(255) NOT NULL,
  `PromoID` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`ReservationID`, `CustomerID`, `CheckInDate`, `CheckOutDate`, `TotalAmount`, `DownPayment`, `PaymentStatus`, `ReservationStatus`, `BookingDate`, `Discount`, `PromoID`) VALUES
('RES698c02649c8ba', 'cust_698b975857b68', '2026-02-12 00:00:00.000000', '2026-02-15 00:00:00.000000', 10497, 0, 'Pending', 'Cancelled', '2026-02-11 05:15:32.000000', '', ''),
('RES698c0bd50db8d', 'cust_698c0949b059c', '2026-02-12 00:00:00.000000', '2026-02-14 00:00:00.000000', 4998, 0, 'Pending', 'Booked', '2026-02-11 05:55:49.000000', '', ''),
('RES698c0d0d5c22a', 'cust_698b975857b68', '2026-02-12 00:00:00.000000', '2026-02-15 00:00:00.000000', 10497, 0, 'Pending', 'Cancelled', '2026-02-11 06:01:01.000000', '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `amenity`
--
ALTER TABLE `amenity`
  ADD PRIMARY KEY (`AmenityID`);

--
-- Indexes for table `billing_invoice`
--
ALTER TABLE `billing_invoice`
  ADD PRIMARY KEY (`InvoiceID`),
  ADD KEY `resrvationid` (`ReservationID`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`CustomerID`);

--
-- Indexes for table `extra_items`
--
ALTER TABLE `extra_items`
  ADD PRIMARY KEY (`ItemID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`PaymentID`),
  ADD KEY `reservationId` (`ReservationID`);

--
-- Indexes for table `promotion`
--
ALTER TABLE `promotion`
  ADD PRIMARY KEY (`PromoID`);

--
-- Indexes for table `reservation_room`
--
ALTER TABLE `reservation_room`
  ADD PRIMARY KEY (`ReservationRoomID`),
  ADD KEY `reservation` (`ReservationID`),
  ADD KEY `roomID` (`RoomID`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`RoomID`),
  ADD KEY `roomtypeID` (`RoomTypeID`);

--
-- Indexes for table `room_amenity`
--
ALTER TABLE `room_amenity`
  ADD KEY `roomtype` (`RoomTypeID`),
  ADD KEY `amenity` (`AmenityID`);

--
-- Indexes for table `room_type`
--
ALTER TABLE `room_type`
  ADD PRIMARY KEY (`RoomTypeID`);

--
-- Indexes for table `service_request`
--
ALTER TABLE `service_request`
  ADD PRIMARY KEY (`RequestID`),
  ADD KEY `reservationroomidd` (`ReservationRoomID`),
  ADD KEY `item` (`ItemID`),
  ADD KEY `staff` (`FulfilledByStaffID`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`StaffID`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`ReservationID`),
  ADD KEY `customerID` (`CustomerID`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `billing_invoice`
--
ALTER TABLE `billing_invoice`
  ADD CONSTRAINT `resrvationid` FOREIGN KEY (`ReservationID`) REFERENCES `transaction` (`ReservationID`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `reservationId` FOREIGN KEY (`ReservationID`) REFERENCES `transaction` (`ReservationID`);

--
-- Constraints for table `reservation_room`
--
ALTER TABLE `reservation_room`
  ADD CONSTRAINT `reservation` FOREIGN KEY (`ReservationID`) REFERENCES `transaction` (`ReservationID`),
  ADD CONSTRAINT `roomID` FOREIGN KEY (`RoomID`) REFERENCES `room` (`RoomID`);

--
-- Constraints for table `room`
--
ALTER TABLE `room`
  ADD CONSTRAINT `roomtypeID` FOREIGN KEY (`RoomTypeID`) REFERENCES `room_type` (`RoomTypeID`);

--
-- Constraints for table `room_amenity`
--
ALTER TABLE `room_amenity`
  ADD CONSTRAINT `amenity` FOREIGN KEY (`AmenityID`) REFERENCES `amenity` (`AmenityID`),
  ADD CONSTRAINT `roomtype` FOREIGN KEY (`RoomTypeID`) REFERENCES `room_type` (`RoomTypeID`);

--
-- Constraints for table `service_request`
--
ALTER TABLE `service_request`
  ADD CONSTRAINT `item` FOREIGN KEY (`ItemID`) REFERENCES `extra_items` (`ItemID`),
  ADD CONSTRAINT `reservationroomidd` FOREIGN KEY (`ReservationRoomID`) REFERENCES `reservation_room` (`ReservationRoomID`),
  ADD CONSTRAINT `staff` FOREIGN KEY (`FulfilledByStaffID`) REFERENCES `staff` (`StaffID`);

--
-- Constraints for table `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `customerID` FOREIGN KEY (`CustomerID`) REFERENCES `customer` (`CustomerID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
