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
        'repo' => 'seba-dvd/seba-portfolio',
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
        'id' => 9,
        'slug' => 'live-auction',
        'icon' => '🔨',
        'tip' => 'echipa',
        'eticheta' => ['ro' => 'echipă · 5 membri · în lucru', 'en' => 'team · 5 members · in progress'],
        'titlu' => ['ro' => 'Live Auction App', 'en' => 'Live Auction App'],
        'scurt' => [
            'ro' => 'Platformă de licitații live cu monede virtuale, construită din microservicii. Ofertele, prețul și cronometrul se actualizează instantaneu pentru toți cei care urmăresc licitația. Proiect în echipă, după Agile/Scrum.',
            'en' => 'A live auction platform with virtual coins, built from microservices. Bids, price and countdown update instantly for everyone watching the auction. A team project run with Agile/Scrum.',
        ],
        'functii' => [
            'ro' => [
                'Conturi cu JWT și portofel de monede virtuale',
                'Licitare în timp real prin WebSocket (STOMP)',
                'Fonduri rezervate la licitare, eliberate când ești depășit',
                'Anti-sniping: o ofertă în ultimele 30 de secunde prelungește licitația',
                'API gateway și o bază de date separată pentru fiecare serviciu',
            ],
            'en' => [
                'JWT accounts and a virtual-coin wallet',
                'Real-time bidding over WebSocket (STOMP)',
                'Funds held when you bid, released when you are outbid',
                'Anti-sniping: a bid in the last 30 seconds extends the auction',
                'An API gateway and a separate database for each service',
            ],
        ],
        'rol' => [
            'ro' => 'Project Manager; user-service și api-gateway (autentificare JWT)',
            'en' => 'Project Manager; user-service and api-gateway (JWT authentication)',
        ],
        'tehnologii' => ['Java 21', 'Spring Boot', 'React', 'MySQL', 'WebSocket', 'Docker'],
        'repo' => 'seba-dvd/live-auction-app',
        'privat' => true,
    ],
    [
        'id' => 10,
        'slug' => 'ip-tracker',
        'icon' => '🌐',
        'tip' => 'echipa',
        'eticheta' => ['ro' => 'echipă · 3 membri', 'en' => 'team · 3 members'],
        'titlu' => ['ro' => 'IP Tracker', 'en' => 'IP Tracker'],
        'scurt' => [
            'ro' => 'Aplicație care arată informații despre o adresă IP sau un domeniu: locația aproximativă, furnizorul de internet, fusul orar și poziția pe hartă. Există ca pagină web simplă, aplicație React și aplicație mobilă React Native.',
            'en' => 'An app that shows information about an IP address or a domain: approximate location, internet provider, time zone and position on a map. It exists as a plain web page, a React app and a React Native mobile app.',
        ],
        'functii' => [
            'ro' => [
                'Căutare după IPv4, IPv6 sau domeniu; la pornire, IP-ul propriu',
                'Hartă cu locația: OpenStreetMap pe web, Google Maps / Apple Maps pe telefon',
                'Istoricul ultimelor 5 căutări și tema luminoasă / întunecată, păstrate între vizite',
                'Cod comun pentru web și mobil, testat cu Vitest',
            ],
            'en' => [
                'Search by IPv4, IPv6 or domain; your own IP on start',
                'A location map: OpenStreetMap on the web, Google Maps / Apple Maps on the phone',
                'The last 5 searches and a light / dark theme, kept between visits',
                'Shared code for web and mobile, tested with Vitest',
            ],
        ],
        'rol' => [
            'ro' => 'istoricul căutărilor, cardurile și așezarea în pagină (web și mobil), design responsive, documentația',
            'en' => 'search history, cards and page layout (web and mobile), responsive design, documentation',
        ],
        'tehnologii' => ['JavaScript', 'React', 'React Native', 'Expo', 'Leaflet', 'Vitest'],
        'repo' => 'seba-dvd/IP_Tracker',
        'privat' => true,
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
        'rol' => [
            'ro' => 'agentul AI cu Ollama, backend și Docker',
            'en' => 'the AI agent with Ollama, backend and Docker',
        ],
        'tehnologii' => ['Python', 'Ollama', 'PostgreSQL', 'OCR', 'Docker', 'React'],
        'repo' => 'hsilviu05/CodeStorm',
        'privat' => false,
    ],
];
