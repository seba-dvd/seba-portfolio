# Changelog

All notable changes to this project are listed here.
The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and versions follow [Semantic Versioning](https://semver.org/).

## [Unreleased]

## [0.7.1] - 2026-10-24
The map keeps working when Google refuses the key.

### Changed
- Google mode verified with a real, restricted key (Google Cloud trial account)
- Google mode: if Google refuses the key (wrong key, billing ended, credit used up), the page loads Leaflet itself and shows the demo map with a short note, instead of an error

## [0.7.0] - 2026-10-24
M7: the map with one marker per place, in demo mode.

### Added
- Map on the protected page (`harta.php`, `js/harta.js`): a green marker for each row of `points`, the description on hover, description and coordinates on click, the view fitted to all markers
- Demo mode (default): Leaflet 1.9.4 and OpenStreetMap, no key or billing needed
- Google mode: `'harta_furnizor' => 'google'` and a key in `config.php` use the Google Maps JavaScript API with `AdvancedMarkerElement`; without a key the page falls back to demo, a refused key shows a message
- `includes/antet.php` accepts CDN stylesheets with SRI; `includes/subsol.php` accepts `async` scripts
- 5 map tests, including a description with HTML stored in the database (123 checks in total)
- Report: section on the map, with a screenshot

### Changed
- `includes/db.php`: `configuratie()` reads `config.php` once for the database and the map settings
- `config.example.php`: the map settings

### Fixed
- Test helper `are()` uses `printf` instead of `echo`, which changed `\u003C` into `<` in Git Bash

### Security
- Map descriptions reach the page only as text (`textContent` elements, never HTML strings given to Leaflet or Google)
- Leaflet is pinned and checked with SRI; the Google key stays in `config.php`, outside git

## [0.6.0] - 2026-10-24
M6: jQuery autocomplete on the login form.

### Added
- `api/utilizatori.php`: up to 10 usernames that start with the typed text, as JSON
- `js/autocomplete.js`: jQuery UI autocomplete on the username field of the login form (from 2 characters, after a short pause in typing)
- jQuery 3.7.1 and jQuery UI 1.13.2 from code.jquery.com, only on the login page
- `includes/subsol.php` accepts CDN scripts with an SRI hash
- Styles for the suggestion list, in both themes, without the jQuery UI theme
- 14 autocomplete tests; the PHP syntax check now covers `api/` too (118 checks in total)
- Report: section on the autocomplete, with a screenshot

### Security
- The autocomplete can't list everyone: at least 2 characters, "starts with" only, at most 10 names, `%` refused and `_` escaped in `LIKE`
- JSON answers with `X-Content-Type-Options: nosniff` and `Cache-Control: no-store`
- CDN libraries are pinned and checked with Subresource Integrity

## [0.5.0] - 2026-10-24
M4 and M5: accounts, login and the protected page.

### Added
- `login.php`: the login and register forms required by the brief, both posting back to the same page
- Register: server-side checks (name of 3–30 letters, digits, `.` `_` `-`; password of 8–72 characters, typed twice), `password_hash`, a clear message for a taken name
- Login with `password_verify`, then a redirect to the protected page
- `harta.php`: the protected page with "Bine ai venit, <user>!", the places from the database and a logout link (the map itself comes in M7)
- `logout.php`: ends the session
- `includes/sesiune.php` (session, login/logout, CSRF tokens) and `includes/auth.php` (guard for protected pages)
- `adaugaUtilizator()` in `includes/db.php`
- "Hartă / Map" in the menu; the map card on the home page links to it
- 26 tests for accounts and sessions, through the real forms (103 checks in total)
- Report: sections on registration, login, sessions and the protected page, with two new figures

### Security
- Passwords stored only as bcrypt hashes; the same message, and the same response time, for a wrong name or a wrong password
- A new session id after login and register (`session_regenerate_id`), strict mode, sessions end after 30 idle minutes
- Session cookie `HttpOnly`, `SameSite=Lax`, `Secure` on HTTPS; only the app pages start a session
- CSRF token in both forms and in the logout link
- The protected page is sent with `Cache-Control: no-store`, so Back can't show it after logout

## [0.4.0] - 2026-10-24
M3: the database.

### Added
- `sql/schema.sql`: database `pai_proiect` with the tables `users(ID, user, password)` and `points(ID, lat, long, description)`, in `utf8mb4`
- `sql/date-initiale.sql`: the first places for the map (Brașov, Codlea)
- `sql/utilizator.sql`: a dedicated account, `pai_app`, for the site
- `www/includes/db.php`: mysqli connection, `toatePunctele()`, `gasesteUtilizator()`
- `www/includes/config.example.php`: model for the local `config.php`
- `teste/verificare-bd.php`: 16 database checks, also run by `verificare.sh` (72 checks in total)
- Project report in the university Word template (`docs/Documentatie_Proiect.docx`): cover page, contents, lists of figures, tables and code, chapters 1–8 (including the database), bibliography, abstracts in Romanian and English

### Changed
- README and technical documentation: installation steps and description of the database

### Removed
- `sql/.gitkeep`: the folder has real files now

### Fixed
- Diacritics were garbled when importing the SQL files with the Windows `mysql` client (cp850); the scripts now start with `SET NAMES utf8mb4`

### Security
- Every visitor value reaches the database through prepared statements
- The site connects as `pai_app` (only SELECT and INSERT on `pai_proiect`), not as root
- The database password lives only in `config.php`, ignored by git; `includes/` is denied by Apache
- Connection errors are logged on the server; visitors see a general message (HTTP 503)
- `CHECK` constraints keep coordinates in range; `UNIQUE` on the user name

## [0.3.0] - 2026-10-23
M2: PHP basics and the bilingual site.

### Added
- Shared header and footer (`www/includes/antet.php`, `subsol.php`); the menu marks the active page
- Bilingual site, Romanian and English: dictionaries in `www/includes/lang/`, a RO | EN switch in the menu, the choice kept in a cookie
- Project data in a PHP array (`www/includes/proiecte-date.php`), shown with `foreach`
- A page per project: `proiect.php?id=N`
- Custom 404 page in the cyber style
- Smoke test script: `bash teste/verificare.sh` (50 checks)
- README and technical documentation (`docs/documentatie.md`)

### Changed
- Home, About, CV and Contact are generated from the dictionaries instead of fixed HTML
- Home shows the real hobbies and the latest timeline steps, from the same data as the About page
- JavaScript texts come from the page (JSON), so the preview window and the transitions are translated too

### Removed
- `docs/texte/despre-en.md`: the English texts now live in `www/includes/lang/en.php`

### Security
- `e()` escapes every printed value (`htmlspecialchars`)
- The language is checked against an allowlist and never builds a file path (no file inclusion)
- `proiect.php?id=` accepts only a positive whole number; anything else answers 404
- The language cookie is `HttpOnly` and `SameSite=Lax` (`Secure` on HTTPS)
- `.htaccess`: no directory listing, `includes/` and hidden files denied

## [0.2.0] - 2026-10-23
M1: the static personal site.

### Added
- First PHP page (www/index.php)
- Theme system with CSS variables: personal (mint) and cyber
- Home page with hero, photo and bento grid
- Projects page in cyber theme: individual and team projects, achievements
- Project preview window with live data from the GitHub API
- About page: story, current work, hobbies, skills and timeline
- Contact page (email and LinkedIn are placeholders for now)
- Formal CV page with PDF export through the browser's print dialog (one A4 page)
- Smooth transitions: page cross-fade, cyber wipe with "acces permis", cards on scroll, theme switch button
- English texts for the about page, saved for M2 (docs/texte/)

### Changed
- Project charter: the site becomes bilingual (RO/EN)
- Projects page: CodeStorm marked as a hackathon project

### Fixed
- Top bar styles no longer leak into other `<header>` elements

## [0.1.0] - 2026-10-21
M0: environment setup.

### Added
- Folder structure: docs/, sql/, www/
- Project brief, project charter and report template in docs/
- .gitignore
