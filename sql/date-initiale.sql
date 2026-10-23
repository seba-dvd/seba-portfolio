-- First places for the map. Run after schema.sql:
--     /c/xampp/mysql/bin/mysql -u root < sql/date-initiale.sql
-- The IDs are fixed, so running it again changes nothing (INSERT IGNORE).
-- TODO: add the other cities I have visited.

USE pai_proiect;
SET NAMES utf8mb4;                   -- the Windows client defaults to cp850 and garbles diacritics

INSERT IGNORE INTO points (ID, lat, `long`, description) VALUES
    (1, 45.642500, 25.588600, 'Brașov – acasă, Universitatea Transilvania'),
    (2, 45.696700, 25.446900, 'Codlea – stagiul de practică la Dräxlmaier');
