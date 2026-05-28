<?php
class KeyManager {
    public static function generateKey() {
        return random_bytes(32);
    }
    public static function encryptKey($keyBinary, $userPassword) {
        $salt = random_bytes(16);
        $iv = random_bytes(16);
        $derivedKey = hash_pbkdf2('sha256', $userPassword, $salt, 10000, 32, true);
        $encrypted = openssl_encrypt($keyBinary, 'aes-256-cbc', $derivedKey, OPENSSL_RAW_DATA, $iv);
        return base64_encode($salt . $iv . $encrypted);
    }
    public static function decryptKey($encryptedBase64, $userPassword) {
        $data = base64_decode($encryptedBase64);
        $salt = substr($data, 0, 16);
        $iv = substr($data, 16, 16);
        $ciphertext = substr($data, 32);
        $derivedKey = hash_pbkdf2('sha256', $userPassword, $salt, 10000, 32, true);
        return openssl_decrypt($ciphertext, 'aes-256-cbc', $derivedKey, OPENSSL_RAW_DATA, $iv);
    }
}