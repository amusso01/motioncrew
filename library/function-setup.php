<?php

/**
 * The starting point for setting up a new theme.
 * Go through this file to setup your preferences
 *
 * @author      Andrea Musso
 *
 *
 */

/*=======================================================
Table of Contents:
–––––––––––––––––––––––––––––––––––––––––––––––––––––––––
  1.0 LOCALE SETTING
  2.0 DEFAULT BLOCK STYLES
  3.0 GOOGLE TAG MANAGER
  4.0 SETUP WP-MENUS
  5.0 SETUP LOGIN PAGE 
=======================================================*/

/*==================================================================================
  1.0 LOCALE SETTING
==================================================================================*/
// Define local time, date and language-location (PHP-only, does not affect WordPress)
// => http://php.net/manual/function.setlocale.php
setlocale(LC_ALL, 'en_US.UTF-8');

/*==================================================================================
  2.0 DEFAULT GUTENBERG BLOCK STYLES
==================================================================================*/
// Gutenberg comes with default styles for all blocks
// by default these styles are disabled. Change this to `true` to enqueue them
$load_default_block_styles = true;



/*==================================================================================
  3.0 GOOGLE TAG MANAGER
==================================================================================*/
// embed the GTM-scripts into head and body => WPSeed_gtm()
// add your GTM_id (for example 'GTM-ABC1234') or leave empty to not enqueue any GTM-script
$GTM_id = '';


/*==================================================================================
  4.0 SETUP WP-MENUS
==================================================================================*/
// loads wordpress-menus, add your custom menus here or remove if not needed
// by default, only 'mainmenu' is shown
// => https://codex.wordpress.org/Function_Reference/register_nav_menus
function wpseed_register_theme_menus()
{
  register_nav_menus([
    'mainmenu' => __('Main menu'),
    'footermenu' => __('Footer menu'),
    'legalmenu' => __('Legal menu'),
    'socialmenu' => __('Social menu')
  ]);
}
add_action('init', 'wpseed_register_theme_menus');


/*==================================================================================
  5.0 SETUP LOGIN PAGE 
==================================================================================*/

$gFontUrl = "https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:ital,wght@0,300;0,400;0,500;0,700;1,400&display=swap";
$fontFamily = "'IBM Plex Mono', monospace";
$customLogo = get_stylesheet_directory_uri() . "/dist/images/logo.png";
$mainColor = "#1B87E0";



// DISABLE COMMENT
// Disable comments site-wide
add_action('admin_init', function () {
  // Redirect any user trying to access comment-related pages
  global $pagenow;
  if ($pagenow === 'edit-comments.php') {
    wp_redirect(admin_url());
    exit;
  }

  // Remove comments metabox from dashboard
  remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');

  // Disable comments and trackbacks support for all post types
  foreach (get_post_types() as $post_type) {
    if (post_type_supports($post_type, 'comments')) {
      remove_post_type_support($post_type, 'comments');
      remove_post_type_support($post_type, 'trackbacks');
    }
  }
});

// Close comments on the front-end
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);

// Hide existing comments
add_filter('comments_array', '__return_empty_array', 10, 2);

// Remove comments menu from admin
add_action('admin_menu', function () {
  remove_menu_page('edit-comments.php');
});

// Remove comments icon from admin bar
add_action('init', function () {
  if (is_admin_bar_showing()) {
    remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
  }
});
