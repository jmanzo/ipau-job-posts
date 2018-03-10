<?php

/**
 * Plugin Name:  Custom Post Jobs
 * Plugin URI:   https://about.me/jeanmanzo
 * Description:  This is a custom plugin for create job post types in a Nytrobit company project
 * Author:       Jean Manzo - Nytrobit
 * Author URI:   https://about.me/jeanmanzo
 * Contributors: Jean Manzo, Gal Doron
 *
 * Version:      1.0.0
 *
 * Text Domain:  custom-posts-job
 * Domain Path:  languages
 *
 */
defined( 'ABSPATH' ) or die( 'You can not access here.' );

/**
 * Load initializer
 * 
 */
require_once ( __DIR__ . '/includes/notification/load.php');
require_once __DIR__.'/src/init.php';

function add_notification_trigger() {

        register_trigger( array(
            'slug'     => 'my_plugin/action',
            'name'     => __( 'Notification job', JOBTEXTDOMAIN ),
        ) );
    }
add_action( 'init', 'add_notification_trigger', 10 );

GSJ_Bootstrapper::Init();