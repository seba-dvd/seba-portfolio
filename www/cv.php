<?php
$pagina = 'cv';
$titlu = 'CV';
$scripturi = ['js/cv.js'];
require __DIR__ . '/includes/antet.php';
?>

    <main>
        <div class="cv-actiuni">
            <button class="btn" type="button" id="descarca-pdf">Descarcă PDF</button>
            <p>Butonul deschide fereastra de printare: alege „Salvează ca PDF”.</p>
        </div>

        <!-- The formal CV: one sheet, printed as-is by css/style.css @media print -->
        <article class="cv">
            <header class="cv-antet">
                <h1>David Sebastian Pilat</h1>
                <p class="cv-rol">Student în anul IV, Telecomunicații · Securitate cibernetică</p>
                <p class="cv-contact">
                    Brașov, România ·
                    <a href="mailto:[EMAIL]">[EMAIL]</a> ·
                    <a href="https://github.com/seba-dvd">github.com/seba-dvd</a> ·
                    <a href="[LINKEDIN]">[LINKEDIN]</a>
                </p>
            </header>

            <section class="cv-sectiune">
                <h2>Profil</h2>
                <p>
                    Student în ultimul an la Telecomunicații, cu interes pentru securitatea rețelelor,
                    a protocoalelor radio și a dispozitivelor IoT. Lucrez la o platformă portabilă de
                    pentesting pe Raspberry Pi și caut un stagiu sau un post de junior ca pentester
                    sau security engineer într-un SOC.
                </p>
            </section>

            <section class="cv-sectiune">
                <h2>Experiență</h2>
                <div class="cv-intrare">
                    <div class="cv-rand">
                        <h3>Stagiu de practică – Dräxlmaier, Codlea</h3>
                        <span class="cv-perioada">Vara 2026</span>
                    </div>
                    <p class="cv-sub">Departamentul Electrics &amp; Applied Engineering</p>
                    <ul>
                        <li>Programare PLC și automatizări (CODESYS, Ladder, Structured Text)</li>
                        <li>Citirea și realizarea schemelor electrice</li>
                        <li>Testarea echipamentelor</li>
                    </ul>
                </div>
                <div class="cv-intrare">
                    <div class="cv-rand">
                        <h3>Barista; vânzător la un magazin de cartier</h3>
                        <span class="cv-perioada">[ANII]</span>
                    </div>
                    <p class="cv-sub">Lucru cu clienții, comunicare, lucru în echipă</p>
                </div>
            </section>

            <section class="cv-sectiune">
                <h2>Educație</h2>
                <div class="cv-intrare">
                    <div class="cv-rand">
                        <h3>Licență în Telecomunicații</h3>
                        <span class="cv-perioada">2023 – 2027</span>
                    </div>
                    <p class="cv-sub">Universitatea Transilvania din Brașov, Facultatea de Inginerie Electrică și Știința Calculatoarelor</p>
                </div>
                <div class="cv-intrare">
                    <div class="cv-rand">
                        <h3>Colegiul Național „Emil Racoviță”, Brașov</h3>
                        <span class="cv-perioada">2019 – 2023</span>
                    </div>
                    <p class="cv-sub">Profil Științe</p>
                </div>
            </section>

            <section class="cv-sectiune">
                <h2>Proiecte</h2>
                <ul>
                    <li><strong>Multi-Tool Pentesting Platform</strong> (lucrare de licență, 2026–2027): dispozitiv portabil pe Raspberry Pi pentru securitatea protocoalelor radio și IoT.</li>
                    <li><strong>Straja</strong>: platformă proprie de monitorizare și securitate a rețelei (Java, Spring Boot, Docker).</li>
                    <li><strong>KubiDoIt</strong> (echipă de 4): aplicație to-do web și mobil; rol: dezvoltator backend (Supabase, securitate).</li>
                    <li><strong>EduDocSync</strong>: hackathonul Transilvania CodeStorm @ MI, ediția a 2-a (Python, OCR, PostgreSQL).</li>
                    <li><strong>SimpleScanner</strong>: scanner de porturi TCP în Python, cu thread-uri.</li>
                </ul>
            </section>

            <section class="cv-sectiune">
                <h2>Competențe</h2>
                <dl class="cv-competente">
                    <dt>Programare</dt>
                    <dd>C, C++, Java, PHP, JavaScript, React, React Native, HTML/CSS, Python (bază)</dd>
                    <dt>Rețelistică</dt>
                    <dd>TCP/IP, subnetare, rutare, Wireshark, Cisco Packet Tracer, Wi-Fi (802.11), DNS, FTP, Profinet</dd>
                    <dt>Securitate</dt>
                    <dd>Kali Linux, Nmap, TryHackMe, HackTheBox, protocoale radio și IoT (Sub-1 GHz, NFC, RFID)</dd>
                    <dt>Instrumente</dt>
                    <dd>Linux, Git/GitHub, VS Code, Docker/WSL, MATLAB, Raspberry Pi, CODESYS / PLC</dd>
                    <dt>Limbi străine</dt>
                    <dd>[LIMBA – NIVEL]</dd>
                </dl>
            </section>

            <section class="cv-sectiune">
                <h2>Interese</h2>
                <p>
                    Fotbal (co-fondator FCP Steagu, Liga Pro Brașov, Divizia B), kickboxing,
                    motorsport românesc (raliuri, viteză în coastă).
                </p>
            </section>
        </article>
    </main>

<?php require __DIR__ . '/includes/subsol.php'; ?>
