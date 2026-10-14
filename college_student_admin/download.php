<?php
require __DIR__ . '/config.php';
$fileId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt=$pdo->prepare("SELECT * FROM student_files WHERE id=?"); $stmt->execute([$fileId]); $file=$stmt->fetch();
if (!$file) { http_response_code(404); exit('File not found.'); }
$path=__DIR__ . '/' . $file['file_path'];
if (!is_file($path)) { http_response_code(404); exit('Stored file is missing.'); }
header('X-Content-Type-Options: nosniff');
header('Content-Type: ' . $file['mime_type']);
header('Content-Length: ' . filesize($path));
header('Content-Disposition: inline; filename="' . str_replace(['"',"\r","\n"],'',basename($file['original_name'])) . '"');
readfile($path);
