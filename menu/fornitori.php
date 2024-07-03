<?php
global $my_fields;
$my_fields = array(
  'nome',
  'cognome',
  'indirizzo',
  'email',
  'telefono',
  'categoria',
  'stato-albo'
);
// Registra il cpt fornitori
add_action('init', 'fornitori_post_type');
function fornitori_post_type()
{
  register_post_type(
    'fornitore',
    array(
      'labels' => array(
        'name' => __('Fornitori'),
        'singular_name' => __('Fornitore')
      ),
      'public' => false, // Set to false to hide from frontend view
      'publicly_queryable' => false, // Set to false to disable public queries
      'exclude_from_search' => true, // Exclude from search schema
      'show_ui' => true, // Display in admin UI
      'show_in_menu' => true, // Display in admin menu
      'has_archive' => false, // Disable archive
      'rewrite' => array('slug' => 'fornitori'),
    )
  );
}

// Function to display the Fornitori page
function fornitori_page()
{
  // Code for the Fornitori list page
  ?>
  <div>
    <h1 style="display: inline-block;">Fornitori</h1>
    <a href="<?php echo admin_url('post-new.php?post_type=fornitore'); ?>" class="page-title-action">Aggiungi Nuovo</a>
  </div>

  <?php
  // Include the WordPress posts list table class
  require_once (ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
  require_once (ABSPATH . 'wp-admin/includes/template.php');

  // Set the current screen to display the post type list table
  $post_type = 'fornitore';
  $post_type_object = get_post_type_object($post_type);
  $screen = get_current_screen();
  $screen->post_type = $post_type;

  // Prepare arguments for displaying the post type list table
  $args = array(
    'post_type' => $post_type,
  );

  // Display the post type list table
  $wp_list_table = _get_list_table('WP_Posts_List_Table');
  $wp_list_table->prepare_items();
  $wp_list_table->display();
}


function aggiungi_custom_fields_metabox_fornitori()
{
  add_meta_box(
    'custom_fields_metabox_fornitori',
    __('Dati Fornitore'),
    'mostra_custom_fields_metabox_fornitori',
    'fornitore',
    'normal',
    'high'
  );
}
add_action('add_meta_boxes_fornitore', 'aggiungi_custom_fields_metabox_fornitori');

// Funzione per visualizzare i custom fields nella metabox
function mostra_custom_fields_metabox_fornitori($post)
{
  // Recupera i custom fields che vuoi visualizzare
  global $my_fields;

  // Recupera le categorie da ACF
  $categories = acf_get_fields('group_66791fd6840e4');

  // Mostra i campi input per ciascun custom field
  foreach ($my_fields as $field) {
    $field_value = get_post_meta($post->ID, $field, true);

    if ($field == 'stato-albo') {
      ?>
      <p>
        <label for="<?php echo esc_attr($field); ?>"><?php echo esc_html($field); ?>:</label><br>
        <select id="<?php echo esc_attr($field); ?>" name="<?php echo esc_attr($field); ?>">
          <option value="confermato" <?php selected($field_value, 'confermato'); ?>>Confermato</option>
          <option value="non_confermato" <?php selected($field_value, 'non_confermato'); ?>>Non confermato</option>
        </select>
      </p>
      <?php
    } elseif ($field == 'categoria') {
      ?>
      <p>
        <label for="<?php echo esc_attr($field); ?>"><?php echo esc_html($field); ?>:</label><br>
        <select id="<?php echo esc_attr($field); ?>" name="<?php echo esc_attr($field); ?>">
          <?php foreach ($categories as $category): ?>
            <option value="<?php echo esc_attr($category['name']); ?>" <?php selected($field_value, $category['name']); ?>>
              <?php echo esc_html($category['label']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </p>
      <?php
    } else {
      ?>
      <p>
        <label for="<?php echo esc_attr($field); ?>"><?php echo esc_html($field); ?>:</label><br>
        <input type="text" id="<?php echo esc_attr($field); ?>" name="<?php echo esc_attr($field); ?>"
          value="<?php echo esc_attr($field_value); ?>">
      </p>
      <?php
    }
  }
}


// Funzione per salvare i custom fields
function salva_custom_fields_fornitori($post_id)
{
  // // Verifica se il campo è stato inviato
  // if (!isset($_POST['email']) || !isset($_POST['telefono']) || !isset($_POST['preventivo'])) {
  //   return;
  // }

  global $my_fields;

  foreach ($my_fields as $field) {
    if (isset($_POST[$field])) {
      update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
    }
  }
}
add_action('save_post_fornitore', 'salva_custom_fields_fornitori');


// Rimuovere l'editor di testo classico
function rimuovi_editor_di_testo_fornitori()
{
  remove_post_type_support('fornitore', 'editor');
}
add_action('init', 'rimuovi_editor_di_testo_fornitori');


// Aggiungere colonne personalizzate nella lista dei post
function aggiungi_colonne_personalizzate_fornitori($columns)
{
  $columns['email'] = __('Email');
  $columns['telefono'] = __('Telefono');
  $columns['categoria'] = __('categoria');
  $columns['stato-albo'] = __('Stato Albo');
  return $columns;
}
add_filter('manage_fornitore_posts_columns', 'aggiungi_colonne_personalizzate_fornitori');

function mostra_colonne_personalizzate_fornitori($column, $post_id)
{
  switch ($column) {
    case 'email':
      echo get_post_meta($post_id, 'email', true);
      break;
    case 'telefono':
      echo get_post_meta($post_id, 'telefono', true);
      break;
    case 'categoria':
      echo get_post_meta($post_id, 'categoria', true);
      break;
    case 'stato-albo':
      echo get_post_meta($post_id, 'stato-albo', true);
      break;
  }
}
add_action('manage_fornitore_posts_custom_column', 'mostra_colonne_personalizzate_fornitori', 10, 2);


// Rendere le colonne ordinabili
function colonne_ordinabili_fornitori($columns)
{
  $columns['email'] = 'email';
  $columns['telefono'] = 'telefono';
  $columns['categoria'] = 'categoria';
  $columns['stato-albo'] = 'stato-albo';
  return $columns;
}
add_filter('manage_edit-fornitore_sortable_columns', 'colonne_ordinabili_fornitori');

// Impostare l'ordine delle colonne
function ordine_colonne_fornitori($query)
{
  if (!is_admin()) {
    return;
  }

  $orderby = $query->get('orderby');

  if ('email' === $orderby) {
    $query->set('meta_key', 'email');
    $query->set('orderby', 'meta_value');
  } elseif ('telefono' === $orderby) {
    $query->set('meta_key', 'telefono');
    $query->set('orderby', 'meta_value');

  } elseif ('categoria' === $orderby) {
    $query->set('meta_key', 'categoria');
    $query->set('orderby', 'meta_value');

  } elseif ('stato-albo' === $orderby) {
    $query->set('meta_key', 'stato-albo');
    $query->set('orderby', 'meta_value');
  }
}
add_action('pre_get_posts', 'ordine_colonne_fornitori');



// Aggiungi il campo "preventivo" nella modifica rapida
//non gestito//non gestito//non gestito//non gestito//non gestito
//non gestito//non gestito//non gestito//non gestito//non gestito//non gestito
//non gestito//non gestito//non gestito//non gestito//non gestito//non gestito
//non gestito//non gestito//non gestito//non gestito//non gestito//non gestito

function aggiungi_campo_modifica_rapida_fornitori($column_name, $post_type)
{
  if ($post_type == 'fornitore' && $column_name == 'preventivo') {
    global $post;
    $preventivo = get_post_meta($post->ID, 'preventivo', true);
    ?>
    <fieldset class="inline-edit-col-right">
      <div class="inline-edit-col">
        <label>
          <span class="title"><?php _e('Preventivo'); ?></span>
          <select name="preventivo" class="preventivo">
            <option value="" <?php selected($preventivo, ''); ?>><?php _e('— No Change —'); ?></option>
            <option value="in_attesa" <?php selected($preventivo, 'in_attesa'); ?>><?php _e('In attesa'); ?></option>
            <option value="confermato" <?php selected($preventivo, 'confermato'); ?>><?php _e('Confermato'); ?></option>
            <option value="non_confermato" <?php selected($preventivo, 'non_confermato'); ?>><?php _e('Non confermato'); ?>
            </option>
          </select>
        </label>
      </div>
    </fieldset>
    <?php
  }
}
// add_action('quick_edit_custom_box', 'aggiungi_campo_modifica_rapida_fornitori', 10, 2);

// Salva il campo "preventivo" dalla modifica rapida
function salva_modifica_rapida_fornitori($post_id)
{
  if (isset($_POST['preventivo'])) {
    update_post_meta($post_id, 'preventivo', sanitize_text_field($_POST['preventivo']));
  }
}
// add_action('save_post', 'salva_modifica_rapida_fornitori');







function enqueue_custom_admin_scripts($hook)
{
  global $typenow;

  // Verifica se si tratta della schermata del tuo CPT 'fornitori'.
  if ('fornitore' === $typenow) {
    // Carica jQuery UI
    wp_enqueue_script('jquery-ui-dialog');
    wp_enqueue_style('wp-jquery-ui-dialog');

    // Carica il file JavaScript personalizzato
    // wp_enqueue_script('custom-admin-js', plugin_dir_url(__FILE__) . 'fornitori.js', array('jquery', 'jquery-ui-dialog'), null, true);
     wp_enqueue_script('custom-admin-js', plugin_dir_url(__FILE__) . 'conferma-albo.js', array('jquery', 'jquery-ui-dialog'), null, true);

    // Passa l'URL di admin-ajax.php al file JavaScript
    wp_localize_script(
      'custom-admin-js',
      'ajax_object',
      array(
        'ajax_url' => admin_url('admin-ajax.php')
      )
    );
  }
}
add_action('admin_enqueue_scripts', 'enqueue_custom_admin_scripts');

