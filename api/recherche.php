<?php
/**
 * API de suggestions pour la recherche instantanée.
 * GET /api/recherche.php?q=...
 */
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$q = isset($_GET['q']) ? (string) $_GET['q'] : '';

try {
    $results = search_suggestions($q, 8);
    echo json_encode(['q' => $q, 'results' => $results], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['q' => $q, 'results' => [], 'error' => true]);
}
