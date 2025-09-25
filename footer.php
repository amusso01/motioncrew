<?php

/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @author Andrea Musso
 *
 * @package foundry
 */

?>

</div><!-- #content -->

<footer class="site-footer content-block">
	<div class="site-footer__inner content-max">

		<div class="site-footer__top">
			<div class="site-footer__top-inner">
				<!-- <?php get_template_part('svg-template/svg-camera-plus'); ?> -->
				<div class="site-footer__top-info">
					<div class="site-footer__top-info-title">
						<h6 class="font-monospace"><?= get_field('site_title', 'options') ?></h6>
					</div>
					<div class="site-footer__top-info-text">
						<a class="font-monospace" href="mailto:<?= get_field('contact_email', 'options') ?>"><?= get_field('contact_email', 'options') ?></a>
						<a class="font-monospace" href="tel:<?= get_field('contact_phone', 'options') ?>"><?= get_field('contact_phone', 'options') ?></a>
					</div>
				</div>
				<div class="site-footer__top-frame">
					<div class="cover cover-top-left"></div>
					<div class="cover cover-bottom-left"></div>
					<div class="cover cover-top-right"></div>
					<div class="cover cover-bottom-right"></div>
					<div class="footer-grid">
						<div class="footer-menu-main">
							<?php get_template_part('components/navigation/footer-nav'); ?>
						</div>
						<div class="footer-social">
							<?php get_template_part('components/navigation/social'); ?>
						</div>
					</div>
				</div>

			</div>
		</div>
		<div class="site-footer__bottom">
			<div class="site-footer__bottom-inner">

				<?php get_template_part('components/navigation/legal'); ?>
			</div>
		</div>

	</div>
</footer><!-- #colophon -->

</div><!-- #page -->

<?php wp_footer(); ?>

</body>

</html>