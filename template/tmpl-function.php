<?php

//
//  Render
//
function render_theme_component($component_name, $args = [])
{
  include get_template_directory() . '/template/components/' . $component_name . '.php';
}


function render_theme_block($block_name, $args = [])
{

  if (isset($args['id_block'])) {
    if (is_string($args['id_block'])) {
      $fields = get_field($args['id_block']);
    } else {
      $fields = $args['id_block'];
    }
  }

  include get_template_directory() . '/template/blocks/' . $block_name . '.php';
}
