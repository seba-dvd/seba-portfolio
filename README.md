# Seba. – site personal și aplicație web (proiect PAI)

Site-ul personal al lui **David Sebastian Pilat**, student în anul IV la Telecomunicații
(Universitatea Transilvania din Brașov), realizat pentru disciplina
**Programarea Aplicațiilor Internet (PAI)**, anul universitar 2026–2027.

Site-ul are două fețe: paginile personale (Acasă, Despre, CV, Contact) în tema **mint**,
și paginile tehnice (Proiecte) în tema **cyber**, cu tranziții animate între ele.
Este disponibil în **română și engleză**. Aspectul este curat: fundal cu o grilă fină, carduri albe
și multe spații libere; pagina de start este minimală și trimite spre pagina Despre.

**Versiunea curentă:** v0.13.0 (etapele M0–M9: site, aplicație, panou de administrare cu căutare Google Places, progres pe hartă, design curat, securitate, conținut profesional). Istoricul complet: [CHANGELOG.md](CHANGELOG.md).

---

## Ce face acum

| Pagină | Conținut |
|---|---|
| **Acasă** (`index.php`) | Salutul scris literă cu literă, o prezentare scurtă, un link spre Despre, iconițele sociale |
| **Despre** (`despre.php`) | Profil cu fotografie, 4 informații rapide, povestea (inclusiv obiectivele profesionale), lucrarea de licență, interese, competențe, parcursul (cronologie), linkuri spre Proiecte și Hartă |
| **Proiecte** (`proiecte.php`) | Proiecte individuale și în echipă; la click, o fereastră cu date live din API-ul GitHub |
| **Proiect** (`proiect.php?id=N`) | Pagina unui proiect: ce face, tehnologii, cod sursă |
| **CV** (`cv.php`) | CV formal; butonul „Descarcă PDF” îl exportă pe o pagină A4 |
| **Contact** (`contact.php`) | Linkurile completate în `includes/contacte.php` (GitHub, LinkedIn, email), locație |
| **404** (`404.php`) | Pagina pentru adrese inexistente |
| **Autentificare** (`login.php`) | Formularele de autentificare și de cont nou; completare automată (jQuery UI) pe numele de utilizator |
| **Hartă** (`harta.php`) | Pagina protejată: „Bine ai venit, <utilizator>!”, nivelul de călător (XP, realizări), harta cu un marcaj pentru fiecare loc și turul locurilor (◀ ▶), lista locurilor, ieșire |
| **Ieșire** (`logout.php?token=…`) | Închide sesiunea |
| **Administrare** (`admin.php`) | Doar pentru administratori: adaugă, modifică și șterge locurile (cu căutare Google Places sau alegerea punctului pe hartă), șterge conturi, statistici |
| **403** (`403.php`) | Acces interzis (Apache și panoul de administrare) |

Plus: comutator **RO | EN** (alegerea se păstrează într-un cookie), buton de schimbare a temei,
tranziții între pagini, design adaptat pentru telefon.

**Baza de date** (`pai_proiect`, MariaDB): tabelele `users(ID, user, password)` și
`points(ID, lat, long, description)`, accesate din PHP cu `mysqli` și interogări pregătite,
printr-un cont dedicat (`pai_app`) care poate doar citi și adăuga rânduri.

### Ce urmează (cerințele din enunț)

- **M7 (extindere):** culori pentru locurile vizitate / de vizitat și fotografii, după acordul profesorului.
- **M10:** prezentarea proiectului. Etapele M8–M9 (finisaje, revizuire de securitate) sunt încheiate.

Planul complet este în [docs/Carta proiectului - PAI.docx](docs/Carta%20proiectului%20-%20PAI.docx).

---

## Tehnologii

- **Server:** Apache și PHP 8.2 (XAMPP 8.2.12), pe Windows
- **Bază de date:** MariaDB 10.4 (compatibil MySQL), extensia PHP `mysqli`
- **Client:** HTML5, CSS (variabile, Grid, Flexbox, View Transitions), JavaScript fără framework-uri
- **jQuery 3.7.1 și jQuery UI 1.13.2** (de pe code.jquery.com, cu hash SRI) pentru completarea automată
- **Hartă:** Leaflet 1.9.4 cu OpenStreetMap în modul demo; Google Maps JavaScript API opțional (cheie cu facturare)
- **Versionare:** Git și GitHub (depozit privat), Conventional Commits, Semantic Versioning

---

## Instalare și rulare

### Cerințe
- [XAMPP](https://www.apachefriends.org/) cu PHP 8.2 (testat cu XAMPP 8.2.12)
- Git

### Pași

1. **Clonează proiectul** (Git Bash):
   ```bash
   git clone https://github.com/seba-dvd/seba-portfolio.git
   ```

2. **Leagă folderul `www` de XAMPP.** Apache servește doar fișierele din `C:\xampp\htdocs`.
   Un *junction* face ca `htdocs\pai` să arate spre `www\` din proiect, fără copii.
   În **Command Prompt (cmd)**, cu căile tale:
   ```bat
   mklink /J "C:\xampp\htdocs\pai" "C:\cale\spre\seba-portfolio\www"
   ```
   Doar `www\` devine accesibil din browser; `docs\`, `sql\`, `teste\` și `.git\` rămân în afara lui.

3. **Pornește Apache și MySQL** din XAMPP Control Panel.

4. **Creează baza de date** (Git Bash, din folderul proiectului):
   ```bash
   /c/xampp/mysql/bin/mysql -u root < sql/schema.sql
   /c/xampp/mysql/bin/mysql -u root < sql/date-initiale.sql
   ```

5. **Creează contul site-ului.** În `sql/utilizator.sql`, înlocuiește `SCHIMBA_PAROLA` cu o parolă
   lungă, aleatorie, apoi:
   ```bash
   /c/xampp/mysql/bin/mysql -u root < sql/utilizator.sql
   ```
   Pune înapoi `SCHIMBA_PAROLA` în fișier înainte de commit.

   Contul pentru panoul de administrare, la fel, cu altă parolă (`SCHIMBA_PAROLA_ADMIN`):
   ```bash
   /c/xampp/mysql/bin/mysql -u root < sql/admin.sql
   ```

6. **Configurează conexiunea:** copiază `www/includes/config.example.php` în
   `www/includes/config.php` și scrie aceeași parolă (și parola contului `pai_admin` la
   `db_admin_pass`). `config.php` este în `.gitignore`.

   **Administratori:** în `config.php`, `'admini' => ['seba']` conține numele conturilor care au acces
   la `admin.php`. Creează contul din pagina de autentificare, cu unul dintre aceste nume.

   Harta folosește implicit **modul demo** (Leaflet + OpenStreetMap, fără cheie). Pentru Google Maps:
   `'harta_furnizor' => 'google'` și `'google_maps_cheie' => '...'` în `config.php`. Cheia vine
   dintr-un proiect Google Cloud cu facturare activată și trebuie restricționată la adresa site-ului.
   Fără cheie, sau dacă Google refuză cheia (de exemplu după expirarea creditului), pagina afișează
   singură harta demo. Bara de căutare din panoul de administrare are nevoie, în plus, de
   *Places API (New)* activat pentru aceeași cheie.

   **Site-ul public:** pe serverul online, adaugă în `config.php` `'harta_publica' => true` (harta se
   vede fără cont) și `'inregistrare_deschisa' => false` (doar administratorul se autentifică). Pentru
   proiect, lasă-le deoparte: harta rămâne pagina protejată.

7. **Deschide** http://localhost/pai/

> Site-ul presupune adresa `/pai/`: `www/.htaccess` folosește `ErrorDocument 404 /pai/404.php`.
> Dacă alegi alt nume pentru junction, modifică și această linie.
> XAMPP are implicit `AllowOverride All` pentru `htdocs`, necesar pentru `.htaccess`.

### Testare

Cu Apache și MySQL pornite, în Git Bash, din folderul proiectului:
```bash
bash teste/verificare.sh
```
Scriptul face 251 de verificări: sintaxa PHP, cheile dicționarelor, răspunsul fiecărei pagini,
erorile 404, folderele blocate, schimbarea limbii, încercările de atac pe parametri, conturile și
sesiunile (prin formularele reale), harta, completarea automată, baza de date, progresul de pe hartă, aspectul paginilor, limita de autentificare, anteturile de securitate, conținutul și accesibilitatea (structura, constrângerile, drepturile contului
`pai_app`, interogările pregătite). Conturile de test sunt șterse la final (cu `root`).
Verificările bazei de date se pot rula și separat: `php teste/verificare-bd.php`.

Cu un cont de administrator, testele parcurg și panoul de administrare (adăugare, modificare,
ștergere de locuri și conturi, bara de căutare), 269 de verificări în total:
```bash
ADMIN_USER=seba ADMIN_PASS='parola ta' bash teste/verificare.sh
```

---

## Structura proiectului

```
seba-portfolio/
├── www/                      ← rădăcina web (legată la C:\xampp\htdocs\pai)
│   ├── index.php, despre.php, proiecte.php, proiect.php, cv.php, contact.php, 404.php
│   ├── login.php, harta.php, logout.php   ← aplicația: conturi, sesiuni, pagina protejată
│   ├── admin.php, 403.php    ← panoul de administrare, pagina de acces interzis
│   ├── api/utilizatori.php   ← numele de utilizator pentru autocomplete (JSON)
│   ├── .htaccess             ← fără listarea folderelor, pagina 404, fișiere ascunse blocate
│   ├── includes/             ← cod PHP comun, blocat din browser (.htaccess)
│   │   ├── antet.php         ← <head>, meniul, comutatorul de limbă
│   │   ├── subsol.php        ← subsolul și scripturile paginii
│   │   ├── functii.php       ← e() (escapare HTML), tr(), gasesteProiect()
│   │   ├── limba.php         ← alegerea limbii (allowlist + cookie)
│   │   ├── db.php            ← conexiunea mysqli și interogările
│   │   ├── sesiune.php       ← sesiunea, autentificare / ieșire, token CSRF
│   │   ├── auth.php          ← paza paginilor protejate
│   │   ├── db-admin.php      ← interogările panoului (contul pai_admin)
│   │   ├── leaflet.php       ← adresele și hash-urile SRI ale Leaflet
│   │   ├── progres.php       ← XP, nivel, realizări, distanțe (pagina hărții)
│   │   ├── contacte.php      ← linkurile de contact, într-un singur loc (gol = nu apare)
│   │   ├── limitare.php      ← limita de încercări la autentificare
│   │   ├── iconite.php       ← iconițe SVG (GitHub, LinkedIn, email, locație...)
│   │   ├── config.example.php ← model pentru config.php (parola, ignorat de git)
│   │   ├── proiecte-date.php ← datele celor 8 proiecte (RO și EN)
│   │   └── lang/ro.php, lang/en.php ← toate textele site-ului
│   ├── css/style.css         ← temele, layout-ul, tranzițiile, stilul de printare
│   ├── js/                   ← tranzitii.js, acasa.js (salutul scris), proiecte.js (fereastra GitHub), cv.js, autocomplete.js, harta.js, admin.js
│   └── img/                  ← profil.jpeg, avatar.jpg (meniu), profil-rotund.jpg (Despre)
├── sql/                      ← schema.sql, date-initiale.sql, utilizator.sql, admin.sql
├── teste/                    ← verificare.sh (site), verificare-bd.php (baza de date), verificare-progres.php
├── docs/                     ← enunțul, carta proiectului, documentația, jurnalul, raportul
│   └── raport-generator/     ← generează raportul Word (py -3.12 genereaza.py)
├── CHANGELOG.md
└── README.md
```

---

## Securitate

Măsurile deja implementate (detalii în [docs/documentatie.md](docs/documentatie.md)):

- **XSS:** orice valoare afișată trece prin `e()` (`htmlspecialchars`); în JavaScript, datele
  externe (API-ul GitHub) sunt puse cu `textContent`, niciodată cu `innerHTML`.
- **File inclusion:** limba este verificată pe o listă permisă (`ro`, `en`) și nu construiește
  niciodată o cale de fișier.
- **Validarea intrărilor:** `proiect.php?id=` acceptă doar un număr întreg pozitiv
  (`filter_input` + `FILTER_VALIDATE_INT`); orice altceva primește 404.
- **Cookie:** `HttpOnly`, `SameSite=Lax`, `Secure` când site-ul rulează pe HTTPS.
- **Apache:** fără listarea folderelor, `includes/` blocat, fișierele ascunse blocate.
- **Rădăcina web separată:** `.git`, documentele și scripturile SQL nu pot fi cerute din browser.
- **SQL injection:** valorile de la utilizator ajung în baza de date doar prin interogări
  pregătite (`mysqli_prepare` + `mysqli_stmt_bind_param`), niciodată lipite în textul SQL.
- **Drepturi minime:** site-ul folosește contul `pai_app` (doar `SELECT` și `INSERT` pe
  `pai_proiect`, plus `DELETE` pe jurnalul `incercari_login`), nu `root`.
- **Erori:** o eroare de conectare la baza de date ajunge în jurnalul serverului; vizitatorul
  primește doar un mesaj general (HTTP 503).
- **Parole:** salvate doar ca hash bcrypt (`password_hash`), verificate cu `password_verify`;
  același mesaj pentru nume sau parolă greșită.
- **Limita de autentificare:** după 5 parole greșite pentru același nume sau 20 de pe aceeași adresă,
  în 15 minute, formularul răspunde cu HTTP 429. Numele și adresele sunt păstrate doar ca hash SHA-256
  (tabelul `incercari_login`); o autentificare reușită șterge greșelile acelui nume.
- **Sesiuni:** cookie `HttpOnly` și `SameSite=Lax`, identificator nou după autentificare
  (`session_regenerate_id`), expirare după 30 de minute de inactivitate.
- **CSRF:** token aleatoriu în formulare și în linkul de ieșire.
- **Pagina protejată:** fără sesiune → `login.php`; `Cache-Control: no-store`.
- **Autocomplete:** minimum 2 caractere, doar nume care încep cu textul, maximum 10; `%` refuzat și
  `_` escapat în `LIKE`, ca nimeni să nu poată lista toți utilizatorii. Răspuns JSON cu `nosniff`.
- **Biblioteci de pe CDN:** versiuni fixe și hash SRI (`integrity`).
- **Administrare:** doar conturile din `config.php` (`admini`), restul primesc 403; modificările trec
  prin contul de bază de date `pai_admin`, separat de `pai_app`; conturile de administrator nu pot fi
  șterse din panou; un cont șters este deconectat la următoarea cerere.
- **Harta:** descrierile din baza de date ajung în etichete și ferestre doar ca text (`textContent`);
  cheia Google, dacă există, stă în `config.php`.
- **Secrete:** `www/includes/config.php` (parola bazei de date) este în `.gitignore`.
- **Linkuri externe:** `rel="noopener noreferrer"` pe toate linkurile care se deschid în tab nou.
- **Anteturi HTTP** (`www/.htaccess`): `Content-Security-Policy` (scripturi doar din sursele listate,
  fără `unsafe-inline`; site-ul nu are JavaScript inline), `frame-ancestors 'none'` / `X-Frame-Options`,
  `nosniff`, `Referrer-Policy`, `Permissions-Policy`; versiunea PHP nu mai apare (`X-Powered-By`).

---

## Documentație

| Fișier | Conținut |
|---|---|
| [docs/documentatie.md](docs/documentatie.md) | Documentația tehnică: arhitectură, componente, securitate, teste |
| [docs/jurnal.md](docs/jurnal.md) | Jurnalul de dezvoltare, pe etape |
| [CHANGELOG.md](CHANGELOG.md) | Ce s-a schimbat în fiecare versiune |
| `docs/Documentatie_Proiect.docx` | Raportul proiectului (Word, șablonul UniTBv) |
| [docs/raport-generator/](docs/raport-generator/README.md) | Generatorul raportului: textul (`continut.py`), șablonul, capturile |
| `docs/Carta proiectului - PAI.docx` | Obiective, cerințe, etape, riscuri |
| `docs/Proiect - PAI 2025.pdf` | Enunțul proiectului |

---

## Autor

**David Sebastian Pilat** – [github.com/seba-dvd](https://github.com/seba-dvd)

Proiect universitar. Am folosit Claude (asistent AI) ca tutore: pentru explicații, pentru
o parte din cod scrisă la cererea mea și pentru revizuirea codului. Detaliile sunt în jurnal.
