<?php 

get_header();

$page = get_query_var('paged', 1);
var_dump($_POST);
?>
<div class="container-wrap" style="opacity: 1;">
    <div class="container main-content">
        <style>
            fieldset {
                border: 0;
            }
            label {
                display: block;
                margin: 30px 0 0 0;
            }
            .chosen-container-single .chosen-single input[type=text]{
                padding:0px!important;
            }
            #results{
                min-height:400px;
                background-color:#efefef;
                padding:0 15px!important;
            }
            .row.job{
                min-height:250px;
                background-color:#fff;
                display:flex;
                flex-wrap:wrap;
                margin-bottom:20px;
                padding-bottom:0px;
            }
            .row.job .content{
                padding-top:20px;
                padding-bottom:20px;
            }
        </style>
        <h1>What opportunities can we find for you?</h1>
        <div id="job-search" class="row">
            <form id="filterForm" method="POST">
            <input name="page" type="hidden" value="<?php echo $paged; ?>"/>
            <div class="col-md-6">
                <label for="speed">Search by Keyword</label>
                <input name="search" id="searchBox" value="<?php echo ( isset( $_POST['search'] ) ? $_POST['search'] : '' ); ?>" type="text"/>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-4">
                        <label for="job_role">Job Role</label>
                        <select name="job_role" class="filter">
                            <option value="">Select One</option>
                            <?php
                                $job_role = array(
                                    'taxonomy' => 'job_role',
                                    'hide_empty' => false,
                                );
                                foreach(get_terms($job_role) as $term){
                            ?>
                                    <option <?php echo (( isset($_POST['job_role']) && $_POST['job_role'] == $term ) ? 'selected' : ''); ?> value="<?php echo $term->slug; ?>"><?php echo $term->name; ?></option>
                            <?php
                                };
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="job_location">Location</label>
                        <select name="job_location" class="filter">
                            <option value="">Select One</option>
                            <?php
                                $job_role = array(
                                    'taxonomy' => 'job_location',
                                    'hide_empty' => false,
                                );
                                foreach(get_terms($job_role) as $term){
                            ?>
                                    <option <?php echo (( isset($_POST['job_location']) && $_POST['job_location'] == $term->slug ) ? 'selected' : ''); ?> value="<?php echo $term->slug; ?>"><?php echo $term->name; ?></option>
                            <?php
                                };
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="firm_type">Type of Firm</label>
                        <select name="firm_type" class="filter">
                            <option value="">Select One</option>
                            <?php
                                $job_role = array(
                                    'taxonomy' => 'firm_type',
                                    'hide_empty' => false,
                                );
                                foreach(get_terms($job_role) as $term){
                            ?>
                                    <option <?php echo ( ( isset($_POST['firm_type']) && $_POST['firm_type'] == $term ) ? 'selected' : ''); ?> value="<?php echo $term->slug; ?>"><?php echo $term->name; ?></option>
                            <?php
                                };
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            </form>
        </div>
        <div id="results" class="container">
            <?php
                
                $datas = array(
                    'top_level' => array(
                        'search' => 's',
                    ),
                    'taxonomies' => array(
                        'job_role' => 'job_role',
                        'job_location' => 'job_location',
                        'firm_type' => 'firm_type'
                    )
                );
                
                $args = array(
                    'post_type' => 'job',
                    'order' => 'DESC',
                    'orderby' => 'date',
                    'paged' => $page,
                );
                
                foreach($datas as $type => $data){
                    switch($type){
                        case 'top_level':
                            foreach($data as $key => $field){
                                if(isset($_POST[$key]) && $_POST[$key]){
                                    $args[$field] = $_POST[$key];
                                }
                            }
                            break;
                        case 'taxonomies':
                            foreach($data as $key => $field){
                                if(isset($_POST[$key]) && $_POST[$key]){
                                    if(!isset($args['tax_query'])){
                                       $args['tax_query'] = array(
                                            array(
                                                'taxonomy' => $field,
			                                    'field'    => 'slug',
			                                    'terms'    => array(
                                                    $_POST[$key],
                                                ),
                                            )
                                        );
                                    }else{
                                        $args['tax_query']['relation'] = 'AND';
                                        $args['tax_query'][] = array(
                                            'taxonomy' => $field,
		                                    'field'    => 'slug',
		                                    'terms'    => array(
                                                $_POST[$key],
                                            ),
                                        );
                                    }
                                }
                            }
                        break;
                    }
                }
                
                //var_dump($args);
                
                $job_query = new WP_Query($args);
                
                if($job_query->have_posts()){
                    while($job_query->have_posts()){
                        
                        $job_query->the_post();
                        include __DIR__.'/job-search-job-template.php';
                    } 
                    wp_reset_postdata();
            ?>
            
                <div id="pagination">
            <?php 
                    echo paginate_links(array(
                        'base' => str_replace( 20, '%#%', esc_url( get_pagenum_link( 20 ) ) ),
                        'format' => '?paged=%#%',
                        'current' => max( 1, get_query_var('paged') ),
                        'total' => $job_query->max_num_pages
                    ));
            ?>
                </div>
            <?php
                }
            ?>
        </div>


    </div>
</div>
<?php get_footer(); ?>