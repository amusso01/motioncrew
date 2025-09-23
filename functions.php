<?php

/**
 * Main Functions File - used for:
 * • including other function-files
 * • WP-Support & WP-Setup
 * • general functions like replacements, translations
 *
 * @author      Andrea Musso
 *
 */

/*==================================================================================
  WP SETUP
==================================================================================*/
// general setup like menu, login font, GTM
require get_template_directory() . '/library/function-require-plugins.php';

/*==================================================================================
  WP SETUP
==================================================================================*/
// general setup like menu, login font, GTM
require get_template_directory() . '/library/function-setup.php';

/*==================================================================================
  DEVELOPER TOOLKIT
==================================================================================*/
// functions used for development purposes like debuggers, return SVG from ACF
require get_template_directory() . '/library/function-dev.php';

/*==================================================================================
  SETTINGS
==================================================================================*/
// general settings, enqueue, theme support, disable backend-theme-editor & more
require get_template_directory() . '/library/function-settings.php';

/*==================================================================================
  ADMIN DISPALY
==================================================================================*/
// Admin Display and rename post
require get_template_directory() . '/library/function-admin_display.php';

/*==================================================================================
  WIDGET AND SIDEBAR
==================================================================================*/
// register widget and sidebar
require get_template_directory() . '/library/function-widget.php';

/*==================================================================================
  ACF
==================================================================================*/
// register widget and sidebar
require get_template_directory() . '/library/function-acf.php';

/*==================================================================================
  CUSTOMIZER
==================================================================================*/
// set up the wordpress customizer for the user
require get_template_directory() . '/library/function-customizer.php';


/*==================================================================================
  GUTENBERG ACF
==================================================================================*/
// bundles all custom Gutenberg-blocks created with ACF
require get_template_directory() . '/library/function-guttenberg.php';


/*==================================================================================
  CUSTOM
==================================================================================*/
// custom functions like shortcodes should be added here
require get_template_directory() . '/library/function-custom.php';
