<?php
require_once __DIR__ . '/../includes/functions.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$page_title = 'Recherche';
$page_description = 'Recherchez une démarche, un ministère ou une actualité.';
$active = '';

$demarches = $ministeres = $actualites = [];
$total = 0;

if ($q !== '') {
    $like = '%' . $q . '%';

    $s = db()->prepare('SELECT d.*, c.nom AS categorie FROM demarches d
                        LEFT JOIN categories c ON c.id = d.categorie_id
                        WHERE d.actif = 1 AND (d.titre LIKE :q1 OR d.resume LIKE :q2 OR d.description LIKE :q3)
                        ORDER BY d.vues DESC LIMIT 20');
    $s->execute(['q1' => $like, 'q2' => $like, 'q3' => $like]);
    $demarches = $s->fetchAll();

    $s = db()->prepare('SELECT * FROM ministeres WHERE actif = 1 AND (nom LIKE :q1 OR description LIKE :q2 OR missions LIKE :q3) ORDER BY ordre LIMIT 20');
    $s->execute(['q1' => $like, 'q2' => $like, 'q3' => $like]);
    $ministeres = $s->fetchAll();

    $s = db()->prepare('SELECT * FROM actualites WHERE actif = 1 AND (titre LIKE :q1 OR extrait LIKE :q2 OR contenu LIKE :q3) ORDER BY date_pub DESC LIMIT 20');
    $s->execute(['q1' => $like, 'q2' => $like, 'q3' => $like]);
    $actualites = $s->fetchAll();

    $total = count($demarches) + count($ministeres) + count($actualites);
}

require __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <nav class="breadcrumb"><a href="<?= url('index.php') ?>">Accueil</a><span>›</span>Recherche</nav>
</div>

<section class="section section--tight">
    <div class="container">
        <h1>Recherche</h1>
        <form class="search" method="get" style="max-width:640px;justify-self:start;margin:18px 0 8px">
            <span class="search__icon"><?= icon('search', 20) ?></span>
            <input type="search" name="q" placeholder="Que recherchez-vous ?" value="<?= e($q) ?>" autofocus>
            <button type="submit">Rechercher</button>
        </form>
        <?php if ($q !== ''): ?>
            <p style="color:var(--muted)"><strong><?= $total ?></strong> résultat(s) pour « <?= e($q) ?> »</p>
        <?php endif; ?>
    </div>
</section>

<?php if ($q !== ''): ?>
<section class="section section--tight">
    <div class="container">
        <?php if ($total === 0): ?>
            <div class="empty">
                <?= icon('search', 48) ?>
                <h2>Aucun résultat</h2>
                <p>Aucun contenu ne correspond à « <?= e($q) ?> ». Essayez un autre mot-clé.</p>
                <a class="btn btn--green" href="<?= url('pages/demarches.php') ?>">Parcourir les démarches</a>
            </div>
        <?php else: ?>

            <?php if ($demarches): ?>
                <h2>Démarches (<?= count($demarches) ?>)</h2>
                <div class="grid grid--3" style="margin-bottom:34px">
                    <?php foreach ($demarches as $d): ?>
                        <a class="card card--link" href="<?= e(demarche_url($d)) ?>">
                            <span class="card__icon"><?= icon('folder', 24) ?></span>
                            <h3><?= e($d['titre']) ?></h3>
                            <p><?= e(excerpt($d['resume'], 16)) ?></p>
                            <span class="card__more">Consulter <?= icon('arrow-right', 15) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($ministeres): ?>
                <h2>Ministères (<?= count($ministeres) ?>)</h2>
                <div class="grid grid--2" style="margin-bottom:34px">
                    <?php foreach ($ministeres as $m): ?>
                        <a class="card card--link" href="<?= e(ministere_url($m)) ?>">
                            <span class="card__icon"><?= icon('building', 24) ?></span>
                            <h3><?= e($m['nom']) ?></h3>
                            <p><?= e(excerpt($m['description'], 18)) ?></p>
                            <span class="card__more">Voir la fiche <?= icon('arrow-right', 15) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($actualites): ?>
                <h2>Actualités (<?= count($actualites) ?>)</h2>
                <div class="grid grid--3">
                    <?php foreach ($actualites as $a): ?>
                        <a class="card card--link news" href="<?= e(actualite_url($a)) ?>">
                            <div class="news__meta"><span class="news__cat"><?= e($a['categorie']) ?></span><span><?= e(date_fr($a['date_pub'])) ?></span></div>
                            <h3><?= e($a['titre']) ?></h3>
                            <span class="card__more">Lire <?= icon('arrow-right', 15) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
