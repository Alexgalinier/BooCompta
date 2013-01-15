-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Mar 15 Janvier 2013 à 18:03
-- Version du serveur: 5.5.24-log
-- Version de PHP: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `alexgaliddhelp`
--

-- --------------------------------------------------------

--
-- Structure de la table `boocompta_charge`
--

CREATE TABLE IF NOT EXISTS `boocompta_charge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_user` int(11) NOT NULL,
  `type` enum('charge','extra') COLLATE utf8_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `amount` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=42 ;

-- --------------------------------------------------------

--
-- Structure de la table `boocompta_coworker`
--

CREATE TABLE IF NOT EXISTS `boocompta_coworker` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `is_collab_assoc` tinyint(1) NOT NULL DEFAULT '0',
  `is_collab_assoc_tier_pay` tinyint(1) NOT NULL DEFAULT '0',
  `is_rempla` tinyint(1) NOT NULL DEFAULT '0',
  `fk_user` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11 ;

--
-- Contenu de la table `boocompta_coworker`
--

INSERT INTO `boocompta_coworker` (`id`, `name`, `is_collab_assoc`, `is_collab_assoc_tier_pay`, `is_rempla`, `fk_user`) VALUES
(6, 'Sandra', 0, 0, 1, 1),
(7, 'Florence', 1, 1, 1, 1),
(8, 'Caro', 1, 0, 0, 1),
(9, 'Christelle', 0, 0, 1, 1),
(10, 'Danielle', 0, 0, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `boocompta_payment`
--

CREATE TABLE IF NOT EXISTS `boocompta_payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('mutuel','cpam','full') COLLATE utf8_unicode_ci NOT NULL,
  `fk_coworker` int(11) NOT NULL,
  `date` date NOT NULL,
  `patient_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `amount` float NOT NULL DEFAULT '0',
  `percent` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=411 ;


--
-- Structure de la table `boocompta_prestation`
--

CREATE TABLE IF NOT EXISTS `boocompta_prestation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_coworker` int(11) NOT NULL,
  `fk_payment` int(11) NOT NULL DEFAULT '0',
  `fk_payment_mutuel` int(11) NOT NULL DEFAULT '0',
  `fk_payment_cpam` int(11) NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  `patient_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `amount` float NOT NULL,
  `is_collab_assoc` tinyint(1) NOT NULL DEFAULT '0',
  `is_rempla` tinyint(1) NOT NULL DEFAULT '0',
  `is_paid` tinyint(11) NOT NULL DEFAULT '0',
  `is_paid_mutuel` tinyint(1) NOT NULL DEFAULT '0',
  `is_paid_cpam` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3821 ;

-- --------------------------------------------------------

--
-- Structure de la table `boocompta_user`
--

CREATE TABLE IF NOT EXISTS `boocompta_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `tax_percent_on_salary` int(11) NOT NULL DEFAULT '0',
  `administrated_user_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Contenu de la table `boocompta_user`
--

INSERT INTO `boocompta_user` (`id`, `login`, `password`, `tax_percent_on_salary`, `administrated_user_id`) VALUES
(1, 'user', 'user', 50, 0),
(2, 'comptable', 'comptable', 50, 1),
(3, 'demo', 'demo', 30, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
