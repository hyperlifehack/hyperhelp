-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 13, 2017 at 10:20 PM
-- Server version: 5.5.31
-- PHP Version: 5.4.4-14+deb7u3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `db_hyperhelp`
--

-- --------------------------------------------------------

--
-- Table structure for table `translations`
--

CREATE TABLE IF NOT EXISTS `translations` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `keyword` text NOT NULL,
  `en` text NOT NULL,
  `de` text NOT NULL,
  `ru` text NOT NULL,
  `es` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `translations`
--

INSERT INTO `translations` (`id`, `keyword`, `en`, `de`, `ru`, `es`) VALUES
(1, 'hello', 'hello', 'hallo', 'nastrovie', '!ola'),
(2, 'password forgotten?', 'password forgotten?', 'Passwort vergessen?', 'забыли пароль?', '¿Olvidó su contraseña?'),
(3, 'Please mail me a new password.', 'Please mail me a new password.', 'Bitte neues Passwort zusenden.', '', ''),
(4, 'New password for', 'New password for', 'Neues Passwort für', '', ''),
(5, 'Your new password for', 'Your new password for', 'Ihr neues Passwort für', '', ''),
(6, 'Password', 'Password', 'Passwort', '', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
