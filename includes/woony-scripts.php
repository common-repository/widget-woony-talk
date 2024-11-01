<?php
    function woony_add_scripts() {
        wp_register_script( 'woony-main-script', 'https://optimobile.se/downloads/installation/woony.widget.install.js' );
        $options = get_option( 'woony_plugin_options' );
        $dataToBePassed = array(
            'apiKey'            => $options['api_key'] ?? '',
            'debugLogging'      => $options['debug_logging'] ?? false,
            'widgetVersion'      => $options['widget_version'] ?? '0.2.8',
        );
        wp_localize_script( 'woony-main-script', 'php_vars', $dataToBePassed );
        wp_enqueue_script( 'woony-main-script' );
    }
    add_action('wp_enqueue_scripts', 'woony_add_scripts'); 