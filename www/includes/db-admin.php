<?php
/**
 * Database functions of the admin panel. They all use the pai_admin account
 * (conectareBd(true)), the only one allowed to change or delete rows.
 * Every value goes through a prepared statement.
 */
require_once __DIR__ . '/db.php';

/** Runs a prepared statement on the admin connection and returns how many rows it changed. */
function executaAdmin(string $sql, string $tipuri, mixed ...$valori): int
{
    $cerere = mysqli_prepare(conectareBd(true), $sql);
    mysqli_stmt_bind_param($cerere, $tipuri, ...$valori);
    mysqli_stmt_execute($cerere);
    $randuri = mysqli_stmt_affected_rows($cerere);
    mysqli_stmt_close($cerere);
    return $randuri;
}

function gasesteLoc(int $id): ?array
{
    $cerere = mysqli_prepare(conectareBd(true), 'SELECT ID, lat, `long`, description FROM points WHERE ID = ?');
    mysqli_stmt_bind_param($cerere, 'i', $id);
    mysqli_stmt_execute($cerere);
    $rand = mysqli_fetch_assoc(mysqli_stmt_get_result($cerere));
    mysqli_stmt_close($cerere);
    return $rand ?: null;
}

function adaugaLoc(float $lat, float $lng, string $descriere): int
{
    executaAdmin('INSERT INTO points (lat, `long`, description) VALUES (?, ?, ?)', 'dds', $lat, $lng, $descriere);
    return mysqli_insert_id(conectareBd(true));
}

function modificaLoc(int $id, float $lat, float $lng, string $descriere): void
{
    executaAdmin('UPDATE points SET lat = ?, `long` = ?, description = ? WHERE ID = ?', 'ddsi', $lat, $lng, $descriere, $id);
}

function stergeLoc(int $id): bool
{
    return executaAdmin('DELETE FROM points WHERE ID = ?', 'i', $id) === 1;
}

/** All accounts, without the password hashes: the panel never needs them. */
function totiUtilizatorii(): array
{
    $rezultat = mysqli_query(conectareBd(true), 'SELECT ID, user FROM users ORDER BY ID');
    return mysqli_fetch_all($rezultat, MYSQLI_ASSOC);
}

function gasesteUtilizatorDupaId(int $id): ?array
{
    $cerere = mysqli_prepare(conectareBd(true), 'SELECT ID, user FROM users WHERE ID = ?');
    mysqli_stmt_bind_param($cerere, 'i', $id);
    mysqli_stmt_execute($cerere);
    $rand = mysqli_fetch_assoc(mysqli_stmt_get_result($cerere));
    mysqli_stmt_close($cerere);
    return $rand ?: null;
}

function stergeUtilizator(int $id): bool
{
    return executaAdmin('DELETE FROM users WHERE ID = ?', 'i', $id) === 1;
}
