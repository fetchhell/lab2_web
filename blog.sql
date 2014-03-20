-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 20, 2014 at 01:09 PM
-- Server version: 5.6.16
-- PHP Version: 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `blog`
--

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `message_id` int(11) NOT NULL,
  `message_box` varchar(1000) NOT NULL,
  `deleted` int(11) NOT NULL,
  `fk_theme_id` int(11) NOT NULL,
  `start_message` int(11) NOT NULL,
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `message_box`, `deleted`, `fk_theme_id`, `start_message`) VALUES
(0, 'Hello Theme 1 !', 0, 1, 1),
(1, 'Hello Theme 2 !', 0, 2, 1),
(2, 'Hello Theme 1 ! Comment 1 !', 0, 1, 0),
(3, 'Hello Theme 1 ! Comment 1.1 !', 0, 1, 0),
(4, '#2 Hello Theme 1 !', 0, 1, 1),
(5, 'Hello Theme 2 ! Comment 1 !', 0, 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `next_messages`
--

CREATE TABLE IF NOT EXISTS `next_messages` (
  `id` int(11) NOT NULL,
  `next_id` int(11) NOT NULL,
  `fk_message_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `next_messages`
--

INSERT INTO `next_messages` (`id`, `next_id`, `fk_message_id`) VALUES
(0, 2, 0),
(1, 3, 2),
(2, 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tag`
--

CREATE TABLE IF NOT EXISTS `tag` (
  `tag_id` int(11) NOT NULL,
  `tag_name` varchar(1000) NOT NULL,
  `fk_message_id` int(11) NOT NULL,
  `deleted` int(11) NOT NULL,
  PRIMARY KEY (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tag`
--

INSERT INTO `tag` (`tag_id`, `tag_name`, `fk_message_id`, `deleted`) VALUES
(1, 'hello', 0, 0),
(2, 'theme1', 0, 0),
(3, 'hello', 1, 0),
(4, 'theme2', 1, 0),
(5, 'hello', 2, 0),
(6, 'theme1', 2, 0),
(7, 'comment1', 2, 0),
(8, 'hello', 3, 0),
(9, 'theme1', 3, 0),
(10, 'comment1.1', 3, 0),
(11, 'hello', 4, 0),
(12, 'theme1', 4, 0),
(13, '2', 4, 0),
(14, 'hello', 5, 0),
(15, 'theme2', 5, 0),
(16, 'comment1', 5, 0);

-- --------------------------------------------------------

--
-- Table structure for table `themes`
--

CREATE TABLE IF NOT EXISTS `themes` (
  `theme_id` int(11) NOT NULL,
  `theme_name` varchar(1000) NOT NULL,
  PRIMARY KEY (`theme_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `themes`
--

INSERT INTO `themes` (`theme_id`, `theme_name`) VALUES
(1, 'Theme 1'),
(2, 'Theme 2');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
