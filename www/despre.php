<?php
$pagina = 'despre';
$titlu = 'Despre';
require __DIR__ . '/includes/antet.php';
?>

    <main>
        <!-- ===== Intro: the story + what I'm working on now ===== -->
        <section class="despre-intro">
            <article class="card">
                <h1>Despre mine</h1>
                <p>
                    Sunt David, student în ultimul an la Telecomunicații, la Universitatea Transilvania din Brașov.
                    În prezent lucrez la lucrarea de licență: un multi-tool portabil de pentesting, construit pe
                    Raspberry Pi, care testează securitatea dispozitivelor radio și IoT. Practic, e propriul meu
                    Flipper Zero, cu câteva trucuri în plus. Țintesc o carieră în securitate cibernetică, ca pentester
                    sau security engineer într-un SOC. Mă gândesc serios și să-mi pun cunoștințele în slujba țării,
                    la MApN, MAI sau SRI.
                </p>
                <p>
                    Totul a început în fața televizorului. Când eram mic, mă uitam la toate filmele cu forțe speciale
                    și spioni pe care le găseam. În timp ce ceilalți urmăreau acțiunea, eu mă uitam la gadgeturi.
                    Cum funcționează stația aia? Cum au intrat în sistem? N-am încetat niciodată să întreb.
                </p>
                <p>
                    Întrebările astea m-au adus la Telecomunicații, unde am descoperit ce mă fascinează și azi:
                    partea invizibilă a tehnologiei. Semnalele zboară prin aer și pachetele traversează rețelele în
                    fiecare secundă, fără ca cineva să le observe. Pe parcurs, stagiul de la Dräxlmaier mi-a arătat
                    cum funcționează sistemele industriale, iar fiecare laborator și proiect m-a adus mai aproape de
                    securitate. În afara laboratorului mă găsești pe teren cu echipa mea, la sală sau urmărind o
                    probă de raliu undeva prin țară.
                </p>
            </article>

            <!-- A highlighted card: the current focus -->
            <aside class="card acum">
                <p class="acum-eticheta">Acum lucrez la</p>
                <h2>Multi-Tool Pentesting Platform</h2>
                <p>
                    Lucrarea de licență: un dispozitiv portabil pe Raspberry Pi pentru testarea securității
                    protocoalelor radio și IoT.
                </p>
                <ul class="taguri">
                    <li>Raspberry Pi</li>
                    <li>Sub-1 GHz</li>
                    <li>NFC</li>
                    <li>RFID</li>
                </ul>
            </aside>
        </section>

        <!-- ===== Hobbies ===== -->
        <h2 class="titlu-sectiune">Hobby-uri</h2>
        <section class="grid-hobby">
            <article class="card">
                <span class="icon">⚽</span>
                <h3>Fotbal</h3>
                <p>
                    Am jucat ca fundaș central la trei cluburi din Brașov între 9 și 15 ani, iar de la 17 ani joc
                    minifotbal, acum la FCP Steagu, echipa pe care am construit-o cu trei prieteni în
                    Liga Pro Brașov, Divizia B.
                </p>
            </article>

            <article class="card">
                <span class="icon">🥊</span>
                <h3>Kickboxing și sală</h3>
                <p>
                    Filmele cu spioni și MMA m-au adus la sporturile de contact, iar kickboxingul și sala sunt
                    timpul meu, în care scap de stres și mă dezvolt.
                </p>
            </article>

            <article class="card">
                <span class="icon">🏁</span>
                <h3>Motorsport românesc</h3>
                <p>
                    De când am descoperit anul trecut raliul și viteza în coastă, urmăresc fiecare etapă pe care
                    o prind, pentru că iubesc mașinile și adrenalina unei curse.
                </p>
            </article>

            <article class="card">
                <span class="icon">🎮</span>
                <h3>Jocuri și filme</h3>
                <p>
                    Joc EA FC, UFC și F1 și mă uit la filme de acțiune, comedie și tehnologie,
                    dar și la documentare.
                </p>
            </article>
        </section>

        <!-- ===== Skills: one card per category ===== -->
        <h2 class="titlu-sectiune">Competențe</h2>
        <section class="grid-hobby">
            <article class="card">
                <h3>Programare</h3>
                <ul class="taguri">
                    <li>C</li>
                    <li>C++</li>
                    <li>Java</li>
                    <li>PHP</li>
                    <li>Python (bază)</li>
                    <li>JavaScript</li>
                    <li>React</li>
                    <li>React Native</li>
                    <li>HTML/CSS</li>
                </ul>
            </article>

            <article class="card">
                <h3>Rețelistică</h3>
                <ul class="taguri">
                    <li>TCP/IP</li>
                    <li>Subnetare</li>
                    <li>Rutare</li>
                    <li>Wireshark</li>
                    <li>Cisco Packet Tracer</li>
                    <li>Wi-Fi (802.11)</li>
                    <li>DNS</li>
                    <li>FTP</li>
                    <li>Profinet</li>
                </ul>
            </article>

            <article class="card">
                <h3>Securitate cibernetică</h3>
                <ul class="taguri">
                    <li>Kali Linux</li>
                    <li>Nmap</li>
                    <li>TryHackMe</li>
                    <li>HackTheBox</li>
                    <li>Sub-1 GHz</li>
                    <li>NFC</li>
                    <li>RFID</li>
                </ul>
            </article>

            <article class="card">
                <h3>Instrumente</h3>
                <ul class="taguri">
                    <li>Linux</li>
                    <li>Git/GitHub</li>
                    <li>VS Code</li>
                    <li>Docker/WSL</li>
                    <li>MATLAB</li>
                    <li>Raspberry Pi</li>
                    <li>CODESYS / PLC</li>
                    <li>Ladder, Structured Text</li>
                </ul>
            </article>
        </section>

        <!-- ===== Timeline: newest first ===== -->
        <h2 class="titlu-sectiune">Parcurs</h2>
        <section class="card">
            <ol class="timeline">
                <li>
                    <span class="an">2026 – 2027</span>
                    Lucrare de licență: Multi-Tool Pentesting Platform pe Raspberry Pi, pentru securitatea
                    protocoalelor radio și IoT
                </li>
                <li>
                    <span class="an">Vara 2026</span>
                    Stagiu de practică la Dräxlmaier (Codlea), departamentul Electrics &amp; Applied Engineering:
                    programare PLC, automatizări, scheme electrice, testare echipamente
                </li>
                <li>
                    <span class="an">2026</span>
                    Hackathonul Transilvania CodeStorm @ MI (ediția a 2-a)
                </li>
                <li>
                    <span class="an">2023 – 2027</span>
                    Licență în Telecomunicații, Universitatea Transilvania din Brașov
                </li>
                <li>
                    <!-- TODO: the year FCP Steagu was founded -->
                    <span class="an">TODO: anul</span>
                    Am co-fondat FCP Steagu, echipă de minifotbal în Liga Pro Brașov, Divizia B
                </li>
                <li>
                    <!-- TODO: the years -->
                    <span class="an">TODO: anii</span>
                    Barista; vânzător la un magazin de cartier
                </li>
                <li>
                    <span class="an">2019 – 2023</span>
                    Colegiul Național „Emil Racoviță” Brașov, profil Științe
                </li>
                <li>
                    <span class="an">9 – 15 ani</span>
                    Jucător de fotbal la trei cluburi din Brașov
                </li>
            </ol>
        </section>
    </main>

<?php require __DIR__ . '/includes/subsol.php'; ?>
