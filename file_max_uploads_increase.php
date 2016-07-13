<?php
defined( 'ABSPATH' ) OR exit;

$Increase_Uploads_Max = Increase_Uploads_Max::get_instance();

$Increase_Uploads_Max::activate();

class Increase_Uploads_Max {

	private static $instance = null;

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public static function activate() {
		add_action('admin_notices', 'show_it');
		//self::increase_uploads('150');
	}

	public static function deactivate() {
		self::increase_uploads('20');
	}

	public static function increase_uploads($upload_max) {
		$conf_file = 'php.ini';
		//$conf_file = '.htaccess';
		$ini_file = $_SERVER["DOCUMENT_ROOT"] . '/'. $conf_file;
		$uploads = 'max_file_uploads = 150';

			if ( file_exists( $ini_file ) ) {


				$ini_contents = file_get_contents( $ini_file );

				if ( $ini_contents ) {

					if ( preg_match( '~max_file_uploads\s*=\s*.*~', $ini_contents ) ) {

						$updated_contents = preg_replace( '~(max_file_uploads\s*=\s*)(.*)~', "\${1}$upload_max", $ini_contents );
						$added_uploads = file_put_contents( $ini_file, $updated_contents, LOCK_EX );
						//system('/etc/init.d/apache2 restart');
						//system("sudo /etc/init.d/apache2 restart");
						add_action('admin_notices', 'show_it');

					} else {

						$added_uploads = file_put_contents( $ini_file, $uploads, FILE_APPEND | LOCK_EX );
						 //system('/etc/init.d/apache2 restart');
						 //system("sudo /etc/init.d/apache2 restart");
						 add_action('admin_notices', 'show_it');
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
			//system('/etc/init.d/apache2 restart');
	} 
}
}


function show_it() {

	echo ini_get('max_file_uploads');
}

?>