<?php
/**
* @package WidgetWoonyTalkPlugin
*/
/*
Plugin Name: Widget Woony Talk
Plugin URI: https://woony.me
Description: Installs Woony Talk Widget on your website. Talk with your visitors!
Version: 1.1.2
Author: OptiMobile AB
Author URI: https://optimobile.se
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text domain: widget-woony-talk
*/

if (!defined('ABSPATH')) { 
    exit;
}

if (!function_exists('write_log')) {

    function write_log($log) {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }
}

class WoonyTalk 
{
    public $name;

    function __construct() {
        write_log('WoonyTalk-Constructor');
        $this->name = plugin_basename( __FILE__);
    }

    function woony_plugin_section_text() {
        echo '<p>Here you fill in the License Key of your Woony account and the version of the Woony Talk Widget. You can also enable and disable debug logging in the Woony Talk widget.</p>';
    }

    function woony_plugin_options_validate( $input ) {
        write_log('WoonyTalk-Validate');
        $newinput['api_key'] = trim( $input['api_key'] );
        if ( ! preg_match( '/^[a-zA-Z0-9\-]{20,50}$/i', $newinput['api_key'] ) ) {
            $newinput['api_key'] = '';
        }     
        $newinput['widget_version'] = trim( $input['widget_version'] );
        if ( ! preg_match( '/^[0-9\.]{5}$/i', $newinput['widget_version'] ) ) {
            $newinput['widget_version'] = '0.2.8';
        }
        $newinput['debug_logging'] = $input['debug_logging'];
        return $newinput;
    }

    function w_register_settings() {
        register_setting( 'woony_plugin_options', 'woony_plugin_options', 'woony_plugin_options_validate' );
        add_settings_section( 'woony_settings', 'Woony Talk settings',  array( $this, 'woony_plugin_section_text' ), 'woony_widget' );
        add_settings_field( 'woony_plugin_setting_api_key', 'License Key', array( $this, 'woony_plugin_setting_api_key' ), 'woony_widget', 'woony_settings' );
        add_settings_field( 'woony_plugin_setting_widget_version', 'Widget version', array( $this, 'woony_plugin_setting_widget_version' ), 'woony_widget', 'woony_settings' );
        add_settings_field( 'woony_plugin_settings_debug_logging', 'Debug logging', array( $this, 'woony_plugin_settings_debug_logging' ), 'woony_widget', 'woony_settings' );  
    }

    function woony_plugin_setting_api_key() {
        $options = get_option( 'woony_plugin_options' );
        echo "<input id='woony_plugin_setting_api_key' size='30' name='woony_plugin_options[api_key]' type='text' value='" . esc_attr( $options['api_key'] ?? '' ) . "' />";
    }

    function woony_plugin_setting_widget_version() {
        $url = "https://optimobile.se/downloads/woony_widgets/opti/config.json";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");     
        $response = curl_exec($ch);
        curl_close($ch);

        $decoded_json = json_decode($response, true);

        $options = get_option( 'woony_plugin_options' );

        echo "<input id='woony_plugin_setting_widget_version' size='30' name='woony_plugin_options[widget_version]' type='text' value='" . esc_attr( $options['widget_version'] ?? $decoded_json['latestVersion'] ) . "' />";
        if ($options['widget_version']) {
            echo "<span style='font-size:12px;'>(latest version is ";
            echo $decoded_json['latestVersion'];
            echo ")</span>";
        }
    }

    function woony_plugin_settings_debug_logging() {
        $options = get_option( 'woony_plugin_options' );
        if (!isset($options['debug_logging'])) {
            $options['debug_logging'] = false;
        }
        $html = "<input type='checkbox' id='woony_plugin_settings_debug_logging' name='woony_plugin_options[debug_logging]' value='1'" . checked( 1, $options['debug_logging'], false ) . "'/>";
        echo $html;
    }

    function register() {
        write_log('WoonyTalk-Register');
        add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );
        add_action( 'admin_init',  array( $this, 'w_register_settings' ) );
        add_filter( "plugin_action_links_$this->name", array( $this, 'settings_link' ) );
    }

    public function settings_link( $links ) {
        $settings_link = '<a href="admin.php?page=woony_widget">Settings</a>';
        array_push( $links, $settings_link );
        return $links;
    }

    public function add_admin_pages() {
        write_log('WoonyTalk-Add Admin pages');
        $icon = "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDI0LjMuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IgoJIHZpZXdCb3g9IjAgMCAyNTYgMjU2IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCAyNTYgMjU2OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+Cgkuc3Qwe2ZpbGw6I0ZGRkZGRjt9Cjwvc3R5bGU+CjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0yMjIuOSw0NC4zSDM0Yy05LjUsMC0xNy4yLDcuNy0xNy4yLDE3LjJWMTc3YzAsOS41LDcuNywxNy4yLDE3LjIsMTcuMmgxMjIuNWw0NS43LDMzLjVsMC41LTMzLjVoMjAuMgoJYzkuNSwwLDE3LjItNy43LDE3LjItMTcuMlY2MS42QzI0MC4xLDUyLjEsMjMyLjQsNDQuMywyMjIuOSw0NC4zeiBNMTY2LjksMTY3LjdjLTEuMiwwLjEtMi4zLDAuMS0zLjUsMC4xCgljLTEyLjEsMC0yMy4zLTMuOC0zMi4yLTEwLjNjNy45LTcuMSwxMy41LTE2LjQsMTUuOS0yNi45YzMuNCw1LDkuMyw4LDE2LjIsOGMxMS4zLDAsMTkuNy03LjcsMTkuNy0xOS4xYzAtMTEuNC04LjQtMTkuMS0xOS43LTE5LjEKCWMtMTEuMSwwLTE5LjUsNy43LTE5LjUsMTkuMWMwLDE0LjEtNi4xLDI2LjUtMTYsMzUuMmMwLDAtMC4xLDAuMS0wLjEsMC4xYzAsMC0wLjEsMC0wLjEtMC4xYy05LjMsOC4yLTIxLjksMTMuMS0zNS45LDEzLjEKCWMtMjguNiwwLTUyLTIxLjEtNTItNDguNHMyMy4yLTQ4LjQsNTItNDguNGMxMi4zLDAsMjMuNSwzLjgsMzIuNCwxMC4zYy04LDcuMS0xMy42LDE2LjUtMTYsMjcuMWMtMy41LTUuMS05LjQtOC4xLTE2LjQtOC4xCgljLTExLjEsMC0xOS41LDcuNy0xOS41LDE5LjFjMCwxMS40LDguNCwxOS4xLDE5LjUsMTkuMWMxMC41LDAsMTguNi02LjcsMTkuNS0xNi45YzAuMS0wLjcsMC4xLTEuNCwwLjEtMi4yYzAtMS4zLDAtMS42LDAuMS0yLjYKCWMwLjQtNy4zLDIuNS0xNC4xLDUuOS0yMC4yYzAuMy0wLjUsMC41LTEsMC44LTEuNGM4LTEyLjgsMjIuMS0yMS45LDM4LjctMjMuOWMwLjYtMC4xLDEuMS0wLjEsMS43LTAuMmMxLjYtMC4xLDMuMy0wLjIsNC45LTAuMgoJYzI4LjksMCw1Mi4zLDIxLjYsNTIuMyw0OC4zQzIxNS42LDE0NS4xLDE5NC4xLDE2NiwxNjYuOSwxNjcuN3oiLz4KPC9zdmc+Cg==";
        add_menu_page( 'Woony Talk', 'Woony Talk', 'manage_options', 'woony_widget',
        array( $this, 'admin_index' ), $icon, 110 );
    }

    public function admin_index() {
        write_log('WoonyTalk-Admin index');
        require_once (plugin_dir_path(__FILE__).'/templates/admin.php');
    }

    function activate() {
        write_log('WoonyTalk-Activate');
        flush_rewrite_rules();
    }
    function deactivate() {
        write_log('WoonyTalk-DeActivate');
        flush_rewrite_rules();
    }

    function settings_check_activation_hook() {
        set_transient( 'mp-admin-notice-activation', true, 5 );
    }
}

if ( class_exists( 'WoonyTalk' ) ) {
    $woonyTalk = new WoonyTalk();
    $woonyTalk->register();
}

register_activation_hook( __FILE__, array ( $woonyTalk, 'activate') );
register_deactivation_hook( __FILE__, array ( $woonyTalk, 'deactivate') );
require_once(plugin_dir_path(__FILE__).'/includes/woony-scripts.php');
