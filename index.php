<?php
/**
 * Plugin Name: Gestione preventivi
 * Description: Plugin per invio e gestione preventivi ai fornitori.
 * Version: 1.0
 * Author: Dario
 **/

 include_once(plugin_dir_path(__FILE__) . 'menu/main.php');
 include_once(plugin_dir_path(__FILE__) . 'menu/conferma-albo.php');
 include_once(plugin_dir_path(__FILE__) . 'menu/fornitori.php');
 include_once(plugin_dir_path(__FILE__) . 'menu/opzioni.php');
 include_once(plugin_dir_path(__FILE__) . 'menu/invio-richieste.php');
 include_once(plugin_dir_path(__FILE__) . 'menu/preventivi.php');
 include_once(plugin_dir_path(__FILE__) . 'registra-fornitori.php');





function enqueue_custom_admin_styles() {
  wp_enqueue_style('custom-admin-styles', plugin_dir_url(__FILE__) . 'stile.css');
}


add_action('admin_enqueue_scripts', 'enqueue_custom_admin_styles');








