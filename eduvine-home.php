<?php

/*
 Template Name: eduvine-home

$Id: pageofposts.php 517 2011-08-09 19:52:42Z bellona $
*/

// First, get the post ID for the current post
$hasMap = false;

get_header();
?>
	
<!-- Global page stuff -->
<div id="container" >
	<div id="content" role="main">
		<!--<h1 class="entry-title"><?php echo $post->post_title ?></h1>-->
		<a href="<?php echo get_permalink($post->ID) ?>"><div id="eduvine_logo"> 
			<img src="<?php echo plugins_url() ?>/eduvine/images/eduvine-banner-skinny.png" />
		</div></a>

		<!-- Eduvine Landing Page Header/Instructions -->
		<div class="eduvine_contributeHeader">
			<?php
				global $eduvine_options_title_order, $eduvine_home_page;
				$eduvine_home_html = get_option('eduvine_home_page');

				//reset to default if empty
				if (empty($eduvine_home_html)) {
					$eduvine_home_html = $eduvine_home_page;
				}
				//now echo it out.
				$eduvine_home_html = apply_filters('add_links', $eduvine_home_html);
				//echo "<div class='post-content'>";
				echo $eduvine_home_html;
				//echo "</div>"; // end main content of post
			?>
			
				<!--<span><img id="instruction_orb" src="<?php echo plugins_url() ?>/eduvine/images/orb-1.png" width="63"  alt="orb" /></span>-->
				<span></span>
				<!--<span style="bottom: 23px; position: relative;"><b class="edu-instructions">Click on the orbs below to visit particular EduVine Topics.</b></span>-->
				<div style=""><b class="edu-instructions">Click on the orbs below to visit particular EduVine Topics.</b></div>
		</div>
		<!--<img id='instruction_orb_halo' src="<?php echo plugins_url() ?>/eduvine/images/orb-halo1.png" width="63"  alt="orb"  />-->
		<div id='instruction_orb_halo_div'></div>


		<!-- The hidden intro panel. loads in lightbox on page load. -->
		<a id="load_intro" href="#eduvine_intro" class="fancybox"></a>
		<div id='eduvine_intro' style="display:none;">
			<h2>EduVine</h2>
			<?php
				global $eduvine_options_title_order, $eduvine_terms;
				$eduvine_term_html = get_option('eduvine_terms');

				//reset to default if empty
				if (empty($eduvine_term_html)) {
					$eduvine_term_html = $eduvine_terms;
				}
				//now echo it out.
				$eduvine_term_html = apply_filters('add_links', $eduvine_term_html);
				echo $eduvine_term_html;
			?>

			<img src="<?php echo plugins_url() ?>/eduvine/images/dragon-laptop.png" width="600"  alt="EduVineTerms" />	

			<form id="eduvine_terms" method="post" action="">
				<input type="hidden" name="eduvine_terms_nonce" id="eduvine_terms_nonce" value="<?php echo wp_create_nonce( plugin_basename(__FILE__) ) ?>" />
				<input type="checkbox" id="eduvine_agreement" name="eduvine_agreement" value="Agreement" /> Don't show this message again. 
				<input type="submit" value="Continue" />
			</form>
		</div><!-- #eduvine_intro panel -->


		<!-- Eduvine Landing page content.  Image with Orbs. -->
		<!--<div id="intro-eduvine-wrapper" class="class_box_shadow">-->
			<div id="intro-eduvine" > 
				<!--<img src="<?php echo plugins_url() ?>/eduvine/images/orb-dragon-3-static.png" width="860"  alt="EduVine" />	-->
				<img src="<?php echo plugins_url() ?>/eduvine/images/orb-dragon.png" width="860"  alt="EduVine" />	

				<!-- Grab the eduvine_topic post type, iterating through to create div links -->
				<?php 
					$post_counter = 1;

					$myposts = new WP_Query(array(
						'post_type' => array('eduvine_topic'),
						'post_status' => 'publish',
						'orderby' => 'date',
						'order' => 'ASC',
						'posts_per_page' => -1
						));	

					while ($myposts->have_posts()) {
							$myposts->the_post();
				?>

						<img id="eduvine-topic-<?php echo $post_counter ?>-hoverimg" src="<?php echo plugins_url() ?>/eduvine/images/orb-dragon-halo<?php echo $post_counter ?>.png" width="860" alt="<?php the_title(); ?> Hover Image" />
						<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>">
							<div id="eduvine-topic-<?php echo $post_counter ?>" class="eduvine-orb-placeholder">&nbsp;</div>
						</a>
				<?php 
						$post_counter += 1;
					} //end while
					
				?>

				<div id="captionfull-eduvine">
	   			<div class="cover boxcaption-eduvine">

	   				<!-- Rewind the posts, reloop to spit out the HTML rollover descriptions -->
	   				<?php 
	   				
	   					$post_counter = 1;

	   					while ($myposts->have_posts()) {
	   						$myposts->the_post();
	   				?>
	   						<p id="eduvine-topic-<?php echo $post_counter ?>-title" class="captiontext-eduvine">
	   							<?php 
	   								$title = get_the_title();
	   								echo '<b class="rollover_title">' . $title . '</b><br/><br/>';
	   								$content = get_the_content();
	   								echo $content;
	   							?>
	   						</p>
	   						
	   				<?php 
	   						$post_counter += 1;
	   					} //end while
	   					wp_reset_query();
	   				?>

	   			</div>
	   		</div><!-- .boxgrid-eduvine .captionfull-eduvine -->

			</div><!-- #intro-eduvine -->
		<!--</div>--><!-- #intro-eduvine-wrapper -->

		<!-- 
		<div class="boxgrid-eduvine captionfull-eduvine"> 
			<a href="http://imgdev.uoregon.edu/bellona/chinavine2/navigation/architecture/">
				<img src="<?php echo plugins_url() ?>/eduvine/images/orb-dragon.png" width="860"  alt="EduVine" />
			</a>

   		<a href="http://imgdev.uoregon.edu/bellona/chinavine2/navigation/architecture/">
   			<div class="cover boxcaption-eduvine">
   				<p class="captiontext-eduvine">Chinese instructions for EduVine go here to explain cultural identity throughout China. 
   					Click on an orb to read more.</p>
   			</div>
   		</a>
		</div>
		-->


		<div id="eduvine_grants">
			<div id="eduvine-logo-nea" class="eduvine_logos">
				<span>This project is supported in part by an award or grant from: National Endowment for the Arts</span>
				<img src="<?php echo plugins_url() ?>/eduvine/images/NEA_logo.png" width="60" alt="National Endowment for the Arts" />
			</div>
			<div id="eduvine-logo-neaf" class="eduvine_logos">
				<span>National Art Education Foundation</span>
				<img src="<?php echo plugins_url() ?>/eduvine/images/NAEF_logo.png" width="80" alt="National Art Education Foundation" />
			</div>
		</div><!-- #eduvine_grants -->

	</div><!-- #content -->



</div><!-- #container -->



<?php get_footer(); ?>