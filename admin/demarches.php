<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$categories = get_categories();

/* ---------- Enregistrement ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $data = [
        'categorie_id'     => ($_POST['categorie_id'] ?? '') !== '' ? (int) $_POST['categorie_id'] : null,
        'titre'            => trim($_POST['titre'] ?? ''),
        'resume'           => trim($_POST['resume'] ?? ''),
        'description'      => trim($_POST['description'] ?? ''),
        'documents_requis' => trim($_POST['documents_requis'] ?? ''),
        'cout'             => trim($_POST['cout'] ?? ''),
        'delai'            => trim($_POST['delai'] ?? ''),
        'lieu'             => trim($_POST['lieu'] ?? ''),
        'lien_officiel'    => trim($_POST['lien_officiel'] ?? ''),
        'en_ligne'         => isset($_POST['en_ligne']) ? 1 : 0,
        'actif'            => isset($_POST['actif']) ? 1 : 0,
    ];
    $editId = (int) ($_POST['id'] ?? 0);

    if ($data['titre'] === '') {
        flash('error', 'Le titre est obligatoire.');
    } else {
        if ($editId > 0) {
            $sql = 'UPDATE demarches SET categorie_id=:categorie_id, titre=:titre, resume=:resume, description=:description,
                    documents_requis=:documents_requis, cout=:cout, delai=:delai, lieu=:lieu, lien_officiel=:lien_officiel,
                    en_ligne=:en_ligne, actif=:actif WHERE id=:id';
            $data['id'] = $editId;
            db()->prepare($sql)->execute($data);
            flash('success', 'Démarche mise à jour.');
        } else {
            $data['slug'] = slugify($data['titre']);
            // garantir l'unicité du slug
            $base = $data['slug']; $i = 2;
            while (db()->query('SELECT COUNT(*) FROM demarches WHERE slug = ' . db()->quote($data['slug']))->fetchColumn()) {
                $data['slug'] = $base . '-' . $i++;
            }
            $sql = 'INSERT INTO demarches (categorie_id,titre,slug,resume,description,documents_requis,cout,delai,lieu,lien_officiel,en_ligne,actif)
                    VALUES (:categorie_id,:titre,:slug,:resume,:description,:documents_requis,:cout,:delai,:lieu,:lien_officiel,:en_ligne,:actif)';
            db()->prepare($sql)->execute($data);
            flash('success', 'Démarche créée.');
        }
    }
    redirect('admin/demarches.php');
}

/* ---------- Suppression ---------- */
if ($action === 'delete' && $id > 0 && isset($_GET['csrf']) && hash_equals($_SESSION['csrf'], $_GET['csrf'])) {
    db()->prepare('DELETE FROM demarches WHERE id = ?')->execute([$id]);
    flash('success', 'Démarche supprimée.');
    redirect('admin/demarches.php');
}

/* ---------- Données pour le formulaire ---------- */
$row = [
    'id' => 0, 'categorie_id' => '', 'titre' => '', 'resume' => '', 'description' => '',
    'documents_requis' => '', 'cout' => '', 'delai' => '', 'lieu' => '', 'lien_officiel' => '',
    'en_ligne' => 0, 'actif' => 1,
];
if (in_array($action, ['edit', 'new'], true) && $id > 0) {
    $stmt = db()->prepare('SELECT * FROM demarches WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if ($found) { $row = $found; }
}

if (in_array($action, ['edit', 'new'], true)) {
    admin_header($action === 'edit' ? 'Modifier une démarche' : 'Nouvelle démarche', 'demarches');
    ?>
    <form method="post" class="panel" style="max-width:900px">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">

        <div class="form-grid">
            <div class="form-field">
                <label for="titre">Titre *</label>
                <input type="text" id="titre" name="titre" value="<?= e($row['titre']) ?>" required>
            </div>
            <div class="form-field">
                <label for="categorie_id">Catégorie</label>
                <select id="categorie_id" name="categorie_id">
                    <option value="">— Aucune —</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int) $c['id'] ?>" <?= (int) $row['categorie_id'] === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-field">
            <label for="resume">Résumé court</label>
            <input type="text" id="resume" name="resume" value="<?= e($row['resume']) ?>" maxlength="300">
        </div>

        <div class="form-field">
            <label for="description">Description</label>
            <textarea id="description" name="description"><?= e($row['description']) ?></textarea>
        </div>

        <div class="form-field">
            <label for="documents_requis">Documents requis (un par ligne)</label>
            <textarea id="documents_requis" name="documents_requis"><?= e($row['documents_requis']) ?></textarea>
            <div class="form-hint">Saisissez chaque document sur une nouvelle ligne.</div>
        </div>

        <div class="form-grid">
            <div class="form-field">
                <label for="cout">Coût</label>
                <input type="text" id="cout" name="cout" value="<?= e($row['cout']) ?>">
            </div>
            <div class="form-field">
                <label for="delai">Délai</label>
                <input type="text" id="delai" name="delai" value="<?= e($row['delai']) ?>">
            </div>
            <div class="form-field">
                <label for="lieu">Lieu / guichet</label>
                <input type="text" id="lieu" name="lieu" value="<?= e($row['lieu']) ?>">
            </div>
            <div class="form-field">
                <label for="lien_officiel">Lien officiel (URL)</label>
                <input type="url" id="lien_officiel" name="lien_officiel" value="<?= e($row['lien_officiel']) ?>" placeholder="https://">
            </div>
        </div>

        <div class="form-field">
            <label style="display:flex;align-items:center;gap:8px;font-weight:500">
                <input type="checkbox" name="en_ligne" value="1" style="width:auto" <?= $row['en_ligne'] ? 'checked' : '' ?>> Démarche disponible en ligne
            </label>
            <label style="display:flex;align-items:center;gap:8px;font-weight:500;margin-top:8px">
                <input type="checkbox" name="actif" value="1" style="width:auto" <?= $row['actif'] ? 'checked' : '' ?>> Publier (visible sur le site)
            </label>
        </div>

        <div class="hero__actions" style="margin:0">
            <button class="btn btn--green" type="submit"><?= icon('check', 18) ?> Enregistrer</button>
            <a class="btn btn--ghost" href="<?= url('admin/demarches.php') ?>">Annuler</a>
        </div>
    </form>
    <?php
    admin_footer();
    exit;
}

/* ---------- Liste ---------- */
$rows = db()->query('SELECT d.*, c.nom AS categorie FROM demarches d LEFT JOIN categories c ON c.id = d.categorie_id ORDER BY d.titre')->fetchAll();
admin_header('Démarches', 'demarches');
?>
<div style="margin-bottom:18px">
    <a class="btn btn--green" href="<?= url('admin/demarches.php?action=new') ?>"><?= icon('plus', 18) ?> Nouvelle démarche</a>
</div>
<div class="table-wrap">
    <table class="data">
        <thead>
            <tr><th>Titre</th><th>Catégorie</th><th>Vues</th><th>En ligne</th><th>Statut</th><th style="text-align:right">Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><strong><?= e($r['titre']) ?></strong></td>
                <td><?= e($r['categorie'] ?? '—') ?></td>
                <td><?= (int) $r['vues'] ?></td>
                <td><?= $r['en_ligne'] ? '<span class="pill pill--online">Oui</span>' : '<span class="pill pill--off">Non</span>' ?></td>
                <td><?= $r['actif'] ? '<span class="pill pill--on">Publié</span>' : '<span class="pill pill--off">Brouillon</span>' ?></td>
                <td class="actions">
                    <a class="btn btn--ghost btn--sm" href="<?= url('admin/demarches.php?action=edit&id=' . (int) $r['id']) ?>"><?= icon('edit', 16) ?> Modifier</a>
                    <a class="btn btn--danger btn--sm" data-confirm="Supprimer cette démarche ?"
                       href="<?= url('admin/demarches.php?action=delete&id=' . (int) $r['id'] . '&csrf=' . urlencode(csrf_token())) ?>"><?= icon('trash', 16) ?></a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php admin_footer(); ?>
