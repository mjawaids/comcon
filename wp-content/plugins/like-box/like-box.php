<?php
/*
Plugin Name: Facebook Like Box
Plugin URI: https://wordpress.org/plugins/like-box
Description: Facebook like box plugin will help you to display Facebook like box on your wesite, just add Facebook Like box widget to your sidebar and use it. Also you can use Facebook Like box on your pages/posts and create Facebook Like box popup for your website.
Version: 0.7.53
Author: smplug-in
Author URI: http://wpdevart.com/wordpress-facebook-like-box-plugin
License: GPL/GPL3
*/
 

class like_box_main{
	// required variables
	
	private $like_box_plugin_url;
	
	private $like_box_plugin_path;
	
	private $like_box_version;
	
	public $like_box_options;
	
	
	function __construct(){
		
		$this->like_box_plugin_url  = trailingslashit( plugins_url('', __FILE__ ) );
		$this->like_box_plugin_path = trailingslashit( plugin_dir_path( __FILE__ ) );
		if(!class_exists('like_box_setting'))
		require_once($this->like_box_plugin_path.'includes/library.php');
		$this->like_box_version     = 10.0;
		$this->call_base_filters();
		$this->install_databese();
		$this->create_admin_menu();	
		$this->like_box_front_end();
		
	}
	
	public function create_admin_menu(){
		
		require_once($this->like_box_plugin_path.'includes/admin_menu.php');
		
		$like_box_admin_menu = new like_box_admin_menu(array('menu_name' => 'Like Box','databese_parametrs'=>$this->like_box_options));
		
		add_action('admin_menu', array($like_box_admin_menu,'create_menu'));
		
	}
	
/*############ Install database Function ##################*/	
	
	public function install_databese(){
		
		require_once($this->like_box_plugin_path.'includes/install_database.php');
		
		$like_box_install_database = new like_box_install_database();
		
		$this->like_box_options = $like_box_install_database->installed_options;
		
	}
	
	public function like_box_front_end(){
		
		require_once($this->like_box_plugin_path.'includes/front_end.php');
		require_once($this->like_box_plugin_path.'includes/widget.php');
		$like_box_front_end = new like_box_front_end(array('menu_name' => 'Like Box','databese_parametrs'=>$this->like_box_options));
		
	}
	
	public function registr_requeried_scripts(){
		wp_register_script('like-box-admin-script',$this->like_box_plugin_url.'includes/javascript/admin-like-box.js');
		wp_register_script('like-box-front-end',$this->like_box_plugin_url.'includes/javascript/front_end_js.js');
		wp_register_style('front_end_like_box',$this->like_box_plugin_url.'includes/style/style.css');
		wp_register_style('animated',$this->like_box_plugin_url.'includes/style/effects.css');
		wp_register_style('like-box-admin-style',$this->like_box_plugin_url.'includes/style/admin-style.css');
		
	}
	
	public function call_base_filters(){
		add_action( 'init',  array($this,'registr_requeried_scripts') );
		add_action( 'admin_head',  array($this,'include_requeried_scripts') );
		//for_upgrade
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array($this,'plugin_activate_sublink') );
	}
	public function plugin_activate_sublink($links){
		$plugin_submenu_added_link=array();		
		 $added_link = array(
		 '<a target="_blank" style="color: rgba(10, 154, 62, 1); font-weight: bold; font-size: 13px;" href="http://wpdevart.com/wordpress-facebook-like-box-plugin">Upgrade to Pro</a>',
		 );
		$plugin_submenu_added_link=array_merge( $plugin_submenu_added_link, $added_link );
		$plugin_submenu_added_link=array_merge( $plugin_submenu_added_link, $links );
		return $plugin_submenu_added_link;
	}
  	public function include_requeried_scripts(){
		wp_enqueue_script('wp-color-picker');
		wp_enqueue_style( 'wp-color-picker' );
	}

}
$like_box_main = new like_box_main();

?>