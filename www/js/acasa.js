// Home page: the greeting is typed letter by letter, like in a terminal.
// The full text is already in the HTML (and in a hidden copy for screen readers),
// so without JavaScript, or with animations turned off, it simply stays as it is.

document.addEventListener('DOMContentLoaded', () => {
    const tinta = document.querySelector('.scris-text');
    if (!tinta || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const text = tinta.textContent;
    tinta.textContent = '';

    let i = 0;
    const scrie = () => {
        tinta.textContent = text.slice(0, ++i);
        if (i < text.length) {
            setTimeout(scrie, 70 + Math.random() * 60);   // a slightly uneven rhythm, like a person typing
        }
    };
    setTimeout(scrie, 400);
});
