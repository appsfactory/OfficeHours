-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 18, 2012 at 07:10 AM
-- Server version: 5.1.53
-- PHP Version: 5.3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `scheduling`
--

-- --------------------------------------------------------

CREATE DATABASE `scheduling` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `scheduling`;

--
-- Table structure for table `boardlocations`
--

CREATE TABLE IF NOT EXISTS `boardlocations` (
  `locationCode` varchar(10) NOT NULL,
  `locationDetails` varchar(50) NOT NULL,
  `locationBranchId` int(10) NOT NULL,
  `locationOrganizationId` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `boardlocations`
--


-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE IF NOT EXISTS `branches` (
  `branchId` int(10) NOT NULL AUTO_INCREMENT,
  `branchName` varchar(20) NOT NULL,
  `branchAddress` varchar(50) NOT NULL,
  `branchPhone` varchar(15) NOT NULL,
  `organizationId` int(10) NOT NULL,
  PRIMARY KEY (`branchId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `branches`
--


-- --------------------------------------------------------

--
-- Table structure for table `organizations`
--

CREATE TABLE IF NOT EXISTS `organizations` (
  `organizationId` int(11) NOT NULL AUTO_INCREMENT,
  `organizationName` varchar(20) NOT NULL,
  `organizationAddress` varchar(50) NOT NULL,
  `organizationPhone` varchar(15) NOT NULL,
  `accessCode` varchar(50) NOT NULL,
  `fontHeadingColor` char(7) NOT NULL,
  `backgroundHeadingColor` char(7) NOT NULL,
  `fontGridOddColor` char(7) NOT NULL,
  `backgroundGridOddColor` char(7) NOT NULL,
  `fontGridEvenColor` char(7) NOT NULL,
  `backgroundGridEvenColor` char(7) NOT NULL,
  `fontBottomColor` char(7) NOT NULL,
  `backgroundBottomColor` char(7) NOT NULL,
  PRIMARY KEY (`organizationId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `organizations`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `userName` varchar(20) NOT NULL,
  `firstName` varchar(20) NOT NULL,
  `lastName` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `isAdministrator` char(1) NOT NULL,
  `organizationId` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--


-- --------------------------------------------------------

--
-- Table structure for table `userschedules`
--

CREATE TABLE IF NOT EXISTS `userschedules` (
  `userName` varchar(20) NOT NULL,
  `organizationId` int(10) NOT NULL,
  `branchId` int(10) NOT NULL,
  `locationCode` varchar(10) NOT NULL,
  `date` date NOT NULL,
  `startingTime` time NOT NULL,
  `signedIn` time NOT NULL,
  `finishingTime` time NOT NULL,
  `signedOut` time NOT NULL,
  `status` char(1) NOT NULL,
  `referenceId` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`referenceId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

create index ix_userschedules_date on  userschedules (date);--
-- Dumping data for table `userschedules`
--

