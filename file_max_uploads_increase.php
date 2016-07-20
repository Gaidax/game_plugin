<?php
defined( 'ABSPATH' ) OR exit;

$Increase_Uploads_Max = Increase_Uploads_Max::get_instance();

$Increase_Uploads_Max::activate();

class Increase_Uploads_Max {

	private static $instance = null;
		private static $conf_file = 'php.ini';
		private static $uploads_str = 'max_file_uploads = 150';
		private $max_ups;
		//$conf_file = '.htaccess'; //sometimes it needs to be .htaccess file
		private $site_root;
		private $ini_file;
		

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function __construct() {
		global $site_root; $site_root = $_SERVER["DOCUMENT_ROOT"];
		global $ini_file; $ini_file = $site_root . '/'. self::$conf_file;

	}

	public static function activate() {
		//add_action('admin_notices', 'show_it');
		global $max_ups;
		$max_ups = '151';
		self::change_uploads();
	}

	public static function deactivate() {
		self::change_uploads();
	}

	public static function change_uploads() {
		global $ini_file;
		global $site_root;
		$ini = $ini_file;

			if ( file_exists( $ini ) ) {
				self::edit_ini();
	} else {
		$ini = php_ini_loaded_file();
		copy($ini, $site_root);
		self::edit_ini();
	}
}

public static function edit_ini() {
					global $ini_file;
					global $max_ups;

					$ini_contents = file_get_contents( $ini_file );
					$upload_max = $max_ups;

				if ( $ini_contents ) {

					if ( preg_match( '~max_file_uploads\s*=\s*.*~', $ini_contents ) ) {

						$updated_contents = preg_replace( '~(max_file_uploads\s*=\s*)(.*)~', "\${1}$upload_max", $ini_contents );
						$added_uploads = file_put_contents( $ini_file, $updated_contents, LOCK_EX );
						//system("sudo /etc/init.d/apache2 restart"); //might be a bad idea
						add_action('admin_notices', 'show_it');

					} else {

						$added_uploads = file_put_contents( $ini_file, self::$uploads_str, FILE_APPEND | LOCK_EX );
						 //system("sudo /etc/init.d/apache2 restart");
						 add_action('admin_notices', 'show_it');
					}

			} else {
		
				if (!$handlec = fopen($ini_file, 'a')) {
			         $error_msg = sprintf( __( 'Could not create file (%s), so no changes will be made. Please deactivate the plugin, and try again. If it still does not work after trying again, then this plugin may not be for you.', 'increase-upload-max-filesize' ), $ini_file );
				}
							
			    if (fwrite($handlec , self::$uploads_str) === FALSE) {
							       
			         $error_msg = sprintf( __( 'Cannot write to newly created file (%s), so no changes will be made. Please deactivate the plugin, and try again. If it still does not work after trying again, then ask your web host to grant you access to write to your php.ini file.', 'increase-upload-max-filesize' ), $ini_file );
						
			    }
			    fclose($handlec);
					
			}

}
}


function show_it() {

	echo ini_get('max_file_uploads');
}

?>