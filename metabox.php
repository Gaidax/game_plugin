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
	$diag = '<div id = "diag_place"></div>';
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
		$files = explode("\n", trim(`find -L $attached`));
		$files = str_replace("/var/www/html", get_site_url(), $files);
		add_post_meta($id, 'attached_files', $files);
		update_post_meta($id, 'attached_files', $files);
		if($_POST["attach_dir"] !='None') {
		add_post_meta($id, 'upload_dir', wp_upload_dir()["url"] . get_folder($attached));
		update_post_meta($id, 'upload_dir', wp_upload_dir()["url"] . get_folder($attached));
	}
}

function get_folder($path) {
	$folder = explode('/', $path);
	$last = last($folder);
	return '/'.$folder[$last];
}

function last($array) { 
	if (!is_array($array)) return $array; 
	if (!count($array)) return null; 
	end($array); 
	return key($array); 
} 

function move_it($name, $temp_name) {
	$path_array  = wp_upload_dir();
		$path = str_replace('\\', '/', $path_array['path']);
		$fold = sort_src($temp_name);
		$new_name = $path. $fold;
		wp_mkdir_p($new_name);
		if(!move_uploaded_file($temp_name, $new_name . $name)) {
			$upload['error'] = sprintf( __( 'Could not write file %s' ), $name );
		} else {
			$upload['url'] = $path_array['url'] . $fold . $name;
			$upload['file'] = $new_name . $name;
		}
		return $upload;
}

function upload_file_meta( $id ) {

	$upload = array();
	foreach ($_FILES as $file) {

		for($i=0; $i <= count($file['name']); $i++) 
		{       	
			$name = $file['name'][$i];
			$temp_name = $file['tmp_name'][$i];
			$upload[] = move_it($name, $temp_name);     	
		}

	}
	foreach ($upload as $uploaded)
	{
		if (strpos( $uploaded['url'], 'game.js')) {
			link_images($uploaded['file']);
		}
	}

		add_post_meta($id, 'uploaded_files', $upload);
		update_post_meta($id, 'uploaded_files', $upload);
		add_post_meta($id, 'upload_dir', wp_upload_dir()["url"]);
		update_post_meta($id, 'upload_dir', wp_upload_dir()["url"]);
}

function link_images($file) {
	$contents = file_get_contents($file);
	$plug_dir =  wp_upload_dir();
	$contents = str_replace("../..", $plug_dir['url'], $contents);
	file_put_contents($file, $contents);
}

function sort_src($file) {
	$contents = file_get_contents($file);
	if(strpos( $contents, 'function (config) {')) {
		return "/config/";
	} elseif(strpos( $contents, 'function (objects) {')) {
		return "/objects/";
	} elseif(strpos( $contents, 'function (core) {')) {
		return "/core/";
	} elseif(strpos( $contents, 'function (question_scenes) {')) {
		return "/scenes/";
	} elseif(@is_array(getimagesize($file))) {
		return "/Assets/images/";
	} else {
		return "/";
	}
}


function upload_to_plugin_dir( $dir ) {
	if(!isset($_POST['folder'])) {
		return $dir;
	}

	$custom_name = sanitize_text_field($_POST['folder']);
	$dir_n = "uploaded_games/".$custom_name;
	$plug_p = plugin_dir_path(__FILE__) . $dir_n;
	$plug_u = plugin_dir_url(__FILE__) . $dir_n;
	//if(isset($_POST['post_id'])) {
	$id = $_POST['post_id'];

	$parent = get_post( $id )->post_parent;

		if( "page" == get_post_type( $id ) || "page" == get_post_type( $parent ) ) {

			$dir['path'] = $plug_p;
			$dir['url']  = $plug_u;
			$dir['basedir'] = $plug_p;
			$dir['baseurl'] = $plug_u;
	}
//}
	return $dir;
}


function update_edit_form() {
	echo ' enctype="multipart/form-data"';
}


function delete_fold() {
	$success=null;	
	if(isset($_GET["deletion"])) {	
		$to_del = $_GET["deletion"];
		$ps = delTree(plugin_dir_path(__FILE__)."/uploaded_games/".$to_del);
		if($ps) {
			$success = $ps;
		} else if(!$ps) {
			$success = $ps;
		}
		return $success;
	}
	return $success;
}

function delTree($dir) {
	if(opendir($dir)){ 
	$files = array_diff(scandir($dir), array('.','..')); 
	foreach ($files as $file) { 
		(is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file"); 
	} 
	return rmdir($dir);
	} else {
		return true;
	}
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
