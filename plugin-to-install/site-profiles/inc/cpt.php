<?php
if (!defined('ABSPATH')) exit;

add_action('init', function () {
  // CPT
  register_post_type('profile', [
    'label'         => 'Profiles',
    'public'        => true,
    'has_archive'   => false,
    'rewrite'       => ['slug' => 'profile', 'with_front' => false],
    'show_in_rest'  => true,
    'supports'      => ['title', 'thumbnail', 'custom-fields'],
    'map_meta_cap'  => true,
    'menu_icon'     => 'dashicons-businessperson',
  ]);

  // Taxonomies for faceting
  register_taxonomy('department', 'profile', [
    'label' => 'Departments',
    'public' => true,
    'show_ui' => true,
    'show_in_rest' => true,
    'hierarchical' => false,
  ]);
  register_taxonomy('role_title', 'profile', [
    'label' => 'Roles',
    'public' => true,
    'show_ui' => true,
    'show_in_rest' => true,
    'hierarchical' => false,
  ]);
});

// Flush rewrites on activate (once)
register_activation_hook(__FILE__, function () {
  // FLUSH manually once: visit Settings > Permalinks and Save.
});

/**
 * Customize columns on the Profiles list screen.
 */
add_filter('manage_profile_posts_columns', function ($columns) {
  // Remove any image/thumbnail column.
  foreach (['thumbnail', 'featured_image', 'image'] as $key) {
    unset($columns[$key]);
  }

  // Remove any auto taxonomy columns to control order.
  foreach ($columns as $key => $label) {
    if (strpos($key, 'taxonomy-') === 0) {
      unset($columns[$key]);
    }
  }

  // Build the new column order.
  $new = [];
  if (isset($columns['cb']))    $new['cb'] = $columns['cb'];
  if (isset($columns['title'])) $new['title'] = $columns['title'];

  // Add Department and Role Title.
  $new['department']  = __('Department', 'default');
  $new['role_title']  = __('Role Title', 'default');

  if (isset($columns['date']))  $new['date'] = $columns['date'];

  return $new;
}, 99);

/**
 * Render Department and Role Title column content.
 */
add_action('manage_profile_posts_custom_column', function ($column, $post_id) {
  if ($column === 'department') {
    $terms = wp_get_post_terms($post_id, 'department', ['fields' => 'names']);
    echo $terms && !is_wp_error($terms) ? esc_html(implode(', ', $terms)) : '—';
  }

  if ($column === 'role_title') {
    $terms = wp_get_post_terms($post_id, 'role_title', ['fields' => 'names']);
    echo $terms && !is_wp_error($terms) ? esc_html(implode(', ', $terms)) : '—';
  }
}, 10, 2);

/**
 * Optional: style column widths.
 */
add_action('admin_head-edit.php', function () {
  if (!isset($_GET['post_type']) || $_GET['post_type'] !== 'profile') return;
  echo '<style>
      .column-department { width: 18%; }
      .column-role_title { width: 14%; }
  </style>';
});


// Remove "Add New" button for Profiles
add_action('admin_menu', function () {
  global $submenu;
  if (isset($submenu['edit.php?post_type=profile'])) {
    foreach ($submenu['edit.php?post_type=profile'] as $i => $item) {
      if (in_array('post-new.php?post_type=profile', $item, true)) {
        unset($submenu['edit.php?post_type=profile'][$i]);
      }
    }
  }
});

// Remove row actions (Edit, Quick Edit, Trash) in the list table
add_filter('post_row_actions', function ($actions, $post) {
  if ($post->post_type === 'profile') {
    unset($actions['edit'], $actions['inline hide-if-no-js'], $actions['trash'], $actions['duplicate']);
    // keep 'view' only
  }
  return $actions;
}, 10, 2);

// Disable Quick Edit & Bulk Edit
add_filter('bulk_actions-edit-profile', function ($actions) {
  unset($actions['edit'], $actions['trash']);
  return $actions;
});

// Disable editing on Profile || TODO IMPORTANT Uncomment this once LIVE 
// add_action('load-post.php', function () {
//   $screen = get_current_screen();
//   if ($screen && $screen->post_type === 'profile' && $screen->action !== 'add') {
//     // If someone hits wp-admin/post.php?post=ID&action=edit
//     wp_die(__('Profiles are managed automatically and cannot be edited manually.', 'textdomain'));
//   }
// });

// Disable creating new profiles
add_action('load-post-new.php', function () {
  $screen = get_current_screen();
  if ($screen && $screen->post_type === 'profile') {
    wp_die(__('Profiles are managed automatically and cannot be created manually.', 'textdomain'));
  }
});


// 1) Disable editing controls on the profile edit screen (hide Publish box, disable inputs)
add_action('admin_head-post.php', function () {
  $screen = get_current_screen();
  if ($screen && $screen->post_type === 'profile') {
    echo '<style>
      #submitdiv, .edit-post-status, .misc-pub-section, .page-title-action,
      .editor-post-publish-button, .components-panel__body .components-button.is-primary {
        display:none !important;
      }
      /* Disable inputs in classic editor/metabox UIs */
      .wrap input, .wrap select, .wrap textarea, .acf-field input, .acf-field select, .acf-field textarea {
        pointer-events: none; opacity: .9;
      }
      .acf-field .acf-switch, .acf-field .acf-button-group label { pointer-events:none; }
    </style>';
  }
});

// 2) Make ACF fields read-only on profiles
add_filter('acf/load_field', function ($field) {
  if (is_admin() && function_exists('get_current_screen')) {
    $screen = get_current_screen();
    if ($screen && $screen->post_type === 'profile') {
      $field['readonly'] = 1;
      $field['disabled'] = 1;
    }
  }
  return $field;
});

// 3) Filter admin profile listing to show only subscriber profiles
add_action('pre_get_posts', function ($query) {
  if (is_admin() && $query->is_main_query() && isset($_GET['post_type']) && $_GET['post_type'] === 'profile') {
    // Get all subscriber user IDs
    $subscribers = get_users([
      'role'   => 'subscriber',
      'fields' => 'ID',
    ]);

    if (empty($subscribers)) {
      // If no subscribers exist, show no profiles
      $query->set('post__in', [0]);
      return;
    }

    // Filter to only show profiles where author is a subscriber
    $query->set('author__in', $subscribers);
  }
});
