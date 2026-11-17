<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $data = [
        'titre'      => trim($_POST['titre'] ?? ''),
        'extrait'    => trim($_POST['extrait'] ?? ''),
        'contenu'    => trim($_POST['contenu'] ?? ''),
        'categorie'  => trim($_POST['categorie'] ?? 'Communiqué'),
        'source'     => trim($_POST['source'] ?? ''),
        'source_url' => trim($_POST['source_url'] ?? ''),
        'auteur'     => trim($_POST['auteur'] ?? ''),
        'date_pub'   => trim($_POST['date_pub'] ?? '') !== '' ? str_replace('T', ' ', $_POST['date_pub']) . ':00' : date('Y-m-d H:i:s'),
        'actif'      => isset($_POST['actif']) ? 1 : 0,
    ];
    $editId = (int) ($_POST['id'] ?? 0);

    if ($data['titre'] === '') {
        flash('error', 'Le titre est obligatoire.');
    } else {
        if ($editId > 0) {
            $data['id'] = $editId;
            db()->prepare('UPDATE actualites SET titre=:titre, extrait=:extrait, contenu=:contenu, categorie=:categorie,
                source=:source, source_url=:source_url, auteur=:auteur, date_pub=:date_pub, actif=:actif WHERE id=:id')->execute($data);
            flash('success', 'Actualité mise à jour.');
        } else {
            $data['slug'] = slugify($data['titre']);
            $base = $data['slug']; $i = 2;
            while (db()->query('SELECT COUNT(*) FROM actualites WHERE slug = ' . db()->quote($data['slug']))->fetchColumn()) {
                $data['slug'] = $base . '-' . $i++;
            }
            db()->prepare('INSERT INTO actualites (titre,slug,extrait,contenu,categorie,source,source_url,auteur,date_pub,actif)
                VALUES (:titre,:slug,:extrait,:contenu,:categorie,:source,:source_url,:auteur,:date_pub,:actif)')->execute($data);
            flash('success', 'Actualité créée.');
        }
    }
    redirect('admin/actualites.php');
}

if ($action === 'delete' && $id > 0 && isset($_GET['csrf']) && hash_equals($_SESSION['csrf'], $_GET['csrf'])) {
    db()->prepare('DELETE FROM actualites WHERE id = ?')->execute([$id]);
    flash('success', 'Actualité supprimée.');
    redirect('admin/actualites.php');
}

$row = ['id' => 0, 'titre' => '', 'extrait' => '', 'contenu' => '', 'categorie' => 'Communiqué',
        'source' => '', 'source_url' => '', 'auteur' => '', 'date_pub' => date('Y-m-d H:i:s'), 'actif' => 1];
if (in_array($action, ['edit', 'new'], true) && $id > 0) {
    $stmt = db()->prepare('SELECT * FROM actualites WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch() ?: $row;
}

$cats = db()->query('SELECT DISTINCT categorie FROM actualites ORDER BY categorie')->fetchAll(PDO::FETCH_COLUMN);

if (in_array($action, ['edit', 'new'], true)) {
    admin_header($action === 'edit' ? 'Modifier une actualité' : 'Nouvelle actualité', 'actualites');
    ?>
    <form method="post" class="panel" style="max-width:900px">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
        <div class="form-field">
            <label for="titre">Titre *</label>
            <input type="text" id="titre" name="titre" value="<?= e($row['titre']) ?>" required>
        </div>
        <div class="form-grid">
            <div class="form-field">
                <label for="categorie">Catégorie</label>
                <input type="text" id="categorie" name="categorie" list="cats" value="<?= e($row['categorie']) ?>">
                <datalist id="cats">
                    <?php foreach ($cats as $c): ?><option value="<?= e($c) ?>"><?php endforeach; ?>
                    <option value="Communiqué"><option value="Numérique"><option value="Santé">
                    <option value="Économie"><option value="Éducation"><option value="Sécurité">
                </datalist>
            </div>
            <div class="form-field">
                <label for="date_pub">Date de publication</label>
                <input type="datetime-local" id="date_pub" name="date_pub"
                       value="<?= e(date('Y-m-d\TH:i', strtotime($row['date_pub']))) ?>">
            </div>
        </div>
        <div class="form-field">
            <label for="extrait">Extrait / résumé</label>
            <textarea id="extrait" name="extrait" maxlength="400" style="min-height:70px"><?= e($row['extrait']) ?></textarea>
        </div>
        <div class="form-field">
            <label for="contenu">Contenu complet</label>
            <textarea id="contenu" name="contenu" style="min-height:220px"><?= e($row['contenu']) ?></textarea>
            <div class="form-hint">Séparez les paragraphes par une ligne vide.</div>
        </div>
        <div class="form-grid">
            <div class="form-field">
                <label for="source">Source</label>
                <input type="text" id="source" name="source" value="<?= e($row['source']) ?>">
            </div>
            <div class="form-field">
                <label for="source_url">URL de la source</label>
                <input type="url" id="source_url" name="source_url" value="<?= e($row['source_url']) ?>">
            </div>
            <div class="form-field">
                <label for="auteur">Auteur</label>
                <input type="text" id="auteur" name="auteur" value="<?= e($row['auteur']) ?>">
            </div>
        </div>
        <div class="form-field">
            <label style="display:flex;align-items:center;gap:8px;font-weight:500">
                <input type="checkbox" name="actif" value="1" style="width:auto" <?= $row['actif'] ? 'checked' : '' ?>> Publier
            </label>
        </div>
        <div class="hero__actions" style="margin:0">
            <button class="btn btn--green" type="submit"><?= icon('check', 18) ?> Enregistrer</button>
            <a class="btn btn--ghost" href="<?= url('admin/actualites.php') ?>">Annuler</a>
        </div>
    </form>
    <?php
    admin_footer();
    exit;
}

$rows = db()->query('SELECT * FROM actualites ORDER BY date_pub DESC')->fetchAll();
admin_header('Actualités', 'actualites');
?>
<div style="margin-bottom:18px">
    <a class="btn btn--green" href="<?= url('admin/actualites.php?action=new') ?>"><?= icon('plus', 18) ?> Nouvelle actualité</a>
</div>
<div class="table-wrap">
    <table class="data">
        <thead><tr><th>Titre</th><th>Catégorie</th><th>Date</th><th>Statut</th><th style="text-align:right">Actions</th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><strong><?= e(excerpt($r['titre'], 9)) ?></strong></td>
                <td><?= e($r['categorie']) ?></td>
                <td><?= e(date_fr($r['date_pub'])) ?></td>
                <td><?= $r['actif'] ? '<span class="pill pill--on">Publié</span>' : '<span class="pill pill--off">Brouillon</span>' ?></td>
                <td class="actions">
                    <a class="btn btn--ghost btn--sm" href="<?= url('admin/actualites.php?action=edit&id=' . (int) $r['id']) ?>"><?= icon('edit', 16) ?> Modifier</a>
                    <a class="btn btn--danger btn--sm" data-confirm="Supprimer cette actualité ?"
                       href="<?= url('admin/actualites.php?action=delete&id=' . (int) $r['id'] . '&csrf=' . urlencode(csrf_token())) ?>"><?= icon('trash', 16) ?></a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php admin_footer(); ?>
