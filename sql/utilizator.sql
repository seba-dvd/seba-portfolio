-- A dedicated database account for the site, instead of root.
-- If the site is ever attacked through a bug, the attacker gets only these
-- rights: read the two tables and add rows. No DELETE, UPDATE, DROP, or
-- access to other databases.
--
-- 1. Replace SCHIMBA_PAROLA with a long random password (not your own).
-- 2. Run as root:  /c/xampp/mysql/bin/mysql -u root < sql/utilizator.sql
-- 3. Put the same password in www/includes/config.php (ignored by git).
-- Do not commit this file with the real password in it.

CREATE USER IF NOT EXISTS 'pai_app'@'localhost' IDENTIFIED BY 'SCHIMBA_PAROLA';

GRANT SELECT, INSERT ON pai_proiect.* TO 'pai_app'@'localhost';
