<?php
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Actualités & communiqués';
$page_description = 'Actualités, communiqués et informations de la vie publique au Togo.';
$active = 'actualites';

// Filtre catégorie
$cat = isset($_GET['categorie']) ? trim($_GET['categorie']) : '';

$cats = db()->query('SELECT DISTINCT categorie FROM actualites WHERE actif = 1 ORDER BY categorie')->fetchAll(PDO::FETCH_COLUMN);

$sql = 'SELECT * FROM actualites WHERE actif = 1';
$params = [];
if ($cat !== '') {
    $sql .= ' AND categorie = :cat';
    $params['cat'] = $cat;
}
$sql .= ' ORDER BY date_pub DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$actualites = $stmt->fetchAll();

require __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <nav class="breadcrumb"><a href="<?= url('index.php') ?>">Accueil</a><span>›</span>Actualités</nav>
</div>

<section class="section section--tight">
    <div class="container">
        <span class="kicker">Vie publique</span>
        <h1>Actualités &amp; communiqués</h1>
        <p style="color:var(--muted);max-width:680px">Suivez l'actualité gouvernementale, les communiqués officiels et les initiatives des ministères.</p>

        <div class="filters" style="margin-top:22px">
            <a href="<?= url('pages/actualites.php') ?>" class="<?= $cat === '' ? 'is-active' : '' ?>">Toutes</a>
            <?php foreach ($cats as $c): ?>
                <a href="<?= url('pages/actualites.php?categorie=' . urlencode($c)) ?>" class="<?= $cat === $c ? 'is-active' : '' ?>"><?= e($c) ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--tight">
    <div class="container">
        <?php if (!$actualites): ?>
            <div class="empty"><?= icon('list', 48) ?><h2>Aucune actualité</h2><p>Revenez plus tard pour les dernières informations.</p></div>
        <?php else: ?>
            <div class="grid grid--3">
                <?php foreach ($actualites as $a): ?>
                    <a class="card card--link news" href="<?= e(actualite_url($a)) ?>">
                        <div class="news__meta">
                            <span class="news__cat"><?= e($a['categorie']) ?></span>
                            <span><?= e(date_fr($a['date_pub'])) ?></span>
                        </div>
                        <h3><?= e($a['titre']) ?></h3>
                        <p class="news__excerpt"><?= e(excerpt($a['extrait'], 22)) ?></p>
                        <span class="card__more">Lire la suite <?= icon('arrow-right', 15) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
