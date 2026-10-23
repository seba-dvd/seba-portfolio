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

## 21–23.10.2026 – M1: site-ul personal static (v0.2.0)

### Ce am făcut
- Am construit paginile site-ului: Acasă, Despre, Proiecte, CV și Contact.
- Am creat un sistem de teme cu variabile CSS: tema personală (mint) și tema cyber. Pagina Proiecte folosește tema cyber pe tot `<body>`, iar pe Acasă un singur card este cyber.
- Pe pagina Proiecte am separat proiectele individuale de cele în echipă și am adăugat o fereastră de previzualizare care citește date live din API-ul GitHub.
- CV-ul formal este scris o singură dată, ca pagină web. Butonul „Descarcă PDF” deschide fereastra de printare, iar `@media print` îl transformă într-o singură pagină A4.
- Am adăugat tranziții: între pagini (View Transitions), la intrarea în modul cyber, la apariția cardurilor și la butonul de temă.
- Am folosit Claude (asistent AI) ca tutore: mi-a explicat conceptele, a scris o parte din cod la cererea mea și mi-a revizuit codul. Am verificat fiecare pagină în browser și pe ecran de telefon.

### Ce am învățat
- HTML semantic (`header`, `nav`, `main`, `section`, `article`) și de ce contează pentru accesibilitate.
- CSS Grid (`grid-template-areas`, `repeat(auto-fill, minmax(...))`) și Flexbox.
- Variabilele CSS: o clasă care redefinește variabilele schimbă aspectul a tot ce e în interiorul ei.
- `fetch()` și `async`/`await` pentru date din alte servicii; `textContent` în loc de `innerHTML` pentru date externe (protecție XSS).
- `@media print` și `@page` pentru documente tipărite.
- Un selector prea general (`header`) poate stiliza și elemente nedorite; `body > header` țintește doar bara de sus.

### Probleme întâlnite
- Imaginea `pai.png` nu exista încă; am comentat linia până adaug captura.
- Regulile pentru telefon se aplicau și la printare (pagina A4 e mai îngustă de 760px), așa că le-am limitat la `@media screen`.

### De completat
- Emailul și profilul LinkedIn de pe paginile Contact și CV.
- Anul înființării FCP Steagu, anii de barista, rolul în CodeStorm, progresul pe TryHackMe și HackTheBox, limbile străine.

### Următorul pas
- M2: elemente PHP comune (antet, subsol), site bilingv RO/EN, datele proiectelor într-un array PHP.


## 23.10.2026 – M2: PHP de bază și site bilingv (v0.3.0)

### Ce am făcut
- Am mutat `<head>`, meniul și subsolul în `www/includes/antet.php` și `subsol.php`. Fiecare pagină începe acum cu câteva variabile (`$pagina`, `$temaCyber`) și un `require`.
- Am mutat datele celor 8 proiecte într-un array PHP (`includes/proiecte-date.php`). Pagina Proiecte le afișează cu `foreach`, iar fiecare proiect are pagina lui: `proiect.php?id=N`.
- Am făcut site-ul bilingv: toate textele stau în `includes/lang/ro.php` și `en.php`, cu aceleași chei. Comutatorul RO | EN din meniu păstrează alegerea într-un cookie.
- Am adăugat `.htaccess` (fără listarea folderelor, `includes/` blocat) și o pagină 404 proprie.
- Am scris un script de teste (`teste/verificare.sh`, 50 de verificări), un README și documentația tehnică (`docs/documentatie.md`).
- Ca și la M1, Claude (asistent AI) m-a ajutat cu explicații, cu o parte din cod și cu revizuirea lui; am rulat testele și am verificat paginile în browser.

### Ce am învățat
- `require` și `require_once`: codul comun scris o singură dată.
- `foreach`, `array_filter`, `array_slice` și funcțiile săgeată (`fn`) pentru a genera HTML din date.
- `filter_input(..., FILTER_VALIDATE_INT)`: o adresă poate conține orice, deci orice parametru se validează.
- `htmlspecialchars` (funcția `e()`): textul afișat nu poate deveni cod HTML (protecție XSS).
- *Local File Inclusion*: o valoare din adresă nu se pune niciodată într-o cale de fișier; folosesc o listă permisă.
- Cookie-uri cu `setcookie()`: `HttpOnly`, `SameSite`; redirecționarea 303 după schimbarea limbii (Post/Redirect/Get, aici pentru GET).
- `http_response_code(404)` și `ErrorDocument` din `.htaccess`.
- `json_encode` cu `JSON_HEX_*`, ca textele trimise către JavaScript să nu poată închide tag-ul `<script>`.

### Probleme întâlnite
- Pentru pagina 404 inclusă din `proiect.php`, titlul trebuia ales după ce se încarcă limba; am rezolvat cu `$cheieTitlu`.
- Un `git rm --cached` greșit a scos `sql/.gitkeep` dintr-un commit; commitul nu era încă publicat, așa că l-am corectat cu `git commit --amend`.

### De completat
- Aceleași date ca la M1: email, LinkedIn, anii din parcurs, rolul în CodeStorm, TryHackMe / HackTheBox, limbile străine.

### Următorul pas
- M3: baza de date MySQL (`users`, `points`), `sql/schema.sql`, conexiunea `mysqli` cu prepared statements, după laboratorul 3.
