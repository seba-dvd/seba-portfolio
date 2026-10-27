# Changelog

All notable changes to this project are listed here.
The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and versions follow [Semantic Versioning](https://semver.org/).

## [Unreleased]

## [0.10.0] - 2026-10-27
A cleaner look: a minimal home page that makes visitors curious, and the details one click away.

### Added
- Graph-paper background (a faint 32px CSS grid) with a soft glow; in the cyber theme the grid is neon green
- Home page greeting typed letter by letter (`js/acasa.js`), with the full text in the HTML and a copy for screen readers; no animation with `prefers-reduced-motion`
- About page: a profile card with the photo, four quick facts (location, studies, languages, focus), a timeline with one card per step, and link cards to the projects and the travel map
- A bigger footer: name and tagline, the menu again, social icons, copyright and location; it stays at the bottom on short pages
- `includes/contacte.php`: the contact links in one place, used by the home page, the footer, the contact page and the CV; an empty value is not shown anywhere
- `includes/iconite.php`: small inline SVG icons (GitHub, LinkedIn, email, location, studies, languages, focus)
- A small round avatar next to the logo (`img/avatar.jpg`, 4 KB) and a square About photo (`img/profil-rotund.jpg`)
- 26 checks for the new layout (189 checks in total, 204 with the admin flow)

### Changed
- Calmer colours: an almost-white mint background, white cards with thin borders, teal only for small accents; card headings in the text colour
- The menu sits straight on the background (no bar); tags are small grey chips (the cyber theme keeps its neon pills)
- The cyber theme is now the dark mode of the same layout
- The home page keeps only the greeting, two short paragraphs, one link and the icons; the bento cards moved to the About page

### Removed
- The blob photo, the floating badges and the two buttons from the home page
- The `[EMAIL]` / `[LINKEDIN]` placeholders on the contact page and the CV (they are now empty values in `contacte.php`)

## [0.9.0] - 2026-10-24
The map page feels like a game: travel progress, achievements and a tour of the places.

### Added
- Travel progress on `harta.php`: a level badge, an XP bar and counters (places, farthest place from home, achievements, total XP)
- Six achievements: First step, Explorer, Traveller, Long road, Far from home, Compass rose; locked ones are faded and marked "locked"
- Explore mode: ◀ ▶ under the map (or the arrow keys) fly from place to place, open it, and show "Place 2 of 7", the name and the distance from home; works with Leaflet, Google and the fallback map
- `includes/progres.php`: `distantaKm()` (haversine) and `progresCalatorii()`, computed on the server
- `teste/verificare-progres.php`: 18 checks for distances, achievements, XP and levels; 3 checks for the map page (163 checks in total, 178 with the admin flow)
- Report: section on the travel progress and the tour, with a screenshot

## [0.8.0] - 2026-10-24
An admin panel for the places and the user accounts.

### Added
- `admin.php`: admin panel with statistics, a form to add or edit a place (with a Leaflet map: click or drag the marker to fill in the coordinates), a table of places (edit, delete) and a table of accounts (delete)
- Admins are the usernames in `config.php` → `admini` (the `users` table stays exactly as in the brief)
- `sql/admin.sql`: a second database account, `pai_admin`, used only by the panel
- `includes/db-admin.php`, `includes/leaflet.php`, `js/admin.js`
- `403.php`: an access-denied page in the site's style, also used by Apache (`ErrorDocument 403`)
- "Admin panel" button on the map page, for admins only
- 15 panel tests (with `ADMIN_USER` / `ADMIN_PASS`), 6 always-on access tests, 8 tests for the rights of `pai_admin`

### Changed
- `includes/db.php`: `conectareBd(true)` opens the admin connection; `esteAdmin()`
- `includes/auth.php`: a user deleted by an admin is logged out on the next request

### Security
- The panel answers 403 to everyone who isn't an admin, also for POST requests
- `pai_admin` can change and delete places and delete accounts, but can't create accounts, change passwords or drop tables; `pai_app` is unchanged
- Admin accounts can't be deleted from the panel
- CSRF tokens, server-side validation and delete confirmations on every panel form

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
