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
