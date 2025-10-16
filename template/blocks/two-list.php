<?php
$tagline = $fields['tagline'];
$title = $fields['title'];
$blue_list = $fields['blue_list'];
$green_list = $fields['green_list'];
?>

<section class="block-two-list block-padding content-block">
  <div class="content-max">
    <div class="block-two-list__wrapper">
      <div class="block-two-list__tagline"> <?= $tagline; ?> </div>
      <div class="block-two-list__title"> <?= $title; ?> </div>

      <div class="block-two-list__list-grid">
        <div class="block-two-list__list-grid-item blue-list">
          <div class="block-two-list__list-grid-item-title"> <?= $blue_list['list_title']; ?> </div>
          <div class="block-two-list__list-grid-item-list">
            <ul>
              <?php foreach ($blue_list['list_item'] as $item) : ?>

                <li><span><i><?= get_template_part('svg-template/svg-tick') ?></i></span> <?= $item['content']; ?> </li>

              <?php endforeach; ?>
            </ul>
          </div>
        </div>
        <div class="block-two-list__list-grid-item green-list">
          <div class="block-two-list__list-grid-item-title"> <?= $green_list['list_title']; ?> </div>
          <div class="block-two-list__list-grid-item-list">
            <ul>
              <?php foreach ($green_list['list_item'] as $item) : ?>
                <li><span><i><?= get_template_part('svg-template/svg-tick') ?></i></span> <?= $item['content']; ?> </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>