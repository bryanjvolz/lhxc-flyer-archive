<?php
if (!defined('ABSPATH')) {
    exit;
}

function flyer_gallery_get_images() {
    // Remove nonce check temporarily for testing
    check_ajax_referer('flyer_gallery_nonce', 'nonce');

    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 12;

    // Get filter values
    $year = isset($_GET['year']) ? sanitize_text_field($_GET['year']) : '';
    $venue = isset($_GET['venue']) ? wp_unslash(html_entity_decode(sanitize_text_field($_GET['venue']), ENT_QUOTES)) : '';
    $performer = isset($_GET['performer']) ? wp_unslash(html_entity_decode(sanitize_text_field($_GET['performer']), ENT_QUOTES)) : '';

    $venue = isset($_GET['venue']) ? wp_unslash(html_entity_decode(sanitize_text_field($_GET['venue']), ENT_QUOTES)) : '';

    // Build meta query
    $meta_query = array(
        'relation' => 'AND',
        array(
            'key' => '_flyer_gallery_include',
            'value' => '1',
            'compare' => '='
        )
    );

    // Add year filter
    if (!empty($year)) {
        $meta_query[] = array(
            'key' => '_flyer_gallery_event_date',
            'value' => $year,
            'compare' => 'LIKE',
            'type' => 'NUMERIC'
        );
    }

    // Add venue filter
    if (!empty($venue)) {
        $meta_query[] = array(
            'key' => '_flyer_gallery_venue',
            'value' => $venue,
            'compare' => '='
        );
    }
    $venue = isset($_GET['venue']) ? wp_unslash(html_entity_decode(sanitize_text_field($_GET['venue']), ENT_QUOTES)) : '';

    // Add performer filter
    if (!empty($performer)) {
        $meta_query[] = array(
            'key' => '_flyer_gallery_performers',
            'value' => $performer,
            'compare' => 'LIKE'
        );
    }

    $args = array(
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'posts_per_page' => $per_page,
        'paged' => $page,
        'meta_query' => $meta_query,
        'orderby' => 'date',
        'order' => 'DESC'
    );

    $query = new WP_Query($args);
    $images = array();

    foreach ($query->posts as $post) {
        $images[] = array(
            'id' => $post->ID,
            'title' => $post->post_title,
            'thumbnail' => wp_get_attachment_image_url($post->ID, 'medium'),
            'full' => wp_get_attachment_image_url($post->ID, 'full'),
            'event_date' => get_post_meta($post->ID, '_flyer_gallery_event_date', true),
            'venue' => get_post_meta($post->ID, '_flyer_gallery_venue', true),
            'artists' => get_post_meta($post->ID, '_flyer_gallery_artists', true),
            'performers' => get_post_meta($post->ID, '_flyer_gallery_performers', true)
        );
    }

    wp_send_json_success(array(
        'images' => $images,
        'pages' => $query->max_num_pages,
        'available_filters' => flyer_gallery_get_available_filters()
    ));
}

// Change the action name to match what's expected in the frontend
add_action('wp_ajax_nopriv_get_flyer_gallery', 'flyer_gallery_get_images');
add_action('wp_ajax_get_flyer_gallery', 'flyer_gallery_get_images');

function flyer_gallery_get_available_filters() {
    global $wpdb;

    return array(
        'years' => $wpdb->get_col($wpdb->prepare(
            "SELECT DISTINCT YEAR(meta_value) FROM $wpdb->postmeta
            WHERE meta_key = %s AND meta_value != ''
            ORDER BY meta_value DESC",
            '_flyer_gallery_event_date'
        )),
        'venues' => $wpdb->get_col($wpdb->prepare(
            "SELECT DISTINCT meta_value FROM $wpdb->postmeta
            WHERE meta_key = %s AND meta_value != ''
            ORDER BY meta_value ASC",
            '_flyer_gallery_venue'
        )),
        'performers' => $wpdb->get_col($wpdb->prepare(
            "SELECT DISTINCT meta_value FROM $wpdb->postmeta
            WHERE meta_key = %s AND meta_value != ''
            ORDER BY meta_value ASC",
            '_flyer_gallery_performers'
        ))
    );
}