<?php
/**
 * Database settings: copy this file to config.php and fill in the password.
 *
 * config.php is in .gitignore, so the real password never reaches GitHub.
 * Both files sit in includes/, which Apache refuses to serve (.htaccess).
 */
return [
    'db_host' => 'localhost',
    'db_user' => 'pai_app',             // the account from sql/utilizator.sql, not root
    'db_pass' => 'SCHIMBA_PAROLA',
    'db_name' => 'pai_proiect',
];
