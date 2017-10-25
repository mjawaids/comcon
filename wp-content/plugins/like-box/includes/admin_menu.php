<?php 

/*############ Facebook Like Box Admin Menu Class ################*/

class like_box_admin_menu{
	
	private $menu_name;
	private $databese_parametrs;
	private $plugin_url;
	private $text_parametrs;

	function __construct($param){
		
		$this->text_parametrs=array(
			'parametrs_sucsses_saved'=>'Successfully saved.',
			'error_in_saving'=>'can\'t save "%s" plugin parameter<br>',
			'missing_title'=>'Type Message Title',
			'missing_fromname'=>'Type From Name',
			'missing_frommail'=>'Type From mail',
			'mising_massage'=>'Type Message',
			'sucsses_mailed'=>'Your message was sent successfully.',
			'error_maied'=>'Error sending email',
			'authorize_problem' => 'Authorization Problem'
		);		
		
		$this->menu_name=$param['menu_name'];
		$this->databese_parametrs=$param['databese_parametrs'];
		if(isset($params['plugin_url']))
			$this->plugin_url=$params['plugin_url'];
		else
			$this->plugin_url=trailingslashit(dirname(plugins_url('',__FILE__)));


		// Insert button
		add_action('media_buttons_context', array($this,'like_box_button'));
		add_action( 'wp_ajax_like_box_window_manager', array($this,'like_box_window_insert_content') );
		
		add_action( 'wp_ajax_like_box_page_save', array($this,'save_in_databese') );
		add_action( 'wp_ajax_like_box_send_mail', array($this,'sending_mail') );
	}
	
	/*############################### Posts/Pages insert buttons function part ########################################*/
	
	public function like_box_button($context) {
	  
	  $img = $this->plugin_url. 'images/post.button.png';
	
	  $title = 'Add Like Box';
	
	  $context .= '<a class="button thickbox" title="Create facebook like box and insert in posts/pages"    href="'.admin_url("admin-ajax.php").'?action=like_box_window_manager&height=750&width=640">
			<span class="wp-media-buttons-icon" style="background: url('.$img.'); background-repeat: no-repeat; background-position: left bottom;"></span>
		Add like box
		</a>';  
	  return $context;
	}

	/*############################### Insert content function ########################################*/	
	
	public function like_box_window_insert_content(){
		?>
        <style>
        #miain_like_box_window_manager > tbody > tr:nth-child(odd) {
		  background-color: rgba(176, 176, 176, 0.07);
		}
		#miain_like_box_window_manager>tfoot>tr>td{
			border-top:1px solid #ccc;
		}
		#TB_window{  
			overflow-y: auto;
		}
		#TB_ajaxContent{
			width:95% !important;
		}
		.wp-picker-holder{
			position: absolute;
 			z-index: 100000;
		}
		.desription_class{
			float: right;
			cursor: default;
			color: #0074a2;
			font-size: 18px;
			font-weight: bold;
			border: 1px solid #000000;
			border-radius: 200px;
			height: 20px;
			padding-left: 6px;
			padding-right: 6px;
			margin-left: 15px;
		}
		.pro_feature {
		  font-size: 13px;
		  font-weight: bold;
		  color: rgba(10, 154, 62, 1);
		}
		.input_placholder_small{ width: 85%;}
		.input_placholder_small::-webkit-input-placeholder { font-size:11px; }
		.input_placholder_small::-moz-placeholder {font-size:11px; } /* firefox 19+ */
		.input_placholder_small:-ms-input-placeholder { font-size:11px; } /* ie */
		.input_placholder_smallinput:-moz-placeholder { font-size:11px; }
        </style>
        <script>var pro_text='"If you want to use this feature upgrade to Like box Pro"'</script>
			<table id="miain_like_box_window_manager" class="wp-list-table widefat fixed posts section_parametrs_table">  
                <tbody> 
                    <tr>
                        <td>
                            Page ID <span class="desription_class" title="Type here Facebook fan page url(without https://www.facebook.com/ or https://web.facebook.com, if your Facebook fan page url is https://web.facebook.com/BMW then type here just BMW).">?</span>
                        </td>
                        <td>
                           <input id="like_box_profile_id" type="text" value="" class="widefat">
                        </td>                
                    </tr>
                    <tr>
                        <td>
                         	Like box Animation <span class="pro_feature"> (pro)</span><span  class="desription_class" title="Choose Facebook like box Animation type.">?</span>
                        </td>
                        <td>
                          <?php  like_box_setting::generete_animation_select('animation_efect','none') ?>
                        </td>                
                    </tr>
                    <tr>
                        <td>
                         	Like box border <span class="pro_feature"> (pro)</span><span title="Show Facebook like box border." class="desription_class">?</span>
                        </td>
                        <td>
                            <select onMouseDown="alert(pro_text); return false;" id="like_box_show_border">
                                <option selected="selected" value="show">Show</option>
                                <option value="hide">Hide</option>
                            </select>
                        </td>                
                    </tr>
                     <tr>
                        <td>
                         	Like box border color <span class="pro_feature"> (pro)</span><span title="Select the Border Color of Facebook Like box." class="desription_class">?</span>
                        </td>
                        <td>
                          <div onClick="alert(pro_text); return false;" class="disabled_picker">
                                <div class="wp-picker-container"><a tabindex="0" class="wp-color-result" title="Select Color" data-current="Current Color" style="background-color: rgb(255, 255, 255);"></a></div>
                            </div>
                        </td>                
                    </tr>
                    <tr>
                        <td>
                         	Facebook latest posts <span class="pro_feature"> (pro)</span><span  class="desription_class" title="Show the Latest Posts.">?</span>
                        </td>
                        <td>
                          	<select onMouseDown="alert(pro_text); return false;" id="like_box_stream">
                                <option  value="show">Show</option>
                                <option selected="selected" value="hide">Hide</option>
                            </select>
                        </td>                
                    </tr>
                     <tr>
                        <td>
                         	Show Users Faces <span title="Show/hide Users Faces" class="desription_class">?</span>
                        </td>
                        <td>
                        	<select  id="like_box_connections">
                                <option selected="selected" value="show">Show</option>
                                <option value="hide">Hide</option>
                            </select>
                        </td>                
             		</tr>
                     <tr id="like_box_static_width">
                        <td>
                         	Like box Width <span title="Type here Facebook Like box width(px)" class="desription_class">?</span>
                        </td>
                        <td>
                          <input placeholder="The pixel width of the embed (Min. 180px to Max. 500px)" id="like_box_width"  type="text" value="300" class="input_placholder_small" ><small>(px)</small>
                        </td>                
                    </tr>
                    <tr>
                        <td>
                         	Like box Height <span title="Type here Facebook Like box height(px)" class="desription_class">?</span>
                        </td>
                        <td>
                          <input placeholder="The pixel height of the embed (Min. 70px)" id="like_box_height" type="text" value="550" class="input_placholder_small" ><small>(px)</small>
                        </td>                
                    </tr>
                     <tr>
                        <td>
                         	Like box Header <span title="Select Like box Header size(Small/Big)" class="desription_class">?</span>
                        </td>
                        <td>
                          <select id="like_box_header_size">
                                <option selected="selected" value="small">Small</option>
                                <option value="big">Big</option>
                          </select>
                        </td>                
                    </tr>
                     <tr>
                        <td>
                         	Like box cover photo <span title="Choose to show/hide Like box cover photo" class="desription_class">?</span>
                        </td>
                        <td>
                          <select id="like_box_cover_photo">
                                <option selected="selected" value="show">Show</option>
                                <option value="hide">Hide</option>
                          </select>
                        </td>                
                    </tr>
                    <tr>
                        <td>
                         	Language <span title="Type here the Facebook Like box language code." class="desription_class">?</span>
                        </td>
                        <td>
                         <input id="like_box_locale" type="text" value="en_US" class="" size="4"><small>(en_US, de_DE...)</small>
                        </td>                
                    </tr>
                </tbody>
               <tfoot>
                	<tr>                      
                        <td colspan="2">
                        	 <div style="display:inline-block; float:left;" class="mceActionPanel"><input type="button" id="cancel" name="cancel" value="Insert Like Box" class="button button-primary" onClick="insert_like_box();"/></div>
                             <span style="float:right"><a href="http://wpdevart.com/wordpress-facebook-like-box-plugin/" target="_blank" style="color: rgba(10, 154, 62, 1);; font-weight: bold; font-size: 18px; text-decoration: none;">Upgrade to Pro Version</a><br></span>
                        </td>                
                    </tr>
                </tfoot>
            </table>         
    
                   
    
    <script type="text/javascript">
	
	
       jQuery('#TB_window').css('max-height',(jQuery('#miain_like_box_window_manager').height()+66)+'px');
	   jQuery('#TB_ajaxContent').css('max-height',(jQuery('#miain_like_box_window_manager').height()+16)+'px');
	   jQuery('#miain_like_box_window_manager').ready(function(e) {
                jQuery(".color_my_likbox").wpColorPicker();
        });
        function insert_like_box() {
          
			if(jQuery('#like_box_profile_id').val()!=''){
                var tagtext;
				var generete_atributes = 'profile_id="'+jQuery('#like_box_profile_id').val()+'" connections="'+jQuery('#like_box_connections').val()+'" width="'+jQuery('#like_box_width').val()+'" height="'+jQuery('#like_box_height').val()+'" header="'+jQuery('#like_box_header_size').val()+'" cover_photo="'+jQuery('#like_box_cover_photo').val()+'" locale="'+jQuery('#like_box_locale').val()+'"'

                tagtext = '[wpdevart_like_box '+generete_atributes+']';
                window.send_to_editor(tagtext);
              	tb_remove()
            }
			else{
				alert('Page id field is required')				
			}
        }

    </script>
    </body>
    </html>
<?php
die;	
}

	/*############################### Create menu function ########################################*/	
	
	public function create_menu(){
		$main_page 	 	  = add_menu_page( $this->menu_name, $this->menu_name, 'manage_options', str_replace( ' ', '-', $this->menu_name), array($this, 'main_menu_function'),$this->plugin_url.'images/facebook_menu_icon.png');
		$page_like_box	  =	add_submenu_page($this->menu_name,  $this->menu_name,  $this->menu_name, 'manage_options', str_replace( ' ', '-', $this->menu_name), array($this, 'main_menu_function'));
		$page_like_box	  = add_submenu_page( str_replace( ' ', '-', $this->menu_name), 'Featured Plugins', 'Featured Plugins', 'manage_options', 'like-box-featured-plugins', array($this, 'featured_plugins'));
		add_action('admin_print_styles-' .$main_page, array($this,'menu_requeried_scripts'));
		add_action('admin_print_styles-' .$page_like_box, array($this,'menu_requeried_scripts'));		
	}
	
	/*###################### Required scripts function ##################*/	
	
	public function menu_requeried_scripts(){
		wp_enqueue_script('wp-color-picker');		
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'like-box-admin-script' ); 
		wp_enqueue_style('like-box-admin-style');
	}

	/*############################### Generate parameters function ########################################*/	
	
	private function generete_parametrs($page_name){
		$page_parametrs=array();
		if(isset($this->databese_parametrs[$page_name])){
			foreach($this->databese_parametrs[$page_name] as $key => $value){
				$page_parametrs[$key]=get_option($key,$value);
			}
			return $page_parametrs;
		}
		return NULL;
		
	}
	
	/*############################### Database function ########################################*/	
	
	public function save_in_databese(){
		$kk=1;	
			
		if(isset($_POST['like_box_options_nonce']) && wp_verify_nonce( $_POST['like_box_options_nonce'],'like_box_options_nonce')){
			foreach($this->databese_parametrs[$_POST['curent_page']] as $key => $value){
				if(isset($_POST[$key]))
					update_option($key,$_POST[$key]);
				else{
					$kk=0;
					printf($this->text_parametrs['error_in_saving'],$key);
				}
			}	
		}
		else{		
			die($this->text_parametrs['authorize_problem']);
		}
		if($kk==0){
			exit;
		}
		die($this->text_parametrs['parametrs_sucsses_saved']);
	}
	
    /*############ Main menu Function ##################*/	
	
	public function main_menu_function(){	
	
	$enable_disable=$this->generete_parametrs('general_save_parametr');	
	$enable_disable=$enable_disable['like_box_page_mode'];
		?>
        <script>
        var like_box_ajaxurl="<?php echo admin_url( 'admin-ajax.php'); ?>";
		var like_box_plugin_url="<?php echo $this->plugin_url; ?>";
		var like_box_parametrs_sucsses_saved="<?php echo $this->text_parametrs['parametrs_sucsses_saved'] ?>";
		var like_box_all_parametrs = <?php echo json_encode($this->databese_parametrs); ?>;
        </script>
      <div class="coming_title"><h1>Facebook Like Box <a style="text-decoration:none;" href="http://wpdevart.com/wordpress-facebook-like-box-plugin/"><span style="color: rgba(10, 154, 62, 1);"> (Upgrade to Pro Version)</span></a></h1></div>      
      
	<br>
     
       <div class="wp-table right_margin">
        <table class="wp-list-table widefat fixed posts">
        	<thead>
                <tr>
                    <th>     
                     <h4 class="live_previev">Parameters</h4>              
                   <span class="save_all_paramss"> <button type="button" id="save_all_parametrs" class="save_all_section_parametrs button button-primary"><span class="save_button_span">Save All Sections</span> <span class="saving_in_progress"> </span><span class="sucsses_save"> </span><span class="error_in_saving"> </span></button></span>
                    </th>
                </tr>
         	</thead>
            <tbody>
            <tr>
            	<td>
                <div id="like_box_page">
    				<div class="left_sections">
						<?php
                       		$this->generete_popup_section($this->generete_parametrs('popup_like_box'));										
                       	?>
                     </div>
    				 <div class="right_sections">
                     <?php
					 		$this->generete_sidbar_slide_section($this->generete_parametrs('sidbar_slide_like_box'));	
                     ?>
                  </div><div style="clear:both"></div>
               </td>
       		</tr>
            </tbody>
            <tfoot>
                <tr>
                    <th>                   
                    	<span class="save_all_paramss"><button type="button" id="save_all_parametrs" class="save_all_section_parametrs button button-primary"><span class="save_button_span">Save All Sections</span> <span class="saving_in_progress"> </span><span class="sucsses_save"> </span><span class="error_in_saving"> </span></button></span>
                    </th>
                </tr>
         	</tfoot>
        </table>
        </div>      
       <?php
	  wp_nonce_field('like_box_options_nonce','like_box_options_nonce');
	}
	
	
	/*#########################  LIKE BOX POPUP SECTION CODE #################################*/
	public function generete_popup_section($page_parametrs){
		//for updated parameters
			$jsone_enable_like_box= json_decode(stripslashes($page_parametrs['like_box_enable_like_box']), true); 
			if($jsone_enable_like_box!=NULL){
				if($jsone_enable_like_box['yes']==true){
					$page_parametrs['like_box_enable_like_box']='yes';
				}elseif($jsone_enable_like_box['no']==true){
					$page_parametrs['like_box_enable_like_box']='no';
				}else{
					$page_parametrs['like_box_enable_like_box']='yes';
				}
			}
			
			
			$jsone_like_box_header= json_decode(stripslashes($page_parametrs['like_box_header']), true); 
			if($jsone_like_box_header!=NULL){
				if($jsone_like_box_header['show']==true){
					$page_parametrs['like_box_header']='yes';
				}else{
					$page_parametrs['like_box_header']='yes';
				}
			}

		?>
		<div class="main_parametrs_group_div">
			<div class="head_panel_div">
            	<span class="title_parametrs_image"><img src="<?php echo $this->plugin_url.'images/facebook_popup.png' ?>"></span>
				<span class="title_parametrs_group">Facebook popup</span>
				<span class="enabled_or_disabled_parametr"></span>
				<span class="open_or_closed"></span>         
			</div>
			<div class="inside_information_div">
				<table class="wp-list-table widefat fixed posts section_parametrs_table">                            
				<tbody> 
               		<tr>
						<td>
							Enable/Disable popup <span title="Select Enable/Disable to show or hide popup." class="desription_class">?</span>
						</td>
						<td>
							<select id="like_box_enable_like_box">                         
                                <option <?php selected($page_parametrs['like_box_enable_like_box'],'yes') ?> value="yes">Enable</option>
                                <option <?php selected($page_parametrs['like_box_enable_like_box'],'no') ?> value="no">Disable</option>
                        	</select>
						</td>                
					</tr>
                	<tr>
						<td>
							Display Popup on <span class="pro_feature"> (pro)</span> <span title="Select where to display the like box popup." class="desription_class">?</span>
						</td>
						<td>					                            
                           <input class="pro_checkbox" id="like_box_show_in_home" type="checkbox" value="home" class="" size="" checked="checked"><small>Home</small><br>
                            <input class="pro_checkbox" id="like_box_show_in_post" type="checkbox" value="post" class="" size="" checked="checked"><small>Post</small><br>
                            <input class="pro_checkbox" id="like_box_show_in_page" type="checkbox" value="page" class="" size="" checked="checked"><small>Page</small><br>
                            <input class="pro_checkbox" id="like_box_show_in_everywhere" type="checkbox" value="everywhere" class="" size="" checked="checked"><small>Everywhere</small><br>
                        </td>                
					</tr>                            
					<tr>
						<td>
							Popup display periodicity  <span class="pro_feature"> (pro)</span> <span title="Select the popup display periodicity." class="desription_class">?</span>
						</td>
						<td>
                           <select class="pro_select" id="like_box_popup_show_quantity">
                                <option value="onew_time">Оne Тime</option>
                                <option selected="selected" value="on_refresh">Еvery Тime</option>
                        	</select>
                         </td>                
					</tr>
                    <tr>
						<td>
							Time to show Popup <span class="pro_feature"> (pro)</span> <span title="Type the time when Like box popup will appear." class="desription_class">?</span>
						</td>
						<td>
							<input class="pro_input" type="text" name="like_box_secont_befor_show"  id="like_box_secont_befor_show" value="1">(Seconds)
						</td>                
					</tr>
                    <tr>
						<td>
							Popup width <span title="Type here the popup width(px)" class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="like_box_width" id="like_box_width" value="<?php echo $page_parametrs['like_box_width'] ?>">(Px)
						</td>                
					</tr>
                    <tr>
						<td>
							Popup height <span title="Type here the popup height(px)" class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="like_box_height" id="like_box_height" value="<?php echo $page_parametrs['like_box_height'] ?>">(Px)
						</td>                
					</tr>
                    
                    <tr>
						<td>
							 Popup title <span title="Type here the popup title" class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="like_box_popup_title" id="like_box_popup_title" value="<?php echo $page_parametrs['like_box_popup_title'] ?>">
						</td>                
					</tr>
                     <tr >
                        <td>
                           Popup title color <span title="Type here the popup title color" class="desription_class">?</span>
                        </td>
                        <td>
                            <input type="text" class="color_option" id="like_box_popup_title_color" name="like_box_popup_title_color"  value="<?php echo $page_parametrs['like_box_popup_title_color'] ?>"/>
                         </td>                
                    </tr>
                    <tr>
						<td>
							Popup title Font Family <span title="Choose the title Font family" class="desription_class">?</span>
						</td>
						<td>
							<?php $this->create_select_element_for_font('like_box_popup_title_font_famely',$page_parametrs['like_box_popup_title_font_famely']) ?>
						</td>                
					</tr>
                    <tr>
						<td>
							Page ID  <span title="Type here your Facebook like box page url(without https://www.facebook.com/, if your Facebook page url is https://web.facebook.com/AnIMals then type here just AnIMals)." class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="like_box_profile_id"   id="like_box_profile_id" value="<?php echo $page_parametrs['like_box_profile_id'] ?>">
						</td>                
					</tr>
                    
                    <tr>
                  
						<td>
							Show/Hide border <span class="pro_feature"> (pro)</span>  <span title="Show/hide Facebook like box border." class="desription_class">?</span>
						</td>
						<td>
                           <select class="pro_select" id="like_box_show_border">
                                <option selected="selected" value="show">Show</option>
                                <option  value="hide">Hide</option>
                        	</select>
                         </td>                
					</tr>
                     <tr >
                        <td>
                           Border color <span class="pro_feature"> (pro)</span> <span title="Type the Border Color of your Facebook Like box." class="desription_class">?</span>
                        </td>
                        <td>
							<div class="disabled_picker">
                                <div class="wp-picker-container"><a tabindex="0" class="wp-color-result" title="Select Color" data-current="Current Color" style="background-color: rgb(255, 255, 255);"></a></div>
                            </div>                        
                        </td>                
                    </tr>
                     
                  <tr>
                  
						<td>
							Show Users Faces  <span title="Select to Show/hide Users Faces" class="desription_class">?</span>
						</td>
						<td>                  
                          <select id="like_box_connections">
                                <option <?php selected($page_parametrs['like_box_connections'],'show') ?> value="show">Show</option>
                                <option <?php selected($page_parametrs['like_box_connections'],'hide') ?> value="hide">Hide</option>
                        	</select>
                         </td>                
					</tr>
                     <tr>
						<td>
							Like box Header size <span title="Select Like box Header size" class="desription_class">?</span>
						</td>
						<td>
                           <select id="like_box_header">
                                <option <?php selected($page_parametrs['like_box_header'],'small') ?> value="small">Small</option>
                                <option <?php selected($page_parametrs['like_box_header'],'big') ?> value="big">Big</option>
                        	</select>
                         </td>                
					</tr>
                  <tr>
						<td>
							Like box cover photo  <span title="Select to show/hide Like box cover photo" class="desription_class">?</span>
						</td>
						<td>
                           <select id="like_box_cover_photo">
                                <option <?php selected($page_parametrs['like_box_cover_photo'],'show') ?> value="show">Show</option>
                                <option <?php selected($page_parametrs['like_box_cover_photo'],'hide') ?> value="hide">Hide</option>
                        	</select>
                         </td>                
					</tr>
                    <tr>
						<td>
							Show/Hide latest posts <span class="pro_feature"> (pro)</span> <span title="Show/hide the latest posts from your Facebook Like box." class="desription_class">?</span>
						</td>
						<td>
                           <select class="pro_select" id="like_box_stream">
                                <option  value="show">Show</option>
                                <option selected="selected" value="hide">Hide</option>
                        	</select>
                         </td>                
					</tr>
                    
                     <tr>
						<td>
							Like box language <span title="Type the Facebook Like box language code." class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="like_box_locale"   id="like_box_locale" value="<?php echo $page_parametrs['like_box_locale'] ?>">(en_US,de_DE...)
						</td>                
					</tr>
				</tbody>
					<tfoot>
						<tr>
							<th colspan="1" width="100%"><button type="button" id="popup_like_box" class="save_section_parametrs button button-primary"><span class="save_button_span">Save Section</span> <span class="saving_in_progress"> </span><span class="sucsses_save"> </span><span class="error_in_saving"> </span></button><span class="error_massage"> </span></th>
							<th><button type="button" onClick="like_box_setCookie('facbook_like_box_popup','',2); alert('cookie removed')" class="save_button button button-primary" style="float:right;"><span class="save_button_span">Remove cookies</span></button></th>
                        </tr>
					</tfoot>       
				</table>
			</div>     
		</div>        
		<?php	
	}
	/*#########################  STICKY BOX SECTION   #################################*/
	public function generete_sidbar_slide_section($page_parametrs){

		?>
		<div class="main_parametrs_group_div " >
			<div class="head_panel_div">
            	<span class="title_parametrs_image"><img src="<?php echo $this->plugin_url.'images/facebook_sidebar.png' ?>"></span>
				<span class="title_parametrs_group">Sticky Facebook box</span>
				<span class="enabled_or_disabled_parametr"></span>
				<span class="open_or_closed"></span>         
			</div>
			<div class="inside_information_div">
				<table class="wp-list-table widefat fixed posts section_parametrs_table">                            
				<tbody> 
               		<tr>
						<td>
							Enable/Diasable Sticky box <span title="Select Enable/Disable to show or hide Sticky box." class="desription_class">?</span>
						</td>
						<td>
							<select id="like_box_sidebar_slide_mode">
                                <option <?php selected($page_parametrs['like_box_sidebar_slide_mode'],'1') ?> value="yes">Enable</option>
                                <option <?php selected($page_parametrs['like_box_sidebar_slide_mode'],'0') ?> value="no">Disable</option>
                        	</select>
						</td>                
					</tr>
                	<tr>
						<td>
							Display Sticky box on <span class="pro_feature"> (pro)</span> <span title="Select where to display the Sticky box. " class="desription_class">?</span>
						</td>
						<td>
                            <input class="pro_checkbox" id="like_box_show_in_home" type="checkbox" value="home" class="" size="" checked="checked"><small>Home</small><br>
                            <input class="pro_checkbox" id="like_box_show_in_post" type="checkbox" value="post" class="" size="" checked="checked"><small>Post</small><br>
                            <input class="pro_checkbox" id="like_box_show_in_page" type="checkbox" value="page" class="" size="" checked="checked"><small>Page</small><br>
                            <input class="pro_checkbox" id="like_box_show_in_everywhere" type="checkbox" value="everywhere" class="" size="" checked="checked"><small>Everywhere</small><br>
                        </td>                
					</tr> 
                    <tr>
						<td>
							Sticky box position <span class="pro_feature"> (pro)</span> <span title="Select the Sticky box position." class="desription_class">?</span>
						</td>
						<td>
							<select class="pro_select" id="like_box_sidebar_slide_position">
                                <option selected="selected" value="left">Left</option>
                                <option value="right">Right</option>
                        	</select>
						</td>                
					</tr>
                    <tr>
						<td>
							Sticky box height <span title="Select the Sticky box height." class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="like_box_sidebar_slide_pntik_height"   id="like_box_sidebar_slide_pntik_height" value="<?php echo $page_parametrs['like_box_sidebar_slide_pntik_height'] ?>">(px)
						</td>                
					</tr>
                    <tr>
                        <td>
                           Sticky box button bg color  <span class="pro_feature"> (pro)</span> <span title="Type here the Sticky box button background color." class="desription_class">?</span>
                        </td>
                        <td>
                         <div class="disabled_picker">
                                <div class="wp-picker-container"><a tabindex="0" class="wp-color-result" title="Select Color" data-current="Current Color" style="background-color: rgb(62, 89, 165);"></a></div>
                            </div>                         
                         </td>                
                    </tr>
                    <tr>
                        <td>
                          Sticky box border color <span class="pro_feature"> (pro)</span> <span title="Type here the Sticky box border color." class="desription_class">?</span>
                        </td>
                        <td>
                        <div class="disabled_picker">
                                <div class="wp-picker-container"><a tabindex="0" class="wp-color-result" title="Select Color" data-current="Current Color" style="background-color: rgb(62, 89, 165);"></a></div>
                            </div>
                        </td>                
                    </tr>
                    <tr>
						<td>
							 Sticky box Title <span title="Type here the Sticky box title." class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="like_box_sidebar_slide_title" id="like_box_sidebar_slide_title" value="<?php echo $page_parametrs['like_box_sidebar_slide_title'] ?>">
						</td>                
					</tr>
                     <tr>
                        <td>
                            Sticky box Title color <span title="Select the Sticky box title color." class="desription_class">?</span>
                        </td>
                        <td>
                            <input type="text" class="color_option" id="like_box_sidebar_slide_title_color" name="like_box_sidebar_slide_title_color"  value="<?php echo $page_parametrs['like_box_sidebar_slide_title_color'] ?>"/>
                         </td>                
                    </tr>
                    <tr>
						<td>
							Sticky box Title Font family <span title="hoose the Sticky box Title Font family." class="desription_class">?</span>
						</td>
						<td>
							<?php $this->create_select_element_for_font('like_box_sidebar_slide_title_font_famely',$page_parametrs['like_box_sidebar_slide_title_font_famely']) ?>
						</td>                
					</tr>
                     <tr>
						<td>
							Page ID  <span title=" Type here your Facebook like box page url(without https://www.facebook.com/, if your Facebook page url is https://web.facebook.com/AnIMals then type here just AnIMals)." class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="like_box_sidebar_slide_profile_id"   id="like_box_sidebar_slide_profile_id" value="<?php echo $page_parametrs['like_box_sidebar_slide_profile_id'] ?>">
						</td>                
					</tr>
                     
                                
                    <tr>
						<td>
							Like box width <span title=" Type here the Like box width(px)." class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="like_box_sidebar_slide_width" id="like_box_sidebar_slide_width" value="<?php echo $page_parametrs['like_box_sidebar_slide_width'] ?>">(Px)
						</td>                
					</tr>                    
                    <tr>
						<td>
							Like box height <span title="Type here the Like box height(px)." class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="like_box_sidebar_slide_height" id="like_box_sidebar_slide_height" value="<?php echo $page_parametrs['like_box_sidebar_slide_height'] ?>">(Px)
						</td>                
					</tr>
                     
                  <tr>
						<td>
							Show/Hide Users Faces <span title="Select to Show/Hide Users Faces" class="desription_class">?</span>
						</td>
						<td>
                        	<select id="like_box_sidebar_slide_connections">
                                <option <?php selected($page_parametrs['like_box_sidebar_slide_connections'],'show') ?>  value="show">Show</option>
                                <option <?php selected($page_parametrs['like_box_sidebar_slide_connections'],'hide') ?> value="hide">Hide</option>
                            </select>
                         </td>                
					</tr>
                     <tr>
						<td>
							Like box Header size <span title="Select Like box Header size" class="desription_class">?</span>
						</td>
						<td>
                           <select id="like_box_sidebar_slide_header">
                                <option <?php selected($page_parametrs['like_box_sidebar_slide_header'],'small') ?> value="small">Small</option>
                                <option <?php selected($page_parametrs['like_box_sidebar_slide_header'],'big') ?> value="big">Big</option>
                        	</select>
                         </td>                
					</tr>
                   <tr>
						<td>
							Like box cover photo <span title="Select to show/hide Like box cover photo" class="desription_class">?</span>
						</td>
						<td>
                           <select id="like_box_sidebar_slide_cover_photo">
                                <option <?php selected($page_parametrs['like_box_sidebar_slide_cover_photo'],'show') ?> value="show">Show</option>
                                <option <?php selected($page_parametrs['like_box_sidebar_slide_cover_photo'],'hide') ?> value="hide">Hide</option>
                        	</select>
                         </td>                
					</tr>
                    <tr>
						<td>
							Show/Hide latest posts <span class="pro_feature"> (pro)</span> <span title="Show/Hide the latest posts from your Facebook Like box." class="desription_class">?</span>
						</td>
						<td>
                           <select class="pro_select" id="like_box_sidebar_slide_stream">
                                <option  value="show">Show</option>
                                <option selected="selected" value="hide">Hide</option>
                        	</select>
                         </td>                
					</tr>
                    
                     <tr>
						<td>
							Like box language  <span title="Type the Facebook Like box language code." class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="like_box_sidebar_slide_locale"   id="like_box_sidebar_slide_locale" value="<?php echo $page_parametrs['like_box_sidebar_slide_locale'] ?>">(en_US,de_DE...)
						</td>                
					</tr>
                   
				</tbody>
					<tfoot>
						<tr>
							<th colspan="2" width="100%"><button type="button" id="sidbar_slide_like_box" class="save_section_parametrs button button-primary"><span class="save_button_span">Save Section</span> <span class="saving_in_progress"> </span><span class="sucsses_save"> </span><span class="error_in_saving"> </span></button><span class="error_massage"> </span></th>
						</tr>
					</tfoot>       
				</table>
			</div>     
		</div>        
		<?php	
	}
		    /*#################### Featured plugins page function ########################*/
	public function featured_plugins(){
		$plugins_array=array(
			'coming_soon'=>array(
						'image_url'		=>	$this->plugin_url.'images/featured_plugins/coming_soon.jpg',
						'site_url'		=>	'http://wpdevart.com/wordpress-coming-soon-plugin/',
						'title'			=>	'Coming soon and Maintenance mode',
						'description'	=>	'Coming soon and Maintenance mode plugin is an awesome tool to show your visitors that you are working on your website to make it better.'
						),
			'Contact forms'=>array(
						'image_url'		=>	$this->plugin_url.'images/featured_plugins/contact_forms.png',
						'site_url'		=>	'http://wpdevart.com/wordpress-contact-form-plugin/',
						'title'			=>	'Contact form Builder',
						'description'	=>	'Contact Form Builder plugin is an nice and handy tool for creating different types of contact forms on your WordPress websites.'
						),
			'Booking Calendar'=>array(
						'image_url'		=>	$this->plugin_url.'images/featured_plugins/Booking_calendar_featured.png',
						'site_url'		=>	'http://wpdevart.com/wordpress-booking-calendar-plugin/',
						'title'			=>	'Booking Calendar',
						'description'	=>	'WordPress Booking Calendar plugin is an awesome tool to create a booking system for your website. Create booking calendars in a few minutes.'
						),	
			'youtube'=>array(
						'image_url'		=>	$this->plugin_url.'images/featured_plugins/youtube.png',
						'site_url'		=>	'http://wpdevart.com/wordpress-youtube-embed-plugin',
						'title'			=>	'WordPress YouTube Embed',
						'description'	=>	'YouTube Embed plugin is an convenient tool for adding video to your website. Use YouTube Embed plugin to add YouTube videos in posts/pages, widgets.'
						),
			'countdown'=>array(
						'image_url'		=>	$this->plugin_url.'images/featured_plugins/countdown.jpg',
						'site_url'		=>	'http://wpdevart.com/wordpress-countdown-plugin/',
						'title'			=>	'WordPress Countdown plugin',
						'description'	=>	'WordPress Countdown plugin is an nice tool to create and insert countdown timers into your posts/pages and widgets.'
						),
			'lightbox'=>array(
						'image_url'		=>	$this->plugin_url.'images/featured_plugins/lightbox.png',
						'site_url'		=>	'http://wpdevart.com/wordpress-lightbox-plugin',
						'title'			=>	'WordPress Lightbox plugin',
						'description'	=>	'WordPress lightbox plugin is an high customizable and responsive product for displaying images and videos in popup.'
						),
            'facebook-comments'=>array(
						'image_url'		=>	$this->plugin_url.'images/featured_plugins/facebook-comments-icon.png',
						'site_url'		=>	'http://wpdevart.com/wordpress-facebook-comments-plugin/',
						'title'			=>	'WordPress Facebook comments',
						'description'	=>	'Our Facebook comments plugin will help you to display Facebook Comments on your website. You can use Facebook Comments on your pages/posts.'
						),						
			'facebook'=>array(
						'image_url'		=>	$this->plugin_url.'images/featured_plugins/facebook.jpg',
						'site_url'		=>	'http://wpdevart.com/wordpress-facebook-like-box-plugin',
						'title'			=>	'Facebook Like Box',
						'description'	=>	'Our Facebook like box plugin will help you to display Facebook like box on your wesite, just add Facebook Like box widget to your sidebar and use it..'
						),
			'poll'=>array(
						'image_url'		=>	$this->plugin_url.'images/featured_plugins/poll.png',
						'site_url'		=>	'http://wpdevart.com/wordpress-polls-plugin',
						'title'			=>	'Poll',
						'description'	=>	'WordPress Polls plugin is an wonderful tool for creating polls and survey forms for your visitors. You can use our polls on widgets, posts and pages.'
						),														
		);
		?>
        <style>
         .featured_plugin_main{
			 background-color: #ffffff;
			 border: 1px solid #dedede;
			 box-sizing: border-box;
			 float:left;
			 margin-right:20px;
			 margin-bottom:20px;
			 
			 width:450px;
		 }
		.featured_plugin_image{
			padding: 15px;
			display: inline-block;
			float:left;
		}
		.featured_plugin_image a{
		  display: inline-block;
		}
		.featured_plugin_information{			
			float: left;
			width: auto;
			max-width: 282px;

		}
		.featured_plugin_title{
			color: #0073aa;
			font-size: 18px;
			display: inline-block;
		}
		.featured_plugin_title a{
			text-decoration:none;
					
		}
		.featured_plugin_title h4{
			margin:0px;
			margin-top: 20px;
			margin-bottom:8px;			  
		}
		.featured_plugin_description{
			display: inline-block;
		}
        
        </style>
        <script>
		
        jQuery(window).resize(like_box_feature_resize);
		jQuery(document).ready(function(e) {
            like_box_feature_resize();
        });
		
		function like_box_feature_resize(){
			var like_box_width=jQuery('.featured_plugin_main').eq(0).parent().width();
			var count_of_elements=Math.max(parseInt(like_box_width/450),1);
			var width_of_plugin=((like_box_width-count_of_elements*24-2)/count_of_elements);
			jQuery('.featured_plugin_main').width(width_of_plugin);
			jQuery('.featured_plugin_information').css('max-width',(width_of_plugin-160)+'px');
		}
       	</script>
        	<h2>Featured Plugins</h2>
            <br>
            <br>
            <?php foreach($plugins_array as $key=>$plugin) { ?>
            <div class="featured_plugin_main">
            	<span class="featured_plugin_image"><a target="_blank" href="<?php echo $plugin['site_url'] ?>"><img src="<?php echo $plugin['image_url'] ?>"></a></span>
                <span class="featured_plugin_information">
                	<span class="featured_plugin_title"><h4><a target="_blank" href="<?php echo $plugin['site_url'] ?>"><?php echo $plugin['title'] ?></a></h4></span>
                    <span class="featured_plugin_description"><?php echo $plugin['description'] ?></span>
                </span>
                <div style="clear:both"></div>                
            </div>
            <?php } 
	}
	/*######################################### SUBSCRIBE SECTION #######################################*/

	private function create_select_element_for_font($select_id='',$curent_font='none'){
	?>
   <select id="<?php echo $select_id; ?>" name="<?php echo $select_id; ?>">
   
        <option <?php selected('Arial,Helvetica Neue,Helvetica,sans-serif',$curent_font); ?> value="Arial,Helvetica Neue,Helvetica,sans-serif">Arial *</option>
        <option <?php selected('Arial Black,Arial Bold,Arial,sans-serif',$curent_font); ?> value="Arial Black,Arial Bold,Arial,sans-serif">Arial Black *</option>
        <option <?php selected('Arial Narrow,Arial,Helvetica Neue,Helvetica,sans-serif',$curent_font); ?> value="Arial Narrow,Arial,Helvetica Neue,Helvetica,sans-serif">Arial Narrow *</option>
        <option <?php selected('Courier,Verdana,sans-serif',$curent_font); ?> value="Courier,Verdana,sans-serif">Courier *</option>
        <option <?php selected('Georgia,Times New Roman,Times,serif',$curent_font); ?> value="Georgia,Times New Roman,Times,serif">Georgia *</option>
        <option <?php selected('Times New Roman,Times,Georgia,serif',$curent_font); ?> value="Times New Roman,Times,Georgia,serif">Times New Roman *</option>
        <option <?php selected('Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Arial,sans-serif',$curent_font); ?> value="Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Arial,sans-serif">Trebuchet MS *</option>
        <option <?php selected('Verdana,sans-serif',$curent_font); ?> value="Verdana,sans-serif">Verdana *</option>
        <option <?php selected('American Typewriter,Georgia,serif',$curent_font); ?> value="American Typewriter,Georgia,serif">American Typewriter</option>
        <option <?php selected('Andale Mono,Consolas,Monaco,Courier,Courier New,Verdana,sans-serif',$curent_font); ?> value="Andale Mono,Consolas,Monaco,Courier,Courier New,Verdana,sans-serif">Andale Mono</option>
        <option <?php selected('Baskerville,Times New Roman,Times,serif',$curent_font); ?> value="Baskerville,Times New Roman,Times,serif">Baskerville</option>
        <option <?php selected('Bookman Old Style,Georgia,Times New Roman,Times,serif',$curent_font); ?> value="Bookman Old Style,Georgia,Times New Roman,Times,serif">Bookman Old Style</option>
        <option <?php selected('Calibri,Helvetica Neue,Helvetica,Arial,Verdana,sans-serif',$curent_font); ?> value="Calibri,Helvetica Neue,Helvetica,Arial,Verdana,sans-serif">Calibri</option>
        <option <?php selected('Cambria,Georgia,Times New Roman,Times,serif',$curent_font); ?> value="Cambria,Georgia,Times New Roman,Times,serif">Cambria</option>
        <option <?php selected('Candara,Verdana,sans-serif',$curent_font); ?> value="Candara,Verdana,sans-serif">Candara</option>
        <option <?php selected('Century Gothic,Apple Gothic,Verdana,sans-serif',$curent_font); ?> value="Century Gothic,Apple Gothic,Verdana,sans-serif">Century Gothic</option>
        <option <?php selected('Century Schoolbook,Georgia,Times New Roman,Times,serif',$curent_font); ?> value="Century Schoolbook,Georgia,Times New Roman,Times,serif">Century Schoolbook</option>
        <option <?php selected('Consolas,Andale Mono,Monaco,Courier,Courier New,Verdana,sans-serif',$curent_font); ?> value="Consolas,Andale Mono,Monaco,Courier,Courier New,Verdana,sans-serif">Consolas</option>
        <option <?php selected('Constantia,Georgia,Times New Roman,Times,serif',$curent_font); ?> value="Constantia,Georgia,Times New Roman,Times,serif">Constantia</option>
        <option <?php selected('Corbel,Lucida Grande,Lucida Sans Unicode,Arial,sans-serif',$curent_font); ?> value="Corbel,Lucida Grande,Lucida Sans Unicode,Arial,sans-serif">Corbel</option>
        <option <?php selected('Franklin Gothic Medium,Arial,sans-serif',$curent_font); ?> value="Franklin Gothic Medium,Arial,sans-serif">Franklin Gothic Medium</option>
        <option <?php selected('Garamond,Hoefler Text,Times New Roman,Times,serif',$curent_font); ?> value="Garamond,Hoefler Text,Times New Roman,Times,serif">Garamond</option>
        <option <?php selected('Gill Sans MT,Gill Sans,Calibri,Trebuchet MS,sans-serif',$curent_font); ?> value="Gill Sans MT,Gill Sans,Calibri,Trebuchet MS,sans-serif">Gill Sans MT</option>
        <option <?php selected('Helvetica Neue,Helvetica,Arial,sans-serif',$curent_font); ?> value="Helvetica Neue,Helvetica,Arial,sans-serif">Helvetica Neue</option>
        <option <?php selected('Hoefler Text,Garamond,Times New Roman,Times,sans-serif',$curent_font); ?> value="Hoefler Text,Garamond,Times New Roman,Times,sans-serif">Hoefler Text</option>
        <option <?php selected('Lucida Bright,Cambria,Georgia,Times New Roman,Times,serif',$curent_font); ?> value="Lucida Bright,Cambria,Georgia,Times New Roman,Times,serif">Lucida Bright</option>
        <option <?php selected('Lucida Grande,Lucida Sans,Lucida Sans Unicode,sans-serif',$curent_font); ?> value="Lucida Grande,Lucida Sans,Lucida Sans Unicode,sans-serif">Lucida Grande</option>
        <option <?php selected('monospace',$curent_font); ?> value="monospace">monospace</option>
        <option <?php selected('Palatino Linotype,Palatino,Georgia,Times New Roman,Times,serif',$curent_font); ?> value="Palatino Linotype,Palatino,Georgia,Times New Roman,Times,serif">Palatino Linotype</option>
        <option <?php selected('Tahoma,Geneva,Verdana,sans-serif',$curent_font); ?> value="Tahoma,Geneva,Verdana,sans-serif">Tahoma</option>
        <option <?php selected('Rockwell, Arial Black, Arial Bold, Arial, sans-serif',$curent_font); ?> value="Rockwell, Arial Black, Arial Bold, Arial, sans-serif">Rockwell</option>
    </select>
    <?php
	}
	
}