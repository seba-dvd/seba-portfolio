<?php
$pagina = 'contact';
require __DIR__ . '/includes/antet.php';
?>

    <main>
        <section class="card contact-intro">
            <h1><?= e($t['titluri']['contact']) ?></h1>
            <p><?= e($t['contact_text']) ?></p>
        </section>

        <section class="grid-hobby">
            <!-- TODO: the email you want on the public site -->
            <a class="card contact-card" href="mailto:[EMAIL]">
                <span class="icon">✉️</span>
                <h3>Email</h3>
                <p>[EMAIL]</p>
            </a>

            <a class="card contact-card" href="https://github.com/seba-dvd" target="_blank" rel="noopener noreferrer">
                <span class="icon">💻</span>
                <h3>GitHub</h3>
                <p>github.com/seba-dvd</p>
            </a>

            <!-- TODO: your LinkedIn profile address -->
            <a class="card contact-card" href="[LINKEDIN]" target="_blank" rel="noopener noreferrer">
                <span class="icon">🔗</span>
                <h3>LinkedIn</h3>
                <p>[LINKEDIN]</p>
            </a>

            <div class="card contact-card">
                <span class="icon">📍</span>
                <h3><?= e($t['contact_locatie']) ?></h3>
                <p><?= e($t['locatie']) ?></p>
            </div>
        </section>
    </main>

<?php require __DIR__ . '/includes/subsol.php'; ?>
