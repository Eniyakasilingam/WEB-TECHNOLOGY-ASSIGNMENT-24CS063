<?php
require __DIR__ . '/config.php';

$search = trim($_GET['q'] ?? '');
$sql = "SELECT s.*, (SELECT COUNT(*) FROM student_files f WHERE f.student_id=s.id) AS file_count
        FROM students s";
$params = [];
if ($search !== '') {
    $sql .= " WHERE s.name LIKE :q OR s.roll_number LIKE :q OR s.course LIKE :q OR s.department LIKE :q";
    $params['q'] = "%$search%";
}
$sql .= " ORDER BY s.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();
$total = (int)$pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$fileTotal = (int)$pdo->query("SELECT COUNT(*) FROM student_files")->fetchColumn();
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>CampusVault · Student Admin</title><link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="ambient ambient-one"></div><div class="ambient ambient-two"></div>
<div class="app-shell">
  <aside class="sidebar glass">
    <a class="brand" href="index.php"><span class="brand-mark">C</span><span>Campus<span class="brand-light">Vault</span><small>STUDENT ADMIN</small></span></a>
    <div class="nav-label">WORKSPACE</div>
    <a class="nav-item active" href="index.php"><span>▦</span> Overview</a>
    <a class="nav-item" href="student_form.php"><span>＋</span> Add student</a>
    <div class="sidebar-bottom"><div class="secure-icon">⌑</div><strong>Private workspace</strong><p>Student records and documents, organised in one place.</p><span class="status"><i></i> System ready</span></div>
  </aside>
  <main class="main-content">
    <header class="topbar"><div><div class="eyebrow">CAMPUS MANAGEMENT / OVERVIEW</div><h1>Student records</h1><p class="muted">Manage student profiles and their important documents.</p></div><a class="button button-primary" href="student_form.php"><span>＋</span> Add student</a></header>
    <?php if ($flash): ?><div class="alert <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
    <section class="stats-grid">
      <article class="stat-card glass"><div class="stat-top"><span>Total students</span><span class="stat-icon purple">♙</span></div><strong><?= $total ?></strong><small>Student profiles registered</small></article>
      <article class="stat-card glass"><div class="stat-top"><span>Stored files</span><span class="stat-icon green">▤</span></div><strong><?= $fileTotal ?></strong><small>Images and documents</small></article>
      <article class="stat-card glass"><div class="stat-top"><span>Workspace</span><span class="stat-icon blue">◈</span></div><strong class="status-value">Secure</strong><small>Validated upload handling</small></article>
    </section>
    <section class="panel glass">
      <div class="panel-heading"><div><h2>All students</h2><p class="muted">Browse, search and manage student records.</p></div><span class="count-pill"><?= count($students) ?> records</span></div>
      <form class="search-form" method="get"><span>⌕</span><input type="search" name="q" placeholder="Search name, roll number, course..." value="<?= e($search) ?>"><button class="button button-secondary" type="submit">Search</button><?php if ($search !== ''): ?><a class="clear-link" href="index.php">Clear</a><?php endif; ?></form>
      <div class="table-wrap"><table><thead><tr><th>STUDENT</th><th>ROLL NUMBER</th><th>COURSE / DEPARTMENT</th><th>FILES</th><th>ADDED</th><th class="align-right">ACTIONS</th></tr></thead><tbody>
      <?php if (!$students): ?><tr><td colspan="6"><div class="empty-state"><span>⌕</span><strong>No students found</strong><p><?= $search ? 'Try another search term.' : 'Add your first student to get started.' ?></p></div></td></tr><?php endif; ?>
      <?php foreach ($students as $s): ?>
      <tr><td><div class="student-cell"><span class="avatar"><?= e(strtoupper(substr($s['name'], 0, 1))) ?></span><div><strong><?= e($s['name']) ?></strong><small><?= e($s['email'] ?: 'No email added') ?></small></div></div></td>
      <td><span class="roll-tag"><?= e($s['roll_number']) ?></span></td><td><strong class="course-name"><?= e($s['course']) ?></strong><small class="cell-sub"><?= e($s['department'] ?: 'Department not specified') ?></small></td>
      <td><span class="file-pill">▤ <?= (int)$s['file_count'] ?></span></td><td class="date-cell"><?= e(date('d M Y', strtotime($s['created_at']))) ?></td>
      <td><div class="actions"><a class="icon-button" title="View details" href="view_student.php?id=<?= (int)$s['id'] ?>">↗</a><a class="icon-button" title="Edit student" href="student_form.php?id=<?= (int)$s['id'] ?>">✎</a><form method="post" action="delete_student.php" onsubmit="return confirm('Delete this student and their uploaded files? This cannot be undone.')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$s['id'] ?>"><button class="icon-button danger" title="Delete student">⌫</button></form></div></td></tr>
      <?php endforeach; ?>
      </tbody></table></div>
      <div class="table-footer"><span>Showing <strong><?= count($students) ?></strong> of <strong><?= $total ?></strong> student records<?= $search ? ' · filtered' : '' ?></span><span class="live-indicator"><i></i> Live database</span></div>
    </section>
    <footer class="footer">CampusVault <span>•</span> Student information management <span class="footer-right">Built for secure campus administration</span></footer>
  </main>
</div>
<script src="assets/app.js"></script>
</body></html>
