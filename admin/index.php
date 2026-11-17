<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$stats = [
    'demarches'  => (int) db()->query('SELECT COUNT(*) FROM demarches')->fetchColumn(),
    'ministeres' => (int) db()->query('SELECT COUNT(*) FROM ministeres')->fetchColumn(),
    'actualites' => (int) db()->query('SELECT COUNT(*) FROM actualites')->fetchColumn(),
    'urgences'   => (int) db()->query('SELECT COUNT(*) FROM urgences')->fetchColumn(),
    'messages'   => (int) db()->query('SELECT COUNT(*) FROM messages')->fetchColumn(),
    'non_lus'    => (int) db()->query('SELECT COUNT(*) FROM messages WHERE lu = 0')->fetchColumn(),
];

$derniers_msg = db()->query('SELECT * FROM messages ORDER BY cree_le DESC LIMIT 5')->fetchAll();
$derniers_art = db()->query('SELECT * FROM actualites ORDER BY date_pub DESC LIMIT 5')->fetchAll();

admin_header('Tableau de bord', 'dashboard');
?>

<div class="grid grid--4" style="margin-bottom:26px">
    <a class="card card--link" href="<?= url('admin/demarches.php') ?>">
        <span class="card__icon"><?= icon('folder', 24) ?></span>
        <h3 style="font-size:2rem;margin:0"><?= $stats['demarches'] ?></h3>
        <p style="margin:0">Démarches</p>
    </a>
    <a class="card card--link" href="<?= url('admin/ministeres.php') ?>">
        <span class="card__icon"><?= icon('building', 24) ?></span>
        <h3 style="font-size:2rem;margin:0"><?= $stats['ministeres'] ?></h3>
        <p style="margin:0">Ministères</p>
    </a>
    <a class="card card--link" href="<?= url('admin/actualites.php') ?>">
        <span class="card__icon"><?= icon('list', 24) ?></span>
        <h3 style="font-size:2rem;margin:0"><?= $stats['actualites'] ?></h3>
        <p style="margin:0">Actualités</p>
    </a>
    <a class="card card--link" href="<?= url('admin/messages.php') ?>">
        <span class="card__icon card__icon--red"><?= icon('mail', 24) ?></span>
        <h3 style="font-size:2rem;margin:0"><?= $stats['messages'] ?></h3>
        <p style="margin:0">Messages <span class="pill pill--off"><?= $stats['non_lus'] ?> non lus</span></p>
    </a>
</div>

<div class="grid grid--2">
    <div>
        <h2><?= icon('mail', 20) ?> Derniers messages</h2>
        <div class="table-wrap">
            <table class="data">
                <thead><tr><th>Nom</th><th>Sujet</th><th>Date</th></tr></thead>
                <tbody>
                <?php if (!$derniers_msg): ?>
                    <tr><td colspan="3" style="text-align:center;color:var(--muted)">Aucun message</td></tr>
                <?php else: foreach ($derniers_msg as $m): ?>
                    <tr>
                        <td><?= e($m['nom']) ?><?php if (!$m['lu']): ?> <span class="pill pill--online">Nouveau</span><?php endif; ?></td>
                        <td><?= e($m['sujet']) ?></td>
                        <td><?= e(date_fr($m['cree_le'])) ?></td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <h2><?= icon('list', 20) ?> Dernières actualités</h2>
        <div class="table-wrap">
            <table class="data">
                <thead><tr><th>Titre</th><th>Catégorie</th><th>Date</th></tr></thead>
                <tbody>
                <?php foreach ($derniers_art as $a): ?>
                    <tr>
                        <td><?= e(excerpt($a['titre'], 7)) ?></td>
                        <td><?= e($a['categorie']) ?></td>
                        <td><?= e(date_fr($a['date_pub'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div style="margin-top:26px">
    <h2><?= icon('plus-circle', 20) ?> Actions rapides</h2>
    <div class="hero__actions">
        <a class="btn btn--green" href="<?= url('admin/demarches.php?action=new') ?>"><?= icon('plus', 18) ?> Nouvelle démarche</a>
        <a class="btn btn--green" href="<?= url('admin/actualites.php?action=new') ?>"><?= icon('plus', 18) ?> Nouvelle actualité</a>
        <a class="btn btn--green" href="<?= url('admin/urgences.php?action=new') ?>"><?= icon('plus', 18) ?> Nouveau numéro</a>
    </div>
</div>

<?php admin_footer(); ?>
