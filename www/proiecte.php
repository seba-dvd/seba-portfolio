<?php
$pagina = 'proiecte';
$titlu = 'Proiecte';
$temaCyber = true;
$scripturi = ['js/proiecte.js'];
require __DIR__ . '/includes/antet.php';
?>

    <main>
        <!-- ===== Fake terminal window ===== -->
        <section class="terminal">
            <div class="terminal-bara">
                <span class="buton rosu"></span>
                <span class="buton galben"></span>
                <span class="buton verde"></span>
                <span class="terminal-titlu">seba@pai: ~/proiecte</span>
            </div>
            <div class="terminal-corp">
                <p><span class="prompt">$</span> whoami</p>
                <h1>seba – student, cyber security &amp; networking</h1>
                <p><span class="prompt">$</span> ls ./individuale</p>
                <p>straja/  proiect-pai/  nutritrack/  port-scanner/  contact-book/  cv-web/</p>
                <p><span class="prompt">$</span> ls ./echipa</p>
                <p class="echipa-text">kubidoit/  edudocsync/</p>
                <p><span class="prompt">$</span> <span class="cursor">_</span></p>
            </div>
        </section>

        <!-- ===== Individual projects ===== -->
        <h2 class="titlu-sectiune">// Proiecte individuale</h2>
        <section class="grid-proiecte">
            <article class="card proiect solo" data-repo="" tabindex="0" role="button" aria-haspopup="dialog">
                <div class="proiect-cap">
                    <span class="icon">🛡️</span>
                    <span class="eticheta">solo · în lucru</span>
                </div>
                <h3>Straja – mini-SOC</h3>
                <p>
                    Platformă proprie de monitorizare și securitate a rețelei: verifică disponibilitatea
                    serviciilor, colectează atacuri de pe un honeypot și le îmbogățește cu threat intelligence.
                </p>
                <ul class="taguri">
                    <li>Java 21</li>
                    <li>Spring Boot</li>
                    <li>Keycloak</li>
                    <li>MySQL</li>
                    <li>Docker</li>
                </ul>
            </article>

            <article class="card proiect solo" data-repo="seba-dvd/pai-proiect" data-privat tabindex="0" role="button" aria-haspopup="dialog">
                <div class="proiect-cap">
                    <span class="icon">🗺️</span>
                    <span class="eticheta">solo · PAI</span>
                </div>
                <h3>Proiect PAI – acest site</h3>
                <p>
                    Site personal cu aplicație web integrată: înregistrare și autentificare,
                    autocomplete cu jQuery și o hartă cu orașele vizitate.
                </p>
                <ul class="taguri">
                    <li>PHP</li>
                    <li>MySQL</li>
                    <li>jQuery</li>
                    <li>HTML</li>
                    <li>CSS</li>
                </ul>
                <!-- TODO: put the screenshot in www/img/proiecte/pai.png, then remove the comment marks -->
                <!-- <img src="img/proiecte/pai.png" alt="Captură de ecran a site-ului Proiect PAI"> -->
            </article>

            <article class="card proiect solo" data-repo="seba-dvd/NutriTrack" data-privat tabindex="0" role="button" aria-haspopup="dialog">
                <div class="proiect-cap">
                    <span class="icon">🥗</span>
                    <span class="eticheta">solo · personal</span>
                </div>
                <h3>NutriTrack</h3>
                <p>
                    Aplicație mobilă bilingvă pentru urmărirea macronutrienților și a greutății:
                    262 de alimente, jurnal pe mese, grafic de evoluție și ajustare automată a caloriilor.
                </p>
                <ul class="taguri">
                    <li>React Native</li>
                    <li>Expo</li>
                    <li>TypeScript</li>
                    <li>Supabase</li>
                </ul>
            </article>

            <article class="card proiect solo" data-repo="seba-dvd/PortScanner" tabindex="0" role="button" aria-haspopup="dialog">
                <div class="proiect-cap">
                    <span class="icon">🔍</span>
                    <span class="eticheta">solo · cyber</span>
                </div>
                <h3>SimpleScanner</h3>
                <p>
                    Scanner de porturi TCP: rezolvă DNS-ul, scanează în paralel porturile 1–1024
                    cu thread-uri și identifică serviciile. Doar pe sisteme autorizate.
                </p>
                <ul class="taguri">
                    <li>Python</li>
                    <li>Socket</li>
                    <li>Threading</li>
                </ul>
            </article>

            <article class="card proiect solo" data-repo="seba-dvd/ContactBook" tabindex="0" role="button" aria-haspopup="dialog">
                <div class="proiect-cap">
                    <span class="icon">📒</span>
                    <span class="eticheta">solo · facultate</span>
                </div>
                <h3>Agenda de contacte</h3>
                <p>
                    Aplicație în linia de comandă pentru gestiunea contactelor: validare, căutare,
                    sortare cronologică, export CSV și salvare automată în fișier.
                </p>
                <ul class="taguri">
                    <li>C++</li>
                    <li>STL</li>
                    <li>Fișiere</li>
                </ul>
            </article>

            <article class="card proiect solo" data-repo="" tabindex="0" role="button" aria-haspopup="dialog">
                <div class="proiect-cap">
                    <span class="icon">📄</span>
                    <span class="eticheta">solo · laborator</span>
                </div>
                <h3>CV web – laboratorul 2</h3>
                <p>
                    Primul meu CV ca pagină web, cu două teme de culori care se schimbă
                    dintr-un buton, cu JavaScript.
                </p>
                <ul class="taguri">
                    <li>HTML</li>
                    <li>CSS</li>
                    <li>JavaScript</li>
                </ul>
            </article>
        </section>

        <!-- ===== Team projects: cyan accent, team size and my role ===== -->
        <h2 class="titlu-sectiune">// Proiecte în echipă</h2>
        <section class="grid-proiecte">
            <article class="card proiect echipa" data-repo="seba-dvd/KubiDoIt" data-privat tabindex="0" role="button" aria-haspopup="dialog">
                <div class="proiect-cap">
                    <span class="icon">✅</span>
                    <span class="eticheta">echipă · 4 membri</span>
                </div>
                <h3>KubiDoIt</h3>
                <p>
                    Aplicație de tip to-do, web și mobil, pentru persoane și grupuri care împart sarcini.
                    Datele se sincronizează între web, Android și iOS. Proiect SOPM.
                </p>
                <p class="rol"><span>rolul meu:</span> dezvoltator backend – Supabase, securitate, sincronizare</p>
                <ul class="taguri">
                    <li>React</li>
                    <li>React Native</li>
                    <li>TypeScript</li>
                    <li>Supabase</li>
                    <li>PostgreSQL</li>
                </ul>
            </article>

            <article class="card proiect echipa" data-repo="hsilviu05/CodeStorm" tabindex="0" role="button" aria-haspopup="dialog">
                <div class="proiect-cap">
                    <span class="icon">📚</span>
                    <span class="eticheta">echipă · hackathon</span>
                </div>
                <h3>EduDocSync (CodeStorm)</h3>
                <p>
                    Realizat la hackathonul Transilvania CodeStorm @ MI (ediția a 2-a), într-o echipă de 4:
                    aplicație client-server care procesează documente academice în PDF
                    (planuri de învățământ, fișe de disciplină): extragere de text, OCR și dashboard.
                </p>
                <!-- TODO: write your role, e.g. "backend – OCR service" -->
                <p class="rol"><span>rolul meu:</span> TODO</p>
                <ul class="taguri">
                    <li>Python</li>
                    <li>PostgreSQL</li>
                    <li>OCR</li>
                    <li>React</li>
                </ul>
            </article>
        </section>

        <!-- ===== Achievements ===== -->
        <h2 class="titlu-sectiune">// Realizări</h2>
        <section class="card">
            <ul class="realizari">
                <li>TryHackMe – TODO: rank / număr de camere terminate</li>
                <li>HackTheBox – TODO: rank / mașini rezolvate</li>
                <!-- TODO: certificates, competitions -->
            </ul>
        </section>
    </main>

    <!-- ===== Preview window: filled by js/proiecte.js when a card is clicked ===== -->
    <dialog id="fereastra" aria-labelledby="fereastra-titlu">
        <div class="terminal-bara">
            <span class="buton rosu"></span>
            <span class="buton galben"></span>
            <span class="buton verde"></span>
            <span class="terminal-titlu" id="fereastra-cale"></span>
            <button class="inchide" type="button" aria-label="Închide fereastra">✕</button>
        </div>
        <div class="fereastra-corp">
            <h2 id="fereastra-titlu"></h2>
            <p id="fereastra-descriere"></p>
            <div id="fereastra-github"></div>
        </div>
    </dialog>

<?php require __DIR__ . '/includes/subsol.php'; ?>
