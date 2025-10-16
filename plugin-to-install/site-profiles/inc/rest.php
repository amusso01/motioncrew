<?php

/**
 * REST API Endpoints for Site Profiles
 * 
 * Registers custom REST API routes for user registration, profile management,
 * and profile search functionality.
 */

if (!defined('ABSPATH')) exit;

/**
 * Register custom REST API routes
 */
add_action('rest_api_init', function () {
  // User registration endpoint - publicly accessible
  register_rest_route('site/v1', '/register', [
    'methods'  => 'POST',
    'permission_callback' => '__return_true',
    'callback' => 'site_api_register_user',
  ]);

  // GET: Retrieve current user's profile data | POST: Update current user's profile
  register_rest_route('site/v1', '/me', [
    'methods'  => ['GET', 'POST'],
    'permission_callback' => function () {
      return is_user_logged_in();
    },
    'callback' => 'site_api_me',
  ]);

  // Profile search endpoint - publicly accessible
  register_rest_route('site/v1', '/profiles', [
    'methods'  => 'GET',
    'permission_callback' => '__return_true',
    'callback' => 'site_api_profiles_search',
  ]);
});

/**
 * Handle user registration via REST API
 * 
 * Creates a new user account with profile data including ACF fields,
 * taxonomies, work experience, and profile picture.
 * 
 * @param WP_REST_Request $r The REST request object
 * @return WP_REST_Response Response with user_id and profile_url on success
 */
function site_api_register_user(WP_REST_Request $r)
{
  // Accept both JSON and multipart form data
  $is_json = stripos($r->get_header('content-type') ?? '', 'application/json') !== false;
  $p = $is_json ? (array) $r->get_json_params() : $r->get_params();

  // Rate limiting: prevent abuse (simple implementation)
  $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
  $key = 'reg_rl_' . md5(strtolower($p['email'] ?? '') . '|' . $ip);
  $hits = (int) get_transient($key);
  if ($hits > 20) return new WP_REST_Response(['ok' => false, 'error' => 'Too many attempts'], 429);
  set_transient($key, $hits + 1, 10 * MINUTE_IN_SECONDS);

  // Validate email and password
  $email = sanitize_email($p['email'] ?? '');
  $pass  = (string) ($p['password'] ?? '');
  if (!is_email($email)) return new WP_REST_Response(['ok' => false, 'error' => 'Invalid email'], 400);
  if (strlen($pass) < 8)  return new WP_REST_Response(['ok' => false, 'error' => 'Password too short'], 400);
  if (email_exists($email)) return new WP_REST_Response(['ok' => false, 'error' => 'Email already registered'], 409);

  // Validate username
  $username = sanitize_user($p['username'] ?? $email, true);
  if (username_exists($username)) return new WP_REST_Response(['ok' => false, 'error' => 'Username already exists'], 409);

  // Create user account with site's default role
  $default_role = get_option('default_role', 'subscriber');
  $user_id = wp_insert_user([
    'user_login' => $username,
    'user_email' => $email,
    'user_pass'  => $pass,
    'first_name' => sanitize_text_field($p['first_name'] ?? ''),
    'last_name'  => sanitize_text_field($p['last_name'] ?? ''),
    'role'       => $default_role,
  ]);
  if (is_wp_error($user_id)) {
    return new WP_REST_Response(['ok' => false, 'error' => 'Registration failed'], 400);
  }

  // Safety check: ensure the role was actually applied
  $u = new WP_User($user_id);
  if (empty($u->roles)) {
    $u->set_role($default_role);
  }

  // Prepare ACF user target identifier
  $post_id = 'user_' . $user_id;

  // Helper function to normalize boolean values from FormData
  $bool = fn($v) => in_array($v, [true, '1', 1, 'true', 'on'], true) ? '1' : '0';

  // Save simple ACF fields
  $simple = [
    'phone_number',
    'contact_email',
    'imdb_link',
    'postcode',
    'area_region',
    'availability_status',
    'available_from',
    'available_until',
    'opt_out_marketing',
    'keep_private'
  ];
  foreach ($simple as $k) {
    if (array_key_exists($k, $p)) {
      $val = $p[$k];
      if (in_array($k, ['opt_out_marketing', 'keep_private'], true)) {
        $val = $bool($val);
      }
      if (function_exists('update_field')) update_field($k, $val, $post_id);
      else update_user_meta($user_id, $k, $val);
    }
  }

  // Save taxonomy fields: department & role_title (accept term IDs as single value, CSV string, or array)
  $to_ids = function ($raw) {
    if (is_numeric($raw)) return [(int)$raw];
    if (is_string($raw) && strpos($raw, ',') !== false) return array_map('intval', explode(',', $raw));
    if (is_array($raw)) return array_map('intval', $raw);
    return [];
  };
  foreach (['department', 'role_title'] as $taxField) {
    if (array_key_exists($taxField, $p)) {
      $ids = $to_ids($p[$taxField]);
      if (function_exists('update_field')) update_field($taxField, $ids, $post_id);
      else update_user_meta($user_id, $taxField, $ids);
    }
  }

  // Save work experience repeater field (accept as array or JSON string)
  if (isset($p['work_experience'])) {
    $we = $p['work_experience'];
    // Decode JSON string if provided
    if (is_string($we)) {
      $decoded = json_decode(wp_unslash($we), true);
      if (json_last_error() === JSON_ERROR_NONE) $we = $decoded;
      else $we = [];
    }
    // Sanitize and structure work experience rows
    if (is_array($we)) {
      $rows = [];
      foreach ($we as $row) {
        $rows[] = [
          'we_role_title'      => sanitize_text_field($row['we_role_title'] ?? ''),
          'we_production_name' => sanitize_text_field($row['we_production_name'] ?? ''),
          'we_date'            => sanitize_text_field($row['we_date'] ?? ''),
        ];
      }
      if (function_exists('update_field')) update_field('work_experience', $rows, $post_id);
      else update_user_meta($user_id, 'work_experience', $rows);
    }
  }

  // Handle profile picture upload (multipart form data)
  if (!empty($_FILES['picture']['name'])) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
    $attach_id = media_handle_upload('picture', 0);
    if (!is_wp_error($attach_id)) {
      if (function_exists('update_field')) update_field('picture', (int)$attach_id, $post_id);
      else update_user_meta($user_id, 'picture', (int)$attach_id);
    }
  }

  // Normalize availability and sync to profile post
  site_normalize_availability_for_user($user_id);
  site_sync_profile_from_user($user_id);

  // Build success response with profile URL
  $profile_post_id = (int) get_user_meta($user_id, 'profile_post_id', true);
  $profile_url = $profile_post_id ? get_permalink($profile_post_id) : '';

  // Clean any stray output that may have been generated
  if (ob_get_length()) {
    ob_clean();
  }

  return new WP_REST_Response([
    'ok'          => true,
    'user_id'     => $user_id,
    'profile_url' => $profile_url,
  ], 201);
}

/**
 * Handle current user profile retrieval and updates
 * 
 * GET: Returns current user's profile data for form prefilling
 * POST: Updates current user's profile data
 * 
 * @param WP_REST_Request $r The REST request object
 * @return WP_REST_Response Response with user data on GET, success status on POST
 */
function site_api_me(WP_REST_Request $r)
{
  $uid = get_current_user_id();
  if ($r->get_method() === 'GET') {
    // GET: Return profile data for prefilling the front-end form
    $post_id = 'user_' . $uid;

    // Get taxonomy term IDs
    $dept = get_user_meta($uid, 'department', true);
    $role = get_user_meta($uid, 'role_title', true);

    // Build profile data payload
    $data = [
      'first_name' => get_user_meta($uid, 'first_name', true),
      'last_name'  => get_user_meta($uid, 'last_name', true),
      'phone_number' => get_user_meta($uid, 'phone_number', true),
      'contact_email' => get_user_meta($uid, 'contact_email', true),
      'keep_private' => (bool) get_user_meta($uid, 'keep_private', true),
      'opt_out_marketing' => (bool) get_user_meta($uid, 'opt_out_marketing', true),
      'department' => is_array($dept) ? array_map('intval', $dept) : (is_numeric($dept) ? [(int)$dept] : []),
      'role_title' => is_array($role) ? array_map('intval', $role) : (is_numeric($role) ? [(int)$role] : []),
      'imdb_link' => get_user_meta($uid, 'imdb_link', true),
      'postcode'  => get_user_meta($uid, 'postcode', true),
      'area_region' => get_user_meta($uid, 'area_region', true),
      'availability_status' => get_user_meta($uid, 'availability_status', true) ?: 'available',
      'available_from'  => get_user_meta($uid, 'available_from', true),
      'available_until' => get_user_meta($uid, 'available_until', true),
      'work_experience' => get_user_meta($uid, 'work_experience', true) ?: [],
      'picture_id' => (int) get_user_meta($uid, 'picture', true),
      'profile_url' => ($pid = (int) get_user_meta($uid, 'profile_post_id', true)) ? get_permalink($pid) : '',
    ];
    return new WP_REST_Response(['ok' => true, 'data' => $data], 200);
  }

  // POST: Update user profile data
  $p = $r->get_params();
  $post_id = 'user_' . $uid;

  // Helper function to normalize boolean values
  $bool = fn($v) => in_array($v, [true, '1', 1, 'true', 'on'], true) ? '1' : '0';

  // Update simple ACF fields
  $simple = [
    'phone_number',
    'contact_email',
    'imdb_link',
    'postcode',
    'area_region',
    'availability_status',
    'available_from',
    'available_until',
    'opt_out_marketing',
    'keep_private'
  ];
  foreach ($simple as $k) {
    if (array_key_exists($k, $p)) {
      $val = $p[$k];
      if (in_array($k, ['opt_out_marketing', 'keep_private'], true)) {
        $val = $bool($val);
      }
      if (function_exists('update_field')) update_field($k, $val, $post_id);
      else update_user_meta($uid, $k, $val);
    }
  }

  // Update taxonomy fields (accept term IDs as single value, CSV string, or array)
  $to_ids = function ($raw) {
    if (is_numeric($raw)) return [(int)$raw];
    if (is_string($raw) && strpos($raw, ',') !== false) return array_map('intval', explode(',', $raw));
    if (is_array($raw)) return array_map('intval', $raw);
    return [];
  };
  foreach (['department', 'role_title'] as $taxField) {
    if (array_key_exists($taxField, $p)) {
      $ids = $to_ids($p[$taxField]);
      if (function_exists('update_field')) update_field($taxField, $ids, $post_id);
      else update_user_meta($uid, $taxField, $ids);
    }
  }

  // Update work experience repeater field (accept as array or JSON string)
  if (isset($p['work_experience'])) {
    $we = is_string($p['work_experience']) ? json_decode(wp_unslash($p['work_experience']), true) : $p['work_experience'];
    if (!is_array($we)) $we = [];
    $rows = [];
    foreach ($we as $row) {
      $rows[] = [
        'we_role_title'      => sanitize_text_field($row['we_role_title'] ?? ''),
        'we_production_name' => sanitize_text_field($row['we_production_name'] ?? ''),
        'we_date'            => sanitize_text_field($row['we_date'] ?? ''),
      ];
    }
    if (function_exists('update_field')) update_field('work_experience', $rows, $post_id);
    else update_user_meta($uid, 'work_experience', $rows);
  }

  // TODO: Handle profile picture upload (multipart) if needed

  // Normalize availability and sync to profile post
  site_normalize_availability_for_user($uid);
  site_sync_profile_from_user($uid);

  return new WP_REST_Response(['ok' => true], 200);
}

/**
 * Handle profile search requests
 * 
 * Searches for user profiles based on various criteria.
 * Only returns profiles for users with 'subscriber' role (default members).
 * 
 * @param WP_REST_Request $r The REST request object
 * @return WP_REST_Response Search results or error
 */
function site_api_profiles_search(WP_REST_Request $r)
{
  // Get all subscriber user IDs to filter profiles
  $subscribers = get_users([
    'role'   => 'subscriber',
    'fields' => 'ID',
  ]);

  if (empty($subscribers)) {
    return new WP_REST_Response(['ok' => true, 'profiles' => []], 200);
  }

  // Build query args for profile search
  $args = [
    'post_type'      => 'profile',
    'post_status'    => 'publish',
    'posts_per_page' => 50,
    'author__in'     => $subscribers, // Only show subscriber profiles
  ];

  // TODO: Add search/filter parameters when needed

  $query = new WP_Query($args);
  $profiles = [];

  foreach ($query->posts as $post) {
    $profiles[] = [
      'id'    => $post->ID,
      'title' => $post->post_title,
      'url'   => get_permalink($post->ID),
    ];
  }

  return new WP_REST_Response([
    'ok'       => true,
    'profiles' => $profiles,
    'total'    => $query->found_posts,
  ], 200);
}
