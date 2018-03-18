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
        
        //wp_die(var_dump($_POST));//tax_input
        
        $taxonomies_available = array(
            'job_type',
            'job_role',
            'job_location',
            'firm_type'
        );
        
        $aggregate = array();
        
        foreach($taxonomies_available as $tax){
            $terms_attached = wp_get_post_terms($post_id, $tax);
            
            foreach($terms_attached as $key => $term){
                $terms_attached[$term->slug] = $term->slug;
                unset($terms_attached[$key]);
            }
            
            if($terms_attached){
                $aggregate[$tax] = $terms_attached;
            }
        }
        
        //var_dump($aggregate);
        //$aggregate2 = get_combinations($aggregate);
        
        $defaults = array(
            'job_location' => '',
            'job_type' => '',
            'job_role' => '',
            'firm_type' => ''
        );
        
        $bigdude = array();
        
        $aggregate['firm_type'][] = '_blank_';
        $aggregate['job_role'][] = '_blank_';
        $aggregate['job_location'][] = '_blank_';
        foreach($aggregate['firm_type'] as $key1 => $val1){
            foreach($aggregate['job_role'] as $key2 => $val2){
                foreach($aggregate['job_location'] as $key3 => $val3){
                    $bigdude[] = array(
                        'firm_type' => $val1,
                        'job_role' => $val2,
                        'job_location' => $val3
                    );
                }
            }
        }
        
        $meta_query = array(
            'relation' => 'OR'    
        );
        
        foreach($bigdude as $item){
            $meta_query[] = array(
                'relation' => 'AND',
                array(
                    'key' => 'preferences_job_role',
                    'value' => $item['job_role'],
                    'compare' => '='
                ),
                array(
                    'key' => 'preferences_job_location',
                    'value' => $item['job_location'],
                    'compare' => '='
                ), 
                array(
                    'key' => 'preferences_firm_type',
                    'value' => $item['firm_type'],
                    'compare' => '='
                ),
            );    
        }
        
        var_dump($meta_query);
        var_dump($bigdude);
        
        wp_die();
        
        foreach($aggregate2 as $key => $combo){
            $aggregate2[$key] = array_merge($defaults, $combo);
        }
        
        foreach(listshit($aggregate) as $item){
            $aggregate2[] = $item;
        }
        
        var_dump($aggregate2);
        
        wp_die(var_dump());
        //wp_die(var_dump(get_combinations($aggregate)));
        
        $formatted = false;
        
        foreach($aggregate as $tax => $val){
            listcombos();
        }
        
        //wp_die(var_dump($formatted));
        
        if($formatted){
            
            $args = array(
	            'meta_query' => $formatted,
	        );
	        $user_query = new WP_User_Query($args);
	        
        };
        
        wp_die(var_dump($user_query));
        
        /*if('on' == get_user_meta( um_user( 'ID' ), 'preferences_email', true)){
            
            notification( 'my_plugin/action' );
            
        }*/
        
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