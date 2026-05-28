<?php
class PasswordGenerator {
    private static $lower = 'abcdefghijklmnopqrstuvwxyz';
    private static $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private static $numbers = '0123456789';
    private static $specials = '!@#$%^&*()_+{}[]|:;<>,.?~';
    public static function generate($length, $lowerCount, $upperCount, $numberCount, $specialCount) {
        $required = [];
        for ($i = 0; $i < $lowerCount; $i++) $required[] = self::$lower[random_int(0, strlen(self::$lower)-1)];
        for ($i = 0; $i < $upperCount; $i++) $required[] = self::$upper[random_int(0, strlen(self::$upper)-1)];
        for ($i = 0; $i < $numberCount; $i++) $required[] = self::$numbers[random_int(0, strlen(self::$numbers)-1)];
        for ($i = 0; $i < $specialCount; $i++) $required[] = self::$specials[random_int(0, strlen(self::$specials)-1)];
        $all = self::$lower . self::$upper . self::$numbers . self::$specials;
        while (count($required) < $length) $required[] = $all[random_int(0, strlen($all)-1)];
        shuffle($required);
        return implode('', $required);
    }
}