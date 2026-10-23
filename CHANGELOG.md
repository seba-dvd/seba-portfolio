# Changelog

All notable changes to this project are listed here.
The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and versions follow [Semantic Versioning](https://semver.org/).

## [Unreleased]

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
