<?php
/**
 * Plugin Name: Filelist
 * Plugin URI: http://virally.de/
 * Description: Elementor plugin to generate a file list from an existing folder and rendering `.index.html` files as headers.
 * Version: 1.0.0
 * Author: Nabil Redmann, Virally.de
 * Author URI: http://virally.de
 */

// Exit if accessed directly
defined('ABSPATH') or die('Can\'t do.');

if( !defined( 'BFL_DIR' ) ) {
	define( 'BFL_DIR', dirname( __FILE__ ) ); // plugin dir
}
if( !defined( 'BFL_URL' ) ) {
	define( 'BFL_URL', plugin_dir_url( __FILE__ ) ); // plugin url
}
if( !defined( 'BFL_BASENAME') ) {
	define( 'BFL_BASENAME', 'bananaacid-fileslist' ); // plugin base name
}
if( !defined( 'BFL_ADMIN' ) ) {
	define( 'BFL_ADMIN', BFL_DIR . '/inc/admin' ); // plugin admin dir
}


class BFL
{
    function __construct()
    {
        add_action( 'elementor/widgets/register', [$this, 'register_widget'] );
    }

    function register_widget($widgets_manager)
	{
        require_once(BFL_DIR . '/inc/bfl.elementor.class.php');

		$widgets_manager->register( new \BFL_Elementor() );
	}
}

new BFL();