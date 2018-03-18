<?php

class Post_Types{
    
    private static $post_types = array(
        'job',
    );
        
    public function init(){
        
        self::register_dependencies();
        self::register_actions();
        
    }
    
    private function register_dependencies(){
    
        require_once PLUGINDIRECTORY.'includes/cmb2/init.php';
        
    }
    
    private function register_actions(){
        
        add_action('init', array(__CLASS__, 'register_post_types'));
        add_action('cmb2_admin_init', array(__CLASS__, 'job_posts_metaboxes'));
        add_action('template_include', array(__CLASS__, 'template_includes'), 999);
        add_action('pre_get_posts', array(__CLASS__, 'force_posts_per_page'), 9999);
        add_shortcode('featured_jobs', array(__CLASS__, 'featured_jobs'));
        
    }
    
    public static function featured_jobs(){
        
        self::assets();
        
        $jobs = new WP_Query(array(
           'post_type' => 'job',
           'posts_per_page' => '5',
           'meta_key' => '_job_posts_featured',
	       'meta_value' => 'on'
        ));
        
        ob_start();
        if($jobs->have_posts()){
            
        ?>
            <style>
                .marquee{
                    overflow: hidden;
                    background-color: #444;
                    font-size: 2rem;
                    line-height: 7rem;
                }
                
                .marquee a{
                    padding: 0 40px;
                }
                
                /* Move it (define the animation) */
                @-moz-keyframes example1 {
                    0%   { -moz-transform: translateX(100%); }
                    100% { -moz-transform: translateX(-100%); }
                }

                @-webkit-keyframes example1 {
                    0%   { -webkit-transform: translateX(100%); }
                    100% { -webkit-transform: translateX(-100%); }
                }
    
                @keyframes example1 {
                    0%   { 
                        -moz-transform: translateX(100%); /* Firefox bug fix */
                        -webkit-transform: translateX(100%); /* Firefox bug fix */
                        transform: translateX(100%); 		
                    }
                    100% { 
                        -moz-transform: translateX(-100%); /* Firefox bug fix */
                        -webkit-transform: translateX(-100%); /* Firefox bug fix */
                        transform: translateX(-100%); 
                    }
                }
            </style>
            <div class="marquee">
                <?php
                
                    while($jobs->have_posts()){
                        $jobs->the_post();
                        
                ?>
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                <?php
                        
                    }
                    
                ?>
            </div>
        <?php
        
        }
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }
    
    public static function force_posts_per_page($query){
        
        if (is_post_type_archive('job')) {
            $query->set('posts_per_page', '3');
        }
        
    }
        
    public static function register_post_types(){
        
        foreach(self::$post_types as $type){
            
            require_once __DIR__.'/'.$type.'.php';
            
            register_post_type($type, $type_config['args']);
            
            if($type_config['taxonomies']){
                
                self::register_taxonomies($type, $type_config['taxonomies']);

            }
            
        }
        
    }
    
    private static function register_taxonomies($post_type, $taxonomies){
        
        foreach($taxonomies as $uid => $tax){
            
            register_taxonomy($uid, $post_type, $tax['args']);
            
        }
        
    }
    
    public static function assets(){
        wp_enqueue_style('GSJ_chosen');
        wp_enqueue_style('GSJ_bootstrap');
        wp_enqueue_script('GSJ_chosen');
        wp_enqueue_script('GSJ_main');
        wp_enqueue_script('GSJ_pause');
        wp_enqueue_script('GSJ_marquee');
        wp_localize_script('GSJ_main', 'job_form_ajaxdata',
            array( 
                'url' => admin_url( 'admin-ajax.php' ),
            )
        );
    }
    
    public static function template_includes($template){
    	
    	$rval = $template;
    	
    	switch(true){
    	    case(is_post_type_archive('job')):
                add_action('wp_enqueue_scripts', array(__CLASS__, 'assets'));
                $new_template = PLUGINDIRECTORY.'src/front/job-search.php';
                $rval = $new_template;
                break;
            case(is_singular('job')):
                add_action('wp_enqueue_scripts', array(__CLASS__, 'assets'));
                $new_template = PLUGINDIRECTORY.'src/front/single-job.php';
                $rval = $new_template;
                break;
	    }

	    return $rval;
	    
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
    		'name'       => __( 'Ref Code', 'cmb2' ),
    		//'desc'       => __( 'field description (optional)', 'cmb2' ),
    		'id'         => $prefix . 'ref_code',
    		'type'       => 'text',
    		'show_on_cb' => 'cmb2_hide_if_no_cats',
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
}