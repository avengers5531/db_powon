-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 24, 2016 at 02:45 AM
-- Server version: 5.5.49-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `powon`
--

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE IF NOT EXISTS `event` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(64) DEFAULT NULL,
  `description` text,
  `powon_group_id` int(11) NOT NULL,
  PRIMARY KEY (`event_id`),
  KEY `powon_group_id` (`powon_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `event_details`
--

CREATE TABLE IF NOT EXISTS `event_details` (
  `event_id` int(11) NOT NULL,
  `event_date` date NOT NULL,
  `event_time` time NOT NULL,
  `location` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`event_id`,`event_date`,`event_time`,`location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `gift_exchange`
--

CREATE TABLE IF NOT EXISTS `gift_exchange` (
  `from_member` int(11) NOT NULL,
  `to_member` int(11) NOT NULL,
  `gift_exchange_date` date NOT NULL,
  PRIMARY KEY (`from_member`,`to_member`,`gift_exchange_date`),
  KEY `to_member` (`to_member`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `group_page`
--

CREATE TABLE IF NOT EXISTS `group_page` (
  `page_id` int(11) NOT NULL,
  `page_description` text,
  `access_type` char(1) NOT NULL DEFAULT 'P',
  `page_owner` int(11) DEFAULT NULL,
  `page_group` int(11) NOT NULL,
  PRIMARY KEY (`page_id`),
  KEY `page_owner` (`page_owner`),
  KEY `page_group` (`page_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `has_interests`
--

CREATE TABLE IF NOT EXISTS `has_interests` (
  `interest_name` varchar(255) NOT NULL,
  `member_id` int(11) NOT NULL,
  PRIMARY KEY (`interest_name`,`member_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `has_interests`
--

INSERT INTO `has_interests` (`interest_name`, `member_id`) VALUES
('Fishing', 2),
('Aliens', 3),
('Fantasy Books', 4),
('Soccer', 4),
('Aliens', 6),
('Basketball', 6),
('Fantasy Books', 6);

-- --------------------------------------------------------

--
-- Table structure for table `interests`
--

CREATE TABLE IF NOT EXISTS `interests` (
  `interest_name` varchar(255) NOT NULL,
  PRIMARY KEY (`interest_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `interests`
--

INSERT INTO `interests` (`interest_name`) VALUES
('Aliens'),
('Basketball'),
('Fantasy Books'),
('Fishing'),
('Soccer');

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE IF NOT EXISTS `invoice` (
  `invoice_id` int(11) NOT NULL AUTO_INCREMENT,
  `amount_due` decimal(5,2) NOT NULL,
  `payment_deadline` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_paid` timestamp NULL DEFAULT NULL,
  `billing_period_start` timestamp NOT NULL DEFAULT '1970-01-01 00:00:01',
  `billing_period_end` timestamp NOT NULL DEFAULT '1970-01-01 00:00:01',
  `account_holder` int(11) NOT NULL,
  PRIMARY KEY (`invoice_id`),
  KEY `account_holder` (`account_holder`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`invoice_id`, `amount_due`, `payment_deadline`, `date_paid`, `billing_period_start`, `billing_period_end`, `account_holder`) VALUES
(1, 32.00, '2016-07-12 00:00:00', NULL, '2016-07-17 17:33:19', '2017-06-12 00:00:00', 4),
(2, 32.00, '2016-07-12 00:00:00', NULL, '2016-07-17 17:33:19', '2017-06-12 00:00:00', 2),
(3, 32.00, '2016-07-12 00:00:00', NULL, '2016-07-17 17:33:19', '2017-06-12 00:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `is_group_member`
--

CREATE TABLE IF NOT EXISTS `is_group_member` (
  `powon_group_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `request_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `approval_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`powon_group_id`,`member_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `is_group_member`
--

INSERT INTO `is_group_member` (`powon_group_id`, `member_id`, `request_date`, `approval_date`) VALUES
(1, 4, '2016-07-17 17:33:19', '2016-07-17 17:33:19'),
(2, 4, '2016-07-17 17:33:19', '2016-07-17 17:33:19'),
(2, 5, '2016-07-17 17:33:19', '2016-07-17 17:33:19');

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE IF NOT EXISTS `member` (
  `member_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(64) DEFAULT NULL,
  `last_name` varchar(64) DEFAULT NULL,
  `user_email` varchar(255) NOT NULL,
  `date_of_birth` date NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_admin` char(1) NOT NULL DEFAULT 'N',
  `status` char(1) NOT NULL DEFAULT 'A',
  `region_access` int(11) NOT NULL DEFAULT '0',
  `lives_in` int(11) DEFAULT NULL,
  `professions_access` int(11) NOT NULL DEFAULT '0',
  `interests_access` int(11) NOT NULL DEFAULT '0',
  `dob_access` int(11) NOT NULL DEFAULT '0',
  `email_access` int(11) NOT NULL DEFAULT '0',
  `profile_picture` varchar(255) DEFAULT '/assets/images/profile/lionfish.jpg',
  PRIMARY KEY (`member_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `user_email` (`user_email`),
  UNIQUE KEY `member_username_index` (`username`),
  UNIQUE KEY `member_email_index` (`user_email`),
  KEY `lives_in` (`lives_in`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`member_id`, `username`, `password`, `first_name`, `last_name`, `user_email`, `date_of_birth`, `registration_date`, `is_admin`, `status`, `region_access`, `lives_in`, `professions_access`, `interests_access`, `dob_access`, `email_access`, `profile_picture`) VALUES
(1, 'johnsmith', '$2y$10$3r.tgTgusETeYuKstAbqb.AooeLLU9RFhUJTIKXDW5HJk3Hjyft8K', 'John', 'Smith', 'johnsmith@warmup.project.ca', '1990-06-12', '2016-07-17 17:33:19', 'N', 'A', -1, 3, -1, -1, -1, -1, '/assets/images/profile/lionfish.jpg'),
(2, 'ndalo', '$2y$10$gZc2loyYSeJuB48JILeSeuGgG1038zzE3VhvH.j7ybOSidpiT4yNu', 'Ndalo', 'Zolani', 'ndalo.zolani@warmup.project.ca', '1989-12-13', '2016-07-17 17:33:19', 'N', 'A', -1, 3, -1, -1, -1, -1, '/assets/images/profile/lionfish.jpg'),
(3, 'haruhisuzumiya', '$2y$10$ail5Y3rzubZCSH1yDeqHo.VhWW3ce9plNM59Gkw.5pbk5DF899mk2', 'ハルヒ', '涼宮', 'suzumiya.haruhi@warmup.project.ca', '1992-07-26', '2016-07-17 17:33:19', 'Y', 'A', -1, 4, -1, -1, -1, -1, '/assets/images/profile/lionfish.jpg'),
(4, 'robertom', '$2y$10$D6F1JbRmdGr0coOVIMcj1.ySlMdNuISj3P3FzupHqFdbTp0BAGbVS', 'Roberto', 'McDonald', 'roberto.m@warmup.project.ca', '1959-04-08', '2016-07-17 17:33:19', 'N', 'I', -1, 1, -1, -1, -1, -1, '/assets/images/profile/lionfish.jpg'),
(5, 'rohit', '$2y$10$e1b7JEyG4L0vU9lJPI.r8uFjlgmbt7asRcaW4YiJHb0HShZGxVwai', 'Rohit', 'Singh', 'rohit.singh@warmup.project.ca', '1994-10-17', '2016-07-17 17:33:19', 'N', 'S', -1, 2, -1, -1, -1, -1, '/assets/images/profile/lionfish.jpg'),
(6, 'admin', '$2y$10$swN87lyk6IeGJ6uJgeqRRusDkxpFJI9BkJimRfZWVmMMIoOzpWOku', 'Admin 123', 'Admin', 'admin@powon.ca', '1999-12-31', '2016-07-17 17:33:19', 'Y', 'A', 0, 7, 0, 0, 0, 0, '/assets/images/profile/lionfish.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `member_can_access_page`
--

CREATE TABLE IF NOT EXISTS `member_can_access_page` (
  `page_id` int(11) NOT NULL,
  `powon_group_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  PRIMARY KEY (`page_id`,`powon_group_id`,`member_id`),
  KEY `powon_group_id` (`powon_group_id`,`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `member_session`
--

CREATE TABLE IF NOT EXISTS `member_session` (
  `token` varchar(64) NOT NULL,
  `member_id` int(11) NOT NULL,
  `last_access` int(11) NOT NULL,
  `session_data` text,
  PRIMARY KEY (`token`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `member_session`
--

INSERT INTO `member_session` (`token`, `member_id`, `last_access`, `session_data`) VALUES
('04bb3e8b72cae2fb9d5c72959bdbb0d445c433931aa0e40c68922ce0c54b6eff', 6, 1469137241, '{"remember":false}'),
('1bed11e605849eccf0f2f6e374719d7b9297f8f5de1007d7278374fed20e24b0', 6, 1469323621, '{"remember":false}');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `message_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `subject` varchar(255) DEFAULT NULL,
  `body` text,
  `from_member` int(11) DEFAULT NULL,
  PRIMARY KEY (`message_id`),
  KEY `from_member` (`from_member`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `messages_to`
--

CREATE TABLE IF NOT EXISTS `messages_to` (
  `message_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `message_seen` char(1) NOT NULL DEFAULT 'N',
  `message_deleted` char(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (`message_id`,`member_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `page`
--

CREATE TABLE IF NOT EXISTS `page` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_title` varchar(64) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE IF NOT EXISTS `post` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `post_date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `post_type` char(1) NOT NULL DEFAULT 'T',
  `path_to_resource` varchar(255) DEFAULT NULL,
  `post_body` text,
  `comment_permission` char(1) NOT NULL DEFAULT 'A',
  `parent_post` int(11) DEFAULT NULL,
  `page_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  PRIMARY KEY (`post_id`),
  KEY `parent_post` (`parent_post`),
  KEY `page_id` (`page_id`),
  KEY `author_id` (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `powon_group`
--

CREATE TABLE IF NOT EXISTS `powon_group` (
  `powon_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_title` varchar(64) DEFAULT NULL,
  `description` text,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `group_picture` varchar(255) DEFAULT NULL,
  `group_owner` int(11) DEFAULT NULL,
  PRIMARY KEY (`powon_group_id`),
  KEY `group_owner` (`group_owner`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `powon_group`
--

INSERT INTO `powon_group` (`powon_group_id`, `group_title`, `description`, `date_created`, `group_picture`, `group_owner`) VALUES
(1, 'Lord of the Rings Fans', 'A relaxed group to share information about The Lord Of The Rings.', '2016-07-17 17:33:19', NULL, 3),
(2, 'Project R', 'A mysterious group working on the so-called ''Project R''', '2016-07-17 17:33:19', NULL, 4);

-- --------------------------------------------------------

--
-- Table structure for table `profession`
--

CREATE TABLE IF NOT EXISTS `profession` (
  `profession_name` varchar(255) NOT NULL,
  PRIMARY KEY (`profession_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `profession`
--

INSERT INTO `profession` (`profession_name`) VALUES
('Software Developer'),
('Student');

-- --------------------------------------------------------

--
-- Table structure for table `profile_page`
--

CREATE TABLE IF NOT EXISTS `profile_page` (
  `page_id` int(11) NOT NULL,
  `page_access` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  PRIMARY KEY (`page_id`),
  UNIQUE KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `region`
--

CREATE TABLE IF NOT EXISTS `region` (
  `region_id` int(11) NOT NULL AUTO_INCREMENT,
  `country` varchar(32) NOT NULL,
  `province` varchar(32) DEFAULT NULL,
  `city` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`region_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `region`
--

INSERT INTO `region` (`region_id`, `country`, `province`, `city`) VALUES
(1, 'Canada', 'Quebec', 'Montreal'),
(2, 'Canada', 'Ontario', 'Toronto'),
(3, 'Canada', 'Quebec', 'Laval'),
(4, '日本', '関東', '東京'),
(5, 'Canada', 'Quebec', 'Montreal'),
(6, 'Canada', 'Quebec', 'City Of Montreal'),
(7, 'Canada', 'Quebec', 'Quebec City'),
(8, 'Canada', 'Quebec', 'Quebec City'),
(9, 'Canada', 'Quebec', 'Quebec City');

-- --------------------------------------------------------

--
-- Table structure for table `related_members`
--

CREATE TABLE IF NOT EXISTS `related_members` (
  `member_from` int(11) NOT NULL,
  `member_to` int(11) NOT NULL,
  `relation_type` char(1) NOT NULL DEFAULT 'F',
  `request_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `approval_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`member_from`,`member_to`),
  KEY `member_to` (`member_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `related_members`
--

INSERT INTO `related_members` (`member_from`, `member_to`, `relation_type`, `request_date`, `approval_date`) VALUES
(1, 3, 'F', '2016-07-17 17:33:19', '2016-07-17 17:33:19');

-- --------------------------------------------------------

--
-- Table structure for table `votes_on`
--

CREATE TABLE IF NOT EXISTS `votes_on` (
  `member_id` int(11) NOT NULL,
  `powon_group_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `event_date` date NOT NULL,
  `event_time` time NOT NULL,
  `location` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`member_id`,`powon_group_id`,`event_id`,`event_date`,`event_time`,`location`),
  KEY `event_id` (`event_id`,`event_date`,`event_time`,`location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `works_as`
--

CREATE TABLE IF NOT EXISTS `works_as` (
  `member_id` int(11) NOT NULL,
  `profession_name` varchar(255) NOT NULL,
  `date_started` date DEFAULT NULL,
  `date_ended` date DEFAULT NULL,
  PRIMARY KEY (`member_id`,`profession_name`),
  KEY `profession_name` (`profession_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `works_as`
--

INSERT INTO `works_as` (`member_id`, `profession_name`, `date_started`, `date_ended`) VALUES
(3, 'Student', NULL, NULL),
(4, 'Software Developer', NULL, NULL),
(6, 'Student', '2016-01-01', '2016-07-02');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `event_ibfk_1` FOREIGN KEY (`powon_group_id`) REFERENCES `powon_group` (`powon_group_id`) ON DELETE CASCADE;

--
-- Constraints for table `event_details`
--
ALTER TABLE `event_details`
  ADD CONSTRAINT `event_details_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`) ON DELETE CASCADE;

--
-- Constraints for table `gift_exchange`
--
ALTER TABLE `gift_exchange`
  ADD CONSTRAINT `gift_exchange_ibfk_1` FOREIGN KEY (`from_member`) REFERENCES `member` (`member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gift_exchange_ibfk_2` FOREIGN KEY (`to_member`) REFERENCES `member` (`member_id`) ON DELETE CASCADE;

--
-- Constraints for table `group_page`
--
ALTER TABLE `group_page`
  ADD CONSTRAINT `group_page_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `page` (`page_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_page_ibfk_2` FOREIGN KEY (`page_owner`) REFERENCES `member` (`member_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `group_page_ibfk_3` FOREIGN KEY (`page_group`) REFERENCES `powon_group` (`powon_group_id`) ON DELETE CASCADE;

--
-- Constraints for table `has_interests`
--
ALTER TABLE `has_interests`
  ADD CONSTRAINT `has_interests_ibfk_1` FOREIGN KEY (`interest_name`) REFERENCES `interests` (`interest_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `has_interests_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `member` (`member_id`) ON DELETE CASCADE;

--
-- Constraints for table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`account_holder`) REFERENCES `member` (`member_id`) ON DELETE CASCADE;

--
-- Constraints for table `is_group_member`
--
ALTER TABLE `is_group_member`
  ADD CONSTRAINT `is_group_member_ibfk_1` FOREIGN KEY (`powon_group_id`) REFERENCES `powon_group` (`powon_group_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `is_group_member_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `member` (`member_id`) ON DELETE CASCADE;

--
-- Constraints for table `member`
--
ALTER TABLE `member`
  ADD CONSTRAINT `member_ibfk_1` FOREIGN KEY (`lives_in`) REFERENCES `region` (`region_id`) ON DELETE SET NULL;

--
-- Constraints for table `member_can_access_page`
--
ALTER TABLE `member_can_access_page`
  ADD CONSTRAINT `member_can_access_page_ibfk_1` FOREIGN KEY (`powon_group_id`, `member_id`) REFERENCES `is_group_member` (`powon_group_id`, `member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `member_can_access_page_ibfk_2` FOREIGN KEY (`page_id`) REFERENCES `page` (`page_id`) ON DELETE CASCADE;

--
-- Constraints for table `member_session`
--
ALTER TABLE `member_session`
  ADD CONSTRAINT `member_session_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`member_id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`from_member`) REFERENCES `member` (`member_id`) ON DELETE SET NULL;

--
-- Constraints for table `messages_to`
--
ALTER TABLE `messages_to`
  ADD CONSTRAINT `messages_to_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `messages` (`message_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_to_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `member` (`member_id`) ON DELETE CASCADE;

--
-- Constraints for table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`parent_post`) REFERENCES `post` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_ibfk_2` FOREIGN KEY (`page_id`) REFERENCES `page` (`page_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_ibfk_3` FOREIGN KEY (`author_id`) REFERENCES `member` (`member_id`) ON DELETE CASCADE;

--
-- Constraints for table `powon_group`
--
ALTER TABLE `powon_group`
  ADD CONSTRAINT `powon_group_ibfk_1` FOREIGN KEY (`group_owner`) REFERENCES `member` (`member_id`) ON DELETE SET NULL;

--
-- Constraints for table `profile_page`
--
ALTER TABLE `profile_page`
  ADD CONSTRAINT `profile_page_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `page` (`page_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `profile_page_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `member` (`member_id`) ON DELETE CASCADE;

--
-- Constraints for table `related_members`
--
ALTER TABLE `related_members`
  ADD CONSTRAINT `related_members_ibfk_1` FOREIGN KEY (`member_from`) REFERENCES `member` (`member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `related_members_ibfk_2` FOREIGN KEY (`member_to`) REFERENCES `member` (`member_id`) ON DELETE CASCADE;

--
-- Constraints for table `votes_on`
--
ALTER TABLE `votes_on`
  ADD CONSTRAINT `votes_on_ibfk_1` FOREIGN KEY (`member_id`, `powon_group_id`) REFERENCES `is_group_member` (`member_id`, `powon_group_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_on_ibfk_2` FOREIGN KEY (`event_id`, `event_date`, `event_time`, `location`) REFERENCES `event_details` (`event_id`, `event_date`, `event_time`, `location`) ON DELETE CASCADE;

--
-- Constraints for table `works_as`
--
ALTER TABLE `works_as`
  ADD CONSTRAINT `works_as_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `works_as_ibfk_2` FOREIGN KEY (`profession_name`) REFERENCES `profession` (`profession_name`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
