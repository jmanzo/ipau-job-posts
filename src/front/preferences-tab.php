<div class="um-field um-field-<?php echo $key ?>" data-key="<?php echo $key ?>">
    <div class="um-field-label">
        <label for="<?php echo $key ?>"><?php echo $value ?></label>
        <div class="um-clear"></div>
    </div>
    <div class="um-field-area">
        <?php if( "preferences_email" == $key ): ?>
            Yes <input class="um-form-field valid" type="checkbox" name="<?php echo $key ?>" id="<?php echo $key ?>" value="on" data-validate="" data-key="<?php echo $key ?>" <?php echo ($user_preferences[$key] == 'on' ? 'checked' : '') ?> />
            No <input class="um-form-field valid" type="checkbox" name="<?php echo $key ?>" id="<?php echo $key ?>" value="off" data-validate="" data-key="<?php echo $key ?>" <?php echo ($user_preferences[$key] == 'off' ? 'checked' : '') ?> />
        <?php else: ?>
            <select class="um-form-field valid" name="<?php echo $key ?>" id="<?php echo $key ?>">
                <option value=""><?php _e( 'None', JOBTEXTDOMAIN ) ?></option>
                <?php echo '<pre>'; var_dump(get_terms( $taxonomy )); echo '</pre>'; ?>
                <?php foreach( get_terms( $taxonomy ) as $term ): ?>
                    <option value="<?php echo $term->slug; ?>" <?php echo ($user_preferences[$key] == $term->slug ? 'selected' : '') ?>><?php echo $term->name; ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
    </div>  
</div>