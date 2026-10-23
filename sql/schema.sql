-- Database for the PAI project: the two tables required by the brief.
-- Run as root (XAMPP), from the project folder, in Git Bash:
--     /c/xampp/mysql/bin/mysql -u root < sql/schema.sql
-- Safe to run again: nothing is created twice and no data is deleted.

CREATE DATABASE IF NOT EXISTS pai_proiect
    CHARACTER SET utf8mb4            -- full Unicode: Romanian diacritics, emoji
    COLLATE utf8mb4_unicode_ci;

USE pai_proiect;
SET NAMES utf8mb4;                   -- the Windows client defaults to cp850 and garbles diacritics

-- Accounts for the login page.
-- password holds the result of password_hash(), never the password itself:
-- bcrypt hashes are 60 characters, VARCHAR(255) leaves room for newer algorithms.
CREATE TABLE IF NOT EXISTS users (
    ID       INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user     VARCHAR(50)  NOT NULL,
    password VARCHAR(255) NOT NULL,
    PRIMARY KEY (ID),
    UNIQUE KEY uq_users_user (user)  -- two accounts can't share a name (error 1062)
) ENGINE=InnoDB;

-- Places shown on the map, one marker per row.
-- DECIMAL(9,6): exact numbers, 6 decimals ≈ 10 cm. The lab stored them as text.
-- `long` is a reserved word in MySQL, so it must always be written in backticks.
CREATE TABLE IF NOT EXISTS points (
    ID          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    lat         DECIMAL(9,6)  NOT NULL,
    `long`      DECIMAL(9,6)  NOT NULL,
    description VARCHAR(255)  NOT NULL,
    PRIMARY KEY (ID),
    CONSTRAINT chk_points_lat  CHECK (lat BETWEEN -90 AND 90),
    CONSTRAINT chk_points_long CHECK (`long` BETWEEN -180 AND 180)
) ENGINE=InnoDB;
