<?php

/**
 * Plugin Name: Custom Auth Redirects
 * Description: Redirects default WordPress login, register, and lost password pages to custom front-end pages.
 * Author: Andrea Musso FDRY
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) {
  exit; // Prevent direct access.
}

/**
 * Redirect default auth endpoints to custom pages.
 */
add_action('login_init', function () {
  // Allow WordPress to handle these flows unless you've built custom versions.
  $allowed = ['logout', 'lostpassword', 'retrievepassword', 'rp', 'resetpass', 'postpass'];
  $action  = isset($_REQUEST['action']) ? sanitize_text_field($_REQUEST['action']) : '';

  // If we're not doing one of the allowed actions, send to custom login.
  if (!in_array($action, $allowed, true)) {
    wp_safe_redirect(home_url('/login/')); // login page URL
    exit;
  }
});

/**
 * Update WordPress helper URLs to point to custom pages.
 */
add_filter('login_url', function ($url, $redirect, $force_reauth) {
  $dest = home_url('/login/'); // login page URL
  if (!empty($redirect)) {
    $dest = add_query_arg('redirect_to', rawurlencode($redirect), $dest);
  }
  return $dest;
}, 10, 3);

add_filter('register_url', function ($url) {
  return home_url('/sign-up/'); // ← your custom register page
});


// IF we later want to design a lost password page otherwise let's use default WP
// add_filter('lostpassword_url', function ($url, $redirect) {
//     $dest = home_url('/forgot-password/'); // ← your custom reset page
//     if (!empty($redirect)) {
//         $dest = add_query_arg('redirect_to', rawurlencode($redirect), $dest);
//     }
//     return $dest;
// }, 10, 2);
