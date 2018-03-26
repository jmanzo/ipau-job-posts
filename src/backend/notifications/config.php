<?php

class Notifications{

    public static function init(){
        
        self::register_dependencies();
        self::register_actions();
        
    }
    
    private static function register_dependencies(){
        
        require_once PLUGINDIRECTORY.'/includes/notification/load.php';
        
    }
    
    private static function register_actions(){
        
        add_action('save_post', array(__CLASS__, 'send_notification_job'), 10, 3);
        add_action('init', array(__CLASS__, 'add_notification_trigger'), 10);
        
    }
    
    public static function add_notification_trigger() {

        $args = array(
            'slug' => 'my_plugin/action',
            'name' => __( 'Job Posted', JOBTEXTDOMAIN ),
            'title' => __( 'A new job match with you. Check it now!', JOBTEXTDOMAIN ),
            'recipients' => array( 'member' ),
            'tags' => array(
                'member_email' => 'email',
                'job_list' => 'string',
            ),
        );

        register_trigger( $args );
        
    }
    
    public static function send_notification_job($post_id, $post, $update) {
        
        switch(true){
            case(isset($post->post_status) && 'publish' !== $post->post_status):
                return;
                break;
            case('job' !== $post->post_type):
                return;
                break;
            case($post->post_modified_gmt !== $post->post_date_gmt):
                return;
                break;
        }

        $args = array(
            'meta_key' => 'preferences_email',
            'meta_value' => 'on',
            'meta_compare' => '=',
        );
        $meta_query = array( 'relation' => 'AND' );
        $taxonomies = array();

        foreach ($_POST['tax_input'] as $key => $value) {

            $term = get_term_by( 'id', $value[1], $key, OBJECT );

            if ( '' == $term->slug )
                continue;

            if ( 'firm_type' == $key ) {
                $array2 = array(
                    'relation' => 'OR',
                    array(
                        'key' => 'preferences_firm_type',
                        'value' => $term->slug,
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'preferences_firm_type',
                        'value' => '',
                        'compare' => '=',
                    ),
                );
                array_push($meta_query, $array2);
            }

            if( ! isset($value[1]) || 'job_type' == $key || 'firm_type' == $key )
                continue;
            
            $array = array(
                'key' => 'preferences_' . $key,
                'value' => $term->slug,
                'compare' => '=',
            );
            array_push($meta_query, $array);
            
            $taxonomies[$key] = $value[1];
        }
        $args['meta_query'] = $meta_query;

        $user_query = new WP_User_Query( $args );
        $members = $user_query->get_results();

        foreach ($members as $member) {

            ob_start();
            
            self::compose_message( $post, $taxonomies );

            $content = ob_get_clean();
            
            notification( 'my_plugin/action', array(
                'member_email' => $member->data->user_email,
                'job_list' => $content,
            ) );
        }

        return;
    }

    public static function compose_message( $job, $taxonomies ) {

        if ( isset($job) ) {
            include PLUGINDIRECTORY.'/src/front/job-email-template.php';
        } else {
            return;
        }
    }
    
}

function pc_permute($items, $perms = array( )) {
    $back = array();
    if (empty($items)) { 
        $back[] = join(' ', $perms);
    } else {
        for ($i = count($items) - 1; $i >= 0; --$i) {
             $newitems = $items;
             $newperms = $perms;
             list($foo) = array_splice($newitems, $i, 1);
             array_unshift($newperms, $foo);
             $back = array_merge($back, pc_permute($newitems, $newperms));
         }
    }
    return $back;
}

function get_combinations($arrays) {
	$result = array(array());
	foreach ($arrays as $property => $property_values) {
		$tmp = array();
		foreach ($result as $result_item) {
			foreach ($property_values as $property_value) {
				$tmp[] = array_merge($result_item, array($property => $property_value));
			}
		}
		$result = $tmp;
	}
	return $result;
}

function listshit($data, $base = false){
    $latestbase = $base;
    $main_array;
    $default = array(
        'job_location' => '',
        'job_type' => '',
        'job_role' => '',
        'firm_type' => ''
    );
    
    foreach($data as $label => $content){
        
        if(!$latestbase){
            
            $latestbase = $label;
            
        }
        
        if(is_array($content)){
            
            foreach(listshit($content, $label) as $item){
                $main_array[] = $item;
            }
                
        }else{
            
            $main_array[] = array_merge($default, array($latestbase => $content));
                        
        }
        
    }
    
    return $main_array;
}
    /*
    $default = array(
        'job_location' => '',
        'job_type' => '',
        'job_role' => '',
        'firm_type' => ''
    );
    
    foreach($data as $label => $thing){
        if(!$category){
            $category = $label;
        }
        
        $main_array[$category] = $label;
        
        if(is_array($thing)){
            $main_array[] = listshit($thing, $category);
        }
    }
    
    return array_merge($default, $main_array);
}*/