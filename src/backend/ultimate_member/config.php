<?php

class Ultimate_Member{
    
    public static function init(){
        
        self::register_dependencies();
        self::register_actions();
        
    }
    
    private static function register_dependencies(){
        
        //No dependencies we can register
        
    }
    
    private static function register_actions(){
        
        add_filter('um_account_page_default_tabs_hook', array(__CLASS__, 'my_custom_tab_in_um'), 100);
        add_action('um_account_content_hook_preferences', array(__CLASS__, 'showExtraFields'), 100);
        add_action('um_submit_account_details', array(__CLASS__, 'um_submit_account_preferences'));
        add_action('um_account_tab__preferences', array(__CLASS__, 'um_account_tab__preferences'));
        
    }
    
    public static function my_custom_tab_in_um( $tabs ) {

        $tabs[800]['preferences']['icon'] = 'um-faicon-pencil';
        $tabs[800]['preferences']['title'] = 'Preferences';
        $tabs[800]['preferences']['custom'] = true;
        
        return $tabs;
    }

    public static function showExtraFields() {

        ob_start();

        $custom_fields = array(
            "preferences_email" => "Would you like to receive email communications from us about new potential job openings?",
            "preferences_job_location" => "Job Location",
            "preferences_job_role" => "Job Role",
            "preferences_firm_type" => "Firm Type",
        );

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

    public static function um_account_tab__preferences( $info ) {

        global $ultimatemember;
        extract( $info );

        $output = $ultimatemember->account->get_tab_output('preferences');
        if ( $output ) 
            echo $output;
    }
    
}