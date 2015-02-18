<?php

/** ------------------------------------------------------------------------ **/
/** ----------------------- INTRO PANEL OPTIONS ---------------------------- **/
/** ------------------------------------------------------------------------ **/

//GLOBALS
$eduvine_editor_settings = array(
			'wpautop' => false,
			'media_buttons' => false,
			'quicktags' => array(
				'buttons' => 'em,strong,link',
			),
			//'editor_css' => get_template_directory_uri() . '/editor-style-pageofposts.css',
			//'textarea_rows' => 6,
			'tinymce' => array( 
					//'mode' => 'exact',
					'height' => '350',
					'theme' => 'advanced',
					'theme_advanced_disable' => 'classic_image_button_mce_buttons, image, mce_image, kimiliFlashEmbed, VideoEnhanced'
					//'skin' => 'wp_theme', // use 'default' or 'wp_theme'
					//'theme_advanced_resizing' => false,  //allows the height attribute to work
					//'plugins' => 'inlinepopups,wpdialogs,wplink,media,paste,tabfocus', //allows correct styling of editor
					//'init_instance_callback' => 'mce_has_loaded_eduvine'
			)
		);
$eduvine_options_title_order = array( 'Eduvine Home Page', 'EduVine Terms of Agreement' );
$eduvine_options_sections=array(
						'Eduvine Home Page' => 0,
						'EduVine Terms of Agreement' => 0
					);
$eduvine_instructions_url = 'https://docs.google.com/open?id=0B2HN9SZNKwWpYTRuX0dYUlVCSlU';

/**
 * Set defaults for our intro panel pages.
 */
$eduvine_home_page = "EduVine is a self-guided, interactive educational folk art curriculum based
on the idea that you learn about yourself as you learn about others. The
cultural explorations and challenges presented ask participants to explore
new ways of creating visual and text-based responses as it utilizes
ChinaVine's open source materials. Learn about China's cultural heritage as
you learn about your own individual identity.";
$eduvine_terms = "EduVine is a self-guided, interactive educational folk art curriculum
based on the idea that you learn about yourself as you learn about
others. The cultural explorations and challenges presented ask
participants to explore new ways of creating visual and text-based
responses as it utilizes ChinaVine's open source materials. Learn about
China's cultural heritage as you learn about your own individual
identity.";

/**
 * Creates our option menu for saving introduction text and images.
 *
 * Adds the page as a sub-menu, and then registers the settings for the menu page.
 *
 * @author Jon Bellona
 * top link is the best reference.
 * @link http://wordpress.org/support/topic/do-i-need-add_settings_section-and-add_settings_field
 * @link http://codex.wordpress.org/Creating_Options_Pages
 * @link http://codex.wordpress.org/Adding_Administration_Menus
 */
function eduvine_options_create_menu() {
	//create new menu
	add_menu_page('Eduvine Options', 'Eduvine Options', 'eduvine', __FILE__, 'create_eduvine_options_page', plugin_dir_url( __FILE__ ).'../images/generic.png' );
	//add_submenu_page('edit.php?post_type=page', 'Intro options', 'Introduction content', 'administrator', 'paradiso-settings', 'create_introduction_settings_page');

	//call register settings function
	add_action( 'admin_init', 'register_eduvine_options_settings' );
}
add_action('admin_menu', 'eduvine_options_create_menu');



/**
 * Registers and adds settings for display on introduction options page. 
 *
 * Adds the page as a sub-menu, and then registers the settings for the menu page.
 *
 * @author Jon Bellona
 * @link http://wordpress.org/support/topic/do-i-need-add_settings_section-and-add_settings_field
 */
function register_eduvine_options_settings(){
	
	//Eduvine Options Settings
	add_settings_section('eduvine-options-section', '', 'eduvine_options_header', 'eduvine_options');
	//register our fields for the option page.
	register_setting('eduvine_options_group','eduvine_comment_instructions_url');
	add_settings_field('eduvine-option-instructions-url', 'Eduvine Comment Instructions URL', 'options_instructions_url_input', 'eduvine_options', 'eduvine-options-section');

	register_setting('eduvine_options_group','eduvine_terms');
	add_settings_field('eduvine-option-terms', 'Eduvine Splash Page Welcome', 'options_terms_input', 'eduvine_options', 'eduvine-options-section');

	register_setting('eduvine_options_group','eduvine_home_page');
	add_settings_field('eduvine-option-home-page', 'Eduvine Homepage Content', 'options_home_page_input', 'eduvine_options', 'eduvine-options-section');

}



/**
 * Displays our custom options page content
 *
 * @uses settings_fields(), do_settings_sections()
 * @author Jon Bellona
 */
function create_eduvine_options_page(){
	//register our javascript for our panel
	//wp_enqueue_script('eduvine-introPanels', plugins_url( '/eduvine-introPanels.js' , __FILE__ ));

	?><div class="wrap">
		<h2>Eduvine Options Page</h2>
		<form method="post" action="options.php">
			<?php settings_fields('eduvine_options_group'); ?>
			<?php do_settings_sections('eduvine_options'); ?>
			<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>"  /></p>
		</form>
	</div><!--END wrap-->
	<?php
}


/**
 * Eduvine Options section/description
 */
function eduvine_options_header() {
?><!--<p>Editors for miscellaneous Eduvine content.</p>--><?php
}


/**
 * Displays the eduvine instructions url FIELD on the options page.
 */
function options_instructions_url_input() {
?>
	<input type="text" name="eduvine_comment_instructions_url" size="70" value="<?php echo get_option('eduvine_comment_instructions_url');?>" />
<?php
}
/**
 * Displays the eduvine terms FIELD on the options page.
 */
function options_terms_input() {
?>
	<textarea name="eduvine_terms" rows="7" cols="71" /><?php echo get_option('eduvine_terms');?></textarea>
<?php
}
/**
 * Displays the eduvine home page FIELD on the options page.
 */
function options_home_page_input() {
?>
	<textarea name="eduvine_home_page" rows="7" cols="71" /><?php echo get_option('eduvine_home_page');?></textarea>
<?php
}