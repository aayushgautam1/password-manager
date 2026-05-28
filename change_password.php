<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
require_once 'config/database.php';
require_once 'classes/User.php';
$user = new User($pdo);
$msg = '';
if ($_POST['change']) {
    $old = $_POST['old_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];
    if ($new !== $confirm) $msg = "New passwords do not match.";
    else if ($user->changePassword($_SESSION['user_id'], $old, $new)) {
        $login = $user->login($_SESSION['username'] ?? '', $new);
        if ($login) $_SESSION['master_key'] = base64_encode($login['masterKey']);
        $msg = "Password changed! Master key re-encrypted (saved passwords intact).";
    } else $msg = "Old password incorrect.";
}
?>
<!DOCTYPE html><html><head><title>Change Password</title></head><body>
<h2>Change login password</h2><p style="color:green"><?= $msg ?></p>
<form method="post"><input type="password" name="old_password" placeholder="Old password" required><br><input type="password" name="new_password" placeholder="New password" required><br><input type="password" name="confirm_password" placeholder="Confirm new password" required><br><button type="submit" name="change">Change password</button></form>
<p><a href="dashboard.php">Back to dashboard</a></p>
</body></html>