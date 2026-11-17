<?php
/**
 * Sitemap XML dynamique.
 * Accessible sur /TOGO/sitemap.php
 */
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/xml; charset=utf-8');

$urls = [];

// Pages statiques
foreach ([
    'index.php'                => '1.0',
    'pages/demarches.php'      => '0.9',
    'pages/ministeres.php'     => '0.8',
    'pages/actualites.php'     => '0.8',
    'pages/urgences.php'       => '0.7',
    'pages/a-propos.php'       => '0.6',
    'pages/contact.php'        => '0.5',
] as $path => $prio) {
    $urls[] = ['loc' => site_url($path), 'priority' => $prio, 'lastmod' => date('Y-m-d'), 'freq' => 'weekly'];
}

// Démarches
foreach (db()->query('SELECT slug FROM demarches WHERE actif = 1')->fetchAll(PDO::FETCH_COLUMN) as $slug) {
    $urls[] = ['loc' => site_url('pages/demarche.php?slug=' . urlencode($slug)), 'priority' => '0.7', 'lastmod' => date('Y-m-d'), 'freq' => 'monthly'];
}
// Ministères
foreach (db()->query('SELECT slug FROM ministeres WHERE actif = 1')->fetchAll(PDO::FETCH_COLUMN) as $slug) {
    $urls[] = ['loc' => site_url('pages/ministere.php?slug=' . urlencode($slug)), 'priority' => '0.6', 'lastmod' => date('Y-m-d'), 'freq' => 'monthly'];
}
// Actualités
foreach (db()->query('SELECT slug, date_pub FROM actualites WHERE actif = 1')->fetchAll() as $a) {
    $urls[] = ['loc' => site_url('pages/actualite.php?slug=' . urlencode($a['slug'])), 'priority' => '0.6',
               'lastmod' => date('Y-m-d', strtotime($a['date_pub'])), 'freq' => 'monthly'];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $u) {
    echo '  <url>' . "\n";
    echo '    <loc>' . e($u['loc']) . '</loc>' . "\n";
    echo '    <lastmod>' . e($u['lastmod']) . '</lastmod>' . "\n";
    echo '    <changefreq>' . e($u['freq']) . '</changefreq>' . "\n";
    echo '    <priority>' . e($u['priority']) . '</priority>' . "\n";
    echo '  </url>' . "\n";
}
echo '</urlset>';
