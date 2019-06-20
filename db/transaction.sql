-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 20, 2019 at 07:58 PM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 7.1.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `transaction`
--

-- --------------------------------------------------------

--
-- Table structure for table `t_fee_transactions`
--

CREATE TABLE `t_fee_transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_transaction_id` int(11) NOT NULL,
  `fee` double(11,2) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_fee_transactions`
--

INSERT INTO `t_fee_transactions` (`id`, `user_id`, `user_transaction_id`, `fee`, `created`, `modified`) VALUES
(3, 6, 23, 0.55, '2019-06-01 19:00:00', '2019-06-02 14:44:52'),
(4, 6, 23, 0.55, '2019-06-01 19:00:00', '2019-06-02 14:48:25');

-- --------------------------------------------------------

--
-- Table structure for table `t_timestamp`
--

CREATE TABLE `t_timestamp` (
  `id` int(11) NOT NULL,
  `timestamp` int(20) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `t_users`
--

CREATE TABLE `t_users` (
  `id` int(11) NOT NULL,
  `account_no` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `iban` varchar(255) NOT NULL,
  `proof_of_id` varchar(255) NOT NULL DEFAULT '0',
  `proof_of_address` varchar(255) NOT NULL DEFAULT '0',
  `balance` double(50,2) NOT NULL,
  `day_amount` double(50,2) NOT NULL,
  `user_id` int(11) NOT NULL,
  `currency` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `role` varchar(100) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_users`
--

INSERT INTO `t_users` (`id`, `account_no`, `first_name`, `last_name`, `email`, `password`, `iban`, `proof_of_id`, `proof_of_address`, `balance`, `day_amount`, `user_id`, `currency`, `status`, `role`, `created`, `modified`) VALUES
(1, 995157, 'admin', 'admin', 'admin@gmail.com', 'admin', '', '', '', 29.55, 0.00, 0, 'usd', 1, 'admin', '2019-06-09 07:59:48', '0000-00-00 00:00:00'),
(6, 995158, 'haq', 'nawaz', 'haqnawazwgbm2@gmail.com', 'admin2', '', '0', '0', 1.10, 0.06, 0, '', 1, 'user', '2019-06-09 07:59:54', '2019-06-01 16:40:40'),
(10, 995159, 'first name', 'last name', 'test@gmail.com', 'admin', '34234ewr23', '', '', 324234.00, 0.00, 1, 'usd', 1, 'agent', '2019-06-09 07:59:58', '2019-06-09 03:59:47');

-- --------------------------------------------------------

--
-- Table structure for table `t_user_activations`
--

CREATE TABLE `t_user_activations` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_user_activations`
--

INSERT INTO `t_user_activations` (`email`, `token`, `created`, `modified`) VALUES
('haqnawazwgbm@gmail.com', '3e3ff598a292162ac306b70bbfe1769b', '2019-06-07 22:05:40', '2019-06-07 22:05:40'),
('haqnawazwgbm@gmail.com', '3e3ff598a292162ac306b70bbfe1769b', '2019-06-09 08:38:06', '2019-06-09 08:38:06'),
('haqnawazwgbm@gmail.com', '3e3ff598a292162ac306b70bbfe1769b', '2019-06-09 08:54:56', '2019-06-09 08:54:56'),
('haqnawazwgbm@gmail.com', '3e3ff598a292162ac306b70bbfe1769b', '2019-06-09 15:58:01', '2019-06-09 15:58:01'),
('haqnawazwgbm@gmail.com', '3e3ff598a292162ac306b70bbfe1769b', '2019-06-09 15:59:47', '2019-06-09 15:59:47'),
('haqnawazwgbm@gmail.com', '3e3ff598a292162ac306b70bbfe1769b', '2019-06-09 16:02:53', '2019-06-09 16:02:53'),
('haqnawazwgbm@gmail.com', '3e3ff598a292162ac306b70bbfe1769b', '2019-06-09 16:08:22', '2019-06-09 16:08:22'),
('haqnawazwgbm@gmail.com', '3e3ff598a292162ac306b70bbfe1769b', '2019-06-09 16:10:09', '2019-06-09 16:10:09'),
('haqnawazwgbm@gmail.com', '3e3ff598a292162ac306b70bbfe1769b', '2019-06-09 16:13:21', '2019-06-09 16:13:21');

-- --------------------------------------------------------

--
-- Table structure for table `t_user_transactions`
--

CREATE TABLE `t_user_transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaction_no` varchar(100) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `phone_no` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `amount` double(50,2) NOT NULL,
  `secret_question` varchar(255) NOT NULL,
  `cashout` tinyint(1) NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_user_transactions`
--

INSERT INTO `t_user_transactions` (`id`, `user_id`, `transaction_no`, `first_name`, `last_name`, `phone_no`, `email`, `amount`, `secret_question`, `cashout`, `created`, `modified`) VALUES
(4, 1, '10257535049541005248', 'haq', 'nawaz', '03085356405', 'haqnawazwgbm@gmail.com', 10.45, 'question', 0, '2019-06-02 13:44:53', '2019-06-02 18:44:53'),
(5, 1, '50985353559999551025', 'haq', 'nawaz', '03085356405', 'haqnawazwgbm@gmail.com', 10.45, 'question', 0, '2019-06-02 13:48:12', '2019-06-02 18:48:12'),
(6, 1, '10199501025555984998', 'haq', 'nawaz', '03085356405', 'haqnawazwgbm@gmail.com', 10.45, 'question', 0, '2019-06-02 13:50:24', '2019-06-02 18:50:24'),
(7, 1, '97534998991011025356', 'haq', 'nawaz', '03085356405', 'haqnawazwgbm@gmail.com', 10.45, 'question', 0, '2019-06-02 13:54:26', '2019-06-02 18:54:26'),
(8, 1, '10110010098485251525', 'haq', 'nawaz', '03085356405', 'haqnawazwgbm@gmail.com', 10.45, 'question', 0, '2019-06-02 13:59:27', '2019-06-02 18:59:27'),
(9, 6, '55971025254555356565', 'haq', 'nawaz', '03085356405', 'haqnawazwgbm@gmail.com', 10.45, 'question', 0, '2019-06-09 09:18:00', '2019-06-02 19:01:35'),
(10, 6, '54102539952515156101', 'haq', 'nawaz', '03085356405', 'haqnawazwgbm@gmail.com', 10.45, 'question', 0, '2019-06-09 09:18:04', '2019-06-02 19:04:52'),
(11, 1, '97102495355535210048', 'haq', 'nawaz', '03085356405', 'haqnawazwgbm@gmail.com', 10.45, 'question', 0, '2019-06-02 14:10:20', '2019-06-02 19:10:20'),
(12, 1, '97979748975310249995', 'haq', 'nawaz', '03085356405', 'haqnawazwgbm@gmail.com', 10.45, 'question', 0, '2019-06-02 14:11:28', '2019-06-02 19:11:28'),
(13, 1, '53481014999102495749', 'haq', 'nawaz', '03085356405', 'haqnawazwgbm@gmail.com', 10.45, 'question', 0, '2019-06-02 14:13:00', '2019-06-02 19:13:00'),
(14, 1, '51995157511019710249', 'haq', 'nawaz', '03085356405', 'haqnawazwgbm@gmail.com', 10.45, 'question', 0, '2019-06-02 14:14:06', '2019-06-02 19:14:06'),
(15, 1, '10150555099559949545', 'haq', 'nawaz', '03085356405', 'haqnawazwgbm@gmail.com', 10.45, 'question', 0, '2019-06-02 14:15:47', '2019-06-02 19:15:47'),
(16, 1, '48101505098100495449', 'haq', 'nawaz', '03085356405', 'haqnawazwgbm@gmail.com', 10.45, 'question', 0, '2019-06-02 14:19:45', '2019-06-02 19:19:45'),
(17, 1, '97561005710197545210', 'haq', 'nawaz', '03085356405', 'haqnawazwgbm@gmail.com', 10.45, 'question', 0, '2019-06-02 14:21:20', '2019-06-02 19:21:20'),
(18, 1, '99985551559810010148', 'haq', 'nawaz', '03085356405', 'haqnawazwgbm@gmail.com', 10.45, 'question', 0, '2019-06-02 14:25:02', '2019-06-02 19:25:02'),
(19, 1, '54101575148971015110', 'haq', 'nawaz', '03085356405', 'haqnawazwgbm@gmail.com', 10.45, 'question', 0, '2019-06-02 14:25:20', '2019-06-02 19:25:20'),
(20, 1, '48100535410110155499', 'haq', 'nawaz', '03085356405', 'haqnawazwgbm@gmail.com', 10.45, 'question', 0, '2019-06-02 14:26:03', '2019-06-02 19:26:03'),
(21, 1, '57524957491019899565', 'haq', 'nawaz', '03085356405', 'haqnawazwgbm@gmail.com', 10.45, 'question', 0, '2019-06-02 14:32:21', '2019-06-02 19:32:21'),
(22, 1, '48535451100505797499', 'haq', 'nawaz', '03085356405', 'haqnawazwgbm@gmail.com', 10.45, 'question', 0, '2019-06-02 14:34:23', '2019-06-02 19:34:23'),
(23, 1, '50481005548515199544', 'haq', 'nawaz', '03085356405', 'haqnawazwgbm@gmail.com', 10.45, 'question', 1, '2019-06-02 17:48:26', '2019-06-02 19:34:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `t_fee_transactions`
--
ALTER TABLE `t_fee_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_timestamp`
--
ALTER TABLE `t_timestamp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_users`
--
ALTER TABLE `t_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_user_transactions`
--
ALTER TABLE `t_user_transactions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `t_fee_transactions`
--
ALTER TABLE `t_fee_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `t_timestamp`
--
ALTER TABLE `t_timestamp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `t_users`
--
ALTER TABLE `t_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `t_user_transactions`
--
ALTER TABLE `t_user_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
