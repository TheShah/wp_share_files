<?php
/*
Plugin Name: Simple Share Files 
Plugin URI: http://shahidi-sohrab.com/simplesharefiles
Description: This Plugin will create a Custom Post Type which can be used to share Files like PDF and Word in Facebook. It uses the Open Graph functionality to achieve this.
Author: Sohrab Shahidi
Version: 0.0.1
Author URI: http://shahidi-sohrab.com/
*/

if(!class_exists('Simple_Share_Files'))
{
	class Simple_Share_Files
	{
		private $plugin_dir = null;
		
		// var to hold all data
		private $file_data;
		
		// var to hold all needed meta tags
		private $meta_tags;
		
		/* var to hold all meta-data from post_meta table*/
		private $file_post_meta;
		
		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
			if ( ! defined( 'WPSSF_FILE' ) ) {
				define( 'WPSSF_FILE', __FILE__ );
			}
			$plugin_dir = dirname(__FILE__);
			// Register custom post types
			require_once(sprintf("%s/post-types/post_type_file.php", dirname(__FILE__)));
			$file_post_type = new Post_Type_File();
			$plugin = plugin_basename(__FILE__);
			add_filter("plugin_action_links_$plugin", array( $this, 'plugin_settings_link' ));
			
			/* Filter the single_template with our custom function*/
			add_filter('single_template', array($this, 'add_single_template'));
			
			unset ($this->file_data);
			unset ($this->meta_tags);
			unset ($this->file_post_meta);
			
			// Add Meta-info to the wp_head hook
			add_action('wp_head', array( $this, 'add_meta_in_head' ));
			
		} // END public function __construct
		/**
		 * Activate the plugin
		 */
		public static function activate()
		{
			// Do nothing
		} // END public static function activate
		/**
		 * Deactivate the plugin
		 */
		public static function deactivate()
		{
			// Do nothing
		} // END public static function deactivate
		// Add the settings link to the plugins page
		function plugin_settings_link($links)
		{
			$settings_link = '<a href="options-general.php?page=simple_share_files">Settings</a>';
			array_unshift($links, $settings_link);
			return $links;
		}
		function add_meta_in_head()
		{
			global $post;
			if(!empty($post) && get_post_type() == 'file'){
			
				//set post type file meta data
				$this->file_post_meta = get_post_meta($post->ID);
				
				//set file data
				$this->set_file_data($post->ID);
				
				//generate meta
				$this->generate_meta();
				
				//output meta
				$this->output_meta();
								
			}//end of if
		}	
		function output_meta()
		{
			//fb is set
			if (isset($this->meta_tags['facebook']))
			{
				foreach($this->meta_tags['facebook'] as $key => $value)
				{
					echo '<meta property="og:'.$key .'" content="'.$value.'">';
				}
			}
			
			//tw is set
			if (isset($this->meta_tags['twitter']))
			{
				foreach($this->meta_tags['twitter'] as $key => $value)
				{
					echo '<meta name="twitter:'.$key .'" content="'.$value.'">';
				}
			}
			
			//google set
			if (isset($this->meta_tags['google']))
			{
				foreach($this->meta_tags['google'] as $key => $value)
				{
					echo '<meta itemprop="'.$key .'" content="'.$value.'">';
				}
			}
			// if (isset($this->file_data['meta_image']))
			// {
				// $file_url = $this->file_data['meta_image'][0];
				
				// echo '<meta http-equiv="refresh" content=" 5 '.'; url='. $file_url.'">';
			// }
				
		}
		function generate_meta()
		{
			$websitename = get_bloginfo('name');
			if ($this->file_data['sharein']['facebook'] == 'yes')
			{
				$meta_facebook = array(	
							'url'			=>$this->file_data['file_post_url'],
							'site_name'		=>$websitename,
							'title'			=>$this->file_data['post_type_title'],
							'type'			=>'website',
							'description'	=>$this->file_data['file_description'], 
							'image'			=>$this->file_data['file_image_url'],
							'width'			=>'128',
							'height'		=>'128',
							);
				$this->meta_tags['facebook'] = $meta_facebook;
			}
			if ($this->file_data['sharein']['twitter'] == 'yes')
			{
				$meta_twitter = array(
							'card'			=> 'summary',
							'site'			=> $websitename, 
							'url'			=> $this->file_data['file_post_url'],
							'title'			=> $this->file_data['post_type_title'],
							'description'	=> $this->file_data['file_description'], 
							'image'			=> $this->file_data['file_image_url'], 
							);
				$this->meta_tags['twitter'] = $meta_twitter;
			}
			if ($this->file_data['sharein']['google'] == 'yes')
			{
				$meta_gplus = array(
							'name'			=>$this->file_data['post_type_title'],
							'description'	=>$this->file_data['file_description'],
							'image'			=>$this->file_data['file_image_url'],
							);
				$this->meta_tags['google'] = $meta_gplus;			
			}
			
			//general
			
			//autodownload
			$auto_download = true;
			if ($auto_download)
			{
				$this->meta_tags['download_url'] = $this->file_data['download_url'];
			}
		}
		function set_file_data($post_id)
		{
			$this->file_data['file_name'] = get_the_title($post_id);
			$this->file_data['file_type'] = $this->file_post_meta['meta-radio'][0];
			$this->file_data['file_image_url'] = $this->get_image_url();
			if (isset($this->file_post_meta['meta-cb-fb']))
			{
				$this->file_data['sharein']['facebook']= 'yes'; 
			}
			else { $this->file_data['sharein']['facebook']= 'no'; }
			if (isset($this->file_post_meta['meta-cb-tw']))
			{
				$this->file_data['sharein']['twitter']= 'yes';
			} else { $this->file_data['sharein']['twitter']= 'no'; }
			if (isset($this->file_post_meta['meta-cb-gp']))
			{
				$this->file_data['sharein']['google']= 'yes';
			} else { $this->file_data['sharein']['google']= 'no'; }
			$this->file_data['download_url'] = $this->file_post_meta['meta_image'][0];
			$this->file_data['file_description'] = 'Download Filename blab bla!';
			$this->file_data['file_post_url'] = get_permalink($post_id);
			$this->file_data['post_type_title'] = get_the_title($post_id);
			
		}
		function get_image_url()
		{
			$image_url = null;
			$radio = $this->file_post_meta['meta-radio'][0];
			switch ($radio)
			{
				case "word":
					$image_url = plugins_url( '/simplesharefile/images/word.jpg');
					break;
				case "pdf":
					$image_url = plugins_url( '/simplesharefile/images/pdf.jpg');
					break;
				case "excel":
					$image_url = plugins_url( '/simplesharefile/images/excel.jpg');
			}
			return $image_url;
			
		}
		function add_single_template($single_template) 
		{
			global $post;

			/* Checks for single template by post type */
			if ($post->post_type == 'file'){
				$single_template = dirname( __FILE__ ) . '/templates/single-file.php';
			}
				return $single_template;
		}
	} // END class Simple_Share_Files
} // END if(!class_exists('Simple_Share_Files'))
if(class_exists('Simple_Share_Files'))
{
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('Simple_Share_Files', 'activate'));
	register_deactivation_hook(__FILE__, array('Simple_Share_Files', 'deactivate'));
	// instantiate the plugin class
	$simple_share_files = new Simple_Share_Files();
}
?>
