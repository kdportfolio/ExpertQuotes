<?php
/**
 * Plugin Name: Expert Quote
 * Plugin URI: http://b2c4ojr5.myraidbox.de/expert_quote
 * Description: 
 * Version: 1.0
 * Author: Gerne Gesund
 * Author URI: http://b2c4ojr5.myraidbox.de/
 *
 */

/* 
* If this file is called directly or plugin is already defined, abort. 
*/
if (!defined('WPINC')) {
	die;
}

define('PLUGIN_DIR' , plugin_dir_url( __FILE__ ));

include_once( 'includes/class_expert_quotes.php' );
$expert_quotes = new expert_quotes();
