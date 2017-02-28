-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 07, 2017 at 10:40 PM
-- Server version: 5.5.31
-- PHP Version: 5.4.4-14+deb7u3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `admin_hyperhelp`
--
CREATE DATABASE `admin_hyperhelp` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `admin_hyperhelp`;

-- --------------------------------------------------------

--
-- Table structure for table `action_templates`
--

CREATE TABLE IF NOT EXISTS `action_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `users` text NOT NULL,
  `category` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `actions`
--

CREATE TABLE IF NOT EXISTS `actions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `RandomID` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `username_id` bigint(20) NOT NULL,
  `howmany_minutes` bigint(20) NOT NULL,
  `to_whom` varchar(255) NOT NULL,
  `to_whom_id` bigint(20) NOT NULL,
  `what` text NOT NULL,
  `what_id` bigint(20) NOT NULL,
  `when` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `groupname` text NOT NULL,
  `system` tinyint(1) NOT NULL COMMENT 'if this is a default system group, that should never be deleted',
  `mail` text NOT NULL,
  `profilepicture` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `groupname`, `system`, `mail`, `profilepicture`) VALUES
(1, 'test', 0, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `passwd`
--

CREATE TABLE IF NOT EXISTS `passwd` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `RandomID` varchar(255) NOT NULL,
  `username` text NOT NULL,
  `mail` text NOT NULL,
  `groups` text NOT NULL COMMENT 'list of groups the user belongs to',
  `password` text NOT NULL,
  `session` varchar(255) NOT NULL COMMENT 'random session id',
  `logintime` varchar(255) NOT NULL COMMENT 'server-timestamp when user logged in',
  `loginexpires` varchar(255) NOT NULL COMMENT 'server-timestamp when session expires',
  `activation` varchar(255) NOT NULL COMMENT 'activation id',
  `data` text NOT NULL COMMENT 'additional data about the user',
  `status` varchar(255) NOT NULL COMMENT 'the state of the user active, disabled, deleted',
  `profilepicture` text NOT NULL,
  `LoginCount` bigint(20) NOT NULL,
  `country` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='stores users, passwords and sessions' AUTO_INCREMENT=1 ;

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
