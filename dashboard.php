<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
require_once 'config/database.php';
require_once 'classes/PasswordEntry.php';
require_once 'classes/PasswordGenerator.php';
$masterKey = base64_decode($_SESSION['master_key']);
$entry = new PasswordEntry($pdo, $_SESSION['user_id'], $masterKey);
if ($_POST['save']) { $entry->save($_POST['website'], $_POST['password']); header('Location: dashboard.php'); exit; }
$generated = '';
if ($_POST['generate']) {
    $generated = PasswordGenerator::generate((int)$_POST['length'], (int)$_POST['lower'], (int)$_POST['upper'], (int)$_POST['numbers'], (int)$_POST['specials']);
}
$entries = $entry->getAll();
?>
<!DOCTYPE html>
<html><head><title>Dashboard</title><style>
body{font-family:Arial;margin:30px}.card{border:1px solid #ddd;padding:15px;margin-bottom:20px;background:#fafafa}table{border-collapse:collapse;width:100%}th,td{border:1px solid #ccc;padding:8px}input,button{padding:5px;margin:3px}
</style></head><body>
<h1>Password Manager</h1>
<p><a href="change_password.php">Change login password</a> | <a href="logout.php">Logout</a></p>
<div class="card"><h3>Add new password</h3><form method="post"><input type="text" name="website" placeholder="Website / App name" required><input type="text" name="password" placeholder="Password" value="<?= htmlspecialchars($generated) ?>"><button type="submit" name="save">Save Entry</button></form></div>
<div class="card"><h3>Generate a strong password</h3><form method="post">Length: <input type="number" name="length" value="9" size="3" required><br>Lowercase count: <input type="number" name="lower" value="2"><br>Uppercase count: <input type="number" name="upper" value="3"><br>Numbers count: <input type="number" name="numbers" value="2"><br>Special chars count: <input type="number" name="specials" value="2"><br><button type="submit" name="generate">Generate</button></form><?php if($generated): ?><p><strong>Generated:</strong> <?= $generated ?></p><p><em>Copy into the field above.</em></p><?php endif; ?></div>
<div class="card"><h3>Your saved passwords</h3><?php if($entries): ?><table border="1"><tr><th>Website</th><th>Password</th><th>Created</th></tr><?php foreach($entries as $e): ?><tr><td><?= htmlspecialchars($e['website_name']) ?></td><td><?= htmlspecialchars($e['password']) ?></td><td><?= $e['created_at'] ?></td></tr><?php endforeach; ?></table><?php else: ?><p>No passwords saved yet.</p><?php endif; ?></div>
</body></html>