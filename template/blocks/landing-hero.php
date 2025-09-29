<?php
$tagline = $fields['tagline'];
$hero_text = $fields['hero_text'];
$subtitle = $fields['subtitle'];
$button = $fields['button'];
?>


<section class="block-padding block-landing-hero content-block">

  <div class="content-max">
    <div class="block-landing-hero__wrapper">
      <div class="cover cover-top-left"></div>
      <div class="cover cover-bottom-left"></div>
      <div class="cover cover-top-right"></div>
      <div class="cover cover-bottom-right"></div>

      <?php if ($tagline) : ?>
        <p class="block-landing-hero__tagline"><?php echo $tagline; ?></p>
      <?php endif; ?>

      <?php if ($hero_text) : ?>
        <div class="block-landing-hero__text">
          <?php echo $hero_text; ?>
        </div>
      <?php endif; ?>

      <?php if ($subtitle) : ?>
        <div class="block-landing-hero__subtitle"><?php echo $subtitle; ?></div>
      <?php endif; ?>

      <?php if ($button) : ?>
        <?php get_template_part('components/partials/button', null, ['link' => $button['url'], 'text' => $button['title'], 'color' => 'green']); ?>
      <?php endif; ?>

    </div>
  </div>

</section>