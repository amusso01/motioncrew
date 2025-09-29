<?php
$tagline = $fields['tagline'];
$logos = $fields['logos'];

?>

<section class="block-logo-marquee block-padding">
  <div class="block-logo-marquee__wrapper">
    <?php if ($tagline) : ?>
      <div class="block-logo-marquee__tagline"><?php echo $tagline; ?></div>
    <?php endif; ?>

    <!-- Marquee -->
    <!-- An Alpine.js and Tailwind CSS component by https://pinemix.com -->
    <div
      x-data="{ pauseOnHover: false,

      // Initialization
      init() {
      // Clone the Marquee list and append it to the marquee track (we need 2 full width tracks for the Marquee animation to work correctly)
      this.$nextTick(() => {
        const clonedList = this.$refs.marqueeList.cloneNode(true);
        // Add margin-left to create gap between original and cloned list
        clonedList.classList.add('ml-10'); 
        this.$refs.marqueeTrack.appendChild(clonedList);
      });
    }
  }"
      class="relative overflow-hidden">
      <!-- Marquee overlay gradients -->
      <div
        class="absolute inset-y-0 start-0 z-10 w-16 bg-linear-to-r from-gray to-transparent rtl:bg-linear-to-l"
        aria-hidden="true"></div>
      <div
        class="absolute inset-y-0 end-0 z-10 w-16 bg-linear-to-l from-gray to-transparent rtl:bg-linear-to-r"
        aria-hidden="true"></div>
      <!-- END Marquee overlay gradients -->

      <!-- Marquee Track -->
      <div
        x-ref="marqueeTrack"
        class="animate-full-tl rtl:animate-full-tr relative flex w-full"
        x-bind:class="{ 'hover:[animation-play-state:paused]': pauseOnHover }">
        <!-- Marquee list -->
        <div
          x-ref="marqueeList"
          class="flex w-full shrink-0 flex-nowrap items-center justify-around gap-10 px-5">
          <!-- Marquee Items -->
          <?php if ($logos) : ?>
            <?php foreach ($logos as $logo) : ?>
              <?php $svg_file = $logo['svg']; ?>
              <?php $file_url = is_array($svg_file) ? $svg_file['url'] : $svg_file; ?>
              <?php $svg_content = acfFile_toSvg($file_url); ?>
              <?php echo $svg_content; ?>


            <?php endforeach; ?>
          <?php endif; ?>


          <!-- END Marquee Items -->
        </div>
        <!-- END Marquee List -->
      </div>
      <!-- END Marquee Track -->
    </div>
    <!-- END Marquee -->

  </div>
</section>