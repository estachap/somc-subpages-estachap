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
	}
			
}

?>