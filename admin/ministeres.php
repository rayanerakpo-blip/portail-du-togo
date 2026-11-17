<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $data = [
        'nom'         => trim($_POST['nom'] ?? ''),
        'sigle'       => trim($_POST['sigle'] ?? ''),
        'ministre'    => trim($_POST['ministre'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'missions'    => trim($_POST['missions'] ?? ''),
        'site_web'    => trim($_POST['site_web'] ?? ''),
        'email'       => trim($_POST['email'] ?? ''),
        'telephone'   => trim($_POST['telephone'] ?? ''),
        'adresse'     => trim($_POST['adresse'] ?? ''),
        'ordre'       => (int) ($_POST['ordre'] ?? 0),
        'actif'       => isset($_POST['actif']) ? 1 : 0,
    ];
    $editId = (int) ($_POST['id'] ?? 0);

    if ($data['nom'] === '') {
        flash('error', 'Le nom est obligatoire.');
    } else {
        if ($editId > 0) {
            $data['id'] = $editId;
            db()->prepare('UPDATE ministeres SET nom=:nom, sigle=:sigle, ministre=:ministre, description=:description,
                missions=:missions, site_web=:site_web, email=:email, telephone=:telephone, adresse=:adresse,
                ordre=:ordre, actif=:actif WHERE id=:id')->execute($data);
            flash('success', 'Ministère mis à jour.');
        } else {
            $data['slug'] = slugify($data['nom']);
            $base = $data['slug']; $i = 2;
            while (db()->query('SELECT COUNT(*) FROM ministeres WHERE slug = ' . db()->quote($data['slug']))->fetchColumn()) {
                $data['slug'] = $base . '-' . $i++;
            }
            db()->prepare('INSERT INTO ministeres (nom,slug,sigle,ministre,description,missions,site_web,email,telephone,adresse,ordre,actif)
                VALUES (:nom,:slug,:sigle,:ministre,:description,:missions,:site_web,:email,:telephone,:adresse,:ordre,:actif)')->execute($data);
            flash('success', 'Ministère créé.');
        }
    }
    redirect('admin/ministeres.php');
}

if ($action === 'delete' && $id > 0 && isset($_GET['csrf']) && hash_equals($_SESSION['csrf'], $_GET['csrf'])) {
    db()->prepare('DELETE FROM ministeres WHERE id = ?')->execute([$id]);
    flash('success', 'Ministère supprimé.');
    redirect('admin/ministeres.php');
}

$row = ['id' => 0, 'nom' => '', 'sigle' => '', 'ministre' => '', 'description' => '', 'missions' => '',
        'site_web' => '', 'email' => '', 'telephone' => '', 'adresse' => '', 'ordre' => 0, 'actif' => 1];
if (in_array($action, ['edit', 'new'], true) && $id > 0) {
    $stmt = db()->prepare('SELECT * FROM ministeres WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch() ?: $row;
}

if (in_array($action, ['edit', 'new'], true)) {
    admin_header($action === 'edit' ? 'Modifier un ministère' : 'Nouveau ministère', 'ministeres');
    ?>
    <form method="post" class="panel" style="max-width:900px">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
        <div class="form-field">
            <label for="nom">Nom du ministère *</label>
            <input type="text" id="nom" name="nom" value="<?= e($row['nom']) ?>" required>
        </div>
        <div class="form-grid">
            <div class="form-field">
                <label for="sigle">Sigle</label>
                <input type="text" id="sigle" name="sigle" value="<?= e($row['sigle']) ?>">
            </div>
            <div class="form-field">
                <label for="ministre">Ministre (optionnel)</label>
                <input type="text" id="ministre" name="ministre" value="<?= e($row['ministre']) ?>">
            </div>
        </div>
        <div class="form-field">
            <label for="description">Description</label>
            <textarea id="description" name="description"><?= e($row['description']) ?></textarea>
        </div>
        <div class="form-field">
            <label for="missions">Missions (une par ligne)</label>
            <textarea id="missions" name="missions"><?= e($row['missions']) ?></textarea>
        </div>
        <div class="form-grid">
            <div class="form-field">
                <label for="site_web">Site web</label>
                <input type="url" id="site_web" name="site_web" value="<?= e($row['site_web']) ?>" placeholder="https://">
            </div>
            <div class="form-field">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="<?= e($row['email']) ?>">
            </div>
            <div class="form-field">
                <label for="telephone">Téléphone</label>
                <input type="text" id="telephone" name="telephone" value="<?= e($row['telephone']) ?>">
            </div>
            <div class="form-field">
                <label for="adresse">Adresse</label>
                <input type="text" id="adresse" name="adresse" value="<?= e($row['adresse']) ?>">
            </div>
            <div class="form-field">
                <label for="ordre">Ordre d'affichage</label>
                <input type="number" id="ordre" name="ordre" value="<?= (int) $row['ordre'] ?>">
            </div>
        </div>
        <div class="form-field">
            <label style="display:flex;align-items:center;gap:8px;font-weight:500">
                <input type="checkbox" name="actif" value="1" style="width:auto" <?= $row['actif'] ? 'checked' : '' ?>> Publier
            </label>
        </div>
        <div class="hero__actions" style="margin:0">
            <button class="btn btn--green" type="submit"><?= icon('check', 18) ?> Enregistrer</button>
            <a class="btn btn--ghost" href="<?= url('admin/ministeres.php') ?>">Annuler</a>
        </div>
    </form>
    <?php
    admin_footer();
    exit;
}

$rows = db()->query('SELECT * FROM ministeres ORDER BY ordre, nom')->fetchAll();
admin_header('Ministères', 'ministeres');
?>
<div style="margin-bottom:18px">
    <a class="btn btn--green" href="<?= url('admin/ministeres.php?action=new') ?>"><?= icon('plus', 18) ?> Nouveau ministère</a>
</div>
<div class="table-wrap">
    <table class="data">
        <thead><tr><th>#</th><th>Nom</th><th>Sigle</th><th>Statut</th><th style="text-align:right">Actions</th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= (int) $r['ordre'] ?></td>
                <td><strong><?= e($r['nom']) ?></strong></td>
                <td><?= e($r['sigle'] ?: '—') ?></td>
                <td><?= $r['actif'] ? '<span class="pill pill--on">Publié</span>' : '<span class="pill pill--off">Masqué</span>' ?></td>
                <td class="actions">
                    <a class="btn btn--ghost btn--sm" href="<?= url('admin/ministeres.php?action=edit&id=' . (int) $r['id']) ?>"><?= icon('edit', 16) ?> Modifier</a>
                    <a class="btn btn--danger btn--sm" data-confirm="Supprimer ce ministère ?"
                       href="<?= url('admin/ministeres.php?action=delete&id=' . (int) $r['id'] . '&csrf=' . urlencode(csrf_token())) ?>"><?= icon('trash', 16) ?></a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php admin_footer(); ?>
