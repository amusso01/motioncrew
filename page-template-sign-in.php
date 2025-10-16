<?php

/*
* Template Name: Sign In Page
*/

?>

<?php

if (!defined('ABSPATH')) {
  exit;
}


/**
 * Redirect logged-in users immediately.
 */
if (is_user_logged_in()) {
  $user = wp_get_current_user();
  if (in_array('administrator', (array) $user->roles, true) || in_array('editor', (array) $user->roles, true)) {
    wp_safe_redirect(admin_url());
  } else {
    wp_safe_redirect(home_url('/crew-list/'));
  }
  exit;
}
/**
 * Get redirect destination
 */
$redirect_to = isset($_GET['redirect_to']) && $_GET['redirect_to']
  ? esc_url_raw(wp_unslash($_GET['redirect_to']))
  : home_url('/crew-list/');

$error_message = '';

/**
 * Handle login POST
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signin_nonce']) && wp_verify_nonce($_POST['signin_nonce'], 'do_signin')) {
  $creds = [
    'user_login'    => sanitize_text_field(wp_unslash($_POST['log'] ?? '')),
    'user_password' => $_POST['pwd'] ?? '',
    'remember'      => false, // Disabled "remember me"
  ];

  $user = wp_signon($creds, is_ssl());

  if (is_wp_error($user)) {
    $error_message = __('Invalid username/email or password. Please try again.', 'your-textdomain');
  } else {
    // Role-based redirect
    if (in_array('administrator', (array) $user->roles, true) || in_array('editor', (array) $user->roles, true)) {
      $dest = admin_url();
    } else {
      $dest = $redirect_to ?: home_url('/crew-list/');
    }
    wp_safe_redirect($dest);
    exit;
  }
}


get_header(); ?>

<main role="main" class="site-main page-template-sign-in">
  <div class="content-block">
    <div class="content-max">
      <div class="page-template-sign-in__content">
        <p class="page-template-sign-in__form-tagline">//ACCOUNT</p>
        <h2 class="page-template-sign-in__form-title">SIGN IN</h2>

        <form method="post" action="" class="signin-form">

          <?php if (!empty($error_message)) : ?>
            <div class="signin-form__error" role="alert">
              <?php echo wp_kses_post($error_message); ?>
            </div>
          <?php endif; ?>

          <div class="page-template-sign-in__form-field-wrapper">
            <label for="log" class="page-template-sign-in__form-label">Email
              <input type="text" name="log" id="log" class="page-template-sign-in__form-input-field" required autocomplete="username" />
            </label>
          </div>

          <div class="page-template-sign-in__form-field-wrapper">
            <label for="pwd" class="page-template-sign-in__form-label">Password
              <input type="password" name="pwd" id="pwd" class="page-template-sign-in__form-input-field" required autocomplete="current-password" />
            </label>
          </div>

          <?php
          if (!empty($redirect_to)) {
            echo '<input type="hidden" name="redirect_to" value="' . esc_attr($redirect_to) . '">';
          }
          wp_nonce_field('do_signin', 'signin_nonce');
          ?>

          <p class="page-template-sign-in__form-actions page-template-sign-in__form-field-wrapper">
            <button type="submit" class="fdry-form-btn">Log In</button>
          </p>

          <p class="page-template-sign-in__form-links">
            Forgot password?
            <a href="<?php echo esc_url(wp_lostpassword_url($redirect_to)); ?>">Click here</a>
          </p>
          <?php
          $register_url = function_exists('wp_registration_url') ? wp_registration_url() : home_url('/register/');
          if (get_option('users_can_register')) : ?>
            <p class="page-template-sign-in__form-links">Don't have an account? <a href="<?php echo esc_url($register_url); ?>">Register</a></p>
          <?php endif; ?>

        </form>

      </div>
    </div>
  </div>
</main>

<?php get_footer(); ?>