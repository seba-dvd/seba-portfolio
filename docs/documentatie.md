# Documentație tehnică – site personal PAI

Versiunea documentată: **v0.3.0** (23.10.2026), etapele M0–M2.
Documentul descrie cum este construit site-ul și de ce; servește ca bază pentru raportul Word.

---

## 1. Ce este proiectul

Un site personal cu mai multe pagini (prezentare, proiecte, CV, contact), în română și engleză,
la care se va adăuga o aplicație web cu bază de date MySQL: înregistrare, autentificare,
autocomplete jQuery și o hartă a orașelor vizitate (etapele M3–M7).

---

## 2. Arhitectura: client – server

Proiectul folosește modelul client–server din laboratorul 1:

```
Browser (client)  ──cerere HTTP──►  Apache (XAMPP)  ──►  PHP 8.2  ──►  [MariaDB, din M3]
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

## 8. Securitate

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

---

## 9. Accesibilitate

- HTML semantic: `header`, `nav`, `main`, `section`, `article`, `aside`, `footer`.
- Pagina activă din meniu are `aria-current="page"`; butoanele doar cu iconiță au `aria-label`.
- Cardurile de proiect se deschid și cu tastatura (Enter sau Space); fereastra se închide cu Esc.
- Fiecare imagine are text `alt`; contrastul textului respectă nivelul WCAG AA (4,5:1).
- Animațiile se opresc cu `prefers-reduced-motion`.
- Layout-ul se adaptează la telefon (o coloană sub 760 px).

---

## 10. Testare

### Teste automate
`bash teste/verificare.sh` (Apache pornit). Rezultat pe 23.10.2026: **50 / 50 trecute**.

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

### Teste manuale
- Fiecare pagină verificată în Edge, pe desktop și cu emulatorul de telefon (F12, Ctrl+Shift+M).
- CV-ul exportat ca PDF: o singură pagină A4.
- Consola browserului: fără erori JavaScript pe paginile principale.
- Fereastra de proiect: date live pentru depozitele publice, mesaj pentru cele private.

---

## 11. Limitări și pași următori

- Emailul, profilul LinkedIn, câțiva ani din parcurs, rolul în CodeStorm și progresul pe
  TryHackMe / HackTheBox sunt încă marcate cu `[PARANTEZE]`.
- Datele live din GitHub depind de conexiunea la internet și de limita API-ului
  (60 de cereri pe oră, fără autentificare).
- **M3:** baza de date (`sql/schema.sql`), conexiunea `mysqli` cu prepared statements,
  `includes/config.php` cu datele de conectare (ignorat de git).
- **M4–M7:** înregistrare, autentificare și sesiuni, autocomplete jQuery, harta cu marcaje colorate.
