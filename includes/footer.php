</main>

<footer class="site-footer">
    <div class="container site-footer__grid">
        <div class="site-footer__col">
            <div class="site-footer__brand">
                <?= togo_emblem(58) ?>
                <span>
                    <strong><?= e(SITE_NAME) ?></strong>
                    <em class="site-footer__devise">Travail · Liberté · Patrie</em>
                </span>
            </div>
            <p class="site-footer__desc"><?= e(SITE_TAGLINE) ?></p>
        </div>

        <div class="site-footer__col">
            <h3>Services</h3>
            <ul>
                <li><a href="<?= url('pages/demarches.php') ?>">Toutes les démarches</a></li>
                <li><a href="<?= url('pages/demarches.php?categorie=identite-voyage') ?>">Identité &amp; voyage</a></li>
                <li><a href="<?= url('pages/demarches.php?categorie=etat-civil') ?>">État civil</a></li>
                <li><a href="<?= url('pages/demarches.php?categorie=fiscalite-impots') ?>">Impôts &amp; taxes</a></li>
                <li><a href="<?= url('pages/demarches.php?categorie=entreprise-emploi') ?>">Entreprise &amp; emploi</a></li>
            </ul>
        </div>

        <div class="site-footer__col">
            <h3>Institutions</h3>
            <ul>
                <li><a href="<?= url('pages/ministeres.php') ?>">Ministères &amp; institutions</a></li>
                <li><a href="<?= url('pages/actualites.php') ?>">Actualités &amp; communiqués</a></li>
                <li><a href="<?= url('pages/a-propos.php') ?>">Le Togo en bref</a></li>
                <li><a href="<?= url('pages/urgences.php') ?>">Numéros d'urgence</a></li>
                <li><a href="<?= url('pages/contact.php') ?>">Nous contacter</a></li>
            </ul>
        </div>

        <div class="site-footer__col">
            <h3>Contact</h3>
            <ul class="site-footer__contact">
                <li><?= icon('building', 18) ?> Palais du Gouvernement, Lomé, Togo</li>
                <li><?= icon('mail', 18) ?> <a href="mailto:<?= e(ADMIN_EMAIL) ?>"><?= e(ADMIN_EMAIL) ?></a></li>
                <li><?= icon('phone', 18) ?> Standard : 117 / 118</li>
                <li><?= icon('clock', 18) ?> Lun – Ven, 7h30 – 16h30</li>
            </ul>
        </div>
    </div>

    <div class="container site-footer__bottom">
        <p>© <?= date('Y') ?> <?= e(SITE_NAME) ?> — Portail de démonstration inspiré des services publics togolais.</p>
        <p class="site-footer__legal">
            <a href="<?= url('pages/a-propos.php') ?>">À propos</a>
            <a href="<?= url('pages/contact.php') ?>">Contact</a>
            <a href="<?= url('admin/') ?>">Administration</a>
        </p>
    </div>
</footer>

<button class="to-top" type="button" aria-label="Revenir en haut de la page" data-top><?= icon('arrow-right', 20) ?></button>

<script src="<?= asset('js/main.js') ?>" defer></script>
</body>
</html>
