jQuery(document).ready(function(e) {
    jQuery('.pro_select,.pro_input,.disabled_picker').click(function(){alert("If you want to use this feature upgrade to Like box Pro")});
	  jQuery('.pro_checkbox').mousedown(function(){alert("If you want to use this feature upgrade to Like box Pro")})
});
/*ADMIN CUSTOMIZE SETTINGS OPEN OR HIDE*/
function get_array_of_opened_elements(){
	var kk=0;
	var array_of_activ_elements=new Array();
	jQuery('#like_box_page .main_parametrs_group_div').each(function(index, element) {		
        if(!jQuery(this).hasClass('closed_params')){			
			array_of_activ_elements[kk]=jQuery('#like_box_page .main_parametrs_group_div').index(this);
			kk++;
		}
    });
	return array_of_activ_elements;
}
function like_box_setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays*24*60*60*1000));
	var expires = "expires="+d.toUTCString();
	document.cookie = cname + "=" + cvalue + "; " + expires+"; path=/";
}

jQuery(document).ready(function(e) {


	/*SET CLOR PICKERS*/
	jQuery('.color_option').wpColorPicker()	

	var askofen=0;
	jQuery(".save_all_section_parametrs").click(function(){
		jQuery(".save_section_parametrs").each(function(index, element) {
			jQuery(this).trigger('click');	
		});
		jQuery('.save_all_section_parametrs').addClass('padding_loading');
		jQuery('.save_all_section_parametrs').prop('disabled', true);		
		jQuery('.save_all_section_parametrs .saving_in_progress').css('display','inline-block');
		setTimeout(check_all_saved(),500);
	})
	function check_all_saved(){
		if(askofen==0){
			jQuery('.save_all_section_parametrs .saving_in_progress').css('display','none');
			jQuery('.save_all_section_parametrs .sucsses_save').css('display','inline-block');
			setTimeout(function(){jQuery('.save_all_section_parametrs .sucsses_save').hide('fast');jQuery('.save_all_section_parametrs').removeClass('padding_loading');jQuery('.save_all_section_parametrs').prop('disabled', false);},1800);
			
		}
		else{
		
			setTimeout(check_all_saved,500);
		}
	}
	/*############ Other section Save click ################*/
	jQuery(".save_section_parametrs").click(function(){		
		
		jQuery('.like_box_hidden_parametr').each(function(index, element) {
		   generete_input_values(this)
		});
		var like_box_curent_section=jQuery(this).attr('id');
		jQuery.each( like_box_all_parametrs[like_box_curent_section], function( key, value ) {
		   like_box_all_parametrs[like_box_curent_section][key] =jQuery('#'+key).val() 
		});
		var like_box_date_for_post=like_box_all_parametrs;
		like_box_all_parametrs[like_box_curent_section]['curent_page']=like_box_curent_section;
		like_box_all_parametrs[like_box_curent_section]['like_box_options_nonce']=jQuery('#like_box_options_nonce').val();
		
		
		jQuery('#'+like_box_curent_section).addClass('padding_loading');
		jQuery('#'+like_box_curent_section).prop('disabled', true);		
		jQuery('#'+like_box_curent_section+' .saving_in_progress').css('display','inline-block');
		
		askofen++;
		jQuery.ajax({
					type:'POST',
					url: like_box_ajaxurl+'?action=like_box_page_save',
					data: like_box_all_parametrs[like_box_curent_section],
				}).done(function(date) {
					jQuery('#'+like_box_curent_section+' .saving_in_progress').css('display','none');
					if(date==like_box_parametrs_sucsses_saved){							
						jQuery('#'+like_box_curent_section+' .sucsses_save').css('display','inline-block');
						setTimeout(function(){like_box_clickable=1;jQuery('#'+like_box_curent_section+' .sucsses_save').hide('fast');jQuery('#'+like_box_curent_section+'.save_section_parametrs').removeClass('padding_loading');jQuery('#'+like_box_curent_section).prop('disabled', false);},1800);
						askofen--;
					}
					else{
						jQuery('#'+like_box_curent_section+' .error_in_saving').css('display','inline-block');
						jQuery('#'+like_box_curent_section).parent().find('.error_massage').eq(0).html(date);
						
					}
		});
	});

});


function generete_input_values(hidden_element){
	var element_array = {};
	jQuery(hidden_element).parent().find('input[type=checkbox]').each(function(index, element) {						
		element_array[jQuery(this).val()]=jQuery(this).prop('checked');
	});
	jQuery(hidden_element).val(JSON.stringify(element_array));
}