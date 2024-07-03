<?php

function send_custom_webhook($record, $handler)
{
  // Ottieni tutti i campi del record
  $raw_fields = $record->get('fields');

  // Prepara un array per i campi da inviare
  $fields_to_send = array();

  // Itera su ogni campo nel record
  foreach ($raw_fields as $field_key => $field_data) {
    // Aggiungi il valore del campo all'array dei campi da inviare
    $fields_to_send[$field_key] = $field_data['value'];
  }

  // Aggiungi i dati dei campi alla risposta del webhook
  $handler->add_response_data('fields', $fields_to_send);
}
add_action('elementor_pro/forms/new_record', 'send_custom_webhook', 10, 2);



// Aggiungi il JavaScript personalizzato


function cfl_add_custom_js()
{
  // Includi lo script JavaScript nel footer
  wp_enqueue_script('get_email', plugins_url('/registra-fornitori.js', __FILE__), array('jquery'), null, true);

  // Passa l'url per l'ajax al file javascript
  wp_localize_script('get_email', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'cfl_add_custom_js');




function map_fields($post_data)
{
  // Verifica che il campo 'fields' esista
  if (!isset($post_data)) {
    return [];
  }

  // Mappa i campi personalizzati ai nomi standard
  $mapped_fields = array(
    'nome' => isset($post_data['name']) ? sanitize_text_field ($post_data['name']): '',
    'cognome' => isset($post_data['field_f50917d']) ? sanitize_text_field ($post_data['field_f50917d']) : '',
    'indirizzo' => isset($post_data['field_0cca7df']) ? sanitize_text_field ($post_data['field_0cca7df']) : '',
    'email' => isset($post_data['field_7a7bc64']) ? sanitize_text_field ($post_data['field_7a7bc64']) : '',
    'categoria' => isset($post_data['field_c07434b']) ? sanitize_text_field ($post_data['field_c07434b']) : '', 
    'stato-albo' => 'non_confermato'
  );

  // Restituisce l'array con i campi mappati
  return $mapped_fields;
}



function create_post_from_form()
{

  // Controlla se il parametro fields è presente nella richiesta POST
  if (isset($_POST['fields'])) {
    $fields = map_fields($_POST['fields']);
    $post_title = $fields['nome'] . ' ' . $fields['cognome'];
   

    // Crea il nuovo post
    $post_id = wp_insert_post(
      array(
        'post_title' => $post_title ,
        'post_content' => '',
        'post_type' => 'fornitore',
        'post_status' => 'publish',
      )
    );

    // Verifica se il post è stato creato con successo
    if ($post_id) {
      foreach ($fields as $campo_key => $campo_value) {
        update_post_meta( $post_id, $campo_key, $campo_value);
      }
      wp_send_json_success(array('post_id' => $post_id));
    } else {
      wp_send_json_error(array('message' => 'Post creation failed.'));
    }
  } else {
    wp_send_json_error(array('message' => 'Fields data not provided.'));
  }
}
add_action('wp_ajax_create_post_from_form', 'create_post_from_form');
add_action('wp_ajax_nopriv_create_post_from_form', 'create_post_from_form');

