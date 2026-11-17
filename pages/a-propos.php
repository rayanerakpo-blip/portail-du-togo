<?php
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Le Togo en bref';
$page_description = 'Découvrez la République Togolaise : géographie, régions, langues, économie et symboles nationaux.';
$active = 'apropos';
$og_type = 'article';

$regions = [
    ['Maritime', 'Lomé', 'Chef-lieu économique et capitale du pays.'],
    ['Plateaux', 'Atakpamé', 'Reliefs, forêts et production agricole.'],
    ['Centrale', 'Sokodé', 'Carrefour entre le nord et le sud du pays.'],
    ['Kara', 'Kara', 'Riche patrimoine culturel et montagnes.'],
    ['Savanes', 'Dapaong', 'Zone septentrionale, frontalière du Sahel.'],
];

$structured_data = [
    '@context' => 'https://schema.org',
    '@type' => 'Country',
    'name' => 'Togo',
    'alternateName' => 'République Togolaise',
    'url' => site_url('pages/a-propos.php'),
    'capital' => 'Lomé',
    'area' => '56785',
    'address' => ['@type' => 'PostalAddress', 'addressCountry' => 'TG'],
];

require __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <nav class="breadcrumb" aria-label="Fil d'Ariane">
        <a href="<?= url('index.php') ?>">Accueil</a><span>›</span>Le Togo
    </nav>
</div>

<section class="section section--tight">
    <div class="container">
        <span class="kicker">Portrait du pays</span>
        <h1>Le Togo en bref</h1>
        <p style="color:var(--muted);max-width:720px">
            Situé en Afrique de l'Ouest, sur le golfe de Guinée, le Togo est un pays d'à peine
            57 000 km² qui s'étire du nord au sud. Nation d'accueil et de transit, elle abrite
            une grande diversité de peuples, de langues et de paysages.
        </p>
    </div>
</section>

<section class="section section--green section--tight">
    <div class="container">
        <div class="stats">
            <div class="stat"><div class="stat__num">1960</div><div class="stat__label">Indépendance (27 avril)</div></div>
            <div class="stat"><div class="stat__num">Lomé</div><div class="stat__label">Capitale</div></div>
            <div class="stat"><div class="stat__num">≈ 9 M</div><div class="stat__label">Habitants</div></div>
            <div class="stat"><div class="stat__num">FCFA</div><div class="stat__label">Monnaie (XOF)</div></div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="two-col">
            <div>
                <div class="panel">
                    <h2><?= icon('globe', 20) ?> Chiffres clés</h2>
                    <ul class="info-list">
                        <li><?= icon('map-pin', 20) ?><span><strong>Superficie</strong>56 785 km²</span></li>
                        <li><?= icon('user', 20) ?><span><strong>Langue officielle</strong>Français (langues nationales : éwé, kabyè…)</span></li>
                        <li><?= icon('money', 20) ?><span><strong>Monnaie</strong>Franc CFA (XOF)</span></li>
                        <li><?= icon('building', 20) ?><span><strong>Régime</strong>République — devise « Travail · Liberté · Patrie »</span></li>
                        <li><?= icon('clock', 20) ?><span><strong>Fuseau horaire</strong>GMT (UTC+0)</span></li>
                    </ul>
                </div>

                <div class="panel">
                    <h2><?= icon('map-pin', 20) ?> Les cinq régions</h2>
                    <div class="table-wrap" style="box-shadow:none;border:0">
                        <table class="data">
                            <thead><tr><th>Région</th><th>Chef-lieu</th><th>Caractéristiques</th></tr></thead>
                            <tbody>
                            <?php foreach ($regions as $r): ?>
                                <tr><td><strong><?= e($r[0]) ?></strong></td><td><?= e($r[1]) ?></td><td><?= e($r[2]) ?></td></tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="panel">
                    <h2><?= icon('briefcase', 20) ?> Économie</h2>
                    <p style="color:var(--muted)">
                        L'économie togolaise repose sur l'agriculture (café, cacao, coton, vivriers),
                        les phosphates, et surtout le <strong>port autonome de Lomé</strong> — l'un des
                        ports en eau profonde les plus importants de la sous-région — qui fait du Togo
                        une plateforme de transit vers le Sahel.
                    </p>
                    <a class="btn btn--ghost" href="<?= url('pages/demarches.php?categorie=entreprise-emploi') ?>">
                        <?= icon('briefcase', 18) ?> Créer une entreprise
                    </a>
                </div>
            </div>

            <aside class="sticky">
                <div class="panel" style="text-align:center">
                    <h2 style="justify-content:center"><?= icon('shield', 20) ?> Symboles nationaux</h2>
                    <div style="display:flex;flex-direction:column;align-items:center;gap:16px;margin-top:8px">
                        <div>
                            <?= togo_emblem(120) ?>
                            <div class="form-hint">Armoiries — deux lions tenant arc et flèche</div>
                        </div>
                        <div>
                            <?= togo_flag(110) ?>
                            <div class="form-hint">Drapeau national</div>
                        </div>
                    </div>
                    <p style="margin-top:16px;color:var(--muted);font-size:.9rem">
                        Devise : <strong>« Travail · Liberté · Patrie »</strong><br>
                        Hymne : <em>« Terre de nos aïeux »</em>
                    </p>
                </div>
                <div class="panel">
                    <h2><?= icon('list', 20) ?> Que cherchez-vous ?</h2>
                    <p style="font-size:.9rem;color:var(--muted)">Accédez directement aux services les plus demandés.</p>
                    <div class="hero__actions" style="margin:0">
                        <a class="btn btn--green btn--sm" href="<?= url('pages/demarches.php') ?>">Démarches</a>
                        <a class="btn btn--ghost btn--sm" href="<?= url('pages/ministeres.php') ?>">Ministères</a>
                        <a class="btn btn--ghost btn--sm" href="<?= url('pages/urgences.php') ?>">Urgences</a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
