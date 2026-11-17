<?php
require_once __DIR__ . '/../includes/functions.php';

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$stmt = db()->prepare('SELECT * FROM actualites WHERE slug = :slug AND actif = 1 LIMIT 1');
$stmt->execute(['slug' => $slug]);
$a = $stmt->fetch();

if (!$a) {
    http_response_code(404);
    $page_title = 'Actualité introuvable';
    require __DIR__ . '/../includes/header.php';
    echo '<div class="container"><div class="empty" style="padding:100px 20px">' . icon('list', 52)
        . '<h1>Actualité introuvable</h1><a class="btn btn--green" href="' . url('pages/actualites.php') . '">Toutes les actualités</a></div></div>';
    require __DIR__ . '/../includes/footer.php';
    exit;
}

$page_title = $a['titre'];
$page_description = excerpt($a['extrait'], 25);
$active = 'actualites';
$og_type = 'article';
$canonical = site_url('pages/actualite.php?slug=' . urlencode($a['slug']));

if (!empty($a['image'])) {
    $img = $a['image'];
    $og_image = preg_match('#^https?://#i', $img) ? $img : site_url(ltrim($img, '/'));
}

$structured_data = [
    '@context' => 'https://schema.org',
    '@type'    => 'NewsArticle',
    'headline' => $a['titre'],
    'description' => excerpt($a['extrait'], 30),
    'datePublished' => date('c', strtotime($a['date_pub'])),
    'dateModified'  => date('c', strtotime($a['date_pub'])),
    'articleSection' => $a['categorie'],
    'author'    => ['@type' => 'Organization', 'name' => $a['auteur'] ?: SITE_NAME],
    'publisher' => [
        '@type' => 'GovernmentOrganization',
        'name'  => SITE_NAME,
        'logo'  => ['@type' => 'ImageObject', 'url' => site_url('assets/img/armoiries-togo.svg')],
    ],
    'mainEntityOfPage' => site_url('pages/actualite.php?slug=' . urlencode($a['slug'])),
];

$autres = db()->prepare('SELECT * FROM actualites WHERE actif = 1 AND id <> :id ORDER BY date_pub DESC LIMIT 3');
$autres->execute(['id' => $a['id']]);
$autres = $autres->fetchAll();

require __DIR__ . '/../includes/header.php';

$paragraphes = preg_split('/\n\s*\n/', trim((string) $a['contenu']));
?>

<div class="container">
    <nav class="breadcrumb">
        <a href="<?= url('index.php') ?>">Accueil</a><span>›</span>
        <a href="<?= url('pages/actualites.php') ?>">Actualités</a><span>›</span><?= e(excerpt($a['titre'], 8)) ?>
    </nav>
</div>

<section class="section section--tight">
    <div class="container">
        <div class="two-col">
            <article>
                <div class="news__meta" style="margin-bottom:10px">
                    <span class="news__cat"><?= e($a['categorie']) ?></span>
                    <span><?= e(date_fr($a['date_pub'])) ?></span>
                    <span>· <?= reading_time($a['contenu']) ?> min de lecture</span>
                </div>
                <h1><?= e($a['titre']) ?></h1>
                <p class="lead" style="font-size:1.1rem;color:var(--muted)"><?= e($a['extrait']) ?></p>

                <div class="prose">
                    <?php foreach ($paragraphes as $p): ?>
                        <p><?= nl2br(e(trim($p))) ?></p>
                    <?php endforeach; ?>
                </div>

                <?php if ($a['source']): ?>
                    <p style="font-size:.85rem;color:var(--muted);margin-top:24px">
                        Source : <?= e($a['source']) ?><?php if ($a['source_url']): ?> — <a href="<?= e($a['source_url']) ?>" target="_blank" rel="noopener">lien</a><?php endif; ?>
                    </p>
                <?php endif; ?>

                <?= share_buttons(site_url('pages/actualite.php?slug=' . urlencode($a['slug'])), $a['titre']) ?>

                <a class="btn btn--ghost" style="margin-top:18px" href="<?= url('pages/actualites.php') ?>">← Retour aux actualités</a>
            </article>

            <aside class="sticky">
                <div class="panel">
                    <h2><?= icon('list', 20) ?> À lire aussi</h2>
                    <ul class="info-list">
                        <?php foreach ($autres as $o): ?>
                            <li>
                                <?= icon('arrow-right', 18) ?>
                                <span>
                                    <a href="<?= e(actualite_url($o)) ?>" style="font-weight:600"><?= e($o['titre']) ?></a>
                                    <em style="display:block;color:var(--muted);font-size:.8rem;font-style:normal"><?= e(date_fr($o['date_pub'])) ?></em>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
