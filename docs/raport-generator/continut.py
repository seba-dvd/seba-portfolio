# Report text (Romanian). genereaza.py turns it into Word paragraphs.

TITLU = 'Site personal bilingv și aplicație web cu autentificare și hartă interactivă'
TITLU_SCURT = 'Site personal bilingv și aplicație web – proiect PAI'
AUTOR = 'David Sebastian PILAT'
AUTOR_PROP = 'David Sebastian Pilat'
PROFESOR = '[Titulatura. NUMELE și prenumele cadrului didactic]'
PROGRAM = 'Telecomunicații, anul IV'
DATA_MODIFICARE = '2026-11-13T12:00:00Z'

# Set to True once M3 (database) is done; chapter 5 changes accordingly.
M3_GATA = True
M5_GATA = True   # M4 (register) and M5 (login, sessions) done
M6_GATA = True   # jQuery autocomplete done
M7_GATA = True   # map with markers (demo mode: Leaflet + OpenStreetMap)
ADMIN_GATA = True  # admin panel
JOC_GATA = True    # travel progress, achievements, explore mode
DESIGN_GATA = True # clean redesign: minimal home, richer About page, footer
LIMITA_GATA = True # limit on repeated login attempts (M9)
ANTETE_GATA = True # security headers (CSP...), keyboard focus, tab icon (M8-M9)
CAUTARE_GATA = True # v0.13.0: Places search, professional content, public-site switch, a11y fixes


def construieste(a):
    p, b, pas, t, cap = a['p'], a['bullet'], a['pas'], a['titlu'], a['capitol']
    cod, tabel, fig, bib, img = a['cod'], a['tabel'], a['figura'], a['bib'], a['img']
    x = []

    # ---------- lists ----------
    x.append(cap('Lista de figuri, tabele și coduri sursă', numerotat=False))
    x.append('<!--LISTE-->')

    x.append(cap('Lista de acronime', numerotat=False))
    for acronim in [
        'AJAX – Asynchronous JavaScript and XML;',
        'API – Application Programming Interface (interfață de programare);',
        'CSS – Cascading Style Sheets;',
        'DOM – Document Object Model;',
        'HTML – HyperText Markup Language;',
        'HTTP – HyperText Transfer Protocol;',
        'JS – JavaScript;',
        'JSON – JavaScript Object Notation;',
        'LFI – Local File Inclusion (includerea unui fișier local prin parametri);',
        'OWASP – Open Worldwide Application Security Project;',
        'PAI – Programarea Aplicațiilor Internet;',
        'PDF – Portable Document Format;',
        'PHP – PHP: Hypertext Preprocessor;',
        'SQL – Structured Query Language;',
        'URL – Uniform Resource Locator (adresă web);',
        'WCAG – Web Content Accessibility Guidelines;',
        'XAMPP – pachet cu Apache, MariaDB, PHP și Perl pentru dezvoltare locală;',
        'XSS – Cross-Site Scripting (injectarea de cod în pagină).',
    ]:
        x.append(p(acronim).replace('<w:ind w:firstLine="709"/>', '<w:ind w:left="709"/>', 1))

    # ---------- 1. Introducere ----------
    x.append(cap('Introducere'))
    x.append(t(2, 'Contextul proiectului'))
    x.append(p('Proiectul a fost realizat pentru disciplina Programarea Aplicațiilor Internet (PAI), în anul universitar '
               '2026–2027. Tema cere un site personal cu mai multe pagini (prezentare, proiecte anterioare, CV), la care '
               'se adaugă o aplicație web cu bază de date MySQL: înregistrare și autentificare, completare automată a '
               'numelui de utilizator cu jQuery și o pagină protejată cu o hartă a orașelor vizitate.'))
    x.append(p('Am ales să construiesc site-ul ca pe o carte de vizită pentru angajatori și colegi: pe lângă CV, '
               'site-ul prezintă obiectivele mele profesionale, interesele, competențele și proiectele, iar paginile tehnice trec într-o temă '
               'vizuală „cyber”, legată de domeniul care mă interesează, securitatea cibernetică.'))

    x.append(t(2, 'Cerințele temei'))
    x.append(p('Din enunțul proiectului rezultă următoarele cerințe obligatorii:'))
    for c in [
        'un site personal cu mai multe pagini: despre mine, proiecte anterioare, CV (PDF sau pagină web);',
        'o bază de date MySQL cu tabelele `users(ID, user, password)` și `points(ID, lat, long, description)`;',
        'o pagină de autentificare cu două formulare, pentru autentificare și pentru înregistrare;',
        'completare automată (autocomplete) cu jQuery pe câmpul de utilizator din formularul de autentificare;',
        'o pagină protejată prin sesiune, care afișează mesajul „Bine ai venit, <utilizator>!”, un link de '
        'deconectare și o hartă cu câte un marcaj pentru fiecare punct din baza de date, etichetat cu descrierea lui;',
        'un raport în format Word, verificări intermediare ale progresului și o prezentare finală în decembrie.',
    ]:
        x.append(b(c))

    x.append(t(2, 'Obiectivele proiectului'))
    x.append(p('Pe lângă cerințele de mai sus, mi-am propus câteva obiective proprii:'))
    for c in [
        '**site bilingv**, în română și engleză, cu alegerea limbii păstrată între vizite;',
        '**două teme vizuale** (mint pentru paginile personale și cyber pentru cele tehnice), cu tranziții animate între ele;',
        '**proiecte cu date reale**, citite live din API-ul GitHub și separate în proiecte individuale și în echipă;',
        '**harta extinsă**: marcaje colorate diferit pentru locurile vizitate și pentru cele pe care vreau să le vizitez, '
        'cu 2–3 fotografii și informații la selectarea unui marcaj;',
        '**securitate de la început**: validarea intrărilor, escaparea ieșirii, interogări pregătite, parole stocate ca hash;',
        '**dezvoltare organizată**: versionare cu Git, etape cu versiuni numerotate, teste automate și un jurnal de dezvoltare.',
    ]:
        x.append(b(c))

    x.append(t(2, 'Structura lucrării'))
    x.append(p('Capitolul 2 prezintă tehnologiile folosite, iar capitolul 3 modul în care este organizată aplicația. '
               'Capitolul 4 descrie implementarea site-ului personal, iar capitolul 5 baza de date și aplicația web. '
               'Capitolul 6 adună măsurile de securitate, capitolul 7 modul de testare, iar capitolul 8 concluziile.'))

    # ---------- 2. Tehnologii ----------
    x.append(cap('Tehnologii utilizate'))
    x.append(t(2, 'Arhitectura client–server'))
    x.append(p('Aplicația folosește modelul client–server studiat în laboratorul 1 [1]. Browserul (clientul) trimite o '
               'cerere HTTP, serverul web o prelucrează și răspunde cu o pagină HTML, împreună cu fișierele CSS, '
               'JavaScript și imaginile necesare. Pentru paginile PHP, codul se execută pe server, iar browserul '
               'primește doar rezultatul; codul sursă PHP nu ajunge niciodată la vizitator. Componentele sunt prezentate '
               'în Figura 1.'))
    x.append(fig(img('arhitectura.png'), 'Arhitectura aplicației: componentele clientului și ale serverului'))

    x.append(t(2, 'Serverul web: Apache și PHP'))
    x.append(p('Mediul de dezvoltare este pachetul XAMPP 8.2.12, care conține serverul web Apache 2.4, interpretorul '
               'PHP 8.2 și serverul de baze de date MariaDB 10.4 [9]. Apache primește cererile și le transmite '
               'interpretorului PHP pentru fișierele `.php`. Regulile de acces sunt scrise în fișiere `.htaccess`.'))
    x.append(p('PHP [2] generează paginile: include componentele comune (antet, subsol), alege limba, validează '
               'parametrii din adresă și, din etapa M3, comunică cu baza de date prin extensia `mysqli`, în stilul '
               'procedural folosit în laboratorul 3.'))

    x.append(t(2, 'Baza de date: MariaDB (MySQL)'))
    x.append(p('MariaDB este o bifurcație compatibilă cu MySQL [3]. Este folosită pentru cele două tabele cerute de temă: '
               'utilizatorii aplicației și punctele afișate pe hartă. Administrarea se face din phpMyAdmin sau din '
               'linia de comandă, iar structura este păstrată ca script SQL în depozitul proiectului.'))

    x.append(t(2, 'Tehnologiile clientului: HTML, CSS și JavaScript'))
    x.append(p('Paginile folosesc HTML5 semantic (`header`, `nav`, `main`, `section`, `article`, `footer`), ceea ce '
               'ajută cititoarele de ecran și motoarele de căutare [4]. Aspectul este scris în CSS, cu variabile pentru '
               'teme, CSS Grid și Flexbox pentru așezarea în pagină și View Transitions pentru animațiile dintre pagini [5].'))
    x.append(p('JavaScript este folosit fără framework-uri, doar acolo unde aduce ceva în plus: tranzițiile, fereastra de '
               'previzualizare a proiectelor (care citește date din API-ul GitHub [8]) și exportul CV-ului. Pentru '
               'completarea automată din etapa M6 se va folosi jQuery și jQuery UI, conform laboratorului 9.'))

    x.append(t(2, 'Instrumente de dezvoltare și versionare'))
    x.append(p('Codul este scris în Visual Studio Code și versionat cu Git, într-un depozit privat pe GitHub. Mesajele '
               'commit-urilor respectă convenția Conventional Commits (`feat:`, `fix:`, `docs:`, `test:`), iar fiecare '
               'etapă încheiată primește o versiune după Semantic Versioning [12] (`v0.1.0`, `v0.2.0`, `v0.3.0`). '
               'Modificările sunt descrise în `CHANGELOG.md`, după formatul Keep a Changelog [11], iar pașii, '
               'problemele și lecțiile învățate sunt notate în jurnalul de dezvoltare (`docs/jurnal.md`).'))
    x.append(p('În dezvoltare am folosit și Claude, un asistent AI, ca tutore: pentru explicarea conceptelor, pentru '
               'revizuirea codului și pentru o parte din cod, scrisă la cererea mea. Fiecare pagină a fost verificată '
               'în browser, iar jurnalul notează pentru fiecare etapă ce am lucrat și ce am învățat.'))

    # ---------- 3. Proiectare ----------
    x.append(cap('Proiectarea aplicației'))
    x.append(t(2, 'Organizarea proiectului'))
    x.append(p('Proiectul are patru foldere principale: `www/` conține site-ul, `sql/` scripturile bazei de date, '
               '`teste/` testele automate, iar `docs/` documentația (Codul 1).'))
    x.append(cod([
        'seba-portfolio/',
        '├── www/                  rădăcina web (legată la C:\\xampp\\htdocs\\pai)',
        '│   ├── index.php, despre.php, proiecte.php, proiect.php,',
        '│   │   cv.php, contact.php, 404.php',
        '│   ├── .htaccess         fără listarea folderelor, pagina 404',
        '│   ├── includes/         cod PHP comun, blocat din browser',
        '│   │   ├── antet.php, subsol.php, functii.php, limba.php',
        '│   │   ├── proiecte-date.php',
        '│   │   └── lang/ro.php, lang/en.php',
        '│   ├── css/style.css',
        '│   ├── js/               tranzitii.js, proiecte.js, cv.js',
        '│   └── img/',
        '├── sql/                  scripturile bazei de date',
        '├── teste/verificare.sh   testele automate',
        '├── docs/                 enunț, cartă, documentație, jurnal, raport',
        '├── CHANGELOG.md',
        '└── README.md',
    ], 'Structura folderelor proiectului'))
    x.append(p('Apache servește doar folderul `C:\\xampp\\htdocs`. În loc să copiez fișierele acolo, am creat un '
               '**junction** (o legătură de director în Windows): `htdocs\\pai` arată spre `www\\` din proiect. Astfel, '
               'doar `www\\` este accesibil din browser, la adresa `http://localhost/pai/`. Documentele, scripturile SQL, '
               'testele și istoricul Git (`.git`) rămân în afara rădăcinii web și nu pot fi descărcate de nimeni.'))

    x.append(t(2, 'Componentele comune'))
    x.append(p('Codul care se repetă pe toate paginile este scris o singură dată, în folderul `includes/`, și inclus cu '
               '`require`. O modificare în meniu apare astfel pe toate cele șapte pagini (Tabelul 1).'))
    x.append(tabel(['Fișier', 'Rol'], [
        ['antet.php', 'Începutul documentului (<head>), meniul cu pagina activă marcată, comutatorul RO | EN și butonul de temă.'],
        ['subsol.php', 'Subsolul cu anul curent, scripturile specifice paginii și închiderea documentului.'],
        ['functii.php', 'e() – escaparea textului pentru HTML; tr() – alegerea textului în limba curentă; gasesteProiect().'],
        ['limba.php', 'Alegerea limbii (din adresă sau cookie), cookie-ul de limbă și încărcarea dicționarului în variabila $t.'],
        ['proiecte-date.php', 'Datele celor 8 proiecte: tip (individual sau echipă), texte RO/EN, tehnologii, depozit GitHub.'],
        ['lang/ro.php, lang/en.php', 'Toate textele site-ului, cu aceleași chei în ambele limbi.'],
    ] + ([
        ['contacte.php', 'Linkurile de contact, într-un singur loc; un câmp gol nu apare pe site.'],
        ['iconite.php', 'Iconițe SVG mici (GitHub, LinkedIn, email, locație...), în culoarea textului.'],
    ] if DESIGN_GATA else []), [28, 72], 'Componentele PHP comune din folderul includes/'))
    x.append(p('Datele proiectelor stau într-un array PHP, nu în baza de date. Enunțul cere exact două tabele, pentru '
               'aplicația cu hartă, iar proiectele sunt conținut al site-ului care se schimbă rar, deci un fișier PHP '
               'este suficient și mai simplu de întreținut.'))

    x.append(t(2, 'Drumul unei cereri HTTP'))
    x.append(p('Pentru a urmări cum colaborează componentele, iată ce se întâmplă la cererea paginii '
               '`proiect.php?id=4`:'))
    for i, c in enumerate([
        'Browserul cere `http://localhost/pai/proiect.php?id=4`.',
        '`proiect.php` încarcă `functii.php` și `limba.php`.',
        '`limba.php` citește cookie-ul `lang`, îl compară cu lista permisă și încarcă dicționarul potrivit în `$t`.',
        'Parametrul `id` este validat cu `filter_input`; pentru `abc`, `-1` sau `<script>` rezultatul este `false`, '
        'iar pagina răspunde cu codul 404.',
        'Proiectul cu `id = 4` este căutat în `proiecte-date.php`.',
        'Se includ antetul, conținutul proiectului și subsolul; fiecare text afișat trece prin `e()`.',
        'Apache trimite HTML-ul rezultat, iar browserul încarcă foaia de stil și scripturile.',
    ], 1):
        x.append(pas(i, c))

    x.append(t(2, 'Planificarea pe etape'))
    x.append(p('Proiectul a fost împărțit în etape mici, fiecare încheiată cu un commit, o intrare în jurnal și, '
               'pentru etapele importante, o versiune nouă (Tabelul 2).'))
    stare_m3 = 'Finalizată (v0.4.0, 24.10.2026)' if M3_GATA else 'În lucru'
    tabel_etape = [
        ['M0', 'Mediul de lucru: XAMPP, junction, Git și GitHub', 'Finalizată (v0.1.0, 21.10.2026)'],
        ['M1', 'Site-ul personal static: pagini, teme, CV, tranziții', 'Finalizată (v0.2.0, 23.10.2026)'],
        ['M2', 'PHP de bază: componente comune, date în array, site bilingv', 'Finalizată (v0.3.0, 23.10.2026)'],
        ['M3', 'Baza de date: tabelele users și points, conexiunea mysqli', stare_m3],
        ['M4', 'Înregistrarea utilizatorilor (password_hash)', 'Finalizată (v0.5.0, 24.10.2026)' if M5_GATA else 'Planificată'],
        ['M5', 'Autentificare, sesiuni, deconectare, pagina protejată', 'Finalizată (v0.5.0, 24.10.2026)' if M5_GATA else 'Planificată'],
        ['M6', 'Autocomplete jQuery pe câmpul de utilizator', 'Finalizată (v0.6.0, 24.10.2026)' if M6_GATA else 'După laboratorul 9'],
        ['M7', 'Harta cu marcaje colorate și fotografii', 'Harta cu marcaje: finalizată în mod demo (v0.7.0, 24.10.2026); '
         'culorile și fotografiile după acordul cadrului didactic' if M7_GATA else 'Planificată'],
        ['Extra', 'Panoul de administrare: locuri și conturi', 'Finalizată (v0.8.0, 24.10.2026)'],
        ['Extra', 'Harta în stil de joc: progres, realizări, turul locurilor', 'Finalizată (v0.9.0, 24.10.2026)'],
        ['Extra', 'Design curat: pagina de start minimală, pagina Despre extinsă, subsol', 'Finalizată (v0.10.0, 27.10.2026)'],
        ['M8–M9', 'Finisaje, revizuire de securitate, testare completă',
         'Finalizată (v0.11.0 – v0.12.0, 11.11.2026)' if ANTETE_GATA else
         'În lucru: limita de încercări la autentificare finalizată (v0.11.0, 09.11.2026)' if LIMITA_GATA else 'Planificată'],
        ['Extra', 'Căutare Google Places în panou, conținut profesional, site public', 'Finalizată (v0.13.0, 13.11.2026)' if CAUTARE_GATA else 'Planificată'],
        ['M10', 'Raportul final și prezentarea', 'Decembrie 2026'],
    ]
    x.append(tabel(['Etapa', 'Conținut', 'Stare'], tabel_etape, [12, 58, 30], 'Etapele proiectului'))

    # ---------- 4. Site-ul personal ----------
    x.append(cap('Implementarea site-ului personal'))
    if DESIGN_GATA:
        x.append(t(2, 'Pagina de start și pagina Despre'))
        x.append(p('Pagina de start (Figura 2, stânga) este intenționat minimală: un salut scris literă cu literă, ca într-un '
                   'terminal, două paragrafe scurte, un singur link, „Explorează parcursul meu →”, și iconițele spre profilurile '
                   'mele. Restul informațiilor sunt la un click distanță, ceea ce face vizitatorul curios să meargă mai departe. '
                   'Textul complet al salutului este deja în HTML; scriptul `js/acasa.js` îl șterge și îl scrie din nou, '
                   'literă cu literă. O copie ascunsă vizual rămâne pentru cititoarele de ecran, iar pentru utilizatorii care '
                   'au cerut mai puține animații textul apare direct.'))
        x.append(p('Pagina Despre (Figura 2, dreapta) adună ce era înainte pe pagina de start: un card de profil cu '
                   'fotografia, patru informații rapide (locație, studii, limbi, focus), povestea și obiectivele profesionale, interesele, competențele, '
                   'parcursul sub forma unei cronologii cu câte un card pentru fiecare etapă și, la final, două carduri-link '
                   'spre proiecte și spre hartă. Linkurile de contact (GitHub, LinkedIn, email) stau într-un singur fișier, '
                   '`includes/contacte.php`, și apar pe pagina de start, în subsol, pe pagina de contact și în CV; '
                   'o valoare necompletată nu apare nicăieri.'))
        x.append(fig(img('acasa.png'), 'Pagina de start (stânga) și începutul paginii Despre (dreapta)'))
    else:
        x.append(t(2, 'Pagina de start'))
        x.append(p('Pagina de start (Figura 2) prezintă pe scurt cine sunt și trimite spre celelalte pagini. Rezumatele '
                   '(povestea, hobby-urile, ultimele etape din parcurs, competențele) sunt generate din aceleași date ca '
                   'paginile complete; de exemplu, primele patru etape din parcurs sunt alese cu `array_slice`. Cardurile '
                   'sunt așezate cu CSS Grid și `grid-template-areas` („bento grid”), iar pe telefon trec pe o singură coloană.'))
        x.append(fig(img('acasa.png'), 'Pagina de start, în tema mint'))

    x.append(t(2, 'Pagina de proiecte și fereastra GitHub'))
    x.append(p('Pagina de proiecte (Figura 3) folosește tema cyber și separă proiectele individuale de cele realizate '
               'în echipă. Ambele secțiuni sunt generate de același `foreach`, cu proiectele filtrate prin '
               '`array_filter` după tip. Un terminal decorativ listează proiectele ca foldere.'))
    x.append(fig(img('proiecte.png'), 'Pagina de proiecte, în tema cyber'))
    x.append(p('La click pe un card se deschide o fereastră (`<dialog>`) cu date live din API-ul GitHub: limbajele de '
               'programare, data ultimei actualizări, numărul de stele și de fork-uri. Datele vin de la un serviciu '
               'extern, deci sunt tratate ca nesigure și sunt puse în pagină doar cu `textContent`, niciodată cu '
               '`innerHTML` (Codul 2). Pentru depozitele private, fereastra afișează un mesaj, fără cerere la API.'))
    x.append(cod([
        '// textContent (not innerHTML) treats data as plain text, so nothing from',
        "// GitHub's API can inject HTML or scripts into the page (no XSS).",
        'function element(tag, text, clasa) {',
        '    const el = document.createElement(tag);',
        '    if (text) el.textContent = text;',
        '    if (clasa) el.className = clasa;',
        '    return el;',
        '}',
    ], 'Crearea sigură a elementelor cu date din API-ul GitHub (js/proiecte.js)'))

    x.append(t(2, 'Pagina unui proiect și pagina 404'))
    x.append(p('Fiecare proiect are o pagină proprie, `proiect.php?id=N` (Figura 4). Parametrul `id` vine din adresă, '
               'deci poate fi modificat de oricine. Funcția `filter_input` acceptă doar un număr întreg pozitiv; orice '
               'altă valoare devine `false`, iar vizitatorul primește pagina 404, cu codul HTTP corect (Codul 3).'))
    x.append(cod([
        "$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);",
        '$proiect = is_int($id) ? gasesteProiect($proiecte, $id) : null;',
        '',
        'if ($proiect === null) {',
        "    require __DIR__ . '/404.php';    // http_response_code(404) + the error page",
        '    exit;',
        '}',
    ], 'Validarea parametrului id (proiect.php)'))
    x.append(fig(img('proiect.png'), 'Pagina unui proiect: proiect.php?id=4'))
    x.append(p('Pagina 404 este folosită și de Apache, pentru orice adresă inexistentă, prin directiva '
               '`ErrorDocument 404` din `.htaccess`.'))

    x.append(t(2, 'CV-ul și exportul PDF'))
    x.append(p('Tema permite un CV în format PDF sau ca pagină web. Am ales să scriu CV-ul o singură dată, ca pagină '
               'web (Figura 5), și să generez PDF-ul din ea: butonul „Descarcă PDF” deschide fereastra de printare a '
               'browserului, iar regulile `@media print` și `@page { size: A4 }` ascund meniul și subsolul și încadrează '
               'CV-ul pe o singură pagină A4. Astfel, există o singură variantă a CV-ului de actualizat.'))
    x.append(fig(img('cv.png'), 'Pagina CV, pregătită pentru exportul ca PDF', 14))

    x.append(t(2, 'Teme vizuale și tranziții'))
    x.append(p('Culorile, fonturile și rotunjirea colțurilor sunt variabile CSS definite în `:root` (tema mint). Clasa '
               '`.tema-cyber` redefinește aceleași variabile, deci o singură clasă schimbă aspectul: pusă pe `<body>`, '
               'schimbă toată pagina; pusă pe un card, doar acel card. Tranzițiile sunt descrise în Tabelul 3.'))
    if DESIGN_GATA:
        x.append(p('Aspectul urmărește un stil curat: fundal aproape alb, cu o grilă fină desenată doar din CSS (două '
                   '`linear-gradient` repetate la 32 px), carduri albe cu margini subțiri și multe spații libere; culoarea '
                   'principală apare doar în detalii (linkuri, puncte, etichete). Tema cyber a devenit modul întunecat al '
                   'aceluiași aspect: aceeași așezare în pagină, cu grila în verde neon. Subsolul repetă meniul și '
                   'iconițele, iar `body` este o coloană flexibilă, deci subsolul stă jos și pe paginile scurte.'))
    x.append(tabel(['Efect', 'Tehnică folosită'], [
        ['Trecere lină între pagini', '@view-transition { navigation: auto; } (View Transitions API)'],
        ['Intrarea și ieșirea din modul cyber', 'Evenimentele pageswap și pagereveal compară tema paginii vechi cu a celei noi și aleg o animație cu clip-path; la intrare apare mesajul „> acces permis_”.'],
        ['Cardurile apar la derulare', 'IntersectionObserver adaugă o clasă când cardul devine vizibil.'],
        ['Butonul de temă', 'document.startViewTransition() și un cerc clip-path: circle() care crește din buton.'],
    ], [32, 68], 'Tranzițiile site-ului'))
    x.append(p('Toate animațiile se opresc dacă utilizatorul a cerut acest lucru din sistemul de operare '
               '(`prefers-reduced-motion`). În browserele fără View Transitions, site-ul funcționează la fel, fără animații.'))

    x.append(t(2, 'Site-ul bilingv'))
    x.append(p('Toate textele site-ului stau în două dicționare PHP, `lang/ro.php` și `lang/en.php`, cu aceleași chei. '
               'Linkurile RO | EN din meniu adaugă `?lang=ro` sau `?lang=en` la pagina curentă. Fișierul `limba.php` '
               'verifică valoarea pe o listă permisă, salvează alegerea într-un cookie pentru un an și redirecționează '
               '(HTTP 303) la aceeași adresă fără parametru, astfel încât fiecare pagină are o singură adresă (Codul 4).'))
    x.append(cod([
        "const LIMBI = ['ro', 'en'];",
        '',
        "$limba = $_COOKIE['lang'] ?? 'ro';",
        'if (!in_array($limba, LIMBI, true)) {',
        "    $limba = 'ro';",
        '}',
        "define('LIMBA', $limba);",
        '',
        '// File names are fixed here, never built from input',
        '$dictionare = [',
        "    'ro' => __DIR__ . '/lang/ro.php',",
        "    'en' => __DIR__ . '/lang/en.php',",
        '];',
        '$t = require $dictionare[LIMBA];',
    ], 'Alegerea limbii pe baza unei liste permise (includes/limba.php)'))
    x.append(p('Valoarea din adresă sau din cookie nu este folosită niciodată pentru a construi calea unui fișier. Astfel, '
               'o încercare de tipul `?lang=../../fisier` nu poate face PHP să încarce alt fișier (Local File Inclusion). '
               'Textele necesare în JavaScript ajung în pagină ca JSON, generat cu `json_encode` și opțiunile `JSON_HEX_*`, '
               'care transformă caracterele `<`, `>`, `&` și ghilimelele în secvențe sigure.'))

    # ---------- 5. Baza de date ----------
    x.append(cap('Baza de date și aplicația web'))
    x.extend(capitol_baza_de_date(a))

    # ---------- 6. Securitate ----------
    x.append(cap('Securitate'))
    x.append(p('Măsurile de securitate au fost introduse odată cu funcțiile pe care le protejează, nu la final. Ele '
               'urmăresc riscurile descrise de OWASP Top 10 [6] (Tabelul 5).'))
    randuri = [
        ['XSS (cod injectat în pagină)', 'e() = htmlspecialchars(..., ENT_QUOTES | ENT_SUBSTITUTE, \'UTF-8\') pe orice valoare afișată [7]', 'toate paginile'],
        ['XSS din date externe', 'textContent în loc de innerHTML pentru datele din API-ul GitHub', 'js/proiecte.js'],
        ['XSS în JSON-ul din <script>', 'json_encode cu JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, JSON_HEX_QUOT', 'includes/antet.php'],
        ['Local File Inclusion', 'Lista permisă [\'ro\', \'en\']; numele fișierelor sunt fixe în cod', 'includes/limba.php'],
        ['Intrări invalide în adresă', 'filter_input cu FILTER_VALIDATE_INT și min_range 1; altfel 404', 'proiect.php'],
        ['Folosirea abuzivă a cookie-ului', 'HttpOnly, SameSite=Lax și Secure când site-ul rulează pe HTTPS', 'includes/limba.php'],
        ['Listarea fișierelor din foldere', 'Options -Indexes', 'www/.htaccess'],
        ['Acces direct la codul comun', 'Require all denied', 'www/includes/.htaccess'],
        ['Expunerea .git, a documentelor și a scripturilor SQL', 'Rădăcina web este doar www/ (junction)', 'structura proiectului'],
        ['Pagina deschisă controlează site-ul (tabnabbing)', 'rel="noopener noreferrer" pe linkurile cu target="_blank"', 'toate paginile'],
        ['Parole în depozitul Git', 'Datele de conectare stau în config.php, trecut în .gitignore', '.gitignore'],
    ]
    if M3_GATA:
        randuri += [
            ['SQL injection', 'Interogări pregătite (mysqli_prepare + mysqli_stmt_bind_param), niciodată concatenare [13]', 'includes/db.php, teste'],
            ['Drepturi excesive în baza de date', 'Utilizator dedicat pai_app, doar SELECT și INSERT pe baza pai_proiect', 'sql/utilizator.sql'],
            ['Detalii tehnice în mesajele de eroare', 'Eroarea de conectare este scrisă în jurnalul serverului; vizitatorul primește doar un mesaj general (HTTP 503)', 'includes/db.php'],
        ]
    if M5_GATA:
        randuri += [
            ['Furtul parolelor din baza de date', 'password_hash (bcrypt, cu sare aleatorie) la înregistrare, password_verify la autentificare [14]', 'login.php'],
            ['Ghicirea numelor de utilizator', 'Un singur mesaj pentru nume sau parolă greșită; password_verify rulează și pentru nume inexistente (timp de răspuns egal)', 'login.php'],
        ] + ([
            ['Ghicirea parolei prin încercări repetate', '5 greșeli pe nume sau 20 pe adresă în 15 minute → HTTP 429; numele și adresele memorate doar ca hash SHA-256', 'includes/limitare.php'],
        ] if LIMITA_GATA else []) + [
            ['Session fixation', 'session_regenerate_id(true) după autentificare și înregistrare; session.use_strict_mode', 'includes/sesiune.php'],
            ['Furtul cookie-ului de sesiune', 'Cookie HttpOnly, SameSite=Lax, Secure pe HTTPS; sesiunea expiră după 30 de minute de inactivitate', 'includes/sesiune.php'],
            ['CSRF (formulare trimise de alt site)', 'Token aleatoriu în sesiune, verificat cu hash_equals la fiecare formular și la deconectare', 'login.php, logout.php'],
            ['Pagina protejată rămasă în istoric', 'Cache-Control: no-store; fără sesiune, redirecționare la login.php', 'includes/auth.php'],
            ['Nume de utilizator periculoase', 'Doar litere, cifre, . _ - (3–30 caractere); afișarea trece oricum prin e()', 'login.php'],
        ]
    if ADMIN_GATA:
        randuri += [
            ['Acces la panoul de administrare fără drept', 'esteAdmin() verificat pe server la fiecare cerere, inclusiv POST; 403 pentru ceilalți', 'admin.php'],
            ['Drepturi de modificare în paginile publice', 'Cont de bază de date separat, pai_admin, folosit doar de panou; pai_app rămâne cu SELECT și INSERT', 'sql/admin.sql, includes/db-admin.php'],
            ['Sesiunea unui cont șters', 'auth.php verifică existența contului la fiecare pagină protejată', 'includes/auth.php'],
        ]
    if M7_GATA:
        randuri += [
            ['XSS prin descrierile de pe hartă', 'Descrierile ajung în JSON cu JSON_HEX_*, apoi în etichete și ferestre doar ca elemente DOM cu textContent (Leaflet ar insera un șir ca HTML)', 'harta.php, js/harta.js'],
            ['Cheia Google Maps', 'Stă în config.php (în afara Git); trebuie restricționată în Google Cloud la adresa site-ului, fiindcă apare în pagină', 'includes/config.php'],
            ['Bibliotecă de hărți modificată pe CDN', 'Leaflet 1.9.4 cu versiune fixă și hash SRI', 'harta.php'],
        ]
    if ANTETE_GATA:
        randuri += [
            ['XSS care ar scăpa totuși de escapare', 'Content-Security-Policy: scripturi doar din sursele listate, fără unsafe-inline și unsafe-eval; site-ul nu are JavaScript inline', 'www/.htaccess'],
            ['Pagini încadrate de alt site (clickjacking)', 'frame-ancestors \'none\' și X-Frame-Options: DENY', 'www/.htaccess'],
            ['Tipuri de fișiere ghicite de browser', 'X-Content-Type-Options: nosniff pe orice răspuns', 'www/.htaccess'],
            ['Versiunea PHP afișată', 'Antetul X-Powered-By este eliminat', 'www/.htaccess'],
        ]
    if CAUTARE_GATA:
        randuri += [
            ['Conturi străine pe site-ul public', 'Cu inregistrare_deschisa = false nu apare formularul, iar un POST trimis direct primește 403', 'login.php'],
            ['Numele administratorului aflat prin autocomplete', 'Pe site-ul public, API-ul întoarce mereu o listă goală', 'api/utilizatori.php'],
        ]
    if M6_GATA:
        randuri += [
            ['Listarea tuturor utilizatorilor prin autocomplete', 'Minimum 2 caractere, doar nume care încep cu textul, maximum 10 rezultate; % refuzat, _ escapat în LIKE', 'api/utilizatori.php'],
            ['Răspunsul JSON interpretat ca altceva', 'Content-Type: application/json, X-Content-Type-Options: nosniff, json_encode cu JSON_HEX_*', 'api/utilizatori.php'],
            ['Bibliotecă modificată pe CDN', 'jQuery și jQuery UI cu versiuni fixe și hash SRI (integrity); browserul refuză un fișier schimbat', 'login.php, includes/subsol.php'],
        ]
    x.append(tabel(['Amenințare', 'Măsură', 'Unde'], randuri, [27, 50, 23], 'Măsurile de securitate implementate'))
    if M5_GATA:
        x.append(p('Rămân de tratat în etapa de revizuire a securității: limitarea numărului de încercări de autentificare '
                   '(protecție împotriva ghicirii parolei prin încercări repetate) și rularea site-ului pe HTTPS, care ar '
                   'activa automat atributul `Secure` al cookie-urilor. Completarea automată expune intenționat numele '
                   'de utilizatori, așa cum cere tema; riscul este limitat prin numărul minim de caractere tastate și '
                   'prin limitarea numărului de rezultate.'))
    else:
        x.append(p('Pentru etapele următoare sunt planificate: stocarea parolelor cu `password_hash` și verificarea lor cu '
                   '`password_verify` [14], regenerarea identificatorului de sesiune după autentificare '
                   '(`session_regenerate_id`), un mesaj de eroare unic pentru utilizator sau parolă greșită și un token '
                   'CSRF pe formulare.'))

    # ---------- 7. Testare ----------
    x.append(cap('Testare'))
    x.append(t(2, 'Teste automate'))
    nr_teste = 'ultima rulare' if M3_GATA else '50 de verificări'
    x.append(p('Scriptul `teste/verificare.sh` rulează în Git Bash, cu Apache pornit, și verifică automat site-ul prin '
               'cereri HTTP făcute cu `curl`. Grupurile de verificări sunt prezentate în Tabelul 6.'))
    grupuri = [
        ['Sintaxă PHP', 'php -l pe toate fișierele PHP'],
        ['Dicționare', 'ro.php și en.php au exact aceleași chei'],
        ['Pagini', 'fiecare pagină răspunde cu codul 200'],
        ['Erori 404', 'pagini inexistente și id-uri invalide: 0, -1, 999, abc, <script>, lipsă'],
        ['Acces interzis (403)', 'includes/, dicționarele, .htaccess și folderele fără index'],
        ['Limbă', 'redirecționare 303, cookie HttpOnly și SameSite=Lax, păstrarea parametrului id'],
        ['Atacuri pe parametri', '?lang=../../..., en%00, EN, fr, <script> și cookie falsificat: pagina rămâne în română'],
        ['Escapare', 'un <script> trimis în adresă nu apare neescapat în pagină'],
    ]
    if M5_GATA:
        grupuri.append(['Conturi și sesiuni', 'înregistrare (token CSRF, nume și parole invalide, nume dublat, inclusiv cu '
                        'majuscule), hash bcrypt în baza de date, cookie HttpOnly și SameSite, identificator nou după '
                        'autentificare, pagină protejată fără cache, deconectare cu și fără token, parolă greșită, nume '
                        "inexistent, x' OR '1'='1"])
    if CAUTARE_GATA:
        grupuri.append(['Conținut și accesibilitate', 'nicio paranteză [...] rămasă pe paginile publice, în română și în engleză; '
                        'titlurile de pe Contact fără salturi de nivel; pe Proiecte, câte un buton real pentru fiecare card și '
                        'niciun role="button"; paginile proiectelor noi; setările implicite pentru site-ul public; bara de căutare '
                        'din panou apare doar cu Google Maps și e ascunsă până se încarcă'])
    if ANTETE_GATA:
        grupuri.append(['Anteturi de securitate', 'pe pagini, pagina 404, CSS și API: Content-Security-Policy fără unsafe-inline / unsafe-eval la scripturi, '
                        'default-src, object-src, base-uri, form-action, frame-ancestors; nosniff o singură dată, X-Frame-Options, '
                        'Referrer-Policy, Permissions-Policy, fără X-Powered-By; iconița paginii'])
    if LIMITA_GATA:
        grupuri.append(['Limita de autentificare', 'două greșeli sunt numărate, o autentificare corectă le șterge; după 5 greșeli, a 6-a încercare '
                        '(chiar cu parola bună) primește 429 cu Retry-After și nu autentifică; un nume inexistent este blocat la fel; '
                        'alt nume de pe aceeași adresă merge până la 20 de greșeli; tabelul conține doar hash-uri de 64 de caractere'])
    if DESIGN_GATA:
        grupuri.append(['Aspect', 'salutul complet în HTML (și în engleză), scriptul de scriere doar pe pagina de start, cele 4 informații '
                        'și cronologia de pe pagina Despre, meniul din subsol pe fiecare pagină, imaginile, nicio paranteză '
                        '[EMAIL] / [LINKEDIN] pe site'])
    if JOC_GATA:
        grupuri.append(['Progresul pe hartă', 'distanțe (Brașov–Codlea ≈ 12,6 km), XP și nivel, fiecare realizare (inclusiv cazuri care nu o '
                        'deblochează), „acasă” = cel mai mic ID; pe pagină: nivelul, bara XP, cele 6 realizări, bara de explorare'])
    if ADMIN_GATA:
        grupuri.append(['Panou de administrare', 'acces refuzat fără autentificare și pentru un utilizator obișnuit (și la POST); cu un cont de '
                        'administrator: CSRF, validări, adăugare, modificare, ștergere de locuri, ștergerea unui cont și '
                        'deconectarea lui, protecția conturilor de administrator; drepturile contului pai_admin'])
    if M7_GATA:
        grupuri.append(['Hartă', 'pagina protejată conține harta și datele ei, biblioteca de hărți (Leaflet cu SRI sau Google Maps); '
                        'o descriere cu cod HTML introdusă în baza de date apare escapată în listă și în JSON (\\u003C) și nicăieri ca etichetă reală'])
    if M6_GATA:
        grupuri.append(['Autocomplete', 'răspuns JSON cu nosniff, găsirea unui nume după început (și cu majuscule), maximum 10 rezultate, '
                        'nimic pentru o literă, %%, __, x\' OR \'1\'=\'1, <script>, parametru gol sau tablou; _ nu este wildcard; '
                        'jQuery UI încărcat cu hash SRI'])
    if M3_GATA:
        grupuri.append(['Baza de date', 'conexiunea cu utilizatorul pai_app, structura tabelelor, constrângerile și interogările pregătite'])
    x.append(tabel(['Grup', 'Ce verifică'], grupuri, [26, 74], 'Grupurile de teste automate'))
    x.append(p(REZULTAT_TESTE))

    x.append(t(2, 'Teste manuale'))
    for c in [
        'fiecare pagină a fost verificată în Microsoft Edge, pe desktop și cu emulatorul de telefon din DevTools '
        '(F12, Ctrl+Shift+M);',
        'CV-ul exportat ca PDF ocupă o singură pagină A4;',
        'consola browserului nu afișează erori JavaScript pe paginile principale;',
        'fereastra de proiect afișează date live pentru depozitele publice și un mesaj pentru cele private;',
        'comutatorul de limbă păstrează pagina curentă și parametrii ei.',
    ]:
        x.append(b(c))

    # ---------- 8. Concluzii ----------
    x.append(cap('Concluzii'))
    x.append(p(CONCLUZII))
    x.append(p('Din punct de vedere tehnic, cele mai utile lecții au fost separarea rădăcinii web de restul proiectului, '
               'scrierea codului comun o singură dată și tratarea oricărei date venite din afară (adresă, cookie, API '
               'extern, formular) ca nesigură până la validare. Testele automate au permis verificarea rapidă a acestor '
               'reguli după fiecare modificare.'))
    x.append(p(PASI_URMATORI))

    # ---------- Bibliografie ----------
    x.append(cap('Bibliografie'))
    for i, ref in enumerate([
        'Universitatea Transilvania din Brașov, *Programarea Aplicațiilor Internet – lucrările de laborator 1–3* '
        '(client–server și XAMPP; HTML și CSS; PHP și MySQL), 2025–2026.',
        'The PHP Group, *PHP Manual*, https://www.php.net/manual/en/, accesat în octombrie 2026.',
        'MariaDB Foundation, *MariaDB Server Documentation*, https://mariadb.com/kb/en/documentation/, accesat în octombrie 2026.',
        'Mozilla, *MDN Web Docs – HTML, CSS și JavaScript*, https://developer.mozilla.org/, accesat în octombrie 2026.',
        'W3C, *CSS View Transitions Module Level 2*, https://www.w3.org/TR/css-view-transitions-2/, accesat în octombrie 2026.',
        'OWASP Foundation, *OWASP Top 10:2021*, https://owasp.org/Top10/, accesat în octombrie 2026.',
        'OWASP Foundation, *Cross Site Scripting Prevention Cheat Sheet*, '
        'https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html, accesat în octombrie 2026.',
        'GitHub, *REST API – Repositories*, https://docs.github.com/en/rest/repos/repos, accesat în octombrie 2026.',
        'The Apache Software Foundation, *Apache HTTP Server Version 2.4 Documentation*, https://httpd.apache.org/docs/2.4/, '
        'accesat în octombrie 2026.',
        'W3C, *Web Content Accessibility Guidelines (WCAG) 2.2*, https://www.w3.org/TR/WCAG22/, accesat în octombrie 2026.',
        'O. Lacan și colaboratorii, *Keep a Changelog 1.1.0*, https://keepachangelog.com/en/1.1.0/, accesat în octombrie 2026.',
        'T. Preston-Werner, *Semantic Versioning 2.0.0*, https://semver.org/, accesat în octombrie 2026.',
        'OWASP Foundation, *SQL Injection Prevention Cheat Sheet*, '
        'https://cheatsheetseries.owasp.org/cheatsheets/SQL_Injection_Prevention_Cheat_Sheet.html, accesat în octombrie 2026.',
        'OWASP Foundation, *Password Storage Cheat Sheet*, '
        'https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html, accesat în octombrie 2026.',
    ], 1):
        x.append(bib(i, ref.replace('*', '')))

    # ---------- Rezumat / Abstract ----------
    x.append(cap('Rezumat', numerotat=False))
    for par in REZUMAT:
        x.append(p(par))
    x.append(cap('Abstract', numerotat=False))
    for par in ABSTRACT:
        x.append(p(par))
    return ''.join(x)


def capitol_baza_de_date(a):
    p, b, t, cod, tabel = a['p'], a['bullet'], a['titlu'], a['cod'], a['tabel']
    x = []
    x.append(t(2, 'Cerințele aplicației'))
    x.append(p('A doua parte a proiectului este o aplicație web cu utilizatori. Un vizitator își creează un cont, se '
               'autentifică (cu completare automată a numelui de utilizator) și ajunge pe o pagină protejată, unde vede '
               'un mesaj de bun venit și o hartă cu orașele vizitate, citite din baza de date.'))
    x.append(t(2, 'Proiectarea bazei de date'))
    x.append(p('Enunțul impune două tabele. Tipurile de date au fost alese astfel (Tabelul 4):'))
    x.append(tabel(['Tabel', 'Coloană', 'Tip', 'Observații'], [
        ['users', 'ID', 'INT UNSIGNED', 'cheie primară, AUTO_INCREMENT'],
        ['users', 'user', 'VARCHAR(50)', 'NOT NULL, UNIQUE: două conturi nu pot avea același nume'],
        ['users', 'password', 'VARCHAR(255)', 'NOT NULL; conține hash-ul parolei (password_hash), niciodată parola'],
        ['points', 'ID', 'INT UNSIGNED', 'cheie primară, AUTO_INCREMENT'],
        ['points', 'lat', 'DECIMAL(9,6)', 'NOT NULL, între -90 și 90 (CHECK); precizie de aproximativ 10 cm'],
        ['points', 'long', 'DECIMAL(9,6)', 'NOT NULL, între -180 și 180 (CHECK); LONG este cuvânt rezervat, se scrie `long`'],
        ['points', 'description', 'VARCHAR(255)', 'NOT NULL; textul afișat lângă marcaj'],
    ], [13, 17, 20, 50], 'Structura tabelelor users și points'))
    x.append(p('Față de exemplul din laboratorul 3, unde coordonatele erau memorate ca text (`varchar(20)`), coordonatele '
               'sunt numere `DECIMAL(9,6)`. Astfel baza de date respinge valorile care nu sunt numere și, prin '
               'constrângerile `CHECK`, pe cele în afara intervalului valid. Tabelele folosesc setul de caractere '
               '`utf8mb4`, ca diacriticele din descrieri să fie păstrate corect.'))
    if M3_GATA:
        x.extend(implementare_m3(a))
    if M5_GATA:
        x.extend(implementare_m4_m5(a))
    if M6_GATA:
        x.extend(implementare_m6(a))
    if M7_GATA:
        x.extend(implementare_m7(a))
    if JOC_GATA:
        x.extend(implementare_joc(a))
    if ADMIN_GATA:
        x.extend(implementare_admin(a))
    else:
        x.append(t(2, 'Conexiunea cu baza de date'))
        x.append(p('Conexiunea se va face cu extensia `mysqli`, în stilul procedural din laboratorul 3, dar cu trei '
                   'diferențe importante: un utilizator dedicat al bazei de date, cu drepturi minime, în locul contului '
                   '`root`; datele de conectare într-un fișier separat (`includes/config.php`), exclus din Git; și '
                   'interogări pregătite pentru orice valoare venită de la utilizator.'))
    x.append(t(2, 'Etapele următoare'))
    urmatoare = [] if M5_GATA else [
        '**M4 – Înregistrare:** formular trimis prin POST, validarea numelui și a parolei, parola salvată cu '
        '`password_hash`, mesaj prietenos pentru un nume deja folosit (eroarea MySQL 1062).',
        '**M5 – Autentificare:** `password_verify`, sesiune cu `session_regenerate_id(true)`, pagina protejată cu '
        'mesajul „Bine ai venit, <utilizator>!” și deconectare cu ștergerea sesiunii.',
    ]
    if not M6_GATA:
        urmatoare.append('**M6 – Autocomplete:** un script PHP care întoarce JSON cu utilizatorii al căror nume începe cu '
                         'textul tastat (interogare pregătită, limită de rezultate), folosit de jQuery UI.')
    for c in urmatoare + [
        ('**M7 – Extinderea hărții:** culori diferite pentru locurile vizitate și pentru cele dorite și 2–3 fotografii '
         'cu informații la selectarea unui marcaj. Este nevoie de coloane noi în `points`, deci aștept acordul cadrului didactic.')
        if M7_GATA else
        ('**M7 – Harta:** câte un marcaj pentru fiecare rând din `points`, cu descrierea afișată ca text sigur; '
         'extinderea cu culori pentru locurile vizitate și dorite și cu fotografii, după acordul cadrului didactic.'),
    ]:
        x.append(b(c))
    return x


def implementare_joc(a):
    p, b, t, cod, fig, img, tabel = a['p'], a['bullet'], a['titlu'], a['cod'], a['figura'], a['img'], a['tabel']
    x = []
    x.append(t(2, 'Progresul călătoriilor și turul locurilor'))
    x.append(p('Ca pagina hărții să semene cu un joc, deasupra hărții apare un panou de progres: nivelul de călător, '
               'o bară de experiență (XP) și realizări care se deblochează pe măsură ce pe hartă apar locuri noi '
               '(Figura 12). Fiecare loc aduce 100 XP, fiecare realizare încă 50 XP, iar un nivel are 250 XP. '
               '„Acasă” este primul loc adăugat (cel cu cel mai mic ID), iar distanțele se calculează de acolo, în linie '
               'dreaptă, cu formula haversine (Codul 12).'))
    x.append(cod([
        'function distantaKm(float $lat1, float $lng1, float $lat2, float $lng2): float',
        '{',
        '    $dLat = deg2rad($lat2 - $lat1);',
        '    $dLng = deg2rad($lng2 - $lng1);',
        '    $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;',
        '    return 6371 * 2 * atan2(sqrt($a), sqrt(1 - $a));     // Earth radius: 6371 km',
        '}',
    ], 'Distanța dintre două locuri (includes/progres.php)'))
    x.append(p('Realizările sunt: Primul pas (un loc), Explorator (5 locuri), Călător (10 locuri), Drum lung (un loc la '
               'peste 100 km de casă), Departe de casă (peste 500 km) și Roza vânturilor (locuri la nord, sud, est și vest '
               'de casă). Cele blocate sunt estompate și au textul „blocată”, ca starea să nu fie transmisă doar prin '
               'culoare. Totul se calculează în PHP, deci panoul apare și fără JavaScript, iar regulile au teste '
               'proprii, care nu au nevoie de server sau de baza de date.'))
    x.append(fig(img('joc.png'), 'Pagina hărții: nivelul, realizările și turul locurilor (aici, al doilea loc)'))
    x.append(p('Sub hartă, bara de explorare parcurge locurile în ordinea adăugării, ca nivelurile unui joc: butoanele '
               '◀ ▶ sau săgețile de pe tastatură duc harta la locul următor, îi deschid fereastra și afișează „Locul 2 din '
               '2”, numele și distanța de casă. Pentru utilizatorii care au cerut mai puține animații '
               '(`prefers-reduced-motion`), harta sare direct la loc. Fiecare furnizor de hărți (Leaflet sau Google) își '
               'dă propria funcție de deplasare, deci turul funcționează la fel în toate modurile.'))
    return x


def implementare_admin(a):
    p, b, t, cod, fig, img = a['p'], a['bullet'], a['titlu'], a['cod'], a['figura'], a['img']
    x = []
    x.append(t(2, 'Panoul de administrare'))
    x.append(p('Pe lângă cerințele temei, aplicația are un panou de administrare (`admin.php`), din care locurile de pe '
               'hartă și conturile pot fi gestionate fără phpMyAdmin (Figura 11). Panoul conține:'))
    for c in [
        'statistici: numărul de locuri, de conturi și de administratori;',
        'un formular pentru un loc nou sau pentru modificarea unuia existent, cu o hartă Leaflet: un click pe hartă sau '
        'tragerea marcajului completează coordonatele, iar scrierea coordonatelor mută marcajul;',
        'tabelul locurilor, cu butoane de modificare și ștergere;',
        'tabelul conturilor, cu buton de ștergere pentru conturile obișnuite.',
    ]:
        x.append(b(c))
    x.append(fig(img('admin.png'), 'Panoul de administrare, cu bara de căutare Google Places deasupra coordonatelor', 12))
    x.append(p('Administratorii sunt numele de utilizator trecute în `config.php` (`admini`). Am ales configurarea în '
               'locul unei coloane noi, ca tabelul `users` să rămână exact cel din enunț. Verificarea se face pe server, '
               'la fiecare cerere, inclusiv la trimiterea formularelor: un utilizator obișnuit care trimite direct o '
               'cerere de ștergere primește codul 403, chiar dacă butonul nu îi este afișat.'))
    x.append(p('Modificările trec printr-un al doilea cont de bază de date, `pai_admin`, folosit doar de panou. El poate '
               'modifica și șterge locuri și poate șterge conturi, dar nu poate crea conturi, nu poate schimba parole și nu '
               'poate șterge tabele. Contul `pai_app`, folosit de restul site-ului, a rămas doar cu `SELECT` și `INSERT`. '
               'Conturile de administrator nu pot fi șterse din panou, iar un cont șters este deconectat la următoarea '
               'cerere, deoarece `includes/auth.php` verifică la fiecare pagină protejată dacă numele mai există.'))
    x.append(p('Fiecare formular are token CSRF, valorile sunt validate pe server (latitudine între -90 și 90, longitudine '
               'între -180 și 180, descriere de 1–255 caractere), iar ștergerile cer confirmare în browser. După fiecare '
               'modificare, pagina este reîncărcată prin redirecționare, cu un mesaj afișat o singură dată.'))
    if CAUTARE_GATA:
        x.append(p('Din versiunea 0.13.0, formularul are și o bară de căutare Google Places (`PlaceAutocompleteElement`). '
                   'Când aleg un rezultat, `js/admin.js` cere detaliile locului (numele și coordonatele), completează '
                   'latitudinea, longitudinea și descrierea și mută marcajul pe hartă. Salvarea trece prin aceeași validare pe '
                   'server ca înainte, deci căutarea doar completează câmpurile. Bara este ascunsă până când scriptul Google '
                   'reușește să o creeze; dacă cheia este refuzată sau o cerere eșuează, rămâne ascunsă, iar formularul se '
                   'completează în continuare de mână. Primii pași ai acestei funcții (variabila care decide dacă bara apare '
                   'și încărcarea scriptului Google) i-am scris eu, ghidat pas cu pas.'))
        x.append(p('Tot în versiunea 0.13.0, două setări din `config.php` pregătesc site-ul pentru publicare: '
                   '`harta_publica` face harta vizibilă fără cont, iar `inregistrare_deschisa` = `false` ascunde formularul de '
                   'cont nou și refuză crearea de conturi. Fără aceste setări, comportamentul rămâne cel cerut în enunț: harta '
                   'este pagina protejată și oricine își poate crea un cont.'))
    return x


def implementare_m7(a):
    p, b, t, cod, fig, img = a['p'], a['bullet'], a['titlu'], a['cod'], a['figura'], a['img']
    x = []
    x.append(t(2, 'Harta cu marcaje'))
    x.append(p('Tema cere o hartă Google Maps cu câte un marcaj pentru fiecare punct din baza de date. API-ul Google Maps '
               'JavaScript cere însă o cheie dintr-un proiect Google Cloud cu facturare activată (card bancar), chiar și '
               'pentru utilizarea gratuită. Un abonament la Gemini nu include acest lucru: este un serviciu separat, '
               'facturat prin Google Cloud. De aceea, pagina are două moduri, alese în `includes/config.php`:'))
    for c in [
        '**demo** (implicit): harta este desenată cu biblioteca Leaflet 1.9.4, pe hărți OpenStreetMap, fără cheie și fără cont;',
        '**google**: aceeași hartă, cu API-ul Google Maps JavaScript și cheia din `config.php`. Dacă modul este `google`, '
        'dar cheia lipsește sau Google o refuză (de exemplu după expirarea creditului), pagina afișează automat harta demo.',
    ]:
        x.append(b(c))
    x.append(fig(img('harta-demo.png'), 'Pagina protejată cu harta în modul demo (Leaflet și OpenStreetMap)'))
    x.append(p('Ambele moduri folosesc aceleași date. `harta.php` citește punctele cu `toatePunctele()` și le trimite '
               'scriptului `js/harta.js` ca JSON, într-un element `<script type="application/json">`, generat cu '
               '`json_encode` și opțiunile `JSON_HEX_*`. Scriptul pune câte un marcaj verde pentru fiecare punct și '
               'încadrează harta astfel încât toate marcajele să fie vizibile (Figura 9). La trecerea cu mouse-ul, '
               'marcajul își arată descrierea; la click, o fereastră afișează descrierea și coordonatele.'))
    x.append(p('Descrierile vin din baza de date, deci sunt tratate ca text nesigur. Leaflet ar insera un șir de '
               'caractere ca HTML, așa că etichetele și ferestrele primesc elemente create cu `textContent` (Codul 11). '
               'Un test automat introduce în baza de date descrierea `<img src=x onerror=alert(1)>` și verifică faptul '
               'că ea nu apare nicăieri în pagină ca etichetă HTML reală.'))
    x.append(cod([
        "const eticheta = document.createElement('span');",
        'eticheta.textContent = punct.descriere;          // text, never HTML',
        'L.circleMarker([punct.lat, punct.lng], { radius: 9, fillColor: CULOARE_VIZITAT, fillOpacity: 1 })',
        '    .bindTooltip(eticheta)',
        '    .bindPopup(continutLoc(punct))',
        '    .addTo(harta);',
    ], 'Un marcaj Leaflet cu descrierea pusă ca text (js/harta.js)'))
    x.append(p('În modul `google`, același script folosește `AdvancedMarkerElement`, varianta actuală a marcajelor '
               'Google (clasa `google.maps.Marker` este depreciată). Dacă cheia este refuzată, Google apelează '
               'funcția `gm_authFailure`, care încarcă Leaflet și desenează harta demo în același loc. Cheia apare în codul paginii, ca la orice hartă Google, deci trebuie restricționată '
               'în Google Cloud la adresa site-ului (`http://localhost/*`) și doar la API-ul Maps JavaScript. Modul '
               '`google` a fost verificat cu o cheie reală, dintr-un cont Google Cloud de probă (Figura 10).'))
    x.append(fig(img('harta-google.png'), 'Aceeași pagină în modul google, cu API-ul Google Maps JavaScript'))
    x.append(p('Lista locurilor rămâne sub hartă, ca alternativă accesibilă și ca '
               'rezervă dacă biblioteca de hărți nu se poate încărca.'))
    return x


def implementare_m6(a):
    p, b, t, cod, fig, img = a['p'], a['bullet'], a['titlu'], a['cod'], a['figura'], a['img']
    x = []
    x.append(t(2, 'Completarea automată a numelui de utilizator'))
    x.append(p('Tema cere completare automată cu jQuery pe câmpul de utilizator din formularul de autentificare. Am '
               'folosit widget-ul `autocomplete` din jQuery UI: pe măsură ce vizitatorul tastează, widget-ul trimite '
               'textul la server printr-o cerere AJAX (`api/utilizatori.php?term=...`) și afișează numele primite '
               'într-o listă sub câmp (Figura 8).'))
    x.append(fig(img('autocomplete.png'), 'Lista de completare automată sub câmpul de utilizator'))
    x.append(p('Codul din browser are câteva rânduri (Codul 9). Lista apare de la două caractere tastate, iar cererea '
               'pleacă abia după o scurtă pauză în tastare, nu la fiecare tastă. jQuery UI scrie numele în listă ca text '
               '(`.text()`), deci un nume nu poate introduce cod HTML în pagină.'))
    x.append(cod([
        "$('#login-user').autocomplete({",
        "    source: 'api/utilizatori.php',",
        '    minLength: 2,          // same limit as the server: no list for a single letter',
        '    delay: 250,            // wait for a short pause in typing, not one request per key',
        '});',
    ], 'Activarea completării automate (js/autocomplete.js)'))
    x.append(p('Scriptul PHP întoarce un tablou JSON cu cel mult 10 nume care încep cu textul primit, căutate cu o '
               'interogare pregătită și operatorul `LIKE` (Codul 10). Într-un `LIKE`, `%` înseamnă „orice text”, iar `_` '
               '„orice caracter”. Textul primit poate conține doar caracterele permise într-un nume de utilizator, deci '
               '`%` este refuzat, iar `_` este escapat cu `addcslashes`; altfel, un singur `%` ar lista toți utilizatorii.'))
    x.append(cod([
        "if (!is_string($text) || !preg_match('/^[A-Za-z0-9._-]{2,30}$/', $text)) {",
        "    echo '[]';",
        '    exit;',
        '}',
        "$model = addcslashes($text, '%_\\\\') . '%';           // starts with the text",
        "$cerere = mysqli_prepare(conectareBd(), 'SELECT user FROM users WHERE user LIKE ? ORDER BY user LIMIT 10');",
        "mysqli_stmt_bind_param($cerere, 's', $model);",
    ], 'Căutarea numelor pentru completarea automată (api/utilizatori.php)'))
    x.append(p('Lista de nume dezvăluie ce conturi există; este un compromis cerut de temă. Riscul este redus prin '
               'limitele de mai sus. Răspunsul are antetele `Content-Type: application/json` și '
               '`X-Content-Type-Options: nosniff`, ca browserul să nu îl interpreteze niciodată ca pagină HTML.'))
    x.append(p('jQuery 3.7.1 și jQuery UI 1.13.2 sunt încărcate de pe CDN-ul oficial (code.jquery.com), cu versiuni '
               'fixe și cu atributul `integrity` (Subresource Integrity). Browserul calculează hash-ul fișierului primit '
               'și îl rulează doar dacă se potrivește, deci un fișier modificat pe CDN nu poate rula cod pe site. '
               'Bibliotecile sunt încărcate doar pe pagina de autentificare. Dacă laboratorul 9 va folosi alte versiuni '
               'sau alt mod de încărcare, codul se poate adapta ușor.'))
    return x


def implementare_m4_m5(a):
    p, b, t, cod, fig, img = a['p'], a['bullet'], a['titlu'], a['cod'], a['figura'], a['img']
    x = []
    x.append(t(2, 'Pagina de autentificare și înregistrare'))
    x.append(p('Pagina `login.php` conține cele două formulare cerute de temă, unul lângă altul (Figura 6). Ambele sunt '
               'trimise prin POST către aceeași pagină; un câmp ascuns, `actiune`, spune care formular a fost trimis. '
               'După o operație reușită, browserul este redirecționat (HTTP 303) spre pagina protejată, astfel încât o '
               'reîncărcare a paginii nu retrimite formularul (modelul Post/Redirect/Get).'))
    x.append(fig(img('login.png'), 'Pagina de autentificare, cu mesajele de validare ale formularului de înregistrare'))

    x.append(t(2, 'Înregistrarea utilizatorilor'))
    x.append(p('Datele primite sunt verificate pe server, chiar dacă browserul le verifică deja prin atributele HTML '
               '(`minlength`, `pattern`): verificările din browser pot fi ocolite ușor. Numele de utilizator are 3–30 '
               'caractere, doar litere, cifre, punct, minus și underscore, pentru că apare pe pagini și în lista de '
               'completare automată. Parola are cel puțin 8 caractere și cel mult 72, limita algoritmului bcrypt, care '
               'ar ignora restul fără niciun avertisment.'))
    x.append(p('Parola nu este salvată niciodată ca text. Funcția `password_hash` produce un hash bcrypt, cu o sare '
               'aleatorie inclusă, deci două parole identice dau hash-uri diferite (Codul 7). Dacă numele există deja, '
               'cheia `UNIQUE` din baza de date respinge inserarea cu eroarea 1062, transformată într-un mesaj clar. '
               'Comparația nu face diferența între litere mari și mici, deci „Seba” și „seba” nu pot fi conturi diferite.'))
    x.append(cod([
        'try {',
        '    $id = adaugaUtilizator($numeCont, password_hash($parola, PASSWORD_DEFAULT));',
        '    conecteaza($id, $numeCont);',
        "    header('Location: harta.php', true, 303);",
        '    exit;',
        '} catch (mysqli_sql_exception $e) {',
        '    if ($e->getCode() !== 1062) {     // 1062 = duplicate key: the name is taken',
        '        throw $e;',
        '    }',
        "    $eroriCont[] = $t['eroare_nume_ocupat'];",
        '}',
    ], 'Crearea contului cu parola transformată în hash (login.php)'))

    x.append(t(2, 'Autentificarea și sesiunea'))
    x.append(p('La autentificare, contul este căutat cu interogarea pregătită descrisă mai sus, iar parola este '
               'verificată cu `password_verify`. Pentru un nume inexistent, `password_verify` rulează totuși, pe un hash '
               'fals, ca timpul de răspuns să nu dezvăluie dacă numele există. Mesajul de eroare este același în ambele '
               'cazuri: „Utilizator sau parolă greșită.” (Codul 8).'))
    x.append(cod([
        "$cont = $numeLogin === '' ? null : gasesteUtilizator($numeLogin);",
        "$parolaCorecta = password_verify($parola, $cont['password'] ?? HASH_FALS);",
        '',
        'if ($cont !== null && $parolaCorecta) {',
        "    conecteaza((int) $cont['ID'], $cont['user']);    // session_regenerate_id(true) inside",
        "    header('Location: harta.php', true, 303);",
        '    exit;',
        '}',
        "$eroareLogin = $t['eroare_login'];                    // one message for both cases",
    ], 'Verificarea parolei (login.php)'))
    x.append(p('Fișierul `includes/sesiune.php` pornește sesiunea doar pe paginile aplicației, cu un cookie `HttpOnly` '
               '(inaccesibil din JavaScript), `SameSite=Lax` și `Secure` pe HTTPS. În laboratorul 3, identificatorul '
               'sesiunii era fixat cu `session_id("mainID")`, deci toți vizitatorii împărțeau aceeași sesiune. Aici PHP '
               'alege un identificator aleatoriu, iar după autentificare `session_regenerate_id(true)` îl înlocuiește, '
               'ca un identificator cunoscut dinainte să nu mai fie valabil (session fixation). O sesiune nefolosită '
               '30 de minute expiră.'))
    x.append(p('Ambele formulare conțin un token CSRF: un șir aleatoriu păstrat în sesiune și verificat cu `hash_equals` '
               'la fiecare trimitere. Un alt site poate face browserul să trimită un formular către această pagină, dar '
               'nu poate citi pagina, deci nu cunoaște tokenul.'))
    if LIMITA_GATA:
        x.append(p('Încercările repetate de autentificare sunt limitate (`includes/limitare.php`). Fiecare parolă greșită '
                   'adaugă două rânduri în tabelul `incercari_login`: unul pentru numele de utilizator și unul pentru adresa '
                   'IP a vizitatorului, ambele memorate doar ca hash SHA-256, deci tabelul nu conține nici nume, nici adrese. '
                   'După 5 greșeli pentru același nume sau 20 de pe aceeași adresă, în ultimele 15 minute, formularul răspunde '
                   'cu codul HTTP 429 și mesajul „Prea multe încercări greșite”, fără să mai verifice parola, la fel pentru '
                   'nume existente și inexistente. O autentificare reușită șterge greșelile acelui nume, dar nu și pe cele ale '
                   'adresei, ca un atacator să nu-și poată reseta limita intrând în propriul cont. Tabelul nu face parte din '
                   'cele cerute de temă: este un jurnal tehnic, singurul pe care contul `pai_app` îl poate și șterge.'))

    x.append(t(2, 'Pagina protejată și deconectarea'))
    x.append(p('Pagina `harta.php` începe cu `includes/auth.php`: fără sesiune, vizitatorul este trimis la '
               '`login.php`. Pentru un utilizator autentificat, pagina afișează mesajul „Bine ai venit, <utilizator>!”, '
               'cu numele escapat prin `e()`, linkul de deconectare și locurile citite din baza de date (Figura 7, prima '
               'variantă a paginii). Harta a fost adăugată deasupra listei în etapa M7. Antetul `Cache-Control: no-store` împiedică browserul să '
               'păstreze o copie, deci după deconectare butonul Înapoi nu mai poate afișa pagina.'))
    x.append(fig(img('harta.png'), 'Pagina protejată după autentificare'))
    x.append(p('Linkul de deconectare conține tokenul CSRF (`logout.php?token=...`). Fără el, un alt site ar putea '
               'deconecta utilizatorul printr-o simplă imagine. La deconectare, datele sesiunii sunt șterse, cookie-ul '
               'este invalidat, iar fișierul sesiunii de pe server este distrus.'))
    return x


def implementare_m3(a):
    p, b, t, cod = a['p'], a['bullet'], a['titlu'], a['cod']
    x = []
    x.append(t(2, 'Scripturile SQL'))
    x.append(p('Baza de date este descrisă în trei scripturi din folderul `sql/`, rulate o singură dată, ca `root`, '
               'la instalarea proiectului:'))
    for c in [
        '`schema.sql` creează baza `pai_proiect` și cele două tabele (Codul 5);',
        '`date-initiale.sql` adaugă primele locuri pentru hartă;',
        '`utilizator.sql` creează contul folosit de site, cu drepturi minime.',
    ]:
        x.append(b(c))
    x.append(p('Scripturile pot fi rulate din nou fără efecte nedorite: `CREATE ... IF NOT EXISTS` nu recreează ce '
               'există deja, iar datele inițiale au identificatori stabiliți dinainte și sunt adăugate cu `INSERT IGNORE`.'))
    x.append(cod([
        'CREATE TABLE IF NOT EXISTS points (',
        '    ID          INT UNSIGNED  NOT NULL AUTO_INCREMENT,',
        '    lat         DECIMAL(9,6)  NOT NULL,',
        '    `long`      DECIMAL(9,6)  NOT NULL,',
        '    description VARCHAR(255)  NOT NULL,',
        '    PRIMARY KEY (ID),',
        '    CONSTRAINT chk_points_lat  CHECK (lat BETWEEN -90 AND 90),',
        '    CONSTRAINT chk_points_long CHECK (`long` BETWEEN -180 AND 180)',
        ') ENGINE=InnoDB;',
    ], 'Tabelul points (sql/schema.sql)'))

    x.append(t(2, 'Un utilizator dedicat, cu drepturi minime'))
    x.append(p('În laboratorul 3, conexiunea se făcea cu contul `root`, fără parolă. Pentru proiect am creat contul '
               '`pai_app`, care are doar drepturile `SELECT` și `INSERT`, și doar pe baza `pai_proiect` (plus `DELETE` pe '
               'jurnalul încercărilor de autentificare, `incercari_login`). Site-ul are nevoie '
               'numai de citire (punctele, conturile la autentificare) și de adăugare (conturile noi). Dacă o greșeală din '
               'cod ar fi exploatată, atacatorul nu ar putea șterge sau modifica date, nu ar putea șterge tabele și nu ar '
               'putea citi alte baze de date. Parola contului este generată aleatoriu și stă doar în `includes/config.php`, '
               'fișier exclus din Git; în depozit există doar modelul `config.example.php`.'))

    x.append(t(2, 'Conexiunea din PHP'))
    x.append(p('Fișierul `includes/db.php` deschide conexiunea o singură dată pe cerere, setează setul de caractere '
               '`utf8mb4` și transformă erorile MySQL în excepții (`mysqli_report`). Dacă baza de date nu răspunde, '
               'detaliile sunt scrise în jurnalul serverului, iar vizitatorul primește doar un mesaj scurt și codul HTTP '
               '503, fără nume de utilizatori, căi sau versiuni care l-ar putea ajuta pe un atacator.'))
    x.append(p('Orice valoare venită de la utilizator ajunge în baza de date printr-o interogare pregătită (Codul 6). '
               'Textul interogării, cu semnul `?` în locul valorii, este trimis separat de valoare, deci un nume de '
               'utilizator ca `x\' OR \'1\'=\'1` este comparat ca text obișnuit și nu poate schimba interogarea [13].'))
    x.append(cod([
        'function gasesteUtilizator(string $nume): ?array',
        '{',
        "    $cerere = mysqli_prepare(conectareBd(), 'SELECT ID, user, password FROM users WHERE user = ?');",
        "    mysqli_stmt_bind_param($cerere, 's', $nume);     // 's' = one string parameter",
        '    mysqli_stmt_execute($cerere);',
        '    $rand = mysqli_fetch_assoc(mysqli_stmt_get_result($cerere));',
        '    mysqli_stmt_close($cerere);',
        '    return $rand ?: null;',
        '}',
    ], 'Căutarea unui utilizator cu o interogare pregătită (includes/db.php)'))

    x.append(t(2, 'O problemă întâlnită: codificarea caracterelor'))
    x.append(p('La primul import, descrierea „Brașov – acasă” a fost salvată greșit („BraÈ™ov”). Clientul `mysql` din '
               'Windows citește implicit fișierele în codificarea `cp850`, așa că fiecare literă cu diacritice a fost '
               'transformată în două sau trei caractere greșite. Problema a fost descoperită de testul automat care '
               'compară descrierea citită din baza de date cu textul original. Soluția a fost comanda `SET NAMES utf8mb4` '
               'la începutul scripturilor SQL, care funcționează indiferent de setările clientului.'))
    return x


REZULTAT_TESTE = ('La rularea din 13.11.2026, toate cele 251 de verificări au trecut. Cu un cont de administrator '
                  '(variabilele ADMIN_USER și ADMIN_PASS), testele parcurg și fluxul complet al panoului de administrare: '
                  '269 de verificări. Conturile de test sunt create prin formularul real și șterse la final. Verificările bazei de date sunt în `teste/verificare-bd.php`: rândurile de test sunt adăugate '
                  'într-o tranzacție anulată la final, deci baza de date rămâne neschimbată. Scriptul se termină cu un cod '
                  'de ieșire diferit de zero dacă o verificare eșuează, deci poate fi folosit înainte de fiecare commit.')

CONCLUZII = ('Până în acest moment au fost finalizate etapele M0–M7: mediul de dezvoltare, site-ul personal cu șapte '
             'pagini, în două limbi și cu două teme vizuale, componentele PHP comune, baza de date a aplicației, cu un '
             'utilizator dedicat și interogări pregătite, înregistrarea, autentificarea, pagina protejată prin sesiune și '
             'completarea automată cu jQuery, harta cu câte un marcaj pentru fiecare loc din baza de date, un panou de '
             'administrare pentru locuri și conturi și un panou de progres în stil de joc, cu realizări și turul locurilor. Aspectul a fost simplificat: pagina de start este minimală, iar detaliile sunt pe pagina Despre. Site-ul respectă cerința temei privind paginile de prezentare, '
             'proiectele anterioare și CV-ul, oferit atât ca pagină web, cât și ca PDF.')

PASI_URMATORI = ('Urmează extinderea hărții (culori pentru locurile vizitate și dorite, fotografii), după acordul '
                 'cadrului didactic; revizuirea de securitate și finisajele sunt încheiate, iar toate informațiile personale '
                 'sunt completate. După prezentare, site-ul poate fi publicat pe un server gratuit, cu harta vizibilă fără '
                 'cont și autentificarea păstrată doar pentru administrare.')

REZUMAT = [
    'Proiectul constă într-un site personal bilingv (română și engleză) și o aplicație web cu bază de date, realizate '
    'pentru disciplina Programarea Aplicațiilor Internet. Site-ul prezintă autorul, obiectivele lui profesionale, interesele, competențele, '
    'proiectele și un CV formal, care poate fi descărcat ca PDF dintr-o singură pagină web. Paginile personale folosesc '
    'o temă luminoasă, iar cele tehnice o temă „cyber”, cu tranziții animate între ele, realizate cu View Transitions API.',
    'Aplicația este construită pe arhitectura client–server, cu Apache, PHP 8.2 și MariaDB în pachetul XAMPP. Codul comun '
    '(antet, subsol, alegerea limbii) este scris o singură dată și inclus în toate paginile. Pagina de proiecte citește '
    'date live din API-ul GitHub. Securitatea a fost tratată din primele etape: escaparea oricărui text afișat, '
    'validarea parametrilor din adresă, o listă permisă pentru limbă, cookie-uri HttpOnly și reguli Apache care '
    'blochează accesul la codul intern.',
    'Partea de aplicație adaugă tabelele users și points, înregistrarea și autentificarea utilizatorilor cu parole '
    'stocate ca hash, completarea automată a numelui cu jQuery și o pagină protejată cu o hartă a orașelor vizitate. '
    'Dezvoltarea este versionată cu Git, împărțită în etape cu versiuni numerotate și verificată cu un set de teste automate.',
]

ABSTRACT = [
    'The project is a bilingual (Romanian and English) personal website combined with a database-backed web '
    'application, built for the Internet Application Programming course. The site presents the author, hobbies, skills, '
    'projects and a formal CV that can be downloaded as a PDF from a single web page. Personal pages use a light theme '
    'and technical pages a "cyber" theme, with animated transitions between them built on the View Transitions API.',
    'The application follows the client–server architecture, using Apache, PHP 8.2 and MariaDB from the XAMPP package. '
    'Shared code (header, footer, language selection) is written once and included in every page. The projects page '
    'reads live data from the GitHub API. Security was addressed from the first stages: every printed value is escaped, '
    'address parameters are validated, the language is checked against an allowlist, cookies are HttpOnly and Apache '
    'rules block access to internal code.',
    'The application part adds the users and points tables, user registration and login with hashed passwords, jQuery '
    'username autocomplete and a protected page with a map of visited cities. Development is versioned with Git, split '
    'into numbered releases and checked by a set of automated tests.',
]
