<?php 

/// Class for installing database

class like_box_install_database{
	
	public $installed_options; // Plugin default options value
	private $plugin_url;

	function __construct(){
		
		if(isset($params['plugin_url']))
			$this->plugin_url=$params['plugin_url'];
		else
			$this->plugin_url=trailingslashit(dirname(plugins_url('',__FILE__)));

		
		$this->installed_options=array(
			"sidbar_slide_like_box"=>array(
				"like_box_sidebar_slide_mode"					=> "no",
				"like_box_sidebar_slide_title_color"			=> "#FFFFFF",	
				"like_box_sidebar_slide_title"					=> 'Facebook',
				"like_box_sidebar_slide_title_font_famely"		=> 'Times New Roman,Times,Serif,Georgia',
				"like_box_sidebar_slide_pntik_height"			=> "220",			
				
				"like_box_sidebar_slide_profile_id"			=> "",
					
				"like_box_sidebar_slide_width"				=> "360",
				"like_box_sidebar_slide_height"				=> "450",
				"like_box_sidebar_slide_header"				=> 'small',	
				"like_box_sidebar_slide_cover_photo"		=> 'show',	
				"like_box_sidebar_slide_connections"		=> 'show',								
				"like_box_sidebar_slide_locale"				=> "en_US",				
			),
			"popup_like_box"=>array(
				'like_box_enable_like_box' 			=>  'no',
								
				'like_box_popup_title' 				=>  'Facebook Like Box',
				'like_box_popup_title_color' 		=>  '#000000',
				'like_box_popup_title_font_famely'	=>  'Times New Roman,Times,Serif,Georgia',
				
				"like_box_profile_id"			=> "",							
				"like_box_width"				=> "600",
				"like_box_height"				=> "450",
				"like_box_cover_photo"			=> 'show',	
				"like_box_header"				=> 'show',	
				"like_box_connections"			=> 'show',								
				"like_box_locale"				=> "en_US",
			),
			
		);
		
		
	}
	
	/*############################### Function for installing database ########################################*/	
	
	public function install_databese(){
		foreach( $this->installed_options as $key => $option ){
			if( get_option($key,FALSE) === FALSE ){
				add_option($key,$option);
			}
		}		
	}
}