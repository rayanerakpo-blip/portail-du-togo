<?php
/**
 * En-tête commun
 * Variables optionnelles : $page_title, $page_description, $active,
 * $og_type, $og_image, $structured_data, $canonical, $body_class
 */
require_once __DIR__ . '/functions.php';

$page_title       = $page_title       ?? SITE_NAME;
$page_description = $page_description ?? SITE_TAGLINE;
$active           = $active           ?? '';
$og_type          = $og_type          ?? 'website';
$og_image         = $og_image         ?? site_url('assets/img/armoiries-togo.svg');
$canonical        = $canonical        ?? current_url();
$body_class       = $body_class       ?? '';

/** Construit l'attribut aria-current d'un lien de navigation */
$nav_current = static function (string $key) use ($active): string {
    return $active === $key ? ' aria-current="page"' : '';
};
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($page_title) ?> · <?= e(SITE_NAME) ?></title>
    <meta name="description" content="<?= e($page_description) ?>">
    <meta name="theme-color" content="#006a4e">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= e($canonical) ?>">

    <!-- Open Graph -->
    <meta property="og:site_name" content="<?= e(SITE_NAME) ?>">
    <meta property="og:type" content="<?= e($og_type) ?>">
    <meta property="og:locale" content="fr_TG">
    <meta property="og:title" content="<?= e($page_title) ?>">
    <meta property="og:description" content="<?= e($page_description) ?>">
    <meta property="og:url" content="<?= e($canonical) ?>">
    <meta property="og:image" content="<?= e($og_image) ?>">
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($page_title) ?>">
    <meta name="twitter:description" content="<?= e($page_description) ?>">
    <meta name="twitter:image" content="<?= e($og_image) ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 60 40'><rect width='60' height='40' fill='%23006a4e'/><rect y='8' width='60' height='8' fill='%23ffce00'/><rect y='24' width='60' height='8' fill='%23ffce00'/><rect width='24' height='24' fill='%23d21034'/><path d='M12 4l2.35 4.76 5.25.76-3.8 3.7.9 5.23L12 15.98l-4.7 2.47.9-5.23-3.8-3.7 5.25-.76z' fill='%23fff'/></svg>">
    <link rel="apple-touch-icon" href="<?= asset('img/armoiries-togo.svg') ?>">

    <?= json_ld($structured_data ?? default_structured_data()) ?>
</head>
<body class="<?= e($body_class) ?>">
<a class="skip-link" href="#contenu">Aller au contenu principal</a>

<div class="topbar">
    <div class="container topbar__inner">
        <span class="topbar__brand">
            <?= togo_flag(22) ?>
            <span>République Togolaise — <em>Travail · Liberté · Patrie</em></span>
        </span>
        <nav class="topbar__links" aria-label="Liens secondaires">
            <a href="<?= url('pages/urgences.php') ?>">Numéros d'urgence</a>
            <a href="<?= url('pages/a-propos.php') ?>">Le Togo</a>
            <a href="<?= url('admin/') ?>">Espace administration</a>
        </nav>
    </div>
</div>

<header class="site-header">
    <div class="container site-header__inner">
        <a class="brand" href="<?= url('index.php') ?>" aria-label="<?= e(SITE_NAME) ?> — accueil">
            <?= togo_emblem(40) ?>
            <span class="brand__text">
                <strong><?= e(SITE_NAME) ?></strong>
                <small>Services publics &amp; démarches officielles</small>
            </span>
        </a>

        <form class="search" action="<?= url('pages/recherche.php') ?>" method="get" role="search"
              data-search data-endpoint="<?= url('api/recherche.php') ?>" autocomplete="off">
            <label class="sr-only" for="q">Rechercher une démarche, un service…</label>
            <span class="search__icon"><?= icon('search', 20) ?></span>
            <input type="search" id="q" name="q" placeholder="Rechercher une démarche, un service…"
                   value="<?= e($_GET['q'] ?? '') ?>" autocomplete="off"
                   aria-expanded="false" aria-controls="search-suggest">
            <button type="submit">Rechercher</button>
            <div class="search__suggest" id="search-suggest" role="listbox" hidden></div>
        </form>

        <button class="nav-toggle" type="button" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="mainnav"
                data-nav-toggle><?= icon('menu', 26) ?></button>
    </div>

    <nav class="mainnav" id="mainnav" aria-label="Navigation principale" data-nav>
        <div class="container mainnav__inner">
            <a href="<?= url('index.php') ?>" class="<?= $active === 'accueil' ? 'is-active' : '' ?>"<?= $nav_current('accueil') ?>>Accueil</a>
            <a href="<?= url('pages/demarches.php') ?>" class="<?= $active === 'demarches' ? 'is-active' : '' ?>"<?= $nav_current('demarches') ?>>Démarches &amp; services</a>
            <a href="<?= url('pages/ministeres.php') ?>" class="<?= $active === 'ministeres' ? 'is-active' : '' ?>"<?= $nav_current('ministeres') ?>>Ministères</a>
            <a href="<?= url('pages/actualites.php') ?>" class="<?= $active === 'actualites' ? 'is-active' : '' ?>"<?= $nav_current('actualites') ?>>Actualités</a>
            <a href="<?= url('pages/a-propos.php') ?>" class="<?= $active === 'apropos' ? 'is-active' : '' ?>"<?= $nav_current('apropos') ?>>Le Togo</a>
            <a href="<?= url('pages/urgences.php') ?>" class="<?= $active === 'urgences' ? 'is-active' : '' ?>"<?= $nav_current('urgences') ?>>Urgences</a>
            <a href="<?= url('pages/contact.php') ?>" class="<?= $active === 'contact' ? 'is-active' : '' ?>"<?= $nav_current('contact') ?>>Contact</a>
        </div>
    </nav>
</header>

<main id="contenu">
<?php
foreach (get_flashes() as $flash) {
    echo '<div class="container"><div class="alert alert--' . e($flash['type']) . '" role="status">' . e($flash['message']) . '</div></div>';
}
?>
