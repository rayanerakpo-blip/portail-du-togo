<?php
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Ministères & institutions';
$page_description = 'Liste des ministères et administrations de la République Togolaise.';
$active = 'ministeres';

$ministeres = db()->query('SELECT * FROM ministeres WHERE actif = 1 ORDER BY ordre, nom')->fetchAll();

require __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <nav class="breadcrumb"><a href="<?= url('index.php') ?>">Accueil</a><span>›</span>Ministères</nav>
</div>

<section class="section section--tight">
    <div class="container">
        <span class="kicker">Institutions</span>
        <h1>Ministères &amp; administrations</h1>
        <p style="color:var(--muted);max-width:680px">Les départements ministériels de la République Togolaise et leurs principales attributions. Cliquez sur un ministère pour plus de détails.</p>
    </div>
</section>

<section class="section section--tight">
    <div class="container">
        <div class="grid grid--2">
            <?php foreach ($ministeres as $m): ?>
                <a class="card card--link" href="<?= e(ministere_url($m)) ?>">
                    <span class="card__icon"><?= icon('building', 24) ?></span>
                    <h3><?= e($m['nom']) ?></h3>
                    <p><?= e(excerpt($m['description'], 24)) ?></p>
                    <?php if ($m['sigle']): ?><div class="tags"><span class="tag tag--green"><?= e($m['sigle']) ?></span></div><?php endif; ?>
                    <span class="card__more">Fiche du ministère <?= icon('arrow-right', 15) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
