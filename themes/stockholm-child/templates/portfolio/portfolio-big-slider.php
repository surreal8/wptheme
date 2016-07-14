<?php
//get global variables
global $wp_query;
global $qode_options;
global $wpdb;

//init variables
$portfolio_images 			= get_post_meta(get_the_ID(), "qode_portfolio_images", true);
$lightbox_single_project 	= 'no';


//is lightbox turned on for single project?
if (isset($qode_options['lightbox_single_project'])) {
	$lightbox_single_project = $qode_options['lightbox_single_project'];
}

//sort portfolio images by user defined input
if (is_array($portfolio_images)){
	usort($portfolio_images, "comparePortfolioImages");
}
?>
<div> <a href="<?php echo home_url('/'); ?>" class="return_home">< Return To All Photographs</a> </div>
<div class="flexslider">
	<ul class="slides">
		<?php
        $portfolio_m_images = get_post_meta(get_the_ID(), "qode_portfolio-image-gallery", true);
        if ($portfolio_m_images){
            $portfolio_image_gallery_array=explode(',',$portfolio_m_images);
            foreach($portfolio_image_gallery_array as $gimg_id){
                $title = get_the_title($gimg_id);
                $alt = get_post_meta($gimg_id, '_wp_attachment_image_alt', true);
                $image_src = wp_get_attachment_image_src( $gimg_id, 'blog_image_in_grid' );
                if (is_array($image_src)) $image_src = $image_src[0];
                ?>
                <?php if($lightbox_single_project == "yes"){ ?>
                    <li class="slide">
                        <a class="lightbox_single_portfolio" title="<?php echo $title; ?>" href="<?php echo $image_src; ?>" data-rel="lightbox">
                            <img src="<?php echo $image_src; ?>" alt="<?php echo $alt; ?>" />
                        </a>
                        <div class="portfolio_image_title"><?php echo $title; ?></div>
                    </li>
                 <?php } else { ?>
                     <li class="slide">
                        <img src="<?php echo $image_src; ?>" alt="<?php echo $alt; ?>" />
                        <div class="portfolio_image_title"><?php echo $title; ?></div>
                    </li>
            	<?php }
            }
        }

        if (is_array($portfolio_images) && count($portfolio_images)){
			foreach($portfolio_images as $portfolio_image){
				?>

				<?php if($portfolio_image['portfolioimg'] != ""){ ?>
					<?php

					list($id, $title, $alt) = qode_get_portfolio_image_meta($portfolio_image['portfolioimg']);

					?>
                    <?php if($lightbox_single_project == "yes"){ ?>
                        <li class="slide">
                            <a class="lightbox_single_portfolio" title="<?php echo $title; ?>" href="<?php echo stripslashes($portfolio_image['portfolioimg']); ?>" data-rel="lightbox">
                                <img src="<?php echo stripslashes($portfolio_image['portfolioimg']); ?>" alt="<?php echo $alt; ?>" />
                            </a>
                            <div class="portfolio_image_title"><?php echo $title; ?></div>
                        </li>
                    <?php } else { ?>
                        <li class="slide">
                                <img src="<?php echo stripslashes($portfolio_image['portfolioimg']); ?>" alt="<?php echo $alt; ?>" />
                            <div class="portfolio_image_title"><?php echo $title; ?></div>
                        </li>
                    <?php } ?>
				<?php }else{ ?>

					<?php
					$portfolio_video_type = "";
					if (isset($portfolio_image['portfoliovideotype'])) $portfolio_video_type = $portfolio_image['portfoliovideotype'];
					switch ($portfolio_video_type){
						case "youtube": ?>
							<li class="slide">
								<iframe width="100%" src="//www.youtube.com/embed/<?php echo $portfolio_image['portfoliovideoid'];  ?>?wmode=transparent" wmode="Opaque" frameborder="0" allowfullscreen></iframe>
							</li>
							<?php	break;
						case "vimeo": ?>
							<li class="slide">
								<iframe src="//player.vimeo.com/video/<?php echo $portfolio_image['portfoliovideoid'];  ?>?title=0&amp;byline=0&amp;portrait=0" width="100%" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
							</li>
							<?php break;
						case "self": ?>
							<div class="video">
								<div class="mobile-video-image" style="background-image: url(<?php echo $portfolio_image['portfoliovideoimage']; ?>);"></div>
								<div class="video-wrap"  >
									<video class="video" poster="<?php echo $portfolio_image['portfoliovideoimage']; ?>" preload="auto">
										<?php if(!empty($portfolio_image['portfoliovideowebm'])) { ?> <source type="video/webm" src="<?php echo $portfolio_image['portfoliovideowebm']; ?>"> <?php } ?>
										<?php if(!empty($portfolio_image['portfoliovideomp4'])) { ?> <source type="video/mp4" src="<?php echo $portfolio_image['portfoliovideomp4']; ?>"> <?php } ?>
										<?php if(!empty($portfolio_image['portfoliovideoogv'])) { ?> <source type="video/ogg" src="<?php echo $portfolio_image['portfoliovideoogv']; ?>"> <?php } ?>
										<object width="320" height="240" type="application/x-shockwave-flash" data="<?php echo get_template_directory_uri(); ?>/js/flashmediaelement.swf">
											<param name="movie" value="<?php echo get_template_directory_uri(); ?>/js/flashmediaelement.swf" />
											<param name="flashvars" value="controls=true&file=<?php echo $portfolio_image['portfoliovideomp4']; ?>" />
											<img src="<?php echo $portfolio_image['portfoliovideoimage']; ?>" width="1920" height="800" title="No video playback capabilities" alt="Video thumb" />
										</object>
									</video>
								</div></div>
						<?php break;
					}
				}
			}
		}
		?>
	</ul>
</div>
<div class="two_columns_75_25 clearfix portfolio_container">
	<div class="column1">
		<div class="column_inner">
			<div class="portfolio_single_text_holder">
            	<?php
				if (get_field('artist_name')) { 
				  //add artist name
				  $artist_name =  get_field('artist_name'); 
				  $artist_info = '<p class="artistname">' . $artist_name;
				  if (get_field('artist_origin')) {
				     //add artist origin too
					 $artist_origin =  get_field('artist_origin'); 
				  	 $artist_info .= ' ('.$artist_origin.')';
				  }
				  $artist_info .= '</p>'; 
				  echo $artist_info;
				 }
				?>
				<h2 class="portfolio_single_text_title"><span><?php the_title(); ?></span></h2>
                <?php
				if (get_field('tombstone')) { 
				//tombstone info
				  $tombstone_info = get_field('tombstone'); 
				  echo '<div class="tombstone">' . $tombstone_info .'</div>'; 
				  echo '<p>&nbsp;</p>'; 
				 }
				?>
				<?php the_content(); ?>
                <p>&nbsp;</p>
                <?php
				  $posts = get_field('related_posts');
				  if ($posts) {
					echo '<p class="spacer"></p>';
					echo '<h3 class="related">Related Glossary Pages</h3>';
					echo '<ul class="related-list">';
					foreach($posts as $post):
					  setup_postdata($post);
					  echo '<li class="related">';
					  $categories = get_the_category();
 					  if ( ! empty( $categories ) ) {
					    echo '<p class="artist_cat">' . esc_html( $categories[0]->name ) . '</p>';   
					  }
					  echo '<h4>' . get_the_title() . '</h4>';
					  qode_excerpt();
					  echo '</li>';
					endforeach;
					echo '</ul>';
					wp_reset_postdata();
				  }		
				?>
			</div>
		</div>
	</div>
	<div class="column2">
		<div class="column_inner">
			<div class="portfolio_detail">
             <?php
				if (get_field('primary_sources') || get_field('other_collections') || get_field('object_research')) { 
				  echo '<ul class="related-list">';
				  	if (get_field('object_research')) {
					  echo '<li class="relatedside">';
					  echo '<h3 class="related">Object Research</h3>';
					  echo '<p>' . get_field('object_research') . '</p>'; 
				      echo '</li>';
				    }
				    if (get_field('primary_sources')) {
					  echo '<li class="relatedside">';
					  echo '<h3 class="related">Key Sources</h3>';
					  echo '<p>' . get_field('primary_sources') . '</p>'; 
				      echo '</li>';
				    }
					if (get_field('other_collections')) {
					  echo '<li class="relatedside">';
					  echo '<h3 class="related">In Other Stieglitz Collections</h3>';
					  echo '<p>' . get_field('other_collections') . '</p>'; 
				      echo '</li>';
				    }
				  echo '</ul>';
				}
				?>
            
				<?php
					//get portfolio custom fields section
					get_template_part('templates/portfolio/parts/portfolio-custom-fields');

					//get portfolio date section
					get_template_part('templates/portfolio/parts/portfolio-date');

					//get portfolio categories section
					get_template_part('templates/portfolio/parts/portfolio-categories');

					//get portfolio tags section
					get_template_part('templates/portfolio/parts/portfolio-tags');

					//get portfolio share section
					get_template_part('templates/portfolio/parts/portfolio-social');
				?>
			</div>
		</div>
	</div>
</div>