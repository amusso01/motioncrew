<?php
$content = $fields['content'];

// Option
$bg_color = $fields['bg_color'];
$text_color = $fields['text_color'];
?>

<section class="block-text-banner block-padding content-block">

  <div class="block-text-banner__wrapper trig-reveal-down" <?php if ($bg_color) : ?>style="background-color: <?= $bg_color; ?>; color: <?= $text_color; ?>;" <?php endif; ?> data-trig>
    <div class="trig-target">
      <?php echo $content; ?>
    </div>
  </div>

</section>