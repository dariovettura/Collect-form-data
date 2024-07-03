<?php


// Registra il cpt preventivi
add_action('init', 'preventivi_post_type');
function preventivi_post_type()
{
  register_post_type(
    'preventivo',
    array(
      'labels' => array(
        'name' => __('Preventivi'),
        'singular_name' => __('Preventivo')
      ),
      'public' => false, // Set to false to hide from frontend view
      'publicly_queryable' => false, // Set to false to disable public queries
      'exclude_from_search' => true, // Exclude from search schema
      'show_ui' => true, // Display in admin UI
      'show_in_menu' => true, // Display in admin menu
      'has_archive' => false, // Disable archive
      'rewrite' => array('slug' => 'preventivi'),
    )
  );
}



// Function to display the Preventivi page
function preventivi_page()
{
    // Code for the Preventivi list page
    ?>
    <div>
        <h1 style="display: inline-block;">Preventivi</h1>
        <a href="<?php echo admin_url('post-new.php?post_type=preventivo'); ?>" class="page-title-action">Aggiungi Nuovo</a>
    </div>

    <?php
    // Include the WordPress posts list table class
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
    require_once(ABSPATH . 'wp-admin/includes/template.php');

    // Set the current screen to display the post type list table
    $post_type = 'preventivo';
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

// Aggiungi un endpoint Ajax per recuperare i preventivi
add_action('wp_ajax_get_preventivi', 'get_preventivi_callback');
function get_preventivi_callback() {
    // Query per ottenere i post di tipo "preventivo"
    $preventivi_query = new WP_Query(array(
        'post_type' => 'preventivo',
        'posts_per_page' => -1,
        // Altre opzioni di query se necessario
    ));

    // Array per memorizzare i preventivi
    $preventivi = array();

    // Loop attraverso i risultati della query e aggiungi i preventivi all'array
    if ($preventivi_query->have_posts()) {
        while ($preventivi_query->have_posts()) {
            $preventivi_query->the_post();
            $preventivi[get_the_ID()] = get_the_title();
        }
    }

    // Resetta le informazioni sul post
    wp_reset_postdata();

    // Restituisci i preventivi come JSON
    wp_send_json_success($preventivi);
}



// Aggiungi il metabox per l'allegato
add_action('add_meta_boxes', 'add_allegato_metabox');
function add_allegato_metabox()
{
    add_meta_box(
        'allegato_metabox', // ID del metabox
        __('Allegato'), // Titolo del metabox
        'render_allegato_metabox', // Callback per il rendering del metabox
        'preventivo', // Post type
        'side', // Contesto
        'default' // Priorità
    );
}

// Rendering del metabox
function render_allegato_metabox($post)
{
    // Recupera il valore del metadato esistente
    $allegato = get_post_meta($post->ID, '_allegato', true);
    
    // Usa wp_nonce_field per proteggere il salvataggio del metadato
    wp_nonce_field('save_allegato_metabox', 'allegato_metabox_nonce');
    
    ?>
    <p>
        <label for="allegato"><?php _e('Scegli un file dai media:'); ?></label>
        <input type="hidden" id="allegato" name="allegato" value="<?php echo esc_attr($allegato); ?>">
        <button type="button" class="button" id="upload_allegato_button"><?php _e('Scegli File'); ?></button>
        <div id="allegato_preview">
            <?php if ($allegato) : ?>
                <p><?php echo basename(get_attached_file($allegato)); ?></p>
            <?php endif; ?>
        </div>
    </p>
    <script>
    jQuery(document).ready(function($){
        var mediaUploader;
        $('#upload_allegato_button').click(function(e) {
            e.preventDefault();
            // If the uploader object has already been created, reopen the dialog
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            // Extend the wp.media object
            mediaUploader = wp.media.frames.file_frame = wp.media({
                title: '<?php _e('Scegli un file dai media'); ?>',
                button: {
                    text: '<?php _e('Scegli File'); ?>'
                },
                multiple: false
            });
            // When a file is selected, grab the URL and set it as the text field's value
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#allegato').val(attachment.id);
                $('#allegato_preview').html('<p>' + attachment.filename + '</p>');
            });
            // Open the uploader dialog
            mediaUploader.open();
        });
    });
    </script>
    <?php
}

// Salva il metadato del metabox
add_action('save_post', 'save_allegato_metabox');
function save_allegato_metabox($post_id)
{
    // Verifica il nonce per assicurarsi che la richiesta provenga dal nostro metabox
    if (!isset($_POST['allegato_metabox_nonce']) || !wp_verify_nonce($_POST['allegato_metabox_nonce'], 'save_allegato_metabox')) {
        return $post_id;
    }

    // Verifica che non sia un autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // Verifica i permessi dell'utente
    if ('preventivo' == $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    } else {
        return $post_id;
    }

    // Salva o cancella il metadato
    $allegato = isset($_POST['allegato']) ? intval($_POST['allegato']) : '';
    if ($allegato) {
        update_post_meta($post_id, '_allegato', $allegato);
    } else {
        delete_post_meta($post_id, '_allegato');
    }
}
