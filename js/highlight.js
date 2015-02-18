/* 
 * Javascript for Eduvine navigation highlight on submenus
 * 
 * $Id: 
 * 
*/

// Add startup function calls here
//
if (typeof($jQ) == "undefined");
	var $jQ = jQuery.noConflict();
	
$jQ(document).ready(function () {
	
	var current_blog_url = window.location.pathname;
	
	//nav highlights for Eduvine posts (all eduvine post types)
	if(current_blog_url.match("eduvine_")) {
		$jQ('#menu-item-3160').addClass('current-page-ancestor');
		$jQ('#menu-item-5379').addClass('current-page-ancestor');
	}
});