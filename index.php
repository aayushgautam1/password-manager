<?php
session_start();
require_once 'config/database.php';
require_once 'classes/User.php';
$user = new User($pdo);
$message = '';
if ($_POST['register']) {
    if ($user->register($_POST['username'], $_POST['password']))
        $message = "Registration OK. You can now login.";
    else $message = "Username already exists.";
}
if ($_POST['login']) {
    $result = $user->login($_POST['username'], $_POST['password']);
    if ($result) {
        $_SESSION['user_id'] = $result['id'];
        $_SESSION['master_key'] = base64_encode($result['masterKey']);
        header('Location: dashboard.php'); exit;
    } else $message = "Invalid username or password.";
}
?>
<!DOCTYPE html>
<html><head><title>Password Manager</title><style>
body{font-family:Arial;margin:40px}.box{width:300px;border:1px solid #ccc;padding:15px;margin-bottom:20px}input,button{width:100%;padding:8px;margin:5px 0}
</style></head><body><h1>Password Manager</h1><p style="color:red"><?= $message ?></p>
<div class="box"><h3>Register</h3><form method="post"><input type="text" name="username" placeholder="Username" required><input type="password" name="password" placeholder="Password" required><button type="submit" name="register">Register</button></form></div>
<div class="box"><h3>Login</h3><form method="post"><input type="text" name="username" placeholder="Username" required><input type="password" name="password" placeholder="Password" required><button type="submit" name="login">Login</button></form></div>
</body></html>