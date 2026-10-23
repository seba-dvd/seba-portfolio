// Smooth transitions for every page.
// Loaded in <head> (not at the end of <body>) because "pagereveal" fires
// before the page is first drawn, so its listener must already exist.

const faraAnimatii = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Texts in the page's language, sent by PHP as JSON (includes/antet.php)
const TEXTE = JSON.parse(document.getElementById('texte-js')?.textContent || '{}');

// sessionStorage can be blocked (private mode, strict settings): never let that break the page
function citeste(cheie) {
    try { return sessionStorage.getItem(cheie); } catch (e) { return null; }
}
function scrie(cheie, valoare) {
    try { sessionStorage.setItem(cheie, valoare); } catch (e) { /* ignore */ }
}

const esteCyber = () => document.body.classList.contains('tema-cyber');

// ===== 1 + 2. Page transitions, with a special effect when the theme changes =====

// Leaving a page: remember which theme it had
window.addEventListener('pageswap', () => {
    scrie('temaAnterioara', esteCyber() ? 'cyber' : 'mint');
});

// Arriving on a page: compare the themes and pick the animation type
window.addEventListener('pagereveal', (e) => {
    if (!e.viewTransition) return;                 // no transition (first visit, old browser)

    const anterioara = citeste('temaAnterioara');
    const acum = esteCyber() ? 'cyber' : 'mint';

    if (anterioara === 'mint' && acum === 'cyber') {
        e.viewTransition.types.add('intra-cyber');
        arataAccesPermis();
    } else if (anterioara === 'cyber' && acum === 'mint') {
        e.viewTransition.types.add('iese-cyber');
    }
});

// The "> acces permis" flash, shown once when entering cyber mode
function arataAccesPermis() {
    const mesaj = document.createElement('div');
    mesaj.className = 'acces';
    mesaj.textContent = TEXTE.acces || '> acces permis_';
    mesaj.setAttribute('aria-hidden', 'true');      // decoration: screen readers skip it
    mesaj.addEventListener('animationend', () => mesaj.remove());
    document.body.append(mesaj);
}

document.addEventListener('DOMContentLoaded', () => {

    // ===== 3. Cards appear as you scroll to them =====
    const carduri = document.querySelectorAll('.card');

    if (!faraAnimatii && 'IntersectionObserver' in window) {
        const observator = new IntersectionObserver((intrari) => {
            intrari.forEach((intrare) => {
                if (!intrare.isIntersecting) return;
                const card = intrare.target;
                card.classList.add('vizibil');
                observator.unobserve(card);          // animate only once
                // When the entrance is over, give the card back its normal hover behaviour
                card.addEventListener('transitionend', () => {
                    card.classList.remove('apare', 'vizibil');
                    card.style.transitionDelay = '';
                }, { once: true });
            });
        }, { threshold: 0.15 });                     // 15% of the card must be visible

        carduri.forEach((card, i) => {
            card.classList.add('apare');             // hidden only when JS runs: without JS, cards stay visible
            card.style.transitionDelay = (i % 4) * 80 + 'ms';   // small cascade
            observator.observe(card);
        });
    }

    // ===== 4. Theme switch button =====
    const buton = document.querySelector('.comuta-tema');
    if (!buton) return;

    buton.addEventListener('click', () => {
        const comuta = () => document.body.classList.toggle('tema-cyber');

        // Fallback: browsers without View Transitions, or animations turned off
        if (!document.startViewTransition || faraAnimatii) {
            comuta();
            return;
        }

        // Circle growing from the button's centre until it covers the whole screen
        const r = buton.getBoundingClientRect();
        const x = r.left + r.width / 2;
        const y = r.top + r.height / 2;
        const raza = Math.hypot(Math.max(x, innerWidth - x), Math.max(y, innerHeight - y));

        document.documentElement.classList.add('comutare');
        const tranzitie = document.startViewTransition(comuta);

        tranzitie.ready.then(() => {
            document.documentElement.animate(
                { clipPath: [`circle(0px at ${x}px ${y}px)`, `circle(${raza}px at ${x}px ${y}px)`] },
                { duration: 650, easing: 'ease-in-out', pseudoElement: '::view-transition-new(root)' }
            );
        });
        tranzitie.finished.finally(() => document.documentElement.classList.remove('comutare'));
    });
});
