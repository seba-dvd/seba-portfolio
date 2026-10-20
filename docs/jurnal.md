# Jurnal de dezvoltare – Proiect PAI

## 21.10.2026 – M0: configurarea mediului (v0.1.0)

### Ce am făcut
- Am creat structura proiectului: `docs/` (enunț, cartă, șablon raport), `sql/` (scriptul bazei de date, mai târziu) și `www/` (codul site-ului).
- Am creat un repository git local (`git init -b main`) și unul privat pe GitHub (`seba-dvd/pai-proiect`), cu `gh repo create`.
- Am scris `.gitignore`, ca fișierele cu parole (`config.php`), setările locale și fișierele temporare Word să nu ajungă în git.
- Am marcat prima versiune cu tag-ul `v0.1.0` și am început un `CHANGELOG.md`.
- Am legat `C:\xampp\htdocs\pai` de `PROIECT\www` printr-un junction (`mklink /J`), ca Apache să servească site-ul la `http://localhost/pai/`.
- Am scris prima pagină PHP (`index.php`), care afișează data și ora serverului și versiunea PHP (8.2.12).

### Ce am învățat
- Apache servește doar fișierele din web root. Tot ce e acolo poate fi cerut din browser, așa că `docs/`, `sql/` și `.git/` stau în afara lui.
- PHP rulează pe server. Browserul primește doar HTML-ul rezultat (verificat cu Ctrl+U: nu apare `<?php`).
- Dacă PHP nu ar rula, serverul ar trimite codul sursă ca text, inclusiv eventualele parole. De aceea parolele vor sta separat, în `config.php`, ignorat de git.
- Dacă `.git` ar fi în web root, oricine ar putea descărca tot istoricul proiectului („exposed .git”).
- Un folder fără `index.php` își afișează conținutul în browser („directory listing”). Va fi dezactivat cu `.htaccess` (`Options -Indexes`).
- Versionare: commit-urile salvează fiecare modificare, iar tag-urile (`v0.1.0`, `v0.2.0` …) marchează etapele importante, după Semantic Versioning.

### Probleme întâlnite
- Comanda `New-Item` merge doar în PowerShell. În cmd am folosit `mklink /J`.
- Am deschis din greșeală `indes.php` în loc de `index.php`. Nu era salvat, așa că am închis tab-ul fără salvare.

### Următorul pas
- M1: site-ul personal static (HTML + CSS), pornind de la CV-ul din laboratorul 2.