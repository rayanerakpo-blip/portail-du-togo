<?php
/**
 * Page 404 personnalisée.
 * Configurée via .htaccess : ErrorDocument 404 /www/TOGO/404.php
 */
require_once __DIR__ . '/includes/functions.php';

http_response_code(404);
$page_title = 'Page introuvable (404)';
$page_description = "La page demandée n'existe pas ou a été déplacée.";
$active = '';

$populaires = db()->query('SELECT titre, slug FROM demarches WHERE actif = 1 ORDER BY vues DESC LIMIT 5')->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container">
        <div class="empty" style="padding:60px 20px">
            <div style="font-size:5rem;font-weight:800;color:var(--green);line-height:1">404</div>
            <h1>Cette page n'existe pas</h1>
            <p style="max-width:520px;margin:0 auto 20px">
                Le lien est peut-être erroné ou la page a été déplacée.
                Utilisez la recherche ou repartez de l'accueil.
            </p>

            <form class="search" action="<?= url('pages/recherche.php') ?>" method="get" role="search"
                  style="max-width:520px;margin:0 auto 14px">
                <span class="search__icon"><?= icon('search', 20) ?></span>
                <input type="search" name="q" placeholder="Rechercher un service…" aria-label="Rechercher">
                <button type="submit">Rechercher</button>
            </form>

            <div class="hero__actions" style="justify-content:center">
                <a class="btn btn--green" href="<?= url('index.php') ?>"><?= icon('arrow-right', 18) ?> Retour à l'accueil</a>
                <a class="btn btn--ghost" href="<?= url('pages/demarches.php') ?>"><?= icon('list', 18) ?> Toutes les démarches</a>
            </div>
        </div>
    </div>
</section>

<?php if ($populaires): ?>
<section class="section section--white">
    <div class="container">
        <div class="section-head"><div><span class="kicker">Suggestions</span><h2>Démarches populaires</h2></div></div>
        <div class="grid grid--3">
            <?php foreach ($populaires as $d): ?>
                <a class="card card--link" href="<?= url('pages/demarche.php?slug=' . urlencode($d['slug'])) ?>">
                    <span class="card__icon"><?= icon('folder', 24) ?></span>
                    <h3 style="font-size:1rem"><?= e($d['titre']) ?></h3>
                    <span class="card__more">Consulter <?= icon('arrow-right', 15) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
