<?php
require_once __DIR__ . '/../includes/functions.php';

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$stmt = db()->prepare('SELECT * FROM ministeres WHERE slug = :slug AND actif = 1 LIMIT 1');
$stmt->execute(['slug' => $slug]);
$m = $stmt->fetch();

if (!$m) {
    http_response_code(404);
    $page_title = 'Ministère introuvable';
    require __DIR__ . '/../includes/header.php';
    echo '<div class="container"><div class="empty" style="padding:100px 20px">' . icon('building', 52)
        . '<h1>Ministère introuvable</h1><a class="btn btn--green" href="' . url('pages/ministeres.php') . '">Tous les ministères</a></div></div>';
    require __DIR__ . '/../includes/footer.php';
    exit;
}

$missions = textToLines($m['missions']);

// Démarches liées : on fait un rapprochement par mot-clé du nom de ministère (simple)
$page_title = $m['nom'];
$page_description = excerpt($m['description'], 25);
$active = 'ministeres';
$canonical = site_url('pages/ministere.php?slug=' . urlencode($m['slug']));
$structured_data = [
    '@context' => 'https://schema.org',
    '@type'    => 'GovernmentOrganization',
    'name'     => $m['nom'],
    'alternateName' => $m['sigle'] ?: null,
    'description' => excerpt($m['description'], 30),
    'url'      => site_url('pages/ministere.php?slug=' . urlencode($m['slug'])),
    'areaServed' => 'TG',
];

require __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <nav class="breadcrumb">
        <a href="<?= url('index.php') ?>">Accueil</a><span>›</span>
        <a href="<?= url('pages/ministeres.php') ?>">Ministères</a><span>›</span><?= e($m['sigle'] ?: $m['nom']) ?>
    </nav>
</div>

<section class="section section--tight">
    <div class="container">
        <div class="two-col">
            <div>
                <span class="kicker"><?= e($m['sigle'] ?: 'Institution') ?></span>
                <h1><?= e($m['nom']) ?></h1>
                <p class="lead" style="font-size:1.08rem;color:var(--muted)"><?= e($m['description']) ?></p>

                <?php if ($missions): ?>
                <div class="panel">
                    <h2><?= icon('list', 20) ?> Missions principales</h2>
                    <ul class="checklist">
                        <?php foreach ($missions as $mission): ?>
                            <li><?= icon('check', 18) ?><span><?= e($mission) ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <div class="panel">
                    <h2><?= icon('phone', 20) ?> Contacter ce ministère</h2>
                    <p style="color:var(--muted);font-size:.92rem">Pour toute démarche relevant de ce ministère, adressez-vous directement à ses services.</p>
                    <a class="btn btn--green" href="<?= url('pages/contact.php') ?>"><?= icon('mail', 18) ?> Formulaire de contact</a>
                </div>
            </div>

            <aside class="sticky">
                <div class="panel">
                    <h2><?= icon('building', 20) ?> Coordonnées</h2>
                    <ul class="info-list">
                        <?php if ($m['sigle']): ?><li><?= icon('folder', 20) ?><span><strong>Sigle</strong><?= e($m['sigle']) ?></span></li><?php endif; ?>
                        <?php if ($m['adresse']): ?><li><?= icon('map-pin', 20) ?><span><strong>Adresse</strong><?= e($m['adresse']) ?></span></li><?php endif; ?>
                        <?php if ($m['telephone']): ?><li><?= icon('phone', 20) ?><span><strong>Téléphone</strong><?= e($m['telephone']) ?></span></li><?php endif; ?>
                        <?php if ($m['email']): ?><li><?= icon('mail', 20) ?><span><strong>E-mail</strong><?= e($m['email']) ?></span></li><?php endif; ?>
                    </ul>
                    <?php if ($m['site_web']): ?>
                        <a class="btn btn--green" style="width:100%;justify-content:center;margin-top:8px" target="_blank" rel="noopener" href="<?= e($m['site_web']) ?>"><?= icon('external', 18) ?> Site officiel</a>
                    <?php endif; ?>
                </div>

                <div class="panel">
                    <h2><?= icon('arrow-right', 20) ?> Démarches utiles</h2>
                    <p style="font-size:.9rem;color:var(--muted)">Retrouvez les démarches liées aux administrations.</p>
                    <a class="btn btn--ghost" style="width:100%;justify-content:center" href="<?= url('pages/demarches.php') ?>"><?= icon('list', 18) ?> Voir les démarches</a>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
