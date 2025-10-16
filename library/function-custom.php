<?php

/**
 * Add your own custom functions here
 * For example, your shortcodes...
 *
 */


/*==================================================================================
 SHORTCODES
==================================================================================*/


/* Copyright
/––––––––––––––––––––––––*/
function copyright()
{
  return '&copy; ' . date('Y') . ' <span class="copyright-site-name">' . get_bloginfo('name') . '</span>.';
}
add_shortcode('copyright', 'copyright');

/* Menu Numbering
/––––––––––––––––––––––––*/
function add_menu_numbers($items, $args)
{
  // Only apply to main menu
  if ($args->theme_location == 'mainmenu') {
    $item_count = 0;
    $items = preg_replace_callback(
      '/<li([^>]*class="[^"]*menu-item[^"]*"[^>]*)>(<a[^>]*>.*?<\/a>)/s',
      function ($matches) use (&$item_count) {
        $item_count++;
        $formatted_number = sprintf('/%02d', $item_count);
        return '<li' . $matches[1] . '><span class="menu-number">' . $formatted_number . '</span>' . $matches[2] . '</li>';
      },
      $items
    );
  }
  return $items;
}
add_filter('wp_nav_menu_items', 'add_menu_numbers', 10, 2);

/* Hide WordPress Admin Bar
/––––––––––––––––––––––––*/
function hide_admin_bar_for_non_admins()
{
  if (!current_user_can('administrator')) {
    show_admin_bar(false);
  }
}
add_action('after_setup_theme', 'hide_admin_bar_for_non_admins');


/**
 * Add custom class and data attribute to a specific menu item link
 */
function my_custom_menu_link_attributes($atts, $item, $args)
{
  // Replace 'primary' with your menu slug (theme_location)
  // Replace 123 with the menu item ID you want to target
  if ($args->theme_location === 'mainmenu') {
    // Add your custom data attribute
    $atts['data-text-hover'] = $item->title;
  }

  return $atts;
}
add_filter('nav_menu_link_attributes', 'my_custom_menu_link_attributes', 10, 3);


/*==================================================================================
  SOCIAL MENU SVG ICONS
==================================================================================*/

/**
 * SETUP INSTRUCTIONS:
 * 
 * 1. In ACF, create a new Field Group
 * 2. Add a File field with:
 *    - Field Label: "Icon"
 *    - Field Name: "icon"
 *    - Return Format: "File URL" or "File Array"
 *    - Mime Types: "svg"
 * 3. Set Location Rules to:
 *    - Menu Item equals All (or specific menu)
 * 4. Upload SVG files to menu items via Appearance > Menus
 */

/**
 * Replace social menu item titles with SVG icons from ACF field
 * 
 * This filter replaces menu item titles with SVG content from ACF file field
 * for the social menu only, keeping other menus unchanged.
 *
 * @param array $items The menu items
 * @param object $args The wp_nav_menu() arguments
 * @return array Modified menu items
 */
function social_menu_svg_icons($items, $args)
{
  // Only apply to social menu
  if ($args->theme_location !== 'socialmenu') {
    return $items;
  }

  foreach ($items as &$item) {
    // Get the SVG file from ACF field
    $svg_file = get_field('icon', $item);

    if ($svg_file) {
      // Handle both URL string and ACF file array
      $file_url = is_array($svg_file) ? $svg_file['url'] : $svg_file;

      // Use existing helper function to get SVG content
      $svg_content = acfFile_toSvg($file_url);

      if ($svg_content) {
        // Store original title for screen readers
        $original_title = $item->title;

        // Replace title with SVG content
        $item->title = $svg_content;

        // Add screen reader friendly attributes to the item
        $item->attr_title = $original_title;
        $item->classes[] = 'social-icon-menu-item';
      }
    }
  }

  return $items;
}
add_filter('wp_nav_menu_objects', 'social_menu_svg_icons', 10, 2);



/**
 * Disable Gutenberg for specific page templates.
 */
add_filter('use_block_editor_for_post_type', 'my_disable_gutenberg_for_template', 10, 2);
function my_disable_gutenberg_for_template($can_edit, $post_type)
{
  // Get the currently assigned template for the post.
  $current_template = get_page_template_slug();

  // Define the template(s) you want to exclude.
  $excluded_templates = array(
    'page-template-block.php', // Replace with your actual template file name(s)
    // Add more template file names here if needed
  );

  // Check if it's a page post type and the template is in the excluded list.
  if ($post_type === 'page' && in_array($current_template, $excluded_templates)) {
    return false; // Disable Gutenberg for this template.
  }

  return $can_edit; // Otherwise, use the default setting.
}


// REDIRECT TO SPECIFIC PAGE IF RESTRICTED BY MEMBERS PLUGIN
/**
 * Redirect users away from Members-restricted content.
 * Change $redirect_to to your target URL (absolute URL recommended).
 */
add_action('template_redirect', function () {
  if (!is_singular()) {
    return;
  }

  $post = get_queried_object();
  if (empty($post) || empty($post->ID)) {
    return;
  }

  // Allowed roles set by Members "Content Permissions" meta box.
  // Meta key used by Members for allowed roles:
  $allowed_roles = get_post_meta($post->ID, '_members_access_role', true);

  // If nothing is set, this post isn't restricted.
  if (empty($allowed_roles)) {
    return;
  }

  $redirect_to = home_url('/login'); // ← change to your target page

  // Authors/editors and roles with 'restrict_content' can always view.
  if (is_user_logged_in()) {
    $user = wp_get_current_user();
    if (
      user_can($user, 'restrict_content') ||
      current_user_can('edit_post', $post->ID)
    ) {
      return;
    }

    // If user has any allowed role, let them in.
    $user_roles = (array) $user->roles;
    if (!empty(array_intersect((array) $allowed_roles, $user_roles))) {
      return;
    }
  }

  // Not logged in or doesn't have one of the allowed roles — redirect.
  wp_safe_redirect($redirect_to, 302);
  exit;
});


/**
 * Hide the 'news' CPT UI.
 * Keeps posts and REST/API intact; just removes admin screens/menus/toolbars.
 */
add_action('admin_menu', 'remove_default_post_type');

function remove_default_post_type()
{
  remove_menu_page('edit.php');
}
add_action('admin_bar_menu', 'remove_default_post_type_menu_bar', 999);

function remove_default_post_type_menu_bar($wp_admin_bar)
{
  $wp_admin_bar->remove_node('new-post');
}
function remove_add_new_post_href_in_admin_bar()
{
?>
  <script type="text/javascript">
    function remove_add_new_post_href_in_admin_bar() {
      var add_new = document.getElementById('wp-admin-bar-new-content');
      if (!add_new) return;
      var add_new_a = add_new.getElementsByTagName('a')[0];
      if (add_new_a) add_new_a.setAttribute('href', '#!');
    }
    remove_add_new_post_href_in_admin_bar();
  </script>
<?php
}
add_action('admin_footer', 'remove_add_new_post_href_in_admin_bar');


function remove_frontend_post_href()
{
  if (is_user_logged_in()) {
    add_action('wp_footer', 'remove_add_new_post_href_in_admin_bar');
  }
}
add_action('init', 'remove_frontend_post_href');

add_action('wp_dashboard_setup', 'remove_draft_widget', 999);

function remove_draft_widget()
{
  remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
}
