<?php

// Options page content
function richieste_options_page()
{
  // Recupera le opzioni della select dal campo ACF
  $categories = acf_get_fields('group_66791fd6840e4');

  // Recupera i post di tipo 'preventivo'
  $preventivi = get_posts(
    array(
      'post_type' => 'preventivo',
      'posts_per_page' => -1
    )
  );

  echo '<div class="wrap">';
  echo '<h1>Invio richieste preventivi</h1>';
  echo '<p>Qui puoi inviare le richieste di preventivo ai fornitori.</p>';


  

  if ($preventivi):
    echo '<select id="preventivo-select" class="custom-select">';
    // Aggiungi un'opzione placeholder
    echo '<option value="">' . esc_html__('Seleziona preventivo', 'textdomain') . '</option>';
    foreach ($preventivi as $preventivo):
      echo '<option value="' . esc_attr($preventivo->ID) . '">' . esc_html($preventivo->post_title) . '</option>';
    endforeach;
    echo '</select>';
  endif;
  echo '<button id="open-modal-button" class="button button-primary">Invia richieste</button>';
  echo '</div>'; // Chiude select-wrapper
  
  // Modale per mostrare i dettagli
  echo '<div id="dialog" title="Dettagli preventivo" style="display:none;">';
  echo '<p><strong>Preventivo selezionato:</strong> <span id="selected-preventivo"></span></p>';
  echo '<h2>Fornitori</h2>';
  echo '<div id="modal-fornitori-list"></div>';
  echo '<button id="invia-richiesta-button" class="button button-primary">Invia richiesta preventivo</button>';
  echo '</div>';
  echo '<div class="list-container ">';
 
  echo '<div class="checkbox-wrapper flex-1">';
  echo '<h2>Lista categorie</h2>';
  if ($categories):
    echo '<div id="category-checkboxes">';
    foreach ($categories as $category):
      echo '<label>';
      echo '<input type="checkbox" name="category[]" value="' . esc_attr($category['name']) . '"> ' . esc_html($category['label']);
      echo '</label><br>';
    endforeach;
    echo '</div>';
  endif;
  echo '</div>';
  echo '  <div class="divider"></div>';

  echo '<div class="list-wrapper flex-1">';
  echo '<h2>Lista fornitori per categoria selezionati</h2>';
  echo '<div id="fornitori-list" class="fornitori-list"></div>'; // Div per visualizzare i risultati
  echo '</div>';
  echo '</div>';
}

function get_fornitori_by_categories()
{
  // Controlla nonce per la sicurezza
  check_ajax_referer('ajax_nonce', 'security');

  $categories = isset($_POST['categories']) ? array_map('sanitize_text_field', $_POST['categories']) : [];

  if (empty($categories)) {
    wp_send_json_success([]);
    wp_die();
  }

  $meta_query = array(
    'relation' => 'AND',
    array(
      'key' => 'stato-albo',
      'value' => 'confermato',
      'compare' => '='
    ),
    array(
      'relation' => 'OR',
      // Aggiungi una condizione per ogni categoria selezionata
      ...array_map(function($category) {
        return array(
          'key' => 'categoria',
          'value' => $category,
          'compare' => '='
        );
      }, $categories)
    )
  );

  $args = array(
    'post_type' => 'fornitore',
    'meta_query' => $meta_query
  );

  $query = new WP_Query($args);

  $fornitori = array();

  if ($query->have_posts()) {
    while ($query->have_posts()) {
      $query->the_post();
      $fornitori[] = array(
        'title' => get_the_title(),
        'email' => get_post_meta(get_the_ID(), 'email', true) // Utilizzare get_post_meta per ottenere il valore del metadato
      );
    }
  }

  wp_send_json_success($fornitori);

  wp_die();
}
add_action('wp_ajax_get_fornitori_by_categories', 'get_fornitori_by_categories');
add_action('wp_ajax_nopriv_get_fornitori_by_categories', 'get_fornitori_by_categories');


add_action('wp_ajax_get_fornitori_by_category', 'get_fornitori_by_category');
add_action('wp_ajax_nopriv_get_fornitori_by_category', 'get_fornitori_by_category');


function invia_email_fornitori()
{
  // Controlla nonce per la sicurezza
  check_ajax_referer('ajax_nonce', 'security');

  // Recupera i dati dalla richiesta AJAX
  $preventivo_id = isset($_POST['preventivo_id']) ? intval($_POST['preventivo_id']) : 0;
  $fornitori = isset($_POST['fornitori']) ? $_POST['fornitori'] : array();

  // Verifica che l'id del preventivo sia valido
  if ($preventivo_id <= 0) {
    wp_send_json_error(array('message' => 'ID del preventivo non valido.'));
  }

  // Recupera il titolo del preventivo


  // Recupera il contenuto del post preventivo
  $body_message = get_post_field('post_content', $preventivo_id);

  $allegato_id = get_post_meta($preventivo_id, '_allegato', true);

  // Inizializza l'allegato
  $attachments = array();
  $allegato_url = '';

  // Verifica se è stato specificato un allegato
  if ($allegato_id) {
    $allegato_url = wp_get_attachment_url($allegato_id);
    $allegato_path = get_attached_file($allegato_id);
    if ($allegato_path && file_exists($allegato_path)) {
      $attachments[] = $allegato_path;
    }
  }


  // Imposta il corpo dell'email e l'allegato
  $headers = array('Content-Type: text/html; charset=UTF-8');

  // Invia l'email a ciascun fornitore
  $success = true;
  foreach ($fornitori as $fornitore) {
    $to = sanitize_email($fornitore['email']);
    $subject = 'Richiesta di preventivo';

    // Costruisci il corpo del messaggio personalizzato
    $message = 'Gentile ' . sanitize_text_field($fornitore['name']) . ',<br><br>';
    $message .= nl2br($body_message) . '<br><br>';
    $message .= 'Cordiali saluti';

    // Esegui l'invio dell'email
    $result = wp_mail($to, $subject, $message, $headers, $attachments);

    // Verifica se l'invio dell'email ha avuto successo
    if (!$result) {
      $success = false;
      // break; // Interrompi il ciclo se c'è un errore
    }
  }

  // Verifica se l'invio delle email ha avuto successo
  if ($success) {
    wp_send_json_success();
  } else {
    wp_send_json_error();
  }

  wp_die();
}

// Aggiungi l'azione per gestire la funzione invia_email_fornitori
add_action('wp_ajax_invia_email_fornitori', 'invia_email_fornitori');
add_action('wp_ajax_nopriv_invia_email_fornitori', 'invia_email_fornitori');


function enqueue_custom_admin_scripts_richieste($hook)
{
  global $typenow;


  // Carica jQuery UI
  wp_enqueue_script('jquery-ui-dialog');
  wp_enqueue_style('wp-jquery-ui-dialog');

  // Carica il file JavaScript personalizzato
  // wp_enqueue_script('custom-admin-js', plugin_dir_url(__FILE__) . 'fornitori.js', array('jquery', 'jquery-ui-dialog'), null, true);
  wp_enqueue_script('richieste-admin-js', plugin_dir_url(__FILE__) . 'invio-richieste.js', array('jquery', 'jquery-ui-dialog'), null, true);
  // Passa l'URL di admin-ajax.php al file JavaScript
  wp_localize_script(
    'richieste-admin-js',
    'fornitori_ajax_object',
    array(
      'ajax_url' => admin_url('admin-ajax.php'),
      'ajax_nonce' => wp_create_nonce('ajax_nonce')
    )
  );


}

add_action('admin_enqueue_scripts', 'enqueue_custom_admin_scripts_richieste');