<?php

/**
 * Button
 * 
 * @author Andrea Musso
 * 
 * @package Foundry
 */
?>

<?php
$text = $args['text'];
$link = $args['link'];
?>

<div class="btn" x-data="{ hover: false }">
  <a class="btn_wrapper" href="<?php echo $link; ?>">
    <div class="text"><?php echo $text; ?> <span class="circle"></span></div>
    <div class="text-hover"><?php echo $text; ?> <span class="circle"></span></div>
  </a>
</div>