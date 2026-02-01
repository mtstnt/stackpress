<?php

namespace StackPress\Config;

class Config {
    private array $config = [];
    private static ?Config $instance = null;

    private function __construct() {
        define('ABSPATH', dirname(__DIR__, 2));
        $this->config = [
            'data_path' => ABSPATH . '/data',
            'views_path' => ABSPATH . '/views',
            'build_path' => ABSPATH . '/public/build',
            'admin_path' => ABSPATH . '/public/admin'
        ];
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get(string $key, $default = null) {
        return $this->config[$key] ?? $default;
    }

    public function set(string $key, $value): void {
        $this->config[$key] = $value;
    }

    public function getDataPath(): string {
        return $this->get('data_path');
    }

    public function getViewsPath(): string {
        return $this->get('views_path');
    }

    public function getBuildPath(): string {
        return $this->get('build_path');
    }

    public function getAdminPath(): string {
        return $this->get('admin_path');
    }
}
