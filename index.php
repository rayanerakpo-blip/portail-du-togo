<?php
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Accueil';
$page_description = 'Portail des services publics du Togo : démarches, ministères, actualités et numéros utiles.';
$active = 'accueil';

$categories   = get_categories();
$populaires   = db()->query('SELECT d.*, c.nom AS categorie FROM demarches d LEFT JOIN categories c ON c.id = d.categorie_id WHERE d.actif = 1 ORDER BY d.vues DESC LIMIT 6')->fetchAll();
$urgences     = get_urgences(4);
$featured     = db()->query('SELECT * FROM actualites WHERE actif = 1 ORDER BY date_pub DESC LIMIT 1')->fetch();
$actualites   = db()->query('SELECT * FROM actualites WHERE actif = 1 ORDER BY date_pub DESC LIMIT 4')->fetchAll();
$ministeres   = db()->query('SELECT * FROM ministeres WHERE actif = 1 ORDER BY ordre LIMIT 4')->fetchAll();

$nb_demarches = (int) db()->query('SELECT COUNT(*) FROM demarches WHERE actif = 1')->fetchColumn();
$nb_ministeres = (int) db()->query('SELECT COUNT(*) FROM ministeres WHERE actif = 1')->fetchColumn();
$nb_categories = (int) db()->query('SELECT COUNT(*) FROM categories')->fetchColumn();

require __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="container hero__grid">
        <div class="hero__inner">
            <span class="hero__eyebrow"><?= togo_flag(30) ?> Portail des services publics</span>
            <h1>Vos démarches administratives, simplement.</h1>
            <p><?= e(SITE_TAGLINE) ?>. Retrouvez l'ensemble des services, ministères et informations officielles de la République Togolaise au même endroit.</p>

            <form class="hero-search" action="<?= url('pages/recherche.php') ?>" method="get" role="search" data-search data-endpoint="<?= url('api/recherche.php') ?>" autocomplete="off">
                <span class="search__icon"><?= icon('search', 22) ?></span>
                <label class="sr-only" for="hero-q">Rechercher une démarche</label>
                <input type="search" id="hero-q" name="q" placeholder="Ex. : passeport, acte de naissance, impôts…"
                       aria-expanded="false" aria-controls="hero-suggest">
                <button type="submit">Rechercher</button>
                <div class="search__suggest" id="hero-suggest" role="listbox" hidden></div>
            </form>

            <div class="quicklinks">
                <span>Accès rapide :</span>
                <a href="<?= url('pages/demarche.php?slug=demande-passeport-ordinaire') ?>">Passeport</a>
                <a href="<?= url('pages/demarche.php?slug=carte-nationale-identite') ?>">Carte d'identité</a>
                <a href="<?= url('pages/demarche.php?slug=creation-entreprise-rccm') ?>">Créer une entreprise</a>
                <a href="<?= url('pages/demarche.php?slug=casier-judiciaire') ?>">Casier judiciaire</a>
            </div>

            <div class="hero__badges">
                <span class="hero__badge"><?= icon('check', 16) ?> <?= $nb_demarches ?> démarches référencées</span>
                <span class="hero__badge"><?= icon('building', 16) ?> <?= $nb_ministeres ?> ministères</span>
                <span class="hero__badge"><?= icon('globe', 16) ?> Services en ligne</span>
            </div>
        </div>

        <aside class="hero__aside">
            <div class="hero-card">
                <h3><?= icon('bolt', 20) ?> Les plus consultées</h3>
                <ul class="hero-card__list">
                    <?php foreach (array_slice($populaires, 0, 5) as $p): ?>
                        <li>
                            <a href="<?= e(demarche_url($p)) ?>">
                                <span class="hero-card__ico"><?= icon('folder', 18) ?></span>
                                <span><?= e($p['titre']) ?></span>
                                <?= icon('chevron-right', 16) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <a class="btn btn--ghost btn--sm" style="width:100%;justify-content:center" href="<?= url('pages/demarches.php') ?>">
                    Voir toutes les démarches <?= icon('arrow-right', 15) ?>
                </a>
            </div>
        </aside>
    </div>
</section>

<!-- Catégories -->
<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="kicker">Par thème</span>
                <h2>Que souhaitez-vous faire ?</h2>
                <p>Parcourez les démarches par domaine.</p>
            </div>
            <a class="btn btn--ghost btn--sm" href="<?= url('pages/demarches.php') ?>">Tout voir <?= icon('arrow-right', 16) ?></a>
        </div>
        <div class="grid grid--4">
            <?php foreach ($categories as $cat): ?>
                <a class="card card--link cat-card" href="<?= url('pages/demarches.php?categorie=' . urlencode($cat['slug'])) ?>">
                    <span class="card__icon"><?= icon($cat['icone'], 24) ?></span>
                    <span>
                        <h3><?= e($cat['nom']) ?></h3>
                        <span><?= e(excerpt($cat['description'], 8)) ?></span>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Démarches populaires -->
<section class="section section--white">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="kicker">Les plus consultées</span>
                <h2>Démarches populaires</h2>
                <p>Les services les plus demandés par les usagers.</p>
            </div>
            <a class="btn btn--ghost btn--sm" href="<?= url('pages/demarches.php') ?>">Toutes les démarches <?= icon('arrow-right', 16) ?></a>
        </div>
        <div class="grid grid--3">
            <?php foreach ($populaires as $d): ?>
                <a class="card card--link demarche-card" href="<?= e(demarche_url($d)) ?>">
                    <span class="card__icon"><?= icon('folder', 24) ?></span>
                    <h3><?= e($d['titre']) ?></h3>
                    <p><?= e(excerpt($d['resume'], 16)) ?></p>
                    <div class="tags">
                        <?php if ($d['categorie']): ?><span class="tag tag--green"><?= e($d['categorie']) ?></span><?php endif; ?>
                        <?php if ($d['en_ligne']): ?><span class="tag tag--online"><?= icon('globe', 13) ?> En ligne</span><?php endif; ?>
                    </div>
                    <span class="card__more">Consulter <?= icon('arrow-right', 15) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Actualités -->
<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="kicker">Vie publique</span>
                <h2>Actualités &amp; communiqués</h2>
                <p>L'information gouvernementale en continu.</p>
            </div>
            <a class="btn btn--ghost btn--sm" href="<?= url('pages/actualites.php') ?>">Toutes les actualités <?= icon('arrow-right', 16) ?></a>
        </div>

        <?php if ($featured): ?>
        <a class="card card--link news news--featured" href="<?= e(actualite_url($featured)) ?>" style="margin-bottom:20px">
            <div class="news__img"><?= togo_emblem(90) ?></div>
            <div class="news__body">
                <div class="news__meta">
                    <span class="news__cat"><?= e($featured['categorie']) ?></span>
                    <span><?= e(date_fr($featured['date_pub'])) ?></span>
                </div>
                <h3><?= e($featured['titre']) ?></h3>
                <p class="news__excerpt"><?= e($featured['extrait']) ?></p>
                <span class="card__more">Lire le communiqué <?= icon('arrow-right', 15) ?></span>
            </div>
        </a>
        <?php endif; ?>

        <div class="grid grid--3">
            <?php foreach (array_slice($actualites, 1) as $a): ?>
                <a class="card card--link news" href="<?= e(actualite_url($a)) ?>">
                    <div class="news__meta">
                        <span class="news__cat"><?= e($a['categorie']) ?></span>
                        <span><?= e(date_fr($a['date_pub'])) ?></span>
                    </div>
                    <h3><?= e($a['titre']) ?></h3>
                    <p class="news__excerpt"><?= e(excerpt($a['extrait'], 18)) ?></p>
                    <span class="card__more">Lire la suite <?= icon('arrow-right', 15) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Ministères -->
<section class="section section--white">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="kicker">Institutions</span>
                <h2>Ministères &amp; administrations</h2>
                <p>Les départements ministériels de la République.</p>
            </div>
            <a class="btn btn--ghost btn--sm" href="<?= url('pages/ministeres.php') ?>">Tous les ministères <?= icon('arrow-right', 16) ?></a>
        </div>
        <div class="grid grid--2">
            <?php foreach ($ministeres as $m): ?>
                <a class="card card--link" href="<?= e(ministere_url($m)) ?>">
                    <span class="card__icon"><?= icon('building', 24) ?></span>
                    <h3><?= e($m['nom']) ?></h3>
                    <p><?= e(excerpt($m['description'], 20)) ?></p>
                    <span class="card__more">En savoir plus <?= icon('arrow-right', 15) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Urgences -->
<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="kicker">En cas d'urgence</span>
                <h2>Numéros utiles</h2>
                <p>À conserver et à partager.</p>
            </div>
            <a class="btn btn--ghost btn--sm" href="<?= url('pages/urgences.php') ?>">Tous les numéros <?= icon('arrow-right', 16) ?></a>
        </div>
        <div class="grid grid--2">
            <?php foreach ($urgences as $u): ?>
                <div class="urgence-card">
                    <span class="urgence-card__icon"><?= icon($u['icone'], 22) ?></span>
                    <div class="urgence-card__body">
                        <h3><?= e($u['nom']) ?></h3>
                        <p><?= e($u['description']) ?></p>
                    </div>
                    <a class="urgence-card__num" href="tel:<?= e(preg_replace('/\s+/', '', $u['numero'])) ?>"><?= e($u['numero']) ?></a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Stats + CTA -->
<section class="section section--green">
    <div class="container">
        <div class="stats" style="margin-bottom:34px">
            <div class="stat"><div class="stat__num"><?= $nb_demarches ?></div><div class="stat__label">Démarches référencées</div></div>
            <div class="stat"><div class="stat__num"><?= $nb_ministeres ?></div><div class="stat__label">Ministères &amp; institutions</div></div>
            <div class="stat"><div class="stat__num"><?= $nb_categories ?></div><div class="stat__label">Domaines de service</div></div>
            <div class="stat"><div class="stat__num">24/7</div><div class="stat__label">Information accessible</div></div>
        </div>
        <div class="cta" style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.15)">
            <div>
                <h2>Vous ne trouvez pas votre service ?</h2>
                <p>Utilisez la recherche ou contactez-nous, nous vous orienterons vers le bon guichet.</p>
            </div>
            <div class="hero__actions" style="margin:0">
                <a class="btn btn--primary" href="<?= url('pages/contact.php') ?>"><?= icon('mail', 18) ?> Nous contacter</a>
                <a class="btn btn--outline" href="<?= url('pages/recherche.php') ?>"><?= icon('search', 18) ?> Rechercher</a>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
