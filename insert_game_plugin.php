<?php

/*
   Plugin Name: HTML5GameInsert
   Plugin URI: http://wordpress.org/extend/plugins/html5gameinsert/
   Version: 0.1
   Author: Vasyl Milchevskyi
   Description: Add a specific HTML5 based game on your page. (Has to be create.js game)
   Text Domain: html5gameinsert
   License: GPLv3
  */


//include( plugin_dir_path( __FILE__ ) . '/ChromePhp.php'); \\chrome logger

//register_activation_hook( __FILE__, array( 'Increase_Uploads_Max', 'deactivate' ) ); \\--never works
//register_deactivation_hook(__FILE__, array( 'Increase_Uploads_Max', 'deactivate' ) ); \\--never works

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
add_action( 'plugins_loaded', array('Plugin_Init', 'init'));

class Plugin_Init
{
	protected static $instance;
	public static function init() {
		is_null( self::$instance ) AND self::$instance = new self;
        return self::$instance;
	}

	public function __construct()
    {
        add_action( current_filter(), array( $this, 'load_files' ), 30 );
    }

    public function load_files()
    {
        foreach ( glob( plugin_dir_path( __FILE__ ).'/*.php' ) as $file 
  )          include_once $file;
    }
}
?>