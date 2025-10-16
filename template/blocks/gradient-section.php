<?php
$tagline = $fields['tagline'];
$title = $fields['section_title'];
$subtitle = $fields['subtitle'];

// Option
$gradient_color = $fields['gradient_color'];
$color1 = $gradient_color['color_stop_1'];
$color2 = $gradient_color['color_stop_2'];
$text_color = $fields['text_color'];

//Option
$addPadding = $fields['add_padding'];


?>

<section class="block-gradient-section <?php if ($addPadding) : ?>block-padding<?php endif; ?>">

  <div class="block-gradient-section__wrapper" <?php if ($gradient_color) : ?>style="background: transparent linear-gradient(180deg, <?= $color1; ?> 0%, <?= $color2; ?> 100%) 0% 0% no-repeat padding-box" <?php endif; ?>>
    <div class="block-gradient-section__content content-block trig-fade-up" data-trig style="--trig-delay: 0.1s; --trig-duration: 1.3s;">
      <div class="trig-target">
        <?php if ($tagline) : ?>
          <div class="block-gradient-section__tagline" <?php if ($text_color) : ?>style="color: <?= $text_color; ?>;" <?php endif; ?>> <?= $tagline; ?> </div>
        <?php endif; ?>
        <?php if ($title) : ?>
          <div class="block-gradient-section__title" <?php if ($text_color) : ?>style="color: <?= $text_color; ?>;" <?php endif; ?>> <?= $title; ?> </div>
        <?php endif; ?>
        <?php if ($subtitle) : ?>
          <div class="block-gradient-section__subtitle trig-target" <?php if ($text_color) : ?>style="color: <?= $text_color; ?>; --trig-delay: 0.6s;" <?php endif; ?>> <?= $subtitle; ?> </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

</section>