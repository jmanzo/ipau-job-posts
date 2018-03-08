<div class="row job">
    <div class="col-md-4 image" style="background-image:url(<?php echo get_the_post_thumbnail_url(get_the_ID(), 'large'); ?>);background-size:cover;">
    </div>
    <div class="col-md-8 content">
        <div class="row">
            <div class="col-md-6">
                <h2><?php the_title(); ?></h2>
                <p>
                    <?php echo apply_filters('the_content', wp_trim_words(strip_tags(get_the_content()), 55 )); ?>
                </p>
                <a href="<?php the_permalink(); ?>" class="large nectar-button has-icon" data-color-override="false" data-hover-color-override="false">
                    <span>View Details</span>
                    <i class="steadysets-icon-align-center" style="color:#fff"></i>
                </a>
            </div>
            <div class="col-md-6">
                <?php
                    $taxonomies = array(
                        'job_location' => 'Job Location(s)',
                        'job_role' => 'Role',
                        'firm_type' => 'Type of Firm'
                    );
                            
                    foreach($taxonomies as $tax => $name){
                ?>      
                        <h4><?php echo $name; ?></h4>
                        <ul>
                            <?php
                                $terms = get_the_terms(get_the_ID(), $tax);
                                if($terms){
                                    foreach($terms as $term){
                            ?> 
                                        <li><?php echo $term->name; ?></li>
                            <?php
                                    }
                                }
                            ?>
                        </ul>
                <?php
                    }
                ?>
            </div>
        </div>
    </div>
</div>