<?php
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Démarches & services';
$page_description = 'Liste complète des démarches administratives togolaises par catégorie.';
$active = 'demarches';

$categories = get_categories();

// Catégorie active
$catSlug = isset($_GET['categorie']) ? trim($_GET['categorie']) : '';
$catActive = null;
foreach ($categories as $c) {
    if ($c['slug'] === $catSlug) { $catActive = $c; break; }
}

// Recherche
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

$sql = 'SELECT d.*, c.nom AS categorie, c.slug AS cat_slug
        FROM demarches d
        LEFT JOIN categories c ON c.id = d.categorie_id
        WHERE d.actif = 1';
$params = [];

if ($catActive) {
    $sql .= ' AND d.categorie_id = :cid';
    $params['cid'] = $catActive['id'];
}
if ($q !== '') {
    $sql .= ' AND (d.titre LIKE :q1 OR d.resume LIKE :q2 OR d.description LIKE :q3)';
    $params['q1'] = '%' . $q . '%';
    $params['q2'] = '%' . $q . '%';
    $params['q3'] = '%' . $q . '%';
}
$sql .= ' ORDER BY d.titre';

$stmt = db()->prepare($sql);
$stmt->execute($params);
$demarches = $stmt->fetchAll();

require __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <nav class="breadcrumb"><a href="<?= url('index.php') ?>">Accueil</a><span>›</span>Démarches &amp; services</nav>
</div>

<section class="section section--tight">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="kicker">Guichet unique</span>
                <h1>Démarches &amp; services publics</h1>
                <p><?= count($demarches) ?> démarche(s) <?= $catActive ? 'dans « ' . e($catActive['nom']) . ' »' : 'disponibles' ?>.</p>
            </div>
        </div>

        <form method="get" class="filters" style="margin-bottom:24px">
            <input type="search" name="q" placeholder="Filtrer par mot-clé…" value="<?= e($q) ?>"
                   style="min-width:260px;padding:8px 14px;border:1px solid var(--line);border-radius:100px">
            <?php if ($catActive): ?><input type="hidden" name="categorie" value="<?= e($catActive['slug']) ?>"><?php endif; ?>
            <button class="btn btn--green btn--sm" type="submit"><?= icon('search', 16) ?> Filtrer</button>
            <?php if ($q !== '' || $catActive): ?>
                <a class="btn btn--ghost btn--sm" href="<?= url('pages/demarches.php') ?>">Réinitialiser</a>
            <?php endif; ?>
        </form>

        <div class="filters">
            <a href="<?= url('pages/demarches.php' . ($q ? '?q=' . urlencode($q) : '')) ?>" class="<?= !$catActive ? 'is-active' : '' ?>">Toutes</a>
            <?php foreach ($categories as $c): ?>
                <a href="<?= url('pages/demarches.php?categorie=' . urlencode($c['slug']) . ($q ? '&q=' . urlencode($q) : '')) ?>"
                   class="<?= $catActive && $catActive['id'] == $c['id'] ? 'is-active' : '' ?>"><?= e($c['nom']) ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--tight">
    <div class="container">
        <?php if (!$demarches): ?>
            <div class="empty">
                <?= icon('search', 48) ?>
                <h2>Aucune démarche trouvée</h2>
                <p>Essayez un autre mot-clé ou parcourez toutes les catégories.</p>
                <a class="btn btn--green" href="<?= url('pages/demarches.php') ?>">Voir toutes les démarches</a>
            </div>
        <?php else: ?>
            <div class="grid grid--3">
                <?php foreach ($demarches as $d): ?>
                    <a class="card card--link demarche-card" href="<?= e(demarche_url($d)) ?>">
                        <span class="card__icon"><?= icon('folder', 24) ?></span>
                        <h3><?= e($d['titre']) ?></h3>
                        <p><?= e(excerpt($d['resume'], 18)) ?></p>
                        <div class="tags">
                            <?php if ($d['categorie']): ?>
                                <span class="tag tag--green"><?= e($d['categorie']) ?></span>
                            <?php endif; ?>
                            <?php if ($d['en_ligne']): ?><span class="tag tag--online"><?= icon('globe', 13) ?> En ligne</span><?php endif; ?>
                            <?php if ($d['delai']): ?><span class="tag"><?= icon('clock', 13) ?> <?= e($d['delai']) ?></span><?php endif; ?>
                        </div>
                        <span class="card__more">Voir la démarche <?= icon('arrow-right', 15) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
