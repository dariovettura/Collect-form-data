<?php

// Add menu item in the admin menu
add_action('admin_menu', 'gestione_preventivi_menu');
function gestione_preventivi_menu()
{
    // Add main menu item, set the main URL to the Fornitori CPT list
    $plugin_menu = add_menu_page('Gestione Preventivi', 'Gestione Preventivi', 'manage_options', 'gestione-preventivi', 'redirect_to_fornitori', 'dashicons-admin-generic', 6);

    // Add sub menu items
    add_submenu_page('gestione-preventivi', 'Lista Fornitori', 'Fornitori', 'manage_options', 'edit.php?post_type=fornitore');
    add_submenu_page('gestione-preventivi', 'Lista Preventivi', 'Preventivi', 'manage_options', 'edit.php?post_type=preventivo');
    add_submenu_page('gestione-preventivi', 'Opzioni', 'Opzioni', 'manage_options', 'gestione-preventivi-opzioni', 'preventivi_options_page');
    add_submenu_page('gestione-preventivi', 'Invio Richieste', 'Richieste', 'manage_options', 'richieste-preventivi-opzioni', 'richieste_options_page');
    
    // Remove the default first submenu item
    global $submenu;
    if (isset($submenu['gestione-preventivi'])) {
        unset($submenu['gestione-preventivi'][0]);
    }

    // Remove the menu items from the main menu
    remove_menu_page('edit.php?post_type=preventivo');
    remove_menu_page('edit.php?post_type=fornitore');
}


// Redirect function to go to Fornitori page
function redirect_to_fornitori()
{
    // Use JavaScript to redirect to the Fornitori list page
    echo '<script>window.location.href = "edit.php?post_type=fornitore";</script>';
}