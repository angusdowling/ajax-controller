<?php
/**
 * Plugin Name: Ajax Controller
 * Description: Easily reload custom post types with AJAX.
 * Version: 0.1.2
 * Author: Yoke
 *
 *
 * @package AjaxController
 * @category Core
 * @author Yoke
 */

if ( ! defined( 'ABSPATH' ) )
{
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'AjaxController' ) ) :

final class AjaxController {
	/**
	 * @var AjaxController The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * @var string action
	 */
	const ACTION = 'ajax_controller';

	/**
	 * @var string nonce
	 */
	const NONCE = 'ajax_controller';

	/**
	 * @var WP_Post $post
	 */
	public $post = null;

	/**
	 * @var array $response
	 */
	public $response = null;

	/**
	 * @var array $options
	 */
	public $options = null;

	/**
	 * @var array $ignored
	 */
	public $ignored = null;

	/**
	 * @var WP_Query $page_query
	 */
	public $page_query = null;

	/**
	 * @var AC_Query $query
	 */
	public $query = null;

	/**
	 * @var AC_Fragment $fragment
	 */
	public $fragment = null;

	/**
	 * @var array $settings
	 */
	public $settings = null;

	/**
	 * Main AC Instance
	 *
	 * Ensures only one instance of AC is loaded or can be loaded.
	 *
	 * @static
	 * @see AC()
	 * @return AjaxController - Main instance
	 */
	public static function instance()
	{

		if ( is_null( self::$_instance ) )
		{
			self::$_instance = new self();
		}

		return self::$_instance;

	}

	/**
	 * AC constructor.
	 */
	private function __construct()
	{

		$this->hooks();
		$this->includes();
		$this->constants();

	}


	/**
	 * Register AC with all the appropriate WordPress hooks.
	 */
	private function hooks()
	{

		add_action( 'wp_ajax_' . self::ACTION, array( $this, 'handle' ) );
		add_action( 'wp_ajax_nopriv_' . self::ACTION, array( $this, 'handle' ) );
		add_action( 'wp', array( $this, 'assets') );

		add_shortcode( 'ajaxcontroller', array( $this, 'set_template_hook') );
		add_action( 'init', array( $this, 'include_template_functions' ) );

	}


	/**
	 * Include required core files.
	 */
	private function includes()
	{

		include_once( 'includes/ac-helper.php' );
		$this->query     = include_once( 'includes/ac-query.php' );
		$this->fragment  = include_once( 'includes/ac-fragment.php' );

	}


	/**
	 * Define AC Constants
	 */
	private function constants()
	{

		if ( ! defined( 'AC_TEMPLATE_PATH' ) )
		{
			define( 'AC_TEMPLATE_PATH', AC_Helper::template_path() );
		}

	}


	/**
	 * Register our frontend assets.
	 */
	public function assets()
	{

		wp_register_script( 'wp_underscore', AC_Helper::plugin_url() . '/js/underscore.min.js' , array( 'jquery' ) );
		wp_register_script( 'wp_ajax_controller', AC_Helper::plugin_url() . '/js/ajax-controller.js' , array( 'jquery', 'wp_underscore' ) );

	}


	/**
	 * Localize script.
	 */
	public function localize_script()
	{

		wp_localize_script( 'wp_ajax_controller', 'wp_ajax_data',
			array(
				'data'    => $this->get_ajax_data(),
				'ajaxurl' => admin_url( 'admin-ajax.php' )
			)
		);

		wp_enqueue_script( 'wp_ajax_controller' );
		wp_enqueue_script( 'wp_underscore' );

	}


	/**
	 * Handle data request.
	 */
	public function handle()
	{

		if( AC_Helper::doing_ajax() )
		{
			check_ajax_referer( self::NONCE );
		}
		
		$this->set_options();
		$this->set_ignored();
		$this->set_post();
		$this->set_page_query();
		$this->query->parse_request( null );
		$this->set_response();
		return $this->send_response( null );

	}


	/**
	 * Get the AJAX data that WordPress needs to output.
	 *
	 * @return array
	 */
	private function get_ajax_data()
	{

		return array(
			'action'     => self::ACTION,
			'nonce'      => wp_create_nonce( AjaxController::NONCE ),
			'post'       => $this->post,
			'options'    => $this->options,
			'ignored'    => $this->ignored
		);

	}


	/**
	 * Set template hook.
	 */
	public function set_template_hook($atts)
	{

		$this->options = shortcode_atts( array(
			'pagination'    => true,
			'template_path' => null,
			'is_search'     => 'false'
		), $atts );

		$this->ignored = apply_filters( 'ac_filter_ignore', array() );

		$this->set_post();
		$this->set_page_query();
		$this->localize_script();
		$this->query->parse_request( $this->set_default_query() );
		$this->set_response();
		return $this->get_form();

	}


	public function get_form()
	{

		ob_start();
		/* ac/templates/form_start hook
		*   @hooked ac_templates_form_start     10
		*   @hooked ac_templates_form           10
		*   @hooked ac_templates_form_end       10
		*/
		do_action( "ac/templates/form" );
		return ob_get_clean();

	}


	/**
	 * Function used to Init AjaxController Template Functions - This makes them pluggable by plugins and themes.
	 */
	public function include_template_functions()
	{

		include_once( 'includes/ac-template.php' );

	}


	/**
	 * Set post.
	 */
	public function set_post()
	{

		global $post;

		if( !empty( $post ) )
		{
			$this->post = $post;
		}

		else if( !empty($_POST['post'] ) )
		{
			$this->post = get_post( $_POST['post']['ID'] );
		}

		$post = $this->post;

	}


	/**
	 * Set options.
	 */
	public function set_options($atts = null)
	{

		if( !empty($_POST['options'] ) )
		{
			$this->options = $_POST['options'];
		}

	}


	/**
	 * Set options.
	 */
	public function set_ignored($atts = null)
	{

		if( !empty( $_POST['ignored'] ) )
		{
			$this->ignored = $_POST['ignored'];
		}

	}


	/**
	 * Set page query.
	 */
	public function set_page_query($atts = null)
	{

		global $wp_query;

		if(!empty( $wp_query->query ) && array_key_exists( 's', $wp_query->query ) )
		{
			$args['s'] = $wp_query->query['s'];
		}

		else if( array_key_exists( 's', $_POST ) )
		{
			$args['s'] = $_POST['s'];
		}

		if( !empty( $this->post ) && $this->options['is_search'] == 'false' )
		{
			$args['page_id'] = $this->post->ID;
		}

		$this->page_query       = new WP_Query( $args );
		$this->page_query->post = $this->post;

	}


	/**
	 * Set default query.
	 */
	public function set_default_query()
	{

		$args = array(
			'paged'          => '1',
			'post_type'      => 'post',
			'posts_per_page' => get_option( 'posts_per_page' ),
			'post_status'    => 'publish'
		);

		if(array_key_exists( 's', $this->page_query->query ) )
		{
			$args['s'] = $this->page_query->query['s'];
		}

		return $args;

	}


	/**
	 * Set response.
	 */
    public function set_response()
    {

		$response = array();

		try
		{
			$this->response['post']       = apply_filters( 'ac_response_post',$this->post );
			$this->response['options']    = apply_filters( 'ac_response_options',$this->options );
			$this->response['ignored']    = apply_filters( 'ac_response_ignored',$this->ignored );
			$this->response['page_query'] = apply_filters( 'ac_response_page_query',$this->page_query );
			$this->response['query']      = apply_filters( 'ac_response_query', $this->query->get_query() );
			$this->response['fragments']  = apply_filters( 'ac_response_fragments', $this->fragment->get_fragments() );
		}

		catch(Exception $e)
		{
			$this->response['error'] = $e;
		}

		$this->response = apply_filters( 'ac_response', $this->response );

	}


	/**
	 * Send response.
	 */
	private function send_response()
	{

		if( AC_Helper::doing_ajax() )
		{
			if( array_key_exists( 'error', $this->response ) )
			{
				return wp_send_json_error( $this->response );
			}

			return wp_send_json_success( $this->response );
		}

		return $this->response;

	}

}

endif;

/**
 * Returns the main instance of AC to prevent the need to use globals.
 *
 * @return AjaxController
 */
function AC()
{
	return AjaxController::instance();
}

// Global for backwards compatibility.
$GLOBALS['ajaxcontroller'] = AC();