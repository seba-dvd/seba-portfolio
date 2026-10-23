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
