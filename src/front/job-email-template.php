<div>
    <?php echo get_the_post_thumbnail_url($job->ID, 'large'); ?>
</div>

<div>
    <div>
        <div>
            <h2><?php echo $job->post_title ?></h2>
            <p><?php echo apply_filters($job->post_content, wp_trim_words(strip_tags($job->post_content), 55 )); ?></p>
            <a href="<?php echo get_the_permalink( $job->ID ); ?>">
                <span>View Details</span>
            </a>
        </div>
    </div>
</div>