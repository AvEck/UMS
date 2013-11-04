-- phpMyAdmin SQL Dump
-- version 4.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 04, 2013 at 07:41 PM
-- Server version: 5.5.25
-- PHP Version: 5.4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `jcgroep`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT       NULL AUTO_INCREMENT,
  `user` varchar(255) NOT NULL,
  `role` tinyint(1) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `pass_hash` varchar(64) NOT NULL,
  `needs_forgot` tinyint(1) NOT NULL,
  `forgot_timestamp` datetime NOT NULL,
  `forgot_hash` varchar(64) NOT NULL,
  `is_locked` tinyint(1) NOT NULL,
  `admin_note` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user`, `role`, `first_name`, `last_name`, `email`, `pass_hash`, `needs_forgot`, `forgot_timestamp`, `forgot_hash`, `is_locked`, `admin_note`) VALUES
(1, 'Alexander', 1, 'Alexander', 'van Eck', 'a.vaneck@jcgroep.nl', '$2a$10$ko1T2iwS8R0rnBwS7P3ky.HYm2lU3s4zQgLcGyH/jt8fZopSX0jcW', 0, '0000-00-00 00:00:00', '', 0, '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
