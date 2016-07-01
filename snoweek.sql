-- phpMyAdmin SQL Dump
-- version 3.3.8.1
-- http://www.phpmyadmin.net
--
-- Host: w.rdc.sae.sina.com.cn:3307
-- Generation Time: Jul 01, 2016 at 02:58 PM
-- Server version: 5.6.23
-- PHP Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `app_snoweek`
--

-- --------------------------------------------------------

--
-- Table structure for table `grade_list`
--

CREATE TABLE IF NOT EXISTS `grade_list` (
  `grade_id` mediumint(9) NOT NULL,
  `course` text NOT NULL,
  `grade` int(11) NOT NULL,
  `student_id` varchar(12) NOT NULL,
  PRIMARY KEY (`grade_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `grade_list`
--

INSERT INTO `grade_list` (`grade_id`, `course`, `grade`, `student_id`) VALUES
(0, '语文', 34, '201334040086'),
(1, '语文', 56, '201334040086');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `open_id` varchar(30) NOT NULL,
  `student_id` varchar(12) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `open_id`, `student_id`) VALUES
(2, 'oCBW_wtLrNLhOFYTdGSrIhzfoMuk', '201334040086');
