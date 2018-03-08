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
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You can not access here.' );
}

/**
 * Load initializer
 * 
 */
require_once __DIR__.'/src/init.php';
GSJ_Bootstrapper::Init();