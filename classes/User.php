<?php
require_once 'KeyManager.php';
class User {
    private $db;
    public function __construct($pdo) { $this->db = $pdo; }
    public function register($username, $plainPassword) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) return false;
        $masterKey = KeyManager::generateKey();
        $encryptedKey = KeyManager::encryptKey($masterKey, $plainPassword);
        $passwordHash = password_hash($plainPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (username, login_password_hash, encrypted_key) VALUES (?, ?, ?)");
        return $stmt->execute([$username, $passwordHash, $encryptedKey]);
    }
    public function login($username, $plainPassword) {
        $stmt = $this->db->prepare("SELECT id, login_password_hash, encrypted_key FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) return false;
        if (!password_verify($plainPassword, $user['login_password_hash'])) return false;
        $masterKey = KeyManager::decryptKey($user['encrypted_key'], $plainPassword);
        if ($masterKey === false) return false;
        return ['id' => $user['id'], 'masterKey' => $masterKey];
    }
    public function changePassword($userId, $oldPassword, $newPassword) {
        $stmt = $this->db->prepare("SELECT encrypted_key, login_password_hash FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) return false;
        if (!password_verify($oldPassword, $user['login_password_hash'])) return false;
        $masterKey = KeyManager::decryptKey($user['encrypted_key'], $oldPassword);
        if ($masterKey === false) return false;
        $newEncryptedKey = KeyManager::encryptKey($masterKey, $newPassword);
        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET login_password_hash = ?, encrypted_key = ? WHERE id = ?");
        return $stmt->execute([$newHash, $newEncryptedKey, $userId]);
    }
}