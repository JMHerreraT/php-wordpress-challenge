<?php
/*
Plugin Name: Event Manager
Description: A custom plugin for managing events and user accounts.
Version: 1.0
Author: Jorge Herrera
*/


// 1.	Create a custom WordPress plugin called Event Manager
// 2.	On activation of the plugin, create a custom database table named “events”
function create_events_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'events';
    
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        org_id bigint(20) NOT NULL,
        staff_count int NOT NULL,
        child_count int NOT NULL,
        address text NOT NULL,
        date_added datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function deactivate() {
    flush_rewrite_rules();
}

register_activation_hook(__FILE__, 'create_events_table');
register_deactivation_hook(__FILE__, 'deactivate');

add_action('admin_menu', 'createMenu');


// When the user logs in, they should be presented with a screen that shows their email address and the school data in a table.
function createMenu() {
    add_menu_page(
        'User dashboard', // title
        'User Dashboard Menu', // menu title
        'manage_options', // capability
        plugin_dir_path(__FILE__    ).'admin/custom-dashboard.php', // slug
        null, //function of content
        '',
        '1' // menu position
    );
}

// Create a custom post type called “schools” that does not divulge any information to the WordPress API.
// a.	Add a record to this CPT with the following data
// i.	Name: Dover School System
// ii.	Org ID: 1001900
// iii.	Staff Count: 120
// iv.	Child Count: 1898
// v.	Address: 123 Education Street, Austin, TX 55555
// vi.	Date Added: [the DateTime this record was created]

function create_schools_post_type() {
    $args = array(
        'labels' => array(
            'name' => 'Schools',
            'singular_name' => 'School',
        ),
        'public' => false, // Set to false to hide it from the WordPress API
        'show_ui' => true, // Show in the admin interface
        'supports' => array('title'),
    );
    register_post_type('schools', $args);
}
add_action('init', 'create_schools_post_type');

// Add a record to the 'schools' custom post type
function add_school_record() {
    $post_data = array(
        'post_title' => 'Dover School System',
        'post_type' => 'schools',
        'post_status' => 'publish',
    );

    // Insert the post and get the post ID
    $post_id = wp_insert_post($post_data);

    // Add custom fields for the additional data
    update_post_meta($post_id, 'org_id', 1001900);
    update_post_meta($post_id, 'staff_count', 120);
    update_post_meta($post_id, 'child_count', 1898);
    update_post_meta($post_id, 'address', '123 Education Street, Austin, TX 55555');
    update_post_meta($post_id, 'date_added', current_time('mysql'));
}

// Hook the function to add the school record
register_activation_hook(__FILE__, 'add_school_record');

// 4.	When a user registers for an account in WordPress, 
// user metadata should be added to track the user's account status. 
// We would want the user's account to be considered “pending” at this point.

function set_user_status_pending($user_id) {
    update_user_meta($user_id, 'account_status', 'pending');
}
add_action('user_register', 'set_user_status_pending');



// When the user logs in to their account for the first time, the user's account status should reflect “active”
function update_user_status_on_first_login($user_login, $user) {
    $account_status = get_user_meta($user->ID, 'account_status', true);

    // Check if the account status is "pending" and set it to "active"
    if ($account_status === 'pending') {
        update_user_meta($user->ID, 'account_status', 'active');
    }
}
add_action('wp_login', 'update_user_status_on_first_login', 10, 2);

// Add bootstrap JS 
function addBootstrapJS($hook) {
    if ($hook !== 'eventManager/admin/custom-dashboard.php') {
        return;
    }
    wp_enqueue_script('bootstrapjs', plugins_url('admin/bootstrap/js/bootstrap.min.js', __FILE__), array('jquery'));
}
add_action('admin_enqueue_scripts', 'addBootstrapJS');

// Add bootstrap CSS

function addBootstrapCSS($hook) {
    if ($hook !== 'eventManager/admin/custom-dashboard.php') {
        return;
    }
    wp_enqueue_style('bootstrapcss', plugins_url('admin/bootstrap/css/bootstrap.min.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'addBootstrapCSS');