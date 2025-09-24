<?php

/**
 * Main Site Header Template
 * 
 * @author   Andrea Musso
 * 
 * @package  Foundry
 * 
 */

?>

<?php
// NAV BTN

// Get the menu object for the main menu location
$menu_locations = get_nav_menu_locations();
$menu_object = null;

if (isset($menu_locations['mainmenu'])) {
	$menu_object = wp_get_nav_menu_object($menu_locations['mainmenu']);
}

// Get ACF fields from the menu object
$navBtn = null;
$navBtnNonLoggedIn = null;

if ($menu_object) {
	$navBtn = get_field('button_link', $menu_object);
	$navBtnNonLoggedIn = get_field('button_link_non_log_in', $menu_object);
}

?>


<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<!--=== GMT head ===-->
	<?php WPSeed_gtm('head') ?>
	<!--=== gmt end ===-->
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<!--=== GMT body ===-->
	<?php WPSeed_gtm('body') ?>
	<!--=== gmt end ===-->

	<div id="page" class="site">
		<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e('Skip to content', 'foundry'); ?></a>
		<header class="site-header" x-data="{ navOpen: false }" @click.outside="navOpen = false">
			<div class="content-max site-header__container">
				<div class="site-header__inner-wrapper fixed z-100">
					<div class="site-header__inner flex justify-between items-center bg-white">
						<?php get_template_part('components/header/logo'); ?>
						<?php get_template_part('components/navigation/primary'); ?>
						<?php get_template_part('components/header/hamburger'); ?>
					</div>
				</div>
				<div class="main-nav-btn">
					<?php if (is_user_logged_in() && $navBtn): ?>
						<?php get_template_part('components/partials/button', null, ['link' => $navBtn['url'], 'text' => $navBtn['title']]); ?>
					<?php elseif (!is_user_logged_in() && $navBtnNonLoggedIn): ?>
						<?php get_template_part('components/partials/button', null, ['link' => $navBtnNonLoggedIn['url'], 'text' => $navBtnNonLoggedIn['title']]); ?>
					<?php endif; ?>
				</div>
			</div>
		</header><!-- .site-header -->


		<div id="content" class="site-content">