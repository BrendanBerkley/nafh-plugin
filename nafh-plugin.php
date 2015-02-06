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
		if (isset($instance['title'])) :
			$title = esc_attr($instance['title']);
		endif;
		if (isset($instance['subtitle'])) :
			$subtitle = esc_attr($instance['subtitle']);
		endif;
		if (isset($instance['desc'])) :
			$desc = esc_attr($instance['desc']);
		endif;
		if (isset($instance['page_to_link'])) :
			$page_to_link = esc_attr($instance['page_to_link']);
		endif;
		if (isset($instance['button_text'])) :
			$button_text = esc_attr($instance['button_text']);
		endif;

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
		foreach ($button_labels as $button_label) : 
			$checked = "";

			if ($button_label == $button_text) :
				$checked = " checked";
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
		if ( ! empty( $title ) )
		echo $args['before_title'] . $title . $args['after_title'];
		
		 
		// This is where you run the code and display the output
		echo '<div class="signup-section-subtitle">' . $subtitle . '</div>';
		echo '<p class="signup-section-desc">' . $desc . '</p>';

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

?>