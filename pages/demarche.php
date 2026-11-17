<?php
require_once __DIR__ . '/../includes/functions.php';

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$stmt = db()->prepare('SELECT d.*, c.nom AS categorie, c.slug AS cat_slug
                       FROM demarches d LEFT JOIN categories c ON c.id = d.categorie_id
                       WHERE d.slug = :slug AND d.actif = 1 LIMIT 1');
$stmt->execute(['slug' => $slug]);
$d = $stmt->fetch();

if (!$d) {
    http_response_code(404);
    $page_title = 'Démarche introuvable';
    require __DIR__ . '/../includes/header.php';
    echo '<div class="container"><div class="empty" style="padding:100px 20px">' . icon('search', 52)
        . '<h1>Démarche introuvable</h1><p>La démarche demandée n\'existe pas ou n\'est plus disponible.</p>'
        . '<a class="btn btn--green" href="' . url('pages/demarches.php') . '">Voir toutes les démarches</a></div></div>';
    require __DIR__ . '/../includes/footer.php';
    exit;
}

// Compteur de vues
db()->prepare('UPDATE demarches SET vues = vues + 1 WHERE id = ?')->execute([$d['id']]);

$documents = textToLines($d['documents_requis']);

$page_title = $d['titre'];
$page_description = excerpt($d['resume'], 25);
$active = 'demarches';
$og_type = 'article';
$canonical = site_url('pages/demarche.php?slug=' . urlencode($d['slug']));

$structured_data = [
    '@context'   => 'https://schema.org',
    '@type'      => 'GovernmentService',
    'name'       => $d['titre'],
    'description' => excerpt($d['resume'], 30),
    'serviceType' => $d['categorie'] ?: 'Service public',
    'areaServed' => 'TG',
    'provider'   => ['@type' => 'GovernmentOrganization', 'name' => SITE_NAME, 'url' => site_url('index.php')],
    'mainEntityOfPage' => site_url('pages/demarche.php?slug=' . urlencode($d['slug'])),
];

$similaires = db()->prepare('SELECT d.* , c.nom AS categorie FROM demarches d
                             LEFT JOIN categories c ON c.id = d.categorie_id
                             WHERE d.actif = 1 AND d.categorie_id = :cid AND d.id <> :id
                             ORDER BY d.vues DESC LIMIT 3');
$similaires->execute(['cid' => $d['categorie_id'], 'id' => $d['id']]);
$similaires = $similaires->fetchAll();

require __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <nav class="breadcrumb">
        <a href="<?= url('index.php') ?>">Accueil</a><span>›</span>
        <a href="<?= url('pages/demarches.php') ?>">Démarches</a>
        <?php if ($d['categorie']): ?><span>›</span><a href="<?= url('pages/demarches.php?categorie=' . urlencode($d['cat_slug'])) ?>"><?= e($d['categorie']) ?></a><?php endif; ?>
        <span>›</span><?= e($d['titre']) ?>
    </nav>
</div>

<section class="section section--tight">
    <div class="container">
        <div class="two-col">
            <div>
                <span class="kicker"><?= e($d['categorie'] ?? 'Démarche') ?></span>
                <h1><?= e($d['titre']) ?></h1>
                <p class="lead" style="font-size:1.08rem;color:var(--muted)"><?= e($d['resume']) ?></p>

                <div class="article-actions">
                    <button class="btn btn--ghost btn--sm" type="button" data-print><?= icon('printer', 16) ?> Imprimer</button>
                    <?= share_buttons(site_url('pages/demarche.php?slug=' . urlencode($d['slug'])), $d['titre']) ?>
                </div>

                <div class="panel prose">
                    <h2><?= icon('list', 20) ?> Présentation</h2>
                    <p><?= nl2br(e($d['description'])) ?></p>
                </div>

                <?php if ($documents): ?>
                <div class="panel">
                    <h2><?= icon('check', 20) ?> Documents à fournir</h2>
                    <ul class="checklist">
                        <?php foreach ($documents as $doc): ?>
                            <li><?= icon('check', 18) ?><span><?= e($doc) ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <div class="panel">
                    <h2><?= icon('arrow-right', 20) ?> Comment faire ?</h2>
                    <ol class="steps">
                        <li><strong>Préparer le dossier</strong>Rassemblez tous les documents demandés et vérifiez leur validité.</li>
                        <li><strong>Se présenter au guichet</strong><?= $d['lieu'] ? 'Rendez-vous à : ' . e($d['lieu']) . '.' : 'Rendez-vous au service compétent.' ?></li>
                        <li><strong>Payer les frais</strong><?= $d['cout'] ? 'Frais : ' . e($d['cout']) . '.' : 'Réglez les frais applicables.' ?></li>
                        <li><strong>Retirer le document</strong><?= $d['delai'] ? 'Délai estimé : ' . e($d['delai']) . '.' : 'Retirez votre document selon le délai indiqué par le service.' ?></li>
                    </ol>
                </div>

                <?php if ($similaires): ?>
                <div class="panel">
                    <h2><?= icon('folder', 20) ?> Démarches similaires</h2>
                    <div class="grid grid--3">
                        <?php foreach ($similaires as $s): ?>
                            <a class="card card--link" href="<?= e(demarche_url($s)) ?>">
                                <h3 style="font-size:1rem"><?= e($s['titre']) ?></h3>
                                <span class="card__more">Consulter <?= icon('arrow-right', 14) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <aside class="sticky">
                <div class="panel">
                    <h2><?= icon('clock', 20) ?> Informations clés</h2>
                    <ul class="info-list">
                        <?php if ($d['cout']): ?>
                        <li><?= icon('money', 20) ?><span><strong>Coût</strong><?= e($d['cout']) ?></span></li>
                        <?php endif; ?>
                        <?php if ($d['delai']): ?>
                        <li><?= icon('clock', 20) ?><span><strong>Délai</strong><?= e($d['delai']) ?></span></li>
                        <?php endif; ?>
                        <?php if ($d['lieu']): ?>
                        <li><?= icon('map-pin', 20) ?><span><strong>Où s'adresser</strong><?= e($d['lieu']) ?></span></li>
                        <?php endif; ?>
                        <li><?= icon('globe', 20) ?><span><strong>En ligne</strong><?= $d['en_ligne'] ? 'Démarche disponible en ligne' : 'Non disponible en ligne' ?></span></li>
                    </ul>
                </div>

                <?php if ($d['lien_officiel']): ?>
                <div class="panel">
                    <h2><?= icon('external', 20) ?> Service officiel</h2>
                    <p style="font-size:.9rem;color:var(--muted)">Consultez le site officiel pour effectuer la démarche.</p>
                    <a class="btn btn--green" style="width:100%;justify-content:center" target="_blank" rel="noopener"
                       href="<?= e($d['lien_officiel']) ?>"><?= icon('external', 18) ?> Accéder au service</a>
                </div>
                <?php endif; ?>

                <div class="panel">
                    <h2><?= icon('phone', 20) ?> Besoin d'aide ?</h2>
                    <p style="font-size:.9rem;color:var(--muted)">Un doute sur la procédure ? Contactez-nous et nous vous orienterons.</p>
                    <a class="btn btn--ghost" style="width:100%;justify-content:center" href="<?= url('pages/contact.php') ?>"><?= icon('mail', 18) ?> Nous contacter</a>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
