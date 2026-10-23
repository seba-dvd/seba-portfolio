# Documentație tehnică – site personal PAI

Versiunea documentată: **v0.9.0** (24.10.2026), etapele M0–M7, panoul de administrare și progresul pe hartă.
Documentul descrie cum este construit site-ul și de ce; servește ca bază pentru raportul Word.

---

## 1. Ce este proiectul

Un site personal cu mai multe pagini (prezentare, proiecte, CV, contact), în română și engleză,
la care se va adăuga o aplicație web cu bază de date MySQL: înregistrare, autentificare,
autocomplete jQuery și o hartă a orașelor vizitate (baza de date în M3, conturile în M4–M5, autocomplete în M6, harta în M7).

---

## 2. Arhitectura: client – server

Proiectul folosește modelul client–server din laboratorul 1:

```
Browser (client)  ──cerere HTTP──►  Apache (XAMPP)  ──►  PHP 8.2  ──►  MariaDB 10.4
      ▲                                                     │
      └──────────────── răspuns: HTML + CSS + JS ◄──────────┘
```

- **Clientul** (browserul) cere o pagină și afișează HTML-ul primit. Codul JavaScript
  (tranziții, fereastra de proiect) rulează în browser.
- **Serverul** (Apache) primește cererea. Pentru un fișier `.php`, îl dă interpretorului PHP,
  care execută codul și trimite înapoi **doar HTML-ul rezultat**. Codul PHP nu ajunge niciodată
  în browser (verificabil cu Ctrl+U).
- **Fișierele statice** (CSS, JS, imagini) sunt trimise neschimbate.

### Rădăcina web
Apache servește doar `C:\xampp\htdocs`. Folderul `htdocs\pai` este un *junction* spre `www\` din
proiect, deci în browser este accesibil **doar** `www\`. Documentele, scripturile SQL, testele și
istoricul git (`.git`) stau în afara lui și nu pot fi cerute de nimeni.

---

## 3. Drumul unei cereri: `proiect.php?id=4`

1. Browserul cere `http://localhost/pai/proiect.php?id=4`.
2. `proiect.php` încarcă `includes/functii.php` și `includes/limba.php`.
3. `limba.php` citește cookie-ul `lang` (de exemplu `en`), îl verifică pe lista permisă și încarcă
   dicționarul `includes/lang/en.php` în variabila `$t`.
4. `proiect.php` validează `id` cu `filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ...)`.
   Pentru `abc`, `-1` sau `<script>` rezultatul este `false`, iar pagina răspunde cu 404.
5. Caută proiectul cu `id = 4` în `includes/proiecte-date.php`.
6. Include `includes/antet.php` (head, meniu), afișează proiectul, apoi `includes/subsol.php`.
7. Fiecare text afișat trece prin `e()`, care transformă `<`, `>`, `&`, `"` și `'` în entități HTML.
8. Apache trimite HTML-ul final; browserul încarcă `style.css` și scripturile.

---

## 4. Componentele comune (`www/includes/`)

| Fișier | Rol |
|---|---|
| `antet.php` | `<!DOCTYPE>`, `<head>`, meniul cu pagina activă, comutatorul RO \| EN, butonul de temă. Pagina setează înainte `$pagina`, opțional `$titlu`, `$temaCyber`. |
| `subsol.php` | Subsolul (anul curent cu `date('Y')`), scripturile paginii (`$scripturi`), închiderea documentului. |
| `functii.php` | `e()` – escapare HTML; `tr()` – alege textul în limba curentă dintr-un `['ro' => ..., 'en' => ...]`; `gasesteProiect()`. |
| `limba.php` | Alege limba, setează cookie-ul, încarcă dicționarul în `$t`. Definește constanta `LIMBA`. |
| `db.php` | Conexiunea `mysqli` (o dată pe cerere), `toatePunctele()`, `gasesteUtilizator()` cu interogare pregătită. |
| `sesiune.php` | Pornește sesiunea (cookie securizat), `utilizatorConectat()`, `conecteaza()`, `deconecteaza()`, `tokenCsrf()`, `verificaCsrf()`. |
| `auth.php` | Paza paginilor protejate: fără sesiune → `login.php`; altfel `$utilizator` și `Cache-Control: no-store`. |
| `db-admin.php` | Funcțiile panoului: locuri (adăugare, modificare, ștergere), conturi (listă, ștergere), toate cu `conectareBd(true)` (contul `pai_admin`). |
| `progres.php` | `distantaKm()` (haversine) și `progresCalatorii()`: XP, nivel, realizări, distanțele de acasă. Fără bază de date, deci ușor de testat. |
| `leaflet.php` | Constanta `LEAFLET`: adresele și hash-urile SRI ale Leaflet, folosite de hartă și de panou. |
| `config.php` | Datele de conectare la baza de date. Ignorat de git; modelul este `config.example.php`. |
| `proiecte-date.php` | Un array cu cele 8 proiecte: tip (solo/echipă), texte RO/EN, tehnologii, depozit GitHub. |
| `lang/ro.php`, `lang/en.php` | Toate textele site-ului, cu aceleași chei în ambele fișiere. |

Avantajul: meniul, antetul și subsolul există **o singură dată**. O schimbare în `antet.php`
apare pe toate cele 7 pagini.

**De ce datele proiectelor stau într-un array PHP, nu în baza de date:** enunțul cere exact două
tabele (`users` și `points`), pentru aplicația cu hartă. Proiectele sunt conținut al site-ului,
care se schimbă rar, deci un fișier PHP este suficient și mai simplu.

---

## 5. Paginile

| Pagină | Ce face tehnic |
|---|---|
| `index.php` | Rezumate generate din aceleași date ca paginile complete (`array_slice` pentru primele 4 etape din parcurs, lista proiectelor din `proiecte-date.php`). |
| `despre.php` | Paragrafe, hobby-uri, competențe și parcurs, toate generate cu `foreach` din dicționar. |
| `proiecte.php` | Un singur `foreach` pentru ambele secțiuni (individuale și în echipă), filtrate cu `array_filter`. Fiecare card are `data-repo` și, la nevoie, `data-privat`, citite de `js/proiecte.js`. |
| `proiect.php` | Pagina unui proiect, cu validarea `id`-ului și 404 pentru valori invalide. |
| `cv.php` | CV formal, generat din dicționar. `js/cv.js` deschide fereastra de printare; `@media print` și `@page { size: A4 }` îl transformă într-o singură pagină A4. |
| `contact.php` | Carduri-link (email, GitHub, LinkedIn). |
| `login.php` | Cele două formulare (autentificare, cont nou), trimise prin POST la aceeași pagină; câmpul ascuns `actiune` le deosebește. |
| `api/utilizatori.php` | JSON cu cel mult 10 nume de utilizator care încep cu `?term=`, pentru autocomplete. |
| `harta.php` | Pagina protejată: mesajul de bun venit, harta (`js/harta.js`), lista locurilor din `points`, linkul de ieșire. |
| `logout.php` | Închide sesiunea dacă tokenul din adresă este corect. |
| `admin.php` | Panoul de administrare (doar pentru numele din `config.php` → `admini`). |
| `403.php` | Codul 403 și o pagină în stilul site-ului; folosită de Apache (`ErrorDocument 403`) și de `admin.php`. |
| `404.php` | Trimite codul HTTP 404 (`http_response_code`) și o pagină în stilul site-ului. Folosită de Apache (`ErrorDocument`) și de `proiect.php`. |

---

## 6. Teme și tranziții

### Teme cu variabile CSS
Culorile, fontul și rotunjirea colțurilor sunt variabile CSS definite în `:root` (tema mint).
Clasa `.tema-cyber` redefinește aceleași variabile. Restul regulilor folosesc doar `var(--...)`,
deci o singură clasă schimbă aspectul:
- pe `<body>` → toată pagina (Proiecte, Proiect, 404);
- pe un singur card → doar acel card (cardul de proiecte de pe Acasă).

### Tranziții (`js/tranzitii.js`)
| Efect | Tehnică |
|---|---|
| Trecere lină între pagini | `@view-transition { navigation: auto; }` (View Transitions API) |
| Intrare / ieșire din modul cyber | Evenimentele `pageswap` și `pagereveal` compară tema paginii vechi cu a celei noi și aleg o animație (`clip-path`) |
| Cardurile apar la scroll | `IntersectionObserver` |
| Butonul de temă | `document.startViewTransition()` și un cerc `clip-path: circle()` care crește din buton |

Toate animațiile se opresc dacă utilizatorul a cerut asta în sistemul de operare
(`prefers-reduced-motion`). În browserele fără View Transitions (de exemplu Firefox),
site-ul funcționează identic, fără animații între pagini.

---

## 7. Site bilingv (RO / EN)

1. Linkurile RO | EN din meniu duc la pagina curentă cu `?lang=ro` sau `?lang=en`,
   păstrând ceilalți parametri (`adresaInLimba()`).
2. `limba.php` verifică valoarea pe lista permisă, setează cookie-ul `lang` pentru un an și
   redirecționează (HTTP 303) la aceeași adresă fără `?lang=`. Astfel fiecare pagină are o
   singură adresă, iar limba aleasă rămâne la navigare.
3. Fără parametru, decide cookie-ul; fără cookie, româna.
4. `<html lang="...">` primește limba curentă (pentru cititoarele de ecran și motoarele de căutare).
5. Textele pentru JavaScript (de exemplu „> acces permis_”, mesajele ferestrei GitHub) ajung în
   pagină ca JSON, într-un `<script type="application/json">`, generat cu `json_encode` și
   opțiunile `JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT`.

---

## 8. Baza de date

### Structura (`sql/schema.sql`)

| Tabel | Coloană | Tip | Observații |
|---|---|---|---|
| `users` | `ID` | `INT UNSIGNED` | cheie primară, `AUTO_INCREMENT` |
| `users` | `user` | `VARCHAR(50)` | `NOT NULL`, `UNIQUE` (un nume dublat dă eroarea 1062) |
| `users` | `password` | `VARCHAR(255)` | hash-ul din `password_hash()`, niciodată parola |
| `points` | `ID` | `INT UNSIGNED` | cheie primară, `AUTO_INCREMENT` |
| `points` | `lat` | `DECIMAL(9,6)` | `CHECK` între -90 și 90 |
| `points` | `long` | `DECIMAL(9,6)` | `CHECK` între -180 și 180; cuvânt rezervat, scris mereu `` `long` `` |
| `points` | `description` | `VARCHAR(255)` | textul de lângă marcaj |

Baza folosește `utf8mb4`, ca diacriticele să fie păstrate. Laboratorul 3 memora coordonatele ca
text (`varchar(20)`); aici sunt numere exacte, iar baza de date respinge valorile invalide.

### Scripturile
| Fișier | Ce face |
|---|---|
| `sql/schema.sql` | Creează baza `pai_proiect` și tabelele (`IF NOT EXISTS`, se poate rula din nou) |
| `sql/date-initiale.sql` | Primele locuri pentru hartă (`INSERT IGNORE` cu ID-uri fixe) |
| `sql/utilizator.sql` | Contul `pai_app`, cu `SELECT` și `INSERT` doar pe `pai_proiect` |

Toate încep cu `SET NAMES utf8mb4`: clientul `mysql` din Windows citește implicit fișierele în
`cp850` și strică diacriticele (problemă găsită de testul automat, vezi jurnalul).

### Accesul din PHP (`includes/db.php`)
- `conectareBd()` deschide conexiunea o singură dată pe cerere, cu `mysqli_report` (erorile devin
  excepții) și `mysqli_set_charset(..., 'utf8mb4')`.
- Dacă lipsește `config.php` sau conectarea eșuează: detaliile merg în jurnalul serverului
  (`error_log`), vizitatorul primește un mesaj general și codul 503.
- Orice valoare de la utilizator trece printr-o interogare pregătită:
  `mysqli_prepare` → `mysqli_stmt_bind_param` → `mysqli_stmt_execute`.
  Fără valori de la utilizator (lista punctelor), o interogare simplă este suficientă.

### Conturi și sesiuni (M4, M5)

**Cont nou** (`login.php`, `actiune=inregistrare`):
1. Verifică tokenul CSRF.
2. Validează pe server: nume `^[A-Za-z0-9._-]{3,30}$`, parolă de 8–72 de octeți (bcrypt ignoră
   ce trece de 72), cele două parole identice.
3. `adaugaUtilizator($nume, password_hash($parola, PASSWORD_DEFAULT))`, interogare pregătită.
4. Eroarea 1062 (cheia `UNIQUE`) → „nume deja folosit”. Colația `utf8mb4_unicode_ci` nu face
   diferența între majuscule și minuscule, deci `Seba` și `seba` sunt același nume.
5. `conecteaza()` și redirecționare 303 la `harta.php` (Post/Redirect/Get).

**Autentificare** (`actiune=login`): `gasesteUtilizator()` → `password_verify()`. Pentru un nume
inexistent se verifică un hash fals (`HASH_FALS`), ca timpul de răspuns să fie același. Un singur
mesaj: „Utilizator sau parolă greșită.”

**Sesiunea** (`includes/sesiune.php`): numele cookie-ului `pai_sesiune`, calea `/pai/`,
`HttpOnly`, `SameSite=Lax`, `Secure` pe HTTPS, `session.use_strict_mode`. `conecteaza()` apelează
`session_regenerate_id(true)`. După 30 de minute fără activitate, utilizatorul este uitat. Paginile
site-ului personal nu pornesc sesiunea, deci vizitatorii lor nu primesc cookie.

**Ieșire:** `logout.php?token=…` → `deconecteaza()`: golește `$_SESSION`, expiră cookie-ul,
`session_destroy()`, apoi `login.php?iesire=1`. Fără token valid, nu se întâmplă nimic.

### Completarea automată (M6)

`login.php` încarcă jQuery 3.7.1 și jQuery UI 1.13.2 de pe code.jquery.com, cu `integrity`
(hash SRI) și `crossorigin="anonymous"`; `includes/subsol.php` acceptă pentru asta intrări de forma
`['src' => ..., 'integrity' => ...]`. Doar pagina de autentificare încarcă bibliotecile.

`js/autocomplete.js` activează `autocomplete` pe `#login-user` (`minLength: 2`, `delay: 250`).
jQuery UI trimite `api/utilizatori.php?term=...` și afișează numele cu `.text()`. Lista este
stilizată în `style.css`, fără tema jQuery UI.

`api/utilizatori.php`:
1. Acceptă doar un șir de 2–30 de caractere permise într-un nume (`[A-Za-z0-9._-]`); altfel `[]`.
2. `addcslashes($text, '%_\\')` + `%`: `_` devine caracter obișnuit, `%` final înseamnă „începe cu”.
3. `SELECT user FROM users WHERE user LIKE ? ORDER BY user LIMIT 10` (interogare pregătită).
4. `json_encode` cu `JSON_HEX_*`, antetele `Content-Type: application/json`,
   `X-Content-Type-Options: nosniff`, `Cache-Control: no-store`.

Lista arată ce conturi există (user enumeration): tema o cere; limitele de mai sus o reduc.

### Harta (M7)

Furnizorul se alege în `includes/config.php`:

| `harta_furnizor` | Ce se încarcă | Cheie |
|---|---|---|
| `demo` (implicit) | Leaflet 1.9.4 de pe unpkg (CSS și JS, cu SRI), hărți OpenStreetMap | nu |
| `google` | `maps.googleapis.com/maps/api/js?...&callback=pornesteHartaGoogle` (async, fără SRI: încărcătorul Google se schimbă) | `google_maps_cheie`, cu facturare |

`google` fără cheie → `demo`. Cheie refuzată → `gm_authFailure()` încarcă Leaflet (aceleași hash-uri SRI, trimise în JSON)
și afișează harta demo cu o notă; doar dacă nici Leaflet nu se încarcă apare un mesaj de eroare.

**De ce un mod demo:** API-ul Google Maps JavaScript cere un proiect Google Cloud cu facturare
activată, chiar și în limitele gratuite. Un abonament Gemini nu ajută: este facturat separat.

**Datele:** `harta.php` → `toatePunctele()` → JSON în `<script id="date-harta" type="application/json">`
(`json_encode` cu `JSON_HEX_*`): `{furnizor, puncte: [{lat, lng, descriere}]}`.

**`js/harta.js`:**
- demo: `L.circleMarker` verde pentru fiecare punct, `bindTooltip` (descrierea la hover) și
  `bindPopup` (descriere + coordonate). Ambele primesc **elemente DOM** create cu `textContent`;
  un șir ar fi inserat de Leaflet ca HTML. `fitBounds` pe toate marcajele.
- google: `importLibrary('maps')` și `('marker')`, `AdvancedMarkerElement` + `PinElement` verde
  (`google.maps.Marker` este depreciat), `mapId: 'DEMO_MAP_ID'`, `InfoWindow` cu același conținut DOM.
- dacă Leaflet nu s-a încărcat (CDN indisponibil): mesaj în locul hărții; lista de sub hartă rămâne.

`.harta-mare` are `isolation: isolate`, ca straturile Leaflet (z-index până la 1000) să nu acopere
meniul. `includes/antet.php` acceptă `$stiluri` (CSS de pe CDN cu SRI), `includes/subsol.php`
acceptă scripturi `async` fără SRI.

### Progresul călătoriilor și turul locurilor

Pagina hărții are un panou în stil de joc, calculat pe server din locurile din `points`
(`includes/progres.php`), deci apare și fără JavaScript:

- **Acasă** este locul cu cel mai mic `ID` (primul adăugat); distanțele se calculează de acolo, cu
  formula haversine (raza Pământului 6371 km).
- **XP:** 100 pentru fiecare loc + 50 pentru fiecare realizare deblocată. **Nivel:** ⌊XP / 250⌋ + 1;
  bara de progres (`<progress>`) arată XP-ul din nivelul curent.
- **Realizări:**

| Realizare | Condiție |
|---|---|
| 👣 Primul pas | cel puțin un loc |
| 🧭 Explorator | cel puțin 5 locuri |
| 🎒 Călător | cel puțin 10 locuri |
| 🚗 Drum lung | un loc la peste 100 km de casă |
| ✈️ Departe de casă | un loc la peste 500 km de casă |
| 🌐 Roza vânturilor | locuri la nord, sud, est și vest de casă (fiecare la cel puțin 0,1°) |

  Realizările blocate sunt estompate și au textul „🔒 blocată”, nu doar altă culoare.

**Turul locurilor** (`js/harta.js`, obiectul `explorare`): bara ◀ ▶ de sub hartă trece prin locuri în
ordinea adăugării, ca prin nivelurile unui joc. Harta zboară la loc (`flyTo` în Leaflet, `panTo` în
Google; fără animație cu `prefers-reduced-motion`) și îi deschide fereastra; textul arată „Locul 2 din 7”,
numele și distanța de casă (`distanta_km`, trimisă din PHP în JSON). Merge și cu săgețile stânga / dreapta,
iar un click pe un marcaj continuă turul de acolo. Fiecare furnizor de hărți își dă propria funcție
`mergiLa(i)`, deci turul funcționează în modul demo, în modul Google și în harta de rezervă. Bara este
ascunsă (`hidden`) până o pornește JavaScript.

### Panoul de administrare

**Cine are acces:** numele din `config.php` → `'admini' => ['seba']`, comparate fără diferență de
majuscule (`esteAdmin()`). Lista stă în configurare, nu într-o coloană nouă, ca tabelul `users` să
rămână exact cel din enunț. Ceilalți utilizatori autentificați primesc 403, iar vizitatorii
neautentificați sunt trimiși la `login.php`.

**Două conturi de bază de date:**

| Cont | Drepturi | Folosit de |
|---|---|---|
| `pai_app` | `SELECT`, `INSERT` pe `pai_proiect` | toate paginile publice și aplicația |
| `pai_admin` | `points`: `SELECT`, `INSERT`, `UPDATE`, `DELETE`; `users`: `SELECT`, `DELETE` | doar `admin.php` (`includes/db-admin.php`) |

Astfel, o greșeală în paginile publice tot nu poate șterge nimic, iar panoul nu poate crea conturi și
nu poate schimba parole.

**Ce face:** statistici (locuri, conturi, administratori); formular pentru un loc nou sau pentru
modificarea unuia (`admin.php?loc=ID`), cu o hartă Leaflet: click sau tragerea marcajului completează
coordonatele, iar scrierea lor mută marcajul; tabel cu locurile (modifică / șterge); tabel cu conturile
(șterge, cu excepția administratorilor).

**Reguli:** token CSRF în fiecare formular; confirmare în browser înainte de ștergere (`data-confirmare`,
`js/admin.js`); validare pe server (latitudine ±90, longitudine ±180, virgula acceptată ca separator
zecimal, descriere de 1–255 caractere); după fiecare modificare, redirecționare 303 cu un mesaj păstrat
o singură dată în sesiune. Un cont șters este deconectat la următoarea cerere: `includes/auth.php`
verifică la fiecare pagină protejată dacă numele mai există.

---

## 9. Securitate

| Amenințare | Măsură | Unde |
|---|---|---|
| **XSS** (cod injectat în pagină) | `e()` = `htmlspecialchars(..., ENT_QUOTES \| ENT_SUBSTITUTE, 'UTF-8')` pe orice valoare afișată | toate paginile |
| XSS din date externe | `textContent`, nu `innerHTML`, pentru datele din API-ul GitHub | `js/proiecte.js` |
| XSS în JSON-ul din `<script>` | `json_encode` cu `JSON_HEX_*`: `<` devine `\u003C`, deci nu poate închide tag-ul | `includes/antet.php` |
| **Local File Inclusion** (`?lang=../../fisier`) | Lista permisă `['ro', 'en']`; numele fișierelor sunt fixe în cod | `includes/limba.php` |
| Intrări invalide în adresă | `filter_input(..., FILTER_VALIDATE_INT, min_range 1)`, altfel 404 | `proiect.php` |
| Furtul / folosirea cookie-ului | `HttpOnly`, `SameSite=Lax`, `Secure` pe HTTPS | `includes/limba.php` |
| Listarea fișierelor din foldere | `Options -Indexes` | `www/.htaccess` |
| Accesul direct la codul comun | `Require all denied` | `www/includes/.htaccess` |
| Expunerea `.git`, documentelor, SQL | Rădăcina web este doar `www/` (junction) | structura proiectului |
| Pagina deschisă controlează site-ul (*tabnabbing*) | `rel="noopener noreferrer"` pe linkurile cu `target="_blank"` | toate paginile |
| Parole în depozitul git | `www/includes/config.php` în `.gitignore` | `.gitignore` |
| **SQL injection** | Interogări pregătite pentru orice valoare de la utilizator | `includes/db.php` |
| Drepturi excesive în baza de date | Contul `pai_app`: doar `SELECT`, `INSERT` pe `pai_proiect` | `sql/utilizator.sql` |
| Detalii tehnice în erori | `error_log` + mesaj general, HTTP 503 | `includes/db.php` |
| Date invalide în tabele | `NOT NULL`, `UNIQUE`, `CHECK` pe coordonate | `sql/schema.sql` |
| **Parole furate din baza de date** | `password_hash` (bcrypt, sare aleatorie), `password_verify` | `login.php` |
| Ghicirea numelor de utilizator | Același mesaj și același timp de răspuns pentru nume sau parolă greșită | `login.php` |
| **Session fixation** | `session_regenerate_id(true)` după autentificare; `use_strict_mode` | `includes/sesiune.php` |
| Furtul cookie-ului de sesiune | `HttpOnly`, `SameSite=Lax`, `Secure` pe HTTPS, expirare la 30 de minute | `includes/sesiune.php` |
| **CSRF** | Token aleatoriu în sesiune, `hash_equals`, în formulare și în linkul de ieșire | `login.php`, `logout.php` |
| Listarea tuturor utilizatorilor prin autocomplete | Minimum 2 caractere, „începe cu”, `LIMIT 10`, `%` refuzat, `_` escapat | `api/utilizatori.php` |
| JSON interpretat ca HTML | `application/json`, `nosniff`, `JSON_HEX_*` | `api/utilizatori.php` |
| Bibliotecă modificată pe CDN | Versiuni fixe + `integrity` (SRI) | `login.php`, `includes/subsol.php` |
| XSS prin descrierile de pe hartă | JSON cu `JSON_HEX_*`, apoi elemente DOM cu `textContent` în etichete și ferestre | `harta.php`, `js/harta.js` |
| Cheia Google Maps | În `config.php`; restricționată la adresa site-ului în Google Cloud | `includes/config.php` |
| Acces la panou fără drept | `esteAdmin()` după autentificare; 403 pentru ceilalți, inclusiv pentru cereri POST | `admin.php` |
| Drepturi de modificare în paginile publice | Cont separat `pai_admin`, folosit doar de panou | `sql/admin.sql`, `includes/db-admin.php` |
| Ștergerea propriului cont de administrator | Conturile de administrator nu au buton și sunt refuzate și pe server | `admin.php` |
| Sesiunea unui cont șters | `auth.php` verifică existența contului la fiecare cerere | `includes/auth.php` |
| Pagina protejată în cache / istoric | `Cache-Control: no-store`, redirecționare fără sesiune | `includes/auth.php` |

---

## 10. Accesibilitate

- HTML semantic: `header`, `nav`, `main`, `section`, `article`, `aside`, `footer`.
- Pagina activă din meniu are `aria-current="page"`; butoanele doar cu iconiță au `aria-label`.
- Cardurile de proiect se deschid și cu tastatura (Enter sau Space); fereastra se închide cu Esc.
- Fiecare imagine are text `alt`; contrastul textului respectă nivelul WCAG AA (4,5:1).
- Animațiile se opresc cu `prefers-reduced-motion`.
- Layout-ul se adaptează la telefon (o coloană sub 760 px).

---

## 11. Testare

### Teste automate
`bash teste/verificare.sh` (Apache și MySQL pornite). Rezultat pe 24.10.2026: **163 / 163 trecute**; cu `ADMIN_USER` și `ADMIN_PASS`, **178 / 178**.

| Grup | Ce verifică |
|---|---|
| Sintaxă PHP | `php -l` pe toate fișierele PHP |
| Dicționare | `ro.php` și `en.php` au exact aceleași chei |
| Pagini | Fiecare pagină răspunde cu 200 |
| 404 | Pagini inexistente și `id`-uri invalide (`0`, `-1`, `999`, `abc`, `<script>`, lipsă) |
| 403 | `includes/`, dicționarele, `.htaccess` și folderele fără index |
| Limbă | Redirecționare 303, cookie `HttpOnly` și `SameSite=Lax`, păstrarea `?id=` |
| Atacuri | `?lang=../../...`, `en%00`, `EN`, `fr`, `<script>`, cookie falsificat → rămâne română |
| Escapare | Un `<script>` trimis în adresă nu apare neescapat în pagină |
| Conturi și sesiuni | Prin formularele reale: fără token CSRF, nume `<b>x</b>`, parolă scurtă, parole diferite, cont nou, hash `$2y$` în baza de date, nume dublat (și cu majuscule), cookie `HttpOnly` / `SameSite`, identificator nou după autentificare, `no-store`, ieșire fără și cu token, parolă greșită, nume inexistent, `x' OR '1'='1`. Conturile `zz_test_*` sunt șterse la final. |
| Hartă | Pagina protejată are harta și datele ei, biblioteca (Leaflet cu SRI sau Google); descrierea `<img src=x onerror=alert(1)>` introdusă în baza de date apare escapată în listă, ca `\u003C` în JSON și nicăieri ca etichetă reală |
| Progres (`teste/verificare-progres.php`) | Distanțe (Brașov–Codlea ≈ 12,6 km, Brașov–București ≈ 141 km), fără locuri, 2 / 5 / 10 locuri, „acasă” = cel mai mic `ID`, drum lung, departe de casă, roza vânturilor (4 direcții da, 3 nu), calculul XP și al nivelului |
| Pagina hărții | Nivelul, bara XP, cele 6 realizări, bara de explorare ascunsă până la pornirea JavaScript, `distanta_km` în JSON |
| Panou de administrare | Fără autentificare → `login.php`; utilizator obișnuit → 403 (și pentru POST), fără link în pagină; `403.php`. Cu un cont de administrator: CSRF, latitudine 100, descriere goală, adăugare (virgula ca separator), escapare în panou, modificare, ștergere, contul de administrator nu poate fi șters, ștergerea unui cont îl deconectează |
| Autocomplete | JSON cu `nosniff`, un nume găsit după început (și cu majuscule), maximum 10, nimic pentru o literă, `%%`, `__`, `x' OR '1'='1`, `<script>`, gol sau tablou; `_` nu este wildcard (`zzXtest_ac` nu apare pentru `zz_test_ac`); jQuery UI cu SRI |
| Baza de date (`pai_admin`) | Poate `UPDATE` / `DELETE` pe `points` și `DELETE` pe `users` (în tranzacție anulată); nu poate schimba sau crea conturi, nu poate `DROP`, nu poate citi `mysql.user` |
| Baza de date | `teste/verificare-bd.php`: conectarea ca `pai_app`, `utf8mb4`, coloanele, diacriticele, `x' OR '1'='1` nu găsește pe nimeni, `UNIQUE` (1062), `CHECK` (4025), `pai_app` nu poate `DELETE` / `UPDATE` / `DROP` / citi `mysql.user` (1142) |

Rândurile de test sunt adăugate într-o tranzacție anulată la final (`mysqli_rollback`), deci
baza de date rămâne neschimbată.

### Teste manuale
- Fiecare pagină verificată în Edge, pe desktop și cu emulatorul de telefon (F12, Ctrl+Shift+M).
- CV-ul exportat ca PDF: o singură pagină A4.
- Consola browserului: fără erori JavaScript pe paginile principale.
- Fereastra de proiect: date live pentru depozitele publice, mesaj pentru cele private.

---

## 12. Limitări și pași următori

- Emailul, profilul LinkedIn, câțiva ani din parcurs, rolul în CodeStorm și progresul pe
  TryHackMe / HackTheBox sunt încă marcate cu `[PARANTEZE]`.
- Datele live din GitHub depind de conexiunea la internet și de limita API-ului
  (60 de cereri pe oră, fără autentificare).
- Nu există încă o limită pentru încercările repetate de autentificare (M9).
- Site-ul rulează local pe HTTP; pe HTTPS, cookie-urile primesc automat `Secure`.
- Modul `google` a fost verificat pe 24.10.2026 cu o cheie reală (cont Google Cloud de probă, credit
  valabil până pe 22.01.2027), restricționată la `http://localhost/*`, `http://127.0.0.1/*` și la
  Maps JavaScript API. După expirarea creditului, pagina trece singură pe harta demo.
- Harta are deocamdată doar două locuri (Brașov, Codlea); restul orașelor vizitate se adaugă în
  `sql/date-initiale.sql`.
- Culorile marcajelor (vizitat / de vizitat) și fotografiile au nevoie de coloane în plus în
  `points`; se adaugă în M7, după acordul cadrului didactic.
- **M4–M7:** înregistrare, autentificare și sesiuni, autocomplete jQuery, harta cu marcaje colorate.
