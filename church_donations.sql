-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 17, 2025 at 03:20 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `church_donations`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','editor') NOT NULL DEFAULT 'editor',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `email`, `full_name`, `role`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'faithfull', 'admin@faithbaptistchurch.com', 'Administrator', 'admin', NULL, '2025-03-16 04:58:09', '2025-03-16 09:29:43');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('sermon','gallery') NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `type`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Sunday Morning', 'sermon', 'Sunday morning worship services', '2025-03-16 04:58:10', '2025-03-16 04:58:10'),
(2, 'Sunday Evening', 'sermon', 'Sunday evening worship services', '2025-03-16 04:58:10', '2025-03-16 04:58:10'),
(3, 'Wednesday Bible Study', 'sermon', 'Midweek Bible studies', '2025-03-16 04:58:10', '2025-03-16 04:58:10'),
(4, 'Special Events', 'sermon', 'Special events and guest speakers', '2025-03-16 04:58:10', '2025-03-16 04:58:10'),
(5, 'Youth Services', 'sermon', 'Youth-focused messages', '2025-03-16 04:58:10', '2025-03-16 04:58:10'),
(6, 'Worship Services', 'gallery', 'Photos from our worship services', '2025-03-16 04:58:10', '2025-03-16 04:58:10'),
(7, 'Baptisms', 'gallery', 'Baptism ceremonies', '2025-03-16 04:58:10', '2025-03-16 04:58:10'),
(8, 'Community Outreach', 'gallery', 'Community service and outreach events', '2025-03-16 04:58:10', '2025-03-16 04:58:10'),
(9, 'Youth Activities', 'gallery', 'Youth group activities and events', '2025-03-16 04:58:10', '2025-03-16 04:58:10'),
(10, 'Church Events', 'gallery', 'Special church events and celebrations', '2025-03-16 04:58:10', '2025-03-16 04:58:10');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `donation_type` enum('one-time','recurring') NOT NULL,
  `purpose` varchar(100) NOT NULL DEFAULT 'General Fund',
  `frequency` enum('one-time','weekly','biweekly','monthly','quarterly','annually') NOT NULL DEFAULT 'one-time',
  `cover_fees` tinyint(1) NOT NULL DEFAULT 0,
  `reference` varchar(100) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `status` enum('pending','completed','failed') NOT NULL DEFAULT 'pending',
  `payment_date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donation_logs`
--

CREATE TABLE `donation_logs` (
  `id` int(11) UNSIGNED NOT NULL,
  `donation_id` int(11) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `status` enum('success','failed') NOT NULL,
  `payment_date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `event_date` date NOT NULL,
  `event_time` time NOT NULL,
  `location` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `event_date`, `event_time`, `location`, `image`, `is_featured`, `created_at`, `updated_at`) VALUES
(1, 'Community Outreach Day', 'Join us as we serve our local community through various projects and initiatives.', '2025-03-15', '09:00:00', 'Multiple Locations', '67d73a530d951.webp', 1, '2025-03-16 20:53:39', '2025-03-16 20:53:39'),
(2, 'Easter Sunday Service', 'Celebrate the resurrection of Jesus Christ with special music and message.', '2025-04-09', '08:00:00', 'Main Sanctuary', '67d73c979d814.webp', 1, '2025-03-16 21:03:19', '2025-03-16 21:03:19'),
(3, 'Godly Couples Retreat', 'A weekend getaway for couples to connect, relax, and grow together in faith.', '2025-04-23', '19:00:00', 'Church Campus', '67d73d27d135b.webp', 1, '2025-03-16 21:05:43', '2025-03-16 21:05:43');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recurring_donations`
--

CREATE TABLE `recurring_donations` (
  `id` int(11) UNSIGNED NOT NULL,
  `donation_id` int(11) UNSIGNED NOT NULL,
  `next_payment_date` date NOT NULL,
  `status` enum('active','paused','cancelled') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sermons`
--

CREATE TABLE `sermons` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `preacher` varchar(255) NOT NULL,
  `sermon_date` date NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `pdf_file` varchar(255) DEFAULT NULL,
  `audio_file` varchar(255) DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sermons`
--

INSERT INTO `sermons` (`id`, `title`, `description`, `preacher`, `sermon_date`, `image`, `pdf_file`, `audio_file`, `category`, `is_featured`, `created_at`, `updated_at`) VALUES
(1, 'Are you ready when the Lord shall come?', 'Pastor Solomon Ackon explores how how believers can live a righteous life to prepare for the second coming of Jesus Christ.', 'Pastor Solomon Ackon', '2025-03-25', '67d72be59a7ca.webp', '67d72be5ac5f3.pdf', '67d72be5d54fe.mp3', 'The coming', 1, '2025-03-16 19:32:26', '2025-03-16 19:52:06');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) UNSIGNED NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `setting_description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_description`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'Faith Baptist Church', 'Name of the church website', '2025-03-16 04:58:09', '2025-03-16 04:58:09'),
(2, 'site_email', 'info@faithbaptistchurch.com', 'Primary contact email', '2025-03-16 04:58:09', '2025-03-16 04:58:09'),
(3, 'site_phone', '+233 543 957 330', 'Primary contact phone number', '2025-03-16 04:58:09', '2025-03-16 04:58:09'),
(4, 'site_address', 'Ayensudo-Abeyee, Central Region, Ghana, West Africa', 'Church address', '2025-03-16 04:58:09', '2025-03-16 04:58:09'),
(5, 'paystack_public_key', 'your_paystack_public_key', 'Paystack public key for payment processing', '2025-03-16 04:58:09', '2025-03-16 04:58:09'),
(6, 'paystack_secret_key', 'your_paystack_secret_key', 'Paystack secret key for payment processing', '2025-03-16 04:58:09', '2025-03-16 04:58:09'),
(7, 'enable_donations', '1', 'Enable or disable donation functionality', '2025-03-16 04:58:09', '2025-03-16 04:58:09'),
(8, 'enable_recurring_donations', '1', 'Enable or disable recurring donations', '2025-03-16 04:58:09', '2025-03-16 04:58:09');

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

CREATE TABLE `subscribers` (
  `id` int(11) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `status` enum('active','unsubscribed') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_donations_email` (`email`),
  ADD KEY `idx_donations_status` (`status`),
  ADD KEY `idx_donations_date` (`payment_date`);

--
-- Indexes for table `donation_logs`
--
ALTER TABLE `donation_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donation_id` (`donation_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_events_date` (`event_date`),
  ADD KEY `idx_events_featured` (`is_featured`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_gallery_category` (`category`),
  ADD KEY `idx_gallery_featured` (`is_featured`);

--
-- Indexes for table `recurring_donations`
--
ALTER TABLE `recurring_donations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donation_id` (`donation_id`);

--
-- Indexes for table `sermons`
--
ALTER TABLE `sermons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sermons_date` (`sermon_date`),
  ADD KEY `idx_sermons_category` (`category`),
  ADD KEY `idx_sermons_featured` (`is_featured`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `subscribers`
--
ALTER TABLE `subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donation_logs`
--
ALTER TABLE `donation_logs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recurring_donations`
--
ALTER TABLE `recurring_donations`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sermons`
--
ALTER TABLE `sermons`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `subscribers`
--
ALTER TABLE `subscribers`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `donation_logs`
--
ALTER TABLE `donation_logs`
  ADD CONSTRAINT `donation_logs_ibfk_1` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recurring_donations`
--
ALTER TABLE `recurring_donations`
  ADD CONSTRAINT `recurring_donations_ibfk_1` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
