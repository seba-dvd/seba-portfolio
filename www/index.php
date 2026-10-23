<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>David Sebastian Pilat – Acasă</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/tranzitii.js"></script>
</head>

<body>
    <header>
        <nav>
            <a class="logo" href="index.php">Seba<span>.</span></a>
            <div class="meniu">
                <a class="activ" href="index.php">Acasă</a>
                <a href="despre.php">Despre</a>
                <a href="proiecte.php">Proiecte</a>
                <a href="cv.php">CV</a>
                <a href="contact.php">Contact</a>
                <button class="comuta-tema" type="button" aria-label="Schimbă tema"><span class="tema-ic-cyber">&gt;_</span><span class="tema-ic-mint">🌿</span></button>
            </div>
        </nav>
    </header>

    <main>
        <!-- ===== Hero: text on the left, photo on the right ===== -->
        <section class="hero">
            <div class="hero-text">
                <p class="salut">Salut, eu sunt</p>
                <h1>David Sebastian Pilat</h1>
                <p class="tagline">
                    Student în anul IV la Tehnologii și Sisteme de Telecomunicații,
                    pasionat de <strong>cyber security</strong>, <strong>networking</strong>
                    și <strong>programare</strong>.
                </p>
                <p class="locatie">Brașov, România</p>
                <div class="butoane">
                    <a class="btn" href="despre.php">Despre mine</a>
                    <a class="btn btn-cyber" href="proiecte.php">&gt; modul cyber</a>
                </div>
            </div>

            <div class="hero-poza">
                <img class="poza" src="img/profil.jpeg" alt="Fotografia lui David Sebastian Pilat">
                <span class="insigna insigna-1">Cyber security</span>
                <span class="insigna insigna-2">Networking</span>
                <span class="insigna insigna-3">Gamer</span>
            </div>
        </section>

        <!-- ===== Bento grid: cards of different sizes ===== -->
        <section class="bento">
            <article class="card despre">
                <h2>Despre mine</h2>
                <p>
                    Lumea din jurul meu mă cunoaște ca Seba. Îmi place să explorez tehnologii noi
                    și să aplic ce învăț în proiecte practice. Sunt o persoană activă:
                    sport, jocuri video și multă curiozitate.
                </p>
                <a class="link" href="despre.php">Citește mai mult →</a>
            </article>

            <article class="card hobby">
                <h2>Hobby-uri</h2>
                <ul class="lista-hobby">
                    <li><span>⚽</span> Sport</li>
                    <li><span>🎮</span> Jocuri video</li>
                    <li><span>💻</span> Tehnologie</li>
                </ul>
            </article>

            <article class="card educatie">
                <h2>Educație</h2>
                <ol class="timeline">
                    <li>
                        <span class="an">2023 – prezent</span>
                        Facultatea de Inginerie Electrică și Știința Calculatoarelor, Universitatea Transilvania din Brașov
                    </li>
                    <li>
                        <span class="an">2019 – 2023</span>
                        Colegiul Național „Emil Racoviță” Brașov
                    </li>
                    <li>
                        <span class="an">2011 – 2019</span>
                        Școala Gimnazială nr. 3 „Gheorghe Lazăr” Brașov
                    </li>
                </ol>
            </article>

            <article class="card competente">
                <h2>Competențe</h2>
                <ul class="taguri">
                    <li>C</li>
                    <li>C++</li>
                    <li>Python</li>
                    <li>Java</li>
                    <li>HTML</li>
                    <li>CSS</li>
                    <li>JavaScript</li>
                    <li>Wireshark</li>
                    <li>Cisco Packet Tracer</li>
                    <li>TryHackMe</li>
                    <li>HackTheBox</li>
                    <li>Linux</li>
                    <li>VS Code</li>
                </ul>
            </article>

            <!-- This card switches to the cyber theme on its own -->
            <article class="card proiecte tema-cyber">
                <h2>&gt; ls ./proiecte</h2>
                <p>Proiecte din facultate, laboratoare și realizări din cyber security.</p>
                <a class="link" href="proiecte.php">./deschide.sh →</a>
            </article>

            <article class="card harta">
                <h2>Harta călătoriilor</h2>
                <p>Orașele pe care le-am vizitat și cele care sunt pe lista mea.</p>
                <p class="legenda">
                    <span class="punct vizitat"></span> vizitate
                    <span class="punct dorit"></span> de vizitat
                </p>
                <a class="link" href="login.php">Vezi harta →</a>
            </article>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 David Sebastian Pilat</p>
    </footer>
</body>
</html>
