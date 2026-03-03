-- Boost Stride Framework - Database Export
-- Generated for Hobart Auto Shop
-- Compatible with MySQL/MariaDB (XAMPP Standard)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `boost_stride_db`
--
CREATE DATABASE IF NOT EXISTS `boost_stride_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `boost_stride_db`;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `settings`
--
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('hero_data', '{"subtitle":"Welcome To Boost Stride","title":"Best Automotive & Maintenance Services","description":"Vero elitr justo clita lorem. Ipsum dolor at sed stet sit diam no. Kasd rebum ipsum et diam justo clita et kasd rebum sea elitr."}'),
('contact_info', '{"address":"123 Street, New York, USA","phone":"+012 345 6789","email":"info@example.com"}'),
('seo_data', '{"title":"Boost Stride - Professional Auto Shop","keywords":"car repair, auto shop, maintenance","description":"Professional automotive services you can trust."}');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--
CREATE TABLE IF NOT EXISTS `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `services`
--
INSERT INTO `services` (`title`, `description`, `image`, `sort_order`) VALUES
('General Auto Repair', 'Stet stet justo dolor sed duo. Ut clita sea sit ipsum diam lorem diam.', 'img/service-1.jpg', 1),
('Brake Services', 'Precision braking system maintenance and repair for your safety.', 'img/service-2.jpg', 2),
('Tire Replacement', 'High-quality tire brands and professional installation services.', 'img/service-3.jpg', 3);

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--
CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `profession` varchar(100) DEFAULT NULL,
  `text` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `testimonials`
--
INSERT INTO `testimonials` (`name`, `profession`, `text`, `image`) VALUES
('James Wilson', 'Car Owner', 'Boost Stride provided exceptional service for my vehicle. Highly recommended!', 'img/testimonial-1.jpg'),
('Sarah Jenkins', 'Fleet Manager', 'Reliable and professional. They keep our business vehicles on the road.', 'img/testimonial-2.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `ip_address` varchar(45) NOT NULL,
  `attempts` int(11) DEFAULT 0,
  `last_attempt` int(11) DEFAULT NULL,
  PRIMARY KEY (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
