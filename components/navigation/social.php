<?php

/**
 * Social Menu
 * 
 * @author Andrea Musso
 * 
 * @package Foundry
 */

// Legal menu
if (has_nav_menu('socialmenu')) :
  wp_nav_menu([
    'theme_location'    => 'socialmenu',
    'menu_id'           => 'menu_footer_social',
    'container'         => 'nav',
    'container_class'   => 'site-footer__item site-footer__menu social-menu',
    'depth'             => 1
  ]);
endif;
