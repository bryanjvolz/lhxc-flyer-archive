<?php
if (!defined('ABSPATH')) {
    exit;
}

class Flyer_Gallery_Logger {
    private static $log_file;

    public static function init() {
        self::$log_file = FLYER_GALLERY_PLUGIN_DIR . 'logs/debug.log';

        // Create logs directory if it doesn't exist
        $log_dir = dirname(self::$log_file);
        if (!file_exists($log_dir)) {
            wp_mkdir_p($log_dir);
        }
    }

    public static function log($message, $type = 'info') {
        if (!self::$log_file) {
            self::init();
        }

        $timestamp = current_time('Y-m-d H:i:s');
        $log_entry = sprintf("[%s] %s: %s\n", $timestamp, strtoupper($type), $message);

        error_log($log_entry, 3, self::$log_file);
    }
}