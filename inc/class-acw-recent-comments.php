<?php

/**
 * ACW_Recent_Comments Class
 *
 * Adds a Recent Comments widget with extended functionality
 *
 * @package ACW_Recent_Comments
 *
 * @since 1.0
 *
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}


class ACW_Recent_Comments
{

	/**
	 * Full file path to plugin file
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	protected $file = '';


	/**
	 * URL to plugin
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	protected $url = '';


	/**
	 * Filesystem directory path to plugin
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	protected $path = '';


	/**
	 * Base name for plugin
	 *
	 * e.g. "advanced-term-fields/advanced-term-fields.php"
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	protected $basename = '';
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param string $file Full file path to calling plugin file
	 */
	public function __construct( $file ){
		$this->file	    = $file;
		$this->url	    = plugin_dir_url( $this->file );
		$this->path	    = plugin_dir_path( $this->file );
		$this->basename = plugin_basename( $this->file );			
	}
	
	
	/**
	 * Loads the class
	 *
	 * @uses ACW_Recent_Comments::load_widget()
	 * @uses ACW_Recent_Comments::load_admin_scripts()
	 *
	 * @access public
	 *
	 * @since 0.1.0
	 */
	public function init()
	{
		$this->load_widget();
		$this->load_admin_scripts();
	}
	
	/**
	 * Loads the Comment Widget
	 *
	 * @uses ACW_Recent_Comments::register_widget()
	 *
	 * @access public
	 *
	 * @since 0.1.0
	 */
	public function load_widget()
	{
		add_action( 'widgets_init', array( $this, 'register_widget' ) );
	}
	
	/**
	 * Registers the Comment Widget
	 *
	 * @uses WordPress\register_widget()
	 *
	 * @access public
	 *
	 * @since 0.1.0
	 */
	public function register_widget()
	{
		register_widget( 'Widget_ACW_Recent_Comments' );
	}
	
	
	/**
	 * Loads js/css admin scripts
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function load_admin_scripts()
	{
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'admin_head', array( $this, 'admin_styles' ) );
		add_action( 'customize_controls_print_styles', array( $this, 'admin_styles' ) );
	}
	
	
	/**
	 * Loads js admin scripts
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function admin_scripts( $hook )
	{
		global $pagenow;
		
		$enqueue = false;
		
		if( 'customize.php' == $pagenow || 'widgets.php' == $pagenow || 'widgets.php' == $hook ) {
			$enqueue = true;
		}
		
		if ( ! $enqueue ) {
			return;
		}
				
		wp_enqueue_script( 'acw-scripts', $this->url . 'js/admin.js', array( 'jquery' ), '', true );
	}


	/**
	 * Prints out css styles in admin head
	 *
	 * Note: Only loads on edit-tags.php
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function admin_styles()
	{
		?>
		<style type="text/css">
			.acw-avatar { display: block; border: 1px solid #ddd; margin: 5px 0; text-align: center; }
			.acw-avatar .dashicons {  font-size: inherit; height: 100%; width: 100%; }
		</style>
		<?php
	}

}