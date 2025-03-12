<?php
if (!defined('ABSPATH')) {
    exit;
}

function flyer_gallery_register_block() {
    register_block_type('flyer-gallery/gallery', array(
        'editor_script' => 'flyer-gallery-block',
        'editor_style'  => 'flyer-gallery-block-editor',
        'style'         => 'flyer-gallery-block',
        'render_callback' => 'flyer_gallery_render_block'
    ));
}
add_action('init', 'flyer_gallery_register_block');

function flyer_gallery_block_assets() {
    wp_register_script(
        'flyer-gallery-block',
        FLYER_GALLERY_PLUGIN_URL . 'assets/js/blocks/block.js',
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components'),
        FLYER_GALLERY_VERSION
    );

    wp_register_style(
        'flyer-gallery-block',
        FLYER_GALLERY_PLUGIN_URL . 'assets/css/main.css',
        array(),
        FLYER_GALLERY_VERSION
    );

    wp_register_style(
        'flyer-gallery-block-editor',
        FLYER_GALLERY_PLUGIN_URL . 'assets/css/editor.css',
        array('flyer-gallery-block'),
        FLYER_GALLERY_VERSION
    );
}
add_action('init', 'flyer_gallery_block_assets');

function flyer_gallery_render_block($attributes) {
    $wrapper_attributes = get_block_wrapper_attributes(array(
        'class' => 'flyer-gallery-block-wrapper'
    ));

    ob_start();
    ?>
    <div <?php echo $wrapper_attributes; ?>>
        <div class="flyer-gallery-root"
             data-attributes="<?php echo esc_attr(wp_json_encode($attributes)); ?>"
             data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
             data-nonce="<?php echo wp_create_nonce('flyer_gallery_nonce'); ?>">
        </div>
    </div>
    <?php
    return ob_get_clean();
}