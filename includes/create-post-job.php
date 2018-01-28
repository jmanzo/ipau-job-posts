<?php

/**
 *	This file is for process the create post job form
 *
 */
//require 'class/Jobs.php';


if( $_POST ):

	$user_login = $_POST['user_login'];

	$post_received = array(
		'action' 		=> 'job_post',
		'post_title' 	=> $_POST['job-title'],
		'post_content' 	=> $_POST['job-title'],
		'post_type' 	=> 'job',
		'post_author' 	=> $_POST['user_id'],
		'post_status' 	=> 'publish',
		'post_category'	=> str_replace( ' ', '-', strtolower( $_POST['job-term'] ) ),
	);

	$job = new Jobs();

	if( $job ){ 
		$job->post_create_job( $post_received );
		header( 'location:' . home_url() . '/user/' . $user_login );
	} else {
		exit();
	}

endif;