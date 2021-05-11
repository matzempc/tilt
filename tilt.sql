-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Erstellungszeit: 06. Mai 2020 um 19:45
-- Server-Version: 5.7.30-0ubuntu0.18.04.1
-- PHP-Version: 7.0.33-0ubuntu0.16.04.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `tilt`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hydrometer`
--
CREATE DATABASE `tilt`;
USE `tilt`;
CREATE TABLE `hydrometer` (
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timepoint` double NOT NULL,
  `temperature` float NOT NULL,
  `gravity` double NOT NULL,
  `beer` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL,
  `comment` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `hydrometer`
--
ALTER TABLE `hydrometer`
  ADD PRIMARY KEY (`timestamp`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
