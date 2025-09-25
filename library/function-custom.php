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
