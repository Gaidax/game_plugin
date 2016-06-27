<?php

/**
 * @package Prototype
 * @version 0.01
 */

add_action('add_meta_boxes', 'upload_metabox');
add_action('add_meta_boxes', 'message_script');
add_action('save_post', 'verify_and_upload');
add_action('post_edit_form_tag', 'update_edit_form');
add_filter( 'upload_dir', 'upload_to_plugin_dir' );


function upload_metabox() {

	add_meta_box(
		'wp_attach_folder',
		'Upload and attach folder',
		'wp_attach_dir',
		'page',
		'side'
		);

	add_meta_box(
		'wp_attach_ex_folder',
		'Attach existing folder',
		'wp_attach_ex_dir',
		'page',
		'side'
		);
}


function wp_attach_dir() {
	$diag = '<div id="dialog" title="Be weary">You have to choose game template for this to work</div>';
	wp_nonce_field(plugin_basename(__FILE__), 'wp_attached_folder_nonce');

	$html = 'Name the folder for a game: <input type="text" id="folder" name="folder" maxlength="5"/>';
	$html .= '<input type="file" id="wp_upl_dir" name="wp_upl_dir[]" size="25" webkitdirectory directory  multiple/>';
	$html .= '<p class="description">';
	$html .= 'Upload your folder here.';
	$html .= '</p>';
	$html .= $diag;

	echo $html;
}


function wp_attach_ex_dir() {

	wp_nonce_field(plugin_basename(__FILE__), 'wp_attached_ex_folder_nonce');
	$base = plugin_dir_path(__FILE__).'uploaded_games';

	$html = 'Attach existing game to the page';
	$html .= '<br>';
	$html .= "Folders: ";
	$html .= "<br><br>";
	if( is_dir($base) ) {
		if($dir = opendir($base)) {
			while(($file = readdir($dir))!== false) {
				if(strncmp( $file, '.', strlen( '.' ) )) {
					$html .=  '<input type ="radio" name = "attach_dir" value = "'.$base."/".$file .'"/>' .$file . "<br>";
				}				
			}
			closedir($dir);					
		}
	}
	$html .= '<input type ="radio" name = "attach_dir" value = "None" checked/>None<br>';
	$html .= '<br>';
	$html .= '<p class="description">';
	$html .= 'Attach a folder to the page here.';
	$html .= $diag;
	$html .= '</p>';
	echo $html;
}


function verify_and_upload( $id ) {

	if(!wp_verify_nonce($_POST['wp_attached_folder_nonce'], plugin_basename(__FILE__))) {
		return $id;
	} 

	if(!wp_verify_nonce($_POST['wp_attached_ex_folder_nonce'], plugin_basename(__FILE__))) {
		return $id;
	} 

	if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $id;
	} 

	if('page' == $_POST['post_type']) {
		if(!current_user_can('edit_page', $id)) {
			return $id;
		} 
	} else {
		if(!current_user_can('edit_page', $id)) {
			return $id;
		}
	}

	if(!empty($_FILES['wp_upl_dir']) or $_POST['attach_dir'] != 'None') {
		

	if(strcmp(get_page_template_slug($id), 'game_template.php')==0) {
		upload_file_meta($id);
		attach_existing($id); 
	} else {
		add_action('admin_notices', 'not_game_template_notice');
	}
	
	}
}


function attach_existing($id) {
	$attached = $_POST['attach_dir'];
	$fold = get_folder($attached);
	$file_num = 0;
	$arr = array();

	if( is_dir($attached) ) {
		if($dir = opendir($attached)) {
			while (($file = readdir($dir))!== false) {
				if(strncmp( $file, '.', strlen( '.' ) )) {
					add_post_meta($id, 'attached_ex_file' .$file_num, plugin_dir_url(__FILE__) . $fold . $file);
					update_post_meta($id, 'attached_ex_file' .$file_num, plugin_dir_url(__FILE__). $fold . $file);
					$arr[] = $file;
					$file_num++;
				}
			}
			closedir($dir);		
			add_post_meta($id, 'files_atached_existing', $file_num);
			update_post_meta($id, 'files_atached_existing', $file_num);
		}
	}
}


function get_folder($path) {
	$folder = explode('/', $path);
	return $folder[7] .'/'. $folder[8] .'/';
}


function upload_file_meta( $id ) {

	//$file = $_FILES['wp_upl_dir'];
	$upload = array();

    //if(in_array($uploaded_type, $supported_types))  //CHECK TYPES? OR JUST UPLOAD EVERYTHING?
	foreach ($_FILES as $file) {
			
	for($i=0; $i <= count($file['name']); $i++) 
	{       	
		$upload[] = wp_upload_bits($file['name'][$i], null, file_get_contents($file['tmp_name'][$i]));     	
	}

	}


	$file_num = 0;

	foreach ($upload as $uploaded)
	{
		if( isset($uploaded['error']) && $uploaded['error'] != 0 ) {
			wp_die('There was an error uploading your files. The error is: ' . $uploaded['error']); 
		}
		add_post_meta($id, 'attached_file'.$file_num, $uploaded['url']);
		update_post_meta($id, 'attached_file'.$file_num, $uploaded['url']);
		$file_num++;
	}
	add_post_meta($id, 'files_uploaded', $file_num);
	update_post_meta($id, 'files_uploaded', $file_num);                                                                                            

}


function upload_to_plugin_dir( $dir ) {

	$custom_name = sanitize_text_field($_POST['folder']);
	$dir_n = "uploaded_games/".$custom_name;

	$id = $_POST['post_id'];
	$parent = get_post( $id )->post_parent;

	if( "page" == get_post_type( $id ) || "page" == get_post_type( $parent ) ) {

		$dir['path'] = plugin_dir_path(__FILE__) . $dir_n;
		$dir['url']  = plugin_dir_url(__FILE__) . $dir_n;
		$dir['basedir'] = plugin_dir_path(__FILE__) . $dir_n;
		$dir['baseurl'] = plugin_dir_url(__FILE__) . $dir_n;
	}
	return $dir;
}

function not_game_template_notice() {
    ?>
    <div class="error notice">
        <p><?php _e( 'There has been an error. Bummer!', 'my_plugin_textdomain' ); ?></p>
    </div>
    <?php
}

function update_edit_form() {
	echo ' enctype="multipart/form-data"';
}

function message_script() {
	wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script("jquery-effects-core");
    wp_enqueue_script('jquery-ui-dialog');
	wp_enqueue_style("wp-jquery-ui-dialog");
	wp_enqueue_script('message_script', plugin_dir_url(__FILE__) . 'js/template_message.js');
}

?>