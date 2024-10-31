<?php
/*
Plugin Name: Remove Deleted Pages from Search Index
Description: A lightweight plugin that implements the 410 HTTP status code for deleted pages to inform Google that the pages should be removed from its search index.
Version: 3.0
Author: Oliver Hancke
Author URI: https://klutch.dk
*/

function rdp_deleted_page_410() {
  if ( is_404() ) {
    global $wpdb;
    $requested_url = home_url( add_query_arg( NULL, NULL ) );
    
    // Check if the requested URL is in the deleted_urls table
    $deleted_url_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}deleted_urls WHERE url = %s", $requested_url ) );
    
    if ( $deleted_url_id ) {
      status_header( 410 );
    }
  }
}
add_action( 'template_redirect', 'rdp_deleted_page_410' );

function rdp_create_deleted_urls_table() {
  global $wpdb;

  $table_name = $wpdb->prefix . 'deleted_urls';
  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE $table_name (
    id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    url varchar(2048) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY url (url)
  ) $charset_collate;";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );
}
register_activation_hook( __FILE__, 'rdp_create_deleted_urls_table' );

function rdp_store_deleted_url( $post_id ) {
  global $wpdb;

  $url = get_permalink( $post_id );

  if ( $url && filter_var($url, FILTER_VALIDATE_URL)) {
    $table_name = $wpdb->prefix . 'deleted_urls';

    $inserted = $wpdb->insert( $table_name, array( 'url' => $url ) );
    if(!$inserted){
      error_log("Failed to insert URL to deleted_urls table");
    }
  }
}
add_action( 'before_delete_post', 'rdp_store_deleted_url' );

function rdp_trash_deleted_url( $post_id ) {
  global $wpdb;

  $url = get_permalink( $post_id );

  if ( $url && filter_var($url, FILTER_VALIDATE_URL)) {
    $table_name = $wpdb->prefix . 'deleted_urls';

    $inserted = $wpdb->insert( $table_name, array( 'url' => $url ) );
    if(!$inserted){
      error_log("Failed to insert URL to deleted_urls table");
    }
  }
}
add_action( 'wp_trash_post', 'rdp_trash_deleted_url' );

function rdp_untrash_post( $post_id ) {
  global $wpdb;

  $url = get_permalink( $post_id );

  if ( $url && filter_var($url, FILTER_VALIDATE_URL)) {
    $table_name = $wpdb->prefix . 'deleted_urls';

    $wpdb->delete( $table_name, array( 'url' => $url ) );
  }
}
add_action( 'untrash_post', 'rdp_untrash_post' );

function rdp_add_admin_menu() {
    add_menu_page('Deleted URLs', 'Deleted URLs', 'activate_plugins', 'deleted-urls', 'rdp_deleted_urls_page', 'dashicons-admin-links');
}

function rdp_deleted_urls_page() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'deleted_urls';

    $deleted_urls = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );

    echo '<h2>Deleted URLs</h2>';

    if ( isset($_POST['new_url']) && filter_var($_POST['new_url'], FILTER_VALIDATE_URL) ) {
        $wpdb->insert( $table_name, array( 'url' => $_POST['new_url'] ) );
    }

    echo '<form method="post" action="?page=deleted-urls">';
    echo '<input type="url" name="new_url">';
    echo '<input type="submit" value="Add URL">';
    echo '</form>';

    foreach ( $deleted_urls as $deleted_url ) {
        echo '<p>' . esc_html($deleted_url['url']) . ' <a href="?page=deleted-urls&delete=' . esc_attr($deleted_url['id']) . '">Delete</a></p>';
    }
}
add_action( 'admin_menu', 'rdp_add_admin_menu' );

function rdp_check_url_deletion() {
    if ( isset($_GET['delete']) ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'deleted_urls';

        $wpdb->delete( $table_name, array( 'id' => intval($_GET['delete']) ) );
    }
}
add_action( 'admin_init', 'rdp_check_url_deletion' );
