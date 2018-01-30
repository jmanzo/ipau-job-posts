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
 *	Load the Constants
 *
 */
define( 'JOBTEXTDOMAIN', 'custom-posts-job' );
define( 'PLUGINDIRECTORY', plugin_dir_path( __FILE__ ) );


/*require_once PLUGINDIRECTORY . '/includes/class/Jobs.php';
require_once PLUGINDIRECTORY . '/includes/create-post-job.php';*/


/**
 *	Load Text Domain
 *
 */
function job_posts_lang_setup(){
	
	load_theme_textdomain( JOBTEXTDOMAIN, get_template_directory() . '/languages' );
	
}
add_action( 'after_setup_theme', 'job_posts_lang_setup' );


/**
 *	Register Job Post type
 *
 */
function job_posts_register_job_post_type() {

	$labels = array(
		'name'               => _x( 'Jobs', 'post type general name', JOBTEXTDOMAIN ),
		'singular_name'      => _x( 'Job', 'post type singular name', JOBTEXTDOMAIN ),
		'menu_name'          => _x( 'Jobs', 'admin menu', JOBTEXTDOMAIN ),
		'name_admin_bar'     => _x( 'Job', 'add new on admin bar', JOBTEXTDOMAIN ),
		'add_new'            => _x( 'Add New', 'job', JOBTEXTDOMAIN ),
		'add_new_item'       => __( 'Add New Job', JOBTEXTDOMAIN ),
		'new_item'           => __( 'New Job', JOBTEXTDOMAIN ),
		'edit_item'          => __( 'Edit Job', JOBTEXTDOMAIN ),
		'view_item'          => __( 'View Job', JOBTEXTDOMAIN ),
		'all_items'          => __( 'All Jobs', JOBTEXTDOMAIN ),
		'search_items'       => __( 'Search Jobs', JOBTEXTDOMAIN ),
		'parent_item_colon'  => __( 'Parent Jobs:', JOBTEXTDOMAIN ),
		'not_found'          => __( 'No jobs found.', JOBTEXTDOMAIN ),
		'not_found_in_trash' => __( 'No jobs found in Trash.', JOBTEXTDOMAIN )
	);

	$args = array(
		'labels'             => $labels,
        'description'        => __( 'Description.', JOBTEXTDOMAIN ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'job' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields' )
	);

	register_post_type( 'job', $args );

}
add_action( 'init', 'job_posts_register_job_post_type' );


/**
 *	Register Job Taxonomies
 *
 */
function job_posts_register_taxonomies() {

	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'Job Types', 'taxonomy general name', JOBTEXTDOMAIN ),
		'singular_name'     => _x( 'job Type', 'taxonomy singular name', JOBTEXTDOMAIN ),
		'search_items'      => __( 'Search Job Types', JOBTEXTDOMAIN ),
		'all_items'         => __( 'All Job Types', JOBTEXTDOMAIN ),
		'parent_item'       => __( 'Parent Job Type', JOBTEXTDOMAIN ),
		'parent_item_colon' => __( 'Parent Job Type:', JOBTEXTDOMAIN ),
		'edit_item'         => __( 'Edit Job Type', JOBTEXTDOMAIN ),
		'update_item'       => __( 'Update Job Type', JOBTEXTDOMAIN ),
		'add_new_item'      => __( 'Add New Job Type', JOBTEXTDOMAIN ),
		'new_item_name'     => __( 'New Job Type', JOBTEXTDOMAIN ),
		'menu_name'         => __( 'Job Type', JOBTEXTDOMAIN ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'job_type' ),
	);

	register_taxonomy( 'job_type', 'job', $args );

}
add_action( 'init', 'job_posts_register_taxonomies' );


/**
 * Enqueue scripts and styles.
 */
function job_posts_scripts(){
    
    wp_register_script( 'job-posts-function', plugins_url( 'js/functions.js', __FILE__ ),
        array( 'jquery' ) );

    $obj = array(
    	'user' 		=> wp_get_current_user(),
    	'url'  		=> admin_url( 'admin-ajax.php' ),
    	'home_url'	=> home_url(),
    );

    wp_localize_script( 'job-posts-function', 'obj', $obj );

    wp_enqueue_script( 'job-posts-function' );
    
}
add_action( 'wp_enqueue_scripts', 'job_posts_scripts' );


//**********************
function apf_addpost() {

    $results = '';
    $title = $_POST['post_title'];
    $content =  $_POST['post_content'];
    $author = $_POST['post_author'];
    $slug = str_replace( ' ', '-', strtolower( $_POST['post_category'] ) );
    $category = get_term_by( 'slug', $slug, 'job_type', OBJECT, 'raw' );
    $post_id = wp_insert_post( array(
        'post_title'        => $title,
        'post_content'      => $content,
        'post_status'       => 'publish',
        'post_author'       => $author,
        'post_category'		=> array( $category->term_id ),
        'post_type'			=> 'job',
    ));

    wp_set_object_terms( $post_id, $category->term_id, 'job_type', false );
 
    if ( $post_id != 0 ) {
        $results = 'Post Added';
    } else {
        $results = 'Error occurred while adding the post';
    }
    // Return the String
    die($results);
}
// creating Ajax call for WordPress
add_action( 'wp_ajax_nopriv_apf_addpost', 'apf_addpost' );
add_action( 'wp_ajax_apf_addpost', 'apf_addpost' );


add_shortcode( 'job-posts', 'display_custom_post_type' );

    function display_custom_post_type(){
        $args = array(
            'post_type' => 'job',
            'post_status' => 'publish'
        );

        $string = '';
        $query = new WP_Query( $args );
        if( $query->have_posts() ){ 
            $string .= '<div class="job_wrap">';
            while( $query->have_posts() ){
                $query->the_post();

                $string .= '<div class="job-item">';
                $string .= '<h3>' . get_the_title() . '</h3>';
                $string .= '<h5>It will show the categories here</h5>';
                $string .= '</div>';
            }
            $string .= '</ul>';
        }
        wp_reset_postdata();
        return $string;
    }