<?php

/*
 Template Name: pageofposts-eduvine_topic - Eduvine Topics pages

$Id: pageofposts-eduvine-topics.php 517 2011-08-09 19:52:42Z bellona $
*/

// Global Variables
global $post;
$post_title = $post->post_title;
$blog_url = get_bloginfo('url');
$current_post_type = get_post_type($post->ID);

get_header();



//eduvine_sidebar(get_post_type($post->ID));
/**
 * Displays the navigation sidebar for eduvine custom post types on pageofposts-eduvine
 *
 * @author Jon Bellona
 * @param $post_type 		the custom post type
 */
function display_eduvine_sidebar($post_type) {

	//VARIABLES
	//
	global $post;

	//setup individual eduvine post_type variables
	if ($post_type == 'eduvine_topic') {
		$parent_id = $post->ID;
		$topic_title = $post->post_title;
		$highlight_id = $post->ID;
	} 
	elseif ($post_type == 'eduvine_unit') {
		$topic_post = get_post(get_post_meta($post->ID, 'edu_topics', true));
		$parent_id = $topic_post->ID;
		$unit_id = $post->ID;
		$unit_title = $post->post_title;
		$topic_title = $topic_post->post_title;
		$highlight_id = $post->ID;
	}
	elseif ($post_type == 'eduvine_lesson') {
		$parent_unit_post = get_post(get_post_meta($post->ID, 'edu_units', true));
		$topic_post = get_post(get_post_meta($parent_unit_post->ID, 'edu_topics', true));
		$parent_id = $topic_post->ID;
		$unit_id = $parent_unit_post->ID;
		$unit_title = $parent_unit_post->post_title;
		$topic_title = $topic_post->post_title;
		$highlight_id = $parent_unit_post->ID;
	}
	elseif ($post_type == 'eduvine_lsection') {
		$parent_lesson_post = get_post(get_post_meta($post->ID, 'edu_lessons', true));
		$parent_unit_post = get_post(get_post_meta($parent_lesson_post->ID, 'edu_units', true));
		$topic_post = get_post(get_post_meta($parent_unit_post->ID, 'edu_topics', true));
		$parent_id = $topic_post->ID;
		$unit_id = $parent_unit_post->ID;
		$unit_title = $parent_unit_post->post_title;
		$topic_title = $topic_post->post_title;
		$highlight_id = $parent_unit_post->ID;
	}
	
	if(get_post_type($post->ID) != 'eduvine_topic') {
		// Get representative image/portrait URL
		$image_cache = get_flickr_image_cache($unit_id);
		// If we have an image URL, set the representative image string
		$rep_img = (empty($image_cache[0]['url'])) ? '' : '<img src="'. $image_cache[0]['url'] .'" id="eduvineTopicImage" alt=" '.$unit_title.' " />';
		// Navigation Unit sorting needs an array.
	}

	$sortArray = array();
	$sortArray[] = array('idx' => $parent_id, 'unit_title' => 'Overview');

	
	//DISPLAY THE EDUVINE SIDEBAR
	//
	echo '<div id="pageofpostscontainer"><div id="pageofpostsnav">';
		//if(get_post_type($post->ID) != 'eduvine_topic') echo $rep_img."\n";
		
		//collect the units for this topic and display them in a list
		// getting correct meta_value helps keep our query down and our foreach loop low in scope.
		//$unit_args = array( 'post_type' => 'eduvine_unit', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC' );
		$unit_args = array( 
			'post_type' => 'eduvine_unit', 
			'meta_key' => 'edu_topics',
			'meta_value' => $parent_id,
			'numberposts' => -1, 
			'orderby' => 'title', 
			'order' => 'ASC' 
		);
		$unit_posts = get_posts( $unit_args );

		//reorder the posts by meta_box values 'unit_post_order'
		// For all recent posts, get the date and ID, place into an array we can sort
		foreach( $unit_posts as $unit_post ) : setup_postdata($unit_post); 
			//create an array to sort with unit posts that we will use
			if(get_post_meta($unit_post->ID, 'edu_topics', true) == $parent_id) {
				$unitid = $unit_post->ID;
				$sidebar_title = get_post_meta($unitid, 'unit_post_order', true);
				$sortArray[] = array('idx' => $unitid, 'unit_title' => $sidebar_title);
			}
		endforeach;

		//we need to reset the postdata if we want to ensure proper handling of post_content after our sidebar is generated
		wp_reset_postdata();
		
		// Obtain a list of columns for multisort, create a temp var
		foreach ($sortArray as $key => $row) {
			//$idx[$key] = $row['idx'];
	   	$temp_unit_title[$key] = $row['unit_title'];
		}
		
		// Sort the data, a.k.a., reordering our posts
		array_multisort($temp_unit_title, SORT_ASC, $sortArray);
		
		//Display reordered posts on screen as navigation bar
		foreach( $sortArray as $unit_post ) {

			//need a check here for highlighting....
			if ($unit_post['idx'] == $highlight_id) {
				$highlight = ' highlight';
			}
			else{
				$highlight = '';
			}

			$unit_post_title = get_the_title($unit_post['idx']);
			echo "<div class='post-".$unit_post['idx']." '>"; //removed class: post-content no-wrap-content
			echo '<h4 id="post-'. $unit_post['idx'] . '" class="pageofpostsnavlinks' . $highlight .'"><a href="' . get_permalink($unit_post['idx']) . '">' . $unit_post['unit_title'] . ": " . $unit_post_title . "</a></h4>\n";
			echo "</div>"; // end main content of post
		}

		//places the logo below the sidebar nav
		if(get_post_type($post->ID) != 'eduvine_topic') echo $rep_img."\n";

	echo "</div>\n"; // end of pageofpostsnav

	//return post type specific data, in order to save time with database queries
	if (get_post_type($post->ID) == 'eduvine_topic') return $sortArray;
	if (get_post_type($post->ID) == 'eduvine_lsection') return $parent_lesson_post;

} //end eduvine_sidebar()


/**
 * Returns an array of posts to be displayed on 'eduvine_lesson' pages
 *
 * @author Jon Bellona
 * @param $post_id 		the 'eduvine_lesson' $post->ID
 */
function get_lesson_sections($post_id) {

	//collect the sections for this lesson and display them in a list
	//just get the sections that match our meta field data
	//$section_args = array( 'post_type' => 'eduvine_lsection', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC' );
	$section_args = array( 
			'post_type' => 'eduvine_lsection', 
			'meta_key' => 'edu_lessons',
			'meta_value' => $post_id,
			'numberposts' => -1, 
			'orderby' => 'title', 
			'order' => 'ASC' 
		);
	$section_posts = get_posts( $section_args );
	//_log(sizeof($section_posts));
	$sortArray = array();

	//reorder the posts by meta_box values 'unit_post_order'
	// For all recent posts, get the date and ID, place into an array we can sort
	foreach( $section_posts as $section_post ) : setup_postdata($section_post); 
		//only save section posts that we will use
		if(get_post_meta($section_post->ID, 'edu_lessons', true) == $post_id) {
			$id = $section_post->ID;
			$section_type = get_post_meta($id, 'lsection_type', true);
			$sortArray[] = array('idx' => $id, 'section_type' => $section_type[0]);
		}
	endforeach;

	//we need to reset the postdata if we want to ensure proper handling of post_content after our sidebar is generated
	wp_reset_postdata();
		
	// Sort and return our array 
	if(!empty($sortArray)) {

		// Obtain a list of columns for multisort
		foreach ($sortArray as $key => $row) {
			$idx[$key] = $row['idx'];
	   	$type[$key] = $row['section_type'];
		}
		// Sort the data, otherwise, reordering our posts (always put explore first if we have it)
		array_multisort($type, SORT_DESC, $sortArray);

		return $sortArray;
	}

}  //END get_lesson_sections()



/** ------------------------------------------------------------------------ **/
/** ------------ Setup Page Content Container & Display Logo --------------- **/
/** ------------------------------------------------------------------------ **/

?>
<!-- Global page stuff -->
<div id="container" >
	<div id="content" role="main">
		<!-- Places the image above the sidebar navigation -->
		<a href="<?php echo $blog_url ?>/participate/eduvine"><div id="eduvine_logo"> 
			<img src="<?php echo plugins_url() ?>/eduvine/images/eduvine-banner-skinny.png" />
		</div></a>
<?php  
	


// Is this an Eduvine topic, a unit, a lesson, or a lesson section?
	
/** ------------------------------------------------------------------------ **/
/** ----------------------- EDUVINE TOPIC PAGES ---------------------------- **/
/** ------------------------------------------------------------------------ **/

if($current_post_type == 'eduvine_topic'){

	//echo out the sidebar and save the sorted sidebar array (post ids [idx] and titles [unit_title]) 
	$units = display_eduvine_sidebar($current_post_type);

		//now display the content --> using a function?
		//	--- start of main post content section
		// 	--- default to show the first lesson.
		echo "<div class='post-".$post->ID." post-content no-wrap-content'>";
		
			// title
			echo '<div class="posttitle">' . $post->post_title . "</div>\n";
			
			// content
			$content = apply_filters('the_content', $post->post_content); 
			echo '<div id="post_content">'. $content . "</div>\n";

			// the units (dynamic)
			if (!empty($units)) {
				foreach($units as $key => $unit) {
					//only display the units, not the topic overview.
					if ($unit['unit_title'] != 'Overview') {
						//collect the unit post info to display (title, permalink, representative image, rep image size, description)
						$unit_post = get_post($unit['idx']);
						$unit_post_permalink = get_permalink($unit['idx']);
						$unit_post_image_cache = get_flickr_image_cache($unit['idx']);
						//force 200x200
						$unit_post_rep_img = (empty($unit_post_image_cache[0]['url'])) ? '' : '<img src="'. $unit_post_image_cache[0]['url'] .'" id="eduvineTopicImage" alt=" '.$unit_post->post_title.' " width="200" height="200" />';
						//$unit_post_rep_img = (empty($unit_post_image_cache[0]['url'])) ? '' : '<img src="'. $unit_post_image_cache[0]['url'] .'" id="eduvineTopicImage" alt=" '.$unit_post->post_title.' " />';
						
						$unit_post_rep_img_size = (empty($unit_post_image_cache[0]['url'])) ? '' : getimagesize($unit_post_image_cache[0]['url']);  //[0] = width, [1] = height, [3] = img tag string
						//force 200x200 styling
						$unit_post_rep_img_css = 100;
						//$unit_post_rep_img_css = (empty($unit_post_rep_img_size[1])) ? '0' : round($unit_post_rep_img_size[1] / 2);
						
						$unit_post_description = get_post_meta($unit['idx'], 'eduvine_customexcerpt', true);
						
						
						//echo '<div class="unit_overview">';
						//	echo '<a href="' . $unit_post_permalink . '"><span>' . $unit_post_rep_img . '</span></a>';
						//	echo '<a href="' . $unit_post_permalink . '"><span style=" top:' . $unit_post_rep_img_css . 'px ;">' . $unit_post->post_title . ': </span></a>';
						//	echo '<span style=" top:' . $unit_post_rep_img_css . 'px ;">' . $unit_post_description . '</span>';
						//echo '</div><h6></h6>';
						//display the unit post (odd)
						//	echo '<div class="unit_overview-right">';
						//		echo '<a href="' . $unit_post_permalink . '"><span style=" top:' . $unit_post_rep_img_css . 'px ;">' . $unit_post->post_title . ': </span></a>';
						//		echo '<span style=" top:' . $unit_post_rep_img_css . 'px ;">' . $unit_post_description . '</span>';
						//		echo '<a href="' . $unit_post_permalink . '"><span style="float:right;">' . $unit_post_rep_img . '</span></a>';
						//	echo '</div><h6></h6>';

						if ($key % 2 == 0) {
							//display the unit post (even)
							echo '<div class="unit_overview">';
								echo '<a href="' . $unit_post_permalink . '"><span>' . $unit_post_rep_img . '</span></a>';
								echo '<a href="' . $unit_post_permalink . '"><span>' . $unit_post->post_title . ': </span></a><br/>';
								echo '<span>' . $unit_post_description . '</span>';
							echo '</div><h6></h6>';
						} else {
							//display the unit post (odd)
							echo '<div class="unit_overview-right">';
								echo '<a href="' . $unit_post_permalink . '"><span style="float:right;">' . $unit_post_rep_img . '</span></a>';
								echo '<a href="' . $unit_post_permalink . '"><span>' . $unit_post->post_title . ': </span></a><br/>';
								echo '<span>' . $unit_post_description . '</span>';
							echo '</div><h6></h6>';
						}
					}
				}
			}

		echo "</div>"; // end main content of post
	echo "</div> <!-- pageofpostscontainer -->";
}


	
/** ------------------------------------------------------------------------ **/
/** ----------------------- EDUVINE UNIT PAGES ----------------------------- **/
/** ------------------------------------------------------------------------ **/

elseif($current_post_type == 'eduvine_unit'){

	display_eduvine_sidebar($current_post_type);
	
	//	--- start of main post content section
	echo "<div class='post-".$post->ID." post-content no-wrap-content'>";
	
		// title, includes unit number
		$unit_number = get_post_meta($post->ID, 'unit_post_order', true);
		echo '<div class="posttitle">' . $unit_number . ": " . $post->post_title . "</div>\n";

		// national standards link
		$post_standards = get_post_meta($post->ID, 'national_standard_url', true);
		if (!empty($post_standards)) {
			echo '<div class="poststandardlink"><p><b>National Standards: </b><a href="' . $post_standards . '" target="_blank">' . $post_standards . '</a></p>';
			echo '<h6></h6></div>';
		}
		
		// content
		$content = apply_filters('the_content', $post->post_content); 
		echo '<div id="post_content">'. $content . "</div>\n";


		//SHOW ----> Lessons as Navigation
		$this_page_title = $post->post_title;
		$this_page_id = $post->ID;
		//collect the lessons for this unit and display them as navigation links
		//$lesson_args = array( 'post_type' => 'eduvine_lesson', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC' );
		$lesson_args = array( 
			'post_type' => 'eduvine_lesson', 
			'meta_key' => 'edu_units',
			'meta_value' => $this_page_id,
			'numberposts' => -1, 
			'orderby' => 'title', 
			'order' => 'ASC' 
		);

		$lesson_posts = get_posts( $lesson_args );
		
		//_log(sizeof($lesson_posts));

		$sortedLessons = array();
		//reorder the posts by meta_box values 'lesson_post_order'
		// For all recent posts, get the date and ID, place into an array we can sort
		foreach( $lesson_posts as $lesson_post ) : setup_postdata($lesson_post); 
			//only save lesson posts that we will use
			if(get_post_meta($lesson_post->ID, 'edu_units', true) == $this_page_id) {
				$id = $lesson_post->ID;
				$sidebar_title = get_post_meta($id, 'lesson_post_order', true);
				$sortedLessons[] = array('idx' => $id, 'lesson_title' => $sidebar_title);
			}
		endforeach;
		
		//sort and display if we have any lessons to display
		if (!empty($sortedLessons)) {
			// Obtain a list of columns for multisort
			foreach ($sortedLessons as $key => $row) {
				$idx[$key] = $row['idx'];
		   	$lesson_title[$key] = $row['lesson_title'];
			}
			// Sort the data, otherwise, reordering our posts
			array_multisort($lesson_title, SORT_ASC, $sortedLessons);
			
			
			//instead of displaying the actual lesson_post link, 
			//we display the lesson's explore link.  if explore section doesn't exist,
			//we display the lesson's challenge link.  if challenge section doesn't exist,
			//we display the lesson_post link.  that's triple protection, trojan!

			echo '<div class="separate"></div>';
			foreach( $sortedLessons as $lesson_post ){
				//get lesson data
				$lesson_post_title = get_the_title($lesson_post['idx']);
				//_log($lesson_post_title);
				$lesson_post_description = get_post_meta($lesson_post['idx'], 'edu_lessons_teaser', true);
				$lesson_permalink = get_permalink($lesson_post['idx']);
				$lesson_permalink_explore = '';
				$lesson_permalink_challenge = '';
				//get lesson sections
				$lesson_sections = get_lesson_sections($lesson_post['idx']);

				if(!empty($lesson_sections)) {
					//update the lesson permalink to the explore section
					foreach ($lesson_sections as $section) {
						//$section_type = get_post_meta($section->ID, 'lsection_type', true);
						if ($section['section_type'] == 'explore') {
							$lesson_permalink_explore = get_permalink($section['idx']);
						} elseif ($section['section_type'] == 'challenge') {
							$lesson_permalink_challenge = get_permalink($section['idx']);
						}
					}
				}

				//display explore section link, fallback challenge section, then lesson post
				if ($lesson_permalink_explore != '') {
					echo '<h5 id="post-'. $lesson_post['idx'] . '" ><a href="' . $lesson_permalink_explore . '">' . $lesson_post['lesson_title'] . ": " . $lesson_post_title . "</a></h5>\n";
				} elseif ($lesson_permalink_challenge != '') {
					echo '<h5 id="post-'. $lesson_post['idx'] . '" ><a href="' . $lesson_permalink_challenge . '">' . $lesson_post['lesson_title'] . ": " . $lesson_post_title . "</a></h5>\n";
				} else {
					echo '<h5 id="post-'. $lesson_post['idx'] . '" ><a href="' . $lesson_permalink . '">' . $lesson_post['lesson_title'] . ": " . $lesson_post_title . "</a></h5>\n";
				}

				echo '<p>' . $lesson_post_description . '</p>';
			}
		}

	echo "</div>"; // end main content of post
?>

</div> <!-- pageofpostscontainer -->

<?php	
} //end Eduvine Units
	
	
	
/** ------------------------------------------------------------------------ **/
/** ---------------------- EDUVINE LESSON PAGES ---------------------------- **/
/** ------------------------------------------------------------------------ **/

elseif($current_post_type == 'eduvine_lesson') {	

	//User doesn't have an explore or challenge post. Display what you've got (no comments)
	
	// sidebar
	display_eduvine_sidebar($current_post_type);
	
	//
	//	--- start of main post content section

	echo "<div class='post-".$post->ID." post-content no-wrap-content'>";
	
		// title
		//echo '<div class="posttitle">' . $post->post_title . "</div>\n";
		// title, includes lesson number
		$lesson_number = get_post_meta($post->ID, 'lesson_post_order', true);
		echo '<div class="posttitle">' . $lesson_number . ": " . $post->post_title . "</div>\n";
		
		// national standards link
		//$post_standards = get_post_meta($post->ID, 'national_standard_url', true);
		//if (!empty($post_standards)) {
		//	echo '<div class="poststandardlink"><p><b>National Standards: </b><a href="' . $post_standards . '">' . $post_standards . '</a></p>';
		//	echo '<h6></h6></div>';
		//}
		
		// content
		$content = apply_filters('the_content', $post->post_content);
		echo '<div id="post_content">'. $content . "<h6></h6></div>\n";
		
		//display the media player here if available
		insert_mediaplayer($post->ID);

		//display the explore and the challenge information, if it exists
		//if we are here we shouldn't have any explore or challenge sections, 
		//but they could get here with a direct url call

		//
		//1. get the explore and challenge section
		$lesson_sections = get_lesson_sections($post->ID);
		$section_posts = array();

		//2. display the tabs
		echo '<div id="lesson_section_tabs">';
		if (!empty($lesson_sections)) {

			foreach( $lesson_sections as $section ) {

				//need a check here for highlighting.... right now it defaults to explore
				if ($section['section_type'] == 'explore') {
					$highlight = ' highlight';
				}
				else{
					$highlight = '';
				}
				echo "<div class='section-tab tab-".$section['idx']." ' >"; //removed class: post-content no-wrap-content
				echo '<div id="tab-'. $section['idx'] . '" class="pageofpostsnavlinks' . $highlight .'"><a href="' . get_permalink($section['idx']) . '">' . strtoupper($section['section_type']) . "</a></div>\n";
				
					//$content = apply_filters('the_content', $unit_post->post_content); 
					//echo '<div id="post_content">'. $content . "</div>\n";
				echo "</div>"; // end tab
			
				$section_posts[] = get_post($section['idx']);

			} //end foreach
		} //end !empty $lesson_sections
		echo '</div>';

		//3. show the content, show the explore section (default)
		if (!empty($section_posts)) {
			foreach( $section_posts as $section_post ) {
				$section_type = get_post_meta($section_post->ID, 'lsection_type', true);
				if ($section_type[0] == 'explore') {
					//content
					$section_content = apply_filters('the_content', $section_post->post_content);
					echo "<div id='post-".$section_post->ID." ' class='post-content'>";
						echo $section_content;
					echo "</div>"; // end main content of post
				}
			}
		} //end $section_posts

		//4. don't show comments
		/*
		
		$post_explore = get_post_meta($post->ID, 'edu_lessons_explore', true);
		if($post_explore != "") {
			$post_explore = apply_filters('the_content', $post_explore);
			//$post_explore = str_replace(']]>', ']]&gt;', $post_explore);
			echo '<div id="post_explore"><h4>Explore</h4>'. $post_explore . "</div>\n"; ?>
			<!-- <div class="fb-comments" data-href="<?php $post->guid ?>" data-num-posts="2" data-width="470"></div> --> <?php 
		}

		$post_challenge = get_post_meta($post->ID, 'edu_lessons_challenge', true);
		if($post_challenge != "") {
			$post_challenge = apply_filters('the_content', $post_challenge);
			echo '<div id="post_challenge"><h4>Challenge</h4>'. $post_challenge . "</div>\n"; ?>
			<!-- <div class="fb-comments" data-href="http://deecerecords.com/projects" data-num-posts="2" data-width="470"></div>  --> <?php
		}
		*/
		//$comments = get_comments('post_id='.$post->ID);
   	//	
   	//	if($comments){
   	//		foreach($comments as $comment) :
   	//		echo '<h3>Previous Student Submission:</h3>';
		//		echo('<span class="commentAuthor"> From: <b>'.$comment->comment_author . '</b></span><p>' . $comment->comment_content .'</p>');
	  // 		endforeach;
   	//	} 
   	?>
   		
   		<!--[if lt IE 7]>
			<br/>
			<p style="background:white; color:#6A000A; padding:2px; font-size:16px; line-height:18px; border:solid black 1px;">You are using an outdated browser. 
			You will not be able to comment. Please either use a different browser or update your current web browser.</p>
		<![endif]-->
   		
		<?php
		
		
		//comments_template( '/../../plugins/eduvine/eduvine-comments.php' , true );


		echo '</div> <!-- post-ID content -->';
	echo '</div> <!-- pageofpostscontainer -->';	
} //end Eduvine Lessons (else statement)


/** ------------------------------------------------------------------------ **/
/** ------------------ EDUVINE LESSON SECTION PAGES ------------------------ **/
/** ------------------------------------------------------------------------ **/

elseif($current_post_type == 'eduvine_lsection') {

	// display sidebar, returns parent lesson post 
	$parent_lesson_post = display_eduvine_sidebar($current_post_type);



	//	-------------------------------------------------- //
	//	--------- parent lesson content section ---------- //
	//  -------------------------------------------------- //

	//wrapper div
	echo "<div class='post-".$parent_lesson_post->ID." post-content no-wrap-content'>";	
		// title
		//echo '<div class="posttitle">' . $parent_lesson_post->post_title . "</div>\n";
		// title, includes lesson number
		$lesson_number = get_post_meta($parent_lesson_post->ID, 'lesson_post_order', true);
		echo '<div class="posttitle">' . $lesson_number . ": " . $parent_lesson_post->post_title . "</div>\n";
		
		// national standards link
		//$post_standards = get_post_meta($parent_lesson_post->ID, 'national_standard_url', true);
		//if (!empty($post_standards)) {
		//	echo '<div class="poststandardlink"><p><b>National Standards: </b><a href="' . $post_standards . '">' . $post_standards . '</a></p>';
		//	echo '<h6></h6></div>';
		//}

		// content
		$content = apply_filters('the_content', $parent_lesson_post->post_content);
		echo '<div id="post_content">'. $content . "<h6></h6></div>\n";
		
		//display the media player here if available
		insert_mediaplayer($parent_lesson_post->ID);


		//	-------------------------------------------------- //
		//	--------- lesson section content section --------- //
		//  -------------------------------------------------- //

		//display the explore and the challenge information, one or both
		//
		//1. get the explore and challenge sections (while we are inside one, we need both)
		$lesson_sections = get_lesson_sections($parent_lesson_post->ID);
		$section_posts = array();
		$current_section_type = get_post_meta($post->ID, 'lsection_type', true);

		//2. display the tabs
		echo '<div id="lesson_section_tabs">';
		if (!empty($lesson_sections)) {

			foreach( $lesson_sections as $section ) {


				//check current type again the loop type. highlight the current
				if ($section['section_type'] == $current_section_type[0]) {
					$highlight = ' highlight';
				}
				else{
					$highlight = '';
				}
				echo "<div class='section-tab tab-".$section['idx']." ' >"; //removed class: post-content no-wrap-content
				echo '<div id="tab-'. $section['idx'] . '" class="pageofpostsnavlinks' . $highlight .'"><a href="' . get_permalink($section['idx']) . '">' . strtoupper($section['section_type']) . "</a></div>\n";
				echo "</div>"; // end tab
			
				$section_posts[] = get_post($section['idx']);

			} //end foreach
		} //end !empty $lesson_sections
		echo '</div>';

		//3. show the section content
		//content
		$section_content = apply_filters('the_content', $post->post_content);
		echo "<div id='post-".$post->ID." ' class=''>";
			echo $section_content;
		echo "</div>"; // end main content of post

		//display the media player here if available
		insert_mediaplayer($post->ID);
		
		//4. display the comments
		comments_template( '/../../plugins/eduvine/eduvine-comments.php' , true );


	//Close the main content section
	echo '</div> <!-- post-ID content -->';
	//close the whole shebang
	echo '</div> <!-- pageofpostscontainer -->';	
}



/** ------------------------------------------------------------------------ **/
/** ------------------ MEDIAPLAYER INLINE JAVASCRIPT ----------------------- **/
/** ------------------------------------------------------------------------ **/

get_footer();

?>

<!--[if gte IE 8]>
 

 <script type="text/javascript">
 
 //These functions deal specifically with the MP on IE 6,7,8
 // originally written by Mark Hazen, reformatted for Page of Posts by Jon Bellona
	
			$jQ(document).ready(function () {
				mp_init();
			});
			
 </script>
 <script type="text/javascript"> 
	
	//HUGE FIX FOR IE8 only!!!!  DO NOT DELETE!!!
	//an seemingly unneeded open OR closing parenthesis actually fixes mp tooltips and alignment for ie8, but breaks the MP in IE7 and IE6.
	//I can only get it to print browswer specific with an html if statement, which means repeat code on this page for IE7/IE6.
			//  (  <-- the fix, in essence, it breaks the rest of this code from calling in IE8????
			
	// let's get that data var id instantiated in CDATA.  dynamic id per page!
	 var playerid;
	 //php creates an Object, not a string, but this is ok.
	 playerid = <?php echo 'mp'.$post->ID.'data'; ?>;

	var current_slide_idx=0;
	
	// Rescale (with aspect) and repad an image to fit a particular size and be centered within
	// 
	// @param elem the image element to scale
	// @param container_x maximum pixel width
	// @param container_y maximum pixel height
	// @param image_should_fill boolean if true, image will be stretched as much as needed to fill in both directions while retaining aspect ratio
	// 
	// @author Mark Hazen <mhazen@uoregon.edu>
	//
	function scale_image_to_fit (elem, container_x, container_y, image_should_fill) {
		
		check_for_src = $jQ(elem).attr('src'); 
		if (typeof(check_for_src)=='undefined' || check_for_src==false) {
			// not an image
			//
    		return;
		}

		$jQ("<img/>")
		.attr("src", $jQ(elem).attr("src"))
		.load(function() {
	
			img_x = this.width;
			img_y = this.height;
			
			ratio = img_y/container_y;
			ratio_x = img_x/container_x;
	
			if (image_should_fill) {
				if (ratio_x < ratio) {
					ratio = ratio_x;
				}
			} else {
				if (ratio_x >= ratio) {
					ratio = ratio_x;
				}
			}

			new_height = parseInt(img_y/ratio);
			new_width = parseInt(img_x/ratio);
	
			padding_x  = parseInt((container_x - new_width)/2);
			padding_y  = parseInt((container_y - new_height)/2);
	
			$jQ(elem).css('padding-top', padding_y);
			$jQ(elem).css('padding-left', padding_x );
			$jQ(elem).css('height', new_height);
			$jQ(elem).css('width', new_width);

		}).each(function(){
			// IE doesn't trigger the load event if an image was previously cached.
			// So, if we've got a 'complete' status or if we're dealing with IE6
			// (which never fires), manually fire the 'load' event.  
			// 
			if (this.complete || ($jQ.browser.msie && parseInt($jQ.browser.version) >= 7)) {
				
				$jQ(this).trigger("load");
				//do not add padding to thumbs of videos!
				if ( $jQ(this).attr('tagName') == 'IMG' ) {
					$jQ(elem).css('padding-left', padding_x+20 );
				}
				
			}
		});
	};
	
	
		
	// Code for page media player
	//
	//
	function mp_init () {	
		// Initalize jQuery cycle
		//
		//alert('ie 8 initialized');
	
		/* sets max-width for IE */
		var cssBefore = "";
		if (($jQ.browser.msie && parseInt($jQ.browser.version) >= 7)) {
			cssBefore = "height: 396px; width: 640px; align: center;";
			fit_browser = false;
			mp_width = 600;
		} else {
			cssBefore = "max-height: 396px; max-width: 640px; align: center;";
			fit_browser = false;
			mp_width = 640;
		}
		
	    $jQ('.mediaplayer').each(function (index,element) {
	    		fit_browser = false;
		    	$jQ(this).cycle({
					fx: 'scrollHorz',
					height: 396,
					width: 640,
					fit: false,
					speed: 300,
					timeout: 0,
					before: function(currSld,nextSld,opt) {
						var needs_to_fit=false;
						scale_image_to_fit(this,mp_width,396,fit_browser);
						$jQ('#mp_title').html(playerid['title'][opt.nextSlide]);
						$jQ('#mpcaption').html(playerid['caption'][opt.nextSlide]);
					},
					cssBefore: '' + cssBefore + '',
					cssAfter: '' + cssBefore + '',
					prev: $jQ(this).parent().find('.mp_prev_control'),
					next: $jQ(this).parent().find('.mp_next_control'),
					after: ie_slide
				});
		    });
	    

			// Set up thumbnail click actions
			//
			$jQ(".ie_alignment").each(function(idx) {
				$jQ(this).click(function() {
				
					
					// Remove and re-add video -- for the div class='showme' (single)
					var clone = $jQ('div[class|="showme"]').find('object').clone(true);
					$jQ('div[class|="showme"]').find('object').remove();
					$jQ('div[class|="showme"]').html(clone);
					
					// Remove and re-add video -- for div class='hideme' (multiple)
					var clones = $jQ('div[class|="hideme"]');
					$jQ.each(clones, function (index) {
						var clone_extras = $jQ(this).find('object').clone(true); 
						$jQ(this).find('object').remove();
						$jQ(this).html(clone_extras);
					});
					
					
					// Now cycle the next element
					$jQ('.mediaplayer').cycle(idx);
					current_slide_idx=idx;
					
					//display the nextSlideElement caption
					//the media player's CDATA id
					post_mpid = $jQ('.mediaplayer').attr('id') + 'data';
					
					funcCall_title = post_mpid + "['title'][" + idx + "];";
					title = eval(funcCall_title);
					funcCall_caption = post_mpid + "['caption'][" + idx + "];";
					caption = eval(funcCall_caption);
					if (caption == '') {
						$jQ('.mpcaption').html(title);
					} else {
						$jQ('.mpcaption').html(title + '<br/>' + caption);
					}
					
				});
			});
			
			
		    // Set first caption for each player
		    //
		    $jQ('div[class="mpcontainer"]').find('div[class="mediaplayer"]').each(function () {
				post_mpid = $jQ('.mediaplayer').attr('id') + 'data';
		    	funcCall_caption1 = post_mpid + "['caption'][" + 0 + "];";
		    	caption1 = eval(funcCall_caption1);
				$jQ('.mpcaption').html(caption1);
		    });
		    		    
			// Create hover action for previous/next panels
			//
			$jQ('.mediaplayer').each(function (idx,player_element) {
				var this_player_id = $jQ(player_element).attr('id'); 
				var is_animating = false;

				$jQ(this).parents('.mpcontainer').hover(
					function () {
						// For the reveal, don't trigger the animation if we're already animating.
						// (hide doesn't need a bypass)
						//
						if (is_animating) return;
						
						is_animating = true;
						$jQ('#'+this_player_id+'prev').stop().animate({ opacity: 1, left: "0" }, 150,'swing' );
						$jQ('#'+this_player_id+'next').stop().animate({ opacity: 1, right: "0" }, 150,'swing', function () { is_animating=false;} );
					},
					function () {
						is_animating = true;
						$jQ('#'+this_player_id+'prev').stop().animate({ opacity: 0, left: "0" }, 150,'swing' );
						$jQ('#'+this_player_id+'next').stop().animate({ opacity: 0, right: "0" }, 150,'swing',function () { is_animating=false;} );
					});

				});
		    		    
		    // Reset jQuery elements on reload/back button
		    //
			$jQ(window).unload( function () {

			} );
		    
		};   // end of front page media player fpmp_init function and vars
		
		
		//callback after prev/next buttons in Slideshow
		//
		function ie_slide(currSlideElement, nextSlideElement, opt, forwardFlag) {
			
			//return if not IE
			if (!($jQ.browser.msie)) {
				return;
			}
			
			
			// Remove and re-add video -- for the div class='showme' (single)
				var clone = $jQ('div[class|="showme"]').find('object').clone(true);
				$jQ('div[class|="showme"]').find('object').remove();
				$jQ('div[class|="showme"]').html(clone);
				
			// Remove and re-add video -- for div class='hideme' (multiple)
				var clones = $jQ('div[class|="hideme"]');
				$jQ.each(clones, function (index) {
					var clone_extras = $jQ(this).find('object').clone(true); 
					$jQ(this).find('object').remove();
					$jQ(this).html(clone_extras);
				});
			
			
			//resize images only IE7 and up
			if (($jQ.browser.msie) && (parseInt($jQ.browser.version) >= 7) ) {
				//alert('width: ' + $jQ(nextSlideElement).attr('width') + ', height: ' + $jQ(nextSlideElement).attr('height') );		
				temp_width = $jQ(nextSlideElement).attr('width');
	    		temp_height = $jQ(nextSlideElement).attr('height');
	    		temp_image = $jQ(nextSlideElement);
	    		if (($jQ.browser.msie) && (parseInt($jQ.browser.version) >= 9) ) {
	    			mp_width = 640;
	    		} else {
	    			mp_width = 600;
	    		}
	    		
	    		if (temp_width >= temp_height) {
		    			fit_browser = false;
		    		} else {
		    			fit_browser = false;
		    		}
		    		
		    	if ((parseInt($jQ.browser.version) >= 7)) {
					scale_image_to_fit(temp_image,mp_width,396,fit_browser);
				}
			}
			
			//display the nextSlideElement caption
			//the media player's CDATA id
					post_mpid = $jQ('.mediaplayer').attr('id') + 'data';
					funcCall_caption = post_mpid + "['caption'][" + opt.currSlide + "];";
					caption = eval(funcCall_caption);
					$jQ('.mpcaption').html(caption);
					funcCall_title = post_mpid + "['title'][" + opt.currSlide + "];";
					title = eval(funcCall_title);

					if (caption == '') {
						$jQ('.mpcaption').html(title);
					} else {
						$jQ('.mpcaption').html(title + '<br/>' + caption);
					}
		}
		
		
</script>
<![endif]-->

<!--[if IE 7]>

 <script type="text/javascript">
 
 //These functions deal specifically with the MP on IE 6,7
 // originally written by Mark Hazen, reformatted for Page of Posts by Jon Bellona

			$jQ(document).ready(function () {
				mp_init();
			});
			
</script>

<script type="text/javascript">		
	
	// let's get that data var id instantiated in CDATA.  dynamic id per page!
	 var playerid;
	 //php creates an Object, not a string, but this is ok.
	 playerid = <?php echo 'mp'.$post->ID.'data'; ?>;

	var current_slide_idx=0;
	
	// Rescale (with aspect) and repad an image to fit a particular size and be centered within
	// 
	// @param elem the image element to scale
	// @param container_x maximum pixel width
	// @param container_y maximum pixel height
	// @param image_should_fill boolean if true, image will be stretched as much as needed to fill in both directions while retaining aspect ratio
	// 
	// @author Mark Hazen <mhazen@uoregon.edu>
	//
	function scale_image_to_fit (elem, container_x, container_y, image_should_fill) {
		
		check_for_src = $jQ(elem).attr('src'); 
		if (typeof(check_for_src)=='undefined' || check_for_src==false) {
			// not an image
			//
    		return;
		}

		$jQ("<img/>")
		.attr("src", $jQ(elem).attr("src"))
		.load(function() {
	
			img_x = this.width;
			img_y = this.height;
			
			ratio = img_y/container_y;
			ratio_x = img_x/container_x;
	
			if (image_should_fill) {
				if (ratio_x < ratio) {
					ratio = ratio_x;
				}
			} else {
				if (ratio_x >= ratio) {
					ratio = ratio_x;
				}
			}

			new_height = parseInt(img_y/ratio);
			new_width = parseInt(img_x/ratio);
	
			padding_x  = parseInt((container_x - new_width)/2);
			padding_y  = parseInt((container_y - new_height)/2);
	
			$jQ(elem).css('padding-top', padding_y);
			$jQ(elem).css('padding-left', padding_x );
			$jQ(elem).css('height', new_height);
			$jQ(elem).css('width', new_width);

		}).each(function(){
			// IE doesn't trigger the load event if an image was previously cached.
			// So, if we've got a 'complete' status or if we're dealing with IE6
			// (which never fires), manually fire the 'load' event.  
			// 
			if (this.complete || ($jQ.browser.msie && parseInt($jQ.browser.version) <= 7)) {
				$jQ(this).trigger("load");
				
				//need not to add padding to thumbs of videos!
				if ( $jQ(this).attr('tagName') == 'IMG' ) {
					$jQ(elem).css('padding-left', padding_x+20 );
					//alert($jQ(this).attr('tagName'));
					//printObject(this);
				}
			}
		});
	};
	
	function printObject(o) {
		  var out = '';
		  for (var p in o) {
		    out += p + ': ' + o[p] + '\n';
		  }
		  alert(out);
	}
		
	// Code for page media player
	//
	//
	function mp_init () {	
		// Initalize jQuery cycle
		//
		//alert('ie 7 initialized');
	
		/* sets max-width for IE */
		var cssBefore = "";
		if (($jQ.browser.msie && parseInt($jQ.browser.version) <= 7)) {
			cssBefore = "height: 396px; width: 640px; align: center;";
			fit_browser = false;
			mp_width = 600;
		} else {
			cssBefore = "max-height: 396px; max-width: 640px; align: center;";
			fit_browser = false;
			mp_width = 640;
		}
		//post_mpid = $jQ('.mediaplayer').attr('id') + 'data';
		
	    $jQ('.mediaplayer').each(function (index,element) {
	    		fit_browser = false;
		    	$jQ(this).cycle({
					fx: 'scrollHorz',
					height: 396,
					width: 640,
					fit: false,
					speed: 300,
					timeout: 0,
					before: function(currSld,nextSld,opt) {
						var needs_to_fit=false;
						scale_image_to_fit(this,mp_width,396,fit_browser);
						$jQ('#mp_title').html(playerid['title'][opt.nextSlide]);
						$jQ('.mpcaption').html(playerid['caption'][opt.nextSlide]);
					},
					cssBefore: '' + cssBefore + '',
					cssAfter: '' + cssBefore + '',
					prev: $jQ(this).parent().find('.mp_prev_control'),
					next: $jQ(this).parent().find('.mp_next_control'),
					after: ie_slide
				});
		    });
	    

			// Set up thumbnail click actions
			//
			$jQ(".ie_alignment").each(function(idx) {
				$jQ(this).click(function() {
					$jQ('.mediaplayer').cycle(idx);
					current_slide_idx=idx;
					/*
					// Remove and re-add video -- for the div class='showme' (single)
					var clone = $jQ('div[class|="showme"]').find('object').clone(true);
					$jQ('div[class|="showme"]').find('object').remove();
					$jQ('div[class|="showme"]').html(clone);
					
					// Remove and re-add video -- for div class='hideme' (multiple)
					var clones = $jQ('div[class|="hideme"]');
					$jQ.each(clones, function (index) {
						var clone_extras = $jQ(this).find('object').clone(true); 
						$jQ(this).find('object').remove();
						$jQ(this).html(clone_extras);
					});
					*/
					
					//display the nextSlideElement caption
					//the media player's CDATA id
					post_mpid = $jQ('.mediaplayer').attr('id') + 'data';
					
					funcCall_title = post_mpid + "['title'][" + idx + "];";
					title = eval(funcCall_title);
					funcCall_caption = post_mpid + "['caption'][" + idx + "];";
					caption = eval(funcCall_caption);
					if (caption == '') {
						$jQ('.mpcaption').html(title);
					} else {
						$jQ('.mpcaption').html(title + '<br/>' + caption);
					}
					
				});
			});  
		    		    
			// Create hover action for previous/next panels
			//
			$jQ('.mediaplayer').each(function (idx,player_element) {
				var this_player_id = $jQ(player_element).attr('id'); 
				var is_animating = false;

				$jQ(this).parents('.mpcontainer').hover(
					function () {
						// For the reveal, don't trigger the animation if we're already animating.
						// (hide doesn't need a bypass)
						//
						if (is_animating) return;
						
						is_animating = true;
						$jQ('#'+this_player_id+'prev').stop().animate({ opacity: 1, left: "0" }, 150,'swing' );
						$jQ('#'+this_player_id+'next').stop().animate({ opacity: 1, right: "0" }, 150,'swing', function () { is_animating=false;} );
					},
					function () {
						is_animating = true;
	
						$jQ('#'+this_player_id+'prev').stop().animate({ opacity: 0, left: "0" }, 150,'swing' );
						$jQ('#'+this_player_id+'next').stop().animate({ opacity: 0, right: "0" }, 150,'swing',function () { is_animating=false;} );
					});

				});
		    		    
		    // Reset jQuery elements on reload/back button
		    //
			$jQ(window).unload( function () {

			} );
		    
		};   // end of front page media player fpmp_init function and vars
		
		
		//callback after prev/next buttons in Slideshow
		//
		function ie_slide(currSlideElement, nextSlideElement, opt, forwardFlag) {
			
			//return if not IE
			if (!($jQ.browser.msie)) {
				return;
			}
			/*
			// Remove and re-add video -- for the div class='showme' (single)
				var clone = $jQ('div[class|="showme"]').find('object').clone(true);
				$jQ('div[class|="showme"]').find('object').remove();
				$jQ('div[class|="showme"]').html(clone);
				
			// Remove and re-add video -- for div class='hideme' (multiple)
				var clones = $jQ('div[class|="hideme"]');
				$jQ.each(clones, function (index) {
					var clone_extras = $jQ(this).find('object').clone(true); 
					$jQ(this).find('object').remove();
					$jQ(this).html(clone_extras);
				});
			*/
			//resize images only IE8 and below
			if (($jQ.browser.msie) && (parseInt($jQ.browser.version) <= 7) ) {
				//alert('width: ' + $jQ(currSlideElement).attr('width') + ', height: ' + $jQ(currSlideElement).attr('height') );	
				//alert('width: ' + $jQ(nextSlideElement).attr('width') + ', height: ' + $jQ(nextSlideElement).attr('height') );		
				temp_width = $jQ(nextSlideElement).attr('width');
	    		temp_height = $jQ(nextSlideElement).attr('height');
	    		temp_image = $jQ(nextSlideElement);
	    		mp_width = 600;
	    		
	    		if (temp_width >= temp_height) {
		    			fit_browser = false;
		    		} else {
		    			fit_browser = false;
		    		}
		    		
		    	//resize vertical images doesn't work in IE7
		    	if ((parseInt($jQ.browser.version) <= 7)) {
					scale_image_to_fit(temp_image,mp_width,396,fit_browser);
				} 
			}
			
			//display the nextSlideElement caption
			//the media player's CDATA id
					post_mpid = $jQ('.mediaplayer').attr('id') + 'data';
					funcCall_caption = post_mpid + "['caption'][" + opt.currSlide + "];";
					caption = eval(funcCall_caption);
					$jQ('.mpcaption').html(caption);	
					
					funcCall_title = post_mpid + "['title'][" + opt.currSlide + "];";
					title = eval(funcCall_title);

					if (caption == '') {
						$jQ('.mpcaption').html(title);
					} else {
						$jQ('.mpcaption').html(title + '<br/>' + caption);
					}
			
		}
		
</script>
<![endif]-->

<!--[if lt IE 7]>

 <script type="text/javascript">
 
 //These functions deal specifically with the MP on IE 6
 // originally written by Mark Hazen, reformatted for Page of Posts by Jon Bellona

			$jQ(document).ready(function () {
				mp_init();
			});
 </script>
	
 <script type="text/javascript">	
	// let's get that data var id instantiated in CDATA.  dynamic id per page!
	 var playerid;
	 //php creates an Object, not a string, but this is ok.
	 playerid = <?php echo 'mp'.$post->ID.'data'; ?>;

	var current_slide_idx=0;
	
	// Rescale (with aspect) and repad an image to fit a particular size and be centered within
	// 
	// @param elem the image element to scale
	// @param container_x maximum pixel width
	// @param container_y maximum pixel height
	// @param image_should_fill boolean if true, image will be stretched as much as needed to fill in both directions while retaining aspect ratio
	// 
	// @author Mark Hazen <mhazen@uoregon.edu>
	//
	function scale_image_to_fit (elem, container_x, container_y, image_should_fill) {
		
		check_for_src = $jQ(elem).attr('src'); 
		if (typeof(check_for_src)=='undefined' || check_for_src==false) {
			// not an image
			//
    		return;
		}

		$jQ("<img/>")
		.attr("src", $jQ(elem).attr("src"))
		.load(function() {
	
			img_x = this.width;
			img_y = this.height;
			
			ratio = img_y/container_y;
			ratio_x = img_x/container_x;
	
			if (image_should_fill) {
				if (ratio_x < ratio) {
					ratio = ratio_x;
				}
			} else {
				if (ratio_x >= ratio) {
					ratio = ratio_x;
				}
			}

			new_height = parseInt(img_y/ratio);
			new_width = parseInt(img_x/ratio);
	
			padding_x  = parseInt((container_x - new_width)/2);
			padding_y  = parseInt((container_y - new_height)/2);
	
			$jQ(elem).css('padding-top', padding_y);
			$jQ(elem).css('padding-left', padding_x );
			$jQ(elem).css('height', new_height);
			$jQ(elem).css('width', new_width);

		}).each(function(){
			// IE doesn't trigger the load event if an image was previously cached.
			// So, if we've got a 'complete' status or if we're dealing with IE6
			// (which never fires), manually fire the 'load' event.  
			// 
			if (this.complete || ($jQ.browser.msie && parseInt($jQ.browser.version) <= 7)) {
				$jQ(this).trigger("load");
				
				//need not to add padding to thumbs of videos!
				if ( $jQ(this).attr('tagName') == 'IMG' ) {
					$jQ(elem).css('padding-left', padding_x+20 );
				}
			}
		});
	};
		
	// Code for page media player
	//
	//
	function mp_init () {	
		// Initalize jQuery cycle
		//
		//alert('ie 6 initialized');
	
		/* sets max-width for IE */
		var cssBefore = "";
		if (($jQ.browser.msie && parseInt($jQ.browser.version) <= 7)) {
			cssBefore = "height: 396px; width: 640px; align: center;";
			fit_browser = false;
			mp_width = 600;
		} else {
			cssBefore = "max-height: 396px; max-width: 640px; align: center;";
			fit_browser = false;
			mp_width = 640;
		}
		//post_mpid = $jQ('.mediaplayer').attr('id') + 'data';
		
	    $jQ('.mediaplayer').each(function (index,element) {
	    		fit_browser = false;
		    	$jQ(this).cycle({
					fx: 'scrollHorz',
					height: 396,
					width: 640,
					fit: false,
					speed: 300,
					timeout: 0,
					before: function(currSld,nextSld,opt) {
						var needs_to_fit=false;
						scale_image_to_fit(this,mp_width,396,fit_browser);
						$jQ('#mp_title').html(playerid['title'][opt.nextSlide]);
						$jQ('.mpcaption').html(playerid['caption'][opt.nextSlide]);
					},
					cssBefore: '' + cssBefore + '',
					cssAfter: '' + cssBefore + '',
					prev: $jQ(this).parent().find('.mp_prev_control'),
					next: $jQ(this).parent().find('.mp_next_control'),
					after: ie_slide
				});
		    });
	    

			// Set up thumbnail click actions
			//
			$jQ(".ie_alignment").each(function(idx) {
				$jQ(this).click(function() {
					$jQ('.mediaplayer').cycle(idx);
					current_slide_idx=idx;
					
					/*
					// Remove and re-add video -- for the div class='showme' (single)
					var clone = $jQ('div[class|="showme"]').find('object').clone(true);
					$jQ('div[class|="showme"]').find('object').remove();
					$jQ('div[class|="showme"]').html(clone);
					
					// Remove and re-add video -- for div class='hideme' (multiple)
					var clones = $jQ('div[class|="hideme"]');
					$jQ.each(clones, function (index) {
						var clone_extras = $jQ(this).find('object').clone(true); 
						$jQ(this).find('object').remove();
						$jQ(this).html(clone_extras);
					});
					*/
					
					//display the nextSlideElement caption
					//the media player's CDATA id
					/*post_mpid = $jQ('.mediaplayer').attr('id') + 'data';
					
					funcCall_title = post_mpid + "['title'][" + idx + "];";
					title = eval(funcCall_title);
					funcCall_caption = post_mpid + "['caption'][" + idx + "];";
					caption = eval(funcCall_caption);
					if (caption == '') {
						$jQ('.mpcaption').html("<br/>" + title);
					} else {
						$jQ('.mpcaption').html(caption);
					}*/
					
				});
			});
			  
			// Set first caption for each player
		    //
		    $jQ('div[class="mpcontainer"]').find('div[class="mediaplayer"]').each(function () {
				post_mpid = $jQ('.mediaplayer').attr('id') + 'data';
		    	funcCall_caption1 = post_mpid + "['caption'][" + 0 + "];";
		    	caption1 = eval(funcCall_caption1);
				$jQ('.mpcaption').html(caption1);
		    });
		    		    
			// Create hover action for previous/next panels
			//
			$jQ('.mediaplayer').each(function (idx,player_element) {
				var this_player_id = $jQ(player_element).attr('id'); 
				var is_animating = false;

				$jQ(this).parents('.mpcontainer').hover(
					function () {
						// For the reveal, don't trigger the animation if we're already animating.
						// (hide doesn't need a bypass)
						//
						if (is_animating) return;
						
						is_animating = true;
						$jQ('#'+this_player_id+'prev').stop().animate({ opacity: 1, left: "0" }, 150,'swing' );
						$jQ('#'+this_player_id+'next').stop().animate({ opacity: 1, right: "0" }, 150,'swing', function () { is_animating=false;} );
					},
					function () {
						is_animating = true;
						$jQ('#'+this_player_id+'prev').stop().animate({ opacity: 0, left: "0" }, 150,'swing' );
						$jQ('#'+this_player_id+'next').stop().animate({ opacity: 0, right: "0" }, 150,'swing',function () { is_animating=false;} );
					});

				});
		    		    
		    // Reset jQuery elements on reload/back button
		    //
			$jQ(window).unload( function () {

			} );
		    
		};   // end of front page media player fpmp_init function and vars
		
		
		//callback after prev/next buttons in Slideshow
		//
		function ie_slide(currSlideElement, nextSlideElement, opt, forwardFlag) {
			
			//return if not IE
			if (!($jQ.browser.msie)) {
				return;
			}
			
			/*
			// Remove and re-add video -- for the div class='showme' (single)
				var clone = $jQ('div[class|="showme"]').find('object').clone(true);
				$jQ('div[class|="showme"]').find('object').remove();
				$jQ('div[class|="showme"]').html(clone);
				
			// Remove and re-add video -- for div class='hideme' (multiple)
				var clones = $jQ('div[class|="hideme"]');
				$jQ.each(clones, function (index) {
					var clone_extras = $jQ(this).find('object').clone(true); 
					$jQ(this).find('object').remove();
					$jQ(this).html(clone_extras);
				});
			*/
			
			//resize images only IE6
			if (($jQ.browser.msie) && (parseInt($jQ.browser.version) <= 7) ) {
				//alert('width: ' + $jQ(currSlideElement).attr('width') + ', height: ' + $jQ(currSlideElement).attr('height') );	
				//alert('width: ' + $jQ(nextSlideElement).attr('width') + ', height: ' + $jQ(nextSlideElement).attr('height') );		
				temp_width = $jQ(nextSlideElement).attr('width');
	    		temp_height = $jQ(nextSlideElement).attr('height');
	    		temp_image = $jQ(nextSlideElement);
	    		mp_width = 600;
	    		fit_browser = false;
	    		
		    	scale_image_to_fit(temp_image,mp_width,396,fit_browser);
			}
			
			//display the nextSlideElement caption, using the media player's CDATA id
			//
			post_mpid = $jQ('.mediaplayer').attr('id') + 'data';
			funcCall_caption = post_mpid + "['caption'][" + opt.currSlide + "];";
			caption = eval(funcCall_caption);
			$jQ('.mpcaption').html(caption);	
			
			funcCall_title = post_mpid + "['title'][" + opt.currSlide + "];";
			title = eval(funcCall_title);

			if (caption == '') {
				$jQ('.mpcaption').html("<br/>" + title);
			} else {
				$jQ('.mpcaption').html(caption);
			}
			
		}
		
</script>
<![endif]-->