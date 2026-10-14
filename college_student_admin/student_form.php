<?php
require __DIR__ . '/config.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: null;
$student = ['name'=>'','roll_number'=>'','email'=>'','phone'=>'','course'=>'','department'=>'','year_level'=>''];
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id=?");
    $stmt->execute([$id]); $found = $stmt->fetch();
    if (!$found) { flash('Student record not found.', 'error'); redirect('index.php'); }
    $student = $found;
}
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    foreach (['name','roll_number','email','phone','course','department','year_level'] as $key) $student[$key] = trim($_POST[$key] ?? '');
    if ($student['name'] === '') $errors[] = 'Student name is required.';
    if ($student['roll_number'] === '') $errors[] = 'Roll number is required.';
    if ($student['course'] === '') $errors[] = 'Course is required.';
    if ($student['email'] !== '' && !filter_var($student['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if ($student['year_level'] !== '' && (!ctype_digit($student['year_level']) || (int)$student['year_level'] < 1 || (int)$student['year_level'] > 8)) $errors[] = 'Year must be between 1 and 8.';
    if (mb_strlen($student['name']) > 120 || mb_strlen($student['roll_number']) > 40) $errors[] = 'Name or roll number is too long.';
    $uploads = [];
    $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','application/pdf'=>'pdf'];
    $maxSize = 5 * 1024 * 1024;
    if (!empty($_FILES['documents']['name'][0])) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        foreach ($_FILES['documents']['name'] as $i => $original) {
            if ($_FILES['documents']['error'][$i] === UPLOAD_ERR_NO_FILE) continue;
            if ($_FILES['documents']['error'][$i] !== UPLOAD_ERR_OK) { $errors[] = "Upload failed for " . basename($original) . "."; continue; }
            if ($_FILES['documents']['size'][$i] > $maxSize) { $errors[] = basename($original) . ' exceeds the 5 MB limit.'; continue; }
            $mime = $finfo->file($_FILES['documents']['tmp_name'][$i]);
            if (!isset($allowed[$mime])) { $errors[] = basename($original) . ' has an unsupported file type. Use JPG, PNG, WEBP or PDF.'; continue; }
            $uploads[] = ['tmp'=>$_FILES['documents']['tmp_name'][$i], 'original'=>basename($original), 'mime'=>$mime, 'size'=>(int)$_FILES['documents']['size'][$i], 'ext'=>$allowed[$mime]];
        }
    }
    if (!$errors) {
        $storedPaths = [];
        try {
            $pdo->beginTransaction();
            if ($id) {
                $stmt = $pdo->prepare("UPDATE students SET name=?,roll_number=?,email=?,phone=?,course=?,department=?,year_level=? WHERE id=?");
                $stmt->execute([$student['name'],$student['roll_number'],$student['email'] ?: null,$student['phone'] ?: null,$student['course'],$student['department'] ?: null,$student['year_level'] ?: null,$id]);
                $studentId = $id;
            } else {
                $stmt = $pdo->prepare("INSERT INTO students(name,roll_number,email,phone,course,department,year_level) VALUES(?,?,?,?,?,?,?)");
                $stmt->execute([$student['name'],$student['roll_number'],$student['email'] ?: null,$student['phone'] ?: null,$student['course'],$student['department'] ?: null,$student['year_level'] ?: null]);
                $studentId = (int)$pdo->lastInsertId();
            }
            $uploadDir = __DIR__ . '/uploads';
            if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) throw new RuntimeException('Could not create uploads directory.');
            foreach ($uploads as $u) {
                $stored = bin2hex(random_bytes(16)) . '.' . $u['ext'];
                $relative = 'uploads/' . $stored;
                if (!move_uploaded_file($u['tmp'], $uploadDir . '/' . $stored)) throw new RuntimeException('Could not save an uploaded file.');
                $storedPaths[] = $uploadDir . '/' . $stored;
                $stmt = $pdo->prepare("INSERT INTO student_files(student_id,original_name,stored_name,file_path,mime_type,file_size) VALUES(?,?,?,?,?,?)");
                $stmt->execute([$studentId,$u['original'],$stored,$relative,$u['mime'],$u['size']]);
            }
            $pdo->commit();
            flash($id ? 'Student record updated successfully.' : 'Student added successfully.');
            redirect('view_student.php?id=' . $studentId);
        } catch (Throwable $ex) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            foreach ($storedPaths as $path) if (is_file($path)) unlink($path);
            $errors[] = str_contains($ex->getMessage(), 'Duplicate') ? 'That roll number already exists.' : 'Could not save the record. Check the database settings and try again.';
        }
    }
}
$existingFiles = [];
if ($id) { $stmt=$pdo->prepare("SELECT * FROM student_files WHERE student_id=? ORDER BY uploaded_at DESC"); $stmt->execute([$id]); $existingFiles=$stmt->fetchAll(); }
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= $id?'Edit':'Add' ?> Student · CampusVault</title><link rel="stylesheet" href="assets/style.css"></head>
<body><div class="ambient ambient-one"></div><div class="ambient ambient-two"></div><div class="app-shell">
<aside class="sidebar glass"><a class="brand" href="index.php"><span class="brand-mark">C</span><span>Campus<span class="brand-light">Vault</span><small>STUDENT ADMIN</small></span></a><div class="nav-label">WORKSPACE</div><a class="nav-item" href="index.php"><span>▦</span> Overview</a><a class="nav-item active" href="student_form.php"><span>＋</span> Add student</a><div class="sidebar-bottom"><div class="secure-icon">⌑</div><strong>Private workspace</strong><p>Only approved file types are accepted. Each upload is assigned a unique stored filename.</p><span class="status"><i></i> Upload protection on</span></div></aside>
<main class="main-content"><header class="topbar"><div><div class="eyebrow"><a href="index.php">STUDENT RECORDS</a> / <?= $id?'EDIT PROFILE':'NEW PROFILE' ?></div><h1><?= $id?'Edit student':'Add a student' ?></h1><p class="muted">Enter profile information and attach supporting documents.</p></div><a class="button button-secondary" href="index.php">← Back to records</a></header>
<?php if ($errors): ?><div class="alert error"><strong>Please check the following:</strong><ul><?php foreach($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" enctype="multipart/form-data" class="form-layout"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<section class="panel glass form-panel"><div class="panel-heading"><div><h2>Profile information</h2><p class="muted">Fields marked with * are required.</p></div><span class="step-badge">01 / PROFILE</span></div>
<div class="form-grid"><label class="field full"><span>Full name <b>*</b></span><input name="name" required maxlength="120" value="<?= e($student['name']) ?>" placeholder="e.g. Ananya Sharma"></label>
<label class="field"><span>Roll number <b>*</b></span><input name="roll_number" required maxlength="40" value="<?= e($student['roll_number']) ?>" placeholder="e.g. CSE2026001"></label>
<label class="field"><span>Course <b>*</b></span><input name="course" required maxlength="120" value="<?= e($student['course']) ?>" placeholder="e.g. B.Tech Computer Science"></label>
<label class="field"><span>Email address</span><input type="email" name="email" value="<?= e($student['email']) ?>" placeholder="student@college.edu"></label>
<label class="field"><span>Phone number</span><input name="phone" maxlength="20" value="<?= e($student['phone']) ?>" placeholder="Enter phone number"></label>
<label class="field"><span>Department</span><input name="department" maxlength="120" value="<?= e($student['department']) ?>" placeholder="e.g. Computer Science"></label>
<label class="field"><span>Year of study</span><select name="year_level"><option value="">Select year</option><?php for($y=1;$y<=8;$y++): ?><option value="<?= $y ?>" <?= (string)$student['year_level']===(string)$y?'selected':'' ?>>Year <?= $y ?></option><?php endfor; ?></select></label></div></section>
<section class="panel glass form-panel"><div class="panel-heading"><div><h2>Profile images & documents</h2><p class="muted">Upload an image, ID proof or certificate.</p></div><span class="step-badge">02 / FILES</span></div>
<label class="upload-zone" for="documents"><span class="upload-symbol">↑</span><strong>Choose files to upload</strong><span>JPG, PNG, WEBP or PDF · Maximum 5 MB per file</span><em id="file-names">You can select multiple files</em><input id="documents" type="file" name="documents[]" accept=".jpg,.jpeg,.png,.webp,.pdf" multiple></label>
<?php if ($existingFiles): ?><div class="existing-files"><h3>Already uploaded</h3><?php foreach($existingFiles as $f): ?><div class="file-row"><span class="file-type"><?= $f['mime_type']==='application/pdf'?'PDF':'IMG' ?></span><div><strong><?= e($f['original_name']) ?></strong><small><?= number_format($f['file_size']/1024,1) ?> KB · <?= e(date('d M Y',strtotime($f['uploaded_at']))) ?></small></div><a href="download.php?id=<?= (int)$f['id'] ?>">View ↗</a></div><?php endforeach; ?></div><?php endif; ?>
<div class="form-actions"><a class="button button-secondary" href="index.php">Cancel</a><button class="button button-primary" type="submit"><?= $id?'Save changes':'Create student profile' ?> <span>→</span></button></div></section></form>
<footer class="footer">CampusVault <span>•</span> Student information management</footer></main></div><script src="assets/app.js"></script></body></html>
