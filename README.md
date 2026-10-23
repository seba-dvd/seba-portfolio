# Seba. – site personal și aplicație web (proiect PAI)

Site-ul personal al lui **David Sebastian Pilat**, student în anul IV la Telecomunicații
(Universitatea Transilvania din Brașov), realizat pentru disciplina
**Programarea Aplicațiilor Internet (PAI)**, anul universitar 2026–2027.

Site-ul are două fețe: paginile personale (Acasă, Despre, CV, Contact) în tema **mint**,
și paginile tehnice (Proiecte) în tema **cyber**, cu tranziții animate între ele.
Este disponibil în **română și engleză**.

**Versiunea curentă:** v0.3.0 (etapele M0–M2). Istoricul complet: [CHANGELOG.md](CHANGELOG.md).

---

## Ce face acum

| Pagină | Conținut |
|---|---|
| **Acasă** (`index.php`) | Prezentare, fotografie, rezumate din celelalte pagini (bento grid) |
| **Despre** (`despre.php`) | Povestea mea, lucrarea de licență, hobby-uri, competențe, parcurs |
| **Proiecte** (`proiecte.php`) | Proiecte individuale și în echipă; la click, o fereastră cu date live din API-ul GitHub |
| **Proiect** (`proiect.php?id=N`) | Pagina unui proiect: ce face, tehnologii, cod sursă |
| **CV** (`cv.php`) | CV formal; butonul „Descarcă PDF” îl exportă pe o pagină A4 |
| **Contact** (`contact.php`) | Email, GitHub, LinkedIn, locație |
| **404** (`404.php`) | Pagina pentru adrese inexistente |

Plus: comutator **RO | EN** (alegerea se păstrează într-un cookie), buton de schimbare a temei,
tranziții între pagini, design adaptat pentru telefon.

### Ce urmează (cerințele din enunț)

- **M3:** baza de date MySQL cu tabelele `users` și `points`.
- **M4–M5:** înregistrare și autentificare, sesiuni, logout.
- **M6:** autocomplete jQuery pe câmpul de utilizator (laboratorul 9).
- **M7:** harta cu orașele vizitate (marcaje colorate: vizitate / de vizitat).
- **M8–M10:** finisaje, revizuire de securitate, raportul Word.

Planul complet este în [docs/Carta proiectului - PAI.docx](docs/Carta%20proiectului%20-%20PAI.docx).

---

## Tehnologii

- **Server:** Apache și PHP 8.2 (XAMPP 8.2.12), pe Windows
- **Bază de date (din M3):** MariaDB 10.4 (compatibil MySQL)
- **Client:** HTML5, CSS (variabile, Grid, Flexbox, View Transitions), JavaScript fără framework-uri
- **Mai târziu:** jQuery și jQuery UI (autocomplete), o bibliotecă de hărți
- **Versionare:** Git și GitHub (depozit privat), Conventional Commits, Semantic Versioning

---

## Instalare și rulare

### Cerințe
- [XAMPP](https://www.apachefriends.org/) cu PHP 8.2 (testat cu XAMPP 8.2.12)
- Git

### Pași

1. **Clonează proiectul** (Git Bash):
   ```bash
   git clone https://github.com/seba-dvd/pai-proiect.git
   ```

2. **Leagă folderul `www` de XAMPP.** Apache servește doar fișierele din `C:\xampp\htdocs`.
   Un *junction* face ca `htdocs\pai` să arate spre `www\` din proiect, fără copii.
   În **Command Prompt (cmd)**, cu căile tale:
   ```bat
   mklink /J "C:\xampp\htdocs\pai" "C:\cale\spre\pai-proiect\www"
   ```
   Doar `www\` devine accesibil din browser; `docs\`, `sql\`, `teste\` și `.git\` rămân în afara lui.

3. **Pornește Apache** din XAMPP Control Panel.

4. **Deschide** http://localhost/pai/

> Site-ul presupune adresa `/pai/`: `www/.htaccess` folosește `ErrorDocument 404 /pai/404.php`.
> Dacă alegi alt nume pentru junction, modifică și această linie.
> XAMPP are implicit `AllowOverride All` pentru `htdocs`, necesar pentru `.htaccess`.

### Testare

Cu Apache pornit, în Git Bash, din folderul proiectului:
```bash
bash teste/verificare.sh
```
Scriptul face 50 de verificări: sintaxa PHP, cheile dicționarelor, răspunsul fiecărei pagini,
erorile 404, folderele blocate, schimbarea limbii și încercările de atac pe parametri.

---

## Structura proiectului

```
pai-proiect/
├── www/                      ← rădăcina web (legată la C:\xampp\htdocs\pai)
│   ├── index.php, despre.php, proiecte.php, proiect.php, cv.php, contact.php, 404.php
│   ├── .htaccess             ← fără listarea folderelor, pagina 404, fișiere ascunse blocate
│   ├── includes/             ← cod PHP comun, blocat din browser (.htaccess)
│   │   ├── antet.php         ← <head>, meniul, comutatorul de limbă
│   │   ├── subsol.php        ← subsolul și scripturile paginii
│   │   ├── functii.php       ← e() (escapare HTML), tr(), gasesteProiect()
│   │   ├── limba.php         ← alegerea limbii (allowlist + cookie)
│   │   ├── proiecte-date.php ← datele celor 8 proiecte (RO și EN)
│   │   └── lang/ro.php, lang/en.php ← toate textele site-ului
│   ├── css/style.css         ← temele, layout-ul, tranzițiile, stilul de printare
│   ├── js/                   ← tranzitii.js, proiecte.js (fereastra GitHub), cv.js
│   └── img/
├── sql/                      ← scriptul bazei de date (din M3)
├── teste/verificare.sh       ← testele automate
├── docs/                     ← enunțul, carta proiectului, documentația, jurnalul, raportul
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
- **Secrete:** `www/includes/config.php` (parola bazei de date, din M3) este în `.gitignore`.
- **Linkuri externe:** `rel="noopener noreferrer"` pe toate linkurile care se deschid în tab nou.

---

## Documentație

| Fișier | Conținut |
|---|---|
| [docs/documentatie.md](docs/documentatie.md) | Documentația tehnică: arhitectură, componente, securitate, teste |
| [docs/jurnal.md](docs/jurnal.md) | Jurnalul de dezvoltare, pe etape |
| [CHANGELOG.md](CHANGELOG.md) | Ce s-a schimbat în fiecare versiune |
| `docs/Documentatie_Proiect.docx` | Raportul proiectului (Word, șablonul UniTBv) |
| `docs/Carta proiectului - PAI.docx` | Obiective, cerințe, etape, riscuri |
| `docs/Proiect - PAI 2025.pdf` | Enunțul proiectului |

---

## Autor

**David Sebastian Pilat** – [github.com/seba-dvd](https://github.com/seba-dvd)

Proiect universitar. Am folosit Claude (asistent AI) ca tutore: pentru explicații, pentru
o parte din cod scrisă la cererea mea și pentru revizuirea codului. Detaliile sunt în jurnal.
