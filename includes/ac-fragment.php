<?php
/**
 * Class for serving HTML fragments
 *
 * @author 		Yoke
 * @package 	AjaxController/Includes
 * @version     0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'AC_Fragment' ) ) :

class AC_Fragment {
	/**
	 * @var array
	 */
	private $templates = array();

	/**
	 * @var array
	 */
	public $response = null;


	/**
	 * Set templates.
	 */
	public function set_template($fragment, $selector)
	{
		$this->templates[$fragment] = $selector;
	}

	/**
	 * Parse request.
	 */
	public function parse_templates()
	{
		if(array_key_exists('fragment', $_POST)){
			foreach($_POST['fragment'] as $fragment => $selector){
				$fragment = sanitize_text_field($fragment);
				$selector = sanitize_text_field($selector);

				$this->set_template($fragment, $selector); 
			}
		}
	}

	/**
	 * Get templates.
	 */
	public function get_templates()
	{
		if ( isset( $this->templates ) ) return $this->templates;
	}

	/**
	 * Setup append functionality.
	 */
	public function pre_append()
	{
		if(array_key_exists('appendContent', $_POST) && $_POST['appendContent'] != 'false'){
			if(has_action('ac/templates/posts', 'ac_templates_posts_start')):
				remove_action( 'ac/templates/posts', 'ac_templates_posts_start', 10 );
			endif;

			if(has_action('ac/templates/posts', 'ac_templates_posts_end')):
				remove_action( 'ac/templates/posts', 'ac_templates_posts_end', 30 );
			endif;
		}
	}

	public function post_append()
	{
		if(array_key_exists('appendContent', $_POST) && $_POST['appendContent'] != 'false'){
			if(!has_action('ac/templates/posts', 'ac_templates_posts_start')):
				add_action( 'ac/templates/posts', 'ac_templates_posts_start', 10 );
			endif;

			if(!has_action('ac/templates/posts', 'ac_templates_posts_end')):
				add_action( 'ac/templates/posts', 'ac_templates_posts_end', 30 );
			endif;
		}
	}

	/**
	 * Get template fragments.
	 */
	public function get_fragments()
	{
		$fragments = array();
		$this->parse_templates();
		$this->pre_append();

		foreach($this->get_templates() as $key => $value){
			ob_start();
			/* ac/templates/posts_start hook
			 *	@hooked ac_templates_posts_start	10
			 *	@hooked ac_templates_posts			20
			 *	@hooked ac_templates_posts_end		30
			 */
			do_action( "ac/templates/{$key}" );
			$fragments[$value] = ob_get_clean();
		}

		$this->post_append();
		
		return $fragments;
	}
}

endif;

return new AC_Fragment();