<?php
/*
Plugin Name: NAFH Plugin
Description: This contains customized stuff that isn't visually related. Things where, if you swap themes, you wouldn't want functionality to go away.
Version: 0.1
License: GPL
Author: Brendan Berkley
Author URI: http://www.brendanberkley.com
*/

class Signup_Widget extends WP_Widget {
	function Signup_Widget() {
		parent::WP_Widget(false, 'Signup');
	}
function form($instance) {
		// outputs the options form on admin
		$title = $subtitle = $desc = $page_to_link = $button_text = "";
		if (isset($instance['title']))
			$title = esc_attr($instance['title']);
		if (isset($instance['subtitle']))
			$subtitle = esc_attr($instance['subtitle']);
		if (isset($instance['desc']))
			$desc = esc_attr($instance['desc']);
		if (isset($instance['page_to_link']))
			$page_to_link = esc_attr($instance['page_to_link']);
		if (isset($instance['button_text']))
			$button_text = esc_attr($instance['button_text']);

		echo '<p><label for="' . $this->get_field_id('title') . '">Title:</label> <input class="widefat" id="' . $this->get_field_id('title') . '" name="'. $this->get_field_name('title') .'" type="text" value="'. $title. '" /></p>';

		echo '<p><label for="' . $this->get_field_id('subtitle') . '">Subtitle:</label> <input class="widefat" id="' . $this->get_field_id('subtitle') . '" name="'. $this->get_field_name('subtitle') .'" type="text" value="'. $subtitle. '" /></p>';

		echo '<p><label for="' . $this->get_field_id('desc') . '">Description:</label> <textarea class="widefat" id="' . $this->get_field_id('desc') . '" name="'. $this->get_field_name('desc') .'" type="text">'. $desc. '</textarea></p>';

		echo '<p><label for="'. $this->get_field_id('page_to_link').'">Registration page to link to:</label><select class="widefat" id="'. $this->get_field_id('page_to_link').'" name="'. $this->get_field_name('page_to_link') . '">';
		
		echo '<option value="-1">No Link</option>';

		// Generate a dropdown filled with pages in the site to link to.
		$site_pages_array = get_pages(); // Get all the pages

		foreach ($site_pages_array as $object) : // Loop through each page object
			$post_title = ""; // Reset variables
			$post_id    = "";
			$selected_text = "";

			foreach ($object as $key => $value) : // Loop through each page's parameters

				if ($key == "ID") : // Get the ID
					$post_id = $value;
				endif;

				if ($key == "post_title") : // Get the page title
					$post_title = $value;
				endif;

			endforeach;

			if ($post_id == $page_to_link) : // Figure out which page is already selected
				$selected_text = ' selected="selected"';
			endif;

			if ($post_id != "" && $post_title != "") : // Build the dropdown item.
				echo '<option value="' . $post_id . '"' . $selected_text . '>' . $post_title . '</option>';
			endif;
		endforeach;

		echo '</select></p>';


		// Radio buttons for button text options
		$button_labels = array("Sign Up Soon!", "Sign Up Now!", "Signup Closed");

		echo '<p><label for="' . $this->get_field_id('button_text') . '">Button Text:</label><br />';
		
		// Create radio buttons in a loop, for DRYness and to handle the 'checked' option better
		$first_button_label = $button_labels[0];

		foreach ($button_labels as $button_label) : 
			$checked = "";

			// If current value = saved value OR if no saved value and we're in the first iteration,
			if ($button_label == $button_text || ($button_text == "" && $first_button_label == $button_label)) : 
				$checked = " checked"; // set the next input to be checked.
			endif;

			echo '<input type="radio" id="' . $this->get_field_id('button_text') . '" name="'. $this->get_field_name('button_text') .'" value="' . $button_label . '"'. $checked .' /> ' . $button_label . '<br />';
		endforeach;

	}
function update($new_instance, $old_instance) {
		// processes widget options to be saved
		return $new_instance;
	}
function widget($args, $instance) {
		// outputs the content of the widget
		$title = apply_filters( 'widget_title', $instance['title'] );
		$subtitle = apply_filters( 'widget_text', $instance['subtitle'] );
		$desc = apply_filters( 'widget_text', $instance['desc'] );
		$page_to_link = apply_filters( 'widget_text', $instance['page_to_link'] );
		$button_text = apply_filters( 'widget_text', $instance['button_text'] );

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];

		if ( ! empty( $title ) ) :
			// These next three lines find the last space and replace it with a
			// non-breaking space, if there is more than one space. Type matters!
			if (substr_count($title, " ") > 1) :
				$last_space_pos = strripos($title, " ");
				$end_of_title   = substr($title, $last_space_pos+1);
				$better_title   = substr_replace($title, "&nbsp;".$end_of_title, $last_space_pos);
			else:
				$better_title   = $title;
			endif;

			echo $args['before_title'] . $better_title . $args['after_title'];
		endif;
		
		if ( ! empty( $subtitle ) )
		echo '<h3 class="signup-section-subtitle">' . $subtitle . '</h3>';
		
		if ( ! empty( $desc ) )
		echo '<div class="signup-section-desc">' . $desc . '</div>';

		// Output button with proper class and href
		$button_state = " disabled";
		$button_href  = " javascript:void(0);";

		if ($button_text == "Sign Up Now!") :
			$button_state = " enabled";
			$button_href  = get_permalink( $page_to_link );
		endif;

		echo '<a class="signup-section-button' . $button_state . '" href="'. $button_href .'">'. $button_text .'</a>';

		echo $args['after_widget'];
		
	}
}

add_action('widgets_init', create_function('', 'return register_widget("Signup_Widget");'));
//register_widget('Signup_Widget');









// I want PayPal buttons to be manageable from admin. My old workflow was to copy and paste button text 
// into functions.php, creating a shortcode that I could then plug in to a post. I'd like to keep it simple - 
// set up the buttons in PayPal still, but not have to edit a PHP file to make 'em work here.
// add_action( 'init', 'create_post_type' );
// function create_post_type() {
//   register_post_type( 'ppbs_generator',
//     array(
//       'labels' => array(
//         'name' => __( 'PayPal Buttons' ),
//         'singular_name' => __( 'PayPal Button' ),
//         'name_admin_bar' => __( 'PayPal Button' ),
//         'add_new' => __( 'Add New Button' ),
//         'add_new_item' => __( 'Add New PayPal Button' ),
//         'edit_item' => __( 'Edit PayPal Button' ),
//         'view_item' => __( 'View PayPal Buttons' ),
//         'not_found' => __( 'No buttons found.' ),
//         'new_item' => __( 'New PayPal Button' )
//       ),
//       'public' => true,
//       'has_archive' => false,
//       'exclude_from_search' => false,
//       'publicly_queryable' => false,
//       'show_in_nav_menus' => false,
//       'menu_icon' => 'dashicons-cart',
//       'supports' => array(
//       	  'title'
//       	)
//     )
//   );
// }



// // function ppbs_generator_meta_box_setup() {
// add_action( 'add_meta_boxes', 'ppbs_generator_box' );
// function ppbs_generator_box() {
// 	add_meta_box( 
// 		'ppbs_generator_box',
// 		'Button Info',
// 		'ppbs_generator_box_content',
// 		'ppbs_generator',
// 		'normal',
// 		'high'
// 	);
// }
// // }

// function ppbs_generator_box_content( $post ) {
// 	wp_nonce_field( plugin_basename( __FILE__ ), 'ppbs_generator_box_content_nonce' );
// 	echo '<p>';
// 	echo '<label for="ppbs_shortcode_name">Shortcode name: </label>';
// 	echo '<input type="text" id="ppbs_shortcode_name" name="ppbs_shortcode_name" placeholder="use-hyphens-not-spaces" class="widefat" />';
// 	echo '</p>';
// 	echo '<p>';
// 	echo '<label for="ppbs_button_text">Button HTML: </label>';
// 	echo '<textarea id="ppbs_button_text" name="ppbs_button_text" class="widefat" ></textarea>';
// 	echo '</p>';
// }

// add_action( 'save_post', 'ppbs_generator_box_save' );
// function ppbs_generator_box_save( $post_id ) {

//   if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
//   return;

//   if ( !wp_verify_nonce( $_POST['ppbs_generator_box_content_nonce'], plugin_basename( __FILE__ ) ) )
//   return;

//   if ( 'page' == $_POST['post_type'] ) {
//     if ( !current_user_can( 'edit_page', $post_id ) )
//     return;
//   } else {
//     if ( !current_user_can( 'edit_post', $post_id ) )
//     return;
//   }
//   $ppbs_shortcode_name = $_POST['ppbs_shortcode_name'];
//   update_post_meta( $post_id, 'ppbs_shortcode_name', $ppbs_shortcode_name );
  
//   $ppbs_button_text = $_POST['ppbs_button_text'];
//   update_post_meta( $post_id, 'ppbs_button_text', $ppbs_button_text );

// }
?>