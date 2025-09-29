<?php
$tagline = $fields['tagline'];
$title = $fields['title'];
$cards_row = $fields['cards_row'];
// debug($cards_row);

?>

<section class="block-cards block-padding content-block">
  <div class="content-max">
    <div class="block-cards__wrapper">

      <?php if ($tagline) : ?>
        <div class="block-cards__tagline"> <?php echo $tagline; ?> </div>
      <?php endif; ?>

      <?php if ($title) : ?>
        <div class="block-cards__title"> <?php echo $title; ?> </div>
      <?php endif; ?>

      <div class="block-cards__cards">
        <?php foreach ($cards_row as $card) : ?>
          <div class="block-cards__grid">
            <?php foreach ($card['row'] as $row) : ?>
              <div class="block-cards__item">
                <div class="icon">
                  <?php $svg_file = $row['icon']; ?>
                  <?php $file_url = is_array($svg_file) ? $svg_file['url'] : $svg_file; ?>
                  <?php $svg_content = acfFile_toSvg($file_url); ?>
                  <?php echo $svg_content; ?>
                </div>
                <div class="title">
                  <?php echo $row['title']; ?>
                </div>
                <div class="description">
                  <div class="content">
                    <?php echo $row['content']; ?>
                  </div>
                  <div class="link">
                    <a href="<?php echo $row['link']; ?>"><?= get_template_part('svg-template/svg-arrow-diagonal') ?></a>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
      </div>
    <?php endforeach; ?>
    </div>
  </div>
  </div>
</section>