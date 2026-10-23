<?php
/**
 * Small helpers used by every page.
 */

/**
 * Escapes text for safe output inside HTML (protects against XSS).
 * Every value printed into a page goes through e(), even our own texts:
 * one habit everywhere is safer than deciding case by case.
 */
function e(?string $text): string
{
    return htmlspecialchars($text ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Picks the current language from a text that exists in both languages:
 * tr(['ro' => 'Salut', 'en' => 'Hi']). Falls back to Romanian.
 * The language is set by includes/limba.php (constant LIMBA).
 */
function tr(?array $texte)
{
    if ($texte === null) {
        return null;
    }
    $limba = defined('LIMBA') ? LIMBA : 'ro';
    return $texte[$limba] ?? $texte['ro'];
}

/**
 * Finds a project by its id. Returns null when there is none.
 */
function gasesteProiect(array $proiecte, int $id): ?array
{
    foreach ($proiecte as $proiect) {
        if ($proiect['id'] === $id) {
            return $proiect;
        }
    }
    return null;
}
