<?php
/*
Plugin Name: Eduvine
Plugin URI: http://imgdev.uoregon.edu
Description: Creates the Eduvine section for use on Chinavine, see chinavine.org
Author: University of Oregon - Interactive Media Group, William Myers & Jon Bellona
Version: 1.0
*/ 

//Reference code for building a WP plugin taken from
//http://mudslidedesign.co.uk/blog/theme-able-wordpress-error-pages

// Update 2015-02
// Admins and editors can now see the eduvine menus.
// Chinavine does still use a more recent version of Capability Manager, but a future release will hopefully remove that dependency.
// In advance of that change, the eduvine menus were made accessible to other types of accounts. Logging in as eduvine is not necessary.
// end update.

//   Unfortunately, our plugin relies on a few things
//1. another plugin! Capability Manager 1.3.2 by Jordi Canals
//2. a specific user role!  'eduvine', which has to be created before we can use our plugin. Fudge!
//3. our china theme.  @see index.php,  header.php for code that will need to be deleted should we delete Eduvine plugin.
//4. @see pageofposts-participate.php for the place holder we used before the EduVine plugin.
//5. @see postTypes.inc.php - saving to write another custom meta box field.
//    line 869, if ( in_array($_POST['post_type'], array('post', 'villagelife', 'provincelife', 'eduvine_lesson')) ) {
//6. tiny_mce_admin_editor_options(). see @functions.php. we specify our eduvine user role to disable the Video Enhanced button.
      //can probably get rid of #6 dependency by specifying the editor options inside wp_editor() call.

//Should we delete Eduvine and redirect, you'll need to do a few things.
//1. deactivate the plugin
//2. delete code located in the china theme, index.php, header.php, functions.php
//3. change the link or have a redirect from the Site Page, 'EduVine' to the new EduVine site.

/** ------------------------------------------------------------------------ **/
/** ----------------------------- INCLUDES --------------------------------- **/
/** ------------------------------------------------------------------------ **/

// Fix plugin error relating to current_user_can throwing error to function not found
//echo(ABSPATH . 'wp-includes/pluggable.php');
require_once( ABSPATH . 'wp-includes/pluggable.php');

// We are using functions and metaboxes located in external files. 
// @see china theme, functions.php  -> all functions pulled in here via includes //include @ ("media.inc.php");
// @see postTypes.inc.php           -> new_meta_boxThumbnail()
// @see postTypes.inc.php           -> new_meta_boxesMedia()
include @ ("introPanels/eduvine-introPanels.inc.php");

/**
 * Enqueues scripts and creates plugin settings only on eduvine backend
 *
 * @author Jon Bellona
 * @link http://codex.wordpress.org/Plugin_API/Action_Reference/admin_init
 */
function myplugin_settings() {
  // Enqueue JS which is only needed on eduvine backend pages
  wp_enqueue_script('eduvine', plugins_url( '/js/eduvine-admin.js' , __FILE__ ));
}

if (current_user_can('editor')) {//changed from eduvine. add other roles?
  add_action( 'admin_init', 'myplugin_settings' );
}


/**
 * Enqueues scripts and creates plugin settings on EduVine front end
 *
 * @author Jon Bellona
 * @global $current_template (@see functions.php in china theme)
 * @link http://fancyapps.com/fancybox/#examples
 */
function eduvine_frontend_includes() {
  
  global $current_template;

  if (in_array($current_template,array('eduvine-home.php', 'pageofposts-eduvine.php')) ) {
    //navigation highlight on sub-menus
    wp_enqueue_script('eduvine-highlight', plugins_url( '/js/highlight.js', __FILE__  ) );
    //add fancyBox
    wp_register_style( 'fancybox-css', plugins_url('/packages/fancybox/source/jquery.fancybox.css?v=2.0.6', __FILE__ ) );
    wp_register_script( 'fancybox-js', plugins_url( '/packages/fancybox/source/jquery.fancybox.pack.js?v=2.0.6', __FILE__ ) );
    wp_enqueue_style( 'fancybox-css' );
    wp_enqueue_script( 'fancybox-js' );
    //add fancyBox optional helpers - button, thumbnail and/or media
    wp_register_style( 'fancybox-helper-buttons-css', plugins_url('/packages/fancybox/source/helpers/jquery.fancybox-buttons.css?v=1.0.2', __FILE__ ) );
    wp_register_script( 'fancybox-helper-buttons-js', plugins_url( '/packages/fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.2', __FILE__ ) );
    wp_register_script( 'fancybox-helper-media', plugins_url( '/packages/fancybox/source/helpers/jquery.fancybox-media.js?v=1.0.0', __FILE__ ) );
    wp_register_style( 'fancybox-helper-thumbs-css', plugins_url('/packages/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=2.0.6', __FILE__ ) );
    wp_register_script( 'fancybox-helper-thumbs-js', plugins_url( '/packages/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=2.0.6', __FILE__ ) );
    wp_enqueue_style( 'fancybox-helper-buttons-css' );
    wp_enqueue_script( 'fancybox-helper-buttons-js' );
    wp_enqueue_script( 'fancybox-helper-media' );
    wp_enqueue_style( 'fancybox-helper-thumbs-css' );
    wp_enqueue_script( 'fancybox-helper-thumbs-js' );
    //add rollover scripts
    wp_register_script( 'rollover-js', plugins_url( '/packages/rollover/jquery.rollover.js', __FILE__ ) );
    wp_register_style( 'rollover-css', plugins_url('/packages/rollover/jquery.rollover.css', __FILE__ ) );
    wp_enqueue_script( 'rollover-js' );
    wp_enqueue_style( 'rollover-css' );
    //main eduvine script
    wp_enqueue_script('eduvine', plugins_url( '/js/eduvine.js' , __FILE__ ));
    wp_register_style( 'eduvine-css', plugins_url('/css/eduvine.css', __FILE__ ) );
    wp_enqueue_style( 'eduvine-css' );
  }
}

add_action('get_header','eduvine_frontend_includes', 2);


/**
 * Wordpress debugging _log function call.
 * 
 * Writes log lines into wp-content/debug.log with a timestamp. 
 * Arrays and objects will automatically be printed in a data dump format
 * to allow for easier debugging.  
 * 
 * @param $message mixed A string, array, or object to log
 * @author Mark Hazen
 */
if(!function_exists('_log')) {

  function _log( $message ) {
    if( WP_DEBUG === true ) {
      if( is_array( $message ) || is_object( $message ) )
        error_log( print_r( $message, true ) );
      else
        error_log( $message );
    }
  } /// end of function _log()
  
} // end of if !function_exists()


/* ---------------------------------------------------------------------------*/
/* ------------------------ USER MENU / POST TYPES ---------------------------*/
/* ---------------------------------------------------------------------------*/

/**
 * Retrieves a Post by its title
 *
 * @link http://stackoverflow.com/questions/1536682/get-wordpress-post-id-from-post-title
 * @return custom post
 */

function get_eduvine_post_by_title($page_title, $post_type, $output = OBJECT) {
    global $wpdb;
        $post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type='$post_type'", $page_title ));
        if ( $post )
            return get_post($post, $output);

    return null;
}


/**
 * Adds/Removes menu pages for eduvine users (left sidebar nav on the admin screen)
 *
 * @author Jon Bellona <bellona@uoregon.edu>
 * @dependencies china theme
 */
function eduvine_remove_menu_pages() {
  remove_menu_page('tools.php');
  remove_menu_page('edit.php?post_type=provincelife');
  remove_menu_page('edit.php?post_type=villagelife');
  remove_menu_page('edit.php?post_type=team_blog_post');
  remove_menu_page('edit.php?post_type=public_blog_post');
  remove_menu_page('edit.php');
  remove_menu_page('edit.php?post_type=page');
  remove_menu_page('options-general.php');
  remove_menu_page('edit.php?post_type=artist');
  remove_menu_page('edit.php?post_type=subject');
  remove_menu_page('edit.php?post_type=navigation');
  remove_menu_page('upload.php'); //not sure if allowed
  remove_menu_page('edit-comments.php'); //not sure if allowed
  remove_menu_page( 'plugins.php' );
  //remove_menu_page('index.php');
  //remove_menu_page('edit.php?post_type=eduvine_topic');
  //remove_menu_page('edit.php?post_type=eduvine_unit');
}
/* disabled this --ls
if (current_user_can('eduvine')) {
  add_action( 'admin_menu', 'eduvine_remove_menu_pages' );
} else {
  add_action( 'admin_menu', 'remove_eduvine_from_noneduvine_admin_menu' );
}
*/

/**
 * Removes custom post types from admin menus for non-eduvine users
 *
 * @author Jon Bellona <bellona@uoregon.edu>
 */
function remove_eduvine_from_noneduvine_admin_menu() {
  remove_menu_page('edit.php?post_type=eduvine_topic');
  remove_menu_page('edit.php?post_type=eduvine_unit');
  remove_menu_page('edit.php?post_type=eduvine_lesson');
  remove_menu_page('edit.php?post_type=eduvine_lsection');
}


/**
 * Create Custom Post Types
 * When this is created, the title is added to 
 * global $wpdb;
 *      $post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type=%v", $page_title,$village ));
 * @author Jon Bellona <bellona@uoregon.edu>
 * @global $wp_rewrite    used so we can flush the rewrite rules after instantiang our post types
 */
function create_post_type_eduvine() {
  global $wp_rewrite;

  register_post_type('eduvine_topic',
    array(
      'labels' => array(
            'name' => __('Eduvine Topics'),
            'singular_name'=> __('Eduvine Topic'),
            'add_new' => __('Add New Topic'),
      ),
      'public' => true,
      'menu_position' => 15,
      'rewrite' => array(
              'slug' => 'eduvine_topic'
      ),
      'supports' => array(
              'title', 'editor'
      ),
      'register_meta_box_cb' => 'add_eduvine_metaboxes_topic',
      '_builtin' => false
    )
  );

  register_post_type('eduvine_unit',
    array(
      'labels' => array(
            'name' => __('Eduvine Units'),
            'singular_name'=> __('Eduvine Unit'),
            'add_new' => __('Add New Unit'),
      ),
      'public' => true,
      'menu_position' => 16,
      'rewrite' => array(
              'slug' => 'eduvine_unit'
      ),
      'supports' => array(
              'title', 'editor'
      ),
      'register_meta_box_cb' => 'add_eduvine_metaboxes_unit',
      '_builtin' => false
    )
  );

  register_post_type('eduvine_lesson',
    array(
      'labels' => array(
            'name' => __('Eduvine Lessons'),
            'singular_name'=> __('Eduvine Lesson'),
            'add_new' => __('Add New Lesson'),
      ),
      'public' => true,
      'menu_position' => 17,
      'rewrite' => array(
              'slug' => 'eduvine_lesson'
      ),
      'supports' => array(
              'title', 'editor', 'comments'
      ),
      'register_meta_box_cb' => 'add_eduvine_metaboxes_lesson',
      '_builtin' => false
    )
  );

  register_post_type('eduvine_lsection',
    array(
      'labels' => array(
            'name' => __('Eduvine Lesson Activities'),
            'singular_name'=> __('Eduvine Lesson Activity'),
            'add_new' => __('Add New Lesson Activity'),
      ),
      'public' => true,
      'menu_position' => 18,
      'rewrite' => array(
              'slug' => 'eduvine_lsection'
      ),
      'supports' => array(
              'title', 'editor', 'comments'
      ),
      'register_meta_box_cb' => 'add_eduvine_metaboxes_lesson_section',
      '_builtin' => false
    )
  );

  $wp_rewrite->flush_rules();
}

add_action('wp_loaded','create_post_type_eduvine');


/**
 * 'eduvine_topic' custom posttype callback
 * 
 * @uses remove_meta_box(), add_meta_box(), new_meta_boxThumbnail()
 * @see postTypes.inc.php -> function new_meta_boxThumbnail()
 */
function add_eduvine_metaboxes_topic(){
  remove_meta_box('geo_mashup_post_edit', 'eduvine_topic', 'advanced');
}


/**
 * 'eduvine_unit' custom posttype callback, effects output of the editor screen
 * 
 * @uses remove_meta_box(), add_meta_box()
 */
function add_eduvine_metaboxes_unit(){
  add_meta_box( 'new-meta-boxesNationalStandard', 'National Standards', 'new_meta_boxNationalStandard', 'eduvine_unit', 'normal', 'high' );
  add_meta_box( 'new-meta-boxUnitThumbnail', 'Eduvine Unit Image & Unit Description', 'new_meta_boxEduvineThumbnail', 'eduvine_unit', 'normal', 'high' );
  add_meta_box( 'new-meta-boxesUnitOrder', 'Unit Title & Order', 'new_meta_boxUnitOrder', 'eduvine_unit', 'normal', 'high' );
  add_meta_box( 'new-meta-boxesEduTopics', 'Eduvine Topics', 'new_meta_boxEduvineTopic', 'eduvine_unit', 'normal', 'high' );
  
  remove_meta_box('geo_mashup_post_edit', 'eduvine_unit', 'advanced');
}


/**
 * 'eduvine_lesson' custom posttype callback
 * 
 * @uses remove_meta_box(), add_meta_box(), new_meta_boxesMedia()
 * @see postTypes.inc.php -> function new_meta_boxesMedia()
 */
function add_eduvine_metaboxes_lesson(){
  add_meta_box( 'new-meta-boxesMedia', 'Media Player Options', 'new_meta_boxesMedia', 'eduvine_lesson', 'normal', 'high' );
  add_meta_box( 'new-meta-boxesEduLessonsTeaser', 'Eduvine Lesson Short Description', 'new_meta_boxEduLessonsTeaser', 'eduvine_lesson', 'normal', 'high' );
  add_meta_box( 'new-meta-boxesLessonOrder', 'Lesson Title & Order', 'new_meta_boxLessonOrder', 'eduvine_lesson', 'normal', 'high' );
  add_meta_box( 'new-meta-boxesEduUnits', 'Eduvine Units', 'new_meta_boxEduvineUnit', 'eduvine_lesson', 'normal', 'high' );
  
  remove_meta_box('geo_mashup_post_edit', 'eduvine_lesson', 'advanced');
  remove_meta_box('commentstatusdiv', 'eduvine_lesson', 'normal');
  remove_meta_box('commentsdiv', 'eduvine_lesson', 'normal');
}


/**
 * 'eduvine_lsection' custom posttype callback
 * 
 * @uses remove_meta_box(), add_meta_box(), new_meta_boxesMedia()
 * @see postTypes.inc.php -> function new_meta_boxesMedia()
 */
function add_eduvine_metaboxes_lesson_section(){
  add_meta_box( 'new-meta-boxesMedia', 'Media Player Options', 'new_meta_boxesMedia', 'eduvine_lsection', 'normal', 'high' );
  add_meta_box( 'new-meta-boxesExploreChallenge', 'Explore or Challenge?', 'new_meta_box_lsection_type', 'eduvine_lsection', 'normal', 'high' );
  
  add_meta_box( 'new-meta-boxesEduLessons', 'Eduvine Lessons', 'new_meta_boxEduvineLesson', 'eduvine_lsection', 'normal', 'high' );
  
  remove_meta_box('geo_mashup_post_edit', 'eduvine_lsection', 'advanced');
}


/**
 * Boolean to determine if post is 'eduvine_topic' 
 *
 * @return boolean
 */
function is_eduvine_topic() {
    $post_type = get_query_var('post_type');
    return ($post_type == 'eduvine_topic' ? true : false );
}

/**
 * Boolean to determine if post is 'eduvine_unit' 
 *
 * @return boolean
 */
function is_eduvine_unit() {
    $post_type = get_query_var('post_type');
    return ($post_type == 'eduvine_unit' ? true : false );
}

/**
 * Boolean to determine if post is 'eduvine_lesson' 
 *
 * @return boolean
 */
function is_eduvine_lesson() {
    $post_type = get_query_var('post_type');
    return ($post_type == 'eduvine_lesson' ? true : false );
}

/**
 * Boolean to determine if post is 'eduvine_lsection' 
 *
 * @return boolean
 */
function is_eduvine_lsection() {
    $post_type = get_query_var('post_type');
    return ($post_type == 'eduvine_lsection' ? true : false );
}



/* ---------------------------------------------------------------------------*/
/* -------------------------- EDUVINE METABOXES ------------------------------*/
/* ---------------------------------------------------------------------------*/

/**
 * Displays eduvine topics metabox on editor screen of 'eduvine_unit' post type
 *
 * @author Jon Bellona <bellona@uoregon.edu>
 * @global $post      
 * @global $new_meta_boxes_eduvine_topic
 * @uses wp_create_nonce(), get_post_meta(), get_posts()
 */
$new_meta_boxes_eduvine_topic =
  array(
    "edu_topics" => array(
      "name" => "edu_topics",
      "std" => "",
      "title" => "Eduvine Topics",
      "description" => "Select the Eduvine Topic that this unit belongs to <em>(e.g. Topic I)</em>"
    )
  );

function new_meta_boxEduvineTopic() {
  
  // declare global access vars. post and our array
  global $post, $new_meta_boxes_eduvine_topic;
  
  // loop through array     
  foreach($new_meta_boxes_eduvine_topic as $meta_boxEduTopic) {

    $meta_box_valueEduTopic = get_post_meta($post->ID, $meta_boxEduTopic['name'], true);
     
    if($meta_box_valueEduTopic == "")
      $meta_box_valueEduTopic = $meta_boxEduTopic['std'];
    
    // hidden field to check values
    echo'<input type="hidden" name="'.$meta_boxEduTopic['name'].'_noncename" id="'.$meta_boxEduTopic['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
    // display title
    echo'<b><font size="2">'.$meta_boxEduTopic['title'].'</font></b>&nbsp;&nbsp;';
    // display description
    echo'<label for="'.$meta_boxEduTopic['name'].'">'.$meta_boxEduTopic['description'].'</label>';
    // get custom taxonomies for display
    $eduTopics_args = array('post_type'=> 'eduvine_topic', 'orderby' => 'title', 'order' => 'ASC', 'numberposts' => 1000);
    $eduTopic_post_type_list = get_posts( $eduTopics_args );
    foreach ($eduTopic_post_type_list as $eduTopic_post) {
      $eduTopic_post_type_title_Array[] = $eduTopic_post->ID;
    }
    // display selection box
    echo'<br /><select name="'.$meta_boxEduTopic['name'].'"><option value="" >Select topic</option>' .  "\n";
    foreach ($eduTopic_post_type_title_Array as $current_option) {
      $selected = ($current_option==$meta_box_valueEduTopic) ? ' selected="selected"' : '';
      echo '<option value="' . $current_option . '"' . $selected . '>'  . get_the_title($current_option) . '</option>' . "\n";
    }
    echo "\n</select>\n<br /><br />";
  }

} //end new_meta_boxEduvineTopic()


/**
 * Saves data for eduvine topics metabox
 *
 * @author Jon Bellona <bellona@uoregon.edu>
 * @global $post      
 * @global $new_meta_boxes_eduvine_topic
 * @uses wp_verify_nonce, current_user_can()
 * @uses get_post_meta(), add_post_meta(), update_post_meta(), delete_post_meta()
 */
function save_postdataEduvineTopic( $post_id ) {
  
  // need the post id and associated vars
  global $post, $new_meta_boxes_eduvine_topic;

  if (! isset($_POST['post_type'])) {
    return $post_id;
  }

  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
    return $post_id;
  
  if ( 'eduvine_unit' != $_POST['post_type'] ) {
    return $post_id;
  } else {
  
    // loop through array
    foreach($new_meta_boxes_eduvine_topic as $meta_boxEduTopic) {
       // Verify
      if ( !wp_verify_nonce( $_POST[$meta_boxEduTopic['name'].'_noncename'], plugin_basename(__FILE__) )) {
        return $post_id;
      }
       
      if ( 'page' == $_POST['post_type'] ) {
        if ( !current_user_can( 'edit_page', $post_id ))
          return $post_id;
      } else {
        if ( !current_user_can( 'edit_post', $post_id ))
          return $post_id;
      }
      
      // var to use for custom field values
      $dataEduTopic = $_POST[$meta_boxEduTopic['name']];
      
      if(get_post_meta($post_id, $meta_boxEduTopic['name']) == "")
        add_post_meta($post_id, $meta_boxEduTopic['name'], $dataEduTopic, true);
      elseif($dataEduTopic != get_post_meta($post_id, $meta_boxEduTopic['name'], true))
        update_post_meta($post_id, $meta_boxEduTopic['name'], $dataEduTopic);
      elseif($dataEduTopic == "")
        delete_post_meta($post_id, $meta_boxEduTopic['name'], get_post_meta($post_id, $meta_boxEduTopic['name'], true));
  
    } //end foreach
  } //end else

} //end save_postdataEduvineTopic( $post_id )

add_action('save_post', 'save_postdataEduvineTopic');


/**
 * Displays eduvine unit metabox on editor screen of 'eduvine_lesson' post type
 *
 * @author Jon Bellona <bellona@uoregon.edu>
 * @global $post      
 * @global $new_meta_boxes_eduvine_unit
 * @uses wp_create_nonce(), get_post_meta(), get_posts()
 */
$new_meta_boxes_eduvine_unit =
  array(
    "edu_units" => array(
      "name" => "edu_units",
      "std" => "",
      "title" => "Eduvine Units",
      "description" => "Select the Eduvine Unit that this lesson belongs to <em>(e.g. Unit 1a)</em>"
    )
  );

function new_meta_boxEduvineUnit() {
  
  // declare global access vars. post and our array
  global $post, $new_meta_boxes_eduvine_unit;
  
  // loop through array     
  foreach($new_meta_boxes_eduvine_unit as $meta_boxEduUnit) {

    $meta_box_valueEduUnit = get_post_meta($post->ID, $meta_boxEduUnit['name'], true);
     
    if($meta_box_valueEduUnit == "")
      $meta_box_valueEduUnit = $meta_boxEduUnit['std'];
    
    // hidden field to check values
    echo'<input type="hidden" name="'.$meta_boxEduUnit['name'].'_noncename" id="'.$meta_boxEduUnit['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
    // display title
    echo'<b><font size="2">'.$meta_boxEduUnit['title'].'</font></b>&nbsp;&nbsp;';
    // display description
    echo'<label for="'.$meta_boxEduUnit['name'].'">'.$meta_boxEduUnit['description'].'</label>';
    // get custom taxonomies for display
    $eduUnits_args = array('post_type'=> 'eduvine_unit', 'orderby' => 'title', 'order' => 'ASC', 'numberposts' => 1000);
    $eduUnit_post_type_list = get_posts( $eduUnits_args );
    foreach ($eduUnit_post_type_list as $eduUnit_post) {
        $eduUnit_post_type_title_Array[] = $eduUnit_post->ID;
      }
    // display selection box
    echo'<br /><select name="'.$meta_boxEduUnit['name'].'"><option value="" >Select Unit</option>' .  "\n";
    foreach ($eduUnit_post_type_title_Array as $current_option) {
      $selected = ($current_option==$meta_box_valueEduUnit) ? ' selected="selected"' : '';
      echo '<option value="' . $current_option . '"' . $selected . '>'  . get_the_title($current_option) . '</option>' . "\n";
    }
    echo "\n</select>\n<br /><br />";
  }

} //end new_meta_boxEduvineUnit()


/**
 * Saves data for eduvine unit metabox
 *
 * @author Jon Bellona <bellona@uoregon.edu>
 * @global $post      
 * @global $new_meta_boxes_eduvine_unit
 * @uses wp_verify_nonce, current_user_can()
 * @uses get_post_meta(), add_post_meta(), update_post_meta(), delete_post_meta()
 */
function save_postdataEduvineUnit( $post_id ) {
  
  // need the post id and associated vars
  global $post, $new_meta_boxes_eduvine_unit;

  if (! isset($_POST['post_type']))
    return $post_id;

  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
    return $post_id;
  
  if ( 'eduvine_lesson' != $_POST['post_type'] ) {
    return $post_id;
  } else {
  
    // loop through array
    foreach($new_meta_boxes_eduvine_unit as $meta_boxEduUnit) {
       // Verify
      if ( !wp_verify_nonce( $_POST[$meta_boxEduUnit['name'].'_noncename'], plugin_basename(__FILE__) )) {
        return $post_id;
      }
       
      if ( 'page' == $_POST['post_type'] ) {
        if ( !current_user_can( 'edit_page', $post_id ))
          return $post_id;
      } else {
        if ( !current_user_can( 'edit_post', $post_id ))
          return $post_id;
      }
      
      // var to use for custom field values
      $dataEduUnit = $_POST[$meta_boxEduUnit['name']];
      
      if(get_post_meta($post_id, $meta_boxEduUnit['name']) == "")
        add_post_meta($post_id, $meta_boxEduUnit['name'], $dataEduUnit, true);
      elseif($dataEduUnit != get_post_meta($post_id, $meta_boxEduUnit['name'], true))
        update_post_meta($post_id, $meta_boxEduUnit['name'], $dataEduUnit);
      elseif($dataEduUnit == "")
        delete_post_meta($post_id, $meta_boxEduUnit['name'], get_post_meta($post_id, $meta_boxEduUnit['name'], true));
  
    } //end foreach
    
  } //end else

} //end save_postdataEduvineUnit( $post_id )

add_action('save_post', 'save_postdataEduvineUnit');


/**
 * Displays eduvine lesson metabox on editor screen of 'eduvine_lsection' post type
 *
 * @author Jon Bellona <bellona@uoregon.edu>
 * @global $post      
 * @global $new_meta_boxes_eduvine_unit
 * @uses wp_create_nonce(), get_post_meta(), get_posts()
 */
$new_meta_boxes_eduvine_lesson =
  array(
    "edu_lessons" => array(
      "name" => "edu_lessons",
      "std" => "",
      "title" => "Eduvine Lessons",
      "description" => "Select the Eduvine Lesson that this section belongs to <em>(e.g. Insole Embroidery)</em>"
    )
  );

function new_meta_boxEduvineLesson() {
  
  // declare global access vars. post and our array
  global $post, $new_meta_boxes_eduvine_lesson;
  
  // loop through array     
  foreach($new_meta_boxes_eduvine_lesson as $meta_box) {

    $meta_box_value = get_post_meta($post->ID, $meta_box['name'], true);
     
    if($meta_box_value == "")
      $meta_box_value = $meta_box['std'];
    
    // hidden field to check values
    echo'<input type="hidden" name="'.$meta_box['name'].'_noncename" id="'.$meta_box['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
    // display title
    echo'<b><font size="2">'.$meta_box['title'].'</font></b>&nbsp;&nbsp;';
    // display description
    echo'<label for="'.$meta_box['name'].'">'.$meta_box['description'].'</label>';
    // get custom taxonomies for display
    $eduLessons_args = array('post_type'=> 'eduvine_lesson', 'orderby' => 'title', 'order' => 'ASC', 'numberposts' => 10000);
    $eduLesson_post_type_list = get_posts( $eduLessons_args );
    foreach ($eduLesson_post_type_list as $eduLesson_post) {
        $eduLesson_post_type_title_Array[] = $eduLesson_post->ID;
      }
    // display selection box
    echo'<br /><select name="'.$meta_box['name'].'"><option value="" >Select Lesson</option>' .  "\n";
    foreach ($eduLesson_post_type_title_Array as $current_option) {
      $selected = ($current_option==$meta_box_value) ? ' selected="selected"' : '';
      //grab the id based upon the post title.
      echo '<option value="' . $current_option . '"' . $selected . '>'  . get_the_title($current_option) . '</option>' . "\n";
    }
    echo "\n</select>\n<br /><br />";
  }

} //end new_meta_boxEduvineLesson()


/**
 * Saves data for eduvine lesson metabox, post_type eduvine_lsection
 *
 * @author Jon Bellona <bellona@uoregon.edu>
 * @global $post      
 * @global $new_meta_boxes_eduvine_unit
 * @uses wp_verify_nonce, current_user_can()
 * @uses get_post_meta(), add_post_meta(), update_post_meta(), delete_post_meta()
 */
function save_postdataEduvineLesson( $post_id ) {
  
  // need the post id and associated vars
  global $post, $new_meta_boxes_eduvine_lesson;

  if (! isset($_POST['post_type']))
    return $post_id;

  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
    return $post_id;
  
  if ( 'eduvine_lsection' != $_POST['post_type'] ) {
    return $post_id;
  } else {
  
    // loop through array
    foreach($new_meta_boxes_eduvine_lesson as $meta_box) {
       // Verify
      if ( !wp_verify_nonce( $_POST[$meta_box['name'].'_noncename'], plugin_basename(__FILE__) )) {
        return $post_id;
      }
       
      if ( 'page' == $_POST['post_type'] ) {
        if ( !current_user_can( 'edit_page', $post_id ))
          return $post_id;
      } else {
        if ( !current_user_can( 'edit_post', $post_id ))
          return $post_id;
      }
      
      // var to use for custom field values
      $data = $_POST[$meta_box['name']];
      
      if(get_post_meta($post_id, $meta_box['name']) == "")
        add_post_meta($post_id, $meta_box['name'], $data, true);
      elseif($data != get_post_meta($post_id, $meta_box['name'], true))
        update_post_meta($post_id, $meta_box['name'], $data);
      elseif($data == "")
        delete_post_meta($post_id, $meta_box['name'], get_post_meta($post_id, $meta_box['name'], true));
  
    } //end foreach
    
  } //end else

} //end save_postdataEduvineLesson( $post_id )

add_action('save_post', 'save_postdataEduvineLesson');



/**   
 *  Eduvine --> image Thumbnail and Excerpts
 */
$new_meta_eduvine_thumbnail =
  array(
    "eduvine_image" => array(
    "name" => "image_value",
    "std" => "",
    "title" => "Post Image",
    "description" => "Using <a href='http://flickr.com' target='blank'><em>Flickr</em></a>, upload an image and paste that photo's URL here.")
  );

$new_meta_eduvine_excerpt =
  array(
    "eduvine_customexcerpt" => array(
    "name" => "eduvine_customexcerpt",
    "stdB" => "",
    "title" => "Description",
    "description" => "Give a brief description that will be used as an introduction. <b>(Maximum 220 characters)</b> <br/> ")
  );

/**  
 * Create/Save Metabox for custom post types 
 *    --> image Thumbnail and Excerpts
 *
 * Chinavine Custom Page --- Thumbnail and Exerpt Custom Fields
 * Custom fields added to the WordPress post editors
 * 
 * @author Jon Bellona
 * @link http://wefunction.com/2008/10/tutorial-creating-custom-write-panels-in-wordpress/
 * @global $post
 * @global $new_meta_thumbnail
 * @global $new_meta_excerpt
 * @see add_artist_profile_metaboxes()
 * @see add_subject_metaboxes()
 * @see add_art_navigation_metaboxes()
 * @dependencies get_post_meta(), wp_create_nonce()
 */
function new_meta_boxEduvineThumbnail() {
  
  // declare global access vars. post and our array
  global $post, $new_meta_eduvine_thumbnail, $new_meta_eduvine_excerpt;
 
  // loop through Thumbnail Image array     
  foreach($new_meta_eduvine_thumbnail as $meta_box) {

    $meta_box_valueA = get_post_meta($post->ID, $meta_box['name'], true);
 
    if($meta_box_valueA == "")
      $meta_box_valueA = $meta_box['std'];

    // hidden field to check the values
    echo'<input type="hidden" name="'.$meta_box['name'].'_noncename" id="'.$meta_box['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
    // display title
    echo'<b><font size="2">'.$meta_box['title'].'</font></b>&nbsp;&nbsp;';
    // display description
    echo'<label for="'.$meta_box['name'].'">'.$meta_box['description'].'</label>';
    // display input box
    echo'<input type="text" name="'.$meta_box['name'].'" value="'.$meta_box_valueA.'" size="60" /><br /><br /><p></p>';
  }

  // loop through Excerpt array      
  foreach($new_meta_eduvine_excerpt as $meta_boxB) {

    $meta_box_valueB = get_post_meta($post->ID, $meta_boxB['name'], true);
 
    if($meta_box_valueB == "")
      $meta_box_valueB = $meta_boxB['stdB'];
    
    // hidden field to check the values
    echo'<input type="hidden" name="'.$meta_boxB['name'].'_noncename" id="'.$meta_boxB['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
    // display title
    echo'<b><font size="2">'.$meta_boxB['title'].'</font></b>&nbsp;&nbsp;';
    // display description
    echo'<label for="'.$meta_boxB['name'].'">'.$meta_boxB['description'].'</label>';
    // display input box
    echo'<textarea id="'.$meta_boxB['name'].'" name="'.$meta_boxB['name'].'" rows="2" cols="60" tabindex="6">'.$meta_box_valueB.'</textarea><br /><br />';
  }
} //end new_meta_boxEduvineThumbnail()


/**
 * Saves Thumbnail and Excerpt PostData of metaboxes 
 *    --> image Thumbnail and Excerpts
 * 
 * @author Jon Bellona
 * @global $post
 * @global $new_meta_thumbnail
 * @global $new_meta_excerpt
 * @uses delete_flickr_cache_if_post_updates()
 * @dependencies current_user_can(), wp_verify_nonce(), get_post_meta(), add_post_meta(), update_post_meta()
 */
function save_postdataEduvineThumbnail( $post_id ) {
  
  // need the post id and associative vars */
  global $post, $new_meta_eduvine_thumbnail, $new_meta_eduvine_excerpt;

  // loop through first array. Thumbnail Image
  foreach($new_meta_eduvine_thumbnail as $meta_box) {
    // Verify
    if (! isset($_POST[$meta_box['name'].'_noncename'])) {
      return $post_id;
    }
    
    if ( !wp_verify_nonce( $_POST[$meta_box['name'].'_noncename'], plugin_basename(__FILE__) )) {
      return $post_id;
    }
   
    if ( 'page' == $_POST['post_type'] ) {
      if ( !current_user_can( 'edit_page', $post_id ))
        return $post_id;
    } else {
      if ( !current_user_can( 'edit_post', $post_id ))
        return $post_id;
    }

    // var to use for custom field values
    $data = $_POST[$meta_box['name']];

    // If returns empty, we know this custom field has not been added before. 
    // Otherwise, update the information of the field. 
    // Else if will delete if entry is blank, so no redundancy.

    if(get_post_meta($post_id, $meta_box['name']) == "")
      add_post_meta($post_id, $meta_box['name'], $data, true);
    
    elseif($data != get_post_meta($post_id, $meta_box['name'], true)) {
      update_post_meta($post_id, $meta_box['name'], $data);
      delete_flickr_cache_if_post_updates($post_id);
    }
    
    elseif($data == "")
      delete_post_meta($post_id, $meta_box['name'], get_post_meta($post_id, $meta_box['name'], true));
    
  } //end foreach

  // loop through Excerpt array
  foreach($new_meta_eduvine_excerpt as $meta_boxB) {
    // Verify
    if ( !wp_verify_nonce( $_POST[$meta_boxB['name'].'_noncename'], plugin_basename(__FILE__) )) {
      return $post_id;
    }
   
    if ( 'page' == $_POST['post_type'] ) {
      if ( !current_user_can( 'edit_page', $post_id ))
        return $post_id;
    } else {
      if ( !current_user_can( 'edit_post', $post_id ))
        return $post_id;
    }
  
    // var to use for custom field values
    $dataB = $_POST[$meta_boxB['name']];
  
    
    if(get_post_meta($post_id, $meta_boxB['name']) == "")
      add_post_meta($post_id, $meta_boxB['name'], $dataB, true);
      
    elseif($dataB != get_post_meta($post_id, $meta_boxB['name'], true))
      update_post_meta($post_id, $meta_boxB['name'], $dataB);
      
    elseif($dataB == "")
      delete_post_meta($post_id, $meta_boxB['name'], get_post_meta($post_id, $meta_boxB['name'], true));
  
  } //end foreach

} //end save_postdataEduvineThumbnail( $post_id )

add_action('save_post', 'save_postdataEduvineThumbnail');


/*************************** GET RID OF THIS FUNCTION *******************/
/**
 * Displays eduvine lesson metabox on editor screen of 'eduvine_lesson' post type
 *
 * @author Jon Bellona <bellona@uoregon.edu>
 * @global $post      
 * @global $new_meta_page_lessons_explore
 * @global $new_meta_page_lessons_challenge
 * @uses wp_create_nonce(), get_post_meta()
 */
$new_meta_page_lessons_explore =
  array(
    "edu_lessons_explore" => array(
      "name" => "edu_lessons_explore",
      "std" => "",
      "title" => "Explore: ",
      "description" => "Enter a description for the explore portion of this lesson. (Some HTML is allowed) <br/>"
    )
  );

$new_meta_page_lessons_challenge =
  array(
    "edu_lessons_challenge" => array(
      "name" => "edu_lessons_challenge",
      "std" => "",
      "title" => "Challenge: ",
      "description" => "Enter a description for the challenge portion of this lesson. (Some HTML is allowed) <br/>"
    )
  );

function new_meta_boxEduLessons() {
  
  // declare global access vars. post and our array
  global $post, $new_meta_page_lessons_explore, $new_meta_page_lessons_challenge;
 
  // loop through array      
  foreach($new_meta_page_lessons_explore as $meta_box) {

    $meta_box_value = get_post_meta($post->ID, $meta_box['name'], true);
 
    if($meta_box_value == "")
      $meta_box_value = $meta_box['std'];
    
    // hidden field to check the values
    echo'<input type="hidden" name="'.$meta_box['name'].'_noncename" id="'.$meta_box['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
    // display title
    echo'<b><font size="2">'.$meta_box['title'].'</font></b>&nbsp;&nbsp;';
    // display description
    echo'<label for="'.$meta_box['name'].'">'.$meta_box['description'].'</label><br/>';
    // display input box
    echo'<textarea id="'.$meta_box['name'].'" name="'.$meta_box['name'].'" rows="7" cols="70" tabindex="6">'.$meta_box_value.'</textarea><br /><br />';
  }

  // loop through array      
  foreach($new_meta_page_lessons_challenge as $meta_box) {

    $meta_box_value = get_post_meta($post->ID, $meta_box['name'], true);
 
    if($meta_box_value == "")
      $meta_box_value = $meta_box['std'];
    
    // hidden field to check the values
    echo'<input type="hidden" name="'.$meta_box['name'].'_noncename" id="'.$meta_box['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
    // display title
    echo'<b><font size="2">'.$meta_box['title'].'</font></b>&nbsp;&nbsp;';
    // display description
    echo'<label for="'.$meta_box['name'].'">'.$meta_box['description'].'</label><br/>';
    // display input box
    echo'<textarea id="'.$meta_box['name'].'" name="'.$meta_box['name'].'" rows="7" cols="70" tabindex="6">'.$meta_box_value.'</textarea><br /><br />';
  }

} //end new_meta_boxEduLessons()

/*************************** GET RID OF THIS FUNCTION *******************/
/**
 * Saves data for eduvine lesson metabox
 *
 * @author Jon Bellona <bellona@uoregon.edu>
 * @global $post      
 * @global $new_meta_page_lessons_explore
 * @global $new_meta_page_lessons_challenge
 * @uses wp_verify_nonce, current_user_can()
 * @uses get_post_meta(), add_post_meta(), update_post_meta(), delete_post_meta()
 */
function save_postdataEduLessons( $post_id ) {
  
  // need the post id and associative vars */
  global $post, $new_meta_page_lessons_explore, $new_meta_page_lessons_challenge;

  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
    return $post_id;
  
  // loop through array
  foreach($new_meta_page_lessons_explore as $meta_box) {
    // Verify
    if (! isset($_POST[$meta_box['name'].'_noncename'])) {
      return $post_id;
    }
    
    if ( !wp_verify_nonce( $_POST[$meta_box['name'].'_noncename'], plugin_basename(__FILE__) )) {
      return $post_id;
    }
   
    if ( 'page' == $_POST['post_type'] ) {
      if ( !current_user_can( 'edit_page', $post_id ))
        return $post_id;
    } else {
      if ( !current_user_can( 'edit_post', $post_id ))
        return $post_id;
    }
  
    // var to use for custom field values
    $data = $_POST[$meta_box['name']];
  
    
    if(get_post_meta($post_id, $meta_box['name']) == "")
      add_post_meta($post_id, $meta_box['name'], $data, true);
      
    elseif($data != get_post_meta($post_id, $meta_box['name'], true))
      update_post_meta($post_id, $meta_box['name'], $data);
      
    elseif($data == "")
      delete_post_meta($post_id, $meta_box['name'], get_post_meta($post_id, $meta_box['name'], true));
  
  } //end foreach

  // loop through array
  foreach($new_meta_page_lessons_challenge as $meta_box) {
    // Verify
    if (! isset($_POST[$meta_box['name'].'_noncename'])) {
      return $post_id;
    }
    
    if ( !wp_verify_nonce( $_POST[$meta_box['name'].'_noncename'], plugin_basename(__FILE__) )) {
      return $post_id;
    }
   
    if ( 'page' == $_POST['post_type'] ) {
      if ( !current_user_can( 'edit_page', $post_id ))
        return $post_id;
    } else {
      if ( !current_user_can( 'edit_post', $post_id ))
        return $post_id;
    }
  
    // var to use for custom field values
    $data = $_POST[$meta_box['name']];
  
    
    if(get_post_meta($post_id, $meta_box['name']) == "")
      add_post_meta($post_id, $meta_box['name'], $data, true);
      
    elseif($data != get_post_meta($post_id, $meta_box['name'], true))
      update_post_meta($post_id, $meta_box['name'], $data);
      
    elseif($data == "")
      delete_post_meta($post_id, $meta_box['name'], get_post_meta($post_id, $meta_box['name'], true));
  
  } //end foreach

} //end save_postdataEduLessons( $post_id )

add_action('save_post', 'save_postdataEduLessons'); 



/**
 * Displays eduvine lesson metabox on editor screen of 'eduvine_lesson' post type
 * Eduvine Lesson metabox is the short lesson desciprtion (placed on unit pages)
 *
 * @author Jon Bellona <bellona@uoregon.edu>
 * @global $post      
 * @global $new_meta_page_lessons_teaser
 * @uses wp_create_nonce(), get_post_meta()
 */
$new_meta_lessons_teaser =
  array(
    "edu_lessons_teaser" => array(
      "name" => "edu_lessons_teaser",
      "std" => "",
      "title" => "Description: ",
      "description" => "Enter a brief summary of this lesson. (Will be seen on Unit pages) <br/>"
    )
  );

function new_meta_boxEduLessonsTeaser() {
  
  // declare global access vars. post and our array
  global $post, $new_meta_lessons_teaser;
 
  // loop through array      
  foreach($new_meta_lessons_teaser as $meta_box) {

    $meta_box_value = get_post_meta($post->ID, $meta_box['name'], true);
 
    if($meta_box_value == "")
      $meta_box_value = $meta_box['std'];
    
    // hidden field to check the values
    echo'<input type="hidden" name="'.$meta_box['name'].'_noncename" id="'.$meta_box['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
    // display title
    echo'<b><font size="2">'.$meta_box['title'].'</font></b>&nbsp;&nbsp;';
    // display description
    echo'<label for="'.$meta_box['name'].'">'.$meta_box['description'].'</label><br/>';
    // display input box
    echo'<textarea id="'.$meta_box['name'].'" name="'.$meta_box['name'].'" rows="4" cols="70" tabindex="6">'.$meta_box_value.'</textarea><br /><br />';
  }

} //end new_meta_boxEduLessonsTeaser()


/**
 * Saves data for eduvine lesson metabox
 *
 * @author Jon Bellona <bellona@uoregon.edu>
 * @global $post      
 * @global $new_meta_lessons_teaser
 * @uses wp_verify_nonce, current_user_can()
 * @uses get_post_meta(), add_post_meta(), update_post_meta(), delete_post_meta()
 */
function save_postdataEduLessonsTeaser( $post_id ) {
  
  // need the post id and associative vars */
  global $post, $new_meta_lessons_teaser;

  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
    return $post_id;
  
  // loop through array
  foreach($new_meta_lessons_teaser as $meta_box) {
    // Verify
    if (! isset($_POST[$meta_box['name'].'_noncename'])) {
      return $post_id;
    }
    
    if ( !wp_verify_nonce( $_POST[$meta_box['name'].'_noncename'], plugin_basename(__FILE__) )) {
      return $post_id;
    }
   
    if ( 'page' == $_POST['post_type'] ) {
      if ( !current_user_can( 'edit_page', $post_id ))
        return $post_id;
    } else {
      if ( !current_user_can( 'edit_post', $post_id ))
        return $post_id;
    }
  
    // var to use for custom field values
    $data = $_POST[$meta_box['name']];
  
    
    if(get_post_meta($post_id, $meta_box['name']) == "")
      add_post_meta($post_id, $meta_box['name'], $data, true);
      
    elseif($data != get_post_meta($post_id, $meta_box['name'], true))
      update_post_meta($post_id, $meta_box['name'], $data);
      
    elseif($data == "")
      delete_post_meta($post_id, $meta_box['name'], get_post_meta($post_id, $meta_box['name'], true));
  
  } //end foreach

} //end save_postdataEduLessonsTeaser( $post_id )

add_action('save_post', 'save_postdataEduLessonsTeaser'); 


/*************************** GET RID OF THIS FUNCTION *******************/
/**
 * Displays sidebar title metabox on post editor screen
 *
 * @author Jon Bellona <bellona@uoregon.edu>
 * @global $post      
 * @global $new_meta_page_lessons_explore
 * @uses wp_create_nonce(), get_post_meta()
 */
$meta_sidebar_post_title =
  array(
    "sidebar_post_title" => array(
      "name" => "sidebar_post_title",
      "std" => "",
      "title" => "",
      "description" => "Enter a title to be displayed in the sidebar navigation. <br/> i.e. Unit 1, Unit 2, etc. for Eduvine Units <br/> Lesson 1, Lesson 2, etc. for Eduvine Lessons <br/>"
    )
  );

function new_meta_boxSidebarTitle() {
  
  // declare global access vars. post and our array
  global $post, $meta_sidebar_post_title;
 
  // loop through array      
  foreach($meta_sidebar_post_title as $meta_box) {

    $meta_box_value = get_post_meta($post->ID, $meta_box['name'], true);
 
    if($meta_box_value == "")
      $meta_box_value = $meta_box['std'];
    
    // hidden field to check the values
    echo'<input type="hidden" name="'.$meta_box['name'].'_noncename" id="'.$meta_box['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
    // display title
    echo'<b><font size="2">'.$meta_box['title'].'</font></b>'; //&nbsp;&nbsp;
    // display description
    echo'<label for="'.$meta_box['name'].'">'.$meta_box['description'].'</label><br/>';
    // display input box
    echo'<input type="text" name="'.$meta_box['name'].'" value="'.$meta_box_value.'" size="30" /><br /><br /><p></p>';
  }

} //end new_meta_boxEduvineLessons_sidebarTitle()


/*************************** GET RID OF THIS FUNCTION *******************/
/**
 * Saves data for eduvine lesson metabox
 *
 * @author Jon Bellona <bellona@uoregon.edu>
 * @global $post      
 * @global $meta_sidebar_post_title
 * @uses wp_verify_nonce, current_user_can()
 * @uses get_post_meta(), add_post_meta(), update_post_meta(), delete_post_meta()
 */
function save_postdata_sidebar_title( $post_id ) {
  
  // need the post id and associative vars */
  global $post, $meta_sidebar_post_title;

  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
    return $post_id;
  
  // loop through array
  foreach($meta_sidebar_post_title as $meta_box) {
    // Verify
    if (! isset($_POST[$meta_box['name'].'_noncename'])) {
      return $post_id;
    }
    
    if ( !wp_verify_nonce( $_POST[$meta_box['name'].'_noncename'], plugin_basename(__FILE__) )) {
      return $post_id;
    }
   
    if ( 'eduvine_lesson' == $_POST['post_type'] ) {
      if ( !current_user_can( 'edit_page', $post_id ))
        return $post_id;
    } else {
      if ( !current_user_can( 'edit_post', $post_id ))
        return $post_id;
    }
  
    // var to use for custom field values
    $data = $_POST[$meta_box['name']];
    
    if(get_post_meta($post_id, $meta_box['name']) == "")
      add_post_meta($post_id, $meta_box['name'], $data, true);
      
    elseif($data != get_post_meta($post_id, $meta_box['name'], true))
      update_post_meta($post_id, $meta_box['name'], $data);
      
    elseif($data == "")
      delete_post_meta($post_id, $meta_box['name'], get_post_meta($post_id, $meta_box['name'], true));
  
  } //end foreach
} //end save_postdata_sidebar_title( $post_id )

add_action('save_post', 'save_postdata_sidebar_title');


/**
 * Creates drop-down metabox for 'eduvine_unit'
 * 
 * Determines a title and orders the lesson posts on a unit page.
 *
 * @author Jon Bellona <bellona@uoregon.edu>
 * @global $post      
 * @global $meta_unit_post_order
 * @uses wp_create_nonce(), get_post_meta()
 */
$meta_unit_post_order =
  array(
    "unit_post_order" => array(
      "name" => "unit_post_order",
      "std" => "",
      "title" => "",
      "description" => "Select a title for the Unit (Unit 1, Unit 2, etc.). This title also correctly sorts your units."
    )
  );

function new_meta_boxUnitOrder() {
  
  // declare global access vars. post and our array
  global $post, $meta_unit_post_order;
 
  // loop through array      
  foreach($meta_unit_post_order as $meta_box) {

    $meta_box_value = get_post_meta($post->ID, $meta_box['name'], true);
 
    if($meta_box_value == "")
      $meta_box_value = $meta_box['std'];
    
    // hidden field to check the values
    echo'<input type="hidden" name="'.$meta_box['name'].'_noncename" id="'.$meta_box['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
    // display title
    echo'<b><font size="2">'.$meta_box['title'].'</font></b>'; //&nbsp;&nbsp;
    // display description
    echo'<label for="'.$meta_box['name'].'">'.$meta_box['description'].'</label><br/>';
    // display input box
    //echo'<input type="text" name="'.$meta_box['name'].'" value="'.$meta_box_value.'" size="30" /><br /><br /><p></p>';
    // display drop down
    $title_array = array( 'Unit 1', 'Unit 2', 'Unit 3', 'Unit 4', 'Unit 5', 'Unit 6', 'Unit 7', 'Unit 8', 'Unit 9', 'Unit 10' );
    // display selection box
    echo'<br /><select name="'.$meta_box['name'].'"><option value="" >Select unit title</option>' .  "\n";
    foreach ($title_array as $current_option) {
      $selected = ($current_option==$meta_box_value) ? ' selected="selected"' : '';
      echo '<option value="' . $current_option . '"' . $selected . '>'  . $current_option . '</option>' . "\n";
    }
    echo "\n</select>\n<br />";
  }

} //end new_meta_boxUnitOrder()


/**
 * Saves data for eduvine unit metabox
 *
 * @author Jon Bellona <bellona@uoregon.edu>
 * @global $post      
 * @global $meta_unit_post_order
 * @uses wp_verify_nonce, current_user_can()
 * @uses get_post_meta(), add_post_meta(), update_post_meta(), delete_post_meta()
 */
function save_postdata_boxUnitOrder( $post_id ) {
  
  // need the post id and associative vars */
  global $post, $meta_unit_post_order;

  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
    return $post_id;
  
  // loop through array
  foreach($meta_unit_post_order as $meta_box) {
    // Verify
    if (! isset($_POST[$meta_box['name'].'_noncename'])) {
      return $post_id;
    }
    
    if ( !wp_verify_nonce( $_POST[$meta_box['name'].'_noncename'], plugin_basename(__FILE__) )) {
      return $post_id;
    }
   
    if ( 'eduvine_unit' == $_POST['post_type'] ) {
      if ( !current_user_can( 'edit_page', $post_id ))
        return $post_id;
    } else {
      if ( !current_user_can( 'edit_post', $post_id ))
        return $post_id;
    }
  
    // var to use for custom field values
    $data = $_POST[$meta_box['name']];
    
    if(get_post_meta($post_id, $meta_box['name']) == "")
      add_post_meta($post_id, $meta_box['name'], $data, true);
      
    elseif($data != get_post_meta($post_id, $meta_box['name'], true))
      update_post_meta($post_id, $meta_box['name'], $data);
      
    elseif($data == "")
      delete_post_meta($post_id, $meta_box['name'], get_post_meta($post_id, $meta_box['name'], true));
  
  } //end foreach
} //end save_postdata_boxLessonOrder( $post_id )

add_action('save_post', 'save_postdata_boxUnitOrder');


/**
 * Creates drop-down metabox for 'eduvine_lesson'
 * 
 * Determines a title and orders the lesson posts on a unit page.
 *
 * @author Jon Bellona <bellona@uoregon.edu>
 * @global $post      
 * @global $meta_lesson_post_order
 * @uses wp_create_nonce(), get_post_meta()
 */
$meta_lesson_post_order =
  array(
    "lesson_post_order" => array(
      "name" => "lesson_post_order",
      "std" => "",
      "title" => "",
      "description" => "Select a title for the Lesson (Lesson 1, Lesson 2, etc.). This title also correctly sorts your lessons."
    )
  );

function new_meta_boxLessonOrder() {
  
  // declare global access vars. post and our array
  global $post, $meta_lesson_post_order;
 
  // loop through array      
  foreach($meta_lesson_post_order as $meta_box) {

    $meta_box_value = get_post_meta($post->ID, $meta_box['name'], true);
 
    if($meta_box_value == "")
      $meta_box_value = $meta_box['std'];
    
    // hidden field to check the values
    echo'<input type="hidden" name="'.$meta_box['name'].'_noncename" id="'.$meta_box['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
    // display title
    echo'<b><font size="2">'.$meta_box['title'].'</font></b>'; //&nbsp;&nbsp;
    // display description
    echo'<label for="'.$meta_box['name'].'">'.$meta_box['description'].'</label><br/>';
    // display input box
    //echo'<input type="text" name="'.$meta_box['name'].'" value="'.$meta_box_value.'" size="30" /><br /><br /><p></p>';
    // display drop down
    $title_array = array( 'Lesson 1', 'Lesson 2', 'Lesson 3', 'Lesson 4', 'Lesson 5', 'Lesson 6', 'Lesson 7', 'Lesson 8', 'Lesson 9', 'Lesson 10' );
    // display selection box
    echo'<br /><select name="'.$meta_box['name'].'"><option value="" >Select lesson title</option>' .  "\n";
    foreach ($title_array as $current_option) {
      $selected = ($current_option==$meta_box_value) ? ' selected="selected"' : '';
      echo '<option value="' . $current_option . '"' . $selected . '>'  . $current_option . '</option>' . "\n";
    }
    echo "\n</select>\n<br />";
  }

} //end new_meta_boxEduvineLessons_sidebarTitle()


/**
 * Saves data for eduvine lesson metabox
 *
 * @author Jon Bellona <bellona@uoregon.edu>
 * @global $post      
 * @global $meta_lesson_post_order
 * @uses wp_verify_nonce, current_user_can()
 * @uses get_post_meta(), add_post_meta(), update_post_meta(), delete_post_meta()
 */
function save_postdata_boxLessonOrder( $post_id ) {
  
  // need the post id and associative vars */
  global $post, $meta_lesson_post_order;

  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
    return $post_id;
  
  // loop through array
  foreach($meta_lesson_post_order as $meta_box) {
    // Verify
    if (! isset($_POST[$meta_box['name'].'_noncename'])) {
      return $post_id;
    }
    
    if ( !wp_verify_nonce( $_POST[$meta_box['name'].'_noncename'], plugin_basename(__FILE__) )) {
      return $post_id;
    }
   
    if ( 'eduvine_lesson' == $_POST['post_type'] ) {
      if ( !current_user_can( 'edit_page', $post_id ))
        return $post_id;
    } else {
      if ( !current_user_can( 'edit_post', $post_id ))
        return $post_id;
    }
  
    // var to use for custom field values
    $data = $_POST[$meta_box['name']];
    
    if(get_post_meta($post_id, $meta_box['name']) == "")
      add_post_meta($post_id, $meta_box['name'], $data, true);
      
    elseif($data != get_post_meta($post_id, $meta_box['name'], true))
      update_post_meta($post_id, $meta_box['name'], $data);
      
    elseif($data == "")
      delete_post_meta($post_id, $meta_box['name'], get_post_meta($post_id, $meta_box['name'], true));
  
  } //end foreach
} //end save_postdata_boxLessonOrder( $post_id )

add_action('save_post', 'save_postdata_boxLessonOrder');


/**
 * Displays National Standards metabox on post editor screen (for saving/updating a url link)
 *
 * @author Jon Bellona <bellona@uoregon.edu>
 * @global $post      
 * @global $meta_lesson_national_standard_url
 * @uses wp_create_nonce(), get_post_meta()
 */
$meta_national_standard_url =
  array(
    "national_standard_url" => array(
      "name" => "national_standard_url",
      "std" => "",
      "title" => "",
      "description" => "Enter a url to National Standards .pdf file. <br/> Should be an external link to a Google doc or a Dropbox file."
    )
  );

function new_meta_boxNationalStandard() {
  
  // declare global access vars. post and our array
  global $post, $meta_national_standard_url;
 
  // loop through array      
  foreach($meta_national_standard_url as $meta_box) {

    $meta_box_value = get_post_meta($post->ID, $meta_box['name'], true);
 
    if($meta_box_value == "")
      $meta_box_value = $meta_box['std'];
    
    // hidden field to check the values
    echo'<input type="hidden" name="'.$meta_box['name'].'_noncename" id="'.$meta_box['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
    // display title
    echo'<b><font size="2">'.$meta_box['title'].'</font></b>'; //&nbsp;&nbsp;
    // display description
    echo'<label for="'.$meta_box['name'].'">'.$meta_box['description'].'</label><br/>';
    // display input box
    echo'<input type="text" name="'.$meta_box['name'].'" value="'.$meta_box_value.'" size="70" /><br /><br /><p></p>';
  }

} //end new_meta_boxNationalStandard()


/**
 * Saves data for Eduvine National Standards metabox
 *
 * @author Jon Bellona <bellona@uoregon.edu>
 * @global $post      
 * @global $meta_lesson_national_standard_url
 * @uses wp_verify_nonce, current_user_can()
 * @uses get_post_meta(), add_post_meta(), update_post_meta(), delete_post_meta()
 */
function save_postdata_national_standard( $post_id ) {
  
  // need the post id and associative vars */
  global $post, $meta_national_standard_url;

  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
    return $post_id;
  
  // loop through array
  foreach($meta_national_standard_url as $meta_box) {
    // Verify
    if (! isset($_POST[$meta_box['name'].'_noncename'])) {
      return $post_id;
    }
    
    if ( !wp_verify_nonce( $_POST[$meta_box['name'].'_noncename'], plugin_basename(__FILE__) )) {
      return $post_id;
    }
   
    if ( 'eduvine_unit' == $_POST['post_type'] ) {
      if ( !current_user_can( 'edit_page', $post_id ))
        return $post_id;
    } else {
      if ( !current_user_can( 'edit_post', $post_id ))
        return $post_id;
    }
  
    // var to use for custom field values
    $data = $_POST[$meta_box['name']];
    
    if(get_post_meta($post_id, $meta_box['name']) == "")
      add_post_meta($post_id, $meta_box['name'], $data, true);
      
    elseif($data != get_post_meta($post_id, $meta_box['name'], true))
      update_post_meta($post_id, $meta_box['name'], $data);
      
    elseif($data == "")
      delete_post_meta($post_id, $meta_box['name'], get_post_meta($post_id, $meta_box['name'], true));
  
  } //end foreach
} //end save_postdata_lesson_national_standard( $post_id )

add_action('save_post', 'save_postdata_national_standard');



/**  
 * Art Navigation Type Array
 */
$new_meta_lsection_type =
  array(
    "lsection_type" => array(
    "name" => "lsection_type",
    "std" => "",
    "title_explore" => __("Explore"),
    "title_challenge" => __("Challenge"),
    "description" => __("Choose the type of section for the above lesson. Default section type is Explore.")
    )
  );

/**  
 * Create radio button metabox for 'eduvine_lsection' post type
 *    --> (Explore and Challenge section)
 *
 * @author Jon Bellona
 * @global $post
 * @global $new_meta_lsection_type
 * @see add_art_navigation_metaboxes()
 * @dependencies get_post_meta(), wp_create_nonce(), get_categories(), current_user_can()
 */ 
function new_meta_box_lsection_type() {
  
  // declare global access vars. post and our array
  global $post, $new_meta_lsection_type;
 
  // loop through first array     
  foreach($new_meta_lsection_type as $meta_box) {

    $meta_box_valueA = get_post_meta($post->ID, $meta_box['name'], true);
 
    if($meta_box_valueA == "")
      $meta_box_valueA[0] = 'explore';
    
    if($meta_box_valueA[0] == "explore") {
      $art_form_checked = "checked";
      $art_topic_checked = "";
    }
    elseif(in_array("challenge", $meta_box_valueA) == "challenge") {
      $art_topic_checked = "checked";
      $art_form_checked = "";
    }

    // hidden field to check the values
    echo'<input type="hidden" name="'.$meta_box['name'].'_noncename" id="'.$meta_box['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
    echo "<div class='nav_indent'>".$meta_box['description']."</div> \n<br />\n";
    // display explore selection
    echo '<input type="radio" name="'.$meta_box['name'].'[]" value="explore" '.$art_form_checked.' /> ';
    echo'<b><font size="2">'.$meta_box['title_explore'].'</font></b>&nbsp;&nbsp;';
    //echo'<label for="'.$meta_box['name'].'">'.$meta_box['description'].'</label>';
    echo "\n<br />\n"; 
    // display challenge selection
    echo '<input type="radio" name="'.$meta_box['name'].'[]" value="challenge" '.$art_topic_checked.' /> ';
    echo'<b><font size="2">'.$meta_box['title_challenge'].'</font></b>&nbsp;&nbsp;';
    //echo'<label for="'.$meta_box['name'].'_value">'.$meta_box['description'].'</label>';
    
  }

} //end new_meta_box_lsection_type()


/**  
 * Save Explore/Challenge MetaBox for 'eduvine_lsection' post type
 *    --> (Art Form Topics)
 *
 * @author Jon Bellona
 * @link http://wefunction.com/2008/10/tutorial-creating-custom-write-panels-in-wordpress/
 * @global $post
 * @global $new_meta_art_nav_page_type
 * @dependencies current_user_can(), wp_verify_nonce(), get_post_meta(), add_post_meta(), update_post_meta()
 * @dependencies get_term_by(), wp_set_object_terms(), wp_delete_term()
 */ 
function save_postdata_lsection_type( $post_id ) {
  
  // need the post id and associative vars */
  global $post, $new_meta_lsection_type;

  if (! isset($_POST['post_type'])) {
    return $post_id;
  }
  
  if ( 'eduvine_lsection' != $_POST['post_type'] ) {
    return $post_id;
  } else {
    
    // loop through first array. art nav page type
    foreach($new_meta_lsection_type as $meta_box) {
      // Verify
      if ( !wp_verify_nonce( $_POST[$meta_box['name'].'_noncename'], plugin_basename(__FILE__) )) {
        return $post_id;
      }
     
      if ( 'page' == $_POST['post_type'] ) {
        if ( !current_user_can( 'edit_page', $post_id ))
          return $post_id;
      } else {
        if ( !current_user_can( 'edit_post', $post_id ))
          return $post_id;
      }
  
      // var to use for custom field values
      $data = $_POST[$meta_box['name']];
  
    /* If returns empty, we know this custom field has not been added before. 
     * Otherwise, update the information of the field. 
     * Else if will delete if entry is blank, so no redundancy. */
  
      if(get_post_meta($post_id, $meta_box['name']) == "")
        add_post_meta($post_id, $meta_box['name'], $data, true);
      
      elseif($data != get_post_meta($post_id, $meta_box['name'], true))
        update_post_meta($post_id, $meta_box['name'], $data);
      
      elseif($data == "")
        delete_post_meta($post_id, $meta_box['name'], get_post_meta($post_id, $meta_box['name'], true));
        
    } //end foreach
  
  } //end else
} //end save_postdata_lsection_type( $post_id )

add_action('save_post', 'save_postdata_lsection_type');



/* ---------------------------------------------------------------------------*/
/* ------------------------ EDUVINE MISCELLANEOUS ----------------------------*/
/* ---------------------------------------------------------------------------*/


//Don't need to use, comments are open already on these post types.
/**
 * Forces custom post types for eduvine to allow comments
 *
 * @todo need to place these under custom post type edits only
 *  don't need to run every post update and post save for every post type.
 *
 * @author Jon Bellona
 */
function force_eduvine_comments($data){
  $post_typeArr = array( 'eduvine_lesson', 'eduvine_lsection' );

  if(in_array($data['post_type'], $post_typeArr)) {
    $data['comment_status'] = 'open';
  }
  /* // works for an action but not a filter
   * if($data->post_type == 'public_blog_post' || $data->post_type == 'team_blog_post'){
    $data->comment_status = 'open'; 
  }*/
  return $data;
}

//add_filter('wp_insert_post_data', 'force_eduvine_comments');

if ( ! function_exists( 'eduvine_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own china_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Twenty Ten 1.0
 */
function eduvine_comment( $comment, $args, $depth ) {
  $GLOBALS['comment'] = $comment;
  switch ( $comment->comment_type ) :
    case '' :
  ?>
  <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
  <div id="comment-<?php comment_ID(); ?>">
    <div class="comment-author vcard">
      <?php echo get_avatar( $comment, 40 ); ?>
      <?php printf( __( '%s <span class="says">says:</span>', 'china' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
    </div><!-- .comment-author .vcard -->
    <?php if ( $comment->comment_approved == '0' ) : ?>
      <em><?php _e( 'Your comment is awaiting moderation.', 'china' ); ?></em>
      <br />
    <?php endif; ?>

    <div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
      <?php
        // translators: 1: date, 2: time
        printf( __( '%1$s', 'china' ), get_comment_date() ); ?></a><?php edit_comment_link( __( '(Edit)', 'china' ), ' ' );
      ?>
    </div><!-- .comment-meta .commentmetadata -->

    <div class="comment-body"><?php comment_text(); ?></div>

    <div class="reply">
      <?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
    </div><!-- .reply -->
  </div><!-- #comment-##  -->

  <?php
      break;
    case 'pingback'  :
    case 'trackback' :
  ?>
  <li class="post pingback">
    <p><?php _e( 'Pingback:', 'china' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', 'china'), ' ' ); ?></p>
  <?php
      break;
  endswitch;
}
endif;


/**
 * Custom WP filter, for adding <a> elements for any http:// in plain text.
 *
 * Encapsulates plain text with <p> elements and then searches for urls and replaces these with a link.
 * Tweaked an original code.  Call with apply_filters('add_links', $your_text);
 * @link http://css-tricks.com/snippets/php/find-urls-in-text-make-links/
 *
 * @author Jon Bellona
 */
function eduvineOptionsFilter ($contentToFilter) {
  //add p tags
  $contentToFilter = '<p>' . $contentToFilter . '</p>';
  $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
  // Check if there is a url in the text
  if(preg_match_all($reg_exUrl, $contentToFilter, $url)) { 
    $usedPatterns = array();
    // make the urls hyper links            
    foreach($url[0] as $pattern){
      if(!array_key_exists($pattern, $usedPatterns)){
        $usedPatterns[$pattern]=true;
        $contentToFilter = str_replace($pattern, "<a href='{$pattern}' target='_blank' >{$pattern}</a>", $contentToFilter);   
      }
    }
    return $contentToFilter;
  } else {
    return $contentToFilter;
  }
}

add_filter('add_links', 'eduvineOptionsFilter');


/*
 * Kills user metadata associated with reordering of metaboxes on custom post types.
 *
 * Since we make metaboxes static, any CSS/jQuery styling errors could cause the user to sort.
 * Once a user sorts, he/she saves "meta-box-order_$posttype" metadata.
 * We could then run into potential styling issues if we let this metadata slide.
 *
 * @uses 
 */
function kill_user_meta_box_order_postmeta_eduvine($user_login, $user) {
  //is meta box ordering isset for our custom posttypes, delete that shit.
  $user_meta = get_user_meta($user->ID);
  //_log($user_meta);

  $posttypes = array( 'eduvine_unit', 'eduvine_lesson', 'eduvine_lsection' );

  foreach( $posttypes as $posttype ) {
    if(isset($user_meta['meta-box-order_'.$posttype])) {
      //_log('meta-box-order_'.$posttype.' isset');
      delete_user_meta($user->ID, 'meta-box-order_'.$posttype);
    }
    if(isset($user_meta['metaboxhidden_'.$posttype])) {
      //_log('metaboxhidden_'.$posttype.' isset');
      delete_user_meta($user->ID, 'metaboxhidden_'.$posttype);
    }
  }
  
}
//run when the user logs in, and if the user updates their profile.
add_action('wp_login', 'kill_user_meta_box_order_postmeta_eduvine', 10, 2);
if (is_admin()) {
  add_action('profile_update', 'kill_user_meta_box_order_postmeta_eduvine', 10, 2);
}

?>
