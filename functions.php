<?php


//add_action('admin_notices', 'log_it');
add_action('media_buttons', 'prototype_button', 15);
add_action('wp_enqueue_media', 'choose_script');
add_shortcode( 'game', 'insertion_func' );


/*function add_script_to_page() {

	$script_meta = get_post_meta(get_the_ID(), 'wp_attached_file', true);
	wp_enqueue_script('testscript', $script_meta['url'], false);	

}*/


function choose_script() {
	wp_enqueue_script('media_button', plugin_dir_url(__FILE__) . 'js/choose_game.js', array('jquery'), '1.0', true);
}

function log_it() {
	$current_page = get_current_screen();
	echo $current_page->id;
}

function prototype_button() {
	if (is_post_edit())
		echo '<a href="#" id="insert-my-game" class="button">Add my prototype</a>';
}

function insertion_func( $atts, $content = null ){		
	return handlefile($content);
}

function is_post_edit() {
	$current_page = get_current_screen();
	if ($current_page->id === 'post')
		return true;
	return false;
}

function handlefile($id) {
	$path = get_attached_file( $id );
	$file_cont = file_get_contents($path);
	echo "<p id = 'script_place'></p>";
	echo"<script type='text/javascript'>" . $file_cont . "</script>";
}

?>