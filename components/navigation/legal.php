<?php

/**
 * Legal Menu
 * 
 * @author Andrea Musso
 * 
 * @package Foundry
 */

// Legal menu
if (has_nav_menu('legalmenu')) :
  wp_nav_menu([
    'theme_location'    => 'legalmenu',
    'menu_class'        => 'flex justify-between items-center',
    'menu_id'           => 'menu_footer_legal',
    'container'         => 'nav',
    'container_class'   => 'site-footer__item site-footer__menu legal-menu',
    'depth'             => 1
  ]);
endif;
