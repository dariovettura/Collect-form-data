<?php
/*
Plugin Name: Collect Form Data
Description: A WordPress plugin to collect form data.
Version: 1.0
Author: Dario
*/

 include_once(plugin_dir_path(__FILE__) . 'main.php');


function enqueue_custom_admin_styles() {
  wp_enqueue_style('custom-admin-styles', plugin_dir_url(__FILE__) . 'stile.css');
}


add_action('admin_enqueue_scripts', 'enqueue_custom_admin_styles');








