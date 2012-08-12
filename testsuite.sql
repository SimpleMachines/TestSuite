-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 15, 2011 at 01:08 AM
-- Server version: 5.1.41
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `smf2`
--

-- --------------------------------------------------------

--
-- Table structure for table `smf_testsuite_cases`
--

CREATE TABLE IF NOT EXISTS `smf_testsuite_cases` (
  `id_case` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_suite` int(10) unsigned NOT NULL DEFAULT '0',
  `case_name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `steps` text NOT NULL,
  `expected_result` text NOT NULL,
  `poster_name` varchar(255) NOT NULL DEFAULT '',
  `poster_email` varchar(255) NOT NULL DEFAULT '',
  `id_member` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `poster_time` int(10) unsigned NOT NULL DEFAULT '0',
  `id_assigned` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_by` varchar(255) NOT NULL DEFAULT '',
  `count` int(10) unsigned NOT NULL DEFAULT '0',
  `fail_count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_case`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `smf_testsuite_cases`
--


-- --------------------------------------------------------

--
-- Table structure for table `smf_testsuite_projects`
--

CREATE TABLE IF NOT EXISTS `smf_testsuite_projects` (
  `id_project` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `poster_name` varchar(255) NOT NULL DEFAULT '',
  `poster_email` varchar(255) NOT NULL DEFAULT '',
  `id_member` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `poster_time` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_by` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_project`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `smf_testsuite_projects`
--


-- --------------------------------------------------------

--
-- Table structure for table `smf_testsuite_runs`
--

CREATE TABLE IF NOT EXISTS `smf_testsuite_runs` (
  `id_run` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_case` int(10) unsigned NOT NULL DEFAULT '0',
  `result_achieved` varchar(255) NOT NULL DEFAULT '',
  `feedback` text NOT NULL,
  `poster_name` varchar(255) NOT NULL DEFAULT '',
  `poster_email` varchar(255) NOT NULL DEFAULT '',
  `id_member` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `poster_time` int(10) unsigned NOT NULL DEFAULT '0',
  `id_bug` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_by` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_run`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `smf_testsuite_runs`
--


-- --------------------------------------------------------

--
-- Table structure for table `smf_testsuite_suites`
--

CREATE TABLE IF NOT EXISTS `smf_testsuite_suites` (
  `id_suite` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_project` int(10) unsigned NOT NULL DEFAULT '0',
  `suite_name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `poster_name` varchar(255) NOT NULL DEFAULT '',
  `poster_email` varchar(255) NOT NULL DEFAULT '',
  `id_member` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `poster_time` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_by` varchar(255) NOT NULL DEFAULT '',
  `count` int(10) unsigned NOT NULL DEFAULT '0',
  `fail_count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_suite`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `smf_testsuite_suites`
--


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
