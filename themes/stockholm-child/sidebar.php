<?php 
global $qode_options;
global $qode_page_id;
$sidebar_id =  $qode_page_id;
?>
	<div class="column_inner">
		<aside class="sidebar">
        <div class="words">
          <div class="detail-type">
            <?php echo get_the_term_list( $post->ID, 'detail-type', 'Type of detail page: ', ', ', '' ); ?> 
          </div>
          <div class="price-range">
            <?php echo get_the_term_list( $post->ID, 'price', 'Price Range: ', ', ', '' ); ?> 
         </div>
         <div class="mood">
            <?php echo get_the_term_list( $post->ID, 'mood', 'The mood this puts me in: ', ', ', '' ); ?> 
         </div>
        </div>
			<?php	
			$sidebar = "";

            $is_woocommerce=false;
            if(function_exists("is_woocommerce")) {
                $is_woocommerce = is_woocommerce();
                if($is_woocommerce){
					$sidebar_id = get_option('woocommerce_shop_page_id');
                }
            }
		
			if(get_post_meta($sidebar_id, 'qode_choose-sidebar', true) != ""){
				$sidebar = get_post_meta($sidebar_id, 'qode_choose-sidebar', true);
			}else{
				if (is_singular("post")) {
					if($qode_options['blog_single_sidebar_custom_display'] != ""){
						$sidebar = $qode_options['blog_single_sidebar_custom_display'];
					}else{
						$sidebar = "Sidebar";
					}
				} elseif (is_singular("portfolio_page") && $qode_options['portfolio_single_sidebar_custom_display'] != ""){
					$sidebar = $qode_options['portfolio_single_sidebar_custom_display'];
				} else {
					$sidebar = "Sidebar Page";
				}
			}
			?>
				
			<?php if(function_exists('dynamic_sidebar') && dynamic_sidebar($sidebar)) : 
			endif;  ?>
            <!--sidebar content for glossary pages-->
            <div id="post_image">
            <?php
			//related image
			$related_image = get_field('related_image');
			$related_caption =  get_field('related_caption'); 
			if (!empty($related_image)) { 
			  //add related image
			  echo '<img src="'.$related_image['url'].'" alt="'.$related_caption.'"/>';
			  
			  echo '<p>' . $related_caption .'</p>'; 
			 }
			?>
            </div>
            <div id="related_glossary">
            <?php
			//related glossary pages
			$posts = get_field('relationship_posts');
			if ($posts) {
				echo '<p class="spacer"></p>';
				echo '<h3 class="related">Related Glossary Pages</h3>';
				echo '<ul class="related-list">';
				foreach($posts as $post):
				  setup_postdata($post);
				  echo '<li class="related">';
				  echo '<a href="'.get_permalink().'">'.get_the_title().'</a>';
				  echo '</li>';
				endforeach;
				echo '</ul>';
				wp_reset_postdata();
			  }		
			?>
            
            </div>
		</aside>
	</div>
