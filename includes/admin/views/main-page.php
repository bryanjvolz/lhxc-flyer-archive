<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php _e('Flyer Gallery', 'flyer-gallery'); ?></h1>
    
    <div class="flyer-gallery-admin-welcome">
        <h2><?php _e('Welcome to Flyer Gallery', 'flyer-gallery'); ?></h2>
        <p><?php _e('Manage your flyer collection and create beautiful galleries.', 'flyer-gallery'); ?></p>
        
        <div class="flyer-gallery-admin-actions">
            <a href="<?php echo esc_url(admin_url('upload.php')); ?>" class="button button-primary">
                <?php _e('Add New Flyers', 'flyer-gallery'); ?>
            </a>
            
            <a href="<?php echo esc_url(admin_url('admin.php?page=flyer-gallery-list')); ?>" class="button">
                <?php _e('View All Flyers', 'flyer-gallery'); ?>
            </a>
        </div>

        <div class="flyer-gallery-admin-usage">
            <h3><?php _e('Usage', 'flyer-gallery'); ?></h3>
            <p><?php _e('To display the gallery on any page or post:', 'flyer-gallery'); ?></p>
            <ol>
                <li><?php _e('Use the shortcode:', 'flyer-gallery'); ?> <code>[flyer_gallery]</code></li>
                <li><?php _e('Or use the Gutenberg block "Flyer Gallery"', 'flyer-gallery'); ?></li>
            </ol>
        </div>
    </div>
</div>