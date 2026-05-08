-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 08, 2026 at 02:08 PM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hospital_db`
--

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `sp_BookAppointment`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_BookAppointment` (IN `p_id` INT, IN `d_id` INT, IN `a_date` DATE)   BEGIN

    START TRANSACTION;

    INSERT INTO appointments
    (
        patient_id,
        doctor_id,
        appointment_date,
        status
    )
    VALUES
    (
        p_id,
        d_id,
        a_date,
        'Pending'
    );

    COMMIT;

END$$

DROP PROCEDURE IF EXISTS `sp_CompletePayment`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_CompletePayment` (IN `app_id` INT, IN `amt` DECIMAL(10,2))   BEGIN
    -- Requirement: Use of Transactions
    START TRANSACTION;
        INSERT INTO payments (appointment_id, amount) VALUES (app_id, amt);
        UPDATE appointments SET status = 'Pending' WHERE appointment_id = app_id;
    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `sp_UpdatePatientProfile`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_UpdatePatientProfile` (IN `p_user_id` INT, IN `p_new_username` VARCHAR(50), IN `p_new_password` VARCHAR(255))   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;
        UPDATE users 
        SET username = p_new_username, 
            password = p_new_password 
        WHERE user_id = p_user_id AND role = 'Patient';
    COMMIT;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
CREATE TABLE IF NOT EXISTS `appointments` (
  `appointment_id` int NOT NULL AUTO_INCREMENT,
  `patient_id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time DEFAULT NULL,
  `status` enum('Pending','Confirmed','Cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  PRIMARY KEY (`appointment_id`),
  KEY `patient_id` (`patient_id`),
  KEY `doctor_id` (`doctor_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `patient_id`, `doctor_id`, `appointment_date`, `appointment_time`, `status`) VALUES
(1, 4, 1, '2026-05-04', NULL, 'Confirmed'),
(2, 4, 2, '2026-05-05', '13:30:00', 'Confirmed'),
(3, 4, 3, '2026-05-06', NULL, 'Cancelled'),
(4, 5, 1, '2026-05-07', NULL, 'Confirmed'),
(5, 7, 1, '2026-05-07', NULL, 'Cancelled'),
(6, 7, 1, '2026-05-08', NULL, 'Confirmed'),
(7, 7, 3, '2026-05-13', '00:59:00', 'Confirmed'),
(8, 7, 3, '2026-05-08', '15:55:00', 'Confirmed'),
(9, 4, 1, '2026-05-29', NULL, 'Confirmed'),
(10, 4, 2, '2026-05-30', '13:30:00', 'Confirmed');

--
-- Triggers `appointments`
--
DROP TRIGGER IF EXISTS `tr_AfterAppointmentCancel`;
DELIMITER $$
CREATE TRIGGER `tr_AfterAppointmentCancel` AFTER UPDATE ON `appointments` FOR EACH ROW BEGIN
    IF NEW.status = 'Cancelled' THEN
        INSERT INTO audit_logs (action_type, appointment_id)
        VALUES ('CANCELLED', OLD.appointment_id);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `log_id` int NOT NULL AUTO_INCREMENT,
  `action_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `appointment_id` int DEFAULT NULL,
  `log_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`log_id`, `action_type`, `appointment_id`, `log_time`) VALUES
(1, 'SYSTEM_INIT', NULL, '2026-05-03 15:11:16'),
(2, 'CANCELLED', 3, '2026-05-03 15:11:16'),
(3, 'CANCELLED', 5, '2026-05-08 04:24:16');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

DROP TABLE IF EXISTS `doctors`;
CREATE TABLE IF NOT EXISTS `doctors` (
  `doctor_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `specialization` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`doctor_id`),
  KEY `specialization` (`specialization`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`doctor_id`, `name`, `specialization`, `contact`) VALUES
(1, 'Dr. Saman Kumara', 'Cardiology', '0771234567'),
(2, 'Dr. Nilmini Perera', 'Neurology', '0719876543'),
(3, 'Dr. Aruna Silva', 'Pediatrics', '0755556666'),
(4, 'Dr. Priyantha Bandara', 'Orthopedics', '0721112222');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `payment_id` int NOT NULL AUTO_INCREMENT,
  `appointment_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`payment_id`),
  KEY `appointment_id` (`appointment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `appointment_id`, `amount`, `payment_date`) VALUES
(1, 1, 2500.00, '2026-05-03 15:11:16'),
(2, 2, 1500.00, '2026-05-03 15:11:16'),
(3, 9, 500.00, '2026-05-08 13:55:18'),
(4, 10, 500.00, '2026-05-08 13:57:02');

--
-- Triggers `payments`
--
DROP TRIGGER IF EXISTS `tr_AfterPaymentLog`;
DELIMITER $$
CREATE TRIGGER `tr_AfterPaymentLog` AFTER INSERT ON `payments` FOR EACH ROW BEGIN
    -- Automatically log the financial transaction for auditing
    INSERT INTO audit_logs (action_type, appointment_id, log_time)
    VALUES ('PAYMENT_SUCCESSFUL', NEW.appointment_id, NOW());
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('Admin','Patient','Doctor') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`) VALUES
(1, 'user01', '123', 'Admin'),
(7, 'user1', '$2y$10$6UEGOOqj2./N07ArFd8GUeu1MB0SAkB/nQMIpy7QtqZXlkNGkzlfe', 'Patient'),
(3, 'bp9999', '$2y$10$S31W7lGEFn6XNxervmQbSuEmR7/.9eNy.OPTbB5SQn2hWdXsEkMhK', 'Admin'),
(4, 'user', '$2y$10$p4TIpY943SjDkXKuhtYXMe/9x.l2x19EIS0IKozKyQi1Ik.PhlyWm', 'Patient'),
(5, 'shehan', '$2y$10$YODs91fPboY416Bg028bIuvDd0lrnODGgX9pbTFbk4KBX5JxZDVda', 'Patient'),
(6, 'admin', '$2y$10$10vXw9agGVWkUjT5EXL1v.oPDwHi3vk3Zz2G.aSkmpB.c1mDFIQUe', 'Admin');

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_appointmentsummary`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `vw_appointmentsummary`;
CREATE TABLE IF NOT EXISTS `vw_appointmentsummary` (
`appointment_id` int
,`patient_name` varchar(50)
,`doctor_name` varchar(100)
,`appointment_date` date
,`appointment_time` time
,`status` enum('Pending','Confirmed','Cancelled')
,`amount` decimal(10,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_revenuebydoctor`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `vw_revenuebydoctor`;
CREATE TABLE IF NOT EXISTS `vw_revenuebydoctor` (
`doctor_name` varchar(100)
,`specialization` varchar(100)
,`total_appointments` bigint
,`total_revenue` decimal(32,2)
);

-- --------------------------------------------------------

--
-- Structure for view `vw_appointmentsummary`
--
DROP TABLE IF EXISTS `vw_appointmentsummary`;

DROP VIEW IF EXISTS `vw_appointmentsummary`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_appointmentsummary`  AS SELECT `a`.`appointment_id` AS `appointment_id`, `u`.`username` AS `patient_name`, `d`.`name` AS `doctor_name`, `a`.`appointment_date` AS `appointment_date`, `a`.`appointment_time` AS `appointment_time`, `a`.`status` AS `status`, `p`.`amount` AS `amount` FROM (((`appointments` `a` join `users` `u` on((`a`.`patient_id` = `u`.`user_id`))) join `doctors` `d` on((`a`.`doctor_id` = `d`.`doctor_id`))) left join `payments` `p` on((`a`.`appointment_id` = `p`.`appointment_id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `vw_revenuebydoctor`
--
DROP TABLE IF EXISTS `vw_revenuebydoctor`;

DROP VIEW IF EXISTS `vw_revenuebydoctor`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_revenuebydoctor`  AS SELECT `d`.`name` AS `doctor_name`, `d`.`specialization` AS `specialization`, count(`a`.`appointment_id`) AS `total_appointments`, sum(`p`.`amount`) AS `total_revenue` FROM ((`doctors` `d` join `appointments` `a` on((`d`.`doctor_id` = `a`.`doctor_id`))) join `payments` `p` on((`a`.`appointment_id` = `p`.`appointment_id`))) GROUP BY `d`.`doctor_id`, `d`.`name`, `d`.`specialization` ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
