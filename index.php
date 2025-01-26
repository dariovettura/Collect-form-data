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







// Funzione per salvare la configurazione del form
function save_form_configuration() {
    // Verifica se i dati sono stati inviati e sono validi
    if (isset($_POST['form_id']) && isset($_POST['form_field_ids']) && isset($_POST['form_field_names'])) {
        // Sanitizzazione dei dati
        $form_id = sanitize_text_field($_POST['form_id']);
        $field_ids = $_POST['form_field_ids'];
        $field_names = $_POST['form_field_names'];

        // Crea un array con la configurazione del form
        $form_config = array(
            'form_id' => $form_id,
            'field_mappings' => array()
        );

        // Popola l'array con i dati dei campi, escludendo i campi vuoti
        for ($i = 0; $i < count($field_ids); $i++) {
            // Se uno dei due campi è vuoto, non aggiungere il mapping
            if (!empty($field_ids[$i]) && !empty($field_names[$i])) {
                $form_config['field_mappings'][] = array(
                    'field_id' => sanitize_text_field($field_ids[$i]),
                    'field_name' => sanitize_text_field($field_names[$i])
                );
            }
        }

        // Salva la configurazione nel database solo se ci sono mappings
        if (!empty($form_config['field_mappings'])) {
            update_option('cfd_form_configuration', $form_config);
        } else {
            // Se non ci sono mappings validi, cancella la configurazione
            delete_option('cfd_form_configuration');
        }
    }

    // Reindirizza l'utente alla pagina di amministrazione
    wp_redirect(admin_url('admin.php?page=collect-form-options')); // Sostituisci con la tua pagina plugin
    exit;
}
add_action('admin_post_save_configuration', 'save_form_configuration');





