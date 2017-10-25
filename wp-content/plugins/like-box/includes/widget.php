<?php 
/*############################### Like Box WIDGET PART ###############################################*/
class like_box_facbook extends WP_Widget {
	private static $id_of_like_box=0;
	// Constructor //	
	function __construct() {		
		parent::__construct(
			'like_box_facbook', // Base ID
			'Like box Facebook', // Name
			array( 'description' => 'Like box Facebook ', ) 
		);	

	}

	/*############################### Widget function ########################################*/
	function widget($args, $instance) {
		self::$id_of_like_box++;
		extract( $args );
		$title = $instance['title'];    
		$params_of_widget=array(
			'profile_id' 	=>  	$instance['profile_id'],
			'width' 		=>  	(int)$instance['width'], // Type here default Maximum Width
			'height' 		=>  	(int)$instance['height'],// Type here default Height
			'show_border' 	=>  	'show',
			'border_color' 	=>  	'#FFF',
			'header' 		=>  	$instance['header'], // Like Box Header type
			'show_cover_photo'=> 	$instance['cover_photo'],  //Like Box Header cover photo
			'connections' 	=> 		$instance['connections'],// Show Facebook users faces
			'stream' 		=> 		'hide',			
			'animation_efect'=>		'none',			
			'locale'		=>   	$instance['locale'], // Language	
		
		);
		// Before widget part //
		echo $before_widget;
		
		// Title of widget //
		if ( $title ) { echo $before_title . $title . $after_title; }
		// Widget output //
		echo like_box_setting::generete_iframe_by_array($params_of_widget); 
		// After widget part //
		
		echo $after_widget;
	}

	// Update Settings //
		function update($new_instance, $old_instance) {	
		extract( $args );
		$instance['title'] = strip_tags($new_instance['title']);    
		$instance['profile_id'] = $new_instance['profile_id'];
		
		$instance['connections'] = $new_instance['connections'];
		$instance['width'] = $new_instance['width'];
		$instance['height'] = $new_instance['height'];
		$instance['header'] = $new_instance['header'];
		$instance['cover_photo'] = $new_instance['cover_photo'];
		$instance['locale'] = $new_instance['locale'];
		return $instance;  /// Return parameters new values
		
	}

	/* Admin page options code */
	function form($instance) {
		
		$defaults = array( 'title' => '','profile_id' => '','stream' => 'true', 'connections' => 'show','width' => '300','height' => '550','header' => 'small','cover_photo' => 'show','locale' => 'en_US');
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>
        

        <p class="flb_field">
          <label for="title">Title</label>
          <br>
          <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" class="widefat">
        </p>
        <p class="flb_field">
          <label for="<?php echo $this->get_field_id('profile_id'); ?>">Page ID</label>
          <br>
          <input id="<?php echo $this->get_field_id('profile_id'); ?>" name="<?php echo $this->get_field_name('profile_id'); ?>" type="text" value="<?php echo $instance['profile_id']; ?>" class="widefat">
        </p>
        
      <p class="flb_field">
          <label for="<?php echo $this->get_field_id('width'); ?>">Like box Width</label>
          <br>
          <input id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $instance['width']; ?>" class="" size="3">
          <small>(px)</small>
        </p>
        
        <p class="flb_field">
          <label for="<?php echo $this->get_field_id('height'); ?>">Like box Height</label>
          <br>
          <input id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $instance['height']; ?>" class="" size="3">
          <small>(px)</small>
        </p>
        
        <label for="this_is_a_bad">Like box Animation <span style="color:rgba(10, 154, 62, 1);;font-weight:bold;">Pro feature!</span></label>
        <br>
       
        <?php  like_box_setting::generete_animation_select('this_is_a_bad','none') ?>
        <br>
        <br>
          <label for="<?php echo $this->get_field_id('show_border'); ?>">Like box border<span style="color:rgba(10, 154, 62, 1);;font-weight:bold;">Pro feature!</span></label>
        <br>
       <select id="show_like_box_border" name="show_like_box_border_name" onMouseDown="alert('If you want use this feature upgrade to Like box Pro')">
            <option selected="selected" value="show">Show</option>
            <option  value="hide">Hide</option>
        </select>
        <br>
        <br>
        <label for="border_color">Like box Border Color<span style="color:rgba(10, 154, 62, 1);;font-weight:bold;">Pro feature!</span></label>
        <br>
            <div class="disabled_for_pro" onclick="alert('If you want use this feature upgrade to Like box Pro')">
              <div class="wp-picker-container"><a tabindex="0" class="wp-color-result" title="Select Color" data-current="Current Color" style="background-color: rgb(255, 255, 255);"></a></div>
            </div>        
        <p class="flb_field">
          <label for="<?php echo $this->get_field_id('stream'); ?>">Facebook latest posts<span style="color:rgba(10, 154, 62, 1);;font-weight:bold;">Pro feature!</span></label>
          <br>
          <select  onMouseDown="alert('If you want use this feature upgrade to Like box Pro')" id="like_box_latest_post" name="like_box_latest_post_name">
            <option  value="show">Show</option>
            <option selected="selected" value="hide">Hide</option>
        </select>
        </p>
        
        <p class="flb_field">
          <label for="<?php echo $this->get_field_id('connections'); ?>">Show Users Faces</label>
          <br>
          <select id="<?php echo $this->get_field_id('connections'); ?>" name="<?php echo $this->get_field_name('connections'); ?>">
            <option <?php selected($instance['connections'],'show') ?> value="show">Show</option>
            <option <?php selected($instance['connections'],'hide') ?> value="hide">Hide</option>
          </select>
        </p>
          
        
        <p class="flb_field">
          <label for="<?php echo $this->get_field_id('header'); ?>">Like box Header</label>
         <br>
           <select id="<?php echo $this->get_field_id('header'); ?>" name="<?php echo $this->get_field_name('header'); ?>">
            <option <?php selected($instance['header'],'small') ?> value="small">Small</option>
            <option <?php selected($instance['header'],'big') ?> value="big">Big</option>
          </select>
        </p>
         <p class="flb_field">
          <label for="<?php echo $this->get_field_id('header'); ?>">Like box cover photo</label>
          <br>
           <select id="<?php echo $this->get_field_id('cover_photo'); ?>" name="<?php echo $this->get_field_name('cover_photo'); ?>">
            <option <?php selected($instance['cover_photo'],'show') ?> value="show">Show</option>
            <option <?php selected($instance['cover_photo'],'hide') ?> value="hide">Hide</option>
          </select>
        </p>
        
        <p class="flb_field">
          <label for="<?php echo $this->get_field_id('locale'); ?>">Language</label>
          <br>
          <input id="<?php echo $this->get_field_id('locale'); ?>" name="<?php echo $this->get_field_name('locale'); ?>" type="text" value="<?php echo $instance['locale']; ?>" class="" size="4">
          <small>(en_US, de_DE...)</small>
        </p>
        <a href="http://wpdevart.com/wordpress-facebook-like-box-plugin/" target="_blank" style="color: rgba(10, 154, 62, 1);; font-weight: bold; font-size: 18px; text-decoration: none;">Upgrade to Pro Version</a><br>
        <br>
        <br>
        <input type="hidden" id="flb-submit" name="flb-submit" value="1">
        <script>
			var pro_text='If you want to use this feature upgrade to Like box Pro';
            jQuery(".color_my_likbox").ready(function(e) {
				
				jQuery(".color_my_likbox").each(function(index, element) {
                    if(!jQuery(this).hasClass('wp-color-picker') && jQuery(this).attr('name').indexOf('__i__')==-1){ jQuery(this).wpColorPicker()};
                });
               
            });
        </script> 
		<?php 
	}
}
add_action('widgets_init', create_function('', 'return register_widget("like_box_facbook");'));