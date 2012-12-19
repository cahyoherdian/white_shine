<?php

//
//  Custom Child Theme Functions
//

// I've included a "commented out" sample function below that'll add a home link to your menu
// More ideas can be found on "A Guide To Customizing The Thematic Theme Framework" 
// http://themeshaper.com/thematic-for-wordpress/guide-customizing-thematic-theme-framework/

// Adds a home link to your menu
// http://codex.wordpress.org/Template_Tags/wp_page_menu
//function childtheme_menu_args($args) {
//    $args = array(
//        'show_home' => 'Home',
//        'sort_column' => 'menu_order',
//        'menu_class' => 'menu',
//        'echo' => true
//    );
//	return $args;
//}
//add_filter('wp_page_menu_args','childtheme_menu_args');

// Unleash the power of Thematic's dynamic classes
// 
// define('THEMATIC_COMPATIBLE_BODY_CLASS', true);
// define('THEMATIC_COMPATIBLE_POST_CLASS', true);

// Unleash the power of Thematic's comment form
//
// define('THEMATIC_COMPATIBLE_COMMENT_FORM', true);

// Unleash the power of Thematic's feed link functions
//
// define('THEMATIC_COMPATIBLE_FEEDLINKS', true);

	function wicked_favicon() {
		echo '<link rel="shortcut icon" href="'
		. get_bloginfo('stylesheet_directory')
		. '/images/favicon.ico"/>';
	}
	add_action('wp_head', 'wicked_favicon');

	/*
	* Featured custom homepage/ index page loop
	*
	*/
	function ws_indexloop() {
		query_posts("posts_per_page=5");
		$counter = 1;
		if (have_posts()) : while (have_posts()) : the_post(); 
		$my_day = get_the_date('d');
		$my_month = get_the_date('M');
		$my_year = get_the_date('Y');
			if ($counter == 1 && !is_paged()) {//for first post						
				?><div id="featured-home"  class='<?php echo " fh".$counter."'>"?>
					<div class="featured-top">
						<div class="featured-ribbon"></div>			
						<div class="top-title"><?php childtheme_featured_postheader(); ?></div>
					</div>
					<div class="featured-center">
						<div class="featured-top-image">
						<?php
							if(has_post_thumbnail()){ 
								the_post_thumbnail('homepage-thumbnail');
							}else{						
								echo '<img src="'
								. get_bloginfo('stylesheet_directory')
								. '/images/default-home-thumbnail.png" alt="home thumbnail" title="home thumbnail"/>';
							}//end of else
						?>
						</div>				
						<?php add_filter('excerpt_length', 'featured_excerpt_length');?>
						<div class="featured-content"><?php 
						echo "<div id='featured-top-meta'>".$my_month." ".$my_day.", ".$my_year."</div>"; 
						the_excerpt(); 
						?>						
						</div>
					</div>
					<div class="top-liner"></div>
				</div>
				<div class="featured-spacer">
					<div class="other-ribbon"></div>
				</div>
				<?php
				$counter++;
			}else{
			?><div id="featured-bottom" class='fh<?php echo $counter ?>'>
			<?php
			echo "<div id='mini-entry-date'>".$my_month." ".$my_day.", ".$my_year."</div>"; 
				childtheme_featured_postheader();
				add_filter('excerpt_length', 'mini_excerpt_length');
				the_excerpt(); 
			echo "<div id='mini-read-more'>";?><a href="<?php the_permalink(); ?>">read more...</a><?php echo "</div>"; 	
			?></div><?php
			$counter++;		
		}
		endwhile; else:				
		//no post error
		?>
			<h2>Upss</h2>
			<p>There are no posts to show!</p>
		<?php
		endif;
		wp_reset_query();
	}
	
	// Featured post header
	function childtheme_featured_postheader(){	 	   
		global $post;
 	   	$postheader = thematic_postheader_posttitle();  
		echo apply_filters( 'childtheme_featured_postheader', $postheader );
	}
	
	function featured_date_display() {
		$the_month = the_date('M');
		$the_day = the_date('d');
		$the_featured_date_display = $the_month;
		$the_featured_date_display .='<br>';
		$the_featured_date_display .=$the_day;
		return $the_featured_date_display; 
	}	
	
	// Override post meta header
	function childtheme_override_postheader_postmeta(){
		$postmeta = '<div class="entry-meta">';
		$postmeta .= "Posted by : ";
	    $postmeta .= thematic_postmeta_authorlink();
		$postmeta .= " | on ";
	    $postmeta .= childtheme_override_postmeta_entrydate();  
	    $postmeta .= thematic_postmeta_editlink();	                   
	    $postmeta .= "</div><!-- .entry-meta -->\n";
	    
	    return apply_filters('childtheme_override_postheader_postmeta',$postmeta); 
	}
	
	// Override entry date for post meta
	function childtheme_override_postmeta_entrydate() {
	
	    $entrydate .= '<span class="entry-date"><abbr class="published" title="';
	    $entrydate .= get_the_time(thematic_time_title()) . '">';
		$entrydate .= get_the_time(thematic_time_display());
 	    /* $entrydate .= my_time_display(); */
	    $entrydate .= '</abbr></span>';
	    
	    return apply_filters('childtheme_override_postmeta_entrydate', $entrydate);  

	} // end postmeta_entrydate()


	// Override edit link for post meta
	function childtheme_override_postmeta_editlink() {    
	    // Display edit link
	    if (current_user_can('edit_posts')) {
		
	        $editlink = ' <span class="meta-sep meta-sep-edit"></span> ' . 
			'<span class="edit">' . thematic_postheader_posteditlink() . 
			'</span>';
			
	        return apply_filters('thematic_post_meta_editlink', $editlink);
	    } 
	} // end postmeta_editlink
	
	// Override author link for post meta
	function childtheme_override_postmeta_authorlink() {
	    
	    global $authordata;
	
	    $authorlink .= '<span class="author vcard">'. '<a class="url fn n" href="';
	    $authorlink .= get_author_posts_url($authordata->ID, $authordata->user_nicename);
	    $authorlink .= '" title="' . __('View all posts by ', 'thematic') . get_the_author_meta( 'display_name' ) . '">';
	    $authorlink .= get_the_author_meta( 'display_name' );
	    $authorlink .= '</a></span>';
	    
	    return apply_filters('childtheme_override_postmeta_authorlink', $authorlink);
	} // end postmeta_authorlink()
	
	// Filter to create the time displayed in Post Header
	function my_time_display() {

	$my_date = the_date('M d','<div id="custom_date_display" class="date_display">','</div>'); 
	$time_display = $my_date;
	
	// Filters should return correct 
	$time_display = apply_filters('my_time_display', $time_display);
	
	return $time_display;
	} // end time_display

	// The childtheme_override_single_post Single Post

	function childtheme_override_single_post() { 
		
				thematic_abovepost(); ?>
			
				<div id="post-<?php the_ID();
					echo '" ';
					if (!(THEMATIC_COMPATIBLE_POST_CLASS)) {
						post_class();
						echo '>';
					} else {
						echo 'class="';
						thematic_post_class();
						echo '">';
					}
     				thematic_postheader(); ?>
					<div class="entry-content">
					<?php thematic_content(); ?>

						<?php wp_link_pages('before=<div class="page-link">' .__('Pages:', 'thematic') . '&after=</div>') ?>
					</div><!-- .entry-content -->
					<?php thematic_postfooter(); ?>
				</div><!-- #post -->
		<?php

			thematic_belowpost();
	} // end childtheme_override_single_post

 	function mini_excerpt_length($length) {
		return 30;
	}
	
	
	function featured_excerpt_length($length) {
		return 50;
	}
	add_theme_support('post-thumbnails');
	set_post_thumbnail_size(540, 300, true);
	add_image_size('homepage-thumbnail', 250, 150, true);
?>