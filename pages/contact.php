<?php
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Contact';
$page_description = 'Contactez le portail des services publics du Togo.';
$active = 'contact';

$errors = [];
$old = ['nom' => '', 'email' => '', 'sujet' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    // Anti-spam 1 : pot de miel (les robots remplissent les champs cachés)
    if (trim((string) ($_POST['site'] ?? '')) !== '') {
        flash('success', 'Merci ! Votre message a bien été envoyé. Nous vous répondrons dans les meilleurs délais.');
        redirect('pages/contact.php');
    }

    $old['nom']     = trim($_POST['nom'] ?? '');
    $old['email']   = trim($_POST['email'] ?? '');
    $old['sujet']   = trim($_POST['sujet'] ?? '');
    $old['message'] = trim($_POST['message'] ?? '');

    // Anti-spam 2 : limitation de fréquence (30 s entre deux envois)
    if (time() - (int) ($_SESSION['contact_last'] ?? 0) < 30) {
        $errors[] = 'Un message vient déjà d\'être envoyé. Merci de patienter quelques secondes.';
    }

    if ($old['nom'] === '') {
        $errors[] = 'Merci d\'indiquer votre nom.';
    }
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Merci d\'indiquer une adresse e-mail valide.';
    }
    if (mb_strlen($old['message']) < 10) {
        $errors[] = 'Votre message est trop court (10 caractères minimum).';
    }

    if (!$errors) {
        $stmt = db()->prepare('INSERT INTO messages (nom, email, sujet, message) VALUES (:nom, :email, :sujet, :message)');
        $stmt->execute([
            'nom'     => $old['nom'],
            'email'   => $old['email'],
            'sujet'   => $old['sujet'] !== '' ? $old['sujet'] : 'Sans objet',
            'message' => $old['message'],
        ]);
        $_SESSION['contact_last'] = time();
        flash('success', 'Merci ! Votre message a bien été envoyé. Nous vous répondrons dans les meilleurs délais.');
        redirect('pages/contact.php');
    }
}

require __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <nav class="breadcrumb"><a href="<?= url('index.php') ?>">Accueil</a><span>›</span>Contact</nav>
</div>

<section class="section section--tight">
    <div class="container">
        <div class="two-col">
            <div>
                <span class="kicker">Nous joindre</span>
                <h1>Contact</h1>
                <p style="color:var(--muted)">Une question sur une démarche, une suggestion ou un signalement ? Écrivez-nous via le formulaire ci-dessous.</p>

                <?php if ($errors): ?>
                    <div class="alert alert--error">
                        <ul style="margin:0;padding-left:1.1em">
                            <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" class="panel">
                    <?= csrf_field() ?>
                    <div aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden">
                        <label for="site">Ne pas remplir ce champ</label>
                        <input type="text" id="site" name="site" tabindex="-1" autocomplete="off">
                    </div>
                    <div class="form-grid">
                        <div class="form-field">
                            <label for="nom">Nom complet *</label>
                            <input type="text" id="nom" name="nom" value="<?= e($old['nom']) ?>" required>
                        </div>
                        <div class="form-field">
                            <label for="email">Adresse e-mail *</label>
                            <input type="email" id="email" name="email" value="<?= e($old['email']) ?>" required>
                        </div>
                    </div>
                    <div class="form-field">
                        <label for="sujet">Sujet</label>
                        <input type="text" id="sujet" name="sujet" value="<?= e($old['sujet']) ?>">
                    </div>
                    <div class="form-field">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" required><?= e($old['message']) ?></textarea>
                        <div class="form-hint">Décrivez votre demande le plus précisément possible.</div>
                    </div>
                    <button class="btn btn--green" type="submit"><?= icon('mail', 18) ?> Envoyer le message</button>
                </form>
            </div>

            <aside class="sticky">
                <div class="panel">
                    <h2><?= icon('building', 20) ?> Coordonnées</h2>
                    <ul class="info-list">
                        <li><?= icon('map-pin', 20) ?><span><strong>Adresse</strong>Palais du Gouvernement, Lomé, Togo</span></li>
                        <li><?= icon('mail', 20) ?><span><strong>E-mail</strong><a href="mailto:<?= e(ADMIN_EMAIL) ?>"><?= e(ADMIN_EMAIL) ?></a></span></li>
                        <li><?= icon('clock', 20) ?><span><strong>Horaires</strong>Lundi – Vendredi, 7h30 – 16h30</span></li>
                    </ul>
                </div>
                <div class="panel">
                    <h2><?= icon('phone', 20) ?> Urgence ?</h2>
                    <p style="font-size:.9rem;color:var(--muted)">Pour toute urgence, utilisez directement les numéros dédiés.</p>
                    <a class="btn btn--danger" style="width:100%;justify-content:center" href="<?= url('pages/urgences.php') ?>"><?= icon('alert', 18) ?> Numéros d'urgence</a>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
