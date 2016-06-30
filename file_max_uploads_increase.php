<?php

class Increase_Uploads_Max {

	private static $instance = null;

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public static function activate() {
		self::increase_uploads("150\n");
	}

	public static function deactivate() {
		self::increase_uploads("20\n");
	}

	public static function increase_uploads($max) {
		$ini_file = basename(php_ini_loaded_file());
		$uploads = 'max_file_uploads = 150';
		$upload_max = $max;

			if ( file_exists( $ini_file ) ) {
			$ch = "YES";
				$ini_contents = file_get_contents( $ini_file );

				if ( $ini_contents ) {

					if ( preg_match( '~max_file_uploads\s*=\s*.*~', $ini_contents ) ) {

						$updated_contents = preg_replace( '~(max_file_uploads\s*=\s*)(.*)~', "\${1}$upload_max", $ini_contents );

						$added_uploads = file_put_contents( $ini_file, $updated_contents, LOCK_EX );

					} else {

						$added_uploads = file_put_contents( $ini_file, $uploads, FILE_APPEND | LOCK_EX );
					}

			} else {
		
				if (!$handlec = fopen($ini_file, 'a')) {
			         $error_msg = sprintf( __( 'Could not create file (%s), so no changes will be made. Please deactivate the plugin, and try again. If it still does not work after trying again, then this plugin may not be for you.', 'increase-upload-max-filesize' ), $ini_file );
				}
							
			    if (fwrite($handlec , $uploads) === FALSE) {
							       
			         $error_msg = sprintf( __( 'Cannot write to newly created file (%s), so no changes will be made. Please deactivate the plugin, and try again. If it still does not work after trying again, then ask your web host to grant you access to write to your php.ini file.', 'increase-upload-max-filesize' ), $ini_file );
						
			    }
			    fclose($handlec);
					
			}
	} 
}
}

$Increase_Uploads_Max = Increase_Uploads_Max::get_instance();
register_activation_hook(__FILE__, array( 'Increase_Uploads_Max', 'activate' ) );
register_deactivation_hook(__FILE__, array( 'Increase_Uploads_Max', 'deactivate' ) );
add_action('admin_notices', 'show_ini');

function show_ini() {
	$max_upl = ini_get('max_file_uploads');
	echo $max_upl;
	echo "<br>";
	echo php_ini_loaded_file();
}

?>