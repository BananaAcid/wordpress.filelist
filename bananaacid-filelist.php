<?php
/**
 * Plugin Name: File List
 * Plugin URI: https://github.com/BananaAcid/wordpress.filelist
 * Description: Elementor plugin to generate a file list from an existing folder and rendering `.index.html` files as headers.
 * Version: 1.0.2
 * Author: Nabil Redmann, Virally.de
 * Author URI: http://virally.de
 */

defined('ABSPATH') or die('Can\'t do.');


if (!defined('BFL_DIR')) {
     define ('BFL_DIR', dirname( __FILE__ )); // plugin dir
}
if (!defined('BFL_URL')) {
     define ('BFL_URL', plugin_dir_url( __FILE__ )); // plugin url
}
if (!defined('BFL_DISPLAYNAME')) {
     define ('BFL_DISPLAYNAME', 'File List' ); // plugin display name
}
if (!defined('BFL_BASENAME')) {
     define ('BFL_BASENAME', 'bananaacid-filelist'); // plugin base name for update
}
if (!defined('BFL_ADMIN')) {
     define ('BFL_ADMIN', BFL_DIR . '/inc/admin'); // plugin admin dir
}

if (!defined('BFL_UPDATE_URL')) {
    if (!isset($_GET['BFL_UPDATE_LOCAL']))
    { 
        define ('BFL_UPDATE_URL', 'https://raw.githubusercontent.com/BananaAcid/wordpress.filelist/main/info.json'); // plugins update info url
    }
    else
    {
        //TEST
        define ('BFL_UPDATE_URL', BFL_URL . '/info.json');
    }
}


require_once(BFL_DIR . '/inc/misha-update-checker.trait.php');



class BFL_init
{
    use mishaUpdateCheckerTrait;

    function __construct()
    {
        add_action( 'elementor/widgets/register', [$this, 'register_widget'] );

        add_action( 'plugins_loaded', [$this, 'check_dependencies'] );

        
        // update checker
        if (is_admin())
        {
            $plugin_data = get_file_data(__FILE__, array('Version' => 'Version'), false);
            $this->initUpdateCheck( $plugin_data['Version'], $plugin_basename, plugin_basename( __FILE__ ), plugin_basename( __DIR__ ), BFL_UPDATE_URL);
        }
    }

    function register_widget($widgets_manager)
    {
        require_once(BFL_DIR . '/inc/bfl.elementor.class.php');

        $widgets_manager->register( new \BFL_Elementor() );
    }


    function check_dependencies()
    {
        if (!did_action( 'elementor/loaded'))
        {
            add_action( 'admin_notices', [$this, 'admin_notice_missing_elementor_plugin'] );
        }
    }

    function admin_notice_missing_elementor_plugin()
    {

        if (isset($_GET['activate'])) unset( $_GET['activate']);
    
        $message = sprintf(
            /* translators: 1: Plugin Name 2: Elementor */
            esc_html__('%1$s requires %2$s to be installed and activated.', 'BFLG'),
            '<strong>' . BFL_DISPLAYNAME . '</strong>',
            '<strong>' . __( 'Elementor', 'BFLG' ) . '</strong>'
        );

        $plugin = "elementor/elementor.php";
        //$url = admin_url('plugins.php?action=activate&plugin=elementor/elementor.php&plugin_status=all&paged=1');
        $url = "plugins.php?action=activate&plugin_status=all&paged=1&s&plugin=" . $plugin;
        $url = wp_nonce_url($url, "activate-plugin_" . $plugin); 

        $button = sprintf(
            '<a class="button-primary" href="%1$s">%2$s</a>',
            $url,
            sprintf(__('Activate %1$s', null), 'Elementor')
        );
    
        printf('<div class="notice notice-error is-dismissible"><p>%1$s</p><p>%2$s</p></div>', $message, $button);
    }

}

new BFL_init();
