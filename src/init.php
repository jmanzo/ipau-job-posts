<?php
/*
*
* Begin Bootstrapping
*
*/

defined( 'ABSPATH' ) or die( 'You can not access here.' );

// Constants
define('JOBTEXTDOMAIN', 'custom-posts-job');
define('PLUGINDIRECTORY', __DIR__.'/../');
define('PLUGINDIRURL', plugins_url('', realpath(__DIR__.'/../job_posts.php')));

class GSJ_Bootstrapper{
    
    public static $post_types = array(
        'self::Jobs',
    );
    
    public static $taxonomies = array(
        'job_type' => 'self::Job_Type',
        'job_role'=> 'self::Job_Role',
        'job_location' => 'self::Job_Location',
        'firm_type' => 'self::Firm_Type',
    );
    
    public static function Init(){
        
        self::include_dependencies();

        
        add_action('init', array(__CLASS__, 'register_post_types'));
        add_action('init', array(__CLASS__, 'register_taxonomies'));
        add_action('cmb2_admin_init', array(__CLASS__, 'job_posts_metaboxes'));
        add_action('after_setup_theme', array(__CLASS__, 'lang_setup'));
        add_action('wp_ajax_jobsearch', array(__CLASS__, 'jobsearch'));
        add_action('wp_ajax_nopriv_jobsearch', array(__CLASS__, 'jobsearch'));
        add_filter('template_include', array(__CLASS__, 'template_redirects'), 999);
        add_shortcode( 'jobs-template', array( __CLASS__, 'get_jobs_template' ) );
        add_filter('um_account_page_default_tabs_hook', array( __CLASS__, 'my_custom_tab_in_um' ), 100 );
        add_action('um_account_content_hook_preferences', array( __CLASS__, 'showExtraFields' ), 100);
        add_action('um_submit_account_details', array( __CLASS__, 'um_submit_account_preferences' ));
        add_action('um_account_tab__preferences', array( __CLASS__, 'um_account_tab__preferences') );
        add_action( 'save_post', array( __CLASS__, 'send_notification_job') );
    }
    
    public static function include_dependencies(){
    
        require_once PLUGINDIRECTORY.'/includes/cmb2/init.php';
        
    }
    
    public static function lang_setup(){
    	
    	load_theme_textdomain( JOBTEXTDOMAIN, get_template_directory() . '/languages' );
    }
    
    public static function register_post_types(){
        
        foreach(self::$post_types as $type){
            //Set in memory for the loop
            $type_config = call_user_func($type);
            register_post_type($type_config['uid'], $type_config['args']);
        }
    }
    
    public static function register_taxonomies(){
        
        foreach(self::$taxonomies as $tax){
            //Set in memory for the loop
            $tax_config = call_user_func($tax);
            register_taxonomy($tax_config['uid'], $tax_config['post_type'], $tax_config['args']);
        }
    }

    public static function jobsearch(){
        $default = array(
            'post_type' => 'job',
            'posts_per_page' => 10,
            'order' => 'DESC',
            'orderby' => 'date',
            'paged' => 1
        );
        
        $changed = array();
        
        $taxonomies = array(
            'job_role' => '',
            'job_location' => '',
            'firm_type' => ''
        );
        
        $required = array(
            's' => '',
            'paged' => 1
        );
        
        foreach($required as $field => $val){
            
            if(isset($_POST[$field]) && $_POST[$field]){
                $changed[$field] = $_POST[$field];
            }
        }

        foreach($taxonomies as $field => $val){
            
            if(isset($_POST[$field]) && $_POST[$field]){
                $changed['tax_query'][$field] = $_POST[$field];
            }
        }
        
        $formatted = false;
        if(count($changed['tax_query']) > 1){
            $formatted = array(
                'relation' => 'AND',
            );
        }
        
        foreach($changed['tax_query'] as $tax => $term){
            $formatted[] = array(
                'taxonomy' => $tax,
                'field' => 'slug',
                'terms' => array( $term ),
            );
        }
        
        if($formatted){
            $changed['tax_query'] = $formatted;
        }
        
        $default = array_merge($default, $changed);
        $rval;
        $results = new WP_Query($default);
        
        if($results->have_posts()){
            $rval = array(
                'status' => 1,
                'data' => array(),
                'pagination' => array(),
            );
            
            while($results->have_posts()){
                $results->the_post();
                
                ob_start();
                
                include __DIR__.'/front/job-search-job-template.php';
                
                $rval['data'][] = ob_get_clean();
            }
            ob_start();
            wp_reset_postdata();
            echo '<div id="pagination" class="ajax_pagination">';
            echo paginate_links(array(
                'total' => $results->max_num_pages,
            ));
            echo '</div>';
            $rval['pagination'][] = ob_get_clean();

        }else{
            $rval = array(
                'status' => 0,
                'data' => 'No results found',
            );
        }
        
        echo json_encode($rval);
        die();
    }
    
    public static function template_redirects($template){
    	
    	if(is_post_type_archive('job')){
            $new_template = __DIR__.'/front/job-search.php';
            return $new_template;
	    }

	    return $template;
    }
    
    public static function Jobs(){

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
		    'supports'           => array( 'title', 'editor', 'thumbnail' )
	    );
	    
	    return array('uid' => 'job', 'args' => $args);
    }
    
    public static function Job_Type(){
	    
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
		    'menu_name'         => __( 'Job Types', JOBTEXTDOMAIN ),
        );

	    $args = array(
		    'hierarchical'      => true,
		    'labels'            => $labels,
		    'show_ui'           => true,
		    'show_admin_column' => true,
		    'query_var'         => true,
		    'public'            => false,
		    'rewrite'           => array( 'slug' => 'job-type' ),
            'has_archive'       => true,
	    );
    
        return array('uid'  => 'job_type', 'post_type' => 'job', 'args' => $args);
    }
    
    public static function Job_Role(){
	    $labels = array(
		    'name'              => _x( 'Job Roles', 'taxonomy general name', JOBTEXTDOMAIN ),
		    'singular_name'     => _x( 'Job Role', 'taxonomy singular name', JOBTEXTDOMAIN ),
		    'search_items'      => __( 'Search Job Roles', JOBTEXTDOMAIN ),
		    'all_items'         => __( 'All Job Roles', JOBTEXTDOMAIN ),
		    'parent_item'       => __( 'Parent Job Role', JOBTEXTDOMAIN ),
		    'parent_item_colon' => __( 'Parent Job Role:', JOBTEXTDOMAIN ),
		    'edit_item'         => __( 'Edit Job Role', JOBTEXTDOMAIN ),
		    'update_item'       => __( 'Update Job Role', JOBTEXTDOMAIN ),
		    'add_new_item'      => __( 'Add New Job Role', JOBTEXTDOMAIN ),
		    'new_item_name'     => __( 'New Job Role', JOBTEXTDOMAIN ),
		    'menu_name'         => __( 'Job Roles', JOBTEXTDOMAIN ),
	    );

    	$args = array(
    		'hierarchical'      => true,
    		'labels'            => $labels,
    		'show_ui'           => true,
    		'show_admin_column' => true,
    		'query_var'         => true,
		    'public'            => false,
		    'rewrite'           => false,
            'has_archive'       => true,
    	);

        return array('uid'  => 'job_role', 'post_type' => 'job', 'args' => $args);
    }
	
	public static function Job_Location(){
	    
	    // Location
	    $labels = array(
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
	    );
    	
    	$args = array(
	    	'hierarchical'      => true,
		    'labels'            => $labels,
		    'show_ui'           => true,
		    'show_admin_column' => true,
		    'query_var'         => true,
		    'public'            => false,
		    'rewrite'           => false,
            'has_archive'       => true,
	    );

        return array('uid'  => 'job_location', 'post_type' => 'job', 'args' => $args);
    }    
    
	public static function Firm_Type(){
	
	    // Firm Type
	    $labels = array(
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
	    );

	    $args = array(
		    'hierarchical'      => true,
		    'labels'            => $labels,
		    'show_ui'           => true,
		    'show_admin_column' => true,
		    'query_var'         => true,
		    'public'            => false,
		    'rewrite'           => false,
            'has_archive'       => true,
	    );

        return array('uid'  => 'firm_type', 'post_type' => 'job', 'args' => $args);
    }

    public static function get_jobs_template() {

        require_once PLUGINDIRECTORY.'/src/front/job-search.php';
    }
    
    public static function job_posts_metaboxes() {

	    $prefix = '_job_posts_';

	    /**
        * Initiate the metabox
	    */
	    $cmb = new_cmb2_box(array(
	    	'id'            => 'jobs_metabox',
	    	'title'         => __( 'Job Info', 'cmb2' ),
	    	'object_types'  => array( 'job', ), // Post type
	    	'context'       => 'normal',
	    	'priority'      => 'high',
	    	'show_names'    => true, // Show field names on the left
	    ));

    	// Regular text field for Pay Rate/Compensation
    	$cmb->add_field(array(
    		'name'       => __( 'Pay Rate/Compensation', 'cmb2' ),
    		//'desc'       => __( 'field description (optional)', 'cmb2' ),
    		'id'         => $prefix . 'pay',
    		'type'       => 'text',
    		'show_on_cb' => 'cmb2_hide_if_no_cats',
    	));

        //Checkbox field for Featured
    	$cmb->add_field(array(
        	'name' => 'Featured',
        	'desc' => 'Feature this job?',
        	'id'   => $prefix . 'featured',
        	'type' => 'checkbox',
        ));
    }

    public static function my_custom_tab_in_um( $tabs ) {

        $tabs[800]['preferences']['icon'] = 'um-faicon-pencil';
        $tabs[800]['preferences']['title'] = 'Preferences';
        $tabs[800]['preferences']['custom'] = true;
        
        return $tabs;
    }

    public static function showExtraFields() {

        ob_start();

        $custom_fields = [
            "preferences_email" => "Would you like to receive email communications from us about new potential job openings?",
            "preferences_job_location" => "Job Location",
            "preferences_job_role" => "Job Role",
            "preferences_firm_type" => "Firm Type",
        ];

        foreach ($custom_fields as $key => $value) {

            $fields[ $key ] = array(
                'title' => $value,
                'metakey' => $key,
                'type' => 'select',
                'label' => $value,
            );

            $user_preferences = array( $key => get_user_meta( um_user( 'ID' ), $key, true ) );

            $taxonomy = strtolower( str_replace( ' ', '_', $value ) );
            $taxonomies = get_terms( array( 
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
            ));
            apply_filters('um_account_secure_fields', $fields, 'general' );

            $field_value = get_user_meta(um_user('ID'), $key, true) ? : ''; ?>

            <div class="um-field um-field-<?php echo $key ?>" data-key="<?php echo $key ?>">
                <div class="um-field-label">
                    <label for="<?php echo $key ?>"><?php echo $value ?></label>
                    <div class="um-clear"></div>
                </div>
                <div class="um-field-area">
                    <?php if( "preferences_email" == $key ): ?>
                        Yes <input class="um-form-field valid" type="radio" name="<?php echo $key ?>" id="<?php echo $key ?>" value="on" data-validate="" data-key="<?php echo $key ?>" <?php echo ($user_preferences[$key] == 'on' ? 'checked' : '') ?> />
                        No <input class="um-form-field valid" type="radio" name="<?php echo $key ?>" id="<?php echo $key ?>" value="off" data-validate="" data-key="<?php echo $key ?>" <?php echo ($user_preferences[$key] == 'off' ? 'checked' : '') ?> />
                    <?php else: ?>
                        <select class="um-form-field valid" name="<?php echo $key ?>" id="<?php echo $key ?>">
                            <option value=""><?php _e( 'None', JOBTEXTDOMAIN ) ?></option>
                            <?php foreach( $taxonomies as $term ): ?>
                                <option value="<?php echo $term->slug; ?>" <?php echo ($user_preferences[$key] == $term->slug ? 'selected' : '') ?>><?php echo $term->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>
            </div>
        <?php } ?>
        <div class="um-col-alt um-col-alt-b">
            <div class="um-left">
                <input type="submit" name="um_account_submit" id="um_account_submit" value="Update Preferences" class="um-button">
            </div>
            <div class="um-clear"></div>
        </div>
        <?php $html = ob_get_clean();

        echo $html;
    }

    public static function um_submit_account_preferences( $args ) {

        $current_tab = isset( $args['_um_account_tab'] ) ? $args['_um_account_tab']: '';

        if ( 'preferences' == $current_tab && $args['preferences_email'] || $args['preferences_job_location'] || $args['preferences_job_role'] || $args['preferences_firm_type'] ) {
            $changes = array(
                'preferences_email' => isset( $_POST['preferences_email'] ) ? $_POST['preferences_email'] : '',
                'preferences_job_location' => isset( $_POST['preferences_job_location'] ) ? $_POST['preferences_job_location'] : '',
                'preferences_job_role' => isset( $_POST['preferences_job_role'] ) ? $_POST['preferences_job_role'] : '',
                'preferences_firm_type' => isset( $_POST['preferences_firm_type'] ) ? $_POST['preferences_firm_type'] : '',
            );
            foreach ($changes as $key => $value) {
                update_user_meta( um_user( 'ID' ), $key, $value );
            }
        }
    }

    public static function add_notification_trigger() {

        $args = array(
            'slug' => 'my_plugin/action',
            'name' => __( 'Notification job', JOBTEXTDOMAIN ),
            'title' => __( 'A new job match with you. Check it now!', JOBTETDOMAIN ),
            'recipients' => array( 'member' ),
        );

        register_trigger( $args );
    }

    public static function um_account_tab__preferences( $info ) {

        global $ultimatemember;
        extract( $info );

        $output = $ultimatemember->account->get_tab_output('preferences');
        if ( $output ) 
            echo $output;
    }

    public static function send_notification_job( $post_id ) {

        if( 'job' != get_post_type( $post_id ) || 'member' != um_user( 'role' ) )
            return;

        if ( 'on' == get_user_meta( um_user( 'ID' ), 'preferences_email', true ) ) {
            notification( 'my_plugin/action' );
        }
    }
}