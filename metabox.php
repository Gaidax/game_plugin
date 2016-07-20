<?php

add_action('add_meta_boxes', 'delete_fold');
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
	$diag = '<div id = "diag_place"/>';
	wp_nonce_field(plugin_basename(__FILE__), 'wp_attached_folder_nonce');

	$html = 'Name the folder for a game: <input type="text" id="folder" name="folder" maxlength="15"/>';
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
					$html .=  '<input type ="radio" id="'.$file.'" name = "attach_dir" value = "'.$base."/".$file .'"><label for="'.$file.'">'.$file.'</label>';
					$html .= '<input class="selector" id = "'.$file.'" type="button" value = "delete"/><br>';
				}				
			}
			closedir($dir);					
		}
	}
	$html .= '<br><input type ="radio" id="None" name = "attach_dir" value = "None" checked/><label for="None">None</label>  <br>';
	$html .= '<br>';
	$html .= '<p class="description">';
	$html .= 'Attach a folder to the page here.';
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
		}

	}
}


function attach_existing($id) {
	$attached = $_POST["attach_dir"];
	$fold = get_folder($attached);
	add_post_meta($id, 'att',  $fold);
	$file_num = 0;
	$arr = array();

	if( is_dir($attached) ) {
		if($dir = opendir($attached)) {
			while (($file = readdir($dir))!== false) {
				if(strpos( $file, '.js' )) {
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
	$last = last($folder);
	return $folder[$last-1].'/'.$folder[$last].'/';
}

function last($array) { 
	if (!is_array($array)) return $array; 
	if (!count($array)) return null; 
	end($array); 
	return key($array); 
} 


function upload_file_meta( $id ) {

	//$file = $_FILES['wp_upl_dir'];
	$upload = array();
	foreach ($_FILES as $file) {

		for($i=0; $i <= count($file['name']); $i++) 
		{       	
			$upload[] = wp_upload_bits($file['name'][$i], null, file_get_contents($file['tmp_name'][$i]));     	
		}

	}


	$file_num = 0;

	foreach ($upload as $uploaded)
	{
		$file_url = $uploaded['url'];

		if( isset($uploaded['error']) && $uploaded['error'] != 0 ) {
			wp_die('There was an error uploading your files. The error is: ' . $uploaded['error']); 
		}
		if( strpos( $file_url, '.js' ) ) {

			if (strpos( $file_url, 'game.js')) {
				link_images($uploaded['file']);
			}

			add_post_meta($id, 'attached_file'.$file_num, $file_url);
			update_post_meta($id, 'attached_file'.$file_num, $file_url);
			$file_num++;
		}
	}
	add_post_meta($id, 'files_uploaded', $file_num);
	update_post_meta($id, 'files_uploaded', $file_num);
}


function link_images($file) {
	$contents = file_get_contents($file);
	$plug_dir =  wp_upload_dir();
	$contents = str_replace("****", $plug_dir['url'], $contents);
	file_put_contents($file, $contents);
}


function upload_to_plugin_dir( $dir ) {
	if(!isset($_POST['folder'])) {
		return null;
	}

	$custom_name = sanitize_text_field($_POST['folder']);
	$dir_n = "uploaded_games/".$custom_name;
	$plug_p = plugin_dir_path(__FILE__) . $dir_n;
	$plug_u = plugin_dir_url(__FILE__) . $dir_n;

	$id = $_POST['post_id'];
	$parent = get_post( $id )->post_parent;
	//if(isset($_POST['folder'])) {

		if( "page" == get_post_type( $id ) || "page" == get_post_type( $parent ) ) {

			$dir['path'] = $plug_p;
			$dir['url']  = $plug_u;
			$dir['basedir'] = $plug_p;
			$dir['baseurl'] = $plug_u;
		//}
	}
	return $dir;
}


function update_edit_form() {
	echo ' enctype="multipart/form-data"';
}


function delete_fold() {
	$success = 0;	
	if(isset($_GET["deletion"])) {	
		$to_del = $_GET["deletion"];
		if(delTree(plugin_dir_path(__FILE__)."/uploaded_games/".$to_del)) {
			$success = 1;
		} else {
			$success = 2;
		}
		return $success;
	}
	return $success;
}


function delTree($dir) { 
	$files = array_diff(scandir($dir), array('.','..')); 
	foreach ($files as $file) { 
		(is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file"); 
	} 
	return rmdir($dir); 
} 


function message_script() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script("jquery-effects-core");
	wp_enqueue_script('jquery-ui-dialog');
	wp_enqueue_style("wp-jquery-ui-dialog");
	wp_enqueue_style('button_style', plugin_dir_url(__FILE__) . 'css/main.css');
	wp_enqueue_script('message_script', plugin_dir_url(__FILE__) . 'js/template_message.js');
	wp_enqueue_script('deletion_message', plugin_dir_url(__FILE__) . 'js/delete_game.js');
	wp_localize_script( 'deletion_message', 'message',
			array( 'success_state' => delete_fold() ) );
/*		wp_localize_script( 'deletion_script', 'ajax_object',
array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'to_delete' => 1234 ) );*/

}

?>
