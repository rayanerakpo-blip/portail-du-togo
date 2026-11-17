<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Marquer comme lu / supprimer
if ($action === 'read' && $id > 0 && isset($_GET['csrf']) && hash_equals($_SESSION['csrf'], $_GET['csrf'])) {
    db()->prepare('UPDATE messages SET lu = 1 WHERE id = ?')->execute([$id]);
    redirect('admin/messages.php');
}
if ($action === 'delete' && $id > 0 && isset($_GET['csrf']) && hash_equals($_SESSION['csrf'], $_GET['csrf'])) {
    db()->prepare('DELETE FROM messages WHERE id = ?')->execute([$id]);
    flash('success', 'Message supprimé.');
    redirect('admin/messages.php');
}

// Export CSV de tous les messages (compatible Excel UTF-8)
if ($action === 'export' && isset($_GET['csrf']) && hash_equals($_SESSION['csrf'], $_GET['csrf'])) {
    $all = db()->query('SELECT * FROM messages ORDER BY cree_le DESC')->fetchAll();
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="messages-togo-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF"); // BOM UTF-8 pour Excel
    fputcsv($out, ['Date', 'Nom', 'E-mail', 'Sujet', 'Message', 'Lu']);
    foreach ($all as $m) {
        fputcsv($out, [date_fr($m['cree_le']), $m['nom'], $m['email'], $m['sujet'], $m['message'], $m['lu'] ? 'Oui' : 'Non']);
    }
    fclose($out);
    exit;
}

$rows = db()->query('SELECT * FROM messages ORDER BY cree_le DESC')->fetchAll();
admin_header('Messages reçus', 'messages');
?>
<div class="toolbar" style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:16px;flex-wrap:wrap">
    <span style="color:var(--muted);font-size:.9rem"><?= count($rows) ?> message(s) au total</span>
    <?php if ($rows): ?>
        <a class="btn btn--green btn--sm" href="<?= url('admin/messages.php?action=export&csrf=' . urlencode(csrf_token())) ?>">
            <?= icon('list', 16) ?> Exporter en CSV
        </a>
    <?php endif; ?>
</div>
<div class="table-wrap">
    <table class="data">
        <thead><tr><th>Date</th><th>Nom</th><th>E-mail</th><th>Sujet</th><th>Message</th><th style="text-align:right">Actions</th></tr></thead>
        <tbody>
        <?php if (!$rows): ?>
            <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:40px">Aucun message pour le moment.</td></tr>
        <?php else: foreach ($rows as $r): ?>
            <tr>
                <td style="white-space:nowrap"><?= e(date_fr($r['cree_le'])) ?></td>
                <td><strong><?= e($r['nom']) ?></strong><?php if (!$r['lu']): ?> <span class="pill pill--online">Nouveau</span><?php endif; ?></td>
                <td><a href="mailto:<?= e($r['email']) ?>"><?= e($r['email']) ?></a></td>
                <td><?= e($r['sujet']) ?></td>
                <td style="max-width:340px"><?= nl2br(e(excerpt($r['message'], 30))) ?></td>
                <td class="actions">
                    <?php if (!$r['lu']): ?>
                        <a class="btn btn--ghost btn--sm" href="<?= url('admin/messages.php?action=read&id=' . (int) $r['id'] . '&csrf=' . urlencode(csrf_token())) ?>"><?= icon('check', 16) ?> Lu</a>
                    <?php endif; ?>
                    <a class="btn btn--danger btn--sm" data-confirm="Supprimer ce message ?"
                       href="<?= url('admin/messages.php?action=delete&id=' . (int) $r['id'] . '&csrf=' . urlencode(csrf_token())) ?>"><?= icon('trash', 16) ?></a>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<?php admin_footer(); ?>
