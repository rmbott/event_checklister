--
-- Creates the event checklister database and admin user.
-- Note: Run 'db_tables.sql' script after this one, to create 
-- tables and some test data.
--
-- Run as mysql root with: mysql -u root -p < db_create.sql > log.txt
--

--
-- Create the ecdb database
--
DROP DATABASE IF EXISTS `ecdb`;
CREATE DATABASE ecdb;

--
-- Create the pc_admin user and grant privileges
--
DROP USER IF EXISTS `ec_admin`@`localhost`;
GRANT ALL PRIVILEGES ON ecdb.* TO 'ec_admin'@'localhost' IDENTIFIED BY 'secretpassword';
