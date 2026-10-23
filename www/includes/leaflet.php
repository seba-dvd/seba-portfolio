<?php
/**
 * Leaflet from unpkg, pinned version with SRI hashes: the browser runs the files
 * only if their hash matches. Used by the map (demo mode and the fallback when
 * Google refuses the key) and by the place picker in the admin panel.
 */
const LEAFLET = [
    'css'      => 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
    'css_hash' => 'sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=',
    'js'       => 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
    'js_hash'  => 'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=',
];
