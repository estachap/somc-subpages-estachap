<?php
/**
 * somc-subpages-estachap
 *
 * @package   Somc-subpages-estachap
 * @author    Estanislao Chapel <estanislao.chapel@stonebeach.se>
 * @license   GPL-2.0+
 * @link      http://plugins.stonebeach.se
 * @copyright 2014 Estanislao Chapel @ Stonebeach AB
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 *
 *
 * @package Somc-subpages-estachap
 * @author  Estanislao Chapel
 */
class SomcSubpagesEstachap {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * 
	 * Unique identifier for a plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'Somc-subpages-estachap';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;
	
	/**
	 * Used to track the deep of the subpages of the current page
	 * @var int
	 */
	protected static $level = 0;
	
	/**
	 * A default img html tag for the current post
	 * 
	 * @var string
	 */
	private $default_post_img;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		/* Define custom functionality.
		 * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( 'estachap_action', array( $this, 'action_estachap' ) );
		add_filter( 'estachap_filter', array( $this, 'filter_estachap' ) );
		
		add_shortcode('somc_subpages', array( $this, 'shortcode') );
		
		$default_icon = plugins_url('../assets/logo-default-subpages.gif', __FILE__ );
		$this->default_post_img = "<img src='{$default_icon}' alt='default image' />";

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		// @TODO: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
		wp_enqueue_style('jquery-ui-lightness-styles', get_template_directory_uri() . '/js/jquery-ui-1.10.4.custom/css/ui-lightness/jquery-ui-1.10.4.custom.css', array());
		//wp_enqueue_style('jquery-ui-smoothness-styles', '//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css', array());
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
		wp_enqueue_script('jquery-ui', get_template_directory_uri() . '/js/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.js', array(), '1.10.4');
		//wp_enqueue_script('jquery-ui', '//code.jquery.com/ui/1.10.4/jquery-ui.js', array(), '1.10.4');
	}

	/**
	 * 
	 * @since    1.0.0
	 */
	public function action_estachap() {
		// @TODO: Define your action hook callback here
		
	}

	/**
	 *
	 * @since    1.0.0
	 */
	public function filter_estachap() {
		// @TODO: Define your filter hook callback here
		
	}
	
	/**
	 * [somc_subpages]
	 * @param array $params -- an array or format "post_array" => post_array, "args" => args_array
	 * post_array - array('id' => post_id, 'size' => 'thumbnail')  
	 * args - array(
				'post_status' => 'publish',
				'post_type' => 'page',
				'post_parent' => post_array['id'],
				'orderby' => 'menu_order',
				'order' => 'ASC',
				'nopaging' => true )
	 */
	public function shortcode($params){
		global $post;
		
		$p = array();
		$args = array();
		
		//if the array $params is empty or not set then assign the data its default values
		if(!isset($params) || empty($params)){
			$p = array('id' => get_the_ID(), 'size' => 'thumbnail');
			$args = array(
					'post_status' => 'publish',
					'post_type' => 'page',
					'post_parent' => $p['id'],
					'orderby' => 'menu_order',
					'order' => 'ASC',
					'nopaging' => true,
			);
		}else{
			//check if the post_array is set and assign the proper values to it
			if(!isset($params['post_array']) || empty($params['post_array'])){
				$p = array('id' => get_the_ID(), 'size' => 'thumbnail');
			}else{
				$p = $params['post_array'];
			}
			
			//check if the args array is set and assign the proper values to it
			if(!isset($params['args']) || empty($params['args'])){
				$args = array(
						'post_status' => 'publish',
						'post_type' => 'page',
						'post_parent' => $p['id'],
						'orderby' => 'menu_order',
						'order' => 'ASC',
						'nopaging' => true,
				);
			}else{
				$args = $params['args'];
			}
			
		}
		
		
		
		//apply any registered filter to the args data
		$args = apply_filters('somc-subpages-estachap-shortcode-query', $args, $p);
		
		return $this->display($args, $p);
	}
	
	/**
	 * 
	 * @param array mixed $args
	 * @return string - output html fragment
	 */
	private function display($args, $post_array){
		global $post;
		
		$outputhtml = '';
		$pages = get_posts($args);
		//if the current post has child pages then process the list recursively
		if(!empty($pages)){
			$outputhtml = "<div class='estachap-label'>";
			$outputhtml .= "<span class='estachap-event toggle ddButtonLeft' name='toggle_{$post_array['id']}' id='toggle_{$post_array['id']}' post_id='{$post_array['id']}'></span>";
			$outputhtml .= "<span id='sort_subpages_asc_{$post_array['id']}' class='estachap-event sortable_list' post_id='{$post_array['id']}' direction='asc'>Sort asc </span>";
			$outputhtml .= "<span id='sort_subpages_desc_{$post_array['id']}' class='estachap-event sortable_list' post_id='{$post_array['id']}' direction='desc'> Sort desc</span>";		
			$outputhtml .= "</div>";

			$outputhtml .= "<ul class='estachap estachap-container' id='subpages_of_{$post_array['id']}'>";
			//track the deep of the recursion. In a future we want to check this in order to go too deep and slow down the page
			self::$level++;
			foreach ($pages as $post){
				setup_postdata($post);
				$post = apply_filters('somc-subpages-estachap-shortcode-post', $post);
				$url = get_permalink($post->ID);
				$img = get_the_post_thumbnail($post->ID, $post_array['size']);
				$img = preg_replace('/(width)="\d*"\s/', 'width="50px"', $img);
				$img = preg_replace('/(height)="\d*"\s/', 'height="50px"', $img);
				if(empty($img))
					$img = $this->default_post_img;
				//extract the first 20 chars from title text according the requirements
				$title = substr($post->post_title, 0, 20);
				$item = sprintf("<li li_value='%s'> %s <a href='%s'>%s</a>",$title, $img, $url, $title);
				$outputhtml .= $item . '</br>';
		
				//run recursive to check for subpages of the current post
				$post_array = array('id' => $post->ID, 'size' => 'thumbnail');
				$args = array(
						'post_status' => 'publish',
						'post_type' => 'page',
						'post_parent' => $post->ID,
						'orderby' => 'menu_order',
						'order' => 'ASC',
						'nopaging' => true );
		
				$params = array("post_array" => $post_array, "args" => $args);
				//check for subpages of the current post and output them as a list
				$subpages = $this->shortcode($params);

				$outputhtml .= (!empty($subpages))? $subpages . '</li>': '</li>';
					
			}
				
			$outputhtml .= '</ul>';
			self::$level--;
				
		}
		
		return apply_filters("somc-subpages-estachap-shortcode-output", $outputhtml);
		
	}

}
