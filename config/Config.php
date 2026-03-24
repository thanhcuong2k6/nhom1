<?php
/**
 * FashionHub - Hệ thống quản lý cấu hình
 */

class Config {
    private static $config = [];

    public static function load() {
        $envFile = __DIR__ . '/../.env';
        
        if (!file_exists($envFile)) {
            die('File .env không tồn tại!');
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Bỏ qua comment
            if (strpos(trim($line), '#') === 0) continue;
            
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                self::$config[trim($key)] = trim($value);
            }
        }
    }

    public static function get($key, $default = null) {
        return isset(self::$config[$key]) ? self::$config[$key] : $default;
    }
}

Config::load();
?>
