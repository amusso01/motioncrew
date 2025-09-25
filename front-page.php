<?php

/**
 * The template for displaying frontpage by default
 *
 * @author Andrea Musso
 *
 * @package foundry
 */

get_header();
?>
<?php $container_blocks = get_field('blocks'); ?>

<main class="main homepage-main" role="main">

	<?php if ($container_blocks) : ?>
		<?php foreach ($container_blocks as $block) : ?>

			<?php $file_name = str_replace("block-", "", $block["acf_fc_layout"]); ?>

			<?php if (file_exists(get_template_directory() . '/template/blocks/' . $file_name . '.php')) :

				render_theme_block($file_name, ["id_block" => $block['block-' . $file_name]]);

			endif; ?>

		<?php endforeach; ?>
	<?php endif; ?>



</main>

<?php get_footer(); ?>