<?php
/**
 * Trigger this file at Plugin uninstall
 * 
 * @package WoonyTalkPlugin
 */

 if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
     exit;
 }

delete_option('woony_plugin_options');
