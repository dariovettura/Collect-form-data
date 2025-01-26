<?php

// Options page content
function preventivi_options_page() {
    ?>
    <div class="wrap">
        <h1>Opzioni Gestione Preventivi</h1>
        <p>Qui puoi gestire le opzioni per i tuoi preventivi.</p>
        <form method="post" action="options.php">
            <?php
            settings_fields('preventivi_options_group');
            do_settings_sections('preventivi_options_page');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', 'preventivi_register_settings');

function preventivi_register_settings() {
    // Registriamo l'opzione
    register_setting('preventivi_options_group', 'gestione_fornitori');

    // Aggiungiamo una sezione alla pagina delle opzioni
    add_settings_section(
        'preventivi_mail_section', 
        'Impostazioni Mail',
        'preventivi_mail_text_callback', 
        'preventivi_options_page'
    );

     // Add the mail_from field
     add_settings_field(
        'gestione_fornitori_mail_from',
        'Mittente della Mail',
        'preventivi_mail_from_callback',
        'preventivi_options_page',
        'preventivi_mail_section'
    );
  
}


function preventivi_mail_text_callback() {
    echo '<p>Configura il messaggio che verrà inviato via mail alla conferma in albo dell fornitore. La mail inizierà sempre e a prescindere con "Gentile NOME FORNITORE,"</p>';
    $options = get_option('gestione_fornitori');
    $mail_text = isset($options['mail_text']) ? $options['mail_text'] : '';
    wp_editor($mail_text, 'gestione_fornitori_mail_text', array(
        'textarea_name' => 'gestione_fornitori[mail_text]',
        'textarea_rows' => 10,
        'media_buttons' => true
    ));
}

function preventivi_mail_from_callback() {
    $options = get_option('gestione_fornitori');
    $mail_from = isset($options['mail_from']) ? $options['mail_from'] : '';
    echo '<input type="text" name="gestione_fornitori[mail_from]" value="' . esc_attr($mail_from) . '" class="regular-text" />';
}
?>

