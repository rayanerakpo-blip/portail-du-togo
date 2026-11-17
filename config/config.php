<?php
/**
 * Configuration générale du portail
 * Portail officiel du Togo - version "gouv.tg" simplifiée
 */

// --- Paramètres du site ---
define('SITE_NAME', 'Portail du Togo');
define('SITE_TAGLINE', 'Toutes les démarches et services publics togolais en un seul endroit');

// --- URL de base (auto-détectée) ---
// Le site fonctionne qu'il soit servi depuis /TOGO, /www/TOGO ou une racine dédiée.
// Vous pouvez forcer une valeur en décommentant la ligne ci-dessous.
// define('BASE_URL', '/TOGO');
if (!defined('BASE_URL')) {
    $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']) ?: '') : '';
    $appRoot = str_replace('\\', '/', realpath(dirname(__DIR__)) ?: '');
    $base = ($docRoot !== '' && str_starts_with($appRoot, $docRoot)) ? substr($appRoot, strlen($docRoot)) : '';
    define('BASE_URL', $base === '' ? '' : '/' . trim($base, '/'));
}

define('ADMIN_EMAIL', 'contact@togo.tg');

// --- Fuseau horaire ---
date_default_timezone_set('Africa/Lome');

// --- Débogage (mettre à false en production) ---
define('DEBUG', true);
if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Démarrer la session pour l'admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
