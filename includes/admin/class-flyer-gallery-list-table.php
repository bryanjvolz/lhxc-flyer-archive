<?php
if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('WP_List_Table')) {
  require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Flyer_Gallery_List_Table extends WP_List_Table
{
  public function __construct()
  {
    parent::__construct([
      'singular' => 'flyer',
      'plural'   => 'flyers',
      'ajax'     => false
    ]);
  }

  public function prepare_items()
  {
    // Set up columns
    $columns = $this->get_columns();
    $hidden = array();
    $sortable = $this->get_sortable_columns();
    $this->_column_headers = array($columns, $hidden, $sortable);

    // Handle sorting
    $orderby = isset($_REQUEST['orderby']) ? sanitize_text_field($_REQUEST['orderby']) : 'date';
    $order = isset($_REQUEST['order']) ? sanitize_text_field($_REQUEST['order']) : 'DESC';

    // Handle filtering
    $venue = isset($_REQUEST['venue']) ? sanitize_text_field($_REQUEST['venue']) : '';
    $event_date = isset($_REQUEST['event_date']) ? sanitize_text_field($_REQUEST['event_date']) : '';

    $per_page = 20;
    $current_page = $this->get_pagenum();

    $meta_query = array(
      'relation' => 'AND',
      array(
        'key' => '_flyer_gallery_include',
        'value' => '1'
      )
    );

    if (!empty($venue)) {
      $meta_query[] = array(
        'key' => '_flyer_gallery_venue',
        'value' => $venue,
        'compare' => 'LIKE'
      );
    }

    if (!empty($event_date)) {
      $meta_query[] = array(
        'key' => '_flyer_gallery_event_date',
        'value' => $event_date,
        'compare' => 'LIKE'
      );
    }

    // Add performer filter
    if (!empty($_REQUEST['performer'])) {
        $performer = sanitize_text_field($_REQUEST['performer']);
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
      'paged' => $current_page,
      'meta_query' => $meta_query,
      'orderby' => $orderby,
      'order' => $order
    );

    $query = new WP_Query($args);
    $this->items = $query->posts;

    $this->set_pagination_args([
      'total_items' => $query->found_posts,
      'per_page' => $per_page,
      'total_pages' => ceil($query->found_posts / $per_page)
    ]);
  }

  public function get_sortable_columns()
  {
    return array(
      'title' => array('title', true),
      'event_date' => array('event_date', false),
      'venue' => array('venue', false),
      'date_added' => array('date', true)
    );
  }

  public function extra_tablenav($which)
  {
    if ($which !== 'top') return;

    $venue = isset($_REQUEST['venue']) ? sanitize_text_field($_REQUEST['venue']) : '';
    $event_date = isset($_REQUEST['event_date']) ? sanitize_text_field($_REQUEST['event_date']) : '';
    $performer = isset($_REQUEST['performer']) ? sanitize_text_field($_REQUEST['performer']) : '';
?>
    <div class="alignleft actions">
      <input type="text" name="venue" value="<?php echo esc_attr($venue); ?>" placeholder="<?php esc_attr_e('Filter by venue', 'flyer-gallery'); ?>">
      <input type="date" name="event_date" value="<?php echo esc_attr($event_date); ?>">
      <input type="text" name="performer" value="<?php echo esc_attr($performer); ?>" placeholder="<?php esc_attr_e('Filter by performer', 'flyer-gallery'); ?>">
      <?php submit_button(__('Filter', 'flyer-gallery'), '', 'filter_action', false); ?>
    </div>
<?php
  }

  public function column_title($item)
  {
    $edit_link = get_edit_post_link($item->ID);
    $actions = array(
      'edit' => sprintf(
        '<a href="%s">%s</a>',
        esc_url(get_edit_post_link($item->ID)),
        __('Edit', 'flyer-gallery')
      ),
      'view' => sprintf(
        '<a href="%s">%s</a>',
        esc_url(wp_get_attachment_url($item->ID)),
        __('View', 'flyer-gallery')
      )
    );

    return sprintf(
      '<a href="%1$s"><strong>%2$s</strong></a> %3$s',
      esc_url($edit_link),
      $item->post_title,
      $this->row_actions($actions)
    );
  }

  public function get_columns()
  {
    return [
      'cb'         => '<input type="checkbox" />',
      'thumbnail'  => __('Thumbnail', 'flyer-gallery'),
      'title'      => __('Title', 'flyer-gallery'),
      'event_date' => __('Event Date', 'flyer-gallery'),
      'venue'      => __('Venue', 'flyer-gallery'),
      'performers' => __('Performers', 'flyer-gallery'),
      'date_added' => __('Date Added', 'flyer-gallery')
    ];
  }

  public function column_default($item, $column_name)
  {
    switch ($column_name) {
      case 'thumbnail':
        return wp_get_attachment_image($item->ID, [100, 100]);
      case 'title':
        $actions = array(
          'edit' => sprintf(
            '<a href="%s">%s</a>',
            esc_url(get_edit_post_link($item->ID)),
            __('Edit', 'flyer-gallery')
          )
        );
        return sprintf(
          '%1$s %2$s',
          esc_html($item->post_title),
          $this->row_actions($actions)
        );
      case 'event_date':
        return esc_html(get_post_meta($item->ID, '_flyer_gallery_event_date', true));
      case 'venue':
        return esc_html(get_post_meta($item->ID, '_flyer_gallery_venue', true));
      case 'performers':
        return esc_html(get_post_meta($item->ID, '_flyer_gallery_performers', true));
      case 'date_added':
        return date_i18n(get_option('date_format'), strtotime($item->post_date));
      default:
        return print_r($item, true);
    }
  }
}
