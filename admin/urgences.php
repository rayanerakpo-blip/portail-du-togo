<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$icones = ['phone', 'shield', 'fire', 'plus', 'child', 'heart', 'alert', 'bolt'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $data = [
        'nom'         => trim($_POST['nom'] ?? ''),
        'numero'      => trim($_POST['numero'] ?? ''),
        'categorie'   => trim($_POST['categorie'] ?? 'Urgence'),
        'description' => trim($_POST['description'] ?? ''),
        'icone'       => in_array($_POST['icone'] ?? '', $icones, true) ? $_POST['icone'] : 'phone',
        'ordre'       => (int) ($_POST['ordre'] ?? 0),
        'actif'       => isset($_POST['actif']) ? 1 : 0,
    ];
    $editId = (int) ($_POST['id'] ?? 0);

    if ($data['nom'] === '' || $data['numero'] === '') {
        flash('error', 'Le nom et le numéro sont obligatoires.');
    } else {
        if ($editId > 0) {
            $data['id'] = $editId;
            db()->prepare('UPDATE urgences SET nom=:nom, numero=:numero, categorie=:categorie, description=:description,
                icone=:icone, ordre=:ordre, actif=:actif WHERE id=:id')->execute($data);
            flash('success', 'Numéro mis à jour.');
        } else {
            db()->prepare('INSERT INTO urgences (nom,numero,categorie,description,icone,ordre,actif)
                VALUES (:nom,:numero,:categorie,:description,:icone,:ordre,:actif)')->execute($data);
            flash('success', 'Numéro ajouté.');
        }
    }
    redirect('admin/urgences.php');
}

if ($action === 'delete' && $id > 0 && isset($_GET['csrf']) && hash_equals($_SESSION['csrf'], $_GET['csrf'])) {
    db()->prepare('DELETE FROM urgences WHERE id = ?')->execute([$id]);
    flash('success', 'Numéro supprimé.');
    redirect('admin/urgences.php');
}

$row = ['id' => 0, 'nom' => '', 'numero' => '', 'categorie' => 'Urgence', 'description' => '',
        'icone' => 'phone', 'ordre' => 0, 'actif' => 1];
if (in_array($action, ['edit', 'new'], true) && $id > 0) {
    $stmt = db()->prepare('SELECT * FROM urgences WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch() ?: $row;
}

if (in_array($action, ['edit', 'new'], true)) {
    admin_header($action === 'edit' ? 'Modifier un numéro' : 'Nouveau numéro', 'urgences');
    ?>
    <form method="post" class="panel" style="max-width:700px">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
        <div class="form-grid">
            <div class="form-field">
                <label for="nom">Nom du service *</label>
                <input type="text" id="nom" name="nom" value="<?= e($row['nom']) ?>" required>
            </div>
            <div class="form-field">
                <label for="numero">Numéro *</label>
                <input type="text" id="numero" name="numero" value="<?= e($row['numero']) ?>" required>
            </div>
            <div class="form-field">
                <label for="categorie">Catégorie</label>
                <input type="text" id="categorie" name="categorie" list="gcats" value="<?= e($row['categorie']) ?>">
                <datalist id="gcats">
                    <option value="Urgence"><option value="Sécurité"><option value="Secours">
                    <option value="Santé"><option value="Assistance"><option value="Services">
                </datalist>
            </div>
            <div class="form-field">
                <label for="icone">Icône</label>
                <select id="icone" name="icone">
                    <?php foreach ($icones as $ic): ?>
                        <option value="<?= e($ic) ?>" <?= $row['icone'] === $ic ? 'selected' : '' ?>><?= e($ic) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-field">
            <label for="description">Description</label>
            <input type="text" id="description" name="description" value="<?= e($row['description']) ?>" maxlength="300">
        </div>
        <div class="form-field">
            <label for="ordre">Ordre d'affichage</label>
            <input type="number" id="ordre" name="ordre" value="<?= (int) $row['ordre'] ?>">
        </div>
        <div class="form-field">
            <label style="display:flex;align-items:center;gap:8px;font-weight:500">
                <input type="checkbox" name="actif" value="1" style="width:auto" <?= $row['actif'] ? 'checked' : '' ?>> Actif
            </label>
        </div>
        <div class="hero__actions" style="margin:0">
            <button class="btn btn--green" type="submit"><?= icon('check', 18) ?> Enregistrer</button>
            <a class="btn btn--ghost" href="<?= url('admin/urgences.php') ?>">Annuler</a>
        </div>
    </form>
    <?php
    admin_footer();
    exit;
}

$rows = db()->query('SELECT * FROM urgences ORDER BY ordre, nom')->fetchAll();
admin_header("Numéros d'urgence", 'urgences');
?>
<div style="margin-bottom:18px">
    <a class="btn btn--green" href="<?= url('admin/urgences.php?action=new') ?>"><?= icon('plus', 18) ?> Nouveau numéro</a>
</div>
<div class="table-wrap">
    <table class="data">
        <thead><tr><th>Ordre</th><th>Service</th><th>Numéro</th><th>Catégorie</th><th>Statut</th><th style="text-align:right">Actions</th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= (int) $r['ordre'] ?></td>
                <td><strong><?= e($r['nom']) ?></strong></td>
                <td><span class="pill pill--online"><?= e($r['numero']) ?></span></td>
                <td><?= e($r['categorie']) ?></td>
                <td><?= $r['actif'] ? '<span class="pill pill--on">Actif</span>' : '<span class="pill pill--off">Inactif</span>' ?></td>
                <td class="actions">
                    <a class="btn btn--ghost btn--sm" href="<?= url('admin/urgences.php?action=edit&id=' . (int) $r['id']) ?>"><?= icon('edit', 16) ?> Modifier</a>
                    <a class="btn btn--danger btn--sm" data-confirm="Supprimer ce numéro ?"
                       href="<?= url('admin/urgences.php?action=delete&id=' . (int) $r['id'] . '&csrf=' . urlencode(csrf_token())) ?>"><?= icon('trash', 16) ?></a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php admin_footer(); ?>
