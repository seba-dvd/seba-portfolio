<?php
/**
 * The projects shown on proiecte.php, proiect.php and the home page.
 * One place for the data: the pages only display it.
 *
 * Every text exists in Romanian and English: ['ro' => ..., 'en' => ...].
 * tr() (includes/functii.php) picks the current language.
 *
 *   id          number used in proiect.php?id=...
 *   slug        short name, shown in the terminal ("ls ./proiecte")
 *   tip         'solo' (individual) or 'echipa' (team)
 *   repo        "owner/name" on GitHub, or null when the code is not on GitHub
 *   privat      true when the GitHub repository is private
 */
return [
    [
        'id' => 1,
        'slug' => 'straja',
        'icon' => '🛡️',
        'tip' => 'solo',
        'eticheta' => ['ro' => 'solo · în lucru', 'en' => 'solo · in progress'],
        'titlu' => ['ro' => 'Straja – mini-SOC', 'en' => 'Straja – mini-SOC'],
        'scurt' => [
            'ro' => 'Platformă proprie de monitorizare și securitate a rețelei: verifică disponibilitatea serviciilor, colectează atacuri de pe un honeypot și le îmbogățește cu threat intelligence.',
            'en' => 'My own network monitoring and security platform: it checks that services are up, collects attacks from a honeypot and enriches them with threat intelligence.',
        ],
        'functii' => [
            'ro' => [
                'Ciclul detectează → îmbogățește → decide → răspunde → auditează',
                'Microservicii Spring Boot: gateway, monitorizare, threat intelligence, incidente',
                'Autentificare cu Keycloak (OIDC) și roluri ADMIN / ANALYST / VIEWER',
                'Honeypot Cowrie pe o mașină virtuală izolată, alerte pe Telegram',
            ],
            'en' => [
                'The detect → enrich → decide → respond → audit loop',
                'Spring Boot microservices: gateway, monitoring, threat intelligence, incidents',
                'Keycloak sign-in (OIDC) with ADMIN / ANALYST / VIEWER roles',
                'A Cowrie honeypot on an isolated virtual machine, Telegram alerts',
            ],
        ],
        'rol' => null,
        'tehnologii' => ['Java 21', 'Spring Boot', 'Keycloak', 'MySQL', 'Redis', 'Docker'],
        'repo' => null,
        'privat' => false,
    ],
    [
        'id' => 2,
        'slug' => 'proiect-pai',
        'icon' => '🗺️',
        'tip' => 'solo',
        'eticheta' => ['ro' => 'solo · PAI', 'en' => 'solo · PAI'],
        'titlu' => ['ro' => 'Proiect PAI – acest site', 'en' => 'PAI project – this site'],
        'scurt' => [
            'ro' => 'Site personal bilingv cu aplicație web integrată: înregistrare și autentificare, autocomplete cu jQuery și o hartă cu orașele vizitate.',
            'en' => 'A bilingual personal site with a built-in web app: sign-up and login, jQuery autocomplete and a map of the cities I have visited.',
        ],
        'functii' => [
            'ro' => [
                'Două teme (mint și cyber) construite cu variabile CSS',
                'Site bilingv RO/EN, cu alegerea limbii păstrată într-un cookie',
                'Fereastră de previzualizare cu date live din API-ul GitHub',
                'Urmează: autentificare cu MySQL și harta orașelor vizitate',
            ],
            'en' => [
                'Two themes (mint and cyber) built with CSS variables',
                'Bilingual RO/EN, with the language choice kept in a cookie',
                'A preview window with live data from the GitHub API',
                'Coming next: MySQL login and the map of visited cities',
            ],
        ],
        'rol' => null,
        'tehnologii' => ['PHP', 'MySQL', 'jQuery', 'JavaScript', 'HTML', 'CSS'],
        'repo' => 'seba-dvd/pai-proiect',
        'privat' => true,
    ],
    [
        'id' => 3,
        'slug' => 'nutritrack',
        'icon' => '🥗',
        'tip' => 'solo',
        'eticheta' => ['ro' => 'solo · personal', 'en' => 'solo · personal'],
        'titlu' => ['ro' => 'NutriTrack', 'en' => 'NutriTrack'],
        'scurt' => [
            'ro' => 'Aplicație mobilă bilingvă pentru urmărirea macronutrienților și a greutății: 262 de alimente, jurnal pe mese, grafic de evoluție și ajustare automată a caloriilor.',
            'en' => 'A bilingual mobile app for tracking macronutrients and weight: 262 foods, a meal log, a progress chart and automatic calorie adjustment.',
        ],
        'functii' => [
            'ro' => [
                'Panou zilnic: consumat, țintă, rămas, pe 6 mese',
                'Jurnal cu selecție categorie → aliment → grame și calcul live',
                'Grafic al greutății și recomandare automată de calorii',
                'Date în Supabase, cu Row-Level Security',
            ],
            'en' => [
                'A daily dashboard: eaten, target and remaining, across 6 meals',
                'A log with category → food → grams selection and live totals',
                'A weight chart and an automatic calorie recommendation',
                'Data in Supabase, with Row-Level Security',
            ],
        ],
        'rol' => null,
        'tehnologii' => ['React Native', 'Expo', 'TypeScript', 'Supabase'],
        'repo' => 'seba-dvd/NutriTrack',
        'privat' => true,
    ],
    [
        'id' => 4,
        'slug' => 'port-scanner',
        'icon' => '🔍',
        'tip' => 'solo',
        'eticheta' => ['ro' => 'solo · cyber', 'en' => 'solo · cyber'],
        'titlu' => ['ro' => 'SimpleScanner', 'en' => 'SimpleScanner'],
        'scurt' => [
            'ro' => 'Scanner de porturi TCP: rezolvă DNS-ul, scanează în paralel porturile 1–1024 cu thread-uri și identifică serviciile. Doar pe sisteme autorizate.',
            'en' => 'A TCP port scanner: it resolves DNS, scans ports 1–1024 in parallel threads and identifies services. Only on systems I am authorised to test.',
        ],
        'functii' => [
            'ro' => [
                'Rezolvare DNS automată',
                'Scanare paralelă a porturilor 1–1024',
                'Identificarea serviciilor TCP',
                'Export al rezultatelor în fișiere .txt',
            ],
            'en' => [
                'Automatic DNS resolution',
                'Parallel scan of ports 1–1024',
                'TCP service identification',
                'Results exported to .txt files',
            ],
        ],
        'rol' => null,
        'tehnologii' => ['Python', 'Socket', 'Threading'],
        'repo' => 'seba-dvd/PortScanner',
        'privat' => false,
    ],
    [
        'id' => 5,
        'slug' => 'contact-book',
        'icon' => '📒',
        'tip' => 'solo',
        'eticheta' => ['ro' => 'solo · facultate', 'en' => 'solo · university'],
        'titlu' => ['ro' => 'Agenda de contacte', 'en' => 'Contact book'],
        'scurt' => [
            'ro' => 'Aplicație în linia de comandă pentru gestiunea contactelor: validare, căutare, sortare cronologică, export CSV și salvare automată în fișier.',
            'en' => 'A command-line contact manager: validation, search, newest-first sorting, CSV export and automatic saving to a file.',
        ],
        'functii' => [
            'ro' => [
                'Adăugare, modificare și ștergere de contacte, cu validare',
                'Căutare după nume, prenume sau telefon',
                'Verificarea duplicatelor (telefon și email unice)',
                'Export CSV compatibil cu Excel',
            ],
            'en' => [
                'Add, edit and delete contacts, with validation',
                'Search by first name, last name or phone',
                'Duplicate checks (phone and email must be unique)',
                'Excel-compatible CSV export',
            ],
        ],
        'rol' => null,
        'tehnologii' => ['C++', 'STL', 'Fișiere'],
        'repo' => 'seba-dvd/ContactBook',
        'privat' => false,
    ],
    [
        'id' => 6,
        'slug' => 'cv-web',
        'icon' => '📄',
        'tip' => 'solo',
        'eticheta' => ['ro' => 'solo · laborator', 'en' => 'solo · lab'],
        'titlu' => ['ro' => 'CV web – laboratorul 2', 'en' => 'Web CV – lab 2'],
        'scurt' => [
            'ro' => 'Primul meu CV ca pagină web, cu două teme de culori care se schimbă dintr-un buton, cu JavaScript.',
            'en' => 'My first CV as a web page, with two colour themes switched from a button in JavaScript.',
        ],
        'functii' => [
            'ro' => [
                'Pagină HTML cu tabele, liste și imagini',
                'Două foi de stil, comutate cu JavaScript',
                'Formular simplu cu mesaj de confirmare',
            ],
            'en' => [
                'An HTML page with tables, lists and images',
                'Two stylesheets, switched with JavaScript',
                'A simple form with a confirmation message',
            ],
        ],
        'rol' => null,
        'tehnologii' => ['HTML', 'CSS', 'JavaScript'],
        'repo' => null,
        'privat' => false,
    ],
    [
        'id' => 7,
        'slug' => 'kubidoit',
        'icon' => '✅',
        'tip' => 'echipa',
        'eticheta' => ['ro' => 'echipă · 4 membri', 'en' => 'team · 4 members'],
        'titlu' => ['ro' => 'KubiDoIt', 'en' => 'KubiDoIt'],
        'scurt' => [
            'ro' => 'Aplicație de tip to-do, web și mobil, pentru persoane și grupuri care împart sarcini. Datele se sincronizează între web, Android și iOS. Proiect SOPM.',
            'en' => 'A web and mobile to-do app for people and groups who share tasks. Data syncs across web, Android and iOS. A SOPM course project.',
        ],
        'functii' => [
            'ro' => [
                'Liste de activități personale și de grup',
                'Sincronizare în timp real între web și mobil',
                'Securitate la nivel de rând (Row Level Security) în PostgreSQL',
            ],
            'en' => [
                'Personal and group task lists',
                'Real-time sync between web and mobile',
                'Row Level Security in PostgreSQL',
            ],
        ],
        'rol' => [
            'ro' => 'dezvoltator backend – Supabase, securitate, sincronizare',
            'en' => 'backend developer – Supabase, security, sync',
        ],
        'tehnologii' => ['React', 'React Native', 'TypeScript', 'Supabase', 'PostgreSQL'],
        'repo' => 'seba-dvd/KubiDoIt',
        'privat' => true,
    ],
    [
        'id' => 8,
        'slug' => 'edudocsync',
        'icon' => '📚',
        'tip' => 'echipa',
        'eticheta' => ['ro' => 'echipă · hackathon', 'en' => 'team · hackathon'],
        'titlu' => ['ro' => 'EduDocSync (CodeStorm)', 'en' => 'EduDocSync (CodeStorm)'],
        'scurt' => [
            'ro' => 'Realizat la hackathonul Transilvania CodeStorm @ MI (ediția a 2-a), într-o echipă de 4: aplicație client-server care procesează documente academice în PDF (planuri de învățământ, fișe de disciplină): extragere de text, OCR și dashboard.',
            'en' => 'Built at the Transilvania CodeStorm @ MI hackathon (2nd edition) in a team of 4: a client-server app that processes academic PDF documents (curricula, course sheets): text extraction, OCR and a dashboard.',
        ],
        'functii' => [
            'ro' => [
                'Încărcare de PDF-uri și imagini',
                'Extragere de text, cu OCR pentru documente scanate',
                'Extragerea automată a câmpurilor academice',
                'Dashboard React și vederi printabile',
            ],
            'en' => [
                'PDF and image upload',
                'Text extraction, with OCR for scanned documents',
                'Automatic extraction of academic fields',
                'A React dashboard and printable views',
            ],
        ],
        // TODO: your role in the team
        'rol' => ['ro' => '[ROLUL TĂU]', 'en' => '[YOUR ROLE]'],
        'tehnologii' => ['Python', 'PostgreSQL', 'OCR', 'React'],
        'repo' => 'hsilviu05/CodeStorm',
        'privat' => false,
    ],
];
