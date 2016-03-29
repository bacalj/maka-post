<?php
/*
Plugin Name: Maka Post
Description: Creates a private post for every Ninja Forms submission - all fields go flat to content, not to post meta
Version:     0.1
Author:      Joe Bacal
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

function add_makapost(){
  add_action( 'ninja_forms_post_process', 'maka_function' );
}
add_action( 'init', 'add_makapost' );

function maka_function(){

  global $ninja_forms_processing;

  $all_fields = $ninja_forms_processing->get_all_fields();

  $content_as_array = [];
  if( is_array( $all_fields ) ){
    foreach( $all_fields as $field_id => $user_value ){
      $field_title = $ninja_forms_processing->data['field_data'][$field_id]['data']['label'];
      $chunk = '<br><b>'. $field_title . ':</b><br>' . $user_value . '<br>';
      if ( $field_title != 'Submit' ) {
        array_push($content_as_array, $chunk);
      }
    }

    // Gather post data.
    $maka_content = implode('<br>', $content_as_array);
    $form_title_string = $ninja_forms_processing->data['form']['form_title'];
    $datestring = date('m j y');

    $new_post = array(
        'post_title'    => $form_title_string . ' submission on ' . $datestring,
        'post_content'  => $maka_content,
        'post_status'   => 'private'
    );

    wp_insert_post( $new_post );
  }

  //die();
}
