<?php
require_once __DIR__ . '/includes/auth.php';

// Déjà connecté ?
if (!empty($_SESSION['admin_id'])) {
    redirect('admin/index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $username = trim($_POST['username'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    $stmt = db()->prepare('SELECT * FROM admins WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int) $admin['id'];
        db()->prepare('UPDATE admins SET derniere_connexion = NOW() WHERE id = ?')->execute([$admin['id']]);
        flash('success', 'Connexion réussie. Bienvenue !');
        redirect('admin/index.php');
    }
    $error = 'Identifiant ou mot de passe incorrect.';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion · Administration</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <div style="text-align:center;margin-bottom:18px">
            <?= togo_emblem(46) ?>
        </div>
        <h1>Administration</h1>
        <p><?= e(SITE_NAME) ?> — espace réservé</p>

        <?php if ($error): ?>
            <div class="alert alert--error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <?= csrf_field() ?>
            <div class="form-field">
                <label for="username">Identifiant</label>
                <input type="text" id="username" name="username" required autofocus autocomplete="username">
            </div>
            <div class="form-field">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            <button class="btn btn--green" style="width:100%;justify-content:center" type="submit">Se connecter</button>
        </form>

        <p style="text-align:center;margin:18px 0 0;font-size:.82rem;color:var(--muted)">
            Identifiants par défaut : <strong>admin</strong> / <strong>Admin@Togo2026</strong>
        </p>
        <p style="text-align:center;margin:8px 0 0"><a href="<?= url('index.php') ?>">← Retour au site</a></p>
    </div>
</div>
</body>
</html>
