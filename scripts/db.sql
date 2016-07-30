-- MySQL dump 10.13  Distrib 5.5.46, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: powon
-- ------------------------------------------------------
-- Server version	5.5.46-0ubuntu0.14.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(64) DEFAULT NULL,
  `description` text,
  `powon_group_id` int(11) NOT NULL,
  PRIMARY KEY (`event_id`),
  KEY `powon_group_id` (`powon_group_id`),
  CONSTRAINT `event_ibfk_1` FOREIGN KEY (`powon_group_id`) REFERENCES `powon_group` (`powon_group_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event`
--

LOCK TABLES `event` WRITE;
/*!40000 ALTER TABLE `event` DISABLE KEYS */;
/*!40000 ALTER TABLE `event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_details`
--

DROP TABLE IF EXISTS `event_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_details` (
  `event_id` int(11) NOT NULL,
  `event_date` date NOT NULL,
  `event_time` time NOT NULL,
  `location` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`event_id`,`event_date`,`event_time`,`location`),
  CONSTRAINT `event_details_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_details`
--

LOCK TABLES `event_details` WRITE;
/*!40000 ALTER TABLE `event_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gift_exchange`
--

DROP TABLE IF EXISTS `gift_exchange`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gift_exchange` (
  `from_member` int(11) NOT NULL,
  `to_member` int(11) NOT NULL,
  `gift_exchange_date` date NOT NULL,
  PRIMARY KEY (`from_member`,`to_member`,`gift_exchange_date`),
  KEY `to_member` (`to_member`),
  CONSTRAINT `gift_exchange_ibfk_1` FOREIGN KEY (`from_member`) REFERENCES `member` (`member_id`) ON DELETE CASCADE,
  CONSTRAINT `gift_exchange_ibfk_2` FOREIGN KEY (`to_member`) REFERENCES `member` (`member_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gift_exchange`
--

LOCK TABLES `gift_exchange` WRITE;
/*!40000 ALTER TABLE `gift_exchange` DISABLE KEYS */;
/*!40000 ALTER TABLE `gift_exchange` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `group_page`
--

DROP TABLE IF EXISTS `group_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_page` (
  `page_id` int(11) NOT NULL,
  `page_description` text,
  `access_type` char(1) NOT NULL DEFAULT 'P',
  `page_owner` int(11) DEFAULT NULL,
  `page_group` int(11) NOT NULL,
  PRIMARY KEY (`page_id`),
  KEY `page_owner` (`page_owner`),
  KEY `page_group` (`page_group`),
  CONSTRAINT `group_page_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `page` (`page_id`) ON DELETE CASCADE,
  CONSTRAINT `group_page_ibfk_2` FOREIGN KEY (`page_owner`) REFERENCES `member` (`member_id`) ON DELETE SET NULL,
  CONSTRAINT `group_page_ibfk_3` FOREIGN KEY (`page_group`) REFERENCES `powon_group` (`powon_group_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_page`
--

LOCK TABLES `group_page` WRITE;
/*!40000 ALTER TABLE `group_page` DISABLE KEYS */;
/*!40000 ALTER TABLE `group_page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `has_interests`
--

DROP TABLE IF EXISTS `has_interests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `has_interests` (
  `interest_name` varchar(255) NOT NULL,
  `member_id` int(11) NOT NULL,
  PRIMARY KEY (`interest_name`,`member_id`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `has_interests_ibfk_1` FOREIGN KEY (`interest_name`) REFERENCES `interests` (`interest_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `has_interests_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `member` (`member_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `has_interests`
--

LOCK TABLES `has_interests` WRITE;
/*!40000 ALTER TABLE `has_interests` DISABLE KEYS */;
INSERT INTO `has_interests` VALUES ('Fishing',1),('Basketball',2),('Fishing',2),('Aliens',3),('Fantasy Books',4),('Soccer',4);
/*!40000 ALTER TABLE `has_interests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `interests`
--

DROP TABLE IF EXISTS `interests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `interests` (
  `interest_name` varchar(255) NOT NULL,
  PRIMARY KEY (`interest_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `interests`
--

LOCK TABLES `interests` WRITE;
/*!40000 ALTER TABLE `interests` DISABLE KEYS */;
INSERT INTO `interests` VALUES ('Aliens'),('Basketball'),('Fantasy Books'),('Fishing'),('Soccer');
/*!40000 ALTER TABLE `interests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice`
--

DROP TABLE IF EXISTS `invoice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice` (
  `invoice_id` int(11) NOT NULL AUTO_INCREMENT,
  `amount_due` decimal(5,2) NOT NULL,
  `payment_deadline` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_paid` timestamp NULL DEFAULT NULL,
  `billing_period_start` timestamp NOT NULL DEFAULT '1970-01-01 00:00:01',
  `billing_period_end` timestamp NOT NULL DEFAULT '1970-01-01 00:00:01',
  `account_holder` int(11) NOT NULL,
  PRIMARY KEY (`invoice_id`),
  KEY `account_holder` (`account_holder`),
  CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`account_holder`) REFERENCES `member` (`member_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice`
--

LOCK TABLES `invoice` WRITE;
/*!40000 ALTER TABLE `invoice` DISABLE KEYS */;
INSERT INTO `invoice` VALUES (1,32.00,'2016-07-12 00:00:00',NULL,'2016-07-17 17:33:19','2017-06-12 00:00:00',4),(2,32.00,'2016-07-12 00:00:00',NULL,'2016-07-17 17:33:19','2017-06-12 00:00:00',2),(3,32.00,'2016-07-12 00:00:00',NULL,'2016-07-17 17:33:19','2017-06-12 00:00:00',1);
/*!40000 ALTER TABLE `invoice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `is_group_member`
--

DROP TABLE IF EXISTS `is_group_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `is_group_member` (
  `powon_group_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `request_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `approval_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`powon_group_id`,`member_id`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `is_group_member_ibfk_1` FOREIGN KEY (`powon_group_id`) REFERENCES `powon_group` (`powon_group_id`) ON DELETE CASCADE,
  CONSTRAINT `is_group_member_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `member` (`member_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `is_group_member`
--

LOCK TABLES `is_group_member` WRITE;
/*!40000 ALTER TABLE `is_group_member` DISABLE KEYS */;
INSERT INTO `is_group_member` VALUES (1,4,'2016-07-17 17:33:19','2016-07-17 17:33:19'),(2,4,'2016-07-17 17:33:19','2016-07-17 17:33:19'),(2,5,'2016-07-17 17:33:19','2016-07-17 17:33:19');
/*!40000 ALTER TABLE `is_group_member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `member`
--

DROP TABLE IF EXISTS `member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `member` (
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
  KEY `lives_in` (`lives_in`),
  CONSTRAINT `member_ibfk_1` FOREIGN KEY (`lives_in`) REFERENCES `region` (`region_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member`
--

LOCK TABLES `member` WRITE;
/*!40000 ALTER TABLE `member` DISABLE KEYS */;
INSERT INTO `member` VALUES (1,'johnsmith','$2y$10$3r.tgTgusETeYuKstAbqb.AooeLLU9RFhUJTIKXDW5HJk3Hjyft8K','John','Smith','johnsmith@warmup.project.ca','1990-06-12','2016-07-17 17:33:19','N','A',-1,3,-1,-1,-1,-1,'/assets/images/profile/lionfish.jpg'),(2,'ndalo','$2y$10$gZc2loyYSeJuB48JILeSeuGgG1038zzE3VhvH.j7ybOSidpiT4yNu','Ndalo','Zolani','ndalo.zolani@warmup.project.ca','1989-12-13','2016-07-17 17:33:19','N','A',-1,3,-1,-1,-1,-1,'/assets/images/profile/lionfish.jpg'),(3,'haruhisuzumiya','$2y$10$ail5Y3rzubZCSH1yDeqHo.VhWW3ce9plNM59Gkw.5pbk5DF899mk2','ハルヒ','涼宮','suzumiya.haruhi@warmup.project.ca','1992-07-26','2016-07-17 17:33:19','Y','A',-1,4,-1,-1,-1,-1,'/assets/images/profile/lionfish.jpg'),(4,'robertom','$2y$10$D6F1JbRmdGr0coOVIMcj1.ySlMdNuISj3P3FzupHqFdbTp0BAGbVS','Roberto','McDonald','roberto.m@warmup.project.ca','1959-04-08','2016-07-17 17:33:19','N','I',-1,1,-1,-1,-1,-1,'/assets/images/profile/lionfish.jpg'),(5,'rohit','$2y$10$e1b7JEyG4L0vU9lJPI.r8uFjlgmbt7asRcaW4YiJHb0HShZGxVwai','Rohit','Singh','rohit.singh@warmup.project.ca','1994-10-17','2016-07-17 17:33:19','N','S',-1,2,-1,-1,-1,-1,'/assets/images/profile/lionfish.jpg'),(6,'admin','$2y$10$swN87lyk6IeGJ6uJgeqRRusDkxpFJI9BkJimRfZWVmMMIoOzpWOku','Admin','Admin','admin@powon.ca','1999-12-31','2016-07-17 17:33:19','Y','A',0,NULL,0,0,0,0,'/assets/images/profile/lionfish.jpg');
/*!40000 ALTER TABLE `member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `member_can_access_page`
--

DROP TABLE IF EXISTS `member_can_access_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `member_can_access_page` (
  `page_id` int(11) NOT NULL,
  `powon_group_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  PRIMARY KEY (`page_id`,`powon_group_id`,`member_id`),
  KEY `powon_group_id` (`powon_group_id`,`member_id`),
  CONSTRAINT `member_can_access_page_ibfk_1` FOREIGN KEY (`powon_group_id`, `member_id`) REFERENCES `is_group_member` (`powon_group_id`, `member_id`) ON DELETE CASCADE,
  CONSTRAINT `member_can_access_page_ibfk_2` FOREIGN KEY (`page_id`) REFERENCES `page` (`page_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member_can_access_page`
--

LOCK TABLES `member_can_access_page` WRITE;
/*!40000 ALTER TABLE `member_can_access_page` DISABLE KEYS */;
/*!40000 ALTER TABLE `member_can_access_page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `member_session`
--

DROP TABLE IF EXISTS `member_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `member_session` (
  `token` varchar(64) NOT NULL,
  `member_id` int(11) NOT NULL,
  `last_access` int(11) NOT NULL,
  `session_data` text,
  PRIMARY KEY (`token`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `member_session_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`member_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member_session`
--

LOCK TABLES `member_session` WRITE;
/*!40000 ALTER TABLE `member_session` DISABLE KEYS */;
INSERT INTO `member_session` VALUES ('04bb3e8b72cae2fb9d5c72959bdbb0d445c433931aa0e40c68922ce0c54b6eff',6,1469137241,'{\"remember\":false}');
/*!40000 ALTER TABLE `member_session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `message_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `subject` varchar(255) DEFAULT NULL,
  `body` text,
  `from_member` int(11) DEFAULT NULL,
  PRIMARY KEY (`message_id`),
  KEY `from_member` (`from_member`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`from_member`) REFERENCES `member` (`member_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages_to`
--

DROP TABLE IF EXISTS `messages_to`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages_to` (
  `message_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `message_seen` char(1) NOT NULL DEFAULT 'N',
  `message_deleted` char(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (`message_id`,`member_id`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `messages_to_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `messages` (`message_id`) ON DELETE CASCADE,
  CONSTRAINT `messages_to_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `member` (`member_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages_to`
--

LOCK TABLES `messages_to` WRITE;
/*!40000 ALTER TABLE `messages_to` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages_to` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `page`
--

DROP TABLE IF EXISTS `page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `page` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_title` varchar(64) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page`
--

LOCK TABLES `page` WRITE;
/*!40000 ALTER TABLE `page` DISABLE KEYS */;
/*!40000 ALTER TABLE `page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post`
--

DROP TABLE IF EXISTS `post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `post` (
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
  KEY `author_id` (`author_id`),
  CONSTRAINT `post_ibfk_1` FOREIGN KEY (`parent_post`) REFERENCES `post` (`post_id`) ON DELETE CASCADE,
  CONSTRAINT `post_ibfk_2` FOREIGN KEY (`page_id`) REFERENCES `page` (`page_id`) ON DELETE CASCADE,
  CONSTRAINT `post_ibfk_3` FOREIGN KEY (`author_id`) REFERENCES `member` (`member_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post`
--

LOCK TABLES `post` WRITE;
/*!40000 ALTER TABLE `post` DISABLE KEYS */;
/*!40000 ALTER TABLE `post` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `powon_group`
--

DROP TABLE IF EXISTS `powon_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `powon_group` (
  `powon_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_title` varchar(64) DEFAULT NULL,
  `description` text,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `group_picture` varchar(255) DEFAULT NULL,
  `group_owner` int(11) DEFAULT NULL,
  PRIMARY KEY (`powon_group_id`),
  KEY `group_owner` (`group_owner`),
  CONSTRAINT `powon_group_ibfk_1` FOREIGN KEY (`group_owner`) REFERENCES `member` (`member_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `powon_group`
--

LOCK TABLES `powon_group` WRITE;
/*!40000 ALTER TABLE `powon_group` DISABLE KEYS */;
INSERT INTO `powon_group` VALUES (1,'Lord of the Rings Fans','A relaxed group to share information about The Lord Of The Rings.','2016-07-17 17:33:19',NULL,3),(2,'Project R','A mysterious group working on the so-called \'Project R\'','2016-07-17 17:33:19',NULL,4);
/*!40000 ALTER TABLE `powon_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profession`
--

DROP TABLE IF EXISTS `profession`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profession` (
  `profession_name` varchar(255) NOT NULL,
  PRIMARY KEY (`profession_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profession`
--

LOCK TABLES `profession` WRITE;
/*!40000 ALTER TABLE `profession` DISABLE KEYS */;
INSERT INTO `profession` VALUES ('Software Developer'),('Student');
/*!40000 ALTER TABLE `profession` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profile_page`
--

DROP TABLE IF EXISTS `profile_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profile_page` (
  `page_id` int(11) NOT NULL,
  `page_access` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  PRIMARY KEY (`page_id`),
  UNIQUE KEY `member_id` (`member_id`),
  CONSTRAINT `profile_page_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `page` (`page_id`) ON DELETE CASCADE,
  CONSTRAINT `profile_page_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `member` (`member_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile_page`
--

LOCK TABLES `profile_page` WRITE;
/*!40000 ALTER TABLE `profile_page` DISABLE KEYS */;
/*!40000 ALTER TABLE `profile_page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `region`
--

DROP TABLE IF EXISTS `region`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `region` (
  `region_id` int(11) NOT NULL AUTO_INCREMENT,
  `country` varchar(32) NOT NULL,
  `province` varchar(32) DEFAULT NULL,
  `city` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`region_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `region`
--

LOCK TABLES `region` WRITE;
/*!40000 ALTER TABLE `region` DISABLE KEYS */;
INSERT INTO `region` VALUES (1,'Canada','Quebec','Montreal'),(2,'Canada','Ontario','Toronto'),(3,'Canada','Quebec','Laval'),(4,'日本','関東','東京');
/*!40000 ALTER TABLE `region` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `related_members`
--

DROP TABLE IF EXISTS `related_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `related_members` (
  `member_from` int(11) NOT NULL,
  `member_to` int(11) NOT NULL,
  `relation_type` char(1) NOT NULL DEFAULT 'F',
  `request_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `approval_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`member_from`,`member_to`),
  KEY `member_to` (`member_to`),
  CONSTRAINT `related_members_ibfk_1` FOREIGN KEY (`member_from`) REFERENCES `member` (`member_id`) ON DELETE CASCADE,
  CONSTRAINT `related_members_ibfk_2` FOREIGN KEY (`member_to`) REFERENCES `member` (`member_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `related_members`
--

LOCK TABLES `related_members` WRITE;
/*!40000 ALTER TABLE `related_members` DISABLE KEYS */;
INSERT INTO `related_members` VALUES (1,3,'F','2016-07-17 17:33:19','2016-07-17 17:33:19');
/*!40000 ALTER TABLE `related_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `votes_on`
--

DROP TABLE IF EXISTS `votes_on`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `votes_on` (
  `member_id` int(11) NOT NULL,
  `powon_group_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `event_date` date NOT NULL,
  `event_time` time NOT NULL,
  `location` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`member_id`,`powon_group_id`,`event_id`,`event_date`,`event_time`,`location`),
  KEY `event_id` (`event_id`,`event_date`,`event_time`,`location`),
  CONSTRAINT `votes_on_ibfk_1` FOREIGN KEY (`member_id`, `powon_group_id`) REFERENCES `is_group_member` (`member_id`, `powon_group_id`) ON DELETE CASCADE,
  CONSTRAINT `votes_on_ibfk_2` FOREIGN KEY (`event_id`, `event_date`, `event_time`, `location`) REFERENCES `event_details` (`event_id`, `event_date`, `event_time`, `location`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `votes_on`
--

LOCK TABLES `votes_on` WRITE;
/*!40000 ALTER TABLE `votes_on` DISABLE KEYS */;
/*!40000 ALTER TABLE `votes_on` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `works_as`
--

DROP TABLE IF EXISTS `works_as`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `works_as` (
  `member_id` int(11) NOT NULL,
  `profession_name` varchar(255) NOT NULL,
  `date_started` date DEFAULT NULL,
  `date_ended` date DEFAULT NULL,
  PRIMARY KEY (`member_id`,`profession_name`),
  KEY `profession_name` (`profession_name`),
  CONSTRAINT `works_as_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`member_id`) ON DELETE CASCADE,
  CONSTRAINT `works_as_ibfk_2` FOREIGN KEY (`profession_name`) REFERENCES `profession` (`profession_name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `works_as`
--

LOCK TABLES `works_as` WRITE;
/*!40000 ALTER TABLE `works_as` DISABLE KEYS */;
INSERT INTO `works_as` VALUES (3,'Student',NULL,NULL),(4,'Software Developer',NULL,NULL);
/*!40000 ALTER TABLE `works_as` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-07-21 21:43:56