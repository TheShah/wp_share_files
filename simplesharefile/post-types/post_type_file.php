<?php 
if(!class_exists('Post_Type_File'))
{
	/**
	 * A PostType class that provides 3 additional meta fields
	 */
	class Post_Type_File
	{
		const POST_TYPE	= "file";
		private $nonce;
		private $action;
		private $_meta	= array(
			'meta-radio',
			'meta-cb-fb',
			'meta-cb-tw',
			'meta_image',
		);
		
    	/**
    	 * The Constructor
    	 */
    	public function __construct()
    	{
    		// register actions
    		add_action('init', array($this, 'init'));
    		add_action('admin_init', array($this, 'admin_init'));
			
    	} // END public function __construct()
    	/**
    	 * hook into WP's init action hook
    	 */
    	public function init()
    	{
    		// Initialize Post Type
    		$this->create_post_type();
    		add_action('save_post', array($this, 'save_post'));
    	} // END public function init()
    	/**
    	 * Create the post type
    	 */
    	public function create_post_type()
    	{
			$labels = array(
					'name'                => 'files',
					'singular_name'       => 'file',
					'menu_name'           => 'Files',
					'name_admin_bar'      => 'Files',
					'parent_item_colon'   => 'Parent Item:',
					'all_items'           => 'All Items',
					'add_new_item'        => 'Add New File',
					'add_new'             => 'Add New',
					'new_item'            => 'New File',
					'edit_item'           => 'Edit File',
					'update_item'         => 'Update File',
					'view_item'           => 'View File',
					'search_items'        => 'Search Item',
					'not_found'           => 'Not found',
					'not_found_in_trash'  => 'Not found in Trash',
				);
				
			$rewrite = array(
					'slug'                => 'files',
					'with_front'          => true,
					'pages'               => false,
					'feeds'               => false,
				);
			$args = array(
					'label'               => 'file',
					'description'         => 'A Post Type to provide the data for the sharing.',
					'labels'              => $labels,
					'supports'            => array( 'title', 'excerpt', 'custom-fields', ),
					'hierarchical'        => false,
					'public'              => true,
					'show_ui'             => true,
					'show_in_menu'        => true,
					'menu_position'       => 5,
					'menu_icon'           => 'dashicons-share',
					'show_in_admin_bar'   => true,
					'show_in_nav_menus'   => false,
					'can_export'          => true,
					'has_archive'         => false,
					'exclude_from_search' => true,
					'publicly_queryable'  => true,
					'rewrite'             => $rewrite,
					'capability_type'     => 'post',
				);
		
			/* register_post_type('name', $args) */
    		register_post_type(self::POST_TYPE, $args);
    	}
	
    	/**
    	 * Save the metaboxes for this custom post type
    	 */
    	public function save_post($post_id)
    	{
			/*
			 * We need to verify this came from our screen and with proper authorization,
			 * because the save_post action can be triggered at other times.
			 */

			// Check if our nonce is set.
			if ( ! isset( $_POST[$this->nonce] ) ) {
				return;
			}

			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $_POST[$this->nonce], $this->action ) ) {
				return;
			}

			// If this is an autosave, our form has not been submitted, so we don't want to do anything.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			// Check the user's permissions.
			if(isset($_POST['post_type']) && $_POST['post_type'] == self::POST_TYPE && current_user_can('edit_post', $post_id))
    		{
    			foreach($this->_meta as $field_name)
    			{
    				// Update the post's meta field
					if(isset($_POST[$field_name]))
					{
						update_post_meta($post_id, $field_name, $_POST[$field_name]);
					}
					else 
					{
						update_post_meta($post_id, $field_name, '');
					}
    			}
    		}
    		else
    		{
    			return;
    		} 
    	} 
    	/**
    	 * hook into WP's admin_init action hook
    	 */
    	public function admin_init()
    	{			
    		// Add metaboxes
    		add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
			
			// Add scripts
			add_action( 'admin_enqueue_scripts', array($this, 'prfx_image_enqueue'));
    	} // END public function admin_init()
			
    	/**
    	 * hook into WP's add_meta_boxes action hook
    	 */
    	public function add_meta_boxes()
    	{
    		// Add this metabox to every selected post
    		add_meta_box( 
    			sprintf('wp_plugin_template_%s_section', self::POST_TYPE),
    			sprintf('%s Information', ucwords(str_replace("_", " ", self::POST_TYPE))),
    			array($this, 'add_inner_meta_boxes'),
    			self::POST_TYPE
    	    );					
    	} // END public function add_meta_boxes()
		/**
		 * called off of the add meta box
		 */		
		public function add_inner_meta_boxes($post)
		{
			$this->nonce = self::POST_TYPE.'add_inner_meta_boxes'.'_nonce';
			$this->action = basename( __FILE__);
			
			// Add an nonce field so we can check for it later.
			wp_nonce_field( $this->action , $this->nonce );
			
			//add the array of input
			$prfx_stored_meta = get_post_meta($post->ID);
			
			// Render the job order metabox
			include(sprintf("%s/../templates/%s_metabox.php", dirname(__FILE__), self::POST_TYPE));			
		} // END public function add_inner_meta_boxes($post)
		
		/**
		 * Loads the image management javascript
		 */
		function prfx_image_enqueue() 
		{
			global $typenow;
			if( $typenow == self::POST_TYPE ) {
				wp_enqueue_media();
			
			// To Do this fucking url should be added without the hardcoded Plugin name!!
			
			// Registers and enqueues the required javascript.
			wp_register_script( 'meta-box-image', plugins_url( '/simplesharefile/js/meta-box-image.js'), array( 'jquery' ) );
			wp_localize_script( 'meta-box-image', 
								'meta_image', 
								array(
									'title' => __( 'Choose or Upload a File', 'prfx-textdomain' ),
									'button' => __( 'Use this File', 'prfx-textdomain' ),
									)
								);
				wp_enqueue_script( 'meta-box-image' );
			}
		}
	} // END class Post_Type_Template
} // END if(!class_exists('Post_Type_Template'))

