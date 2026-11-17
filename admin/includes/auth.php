<?php
/**
 * Authentification et layout du back-office
 */
require_once __DIR__ . '/../../includes/functions.php';

/** Exige une session admin, sinon redirige vers la connexion */
function require_login(): void
{
    if (empty($_SESSION['admin_id'])) {
        redirect('admin/login.php');
    }
}

/** Administrateur connecté */
function current_admin(): ?array
{
    if (empty($_SESSION['admin_id'])) {
        return null;
    }
    static $admin = null;
    if ($admin === null) {
        $stmt = db()->prepare('SELECT * FROM admins WHERE id = ? LIMIT 1');
        $stmt->execute([$_SESSION['admin_id']]);
        $admin = $stmt->fetch() ?: null;
    }
    return $admin;
}

/** En-tête du back-office */
function admin_header(string $title, string $active = ''): void
{
    $admin = current_admin();
    $nav = [
        'dashboard'  => 'Tableau de bord',
        'demarches'  => 'Démarches',
        'ministeres' => 'Ministères',
        'actualites' => 'Actualités',
        'urgences'   => 'Urgences',
        'messages'   => 'Messages',
        'compte'     => 'Mon compte',
    ];
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= e($title) ?> · Administration <?= e(SITE_NAME) ?></title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    </head>
    <body class="admin">
    <div class="admin__bar">
        <div class="container">
            <strong><?= togo_emblem(26) ?> Admin · <?= e(SITE_NAME) ?></strong>
            <nav>
                <?php foreach ($nav as $key => $label): ?>
                    <a href="<?= url('admin/' . ($key === 'dashboard' ? 'index.php' : $key . '.php')) ?>"
                       class="<?= $active === $key ? 'is-active' : '' ?>"><?= e($label) ?></a>
                <?php endforeach; ?>
                <a href="<?= url('index.php') ?>" target="_blank">Voir le site</a>
                <a href="<?= url('admin/logout.php') ?>"><?= icon('logout', 16) ?> Déconnexion</a>
            </nav>
        </div>
    </div>
    <div class="admin__wrap">
        <div class="container">
            <?php foreach (get_flashes() as $flash): ?>
                <div class="alert alert--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
            <?php endforeach; ?>
            <div class="admin__head">
                <h1 style="margin:0"><?= e($title) ?></h1>
                <?php if ($admin): ?>
                    <span style="color:var(--muted);font-size:.9rem">Connecté : <strong><?= e($admin['nom_complet'] ?: $admin['username']) ?></strong></span>
                <?php endif; ?>
            </div>
    <?php
}

/** Pied de page du back-office */
function admin_footer(): void
{
    ?>
        </div>
    </div>
    <script>document.querySelectorAll('[data-confirm]').forEach(function(el){el.addEventListener('click',function(e){if(!confirm(el.getAttribute('data-confirm'))){e.preventDefault();}});});</script>
    </body>
    </html>
    <?php
}

/* ---------------------------------------------------------
 *  (Les symboles togo_flag() et togo_emblem() sont définis
 *   dans includes/functions.php et partagés avec le site public.)
 * -------------------------------------------------------*/
