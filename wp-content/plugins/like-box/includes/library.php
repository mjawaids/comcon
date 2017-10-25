<?php

class like_box_setting{
	public static $list_of_animations=array('bounce','flash','pulse','rubberBand','shake','swing','tada','wobble','bounceIn','bounceInDown','bounceInLeft','bounceInRight','bounceInUp','fadeIn','fadeInDown','fadeInDownBig','fadeInLeft','fadeInLeftBig','fadeInRight','fadeInRightBig','fadeInUp','fadeInUpBig','flip','flipInX','flipInY','lightSpeedIn','rotateIn','rotateInDownLeft','rotateInDownRight','rotateInUpLeft','rotateInUpRight','rollIn','zoomIn','zoomInDown','zoomInLeft','zoomInRight','zoomInUp');
	public static $id_for_iframe=0;
	
	// Function for genereted animations
	public static function get_animations_type_array($animation=''){
		if($animation=='' || $animation=='none')
			return '';
		if($animation=='random'){	
		
			return self::$list_of_animations[array_rand(self::$list_of_animations,1)];
		}
		return $animation;
	}
	
	// Function that generate Facebook iframe and iframe js by array
	public static function generete_iframe_by_array($params){
		self::$id_for_iframe++;
		$output_code='';
		//Default parameters for Like Box iframe
		$defaults=array(
			'iframe_id'  =>  'facbook_like_box_'.self::$id_for_iframe,
			'profile_id' =>  '',
			'width' =>  '300', // Type here default Maximum width
			'height' =>  '550',// Type here default Height
			'show_border' =>  'show',
			'border_color' =>  '#FFFFF',
			'header' =>  'small', // Header type
			'show_cover_photo'=>'true',  //Header cover photo
			'connections' =>  'show',// Show Facebook faces
			'stream' =>  '0',			
			'animation_efect'=>'none',			
			'locale' =>  'en_US', // Language			
		);
		$params=array_merge($defaults,$params);
		$params['width']=max((int)$params['width'],180);
		$params['width']=min((int)$params['width'],500);
		
		if($params['header']=='small' || $params['header']=='0' || $params['header']=='no')
			$params['header']='true';
		else
			$params['header']='false';
			
		if((int)$params['connections']>0 || $params['connections']=="show")
			$params['connections']='true';
		else
			$params['connections']='false';
		
		
		if($params['stream']=='0' || $params['stream']=='hide')
			$params['stream']='false';
		else
			$params['stream']='true';
			
		if($params['show_cover_photo']=='true' || $params['show_cover_photo']=='show')
			$params['show_cover_photo']='false';
		else
			$params['show_cover_photo']='true';
		
		$like_box_array_query=array(
			'adapt_container_width'  => 'true',
			'container_width'  		 => $params['width'],
			'width'  				 => $params['width'],
			'height'  				 => $params['height'],
			'hide_cover'  			 => $params['show_cover_photo'],
			'href'  				 => urlencode("https://www.facebook.com/".$params['profile_id']),
			'locale'  				 => $params['locale'],
			'sdk'  					 => 'joey',
			'show_facepile'  		 => $params['connections'],
			'show_posts'  			 => $params['stream'],
			'small_header'  		 => $params['header'],
		);
		$like_box_src=add_query_arg($like_box_array_query,'http://www.facebook.com/v2.6/plugins/page.php');
		$output_code.='<iframe id="'.$params['iframe_id'].'" src="'.$like_box_src.'" scrolling="no" allowTransparency="true" style="'.(($params['show_border']=='yes' ||  $params['show_border']=='show')?'border:1px solid '.$params['border_color'].';':'border:none').' overflow:hidden;visibility:hidden; max-width:500px; width:'.$params['width'].'px; height:'.$params['height'].'px;"></iframe>';
		$output_code.='<script>
		if(typeof(jQuery)=="undefined")
			jQuery=window.parent.jQuery;
		if(typeof(like_box_animated_element)=="undefined")
			like_box_animated_element=window.parent.like_box_animated_element;
		if(typeof(like_box_set_width_cur_element)=="undefined")
			like_box_set_width_cur_element=window.parent.like_box_animated_element;		
		jQuery(document).ready(function(){';
		if($params['animation_efect']!='none'){
		$output_code.='
				like_box_animated_element("'.like_box_setting::get_animations_type_array($params['animation_efect']).'","'.$params['iframe_id'].'");
				like_box_set_width_cur_element("'.$params['iframe_id'].'",'.$params['width'].')
				jQuery(window).scroll(function(){
					like_box_animated_element("'.self::get_animations_type_array($params['animation_efect']).'","'.$params['iframe_id'].'");
				})';
		}
		else{
			$output_code.='
			document.getElementById("'.$params['iframe_id'].'").style.visibility="visible"
			like_box_set_width_cur_element("'.$params['iframe_id'].'",'.$params['width'].')
			';
		}
        $output_code.= '});</script>';
		return $output_code;
	}
	// Animations function	
	public static function generete_animation_select($select_id='',$curent_effect='none'){
	?>
    <select onClick="alert(pro_text); return false;" id="<?php echo $select_id; ?>" name="<?php echo $select_id; ?>">
   		  <option <?php selected('none',$curent_effect); ?> value="none">none</option>
          <option <?php selected('random',$curent_effect); ?> value="random">random</option>
        <optgroup label="Attention Seekers">
          <option <?php selected('bounce',$curent_effect); ?> value="bounce">bounce</option>
          <option <?php selected('flash',$curent_effect); ?> value="flash">flash</option>
          <option <?php selected('pulse',$curent_effect); ?> value="pulse">pulse</option>
          <option <?php selected('rubberBand',$curent_effect); ?> value="rubberBand">rubberBand</option>
          <option <?php selected('shake',$curent_effect); ?> value="shake">shake</option>
          <option <?php selected('swing',$curent_effect); ?> value="swing">swing</option>
          <option <?php selected('tada',$curent_effect); ?> value="tada">tada</option>
          <option <?php selected('wobble',$curent_effect); ?> value="wobble">wobble</option>
        </optgroup>

        <optgroup label="Bouncing Entrances">
          <option <?php selected('bounceIn',$curent_effect); ?> value="bounceIn">bounceIn</option>
          <option <?php selected('bounceInDown',$curent_effect); ?> value="bounceInDown">bounceInDown</option>
          <option <?php selected('bounceInLeft',$curent_effect); ?> value="bounceInLeft">bounceInLeft</option>
          <option <?php selected('bounceInRight',$curent_effect); ?> value="bounceInRight">bounceInRight</option>
          <option <?php selected('bounceInUp',$curent_effect); ?> value="bounceInUp">bounceInUp</option>
        </optgroup>

        <optgroup label="Fading Entrances">
          <option <?php selected('fadeIn',$curent_effect); ?> value="fadeIn">fadeIn</option>
          <option <?php selected('fadeInDown',$curent_effect); ?> value="fadeInDown">fadeInDown</option>
          <option <?php selected('fadeInDownBig',$curent_effect); ?> value="fadeInDownBig">fadeInDownBig</option>
          <option <?php selected('fadeInLeft',$curent_effect); ?> value="fadeInLeft">fadeInLeft</option>
          <option <?php selected('fadeInLeftBig',$curent_effect); ?> value="fadeInLeftBig">fadeInLeftBig</option>
          <option <?php selected('fadeInRight',$curent_effect); ?> value="fadeInRight">fadeInRight</option>
          <option <?php selected('fadeInRightBig',$curent_effect); ?> value="fadeInRightBig">fadeInRightBig</option>
          <option <?php selected('fadeInUp',$curent_effect); ?> value="fadeInUp">fadeInUp</option>
          <option <?php selected('fadeInUpBig',$curent_effect); ?> value="fadeInUpBig">fadeInUpBig</option>
        </optgroup>

        <optgroup label="Flippers">
          <option <?php selected('flip',$curent_effect); ?> value="flip">flip</option>
          <option <?php selected('flipInX',$curent_effect); ?> value="flipInX">flipInX</option>
          <option <?php selected('flipInY',$curent_effect); ?> value="flipInY">flipInY</option>
        </optgroup>

        <optgroup label="Lightspeed">
          <option <?php selected('lightSpeedIn',$curent_effect); ?> value="lightSpeedIn">lightSpeedIn</option>
        </optgroup>

        <optgroup label="Rotating Entrances">
          <option <?php selected('rotateIn',$curent_effect); ?> value="rotateIn">rotateIn</option>
          <option <?php selected('rotateInDownLeft',$curent_effect); ?> value="rotateInDownLeft">rotateInDownLeft</option>
          <option <?php selected('rotateInDownRight',$curent_effect); ?> value="rotateInDownRight">rotateInDownRight</option>
          <option <?php selected('rotateInUpLeft',$curent_effect); ?> value="rotateInUpLeft">rotateInUpLeft</option>
          <option <?php selected('rotateInUpRight',$curent_effect); ?> value="rotateInUpRight">rotateInUpRight</option>
        </optgroup>

        <optgroup label="Specials">
          
          <option <?php selected('rollIn',$curent_effect); ?> value="rollIn">rollIn</option>        
        </optgroup>

        <optgroup label="Zoom Entrances">
          <option <?php selected('zoomIn',$curent_effect); ?> value="zoomIn">zoomIn</option>
          <option <?php selected('zoomInDown',$curent_effect); ?> value="zoomInDown">zoomInDown</option>
          <option <?php selected('zoomInLeft',$curent_effect); ?> value="zoomInLeft">zoomInLeft</option>
          <option <?php selected('zoomInRight',$curent_effect); ?> value="zoomInRight">zoomInRight</option>
          <option <?php selected('zoomInUp',$curent_effect); ?> value="zoomInUp">zoomInUp</option>
        </optgroup>
      </select>
    <?php 
	}
	
}


 ?>