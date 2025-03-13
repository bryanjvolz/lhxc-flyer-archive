<?php
/**
 * Plugin Name: Flyer Gallery
 * Plugin URI: https://www.louisvillehardcore.com/
 * Description: A gallery plugin for displaying posters and flyers with advanced filtering and metadata
 * Version: 1.0.0
 * Author: Bryan Volz
 * Author URI: https://www.louisvillehardcore.com/
 * Text Domain: flyer-gallery
 * License: GPL v2 or later
 */

if (!defined('ABSPATH')) {
    exit;
}

define('FLYER_GALLERY_VERSION', '1.0.0');
define('FLYER_GALLERY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FLYER_GALLERY_PLUGIN_URL', plugin_dir_url(__FILE__));

// Activation Hook
register_activation_hook(__FILE__, 'flyer_gallery_activate');

function flyer_gallery_activate() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . 'flyer_gallery_meta';

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        attachment_id bigint(20) NOT NULL,
        event_date date,
        venue varchar(255),
        artists text,
        performers text,
        include_in_gallery tinyint(1) DEFAULT 0,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY attachment_id (attachment_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    add_option('flyer_gallery_db_version', FLYER_GALLERY_VERSION);
    add_option('flyer_gallery_title', 'Flyer Gallery');
}

// Initialize Plugin
add_action('plugins_loaded', 'flyer_gallery_init');

function flyer_gallery_init() {
    // Load text domain
    load_plugin_textdomain('flyer-gallery', false, dirname(plugin_basename(__FILE__)) . '/languages');

    // Include required files
    require_once FLYER_GALLERY_PLUGIN_DIR . 'includes/admin/admin.php';
    require_once FLYER_GALLERY_PLUGIN_DIR . 'includes/frontend/shortcode.php';
    require_once FLYER_GALLERY_PLUGIN_DIR . 'includes/frontend/block.php';
    // Add this line to include the AJAX handler
    require_once FLYER_GALLERY_PLUGIN_DIR . 'includes/frontend/ajax.php';

    // Include logger
    require_once FLYER_GALLERY_PLUGIN_DIR . 'includes/class-flyer-gallery-logger.php';

    // Initialize logger
    add_action('plugins_loaded', array('Flyer_Gallery_Logger', 'init'));
}

// Deactivation Hook
register_deactivation_hook(__FILE__, 'flyer_gallery_deactivate');

// deactivate plugin
function flyer_gallery_deactivate() {
  if (!get_option('flyer_gallery_keep_data', false)) {
      global $wpdb;
      $table_name = $wpdb->prefix . 'flyer_gallery_meta';
      $wpdb->query("DROP TABLE IF EXISTS $table_name");

      // Clean up options
      delete_option('flyer_gallery_db_version');
      delete_option('flyer_gallery_title');
      delete_option('flyer_gallery_venue');
      delete_option('flyer_gallery_keep_data');

      // Clean up post meta
      $wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '_flyer_gallery_%'");
  }
}