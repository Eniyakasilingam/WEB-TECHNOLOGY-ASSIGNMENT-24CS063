<?php
require __DIR__ . '/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php');
verify_csrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) { flash('Invalid student record.','error'); redirect('index.php'); }
$stmt=$pdo->prepare("SELECT file_path FROM student_files WHERE student_id=?"); $stmt->execute([$id]); $paths=$stmt->fetchAll(PDO::FETCH_COLUMN);
$stmt=$pdo->prepare("DELETE FROM students WHERE id=?"); $stmt->execute([$id]);
if ($stmt->rowCount()) {
    foreach ($paths as $relative) { $path=__DIR__ . '/' . $relative; if (is_file($path)) unlink($path); }
    flash('Student record and associated files deleted.');
} else flash('Student record not found.','error');
redirect('index.php');
