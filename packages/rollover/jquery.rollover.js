jQuery(document).ready(function(){  
  //automatically hide the animation before hover function is called.
   jQuery(".cover", '#intro-eduvine #captionfull-eduvine').stop().animate({top:'125px'},{queue:true,duration:160});
  //Full Caption Sliding (Hidden to Visible)  

  //EduVine Topics hide/show
  jQuery.each([1,2,3,4,5], function(index, value) {
    jQuery('#eduvine-topic-' + value).hover(function(){ 
        jQuery('#eduvine-topic-' + value + '-hoverimg').toggle(5);
        jQuery('#captionfull-eduvine .captiontext-eduvine').hide(5);
        jQuery('#eduvine-topic-' + value + '-title').fadeIn(400);
        jQuery(".cover", '#intro-eduvine #captionfull-eduvine').stop().animate({top:'0px'},{queue:false,duration:400});  
      }, function() {  
        jQuery('#eduvine-topic-' + value + '-hoverimg').toggle(5);
        jQuery(".cover", '#intro-eduvine #captionfull-eduvine').stop().animate({top:'125px'},{queue:false,duration:400});  
        jQuery('#eduvine-topic-' + value + '-title').fadeOut(400);
    });
  });


  //flash the buttons on the Eduvine page to indicate that they are clickable.

  //flash the buttons all at once.
  //jQuery.each([1,2,3,4,5], function(index, value) {
  //  jQuery('#eduvine-topic-' + value + '-hoverimg').fadeIn(500).delay(600).fadeOut(400);
  //});

  // flash the buttons on the Eduvine page in sequence
  // @see eduvine.js
  //
  //animateOrbs(); 

});