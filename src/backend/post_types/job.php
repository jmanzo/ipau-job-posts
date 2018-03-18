<?php

$type_config = array(

    'args' => array(
        'labels'             => array(
            'name'               => _x( 'Jobs', 'post type general name',   JOBTEXTDOMAIN ),
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
        ),
        'description'        => __( 'Description.', JOBTEXTDOMAIN ),
        'public'             => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'editor', 'thumbnail' )
    ),
    
    'taxonomies' => array(
        'job_type' => array(

	        'args' => array(
		        'hierarchical'      => true,
                'labels' => array(
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
		            'menu_name'         => __( 'Job Types', JOBTEXTDOMAIN ),
                ),
		        'show_ui'           => true,
		        'show_admin_column' => true,
		        'query_var'         => true,
		        'public'            => false,
		        'rewrite'           => array( 'slug' => 'job-type' ),
                'has_archive'       => false,
            ),
        ),
        
        'job_role'=> array(

    	    'args' => array(
    		    'hierarchical'      => true,
                'labels' => array(
		            'name' => _x( 'Job Roles', 'taxonomy general name', JOBTEXTDOMAIN ),
		            'singular_name' => _x( 'Job Role', 'taxonomy singular name', JOBTEXTDOMAIN ),
		            'search_items' => __( 'Search Job Roles', JOBTEXTDOMAIN ),
		            'all_items' => __( 'All Job Roles', JOBTEXTDOMAIN ),
		            'parent_item' => __( 'Parent Job Role', JOBTEXTDOMAIN ),
		            'parent_item_colon' => __( 'Parent Job Role:', JOBTEXTDOMAIN ),
		            'edit_item' => __( 'Edit Job Role', JOBTEXTDOMAIN ),
		            'update_item' => __( 'Update Job Role', JOBTEXTDOMAIN ),
		            'add_new_item' => __( 'Add New Job Role', JOBTEXTDOMAIN ),
		            'new_item_name' => __( 'New Job Role', JOBTEXTDOMAIN ),
		            'menu_name' => __( 'Job Roles', JOBTEXTDOMAIN ),
	            ),
    		    'show_ui'           => true,
    		    'show_admin_column' => true,
    		    'query_var'         => true,
		        'public'            => false,
		        'rewrite'           => false,
                'has_archive'       => false,
    	    ),
        ),
        
        'job_location' => array(

    	    'args' => array(
	    	    'hierarchical'      => true,
    	        'labels' => array(
		            'name'              => _x( 'Locations', 'taxonomy general name', JOBTEXTDOMAIN ),
		            'singular_name'     => _x( 'Location', 'taxonomy singular name', JOBTEXTDOMAIN ),
		            'search_items'      => __( 'Search Locations', JOBTEXTDOMAIN ),
		            'all_items'         => __( 'All Locations', JOBTEXTDOMAIN ),
		            'parent_item'       => __( 'Parent Location', JOBTEXTDOMAIN ),
		            'parent_item_colon' => __( 'Parent Location:', JOBTEXTDOMAIN ),
		            'edit_item'         => __( 'Edit Location', JOBTEXTDOMAIN ),
		            'update_item'       => __( 'Update Location', JOBTEXTDOMAIN ),
		            'add_new_item'      => __( 'Add New Location', JOBTEXTDOMAIN ),
		            'new_item_name'     => __( 'New Location', JOBTEXTDOMAIN ),
		            'menu_name'         => __( 'Locations', JOBTEXTDOMAIN ),
	            ),
		        'show_ui'           => true,
		        'show_admin_column' => true,
		        'query_var'         => true,
		        'public'            => false,
		        'rewrite'           => false,
                'has_archive'       => false,
	        ),
        ),
        
        'firm_type' => array(

	        'args' => array(
		        'hierarchical'      => true,
                'labels' => array(
                    'name'              => _x( 'Firm Types', 'taxonomy general name', JOBTEXTDOMAIN ),
    		        'singular_name'     => _x( 'Firm Type', 'taxonomy singular name', JOBTEXTDOMAIN ),
		            'search_items'      => __( 'Search Firm Types', JOBTEXTDOMAIN ),
		            'all_items'         => __( 'All Firm Types', JOBTEXTDOMAIN ),
		            'parent_item'       => __( 'Parent Firm Type', JOBTEXTDOMAIN ),
		            'parent_item_colon' => __( 'Parent Firm Type:', JOBTEXTDOMAIN ),
		            'edit_item'         => __( 'Edit Firm Type', JOBTEXTDOMAIN ),
		            'update_item'       => __( 'Update Firm Type', JOBTEXTDOMAIN ),
		            'add_new_item'      => __( 'Add New Firm Type', JOBTEXTDOMAIN ),
		            'new_item_name'     => __( 'New Firm Type', JOBTEXTDOMAIN ),
		            'menu_name'         => __( 'Firm Types', JOBTEXTDOMAIN ),
	            ),
		        'show_ui'           => true,
		        'show_admin_column' => true,
		        'query_var'         => true,
		        'public'            => false,
		        'rewrite'           => false,
                'has_archive'       => true,
	        ),
        ),
    ),
);

