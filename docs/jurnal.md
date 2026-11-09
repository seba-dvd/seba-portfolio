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

## 24.10.2026 – Raportul Word și M3: baza de date (v0.4.0)

### Ce am făcut
- Am completat raportul în șablonul facultății (`docs/Documentatie_Proiect.docx`): coperta, cuprinsul, listele de figuri, tabele și coduri, acronimele, capitolele 1–8, bibliografia, rezumatul și abstractul. Am scos paginile specifice proiectului de diplomă (fișa, vizele, anexele, declarația).
- Am creat baza de date `pai_proiect` cu tabelele cerute: `users(ID, user, password)` și `points(ID, lat, long, description)`. Scripturile sunt în `sql/`: `schema.sql`, `date-initiale.sql` (Brașov, Codlea) și `utilizator.sql`.
- Site-ul se conectează cu un cont separat, `pai_app`, care poate doar citi și adăuga rânduri. Parola e generată aleatoriu și stă doar în `www/includes/config.php`, ignorat de git.
- Am scris `www/includes/db.php`, cu `mysqli` procedural ca în laboratorul 3, plus interogări pregătite.
- Am adăugat 16 teste pentru baza de date (`teste/verificare-bd.php`), rulate și de `verificare.sh`: 72 de verificări în total, toate trecute.
- Ca și până acum, Claude m-a ajutat ca tutore și a scris o parte din cod și din raport la cererea mea.

### Ce am învățat
- Tipurile potrivite contează: coordonatele sunt `DECIMAL(9,6)`, nu text ca în laborator, iar `CHECK` respinge o latitudine de 100.
- `long` este cuvânt rezervat în MySQL și trebuie scris între backticks: `` `long` ``.
- `UNIQUE` pe numele de utilizator face ca baza de date să refuze un nume dublat (eroarea 1062), chiar dacă PHP uită să verifice.
- Interogare pregătită: textul SQL cu `?` pleacă separat de valoare, deci `x' OR '1'='1` este doar un nume ciudat, nu o comandă.
- Drepturi minime: dacă cineva ar găsi o greșeală în site, contul `pai_app` nu poate șterge, modifica sau citi alte baze de date (eroarea 1142).
- Testele pot lucra pe baza reală fără să lase urme: tranzacție la început, `ROLLBACK` la final.

### Probleme întâlnite
- La primul import, „Brașov” a apărut ca „BraÈ™ov”. Clientul `mysql` din Windows citește fișierele în `cp850`. Testul automat a prins problema; soluția a fost `SET NAMES utf8mb4` la începutul scripturilor și reimportarea celor două rânduri.

### De completat
- Celelalte orașe vizitate, în `sql/date-initiale.sql`.
- Numele cadrului didactic pe coperta raportului; la deschiderea în Word, „Update fields” pentru cuprins și numerele de pagină.
- Întrebarea pentru profesor rămâne: pot adăuga coloane în `points` (vizitat / de vizitat, fotografii) și pot folosi Leaflet în loc de Google Maps?

### Următorul pas
- M4: formularul de înregistrare, cu `password_hash` și mesaj pentru un nume deja folosit.

## 24.10.2026 – M4 și M5: conturi, autentificare și pagina protejată (v0.5.0)

### Ce am făcut
- Am creat `login.php`, cu cele două formulare din enunț: autentificare și cont nou. Ambele trimit datele prin POST la aceeași pagină, iar câmpul ascuns `actiune` spune care formular a fost trimis.
- Contul nou: verific pe server numele (3–30 caractere: litere, cifre, `.`, `_`, `-`) și parola (8–72 caractere, scrisă de două ori), salvez parola cu `password_hash`, iar pentru un nume ocupat afișez un mesaj clar (eroarea 1062).
- Autentificarea: caut contul cu interogarea pregătită din M3 și verific parola cu `password_verify`.
- Am creat pagina protejată `harta.php`, cu „Bine ai venit, <utilizator>!”, locurile din baza de date și linkul de ieșire. Harta propriu-zisă vine în M7.
- Am adăugat `logout.php`, `includes/sesiune.php` și `includes/auth.php`, plus „Hartă” în meniu.
- Am scris 26 de teste noi, care folosesc formularele reale: 103 verificări în total, toate trecute.
- Am completat raportul cu secțiunile despre conturi și sesiuni.
- Claude a scris o mare parte din cod la cererea mea; am parcurs fiecare fișier și am verificat paginile în browser.

### Ce am învățat
- Hash, nu criptare: din hash-ul bcrypt nu se poate obține parola înapoi. Sarea aleatorie face ca două parole identice să aibă hash-uri diferite.
- bcrypt folosește doar primii 72 de octeți ai parolei, de aceea refuz parolele mai lungi în loc să le tai pe ascuns.
- Același mesaj pentru nume sau parolă greșită, și chiar același timp de răspuns (hash fals pentru nume inexistente), ca nimeni să nu afle ce conturi există.
- Laboratorul 3 folosea `session_id("mainID")`: toți vizitatorii aveau aceeași sesiune. `session_regenerate_id(true)` după autentificare oprește atacul „session fixation”.
- Cookie-ul de sesiune trebuie să fie `HttpOnly` (JavaScript nu îl poate citi) și `SameSite=Lax`.
- CSRF: alt site poate trimite formularul meu din browserul vizitatorului, dar nu poate citi tokenul din pagină. Tokenul e necesar și la ieșire.
- Post/Redirect/Get: după un POST reușit fac redirecționare, ca F5 să nu retrimită formularul.
- `Cache-Control: no-store` face ca butonul Înapoi să nu mai arate pagina protejată după ieșire.

### Probleme întâlnite
- La o sesiune expirată apelam `session_destroy()` și pierdeam tokenul CSRF, deci primul formular trimis după aceea dădea eroare. Acum, la expirare, golesc sesiunea și îi schimb identificatorul, fără să o închid.
- Colația bazei de date nu face diferența între majuscule și minuscule, deci „Seba” și „seba” sunt același cont. E comportamentul dorit, iar acum e acoperit de un test.

### De completat
- Limitarea încercărilor de autentificare (M9).

### Următorul pas
- M6: autocomplete jQuery pe câmpul de utilizator, după laboratorul 9.

## 24.10.2026 – M6: autocomplete jQuery (v0.6.0)

### Ce am făcut
- Pe câmpul de utilizator din formularul de autentificare am pus completare automată cu jQuery UI (`js/autocomplete.js`).
- Am scris `api/utilizatori.php`: primește textul tastat (`?term=`) și întoarce JSON cu cel mult 10 nume care încep cu el.
- jQuery și jQuery UI se încarcă de pe code.jquery.com, cu versiuni fixe și hash SRI, doar pe pagina de autentificare.
- Lista de sugestii folosește culorile site-ului, fără tema jQuery UI.
- Am adăugat 14 teste; testul de sintaxă PHP verifică acum și folderul `api/`. Total: 118 verificări, toate trecute.
- Laboratorul 9 nu a avut loc încă. Am folosit varianta standard din jQuery UI; dacă laboratorul folosește altceva, adaptez.
- De acum fac singur commit-urile; Claude doar modifică fișierele.

### Ce am învățat
- AJAX: pagina cere date de la server fără reîncărcare, iar serverul răspunde cu JSON, nu cu HTML.
- În `LIKE`, `%` înseamnă „orice text” și `_` „orice caracter”. Dacă nu le tratez, `?term=%` ar lista toți utilizatorii.
- `X-Content-Type-Options: nosniff` obligă browserul să trateze răspunsul exact ca JSON.
- SRI (`integrity`): browserul rulează biblioteca de pe CDN doar dacă hash-ul se potrivește.
- Autocomplete-ul arată ce conturi există. E un compromis cerut de temă, pe care îl limitez (2 caractere, 10 rezultate).

### Probleme întâlnite
- Primul test pentru `_` nu verifica de fapt nimic: cele 12 conturi de test ocupau toate cele 10 locuri, deci contul `zzXtest_ac` n-ar fi apărut oricum. L-am refăcut cu un text pe care doar `zzXtest_ac` l-ar putea potrivi dacă `_` ar fi wildcard.

### Următorul pas
- M7: harta cu marcaje. Aștept răspunsul profesorului despre Leaflet și coloanele în plus pentru `points`.

## 24.10.2026 – M7: harta, în mod demo (v0.7.0)

### Ce am făcut
- Pe pagina protejată am pus harta: câte un marcaj verde pentru fiecare loc din tabelul `points`. La hover apare descrierea, iar la click descrierea și coordonatele.
- Am făcut un **mod demo**: harta este desenată cu Leaflet pe hărți OpenStreetMap, fără cheie și fără cont. Este modul implicit.
- Modul Google este pregătit: în `config.php` pun `'harta_furnizor' => 'google'` și cheia. Fără cheie, pagina revine singură la demo; dacă Google refuză cheia, apare un mesaj.
- Lista locurilor a rămas sub hartă.
- Am adăugat 5 teste pentru hartă: 123 de verificări, toate trecute.

### Ce am învățat
- Google Maps cere facturare activată în Google Cloud, chiar și pentru utilizarea gratuită. Abonamentul meu Gemini nu ajută: e un produs separat.
- Datele din PHP ajung în JavaScript ca JSON, într-un `<script type="application/json">`; `JSON_HEX_TAG` transformă `<` în `\u003C`, deci o descriere nu poate închide tag-ul.
- Leaflet pune un șir de caractere în pagină ca HTML. Dacă îi dau un element creat cu `textContent`, descrierea rămâne text.
- `google.maps.Marker` este depreciat; varianta nouă este `AdvancedMarkerElement`, care cere un `mapId`.
- Leaflet folosește z-index mare; `isolation: isolate` pe containerul hărții o ține sub meniu.

### Probleme întâlnite
- Testul pentru JSON eșua deși pagina era corectă. Cauza: în Git Bash, `echo` transformă `\u003C` în `<`. Am trecut funcția de test pe `printf '%s\n'`.
- Modul Google nu l-am putut testa cu o cheie reală; am verificat doar „fără cheie” și „cheie refuzată”.

### De completat
- Restul orașelor vizitate în `sql/date-initiale.sql`.
- Întrebarea pentru profesor: este acceptat modul demo (Leaflet) sau trebuie Google Maps? Pot adăuga coloane în `points` pentru culori și fotografii?

### Următorul pas
- Extinderea hărții după răspunsul profesorului, apoi M8–M9 (finisaje, revizuire de securitate, limita de încercări la autentificare).

## 24.10.2026 – Google Maps cu cheie reală

### Ce am făcut
- Am folosit creditul de probă Google Cloud (300 $, valabil până pe 22.01.2027): am activat Maps JavaScript API și am creat o cheie.
- Am restricționat cheia la `http://localhost/*` și `http://127.0.0.1/*` și doar la Maps JavaScript API, apoi am pus-o în `config.php` (ignorat de git), cu `'harta_furnizor' => 'google'`.
- Harta Google apare pe pagina protejată, cu marcajele verzi.
- Dacă Google refuză cheia (de exemplu după expirarea creditului), pagina încarcă singură harta demo și afișează o notă.

### Ce am învățat
- Cheia unei hărți Google se vede în codul paginii, deci nu poate fi secretă. Protecția vine din restricții: site-urile și API-urile pentru care e valabilă.
- O alertă de buget în Google Cloud anunță din timp orice cost neașteptat.

## 24.10.2026 – Panoul de administrare (v0.8.0)

### Ce am făcut
- Am făcut un panou de administrare (`admin.php`), accesibil din pagina hărții doar pentru administratori.
- Din panou pot adăuga, modifica și șterge locurile de pe hartă. Coordonatele le aleg dând click pe o hartă sau trăgând marcajul.
- Tot din panou văd conturile și le pot șterge, cu excepția celor de administrator.
- Administratorii sunt numele din `config.php` (`'admini' => ['seba']`), ca tabelul `users` să rămână exact cel din enunț.
- Panoul folosește un cont separat de bază de date, `pai_admin` (`sql/admin.sql`). `pai_app` a rămas doar cu `SELECT` și `INSERT`.
- Am adăugat pagina 403 și 29 de teste: 141 de verificări în mod normal, 156 cu un cont de administrator.

### Ce am învățat
- Autorizare față de autentificare: un utilizator autentificat nu are automat voie peste tot. Verific dreptul pe server, la fiecare cerere, inclusiv la POST, nu doar ascunzând butonul.
- Două conturi de bază de date cu drepturi diferite limitează paguba dacă o pagină are o greșeală.
- Un cont șters trebuie deconectat imediat, nu abia când îi expiră sesiunea.
- Un administrator nu trebuie să își poată șterge singur contul din greșeală.

### Probleme întâlnite
- La prima captură de ecran a apărut pagina 403: pagina mea de test se autentificase cu contul obișnuit, pentru că numele de administrator era deja ocupat de la rularea anterioară. Panoul refuzase corect accesul.
- Butoanele din tabel, puse direct într-o celulă cu `display: flex`, strică liniile tabelului; le-am mutat într-un `<div>`.

### De completat
- Creez contul `seba` din pagina de autentificare (sau pun alt nume în `config.php` la `admini`).

## 24.10.2026 – Harta în stil de joc: progres, realizări, tur (v0.9.0)

### Ce am făcut
- Pe pagina hărții am adăugat un panou de joc: nivelul de călător, bara de XP și câteva contoare (locuri, cel mai îndepărtat loc, realizări, XP).
- Am adăugat 6 realizări: Primul pas, Explorator, Călător, Drum lung, Departe de casă, Roza vânturilor. Cele blocate apar estompate, cu „🔒 blocată”.
- Am făcut turul locurilor: butoanele ◀ ▶ de sub hartă (sau săgețile) duc harta de la un loc la altul și arată distanța de casă. Merge cu Leaflet și cu Google.
- Calculele sunt în PHP (`includes/progres.php`) și au teste proprii (`teste/verificare-progres.php`): 163 de verificări, toate trecute.

### Ce am învățat
- Formula haversine: distanța în linie dreaptă pe suprafața Pământului, din latitudine și longitudine.
- Calculele făcute pe server pot fi testate fără browser, iar panoul apare și fără JavaScript.
- `<progress>` este bara de progres nativă din HTML; o pot stiliza, iar cititoarele de ecran o înțeleg.
- O realizare blocată nu se marchează doar prin culoare; are și textul „blocată”.

### Probleme întâlnite
- Două teste au picat din cauza așteptărilor mele, nu a codului: Brașov–București are în linie dreaptă aproximativ 141 km, nu 136, iar București (sud-est) plus Paris (nord-vest) deblochează pe bună dreptate „Roza vânturilor”.
- Un test număra și iconițele realizărilor (`class="realizare-icon"`); acum numără doar elementele `<li>`.

## 27.10.2026 – Design curat (v0.10.0)

### Ce am făcut
- Mi-a plăcut un site de portofoliu foarte curat, care te face curios să afli mai mult. Am păstrat ideea, nu și conținutul lui.
- Pagina de start are acum doar un salut scris literă cu literă, două paragrafe, un link spre Despre și iconițele mele.
- Pagina Despre a primit un card de profil cu fotografia, patru informații rapide, o cronologie cu câte un card pe etapă și carduri spre Proiecte și Hartă.
- Fundalul are o grilă fină, cardurile sunt albe, iar tema cyber a devenit modul întunecat al aceluiași aspect.
- Subsolul are meniul, iconițele și anul. Linkurile de contact stau într-un singur fișier, `includes/contacte.php`.
- 26 de verificări noi: 189 în total, toate trecute.

### Ce am învățat
- O grilă se poate desena doar din CSS, cu două `linear-gradient` repetate.
- Mai puțin conținut pe prima pagină face site-ul să pară mai ordonat și îl face pe vizitator să dea click mai departe.
- Un efect de scriere trebuie să funcționeze și fără JavaScript și să nu deranjeze cititoarele de ecran: textul complet rămâne în HTML.
- Cu `display: flex` și `flex-direction: column` pe `body`, subsolul stă jos și pe paginile scurte.

### Probleme întâlnite
- Între „Seba” și punctul colorat apărea un spațiu: `gap` din flex se aplica și între text și `<span>`. Am renunțat la flex pentru logo.
- Pe pagina 404 subsolul rămânea la mijlocul ecranului; am rezolvat cu coloana flex de pe `body`.
- Fotografia de 247 KB s-ar fi încărcat pe fiecare pagină pentru o iconiță de 40 px; am făcut o variantă de 4 KB.

## 09.11.2026 – Limita de autentificare și generatorul raportului (v0.11.0)

### Ce am făcut
- Am limitat încercările de autentificare: după 5 parole greșite pentru același nume sau 20 de pe aceeași adresă, în 15 minute, formularul răspunde cu HTTP 429.
- Greșelile sunt păstrate în tabelul `incercari_login`, doar ca hash SHA-256 al numelui și al adresei. Contul `pai_app` poate șterge doar din acest tabel.
- Am mutat generatorul raportului Word în depozit (`docs/raport-generator/`). Până acum stătea într-un folder temporar.
- 18 verificări noi: 207 în total, toate trecute.

### Ce am învățat
- Codul HTTP 429 (Too Many Requests) și antetul `Retry-After` spun clientului cât să aștepte.
- Limita trebuie să fie și pe nume, și pe adresă: pe nume protejează un cont, pe adresă oprește pe cineva care încearcă multe nume.
- Adresa reală este `REMOTE_ADDR`; antetul `X-Forwarded-For` poate fi scris de oricine.
- Un hash al numelui și al adresei este suficient pentru numărare și nu păstrează date personale.

### Probleme întâlnite
- Folderul temporar al generatorului raportului fusese deja curățat parțial: lipseau șablonul și imaginile. Le-am recuperat din git (commit-ul `f669ce1`) și din raportul existent, apoi am verificat că raportul generat este identic.
- Testele fac multe autentificări greșite; dacă rulau de două ori în 15 minute, s-ar fi blocat singure. Acum golesc tabelul la început.

