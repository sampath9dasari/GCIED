<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

add_action( 'wp_ajax_hg_gmaps_save_api_key', 'wp_ajax_hg_gmaps_save_api_key_callback' );
function wp_ajax_hg_gmaps_save_api_key_callback(){
    if( isset($_REQUEST['hg_gmaps_nonce']) && !empty($_REQUEST['hg_gmaps_nonce']) && wp_verify_nonce( $_REQUEST['hg_gmaps_nonce'], 'hg_gmaps_nonce' ) && isset($_REQUEST['api_key']) && !empty($_REQUEST['api_key']) ) {
        update_option( 'hg_gmaps_api_key', $_REQUEST['api_key'] );
        echo json_encode(array("success"=>1));
        die();
    }
    die(0);
}
