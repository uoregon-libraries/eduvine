/* Javascript for ChinaVine Eduvine Admins only
 * 
 * $Id: admin.js 847 2011-12-07 23:49:35Z bellona $
 * 
*/

	// Add startup function calls here
	//

if (typeof($jQ) == "undefined");
		var $jQ = jQuery.noConflict();
		
	$jQ(document).ready(function () {
		

		//Hide or replace elements to match the Admin panel Profile wireframes.
		//
		$jQ('h3:contains("Personal Options")').hide();
		
		$jQ('label[for|="rich_editing"]').parents('tr').hide();
		$jQ('input[name|="admin_color"]').parents('tr').hide();
		$jQ('label[for|="comment_shortcuts"]').parents('tr').hide();
		$jQ('label[for|="admin_bar_front"]').parents('tr').hide();

		//hide the Rich Text Editor Option inside Your Profile pages, redundant but necessary
		//
		$jQ('th:contains("Visual Editor")').hide();
		$jQ('input[name|="rich_editing"]').hide();
		$jQ('label[for|="rich_editing"]').hide();
	
		//Hide Contact Info fields
		//
		$jQ('label[for|="url"]').parents('tr').hide();
		$jQ('label[for|="aim"]').parents('tr').hide();
		$jQ('label[for|="yim"]').parents('tr').hide();
		$jQ('label[for|="jabber"]').parents('tr').hide();
		$jQ('h3:contains("About Yourself")').hide();
		$jQ('label[for|="description"]').parents('tr').hide();
		$jQ('th:contains("Admin Color Scheme")').hide();
	
		//Site Page (normal page editor!) not working with remove_meta_box() call.
		$jQ('div[id|="postimagediv"]').hide();
		$jQ('script').each(function () {
			//alert(this.innerHTML.match('pagenow = \'page\''));
			var pagematch_re = /pagenow\s+=\s+'page'/;
			if ((this.innerHTML.match(pagematch_re))) {
				//alert(this.innerHTML.match('pagenow = \'page\'\,'));
				$jQ('div[id|="geo_mashup_post_edit"]').hide();
			}
		});
		//cvadmins only! Post Excerpt doesn't appear even if added and supported.
		$jQ('div[id|="postexcerpt"]').removeClass('hide-if-js');
		
	});


	
$jQ(window).load(function(){ 
	
});
	