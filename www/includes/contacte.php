<?php
/**
 * My public contact links, in one place: the home page, the footer, the
 * contact page and the CV all read them from here.
 * An empty value is not shown anywhere (no "[EMAIL]" placeholders on the site).
 */
require_once __DIR__ . '/iconite.php';

const CONTACTE = [
    'github'   => 'https://github.com/seba-dvd',
    'linkedin' => '',       // TODO: the LinkedIn profile, e.g. https://www.linkedin.com/in/...
    'email'    => '',       // TODO: the email address for the public site
];

const NUME_CONTACTE = [
    'github'   => 'GitHub',
    'linkedin' => 'LinkedIn',
    'email'    => 'Email',
];

/**
 * The filled-in contacts, ready for a link:
 * [cheie, nume, href, text (shown address), extern (opens in a new tab)].
 */
function linkuriContact(): array
{
    $linkuri = [];
    foreach (CONTACTE as $cheie => $valoare) {
        if ($valoare === '') {
            continue;
        }
        $email = $cheie === 'email';
        $linkuri[] = [
            'cheie'  => $cheie,
            'nume'   => NUME_CONTACTE[$cheie],
            'href'   => $email ? 'mailto:' . $valoare : $valoare,
            'text'   => $email ? $valoare : preg_replace('#^https?://(www\.)?#', '', $valoare),
            'extern' => !$email,
        ];
    }
    return $linkuri;
}

/**
 * The row of social icons (home page and footer). $eticheta names the list for screen readers.
 */
function htmlSociale(string $eticheta, string $clasa = 'social'): string
{
    $html = '<ul class="' . e($clasa) . '" aria-label="' . e($eticheta) . '">';
    foreach (linkuriContact() as $link) {
        $extern = $link['extern'] ? ' target="_blank" rel="noopener noreferrer"' : '';
        $html .= '<li><a href="' . e($link['href']) . '" aria-label="' . e($link['nume']) . '"' . $extern . '>'
            . iconita($link['cheie']) . '</a></li>';
    }
    return $html . '</ul>';
}
