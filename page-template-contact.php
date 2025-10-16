<?php

/*
* Template Name: Contact Page
*/

get_header(); ?>

<?php
$tagLine = get_field('tagline');
$title = get_field('title');
$subTitle = get_field('subtitle');
$email = get_field('email_link');
?>

<main role="main" class="site-main page-template-contact">

  <div class="content-block">
    <div class="content-max">

      <div class="page-template-contact__grid">

        <section class="page-template-contact__info">
          <?php if ($tagLine) : ?>
            <p class="tagline"><?= $tagLine ?></p>
          <?php endif; ?>
          <?php if ($title) : ?>
            <div class="title"><?= $title ?></div>
          <?php endif; ?>
          <?php if ($subTitle) : ?>
            <div class="subtitle"><?= $subTitle ?></div>
          <?php endif; ?>
          <?php if ($email) : ?>
            <p class="email-title">Email</p>
            <a class="email" href="mailto:<?= $email ?>"><?= $email ?></a>
          <?php endif; ?>
        </section>

        <section class="page-template-contact__contact-form">

          <div class="form">
            <?= do_shortcode('[contact-form-7 id="6249d4f" title="Contact us"]') ?>

            <p class="tc">By clicking ‘Send Message‘ you’re confirming that you agree with our <a href="<?= get_field('terms_and_conditions') ?>">Terms and Conditions.</a></p>
            <div class="btn blue" id="fdryCf7Submit">
              <a class="btn_wrapper" href="#">
                <div class="text">SEND MESSAGE <span class="circle"></span></div>
                <div class="text-hover">SEND MESSAGE <span class="circle"></span></div>
              </a>
            </div>
          </div>

        </section>

      </div>

    </div>

  </div>

</main>

<?php get_footer(); ?>