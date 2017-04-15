-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 23, 2017 at 02:41 PM
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='stores users, passwords and sessions' AUTO_INCREMENT=3 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
