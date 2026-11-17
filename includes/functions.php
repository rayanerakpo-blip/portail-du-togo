<?php
/**
 * Fonctions utilitaires du portail
 */

require_once __DIR__ . '/../config/database.php';

/** Échappe une chaîne pour l'affichage HTML */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Construit une URL interne au site */
function url(string $path = ''): string
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

/** Construit l'URL d'un asset */
function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

/** Transforme un texte en slug */
function slugify(string $text): string
{
    $map = [
        'à'=>'a','á'=>'a','â'=>'a','ã'=>'a','ä'=>'a','å'=>'a','À'=>'A','Á'=>'A','Â'=>'A','Ã'=>'A','Ä'=>'A','Å'=>'A',
        'æ'=>'ae','Æ'=>'AE',
        'ç'=>'c','Ç'=>'C',
        'è'=>'e','é'=>'e','ê'=>'e','ë'=>'e','È'=>'E','É'=>'E','Ê'=>'E','Ë'=>'E',
        'ì'=>'i','í'=>'i','î'=>'i','ï'=>'i','Ì'=>'I','Í'=>'I','Î'=>'I','Ï'=>'I',
        'ñ'=>'n','Ñ'=>'N',
        'ò'=>'o','ó'=>'o','ô'=>'o','õ'=>'o','ö'=>'o','ø'=>'o','Ò'=>'O','Ó'=>'O','Ô'=>'O','Õ'=>'O','Ö'=>'O','Ø'=>'O',
        'œ'=>'oe','Œ'=>'OE',
        'ù'=>'u','ú'=>'u','û'=>'u','ü'=>'u','Ù'=>'U','Ú'=>'U','Û'=>'U','Ü'=>'U',
        'ý'=>'y','ÿ'=>'y','Ý'=>'Y','ß'=>'ss',
        '’'=>'-',"'"=>'-',
    ];
    $text = strtr($text, $map);
    $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
    $text = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $text) ?? '');
    return trim($text, '-');
}

/** Redirige vers une URL interne */
function redirect(string $path): void
{
    header('Location: ' . (preg_match('#^https?://#', $path) ? $path : url($path)));
    exit;
}

/** Limite un texte à N mots */
function excerpt(?string $text, int $words = 25): string
{
    $text = trim(strip_tags((string) $text));
    $parts = preg_split('/\s+/', $text) ?: [];
    if (count($parts) <= $words) {
        return $text;
    }
    return implode(' ', array_slice($parts, 0, $words)) . '…';
}

/** Transforme un texte multiligne en liste HTML */
function textToLines(?string $text): array
{
    if (!$text) {
        return [];
    }
    $lines = array_map('trim', preg_split('/\r\n|\r|\n/', $text) ?: []);
    return array_values(array_filter($lines, fn($l) => $l !== ''));
}

/* ---------------------------------------------------------
 *  Sécurité / CSRF
 * -------------------------------------------------------*/

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    $token = $_POST['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
        http_response_code(419);
        die('Session expirée ou jeton invalide. Merci de recharger la page.');
    }
}

/* ---------------------------------------------------------
 *  Messages flash
 * -------------------------------------------------------*/

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flashes(): array
{
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

/* ---------------------------------------------------------
 *  Requêtes fréquentes
 * -------------------------------------------------------*/

function get_categories(): array
{
    return db()->query('SELECT * FROM categories ORDER BY ordre, nom')->fetchAll();
}

function get_urgences(int $limit = 0): array
{
    $sql = 'SELECT * FROM urgences WHERE actif = 1 ORDER BY ordre, nom';
    if ($limit > 0) {
        $sql .= ' LIMIT ' . (int) $limit;
    }
    return db()->query($sql)->fetchAll();
}

function demarche_url(array $d): string
{
    return url('pages/demarche.php?slug=' . urlencode($d['slug']));
}

function ministere_url(array $m): string
{
    return url('pages/ministere.php?slug=' . urlencode($m['slug']));
}

function actualite_url(array $a): string
{
    return url('pages/actualite.php?slug=' . urlencode($a['slug']));
}

/** Formate une date MySQL en français */
function date_fr(?string $date): string
{
    if (!$date) {
        return '';
    }
    $ts = strtotime($date);
    if (!$ts) {
        return (string) $date;
    }
    $jours = ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
    $mois = ['', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
    return sprintf(
        '%s %d %s %d',
        $jours[(int) date('w', $ts)],
        (int) date('j', $ts),
        $mois[(int) date('n', $ts)],
        (int) date('Y', $ts)
    );
}

/** Icône SVG (jeu minimal intégré) */
function icon(string $name, int $size = 24, string $class = ''): string
{
    $paths = [
        'search'      => '<circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/>',
        'id-card'     => '<rect x="2" y="5" width="20" height="14" rx="2"/><circle cx="8" cy="11" r="2"/><path d="M5 16c0-1.7 1.3-3 3-3s3 1.3 3 3M14 9h5M14 13h3"/>',
        'car'         => '<path d="M5 17h14M6 17v2M18 17v2M3 13l2-6h14l2 6v4H3z"/><circle cx="7.5" cy="14" r="1"/><circle cx="16.5" cy="14" r="1"/>',
        'receipt'     => '<path d="M5 3v18l2-1 2 1 2-1 2 1 2-1 2 1V3l-2 1-2-1-2 1-2-1-2 1z"/><path d="M8 8h8M8 12h8M8 16h5"/>',
        'scale'       => '<path d="M12 3v18M6 7h12M6 7l-3 6h6zM18 7l-3 6h6zM8 21h8"/>',
        'heart-pulse' => '<path d="M12 21s-7-4.5-9.5-9A5 5 0 0 1 12 6a5 5 0 0 1 9.5 6C19 16.5 12 21 12 21z"/><path d="M4 12h4l1.5-3 3 6 1.5-3h5"/>',
        'graduation-cap' => '<path d="M2 9l10-5 10 5-10 5z"/><path d="M6 11v5c0 1.5 3 3 6 3s6-1.5 6-3v-5"/>',
        'briefcase'   => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 12h18"/>',
        'baby'        => '<circle cx="12" cy="8" r="5"/><path d="M9 7h.01M15 7h.01M10.5 10.5c1 .8 2 .8 3 0M9 22v-4h6v4M12 13v5"/>',
        'folder'      => '<path d="M3 6a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>',
        'phone'       => '<path d="M4 4h4l2 5-2.5 1.5a12 12 0 0 0 6 6L15 14l5 2v4a16 16 0 0 1-16-16z"/>',
        'shield'      => '<path d="M12 3l8 3v6c0 5-3.5 8.5-8 9-4.5-.5-8-4-8-9V6z"/>',
        'fire'        => '<path d="M12 3s4 4 4 8a4 4 0 0 1-8 0c0-1 .5-2 .5-2S6 12 6 15a6 6 0 0 0 12 0c0-4-6-12-6-12z"/>',
        'plus'        => '<path d="M12 5v14M5 12h14"/>',
        'child'       => '<circle cx="12" cy="6" r="3"/><path d="M12 9v7M8 12h8M9 22l3-6 3 6"/>',
        'heart'       => '<path d="M12 21s-7-4.5-9.5-9A5 5 0 0 1 12 6a5 5 0 0 1 9.5 6C19 16.5 12 21 12 21z"/>',
        'alert'       => '<path d="M12 3l10 18H2z"/><path d="M12 9v5M12 17h.01"/>',
        'bolt'        => '<path d="M13 2L4 14h7l-1 8 9-12h-7z"/>',
        'clock'       => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'map-pin'     => '<path d="M12 21s-7-6-7-11a7 7 0 0 1 14 0c0 5-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/>',
        'money'       => '<rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="3"/>',
        'check'       => '<path d="M4 12l5 5L20 6"/>',
        'globe'       => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.5 2.5 15 0 18M12 3c-2.5 2.5-2.5 15 0 18"/>',
        'building'    => '<path d="M4 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16M4 21h16M8 7h2M8 11h2M8 15h2M14 9h4v12"/>',
        'chevron-right' => '<path d="M9 6l6 6-6 6"/>',
        'arrow-right' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'external'    => '<path d="M14 3h7v7M21 3l-9 9M19 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h5"/>',
        'mail'        => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
        'menu'        => '<path d="M4 6h16M4 12h16M4 18h16"/>',
        'x'           => '<path d="M6 6l12 12M18 6L6 18"/>',
        'edit'        => '<path d="M4 20h4L18 10l-4-4L4 16z"/><path d="M14 6l4 4"/>',
        'trash'       => '<path d="M4 7h16M9 7V5h6v2M6 7l1 13h10l1-13"/>',
        'plus-circle' => '<circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/>',
        'logout'      => '<path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l-5-5 5-5M5 12h12"/>',
        'list'        => '<path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>',
        'user'        => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.5-7 8-7s8 3 8 7"/>',
        'printer'     => '<path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/>',
        'share'       => '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="M8.6 13.5l6.8 4M15.4 6.5l-6.8 4"/>',
    ];
    $p = $paths[$name] ?? $paths['folder'];
    $cls = $class ? ' class="' . e($class) . '"' : '';
    return '<svg' . $cls . ' xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size
        . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" '
        . 'stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $p . '</svg>';
}

/* ---------------------------------------------------------
 *  Symboles nationaux
 * -------------------------------------------------------*/

/** Drapeau du Togo en SVG */
function togo_flag(int $w = 46): string
{
    $h = (int) round($w * 2 / 3);
    return '<svg class="flag" xmlns="http://www.w3.org/2000/svg" width="' . $w . '" height="' . $h
        . '" viewBox="0 0 60 40" role="img" aria-label="Drapeau du Togo">'
        . '<rect width="60" height="40" fill="#006a4e"/>'
        . '<rect y="8" width="60" height="8" fill="#ffce00"/>'
        . '<rect y="16" width="60" height="8" fill="#006a4e"/>'
        . '<rect y="24" width="60" height="8" fill="#ffce00"/>'
        . '<rect y="32" width="60" height="8" fill="#006a4e"/>'
        . '<rect width="24" height="24" fill="#d21034"/>'
        . '<path d="M12 4l2.35 4.76 5.25.76-3.8 3.7.9 5.23L12 15.98l-4.7 2.47.9-5.23-3.8-3.7 5.25-.76z" fill="#fff"/>'
        . '</svg>';
}

/**
 * Armoiries officielles de la République Togolaise :
 * deux lions rouges tenant chacun un arc et une flèche, bouclier
 * doré « RT », drapeaux togolais croisés et devise « Travail · Liberté · Patrie ».
 * (SVG officiel — 750 × 1080)
 */
function togo_emblem(int $w = 40, string $class = 'emblem'): string
{
    $ratio = 1080 / 750;                       // les armoiries sont en portrait
    $h = (int) round($w * $ratio);
    return '<img class="' . e($class) . '" src="' . asset('img/armoiries-togo.svg')
        . '" width="' . $w . '" height="' . $h . '" alt="Armoiries de la République Togolaise" '
        . 'decoding="async">';
}

/* ---------------------------------------------------------
 *  URL absolues, SEO & données structurées
 * -------------------------------------------------------*/

/** URL absolue du site (pour canonical, Open Graph, sitemap…) */
function site_url(string $path = ''): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host . url($path);
}

/** URL absolue de la page courante (sans la query string) */
function current_url(): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $path   = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
    return $scheme . '://' . $host . $path;
}

/** Rendu d'un bloc de données structurées JSON-LD */
function json_ld(array $data): string
{
    return '<script type="application/ld+json">'
        . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        . '</script>';
}

/** Données structurées par défaut (organisation + moteur de recherche) */
function default_structured_data(): array
{
    return [
        [
            '@context' => 'https://schema.org',
            '@type'    => 'GovernmentOrganization',
            'name'     => SITE_NAME,
            'url'      => site_url('index.php'),
            'logo'     => site_url('assets/img/armoiries-togo.svg'),
            'areaServed' => 'TG',
            'address'  => [
                '@type' => 'PostalAddress',
                'addressCountry' => 'TG',
                'addressLocality' => 'Lomé',
            ],
        ],
        [
            '@context' => 'https://schema.org',
            '@type'    => 'WebSite',
            'name'     => SITE_NAME,
            'url'      => site_url('index.php'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => site_url('pages/recherche.php') . '?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ],
    ];
}

/* ---------------------------------------------------------
 *  Recherche instantanée (suggestions)
 * -------------------------------------------------------*/

/** Icône/nom lisibles pour un type de résultat */
function type_label(string $type): string
{
    return [
        'demarche'   => 'Démarche',
        'ministere'  => 'Ministère',
        'actualite'  => 'Actualité',
        'categorie'  => 'Catégorie',
    ][$type] ?? ucfirst($type);
}

/** Recherche de suggestions (utilisée par api/recherche.php) */
function search_suggestions(string $q, int $limit = 8): array
{
    $q = trim($q);
    if (mb_strlen($q) < 2) {
        return [];
    }
    $like = '%' . $q . '%';
    $out  = [];

    $s = db()->prepare('SELECT titre AS label, slug FROM demarches
                        WHERE actif = 1 AND (titre LIKE :q1 OR resume LIKE :q2)
                        ORDER BY vues DESC LIMIT :lim');
    $s->bindValue('q1', $like);
    $s->bindValue('q2', $like);
    $s->bindValue('lim', $limit, PDO::PARAM_INT);
    $s->execute();
    foreach ($s as $r) {
        $out[] = ['type' => 'demarche', 'type_label' => type_label('demarche'), 'label' => $r['label'], 'url' => url('pages/demarche.php?slug=' . urlencode($r['slug']))];
    }

    $s = db()->prepare('SELECT nom AS label, slug FROM ministeres
                        WHERE actif = 1 AND (nom LIKE :q1 OR description LIKE :q2)
                        ORDER BY ordre LIMIT :lim');
    $s->bindValue('q1', $like);
    $s->bindValue('q2', $like);
    $s->bindValue('lim', $limit, PDO::PARAM_INT);
    $s->execute();
    foreach ($s as $r) {
        $out[] = ['type' => 'ministere', 'type_label' => type_label('ministere'), 'label' => $r['label'], 'url' => url('pages/ministere.php?slug=' . urlencode($r['slug']))];
    }

    $s = db()->prepare('SELECT titre AS label, slug FROM actualites
                        WHERE actif = 1 AND (titre LIKE :q1 OR extrait LIKE :q2)
                        ORDER BY date_pub DESC LIMIT :lim');
    $s->bindValue('q1', $like);
    $s->bindValue('q2', $like);
    $s->bindValue('lim', $limit, PDO::PARAM_INT);
    $s->execute();
    foreach ($s as $r) {
        $out[] = ['type' => 'actualite', 'type_label' => type_label('actualite'), 'label' => $r['label'], 'url' => url('pages/actualite.php?slug=' . urlencode($r['slug']))];
    }

    return array_slice($out, 0, $limit);
}

/** Journalise une action d'administration (fichier logs/admin.log, non exposé) */
function log_admin(string $action): void
{
    $dir = dirname(__DIR__) . '/logs';
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    $line = sprintf("[%s] admin#%s %s | ip=%s | %s\n",
        date('Y-m-d H:i:s'),
        $_SESSION['admin_id'] ?? '-',
        $action,
        $_SERVER['REMOTE_ADDR'] ?? '-',
        $_SERVER['REQUEST_URI'] ?? '-'
    );
    @file_put_contents($dir . '/admin.log', $line, FILE_APPEND | LOCK_EX);
}

/** Boutons de partage réutilisables */
function share_buttons(string $url, string $title): string
{
    $u = urlencode($url);
    $t = urlencode($title);
    return '<div class="share">'
        . '<span>Partager :</span>'
        . '<button type="button" data-share data-share-title="' . e($title) . '">' . icon('share', 15) . ' Partager</button>'
        . '<a href="https://www.facebook.com/sharer/sharer.php?u=' . $u . '" target="_blank" rel="noopener">Facebook</a>'
        . '<a href="https://twitter.com/intent/tweet?url=' . $u . '&text=' . $t . '" target="_blank" rel="noopener">X (Twitter)</a>'
        . '<a href="https://wa.me/?text=' . $t . '%20' . $u . '" target="_blank" rel="noopener">WhatsApp</a>'
        . '<button type="button" data-copy="' . e($url) . '">Copier le lien</button>'
        . '</div>';
}

/** Durée de lecture estimée d'un texte (en minutes) */
function reading_time(?string $text): int
{
    $words = str_word_count(strip_tags((string) $text), 0, 'àáâãäåçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ');
    return max(1, (int) ceil($words / 200));
}
