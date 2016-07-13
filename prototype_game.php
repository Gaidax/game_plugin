<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * @package Prototype
 * @version 0.01
 */
/*
Plugin Name: MyPrototype
Plugin URI: not-yet.com
Description: Testing plugin
Author: Oleg Olegov
Version: Alpha
Author URI: not-yet.com
*/

//include( plugin_dir_path( __FILE__ ) . '/ChromePhp.php'); \\chrome logger

//register_activation_hook( __FILE__, array( 'Increase_Uploads_Max', 'deactivate' ) ); \\--never works
//register_deactivation_hook(__FILE__, array( 'Increase_Uploads_Max', 'deactivate' ) ); \\--never works

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