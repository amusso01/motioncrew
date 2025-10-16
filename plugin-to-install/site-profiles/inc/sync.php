<?php

/**
 * Profile Synchronization Functions
 * 
 * Handles synchronization between WordPress user metadata and profile custom post type.
 * Ensures user data is always reflected in the corresponding profile post, including
 * taxonomies, meta fields, featured images, and availability data.
 */

if (!defined('ABSPATH')) exit;

/**
 * Safely get a single user meta value as a string
 * 
 * Retrieves user metadata and ensures it returns a string value.
 * Arrays and objects are converted to empty strings to prevent issues.
 * 
 * @param int $user_id The user ID
 * @param string $key The meta key to retrieve
 * @return string The meta value as a string, or empty string if not found/invalid
 */
function site_user_meta_str(int $user_id, string $key): string
{
  $v = get_user_meta($user_id, $key, true);
  // Convert non-scalar values to empty string
  if (is_array($v) || is_object($v)) return '';
  return (string) $v;
}

/**
 * Normalize ACF Taxonomy field values to an array of term IDs
 * 
 * ACF can store taxonomy terms in various formats. This function normalizes all
 * possible formats to a clean array of integer term IDs.
 * 
 * Accepts:
 * - Single ID (int or numeric string): 42
 * - Array of IDs: [42, 84]
 * - Array of term objects: [{term_id: 42}, {term_id: 84}]
 * - Array of arrays with term_id: [['term_id' => 42], ['term_id' => 84]]
 * - Comma-separated string: "42,84"
 * 
 * @param mixed $raw The raw taxonomy field value from ACF
 * @return int[] Array of unique term IDs
 */
function site_normalize_term_ids($raw): array
{
  if (is_null($raw) || $raw === '') return [];

  // Single numeric ID (int or string)
  if (is_numeric($raw)) return [(int) $raw];

  // Comma-separated string "12,34"
  if (is_string($raw) && preg_match('/^\s*\d+(?:\s*,\s*\d+)*\s*$/', $raw)) {
    return array_map('intval', preg_split('/\s*,\s*/', trim($raw)));
  }

  // Arrays: can contain IDs, term objects, or arrays with term_id
  if (is_array($raw)) {
    $ids = [];
    foreach ($raw as $item) {
      if (is_numeric($item)) {
        $ids[] = (int) $item;
      } elseif (is_object($item) && isset($item->term_id)) {
        $ids[] = (int) $item->term_id;
      } elseif (is_array($item) && isset($item['term_id'])) {
        $ids[] = (int) $item['term_id'];
      }
    }
    // Remove duplicates and re-index
    return array_values(array_unique(array_filter($ids)));
  }

  // Unsupported format - return empty array
  return [];
}

/**
 * Build a human-readable profile title from user fields
 * 
 * Creates a profile post title in the format "First Last — Role Title"
 * Falls back to display name or username if name fields are empty.
 * 
 * @param WP_User $u The user object
 * @return string The formatted profile title
 */
function site_profile_title_from_user(WP_User $u): string
{
  // Get user's first and last name
  $first = site_user_meta_str($u->ID, 'first_name');
  $last  = site_user_meta_str($u->ID, 'last_name');

  // Build full name from first and last name
  $name  = trim(implode(' ', array_filter([$first, $last])));

  // Fallback to display name or username if no first/last name
  if ($name === '') {
    $name = $u->display_name ?: $u->user_login;
  }

  // Get role title from taxonomy
  $role_term = get_user_meta($u->ID, 'role_title', true);
  $role_ids  = site_normalize_term_ids($role_term);
  $role_name = '';
  if ($role_ids) {
    $t = get_term(reset($role_ids), 'role_title');
    if ($t && !is_wp_error($t)) $role_name = $t->name;
  }

  // Format: "Name — Role" or just "Name" if no role
  return $role_name ? "$name — $role_name" : $name;
}

/**
 * Get the profile post ID associated with a user
 * 
 * @param int $user_id The user ID
 * @return int The profile post ID, or 0 if none exists
 */
function site_get_profile_post_id($user_id): int
{
  return (int) get_user_meta($user_id, 'profile_post_id', true) ?: 0;
}

/**
 * Synchronize user metadata to their profile post
 * 
 * Creates a new profile post if one doesn't exist, or updates the existing one.
 * Syncs all user metadata including taxonomies, ACF fields, featured image,
 * and availability data to the corresponding profile custom post type.
 * 
 * Only creates/syncs profiles for users with 'subscriber' role (default members).
 * Admins and other roles will have their profiles trashed if they exist.
 * 
 * @param int $user_id The user ID to sync
 * @return void
 */
function site_sync_profile_from_user($user_id): void
{
  $u = get_userdata($user_id);
  if (!$u) return;

  // Check if user has subscriber role (default member)
  $is_subscriber = in_array('subscriber', (array) $u->roles, true);

  // Check for stale profile pointer (post was deleted)
  $pid = site_get_profile_post_id($user_id);
  if ($pid && get_post_status($pid) === false) {
    delete_user_meta($user_id, 'profile_post_id');
    $pid = 0;
  }

  // If user is not a subscriber, trash their profile and return
  if (!$is_subscriber) {
    if ($pid) {
      wp_trash_post($pid);
      delete_user_meta($user_id, 'profile_post_id');
    }
    return;
  }

  // Create new profile post if one doesn't exist
  if (!$pid) {
    // Generate unique slug from user's name or username
    $base_slug = sanitize_title(
      $u->display_name
        ?: trim(site_user_meta_str($u->ID, 'first_name') . ' ' . site_user_meta_str($u->ID, 'last_name'))
        ?: $u->user_login
        ?: 'profile-' . $user_id
    );
    $slug = wp_unique_post_slug($base_slug, 0, 'publish', 'profile', 0);

    // Create the profile post
    $pid = wp_insert_post([
      'post_type'   => 'profile',
      'post_status' => 'publish',
      'post_title'  => site_profile_title_from_user($u),
      'post_name'   => $slug,
      'post_author' => $user_id,
    ]);
    if (is_wp_error($pid)) return;

    // Store bidirectional relationship
    update_user_meta($user_id, 'profile_post_id', $pid);
    update_post_meta($pid, 'user_id', $user_id);
  } else {
    // Update existing profile post title
    wp_update_post(['ID' => $pid, 'post_title' => site_profile_title_from_user($u)]);
  }

  // Sync taxonomies from user meta to profile post (check taxonomy exists first)
  $dept_ids = site_normalize_term_ids(get_user_meta($user_id, 'department', true));
  $role_ids = site_normalize_term_ids(get_user_meta($user_id, 'role_title', true));
  if (taxonomy_exists('department'))  wp_set_object_terms($pid, $dept_ids, 'department', false);
  if (taxonomy_exists('role_title'))  wp_set_object_terms($pid, $role_ids, 'role_title', false);

  // Sync scalar meta fields from user to profile post
  foreach (['availability_status', 'available_from', 'available_until', 'postcode', 'imdb_link', 'keep_private'] as $k) {
    $v = get_user_meta($user_id, $k, true);
    // Normalize keep_private to '1' or '0'
    if ($k === 'keep_private') {
      $v = $v ? '1' : '0';
    }
    // Convert non-scalar values to empty string
    if (is_array($v) || is_object($v)) $v = '';
    update_post_meta($pid, $k, (string) $v);
  }

  // Sync featured image from user's picture field (verify attachment exists)
  $pic_id = (int) get_user_meta($user_id, 'picture', true);
  if ($pic_id && get_post($pic_id)) set_post_thumbnail($pid, $pic_id);

  // Calculate and store next available timestamp (if availability function exists)
  if (function_exists('site_next_available_ts_from_user')) {
    update_post_meta($pid, 'next_available_ts', site_next_available_ts_from_user($user_id));
  }
}

// ========================================================================
// WordPress Action Hooks - Auto-sync profiles when user data changes
// ========================================================================

/**
 * Sync profile when a new user is registered (admin or programmatic)
 */
add_action('user_register', function ($uid) {
  site_normalize_availability_for_user($uid);
  site_sync_profile_from_user($uid);
}, 10);

/**
 * Sync profile when user data is updated via WordPress admin
 */
add_action('profile_update', function ($uid) {
  site_normalize_availability_for_user($uid);
  site_sync_profile_from_user($uid);
}, 10);

/**
 * Sync profile when ACF saves user fields (front-end forms or admin)
 * Triggers after ACF processes user_ prefixed post IDs
 */
add_action('acf/save_post', function ($post_id) {
  if (strpos($post_id, 'user_') === 0) {
    $uid = (int) substr($post_id, 5);
    site_normalize_availability_for_user($uid);
    site_sync_profile_from_user($uid);
  }
}, 20);

/**
 * Trash the profile post when a user is deleted
 */
add_action('delete_user', function ($user_id) {
  $pid = site_get_profile_post_id($user_id);
  if ($pid) wp_trash_post($pid);
}, 10);

/**
 * Handle role changes - sync profile when user role changes
 * This ensures profiles are trashed/created based on role
 */
add_action('set_user_role', function ($user_id, $role, $old_roles) {
  site_normalize_availability_for_user($user_id);
  site_sync_profile_from_user($user_id);
}, 10, 3);

/**
 * Filter profile queries to only show profiles of subscriber users
 * This ensures admins and other roles don't appear in profile listings
 */
add_action('pre_get_posts', function ($query) {
  // Only filter profile post type queries
  if (!is_admin() && $query->is_main_query() && $query->get('post_type') === 'profile') {
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
