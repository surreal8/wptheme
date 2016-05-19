<?php

// enqueue the child theme stylesheet
Function wp_schools_enqueue_scripts() {
wp_register_style( 'childstyle', get_stylesheet_directory_uri() . '/style.css'  );
wp_enqueue_style( 'childstyle' );
}
add_action( 'wp_enqueue_scripts', 'wp_schools_enqueue_scripts', 11);

//Replace default.js file
add_action('wp_enqueue_scripts', 'load_javascript_files');
function load_javascript_files() {
	wp_register_script('defaultjs', get_stylesheet_directory_uri() . '/js/default.js', array('jquery'), true );
	wp_enqueue_script('defaultjs');
}

// Remove Query String
function _remove_script_version( $src ){
$parts = explode( '?ver', $src );
return $parts[0];
}
add_filter( 'script_loader_src', '_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', '_remove_script_version', 15, 1 );

//adding subcategories in menu for portfolio list
if (!function_exists('portfolio_list')) {
	
    function portfolio_list($atts, $content = null) {

        global $wp_query;
        global $qode_options;
        $portfolio_qode_like = "on";
        if (isset($qode_options['portfolio_qode_like'])) {
            $portfolio_qode_like = $qode_options['portfolio_qode_like'];
        }

        $portfolio_list_hide_category = false;
        if (isset($qode_options['portfolio_list_hide_category']) && $qode_options['portfolio_list_hide_category'] == "yes") {
            $portfolio_list_hide_category = true;
        }

        $portfolio_filter_class = "";
        if (isset($qode_options['portfolio_filter_disable_separator']) && !empty($qode_options['portfolio_filter_disable_separator'])) {
            if($qode_options['portfolio_filter_disable_separator'] == "yes"){
                $portfolio_filter_class = "without_separator";
            } else {
                $portfolio_filter_class = "";
            }
        }

        $args = array(
            "type"                      => "standard",
            "hover_type"                => "default_hover",
            "box_border"                => "",
            "box_background_color"         => "",        
            "box_border_color"          => "",
            "box_border_width"          => "",
            "columns"                   => "3",
            "image_size"                => "",
            "order_by"                  => "date",
            "order"                     => "ASC",
            "number"                    => "-1",
            "filter"                    => "no",
            "filter_order_by"           => "name",
            "disable_filter_title"      => "no",
            "filter_align"              => "left_align",
            "disable_link"              => "no",
            "lightbox"                     => "yes",
            "show_like"                 => "yes",
            "category"                  => "",
            "selected_projects"         => "",
            "show_load_more"            => "yes",
            "title_tag"                 => "h4",
            "title_font_size"           => "",
            "text_align"                => ""
        );

        extract(shortcode_atts($args, $atts));

        $headings_array = array('h2', 'h3', 'h4', 'h5', 'h6');

        //get correct heading value. If provided heading isn't valid get the default one
        $title_tag = (in_array($title_tag, $headings_array)) ? $title_tag : $args['title_tag'];

        $html = "";

        $_type_class = '';
        $_portfolio_space_class = '';
        $_portfolio_masonry_with_space_class = '';
        if ($type == "hover_text") {
            $_type_class = " hover_text";
            $_portfolio_space_class = "portfolio_with_space portfolio_with_hover_text";
        } elseif ($type == "standard" || $type == "masonry_with_space"){
            $_type_class = " standard";
            $_portfolio_space_class = "portfolio_with_space portfolio_standard";
            if($type == "masonry_with_space"){
                $_portfolio_masonry_with_space_class = ' masonry_with_space';
            }
        } elseif ($type == "standard_no_space"){
            $_type_class = " standard_no_space";
            $_portfolio_space_class = "portfolio_no_space portfolio_standard";
        } elseif ($type == "hover_text_no_space"){
            $_type_class = " hover_text no_space";
            $_portfolio_space_class = "portfolio_no_space portfolio_with_hover_text";
        }

        $portfolio_box_style = "";
        $portfolio_description_class = "";

        if($box_border == "yes" || $box_background_color != ""){

            $portfolio_box_style .= "style=";
            if($box_border == "yes"){
                $portfolio_box_style .= "border-style:solid;";
                if($box_border_color != "" ){
                    $portfolio_box_style .= "border-color:" . $box_border_color . ";";
                }
                if($box_border_width != "" ){
                    $portfolio_box_style .= "border-width:" . $box_border_width . "px;";
                } else {
                    $portfolio_box_style .= "border-width: 1px;";
                }
            }
            if($box_background_color != ""){
                $portfolio_box_style .= "background-color:" . $box_background_color . ";";
            }
            $portfolio_box_style .= "'";

            $portfolio_description_class .= 'with_padding';

            $_portfolio_space_class = ' with_description_background';
        }

        if($text_align !== '') {
            $portfolio_description_class .= ' text_align_'.$text_align;
        }

        if($type != 'masonry') {
            $html .= "<div class='projects_holder_outer v$columns $_portfolio_space_class $_portfolio_masonry_with_space_class'>";
             if ($filter == "yes") {
                $html .= "<div class='filter_outer filter_portfolio " . $filter_align . "'>";
                $html .= "<div class='filter_holder " . $portfolio_filter_class . "'><ul>";
                if ($disable_filter_title != "yes") {
                    $html .= "<li class='filter_title'><span>" . __('Sort Portfolio:', 'edgt_cpt') . "</span></li>";
                }
                if ($type == 'masonry_with_space' || $type == 'masonry_with_space_without_description') {
                    $html .= "<li class='filter' data-filter='*'><span>" . __('All', 'edgt_cpt') . "</span></li>";
                } else {
                    $html .= "<li class='parent_menu filter' data-filter='all'><span>" . __('All', 'edgt_cpt') . "</span></li>";
                }

                if ($category == "") {
                    $args = array(
                        'parent' => 0,
                        'orderby' => $filter_order_by
                    );
                    $portfolio_categories = get_terms('portfolio_category', $args);
                } else {
                    $top_category = get_term_by('slug', $category, 'portfolio_category');
                    $term_id = '';
                    if (isset($top_category->term_id))
                        $term_id = $top_category->term_id;
                    $args = array(
                        'parent' => $term_id,
                        'orderby' => $filter_order_by
                    );
                    $portfolio_categories = get_terms('portfolio_category', $args);
                }

                foreach ($portfolio_categories as $portfolio_category) {
                	if ($type == 'masonry_with_space' || $type == 'masonry_with_space_without_description') {
						$html .= "<li class='filter' data-filter='portfolio_category_$portfolio_category->term_id'><span>$portfolio_category->name</span>";
                        //$html .= "<li><span>$portfolio_category->name</span>";
                    } else {
						$args = array('child_of' => $portfolio_category->term_id);
						$portfolio_categories_child = get_terms('portfolio_category', $args);
						$subcatnum = count($portfolio_categories_child);
						$subcats = array();
						if ($subcatnum > 1) {
							foreach ($portfolio_categories_child as $portfolio_category_child) {
								array_push($subcats, 'portfolio_category_'.$portfolio_category_child->term_id);
								$subcatresults =  implode(' ', $subcats);
							}
						} else {
								$subcatresults = 'portfolio_category_'.$portfolio_category_child->term_id;
							}
                        	$html .= "<li class='parent_menu filter' data-filter='$subcatresults'><span>$portfolio_category->name</span>";
                    }
                    
                    $html .= "<ul class='child'>";
                    
                    foreach ($portfolio_categories_child as $portfolio_category_child) {
                        $html .= "<li class='filter' data-filter='portfolio_category_$portfolio_category_child->term_id'><span>$portfolio_category_child->name</span></li>"; 
                    }
                    $html .= "</ul></li>";
                }
                $html .= "</ul></div></div><div id='filterSelect'></div>";
            }

            $thumb_size_class = "";
            //get proper image size
            switch($image_size) {
                case 'landscape':
                    $thumb_size_class = 'portfolio_landscape_image';
                    break;
                case 'portrait':
                    $thumb_size_class = 'portfolio_portrait_image';
                    break;
                case 'square':
                    $thumb_size_class = 'portfolio_square_image';
                    break;
                case 'full':
                    $thumb_size_class = 'portfolio_full_image';
                    break;
                default:
                    $thumb_size_class = 'portfolio_default_image';
                    break;
            }

            $html .= "<div class='projects_holder clearfix v$columns$_type_class $thumb_size_class'>n";
            if (get_query_var('paged')) {
                $paged = get_query_var('paged');
            } elseif (get_query_var('page')) {
                $paged = get_query_var('page');
            } else {
                $paged = 1;
            }
            if ($category == "") {
                $args = array(
                    'post_type' => 'portfolio_page',
                    'orderby' => $order_by,
                    'order' => $order,
                    'posts_per_page' => $number,
                    'paged' => $paged
                );
            } else {
                $args = array(
                    'post_type' => 'portfolio_page',
                    'portfolio_category' => $category,
                    'orderby' => $order_by,
                    'order' => $order,
                    'posts_per_page' => $number,
                    'paged' => $paged
                );
            }
            $project_ids = null;
            if ($selected_projects != "") {
                $project_ids = explode(",", $selected_projects);
                $args['post__in'] = $project_ids;
            }
            query_posts($args);
            if (have_posts()) : while (have_posts()) : the_post();
                $terms = wp_get_post_terms(get_the_ID(), 'portfolio_category');
                $html .= "<article class='mix ";
                foreach ($terms as $term) {
                    $html .= "portfolio_category_$term->term_id ";
                }

                $title = get_the_title();
                $featured_image_array = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full'); //original size

                if(get_post_meta(get_the_ID(), 'qode_portfolio-lightbox-link', true) != ""){
                    $large_image = get_post_meta(get_the_ID(), 'qode_portfolio-lightbox-link', true);
                } else {
                    $large_image = $featured_image_array[0];
                }

                $slug_list_ = "pretty_photo_gallery";

                //get proper image size
                switch($image_size) {
                    case 'landscape':
                        $thumb_size = 'portfolio-landscape';
                        break;
                    case 'portrait':
                        $thumb_size = 'portfolio-portrait';
                        break;
                    case 'square':
                        $thumb_size = 'portfolio-square';
                        break;
                    case 'full':
                        $thumb_size = 'full';
                        break;
                    default:
                        $thumb_size = 'portfolio-default';
                        break;
                }

                if($type == "masonry_with_space"){
                    $thumb_size = 'portfolio_masonry_with_space';
                }

                $custom_portfolio_link = get_post_meta(get_the_ID(), 'qode_portfolio-external-link', true);
                $portfolio_link = $custom_portfolio_link != "" ? $custom_portfolio_link : get_permalink();

                if(get_post_meta(get_the_ID(), 'qode_portfolio-external-link-target', true) != ""){
                    $custom_portfolio_link_target = get_post_meta(get_the_ID(), 'qode_portfolio-external-link-target', true);
                } else {
                    $custom_portfolio_link_target = '_blank';
                }

                $target = $custom_portfolio_link != "" ? $custom_portfolio_link_target : '_self';

                $html .="'>";

                $html .= "<div class='image_holder ".$hover_type."'>";
                    $html .= "<span class='image'>";
                        $html .= get_the_post_thumbnail(get_the_ID(), $thumb_size);
                    $html .= "</span>";

                    if ($type == "standard" || $type == "standard_no_space" || $type == "masonry_with_space") {

                        if($disable_link != "yes"){
                            $html .= "<a class='portfolio_link_class' href='" . $portfolio_link . "' target='".$target."'></a>";
                        }

                        $html .= '<div class="portfolio_shader"></div>';

                        $html .= '<div class="icons_holder"><div class="icons_holder_inner">';
                            if ($lightbox == "yes") {
                                $html .= "<a class='portfolio_lightbox' title='" . $title . "' href='" . $large_image . "' data-rel='prettyPhoto[" . $slug_list_ . "]'></a>";
                            }

                            if ($portfolio_qode_like == "on" && $show_like == "yes") {
                                if (function_exists('qode_like_portfolio_list')) {
                                    $html .= qode_like_portfolio_list(get_the_ID());
                                }
                            }
                        $html .= "</div></div>";

                    } else if ($type == "hover_text" || $type == "hover_text_no_space") {

                        if($disable_link != "yes"){
                            $html .= "<a class='portfolio_link_class' href='" . $portfolio_link . "' target='".$target."'></a>";
                        }
                        
                        $html .= '<div class="portfolio_shader"></div>';

                        $html .= '<div class="text_holder">';
                            if($hover_type == "elegant_hover"){
                                $html .= '<div class="text_holder_inner"><div class="text_holder_inner2">';
                            }

                            if($hover_type == "default_hover" && !$portfolio_list_hide_category){
                                $html .= '<span class="project_category">';
                                    $html .= '<span>'. __('In ', 'qode') .'</span>';
                                    $k = 1;
                                    foreach ($terms as $term) {
                                        $html .= "$term->name";
                                        if (count($terms) != $k) {
                                            $html .= ' / ';
                                        }
                                        $k++;
                                    }
                                $html .= '</span>';
                            }    

                            $title_style = '';
                            if($title_font_size != ""){
                                $title_style = 'style="font-size: '.$title_font_size.'px;"';
                            }
                            
                            $html .= '<'.$title_tag.' class="portfolio_title" '.$title_style.'>' . get_the_title() . '</'.$title_tag.'>';

                            if($hover_type != "default_hover" && !$portfolio_list_hide_category){
                                $html .= '<span class="project_category">';
                                    $html .= '<span>'. __('In ', 'qode') .'</span>';
                                    $k = 1;
                                    foreach ($terms as $term) {
                                        $html .= "$term->name";
                                        if (count($terms) != $k) {
                                            $html .= ' / ';
                                        }
                                        $k++;
                                    }
                                $html .= '</span>';
                            }

                            if($hover_type == "elegant_hover"){
                                $html .= '</div></div>';
                            }
                        $html .= "</div>";

                        if($hover_type != "elegant_hover"){
                            $html .= '<div class="icons_holder"><div class="icons_holder_inner">';
                                if ($lightbox == "yes") {
                                    $html .= "<a class='portfolio_lightbox' title='" . $title . "' href='" . $large_image . "' data-rel='prettyPhoto[" . $slug_list_ . "]'></a>";
                                }

                                if ($portfolio_qode_like == "on" && $show_like == "yes") {
                                    if (function_exists('qode_like_portfolio_list')) {
                                        $html .= qode_like_portfolio_list(get_the_ID());
                                    }
                                }
                            $html .= "</div></div>";
                        }
                    }

                $html .= "</div>";

                if ($type == "standard" || $type == "standard_no_space" || $type == "masonry_with_space") {
                    $html .= "<div class='portfolio_description ".$portfolio_description_class."'". $portfolio_box_style .">";
                        
                        $title_style = '';
                        if($title_font_size != ""){
                            $title_style = 'style="font-size: '.$title_font_size.'px;"';
                        }

                        if($disable_link != "yes"){
                            $html .= '<'.$title_tag.' class="portfolio_title" '.$title_style.'><a href="' . $portfolio_link . '" target="'.$target.'">' . get_the_title() . '</a></'.$title_tag.'>';
                        } else {
                            $html .= '<'.$title_tag.' class="portfolio_title" '.$title_style.'>' . get_the_title() . '</'.$title_tag.'>';
                        }
                    
                        if(!$portfolio_list_hide_category){
                            $html .= '<span class="project_category">';
                                $html .= '<span>'. __('In ', 'qode') .'</span>';
                                $k = 1;
                                foreach ($terms as $term) {
                                    $html .= "$term->name";
                                    if (count($terms) != $k) {
                                        $html .= ', ';
                                    }
                                    $k++;
                                }
                            $html .= '</span>';
                        }
                    $html .= '</div>';
                }

                $html .= "</article>n";

            endwhile;

                $i = 1;
                while ($i <= $columns) {
                    $i++;
                    if ($columns != 1) {
                        $html .= "<div class='filler'></div>n";
                    }
                }

            else:
                ?>
                <p><?php _e('Sorry, no posts matched your criteria.', 'qode'); ?></p>
            <?php
            endif;

            $html .= "</div>";
            if (get_next_posts_link()) {
                if ($show_load_more == "yes" || $show_load_more == "") {
                    $html .= '<div class="portfolio_paging"><span rel="' . $wp_query->max_num_pages . '" class="load_more">' . get_next_posts_link(__('Show more', 'qode')) . '</span></div>';
                    $html .= '<div class="portfolio_paging_loading"><a href="javascript: void(0)" class="qbutton">'.__('Loading...', 'qode').'</a></div>';
                }
            }
            $html .= "</div>";
            wp_reset_query();
        } else {
            if ($filter == "yes") {

                $html .= "<div class='filter_outer ".$filter_align."'>";
                $html .= "<div class='filter_holder ".$portfolio_filter_class."'><ul>";
                if($disable_filter_title != "yes"){        
                    $html .= "<li class='filter_title'><span>".__('Sort Portfolio:', 'qode')."</span></li>";
                }    
                $html .= "<li class='filter' data-filter='*'><span>" . __('All', 'qode') . "</span></li>";
                if ($category == "") {
                    $args = array(
                        'parent' => 0,
                        'orderby' => $filter_order_by
                    );
                    $portfolio_categories = get_terms('portfolio_category', $args);
                } else {
                    $top_category = get_term_by('slug', $category, 'portfolio_category');
                    $term_id = '';
                    if (isset($top_category->term_id))
                        $term_id = $top_category->term_id;
                    $args = array(
                        'parent' => $term_id,
                        'orderby' => $filter_order_by
                    );
                    $portfolio_categories = get_terms('portfolio_category', $args);
                }
                foreach ($portfolio_categories as $portfolio_category) {
                    $html .= "<li class='filter' data-filter='.portfolio_category_$portfolio_category->term_id'><span>$portfolio_category->name</span>";
                    $args = array(
                        'child_of' => $portfolio_category->term_id
                    );
                    $html .= '</li>';
                }
                $html .= "</ul></div>";
                $html .= "</div>";

            }
            $html .= "<div class='projects_masonry_holder'>";
            if (get_query_var('paged')) {
                $paged = get_query_var('paged');
            } elseif (get_query_var('page')) {
                $paged = get_query_var('page');
            } else {
                $paged = 1;
            }
            if ($category == "") {
                $args = array(
                    'post_type' => 'portfolio_page',
                    'orderby' => $order_by,
                    'order' => $order,
                    'posts_per_page' => $number,
                    'paged' => $paged
                );
            } else {
                $args = array(
                    'post_type' => 'portfolio_page',
                    'portfolio_category' => $category,
                    'orderby' => $order_by,
                    'order' => $order,
                    'posts_per_page' => $number,
                    'paged' => $paged
                );
            }
            $project_ids = null;
            if ($selected_projects != "") {
                $project_ids = explode(",", $selected_projects);
                $args['post__in'] = $project_ids;
            }
            query_posts($args);
            if (have_posts()) : while (have_posts()) : the_post();
                $terms = wp_get_post_terms(get_the_ID(), 'portfolio_category');
                $featured_image_array = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full'); //original size

                if(get_post_meta(get_the_ID(), 'qode_portfolio-lightbox-link', true) != ""){
                    $large_image = get_post_meta(get_the_ID(), 'qode_portfolio-lightbox-link', true);
                } else {
                    $large_image = $featured_image_array[0];
                }

$custom_portfolio_link = get_post_meta(get_the_ID(), 'qode_portfolio-external-link', true);
                $portfolio_link = $custom_portfolio_link != "" ? $custom_portfolio_link : get_permalink();
                if(get_post_meta(get_the_ID(), 'qode_portfolio-external-link-target', true) != ""){
                $custom_portfolio_link_target = get_post_meta(get_the_ID(), 'qode_portfolio-external-link-target', true);
                    } else {
                $custom_portfolio_link_target = '_blank';
                    }

$target = $custom_portfolio_link != "" ? $custom_portfolio_link_target : '_self';
                
$masonry_size = "default";
                $masonry_size =  get_post_meta(get_the_ID(), "qode_portfolio_type_masonry_style", true);   

$image_size = "";
                if($masonry_size == "large_width"){
                $image_size = "portfolio_masonry_wide";
                    }elseif($masonry_size == "large_height"){
                $image_size = "portfolio_masonry_tall";
                    }elseif($masonry_size == "large_width_height"){
                $image_size = "portfolio_masonry_large";
                    } else{
                $image_size = "portfolio_masonry_regular";
                    }
                
if($type == "masonry_with_space"){
                $image_size = "portfolio_masonry_with_space";
                    }
                
$slug_list_ = "pretty_photo_gallery";
                $title = get_the_title();
                $html .= "<article class='portfolio_masonry_item ";
                
foreach ($terms as $term) {
                $html .= "portfolio_category_$term->term_id ";
                    }
                
$html .= " " . $masonry_size;
                $html .= "'>";
                
$html .= "<div class='image_holder ".$hover_type."'>";
                    $html .= "<span class='image'>";
                        $html .= get_the_post_thumbnail(get_the_ID(), $image_size);
                            $html .= "</span>"; //close span.image               

if($disable_link != "yes"){
                        $html .= "<a class='portfolio_link_class' href='" . $portfolio_link . "' target='".$target."'></a>";
                            }
                        
                        $html .= '<div class="portfolio_shader"></div>';
                        
$html .= '<div class="text_holder">';
                        if($hover_type == "elegant_hover"){
                            $html .= '<div class="text_holder_inner"><div class="text_holder_inner2">';
                                }                         

if($hover_type == "default_hover" && !$portfolio_list_hide_category){
                            $html .= '<span class="project_category">';
                                $html .= '<span>'. __('In ', 'qode') .'</span>';
                                    $k = 1;
                                    foreach ($terms as $term) {
                                    $html .= "$term->name";
                                        if (count($terms) != $k) {
                                        $html .= ' / ';
                                            }
                                        $k++;
                                        }
                                    $html .= '</span>';
                                }
                            
$title_style = '';
                            if($title_font_size != ""){
                            $title_style = 'style="font-size: '.$title_font_size.'px;"';
                                }
                            
                            $html .= '<'.$title_tag.' class="portfolio_title" '.$title_style.'>' . get_the_title() . '</'.$title_tag.'>';
                            
if($hover_type != "default_hover" && !$portfolio_list_hide_category){
                            $html .= '<span class="project_category">';
                                $html .= '<span>'. __('In ', 'qode') .'</span>';
                                    $k = 1;
                                    foreach ($terms as $term) {
                                    $html .= "$term->name";
                                        if (count($terms) != $k) {
                                        $html .= ' / ';
                                            }
                                        $k++;
                                        }
                                    $html .= '</span>';
                                }
                            
if($hover_type == "elegant_hover"){
                            $html .= '</div></div>';
                                }
                            $html .= "</div>";
                        
if($hover_type != "elegant_hover"){
                        $html .= '<div class="icons_holder"><div class="icons_holder_inner">';
                            if ($lightbox == "yes") {
                                $html .= "<a class='portfolio_lightbox' title='" . $title . "' href='" . $large_image . "' data-rel='prettyPhoto[" . $slug_list_ . "]'></a>";
                                    }                               

if ($portfolio_qode_like == "on" && $show_like == "yes") {
                                if (function_exists('qode_like_portfolio_list')) {
                                    $html .= qode_like_portfolio_list(get_the_ID());
                                        }
                                    }
                                $html .= "</div></div>";
                            }
                        $html .= "</div>"; //close div.image_holder
                    $html .= "</article>";               

endwhile;
            else:
            ?>
                <p><?php _e('Sorry, no posts matched your criteria.', 'qode'); ?></p>
                <?php
            endif;
            wp_reset_query();
            $html .= "</div>";
            }
        return $html;
            }
}
add_shortcode('portfolio_list', 'portfolio_list');

if(!function_exists('qode_excerpt')) {
	/**
	 * Function that cuts post excerpt to the number of word based on previosly set global
	 * variable $word_count, which is defined in qode_set_blog_word_count function
	 */
	function qode_excerpt() {
		global $qode_options, $word_count, $post;

		if($word_count != '0') {
			$word_count = isset($word_count) && $word_count !== "" ? $word_count : $qode_options['number_of_chars'];
			$post_excerpt = $post->post_excerpt != "" ? $post->post_excerpt : strip_tags($post->post_content);
			$clean_excerpt = strlen($post_excerpt) && strpos($post_excerpt, '...') ? strstr($post_excerpt, '...', true) : $post_excerpt;

			$excerpt_word_array = explode (' ', $clean_excerpt);
			$excerpt_word_array = array_slice ($excerpt_word_array, 0, $word_count);
			$excerpt = implode (' ', $excerpt_word_array);
			$excerpt_readmore = '<span class="readmore"><a href="' . get_the_permalink() . '">read more</a></span>';

			//is excerpt different than empty string?
			if($excerpt !== '') {
				echo '<p class="post_excerpt">'.$excerpt.' '.$excerpt_readmore.'</p>';
			}
		}
	}
}
