<?php

/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package foundry
 */

get_header();
?>

<main role="main" class="site-main page-main content-block">

	<div class="content-max">
		<div class="page-content">
			<?php
			the_content();
			?>
		</div>
	</div>

</main><!-- #main -->

</main><!-- #main -->


<?php
get_footer();
