<?php
/**
 * Connexion à la base de données (PDO / MySQL)
 * Adaptez les identifiants à votre installation (Ampps par défaut : root / sans mot de passe)
 */

require_once __DIR__ . '/config.php';

// --- Paramètres MySQL ---
// Valeurs par défaut d'une installation AMPPS locale ; surchargeables par
// variables d'environnement en production.
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'togo_portal');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: 'mysql');
define('DB_CHARSET', 'utf8mb4');

/**
 * Retourne une instance PDO unique (singleton)
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            if (DEBUG) {
                die('Erreur de connexion à la base de données : ' . $e->getMessage());
            }
            die('Le service est temporairement indisponible. Merci de réessayer plus tard.');
        }
    }

    return $pdo;
}
