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
		$title = $desc = $page_to_link = $link_text = "";
		if (isset($instance['title'])) :
			$title = esc_attr($instance['title']);
		endif;
		if (isset($instance['desc'])) :
			$desc = esc_attr($instance['desc']);
		endif;
		if (isset($instance['page_to_link'])) :
			$page_to_link = esc_attr($instance['page_to_link']);
		endif;
		if (isset($instance['link_text'])) :
			$link_text = esc_attr($instance['link_text']);
		endif;

		echo '<p><label for="' . $this->get_field_id('title') . '">Title:</label> <input class="widefat" id="' . $this->get_field_id('title') . '" name="'. $this->get_field_name('title') .'" type="text" value="'. $title. '" /></p>';

		echo '<p><label for="' . $this->get_field_id('desc') . '">Description:</label> <textarea class="widefat" id="' . $this->get_field_id('desc') . '" name="'. $this->get_field_name('desc') .'" type="text">'. $desc. '</textarea></p>';

		echo '<p><label for="'. $this->get_field_id('page_to_link').'"Page to link to:</label><select class="widefat" id="'. $this->get_field_id('page_to_link').'" name="'. $this->get_field_name('page_to_link') . '">';
		
		echo '<option value="-1">No Link</option>';

		// Generate a dropdown filled with pages in the site to link to.
		$tgt_pages_array = get_pages(); // Get all the pages

		foreach ($tgt_pages_array as $object) : // Loop through each page object
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

		echo '<p><label for="' . $this->get_field_id('link_text') . '">Link Text:</label> <input class="widefat" id="' . $this->get_field_id('link_text') . '" name="'. $this->get_field_name('link_text') .'" type="link_text" value="'. $link_text. '" /></p>';

		echo '<p>All fields are optional. The widget will figure it out. Play with it and see for yourself!</p>';

	}
function update($new_instance, $old_instance) {
		// processes widget options to be saved
		return $new_instance;
	}
function widget($args, $instance) {
		// outputs the content of the widget
		$title = apply_filters( 'widget_title', $instance['title'] );
		$desc = apply_filters( 'widget_text', $instance['desc'] );
		$page_to_link = apply_filters( 'widget_text', $instance['page_to_link'] );
		$link_text = apply_filters( 'widget_text', $instance['link_text'] );
		$space_adjust = "";

		if ($link_text == "") :
			$link_text = "Visit the ". get_the_title($page_to_link) ." page to learn more&nbsp;&raquo;";
		endif;

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
		echo $args['before_title'] . $title . $args['after_title'];
		 
		// This is where you run the code and display the output
		if ( ! empty( $desc ) ) :
			if ( empty( $title) ) :
				$space_adjust = " space-adjust";
			endif;

			echo '<p class="widget-text'.$space_adjust.'">' . $desc . '</p>';
		endif;

		if ($page_to_link != -1) :
			if ( empty( $title) && empty( $desc ) ) :
				$space_adjust = " space-adjust";
			endif;

			echo '<p class="widget-link'.$space_adjust.'"><a href="'. get_permalink( $page_to_link ) .'">'. $link_text .'</a></p>';
		endif;

		echo $args['after_widget'];
		
	}
}

add_action('widgets_init', create_function('', 'return register_widget("Signup_Widget");'));
//register_widget('Signup_Widget');

?>