<?php

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

define( 'PLUGIN_DIR', dirname(__FILE__).'/' );  
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
include( plugin_dir_path( __FILE__ ) . '/functions.php');
include( plugin_dir_path( __FILE__ ) . '/metabox.php');
include( plugin_dir_path( __FILE__ ) . '/templater.php');
//include( plugin_dir_path( __FILE__ ) . '/file_max_uploads_increase.php');
include( plugin_dir_path( __FILE__ ) . '/ChromePhp.php');

ini_set('max_file_uploads', '150');

?>