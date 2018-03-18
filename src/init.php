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
    
    public static function Init(){
        
        self::register_post_types();
        
        self::register_actions();
        
        self::register_shortcodes();

        self::register_ultimatemember();
        
        self::register_notifications();
        
    }
    
    public static function register_assets(){
        
        wp_register_script('GSJ_main', PLUGINDIRURL.'/assets/js/functions.js', array('jquery'));
        wp_register_script('GSJ_chosen', PLUGINDIRURL.'/assets/js/chosen.jquery.min.js', array('jquery'));
        wp_register_script('GSJ_pause', PLUGINDIRURL.'/assets/js/pause.jquery.js', array('jquery'));
        wp_register_script('GSJ_marquee', PLUGINDIRURL.'/assets/js/marquee.jquery.js', array('jquery'), false, true);
        
        wp_register_style('GSJ_bootstrap', PLUGINDIRURL.'/assets/css/bootstrap-gridonly.css');
        wp_register_style('GSJ_chosen', PLUGINDIRURL.'/assets/css/chosen.min.css');

    }
    
    private static function register_actions(){
    
        add_action('wp_ajax_jobsearch', array(__CLASS__, 'jobsearch'));
        add_action('wp_ajax_nopriv_jobsearch', array(__CLASS__, 'jobsearch'));
        add_action('after_setup_theme', array(__CLASS__, 'lang_setup'));
        add_action('wp_enqueue_scripts', array(__CLASS__, 'register_assets'));
        
    }
    
    private static function register_shortcodes(){
        
        add_shortcode('jobs-template', array( __CLASS__, 'get_jobs_template' ));
        
    }
    
    private static function register_post_types(){
        
        require_once __DIR__.'/backend/post_types/config.php';
        Post_Types::init();

    }
    
    private static function register_ultimatemember(){
        
        require_once __DIR__.'/backend/ultimate_member/config.php';
        Ultimate_Member::init();
        
    }
    
    private static function register_notifications(){
        
        require_once __DIR__.'/backend/notifications/config.php';
        Notifications::init();
        
    }
    
    public static function lang_setup(){
    	
    	load_theme_textdomain( JOBTEXTDOMAIN, get_template_directory() . '/languages' );
    	
    }

    public static function jobsearch(){
        $default = array(
            'post_type' => 'job',
            'posts_per_page' => 3,
            'order' => 'DESC',
            'orderby' => 'date',
            'paged' => $_POST['paged'] ? $_POST['paged'] : 1,
        );

        $changed = array();
        
        $taxonomies = array(
            'job_role' => '',
            'job_location' => '',
            'firm_type' => ''
        );
        
        $required = array(
            's' => ''
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
        //var_dump($default);
        //die();
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
            wp_reset_postdata();
            ob_start();
                echo '<div id="pagination">';
                echo paginate_links(array(
                    'base' => '/job/%_%',
                    'format' => 'page/%#%',
                    'current' => $default['paged'],
                    'total' => $results->max_num_pages
                ));
                echo '</div>';
            $rval['pagination'][] = ob_get_clean();
            /*
                ob_start();
                echo '<div id="pagination" class="ajax_pagination">';
                echo paginate_links(array(
                    'total' => $results->max_num_pages,
                ));
                echo '</div>';
                $rval['pagination'][] = ob_get_clean();
            */
        }else{
            $rval = array(
                'status' => 0,
                'data' => 'No results found',
            );
        }
        
        echo json_encode($rval);
        die();
    }

    public static function get_jobs_template() {

        require_once PLUGINDIRECTORY.'/src/front/job-search.php';
        
    }
    
}