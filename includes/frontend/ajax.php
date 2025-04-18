<?php
if (!defined('ABSPATH')) {
    exit;
}

//Retreive one flyer at a time for lightbox/direct linking
function flyer_gallery_get_single_flyer() {
  check_ajax_referer('flyer_gallery_nonce', 'nonce');

  $flyer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

  if (!$flyer_id) {
      wp_send_json_error(['message' => 'Invalid flyer ID']);
      return;
  }

  $flyer = get_post($flyer_id);

  if (!$flyer || $flyer->post_type != 'attachment') {
      wp_send_json_error(['message' => 'Flyer not found']);
      return;
  }

  $response = [
      'image' => [
        'id' => $flyer_id,
        'title' => $flyer->post_title,
        'thumbnail' => wp_get_attachment_image_url($flyer->ID, 'medium'),
        'full' => wp_get_attachment_image_url($flyer->ID, 'full'),
        'event_date' => get_post_meta($flyer->ID, '_flyer_gallery_event_date', true),
        'venue' => get_post_meta($flyer->ID, '_flyer_gallery_venue', true),
        'artists' => get_post_meta($flyer->ID, '_flyer_gallery_artists', true),
        'performers' => get_post_meta($flyer->ID, '_flyer_gallery_performers', true)
      ]
  ];

  wp_send_json_success($response);
}

/**
 * Flyer_gallery_get_images
 *
 * returns
 */
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

    // Add performer filter with exact matching
    if (!empty($performer)) {
        $meta_query[] = array(
            'key' => '_flyer_gallery_performers',
            'value' => '(^|,\s*)' . preg_quote($performer) . '(\s*,|$)',
            'compare' => 'REGEXP'
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

// Register single flyer actions
add_action('wp_ajax_get_flyer', 'flyer_gallery_get_single_flyer');
add_action('wp_ajax_nopriv_get_flyer', 'flyer_gallery_get_single_flyer');
// Register full gallery load actions
add_action('wp_ajax_nopriv_get_flyer_gallery', 'flyer_gallery_get_images');
add_action('wp_ajax_get_flyer_gallery', 'flyer_gallery_get_images');
