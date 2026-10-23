// Preview window for the projects page.
// Click (or Enter) on a card -> a window opens with the card's details,
// plus live data read from GitHub's public API for public repositories.

const fereastra = document.getElementById('fereastra');
const titlu = document.getElementById('fereastra-titlu');
const cale = document.getElementById('fereastra-cale');
const descriere = document.getElementById('fereastra-descriere');
const zonaGithub = document.getElementById('fereastra-github');

// Small helper: create an element with text.
// textContent (not innerHTML) treats data as plain text, so nothing from
// GitHub's API can inject HTML or scripts into the page (no XSS).
function element(tag, text, clasa) {
    const el = document.createElement(tag);
    if (text) el.textContent = text;
    if (clasa) el.className = clasa;
    return el;
}

function deschide(card) {
    const repo = card.dataset.repo;              // "owner/name" or ""
    const privat = card.hasAttribute('data-privat');

    titlu.textContent = card.querySelector('h3').textContent;
    cale.textContent = repo ? '~/' + repo : '~/local';
    descriere.textContent = card.querySelector('p').textContent.trim();

    // The card's tags, copied into the window
    zonaGithub.replaceChildren(card.querySelector('.taguri').cloneNode(true));

    if (!repo) {
        zonaGithub.append(element('p', '> Codul nu este publicat pe GitHub.', 'nota'));
    } else if (privat) {
        zonaGithub.append(element('p', '🔒 Depozit privat: codul este disponibil la cerere.', 'nota'));
    } else {
        incarcaGithub(repo);
    }

    // Link to the project's own page (proiect.php?id=...)
    const detalii = card.querySelector('a.detalii');
    if (detalii) {
        const link = element('a', 'Pagina proiectului →', 'link');
        link.href = detalii.href;
        zonaGithub.append(element('p', null, 'fereastra-detalii'));
        zonaGithub.lastChild.append(link);
    }

    fereastra.showModal();
}

async function incarcaGithub(repo) {
    const stare = element('p', '> se încarcă datele de pe GitHub...', 'nota');
    zonaGithub.append(stare);

    const link = element('a', 'Deschide pe GitHub ↗', 'btn-github');
    link.href = 'https://github.com/' + repo;
    link.target = '_blank';                       // new tab
    link.rel = 'noopener noreferrer';             // the new tab can't control this page

    try {
        const [info, limbaje] = await Promise.all([
            fetch('https://api.github.com/repos/' + repo).then(r => r.ok ? r.json() : Promise.reject(r.status)),
            fetch('https://api.github.com/repos/' + repo + '/languages').then(r => r.ok ? r.json() : Promise.reject(r.status)),
        ]);

        // Languages as percentages of code size
        const total = Object.values(limbaje).reduce((a, b) => a + b, 0);
        const procente = Object.entries(limbaje)
            .map(([nume, bytes]) => nume + ' ' + Math.round(bytes / total * 100) + '%')
            .join(' · ');

        const lista = element('ul', null, 'github-info');
        lista.append(
            element('li', 'limbaje: ' + (procente || '-')),
            element('li', 'ultima actualizare: ' + new Date(info.pushed_at).toLocaleDateString('ro-RO')),
            element('li', 'stele: ' + info.stargazers_count + ' · fork-uri: ' + info.forks_count)
        );
        stare.replaceWith(lista);
    } catch (eroare) {
        // No internet, or GitHub's limit of 60 requests/hour was reached
        stare.textContent = '> nu am putut citi datele de pe GitHub (' + eroare + ').';
    }

    zonaGithub.append(link);
}

// Open on click, or on Enter/Space for keyboard users
document.querySelectorAll('.proiect').forEach(card => {
    card.addEventListener('click', e => {
        if (e.target.closest('a')) return;          // a click on "./detalii" follows the link
        deschide(card);
    });
    card.addEventListener('keydown', e => {
        if (e.target !== card) return;              // Enter on the link inside follows the link
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            deschide(card);
        }
    });
});

// Close: the ✕ button, the Esc key (built into <dialog>), or a click outside the window
fereastra.querySelector('.inchide').addEventListener('click', () => fereastra.close());
fereastra.addEventListener('click', e => {
    if (e.target === fereastra) fereastra.close();
});
