<?php 

class like_box_front_end{
	private $menu_name;
	
	private $plugin_url;
	
	private $databese_parametrs;
	
	private $params;
	
	public static $id_for_content=0;

	function __construct($params){
		
		$this->databese_parametrs=$params['databese_parametrs'];
		//If Like Box plugin url doesn't come in parent class
		if(isset($params['plugin_url']))
			$this->plugin_url=$params['plugin_url'];
		else
			$this->plugin_url=trailingslashit(dirname(plugins_url('',__FILE__)));

		//Popup iframe Hooks
		add_action( 'wp_ajax_likeboxfrontend', array($this,'like_box_ifreame_generator') );
		add_action( 'wp_ajax_nopriv_likeboxfrontend', array($this,'like_box_ifreame_generator') );
		//Genereting footer js code
		add_action( 'wp_footer', array($this,'like_box_popup_in_footer'));
		add_action( 'wp_footer', array($this,'like_box_sibar_slider_in_footer'));	
		add_action( 'wp_head',array($this,'generete_front_javascript'));
		// Genereting content code
		add_shortcode( 'wpdevart_like_box', array($this,'like_box_ifreame_content_generator') );
		$this->params=$this->generete_params();
		// For updated parameters
		
		$jsone_enable_like_box= json_decode(stripslashes($this->params['like_box_enable_like_box']), true); 
		if($jsone_enable_like_box!=NULL){
			if($jsone_enable_like_box['yes']==true){
				$this->params['like_box_enable_like_box']='yes';
			}elseif($jsone_enable_like_box['no']==true){
				$this->params['like_box_enable_like_box']='no';
			}else{
				$this->params['like_box_enable_like_box']='yes';
			}
		}		
		$jsone_like_box_header= json_decode(stripslashes($this->params['like_box_header']), true); 
		if($jsone_like_box_header!=NULL){
			if($jsone_like_box_header['show']==true){
				$this->params['like_box_header']='yes';
			}else{
				$this->params['like_box_header']='yes';
			}
		}
		
	}
	/*###################### CONNECTING TO THE PLUGIN DATABASE ##################*/
	private function generete_params(){
		
		foreach($this->databese_parametrs as $param_array_key => $param_value){
			foreach($this->databese_parametrs[$param_array_key] as $key => $value){
				$front_end_parametrs[$key]=stripslashes(get_option($key,$value));
			}
		}		
		return $front_end_parametrs;
		
	}
	/*###################### Scripts and Styles ##################*/
	public function generete_front_javascript(){
			wp_enqueue_style('animated');
			wp_enqueue_style('front_end_like_box');
			wp_enqueue_script('like-box-front-end');
			wp_enqueue_script('thickbox');
			wp_enqueue_style('thickbox');
		
	}
	
	/*###################### Creating Iframe ##################*/
	
	public function like_box_ifreame_content_generator($atts){
		self::$id_for_content++;
		$atts = shortcode_atts( array(
			'profile_id' =>  '',
			'show_cover_photo'=>'true',
			'animation_efect'=>'none',			
			'border_color' =>  '#FFFFFF',
			'show_border' =>  'show',
			'stream' =>  'hide',
			'connections' =>  'hide',
			'width' =>  '300',
			'height' =>  '550',
			'header' =>  'small',
			'locale' =>  'en_US',
		), $atts, 'wpdevart_like_box' );
		return  like_box_setting::generete_iframe_by_array($atts);
	}
	/*###################### Creating Iframe Popup ##################*/

	public function like_box_ifreame_generator(){
			
			$iframe_params=array(
					'iframe_id'  =>  'like_box_popup',
					'profile_id' =>  $this->params['like_box_profile_id'],
					'width' =>  (int)$this->params['like_box_width'], // Maximum width
					'height' =>  (int)$this->params['like_box_height'],// Height
					'show_border' =>  'show',
					'border_color' =>  '#FFF',
					'header' =>  $this->params['like_box_header'], // Like Box Header type
					'show_cover_photo'=> $this->params['like_box_cover_photo'],  //Header cover photo
					'connections' => $this->params['like_box_connections'],// Show Facebook user faces
					'stream' =>  'hide',			
					'animation_efect'=>'none',			
					'locale' =>   $this->params['like_box_locale'], // Language	
				
				);
			
		?>
        <html>
        <head>
        <style>
        #like_box_popup{
			<?php if($iframe_params['show_border']=='yes'){ ?>
				border:1px solid <?php echo $iframe_params['border_color']; ?>;
			
			<?php } ?>
			margin:0px;
			padding:0px;
		}
     body{overflow: hidden;}
        </style>
        
        </head>
        <body>
        <?php echo like_box_setting::generete_iframe_by_array($iframe_params);  ?>
        </body>
         <script>
			document.getElementById('like_box_popup').style.height=document.getElementsByTagName('body')[0].offsetHeight-20;
			window.onresize = function(event) {
				document.getElementById('like_box_popup').style.height=document.getElementsByTagName('body')[0].offsetHeight-8;
			};
        </script>
        </html>
        <?php
			die();

	}
	/*########################## Popup Function ########################*/
	
	public function like_box_popup_in_footer(){

		$width=$this->params['like_box_width'];
		$width=max(180,$width);
		$width=min(500,$width);
		$height=$this->params['like_box_height'];
		
		$ifame_parametrs=array();
		
		if($this->params['like_box_enable_like_box']=='yes'){
			?><script>
			var like_box_initial_width=<?php echo $width; ?>;
			var like_box_initial_height=<?php echo $height+12; ?>;
			jQuery(document).ready(function(){ 
			
			
				setTimeout(function(){
					tb_show('<?php echo $this->params['like_box_popup_title']; ?>','<?php echo admin_url('admin-ajax.php').'?action=likeboxfrontend&TB_iframe=true&width='.$width.'&height='.($height-12);?>')
					jQuery('#TB_window').addClass('facbook_like_box_popup');
					jQuery(window).resize(like_box_resize_popup);
					like_box_resize_popup();				
				
				},1000);
				
									
				
			})</script>>
			<style>
			.screen-reader-text{
				display:none;
			}
			.facbook_like_box_popup #TB_ajaxWindowTitle{
				color:<?php echo $this->params['like_box_popup_title_color']; ?>;
				font-family:<?php echo $this->params['like_box_popup_title_font_famely']; ?>;
			}
			.facbook_like_box_popup{    
			overflow: hidden;
			}
			.facbook_like_box_popup iframe{
				margin:0px;
				padding:0px;
				padding-left:5px;
			}
			</style>
			<?php
			
		}
	}
	/*############################### Like box footer sidebar function ########################################*/	
	
	public function css_like_box_sibar_slider_in_footer($width,$height){
		echo '<style>';
		
			echo '.like_box_slideup_close{left:-'.($width+2).'px;}';
			echo '.like_box_slideup_open{left:0px;}';
			echo '.sidbar_slide_header{';
			echo 'float:right; border-radius: 0 4px 4px 0;';
			echo '}';
			echo '.main_sidbar_slide{transition:left .3s;}';
	
		$top_for_margin=120;
		$top_for_margin=($this->params['like_box_sidebar_slide_height']-$this->params['like_box_sidebar_slide_pntik_height'])/2;
		echo '.sidbar_slide_header{height:'.$this->params['like_box_sidebar_slide_pntik_height'].'px; margin-top:'.$top_for_margin.'px;border-color:##405D9A !important;  background-color: #405D9A;}';
		echo '.sidbar_slide_title{font-family:'.$this->params['like_box_sidebar_slide_title_font_famely'].'; color: '.$this->params['like_box_sidebar_slide_title_color'].';}';
		echo '.sidbar_slide_content{width:'.$width.'px;}';
		echo '.sidbar_slide_inner_main {width:'.($width+40).'px;}';		
			
		echo '</style>';
		
	}

	/*############################### Sidebar slider function ########################################*/		
	
	public function like_box_sibar_slider_in_footer(){
		if($this->params['like_box_sidebar_slide_mode']=='yes'){
				$width=$this->params['like_box_sidebar_slide_width'];
				$width=min(500,(int)$width);
				$width=max(180,(int)$width);
				$height=$this->params['like_box_sidebar_slide_height'];
				$params_of_slideup=array(
					'iframe_id'  =>  'like_box_slideup',
					'profile_id' =>  $this->params['like_box_sidebar_slide_profile_id'],
					'width' =>  (int)$this->params['like_box_sidebar_slide_width'], // Maximum width
					'height' =>  (int)$this->params['like_box_sidebar_slide_height'],// Height
					'show_border' =>  'hide',
					'border_color' =>  '#FFFFF',
					'header' =>  $this->params['like_box_sidebar_slide_header'], // Header type
					'show_cover_photo'=> $this->params['like_box_sidebar_slide_cover_photo'],  // Header cover photo
					'connections' => $this->params['like_box_sidebar_slide_connections'],// Show facebook faces
					'stream' =>  'hide',			
					'animation_efect'=>'none',			
					'locale' =>   $this->params['like_box_sidebar_slide_locale'], // Language	
				
				);
				?>
			   <div class="main_sidbar_slide like_box_slideup_close">
					<div class="sidbar_slide_inner_main ">
						<div class="sidbar_slide_header">
							<span class="sidbar_slide_title"><?php echo $this->params['like_box_sidebar_slide_title']; ?></span>
						</div>
						<div class="sidbar_slide_content">
							<div class="sidbar_slide_inner">
                            <?php echo like_box_setting::generete_iframe_by_array($params_of_slideup);  ?>
                            </div>
							</div>
						</div>
						
					</div>
				</div>
				<?php
				$this->css_like_box_sibar_slider_in_footer($width,$height);
			}
		
		
	}
}
?>