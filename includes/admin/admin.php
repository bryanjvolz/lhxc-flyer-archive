<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once FLYER_GALLERY_PLUGIN_DIR . 'includes/admin/class-flyer-gallery-list-table.php';

// Add admin menu
add_action('admin_menu', 'flyer_gallery_admin_menu');

function flyer_gallery_admin_menu() {
    add_menu_page(
        __('Flyer Gallery', 'flyer-gallery'),
        __('Flyer Gallery', 'flyer-gallery'),
        'manage_options',
        'flyer-gallery',
        'flyer_gallery_admin_page',
        'dashicons-format-gallery',
        30
    );

    add_submenu_page(
        'flyer-gallery',
        __('All Flyers', 'flyer-gallery'),
        __('All Flyers', 'flyer-gallery'),
        'manage_options',
        'flyer-gallery-list',
        'flyer_gallery_list_page'
    );
}

function flyer_gallery_admin_page() {
    // We'll add the admin page content later
    include FLYER_GALLERY_PLUGIN_DIR . 'includes/admin/views/main-page.php';
}

function flyer_gallery_list_page() {
    $list_table = new Flyer_Gallery_List_Table();
    $list_table->prepare_items();
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline"><?php _e('Flyer Gallery Images', 'flyer-gallery'); ?></h1>
        <form method="post">
            <?php $list_table->display(); ?>
        </form>
    </div>
    <?php
}

// Add Media Library custom fields
add_filter('attachment_fields_to_edit', 'flyer_gallery_attachment_fields', 10, 2);

function flyer_gallery_attachment_fields($form_fields, $post) {
  // Include in gallery checkbox
  $form_fields['flyer_gallery_include'] = array(
      'label' => __('Include in Flyer Gallery', 'flyer-gallery'),
      'input' => 'html',
      'html' => '<input type="checkbox" name="attachments[' . $post->ID . '][flyer_gallery_include]" value="1" ' .
                checked(get_post_meta($post->ID, '_flyer_gallery_include', true), 1, false) . '/>',
      'helps' => __('Check to include this image in the Flyer Gallery', 'flyer-gallery')
  );

  // Event date
  $event_date = get_post_meta($post->ID, '_flyer_gallery_event_date', true);
  $form_fields['flyer_gallery_event_date'] = array(
      'label' => __('Event Date', 'flyer-gallery'),
      'input' => 'text',
      'value' => $event_date,
      'helps' => __('Enter the event date (YYYY-MM-DD)', 'flyer-gallery')
  );

  // Venue
  $venue = get_post_meta($post->ID, '_flyer_gallery_venue', true);
  $venues = get_option('flyer_gallery_venue', array());
  $venue_html = '<input type="text" class="flyer-gallery-venue" list="flyer-gallery-venue" name="attachments[' . $post->ID . '][flyer_gallery_venue]" value="' . esc_attr($venue) . '">';
  $venue_html .= '<datalist id="flyer-gallery-venue">';
  foreach ($venues as $saved_venue) {
      $venue_html .= '<option value="' . esc_attr($saved_venue) . '">';
  }
  $venue_html .= '</datalist>';

  // $form_fields['flyer_gallery_venue'] = array(
  //     'label' => __('Venue', 'flyer-gallery'),
  //     'input' => 'html',
  //     'html' => $venue_html,
  //     'helps' => __('Enter or select the venue name', 'flyer-gallery')
  // );

  // Get all unique venues
  global $wpdb;
  $venues = $wpdb->get_col($wpdb->prepare(
      "SELECT DISTINCT meta_value FROM $wpdb->postmeta
      WHERE meta_key = %s AND meta_value != ''
      ORDER BY meta_value ASC",
      '_flyer_gallery_venue'
  ));

  // Get current venue
  $current_venue = get_post_meta($post->ID, '_flyer_gallery_venue', true);

  // Create venue options HTML
  $venue_options = '<option value="">Select Venue</option>';
  foreach ($venues as $venue) {
      $venue_options .= sprintf(
          '<option value="%s" %s>%s</option>',
          esc_attr($venue),
          selected($venue, $current_venue, false),
          esc_html($venue)
      );
  }

  // Add custom input field for new venue
  $venue_field = sprintf(
      '<select name="attachments[%d][flyer_gallery_venue]" id="attachments-%d-flyer_gallery_venue" class="venue-select">%s</select>
      <input type="text" class="new-venue-input" placeholder="Or enter new venue" style="display:none; margin-top:5px;">
      <button type="button" class="button toggle-new-venue" style="margin-top:5px;">Add New Venue</button>',
      $post->ID,
      $post->ID,
      $venue_options
  );

  $form_fields['flyer_gallery_venue'] = array(
      'label' => __('Venue', 'flyer-gallery'),
      'input' => 'html',
      'html'  => $venue_field,
      'helps' => __('Select an existing venue or add a new one', 'flyer-gallery')
  );

  // Artists
  $form_fields['flyer_gallery_artists'] = array(
      'label' => __('Artist(s)', 'flyer-gallery'),
      'input' => 'text',
      'value' => get_post_meta($post->ID, '_flyer_gallery_artists', true),
      'helps' => __('Enter artist names (comma separated)', 'flyer-gallery')
  );

  // Performers/Bands
  // $performers = get_post_meta($post->ID, '_flyer_gallery_performers', true);
  // $form_fields['flyer_gallery_performers'] = array(
  //     'label' => __('Performers/Bands', 'flyer-gallery'),
  //     'input' => 'textarea',
  //     'value' => $performers,
  //     'helps' => __('Enter performers or bands (one per line)', 'flyer-gallery')
  // );

  // return $form_fields;
  // Replace the single performers field with a tag-like field
  $performers = get_post_meta($post->ID, '_flyer_gallery_performers', true);
  $performers_array = !empty($performers) ? array_map('trim', explode(',', $performers)) : array();
  $performers_display = implode("\n", $performers_array);

  $form_fields['flyer_gallery_performers'] = array(
      'label' => __('Performers/Bands', 'flyer-gallery'),
      'input' => 'textarea',
      'value' => $performers_display,
      'helps' => __('Enter each performer on a new line', 'flyer-gallery')
  );

  return $form_fields;
}

// Save attachment fields
add_filter('attachment_fields_to_save', 'flyer_gallery_save_attachment_fields', 10, 2);

function flyer_gallery_save_attachment_fields($post, $attachment) {
    if (isset($attachment['flyer_gallery_include'])) {
        update_post_meta($post['ID'], '_flyer_gallery_include', 1);
    } else {
        delete_post_meta($post['ID'], '_flyer_gallery_include');
    }

  // Handle venue saving from either select or text input
  if (isset($attachment['flyer_gallery_venue'])) {
      $venue = sanitize_text_field($attachment['flyer_gallery_venue']);
      update_post_meta($post['ID'], '_flyer_gallery_venue', $venue);

      // Add to venues list if it's a new venue
      $venues = get_option('flyer_gallery_venue', array());
      if (!in_array($venue, $venues) && !empty($venue)) {
          $venues[] = $venue;
          sort($venues);
          update_option('flyer_gallery_venue', array_unique($venues));
          Flyer_Gallery_Logger::log('Venue saved: ' . $venue, 'debug');
      } else {
        Flyer_Gallery_Logger::log('Error saving venue: ' . $error_message, 'error');
      }
  }

  // Save other fields
  $fields = array(
      'flyer_gallery_event_date',
      'flyer_gallery_artists',
      'flyer_gallery_performers'
  );

  foreach ($fields as $field) {
      if (isset($attachment[$field])) {
          update_post_meta($post['ID'], '_' . $field, sanitize_text_field($attachment[$field]));
      }
  }

    if (isset($attachment['flyer_gallery_performers'])) {
        $performers = $attachment['flyer_gallery_performers'];
        // Convert line breaks to commas and clean up
        $performers_array = array_map('trim', explode("\n", $performers));
        $performers_array = array_filter($performers_array); // Remove empty lines
        $performers_string = implode(',', $performers_array);
        update_post_meta($post['ID'], '_flyer_gallery_performers', $performers_string);
    }

    return $post;
}

// Add custom styles to admin
add_action('admin_enqueue_scripts', 'flyer_gallery_admin_styles');

function flyer_gallery_admin_styles() {
  // wp_enqueue_style('flyer-gallery-admin', FLYER_GALLERY_PLUGIN_URL . 'assets/css/admin.css', array(), FLYER_GALLERY_VERSION);
  wp_enqueue_script('flyer-gallery-admin', FLYER_GALLERY_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), FLYER_GALLERY_VERSION, true);
}
