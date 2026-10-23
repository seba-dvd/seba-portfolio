<?php
/**
 * Local settings: copy this file to config.php and fill in the values.
 *
 * config.php is in .gitignore, so the real password and key never reach GitHub.
 * Both files sit in includes/, which Apache refuses to serve (.htaccess).
 */
return [
    // Database
    'db_host' => 'localhost',
    'db_user' => 'pai_app',             // the account from sql/utilizator.sql, not root
    'db_pass' => 'SCHIMBA_PAROLA',
    'db_name' => 'pai_proiect',

    // Map on harta.php:
    //   'demo'   → Leaflet + OpenStreetMap: no key, no billing (default)
    //   'google' → Google Maps JavaScript API: needs a key from a Google Cloud
    //              project with billing enabled. Restrict the key to your site's
    //              address (HTTP referrers) in the Cloud console: it is visible
    //              in the page source, like every Maps JavaScript key.
    'harta_furnizor'    => 'demo',
    'google_maps_cheie' => '',
];
