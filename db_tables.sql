--
-- Creates the tables for the event checklister database.
-- Note: This script should be run after 'db_create.sql' script has 
-- created the "ecdb" database and ec_admin user.
-- 
-- Run as ec_admin with: 
--       mysql -u ec_admin -psecretpassword ecdb < db_tables.sql > log.txt
-- 
--


--
-- Create the users table
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(32) NOT NULL,
  `password` VARCHAR(64) NOT NULL,
  `email` TEXT NOT NULL,
  `permissions` int(3) DEFAULT 5 NOT NULL,
  `verificationhash` VARCHAR(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for users table
-- (plaintext passwords for alice, jane, and bob are 'tmppassword', 'secret', 
-- and 'spot' respectively.)
--

INSERT INTO `users` VALUES
  (1,'alice','$2y$10$8kshpRWyHV0L28/04dIj9uL0rnQ1kB3FcYgkDYiQB4u6Vsj7Qrky2','alice@gmail.com',4, 'ba17c87dd0ed1b39b4a8321561fb39f4'),
  (2,'jane','$2y$10$FkumYaoumhAS0UG3pQR7Su9eE4CNSN924ZI9U4WRDx0N3XRsUxHTC','jane@aol.com',4,'368696148f319fde5ff4c88939a479e2'),
  (3,'betty','$2y$10$Z/ow6kXyOkeipqmNElHpqOO9PHp0oE.qIL/oB2fLGW.pDs5cetnJi','betty1@hotmail.com',5,'89e4b1189f02aeaf692a06464d75a773');

--
-- Create the events table
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` int(3) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `showpage_url` VARCHAR(255),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for events table
--

INSERT INTO `events` VALUES (1,320,1,'2017-02-02','18:30:00.0','https://www.pamperedchef.com/go/abc'),(2,99,2,'2017-02-03','18:00:00.0','https://www.pamperedchef.com/go/def'),(3,251,1,'2017-02-04','19:00:00.0','https://www.pamperedchef.com/go/ghi');

--
-- Create the event_sets table
--

DROP TABLE IF EXISTS `event_sets`;
CREATE TABLE `event_sets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` VARCHAR(50)  NOT NULL,
  `favorite` BOOLEAN DEFAULT 0 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Create the event_sets_events table
--

DROP TABLE IF EXISTS `event_sets_events`;
CREATE TABLE `event_sets_events` ( 
  `event_sets_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  KEY `event_sets_id` (`event_sets_id`),
  KEY `event_id` (`event_id`) 
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Create the contacts table
--

DROP TABLE IF EXISTS `contacts`;
CREATE TABLE `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `first_name` VARCHAR(60) NOT NULL,
  `last_name` VARCHAR(60) NOT NULL,
  `email` VARCHAR(50) NOT NULL,
  `day_phone` VARCHAR(20),
  `evening_phone` VARCHAR(20),
  `cell_phone` VARCHAR(20),
  `addr1` VARCHAR(60) NOT NULL,
  `addr2` VARCHAR(60),
  `addr3` VARCHAR(60),
  `city` VARCHAR(30) NOT NULL,
  `state` VARCHAR(30) NOT NULL,
  `zip` VARCHAR(13) NOT NULL,
  `invite_list_url` VARCHAR(255),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for contacts table
--

INSERT INTO `contacts` VALUES (1,2,'Jane','Doe','jane@aol.com','570-445-0909',NULL,NULL,'123 Main St.',NULL,NULL,'Newberg','PA','17332','https://www.google.com/go/abc'),(2,1,'Betty','Khan','jman@hotmail.com',NULL,'610-818-1234',NULL,'1 Applewood Ln.','Apt. 33B',NULL,'York','PA','19999','https://www.google.com/go/def'),(3,1,'Carl','Borax','hesper@trump.com',NULL,NULL,'717-766-8132','665 West Chestertonville St.','RR1','(under the bridge)','Kingsbury','AK','07734','https://www.google.com/go/ghi');


--
-- Create the events_contacts table
--

DROP TABLE IF EXISTS `events_contacts`;
CREATE TABLE `events_contacts` ( 
  `event_id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL,
  KEY `event_id` (`event_id`),
  KEY `contact_id` (`contact_id`) 
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for the shows_contacts table
--

INSERT INTO `events_contacts` VALUES
  (1,1),
  (2,1),
  (2,2),
  (3,3);



