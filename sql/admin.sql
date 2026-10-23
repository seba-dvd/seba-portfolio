-- A second database account, used only by the admin panel (admin.php).
-- pai_app (sql/utilizator.sql) stays as it is: read and add only. The admin
-- pages need more: change and delete places, delete user accounts. Keeping
-- these rights in a separate account means a bug in the public pages can
-- still never delete anything.
--
-- 1. Replace SCHIMBA_PAROLA_ADMIN with a long random password (not your own,
--    and not the one of pai_app).
-- 2. Run as root:  /c/xampp/mysql/bin/mysql -u root < sql/admin.sql
-- 3. Put the same password in www/includes/config.php as db_admin_pass.
-- Do not commit this file with the real password in it.

CREATE USER IF NOT EXISTS 'pai_admin'@'localhost' IDENTIFIED BY 'SCHIMBA_PAROLA_ADMIN';

GRANT SELECT, INSERT, UPDATE, DELETE ON pai_proiect.points TO 'pai_admin'@'localhost';
GRANT SELECT, DELETE ON pai_proiect.users TO 'pai_admin'@'localhost';
