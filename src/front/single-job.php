<?php

get_header();

?>
<style>
    #apply{
        position:fixed;
        top:0;
        bottom:0;
        right:0;
        left:0;
        width:100%;
        height:100%;
        opacity:0;
        z-index:-1;
        transition:.5s opacity;
        background-color:rgba(0,0,0,.6);
        display:table;
    }
    
    #apply .vertical-center{
        display:table-cell;
        vertical-align:middle;
    }
    
    #apply .popup-content{
        position:relative;
        max-width:500px;
        padding:20px;
        margin:0 auto;
        background-color: #001f44;
        color: #fff;
        border-radius: 2px;
        box-shadow: 0px 20px 40px rgba(0,0,0,.7);
    }
    
    #apply .close-hotspot{
        position:absolute;
        top:0;
        bottom:0;
        right:0;
        left:0;
        width:100%;
        height:100%;
        cursor:pointer;
    }
    
    #apply.active{
        opacity:1;
        z-index:9999;
    }
</style>
<div id="apply">
    <div class="close-hotspot"></div>
    <div class="vertical-center">
        <div class="popup-content">
            <?php echo do_shortcode('[contact-form-7 id="2848" title="Job Application Form"]'); ?>
        </div>
    </div>
</div>
<div class="container-wrap" style="opacity: 1;">
    <div class="container main-content">
        <?php 
        
            $wp_query->the_post();
            
        ?>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h1><?php the_title(); ?></h1>
                        <hr/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Job Description</h5>
                        <?php the_content(); ?>
                        <div>
                            <a id="applybtn" class="large nectar-button has-icon" data-color-override="false" data-hover-color-override="false" style="visibility: visible;">
                                <span>Apply Now</span>
                                <i class="steadysets-icon-align-center" style="color:#fff"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Details</h5>
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
                                    <h6><?php echo $name; ?></h6>
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
                            <div class="col-md-6">
                                <?php
                                    $job_fields = array(
                                        'pay' => 'Pay Rate/Compensation',
                                        'ref_code' => 'Ref Code',
                                    );
                            
                                    foreach($job_fields as $field => $name){
                                        if(get_post_meta(get_the_ID(), '_job_posts_'.$field, true)){
                                        
                                ?>
                                            <h6><?php echo $name; ?></h6>
                                            <strong><?php echo get_post_meta(get_the_ID(), '_job_posts_'.$field, true); ?></strong>
                                            <hr/>
                                <?php
                                    
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>

<?php

get_footer();

?>