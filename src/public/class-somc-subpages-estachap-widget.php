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

//require_once '/wp-includes/widgets.php';
require_once 'class-somc-subpages-estachap.php';

class SomcSubpagesEstachapWidget extends \WP_Widget {
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
	 * @var      SomcSubpagesEstachap
	 */
	protected $plugin;
	
	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;
	
	/**
	 * A default img html tag for the current post
	 *
	 * @var string
	 */
	private $default_post_img;
	
	/**
	 * Register widget with WordPress
	 * And initialize the plugin
	 *
	 * @since     1.0.0
	 */
	function __construct() {
	
		$this->plugin = SomcSubpagesEstachap::get_instance();
		
		if(!$this->plugin){
			echo 'error in constructor of' . __FILE__ . ' No plugin class SomcSubpagesEstachap instance found.' ;
			die;
		}
			
		parent::__construct(
				'subpages_estachap_widget', // Base ID
				__('Subpages', 'text_domain'), // Name
				array( 'description' => __( 'Displays all subpages of the page where the widget is located. Add only one widget per page. NOTE: if a shortcode for Somc-subpages-estachap is used on a page the widget will not display.', 'text_domain' ), ) // Args
		);
		
		add_action('widgets_init', array($this, 'register_subpages_widget'));
		//add_action( 'wp_dashboard_setup', array($this, 'dashboard_add_subpages_widgets' ));
	
	}
	
	/**
	 * Return an instance of this class. Used to allow initialization after the plugin was loaded
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
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// outputs the content of the widget
		$title = apply_filters( 'widget_title', $instance['title'] );
		
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
			
		//add params to process the current post.
		$p = array('id' => get_the_ID(), 'size' => 'thumbnail');
		$postargs = array(
				'post_status' => 'publish',
				'post_type' => 'page',
				'post_parent' => $p['id'],
				'orderby' => 'menu_order',
				'order' => 'ASC',
				'nopaging' => true,
		);
		$params = array($p, $postargs);		
		
		$output = $this->plugin->shortcode($params);
		echo __( $output, 'text_domain' );
		echo $args['after_widget'];
	}
	
	/**
	 * Ouputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// outputs the options form on admin
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Subpages', 'text_domain' );
		}
		?>
				<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
				</p>
				<?php 
	}
	
	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}
	
	/**
	 * Register SomcSubpagesEstachapWidget widget
	 * 
	 */
	public function register_subpages_widget(){
		register_widget('SomcSubpagesEstachapWidget');
		add_action( 'wp_dashboard_setup', array($this, 'dashboard_add_subpages_widgets' ));
		
	}
	
	public function dashboard_add_subpages_widgets(){

		$widget_id = $this->id;
		$widget_name = 'Pages list';
		
		wp_add_dashboard_widget($widget_id, $widget_name, array($this,'subpages_dashboard_widget_function'));
		
	}
	
	/**
	 * Create the function to output the contents of our Dashboard Widget.
	 */
	function subpages_dashboard_widget_function($instance) {	

		// Display all pages
		//first select the top level pages
		$top_pages = array();
		$args = array(
			'sort_order' => 'ASC',
			'sort_column' => 'post_title',
			'hierarchical' => 1,
			'exclude' => '',
			'include' => '',
			'meta_key' => '',
			'meta_value' => '',
			'authors' => '',
			'child_of' => 0,
			'parent' => 0,
			'exclude_tree' => '',
			'number' => '',
			'offset' => 0,
			'post_type' => 'page',
			'post_status' => 'publish'
		); 
		$top_pages = get_pages($args); 
		
		//display the subpages/children of every top level page
		$output = "<div class='estachap_nav_div'><ul class='estachap'>";
		
		foreach($top_pages as $post){
			//output a link to the current top page/post
			setup_postdata($post);
			$url = get_permalink($post->ID);
			$img = get_the_post_thumbnail($post->ID, 'thumbnail');
			$img = preg_replace('/(width)="\d*"\s/', 'width="50px"', $img);
			$img = preg_replace('/(height)="\d*"\s/', 'height="50px"', $img);
			if(empty($img))
				$img = $this->plugin->get_default_icon();
			//extract the first 20 chars from title text according the requirements
			$title = substr($post->post_title, 0, 20);
			$item = sprintf("<li li_value='%s'> %s <a href='%s'>%s</a>",$title, $img, $url, $title);
			$output .= $item . '</br>';
				
			//add params to process the current post.
			$p = array('id' => $post->ID, 'size' => 'thumbnail');
			
			$postargs = array(
					'post_status' => 'publish',
					'post_type' => 'page',
					'post_parent' => $p['id'],
					'orderby' => 'menu_order',
					'order' => 'ASC',
					'nopaging' => true,
			);
			$params = array("post_array" => $p, "args" => $postargs);		
			
			$output .= $this->plugin->shortcode($params);
		}
		
		echo __( $output . '</ul></div>' , 'text_domain' );
		
	}
	
	/**
	 * Gets all widget options, or only options for a specified widget if a widget id is provided.
	 *
	 * @param string $widget_id Optional. If provided, will only get options for that widget.
	 * @return array An associative array
	 */
	public static function get_dashboard_widget_options( $widget_id='' )
	{
		//Fetch ALL dashboard widget options from the db...
		$opts = get_option( 'dashboard_widget_options' );
	
		//If no widget is specified, return everything
		if ( empty( $widget_id ) )
			return $opts;
	
		//If we request a widget and it exists, return it
		if ( isset( $opts[$widget_id] ) )
			return $opts[$widget_id];
	
		//Something went wrong...
		return false;
	}
			
}

?>