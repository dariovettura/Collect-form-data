<?php

// Function to create the admin menu and submenus
function collect_form_data_add_menu() {
    add_menu_page(
        'Collect Form Data',          // Page title
        'Collect Form Data',          // Menu title
        'manage_options',             // Capability
        'collect-form-data',          // Menu slug
        'collect_form_data_main_page' // Callback function
    );

    add_submenu_page(
        'collect-form-data',          // Parent slug
        'Subscribers',                // Page title
        'Subscribers',                // Submenu title
        'manage_options',             // Capability
        'collect-form-subscribers',   // Menu slug
        'collect_form_data_subscribers_page' // Callback function
    );

    add_submenu_page(
        'collect-form-data',          // Parent slug
        'Options',                    // Page title
        'Options',                    // Submenu title
        'manage_options',             // Capability
        'collect-form-options',       // Menu slug
        'collect_form_data_options_page' // Callback function
    );
}

// Callback for the main page
function collect_form_data_main_page() {
    echo '<h1>Welcome to Collect Form Data</h1>';
    echo '<p>Manage your form data collection settings here.</p>';
}

// Load Subscribers page
function collect_form_data_subscribers_page() {
    include_once(plugin_dir_path(__FILE__) . 'subscribers.php');
}

// Load Options page
function collect_form_data_options_page() {
    include_once(plugin_dir_path(__FILE__) . 'options.php');
}

// Hook to add the menu
add_action('admin_menu', 'collect_form_data_add_menu');
