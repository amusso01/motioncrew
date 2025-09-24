<?php

/**
 * Primary Nav
 * 
 * @author Andrea Musso
 * 
 * @package Foundry
 */

?>
<div class="main-nav-container flex flex-col ">
    <div x-cloak
        x-show="navOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2">
        <?php
        // Main menu
        if (has_nav_menu('mainmenu')) :
            wp_nav_menu([
                'theme_location'    => 'mainmenu',
                'menu_class'        => '',
                'menu_id'           => 'menu_main',
                'container'         => 'nav',
                'container_class'   => 'site-header__item site-header__menu primary-menu',
                'depth'             => 1
            ]);
        endif;

        // Legal menu
        if (has_nav_menu('legalmenu')) :
            wp_nav_menu([
                'theme_location'    => 'legalmenu',
                'menu_class'        => 'flex justify-between items-center',
                'menu_id'           => 'menu_legal',
                'container'         => 'nav',
                'container_class'   => 'site-header__item site-header__menu legal-menu',
                'depth'             => 1
            ]);
        endif;
        ?>
    </div>
</div>