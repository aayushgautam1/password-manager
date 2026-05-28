<?php
class PasswordEntry {
    private $db, $userId, $masterKey;
    public function __construct($pdo, $userId, $masterKey) {
        $this->db = $pdo;
        $this->userId = $userId;
        $this->masterKey = $masterKey;
    }
    public function save($website, $plainPassword) {
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($plainPassword, 'aes-256-cbc', $this->masterKey, OPENSSL_RAW_DATA, $iv);
        $combined = base64_encode($iv . $encrypted);
        $stmt = $this->db->prepare("INSERT INTO password_entries (user_id, website_name, encrypted_password) VALUES (?, ?, ?)");
        return $stmt->execute([$this->userId, $website, $combined]);
    }
    public function getAll() {
        $stmt = $this->db->prepare("SELECT id, website_name, encrypted_password, created_at FROM password_entries WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$this->userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as &$row) {
            $data = base64_decode($row['encrypted_password']);
            $iv = substr($data, 0, 16);
            $cipher = substr($data, 16);
            $row['password'] = openssl_decrypt($cipher, 'aes-256-cbc', $this->masterKey, OPENSSL_RAW_DATA, $iv);
        }
        return $rows;
    }
}