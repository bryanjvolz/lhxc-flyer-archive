<?php
if (!defined('ABSPATH')) {
    exit;
}

function flyer_gallery_shortcode($atts) {
    wp_enqueue_script('flyer-gallery-frontend');
    wp_enqueue_style('flyer-gallery-frontend');

    // Add the AJAX URL and nonce to the container
    $data_attributes = array(
        'attributes' => $atts,
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('flyer_gallery_nonce')
    );

    return sprintf(
        '<div class="flyer-gallery-root" data-attributes="%s" data-ajax-url="%s" data-nonce="%s"></div>',
        esc_attr(wp_json_encode($atts)),
        esc_url(admin_url('admin-ajax.php')),
        esc_attr(wp_create_nonce('flyer_gallery_nonce'))
    );
}
add_shortcode('flyer_gallery', 'flyer_gallery_shortcode');

// Register frontend assets
function flyer_gallery_register_frontend_assets() {
    wp_register_script(
        'flyer-gallery-frontend',
        FLYER_GALLERY_PLUGIN_URL . 'assets/js/frontend.js',
        array('react', 'react-dom'),
        FLYER_GALLERY_VERSION,
        true
    );

    wp_register_style(
        'flyer-gallery-frontend',
        FLYER_GALLERY_PLUGIN_URL . 'assets/css/main.css',
        array(),
        FLYER_GALLERY_VERSION
    );
}
add_action('init', 'flyer_gallery_register_frontend_assets');