-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 27 oct. 2025 à 08:58
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Create database if it does not exist (Plesk-friendly with general_ci)
CREATE DATABASE IF NOT EXISTS `memory` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `memory`;

--
-- Base de données : `memory`
--

-- --------------------------------------------------------

--
-- Structure de la table `leaders`
--

DROP TABLE IF EXISTS `leaders`;
CREATE TABLE IF NOT EXISTS `leaders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `score` int NOT NULL,
  `time` int NOT NULL,
  `emoji` varchar(8) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `players`
--

DROP TABLE IF EXISTS `players`;
CREATE TABLE IF NOT EXISTS `players` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `password_hash` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Ensure compatibility on older installs: add missing columns if they don't exist
-- MySQL older versions may not support `ADD COLUMN IF NOT EXISTS`, so we
-- conditionally run ALTER TABLE statements using INFORMATION_SCHEMA and
-- dynamic SQL (PREPARE/EXECUTE). This is more portable across versions.

-- password_hash
SET @s = (
  SELECT IF(COUNT(*)=0,
    'ALTER TABLE `players` ADD COLUMN `password_hash` varchar(255) DEFAULT NULL;',
    'SELECT "password_hash exists";'
  )
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'players' AND COLUMN_NAME = 'password_hash'
);
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- email
SET @s = (
  SELECT IF(COUNT(*)=0,
    'ALTER TABLE `players` ADD COLUMN `email` varchar(255) DEFAULT NULL;',
    'SELECT "email exists";'
  )
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'players' AND COLUMN_NAME = 'email'
);
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- avatar
SET @s = (
  SELECT IF(COUNT(*)=0,
    'ALTER TABLE `players` ADD COLUMN `avatar` varchar(255) DEFAULT NULL;',
    'SELECT "avatar exists";'
  )
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'players' AND COLUMN_NAME = 'avatar'
);
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- is_verified
SET @s = (
  SELECT IF(COUNT(*)=0,
    'ALTER TABLE `players` ADD COLUMN `is_verified` tinyint(1) NOT NULL DEFAULT 0;',
    'SELECT "is_verified exists";'
  )
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'players' AND COLUMN_NAME = 'is_verified'
);
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- --------------------------------------------------------

--
-- Structure de la table `scores`
--

DROP TABLE IF EXISTS `scores`;
CREATE TABLE IF NOT EXISTS `scores` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `player_id` int UNSIGNED NOT NULL,
  `pairs` tinyint UNSIGNED NOT NULL COMMENT 'Nombre de paires jouées (3..12)',
  `moves` int UNSIGNED NOT NULL COMMENT 'Nombre de coups effectués',
  `score` double NOT NULL COMMENT 'Score calculé (ex: moves / pairs)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_scores_player` (`player_id`),
  KEY `idx_scores_score` (`score`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `scores`
--
ALTER TABLE `scores`
  ADD CONSTRAINT `fk_scores_player` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
-- The three lines above were commented out because some hosted MySQL/Plesk
-- environments disallow setting server variables to NULL during import,
-- which causes "#1231 - Variable 'character_set_client' can't be set to the value of 'NULL'".
-- If you control the server and prefer to restore these variables, re-enable them.
