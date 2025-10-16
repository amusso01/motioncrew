<?php

/**
 * Plugin Name: Custom Site Profiles
 * Description: Profile CPT + taxonomies + sync from User.
 * Author: Andrea Musso FDRY
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) exit;

require __DIR__ . '/inc/cpt.php';
require __DIR__ . '/inc/sync.php';
require __DIR__ . '/inc/availability.php';
require __DIR__ . '/inc/rest.php';
