<?php

/**
 *	Job's class
 *	This will handle all process for create jobs
 *
 */

class Jobs {

	/**
	 *	Create job posts
	 *
	 */
	public function post_create_job( $_post ) {

		wp_insert_post( $_post, false );

	}

}