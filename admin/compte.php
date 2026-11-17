<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$admin = current_admin();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $nom     = trim($_POST['nom_complet'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $current = (string) ($_POST['current_password'] ?? '');
    $new     = (string) ($_POST['new_password'] ?? '');
    $confirm = (string) ($_POST['confirm_password'] ?? '');

    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'L\'adresse e-mail n\'est pas valide.';
    }

    // Changement de mot de passe (uniquement si un nouveau est saisi)
    $changing = ($new !== '' || $confirm !== '' || $current !== '');
    if ($changing) {
        if (!password_verify($current, $admin['password_hash'])) {
            $errors[] = 'Le mot de passe actuel est incorrect.';
        } elseif (strlen($new) < 8) {
            $errors[] = 'Le nouveau mot de passe doit contenir au moins 8 caractères.';
        } elseif ($new !== $confirm) {
            $errors[] = 'Les deux mots de passe ne correspondent pas.';
        }
    }

    if (!$errors) {
        if ($changing) {
            db()->prepare('UPDATE admins SET nom_complet = ?, email = ?, password_hash = ? WHERE id = ?')
                ->execute([$nom, $email, password_hash($new, PASSWORD_DEFAULT), $admin['id']]);
            log_admin('changement de mot de passe');
            flash('success', 'Mot de passe modifié avec succès.');
        } else {
            db()->prepare('UPDATE admins SET nom_complet = ?, email = ? WHERE id = ?')
                ->execute([$nom, $email, $admin['id']]);
            log_admin('mise à jour du profil');
            flash('success', 'Profil mis à jour.');
        }
        redirect('admin/compte.php');
    }
}

admin_header('Mon compte', 'compte');
?>

<div class="two-col" style="align-items:start">
    <div class="panel">
        <h2><?= icon('user', 20) ?> Profil administrateur</h2>

        <?php if ($errors): ?>
            <div class="alert alert--error">
                <ul style="margin:0;padding-left:18px">
                    <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post">
            <?= csrf_field() ?>

            <div class="form-field">
                <label for="username">Identifiant</label>
                <input type="text" id="username" value="<?= e($admin['username']) ?>" disabled>
                <div class="form-hint">L'identifiant de connexion ne peut pas être modifié.</div>
            </div>

            <div class="form-field">
                <label for="nom_complet">Nom complet</label>
                <input type="text" id="nom_complet" name="nom_complet" value="<?= e($admin['nom_complet'] ?? '') ?>">
            </div>

            <div class="form-field">
                <label for="email">Adresse e-mail</label>
                <input type="email" id="email" name="email" value="<?= e($admin['email'] ?? '') ?>">
            </div>

            <hr class="divider">
            <h3 style="margin-top:0"><?= icon('shield', 18) ?> Changer le mot de passe</h3>
            <p class="text-muted" style="font-size:.88rem;margin-top:-6px">
                Laissez ces champs vides pour conserver le mot de passe actuel.
            </p>

            <div class="form-field">
                <label for="current_password">Mot de passe actuel</label>
                <input type="password" id="current_password" name="current_password" autocomplete="current-password">
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label for="new_password">Nouveau mot de passe</label>
                    <input type="password" id="new_password" name="new_password" autocomplete="new-password" minlength="8">
                </div>
                <div class="form-field">
                    <label for="confirm_password">Confirmation</label>
                    <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" minlength="8">
                </div>
            </div>

            <button class="btn btn--green" type="submit"><?= icon('check', 18) ?> Enregistrer</button>
        </form>
    </div>

    <aside class="sticky">
        <div class="panel">
            <h2><?= icon('user', 20) ?> Informations</h2>
            <ul class="info-list">
                <li><?= icon('clock', 20) ?><span><strong>Dernière connexion</strong>
                    <?= $admin['derniere_connexion'] ? e(date_fr($admin['derniere_connexion'])) : '—' ?></span></li>
                <li><?= icon('shield', 20) ?><span><strong>Sécurité</strong>
                    Mot de passe haché (bcrypt). Utilisez 8 caractères minimum.</span></li>
            </ul>
        </div>
    </aside>
</div>

<?php admin_footer(); ?>
