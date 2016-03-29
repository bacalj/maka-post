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

  $maka = get_field('maka', 'option');;
  if ($maka == 'yes'){

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
}

//create the options page
if( function_exists('acf_add_options_page') ) {
	acf_add_options_page(array(
		'page_title' 	=> 'MakaPost Options',
		'menu_title'	=> 'Form Submission Options',
		'menu_slug' 	=> 'makapost-general-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
}

//acf field for option
if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array (
	'key' => 'group_56fad0824d0a8',
	'title' => 'Maka Post Options',
	'fields' => array (
		array (
			'key' => 'field_56fad0a1f3562',
			'label' => 'Turn submitted forms into posts',
			'name' => 'maka',
			'type' => 'radio',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array (
				'yes' => 'yes',
				'no' => 'no',
			),
			'other_choice' => 0,
			'save_other_choice' => 0,
			'default_value' => '',
			'layout' => 'horizontal',
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'makapost-general-settings',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => '',
));

endif;
