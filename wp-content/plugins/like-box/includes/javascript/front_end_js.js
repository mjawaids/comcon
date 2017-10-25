/*###################### popup ##############################*/
function like_box_setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays*24*60*60*1000));
	var expires = "expires="+d.toUTCString();
	document.cookie = cname + "=" + cvalue + "; " + expires+"; path=/";
}
function like_box_getCookie(cname) {
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i<ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1);
		if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
	}
	return "";
}
function like_box_resize_popup(){
	
	// popup resize width
	if( like_box_initial_width > jQuery(window).width() ){
		jQuery('.facbook_like_box_popup iframe').css('width','100%')					
		jQuery('.facbook_like_box_popup').width((jQuery(window).width()-15))
		jQuery(".facbook_like_box_popup").css({marginLeft: '-' + parseInt((jQuery('.facbook_like_box_popup').width()/ 2),10) + 'px'});
		
	}else{
		if(jQuery('.facbook_like_box_popup').width()<like_box_initial_width){
			jQuery('.facbook_like_box_popup').width(Math.min((jQuery(window).width()-15),(like_box_initial_width)))
			jQuery(".facbook_like_box_popup").css({marginLeft: '-' + parseInt((jQuery('.facbook_like_box_popup').width()/ 2),10) + 'px'});
		}
	}
	
	// popup resize height
	if( like_box_initial_height > jQuery(window).height() ){					
		jQuery('.facbook_like_box_popup').height((jQuery(window).height()-25))
		jQuery('.facbook_like_box_popup iframe').height((jQuery(window).height()-59))
		jQuery(".facbook_like_box_popup").css({marginTop: '-' + parseInt((jQuery('.facbook_like_box_popup').height()/ 2),10) + 'px'});
		
	}else{
		if(jQuery('.facbook_like_box_popup').height()<like_box_initial_height){
			jQuery('.facbook_like_box_popup').height(Math.min((jQuery(window).height()-25),(like_box_initial_height)))
			jQuery('.facbook_like_box_popup iframe').height((jQuery(window).height()-59))
			jQuery(".facbook_like_box_popup").css({marginTop: '-' + parseInt((jQuery('.facbook_like_box_popup').height()/ 2),10) + 'px'});
		}
	}
}

/*###################### Slideup ##############################*/

var like_box_slideup={
	initial_width:'220',
	initial_height:'480',
	initial_loaction:'left',
	construct_function:function(){
		var self=this
		if(jQuery('.main_sidbar_slide').css('left')=='auto')
		this.initial_loaction='right';
		jQuery(window).resize(self.resize_height);
		jQuery(window).resize(self.resize_width);
		
		self.resize_width();
		jQuery('.sidbar_slide_header').click(function(){			
			if(jQuery('.main_sidbar_slide').hasClass('like_box_slideup_close')){
				jQuery('.main_sidbar_slide').addClass('like_box_slideup_open');
				jQuery('.main_sidbar_slide').removeClass('like_box_slideup_close');
				
			}
			else{
				jQuery('.main_sidbar_slide').addClass('like_box_slideup_close');
				jQuery('.main_sidbar_slide').removeClass('like_box_slideup_open');
			}
			
		});
	},
	resize_height:function(){
		
		jQuery('.sidbar_slide_inner').css('max-height',jQuery(window).height());
		jQuery('.sidbar_slide_header').css('margin-top',parseInt((jQuery('.sidbar_slide_inner_main').height()-jQuery('.sidbar_slide_header').height())/2)+'px');
		
	},
	resize_width:function(){
		if(jQuery(window).width()<=jQuery('.main_sidbar_slide').width())
		{
			
			if(!jQuery('#like_box_phone_slideup_style').length)
				jQuery('body').append('<style id="like_box_phone_slideup_style">.sidbar_slide_content{width:'+(jQuery(window).width()-40)+'px;}.sidbar_slide_inner_main {width:'+jQuery(window).width()+'px;}.like_box_slideup_close{'+like_box_slideup.initial_loaction+':-'+(jQuery(window).width()-40)+'px;}</style>')
			else
				jQuery('#like_box_phone_slideup_style').html('.sidbar_slide_content{width:'+(jQuery(window).width()-40)+'px;}.sidbar_slide_inner_main{width:'+jQuery(window).width()+'px;}.like_box_slideup_close{'+like_box_slideup.initial_loaction+':-'+(jQuery(window).width()-40)+'px;}');
		}
		else{
			if(jQuery('#like_box_phone_slideup_style').length)
				jQuery('.like_box_phone_slideup_style').remove();
		}
		
	},
	
		
	
}
/*############################### ANImation Effekts ########################33*/
function like_box_animated_element(animation,element_id){	
		jQuery('#'+element_id).ready(function(e) {	
			if(!jQuery(jQuery('#'+element_id)).hasClass('animated') && like_box_isScrolledIntoView(jQuery('#'+element_id)))	{	
				jQuery(jQuery('#'+element_id)).css('visibility','visible');
				jQuery(jQuery('#'+element_id)).addClass('animated');
				jQuery(jQuery('#'+element_id)).addClass(animation);	
			}
		});		
}
function like_box_isScrolledIntoView(elem)
{
    var $elem = jQuery(elem);
	if($elem.length=0)
		return true;
    var $window = jQuery(window);
    var docViewTop = $window.scrollTop();
    var docViewBottom = docViewTop + $window.height();
	if(typeof(jQuery(elem).offset())!='undefined')
    	var elemTop = jQuery(elem).offset().top;
	else
		var elemTop = 0;
    var elemBottom = elemTop + parseInt(jQuery(elem).css('height'));	
    return ( ( (docViewTop<=elemTop) && (elemTop<=docViewBottom) )  || ( (docViewTop<=elemBottom) && (elemBottom<=docViewBottom) ));
}
jQuery(document).ready(function(e) {
    like_box_slideup.construct_function();
});
/*####################### other element resize ###################################*/


function like_box_set_width_cur_element(element_id,element_initial_width){
	var element_id='#'+element_id
	// initial variables
	var parent_width=jQuery(element_id).parent().width();
	var curent_src=jQuery(element_id).attr('src');
	// corect seted width
	var element_initial_width=Math.min(500,parseInt(element_initial_width));
	var element_initial_width=Math.max(180,parseInt(element_initial_width));

	// corect width with parent element
	
	if(parent_width<=180){
		curent_src=like_box_replace_src(curent_src,180)
		jQuery(element_id).css('width',180);
	}
	if(parent_width>=180){
		curent_src=like_box_replace_src(curent_src,Math.min(element_initial_width,parent_width));
		jQuery(element_id).css('width',Math.min(element_initial_width,parent_width));
	}
	//set replaced url
	jQuery(element_id).attr('src',curent_src);
}

// replace url in src
function like_box_replace_src(old_src,width){
	old_src=old_src.replace(/&width=[\d]+/,'&width='+width)
	old_src=old_src.replace(/&container_width=[\d]+/,'&container_width='+width)
	return old_src
}










