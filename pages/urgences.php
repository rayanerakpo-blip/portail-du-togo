<?php
require_once __DIR__ . '/../includes/functions.php';

$page_title = "Numéros d'urgence";
$page_description = 'Numéros utiles et d\'urgence au Togo : police, pompiers, SAMU et assistance.';
$active = 'urgences';

$urgences = get_urgences();
$groupes = [];
foreach ($urgences as $u) {
    $groupes[$u['categorie']][] = $u;
}

require __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <nav class="breadcrumb"><a href="<?= url('index.php') ?>">Accueil</a><span>›</span>Urgences</nav>
</div>

<section class="section section--tight">
    <div class="container">
        <span class="kicker" style="color:var(--red)">En cas d'urgence</span>
        <h1>Numéros utiles &amp; urgences</h1>
        <p style="color:var(--muted);max-width:680px">Conservez ces numéros. En situation d'urgence, composez directement le numéro correspondant depuis votre téléphone.</p>

        <div class="alert alert--info" style="margin-top:20px">
            <?= icon('alert', 18) ?> En cas de danger immédiat, appelez le <strong>117</strong> (Police) ou le <strong>118</strong> (Sapeurs-Pompiers).
        </div>
    </div>
</section>

<?php foreach ($groupes as $categorie => $liste): ?>
<section class="section section--tight">
    <div class="container">
        <h2><?= e($categorie) ?></h2>
        <div class="grid grid--2">
            <?php foreach ($liste as $u): ?>
                <div class="urgence-card <?= $categorie === 'Services' ? 'urgence-card--yellow' : '' ?>">
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
<?php endforeach; ?>

<section class="section section--green">
    <div class="container">
        <div class="cta" style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.15)">
            <div>
                <h2>Ces numéros sont-ils à jour ?</h2>
                <p>Signalez-nous toute erreur ou suggestion pour améliorer ce répertoire.</p>
            </div>
            <a class="btn btn--primary" href="<?= url('pages/contact.php') ?>"><?= icon('mail', 18) ?> Nous écrire</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
