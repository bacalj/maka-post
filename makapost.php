<?php
/*
Plugin Name: Maka Post
Description: Creates a private post for every Ninja Forms submission - all fields go flat to content, not to post meta
Version:     0.1
Author:      Joe Bacal
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

/*
  Make a new private post on Ninja Form submission
  Write everything into the content of the post
*/

function add_makapost(){
  add_action( 'ninja_forms_post_process', 'maka_function' );
}
add_action( 'init', 'add_makapost' );

function maka_function(){

  global $ninja_forms_processing;

  $all_fields = $ninja_forms_processing->get_all_fields();

  //pull all the fields as keystring -> valuestring into an array
  $content_as_array = [];
  if( is_array( $all_fields ) ){
    foreach( $all_fields as $field_id => $user_value ){
      $field_title = $ninja_forms_processing->data['field_data'][$field_id]['data']['label'];
      $chunk = '<br><b>'. $field_title . ':</b><br>' . $user_value . '<br>';
      if ( $field_title != 'Submit' ) {
        array_push($content_as_array, $chunk);
      }
    }

    // Smoosh array into content and add a title for the post
    $maka_content = implode('<br>', $content_as_array);
    $form_title_string = $ninja_forms_processing->data['form']['form_title'];
    $datestring = date('m j y');

    // set up and create the post
    $new_post = array(
        'post_title'    => $form_title_string . ' submission | ' . $datestring,
        'post_content'  => $maka_content,
        'post_status'   => 'private'
    );

    wp_insert_post( $new_post );
  }
}

/*
  Create a settings page to turn on and off the functionality
  Thanks, http://wpsettingsapi.jeroensormani.com/
*/


//create the options page
function makapost_custom_admin_menu() {
    add_options_page(
      'MakaPost',             //page title
      'MakaPost Options',     //menu title
      'manage_options',       //capabitlity
      'makapost-plugin',      //menu slug
      'makapost_options_page' //callback function
    );

    add_settings_field(
      'makapost_toggle', //id
    	'Toggle MakaPost functionality', //title
    	'makapost_options_page', //callback
    	'makapost-plugin' //page
    );
}

function makapost_options_page() {
    ?>
    <div class="wrap">
        <h2>MakaPost Options</h2>
        <form action="options.php" method="post">
          <?php settings_fields('makapost_options'); ?>
          <?php do_settings_sections('makapost-plugin'); ?>
          <input class="button-primary" name="Submit" type="submit" value="<?php esc_attr_e('Save'); ?>" />
        </form>
    </div>
    <?php
}
add_action( 'admin_menu', 'makapost_custom_admin_menu' );

//register the settings field
function register_and_build_fields() {
  register_setting('makapost_options', 'makapost_toggle', 'validate_setting');
}
add_action('admin_init', 'register_and_build_fields');
