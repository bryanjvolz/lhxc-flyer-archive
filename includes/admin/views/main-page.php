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

    <hr>

    <div class="flyer-gallery-stats">
        <h3><?php _e('Gallery Statistics', 'flyer-gallery'); ?></h3>

        <?php
        // Get total flyers
        $flyers_count = get_posts(array(
            'post_type' => 'attachment',
            'meta_key' => '_flyer_gallery_include',
            'meta_value' => '1',
            'posts_per_page' => -1,
            'fields' => 'ids'
        ));

        // Get unique venues
        global $wpdb;
        $venues = $wpdb->get_col($wpdb->prepare(
            "SELECT DISTINCT meta_value FROM $wpdb->postmeta
            WHERE meta_key = %s AND meta_value != ''",
            '_flyer_gallery_venue'
        ));

        // Get unique performers
        $performers_meta = $wpdb->get_col($wpdb->prepare(
            "SELECT meta_value FROM $wpdb->postmeta
            WHERE meta_key = %s AND meta_value != ''",
            '_flyer_gallery_performers'
        ));

        $all_performers = array();
        foreach ($performers_meta as $performers_string) {
            $performers = explode(',', $performers_string);
            $all_performers = array_merge($all_performers, array_map('trim', $performers));
        }
        $unique_performers = array_unique(array_filter($all_performers));
        ?>

        <div class="stats-grid">
            <div class="stat-box">
                <h4><?php _e('Total Flyers', 'flyer-gallery'); ?></h4>
                <span class="stat-number"><?php echo count($flyers_count); ?></span>
            </div>

            <div class="stat-box">
                <h4><?php _e('Unique Venues', 'flyer-gallery'); ?></h4>
                <span class="stat-number"><?php echo count($venues); ?></span>
            </div>

            <div class="stat-box">
                <h4><?php _e('Total Performers', 'flyer-gallery'); ?></h4>
                <span class="stat-number"><?php echo count($unique_performers); ?></span>
            </div>
        </div>
    </div>
</div>



<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.stat-box {
    background: #fff;
    padding: 20px;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    text-align: center;
}

.stat-box h4 {
    margin: 0 0 10px 0;
    color: #23282d;
}

.stat-number {
    font-size: 2em;
    font-weight: bold;
    color: #2271b1;
}
</style>