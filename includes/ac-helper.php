<?php
/**
 * Helper class
 *
 * @author 		Yoke
 * @package 	AjaxController/Includes
 * @version     0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class AC_Helper {
	/**
	 * Get template part.
	 *
	 * @access public
	 * @param mixed $slug
	 * @param string $name (default: '')
	 * @return void
	 */
	public static function get_template_part( $slug, $name = '' )
	{
		$template = '';
	
		// Look in yourtheme/slug-name.php and yourtheme/ajaxcontroller/slug-name.php
		if ( $name ) {
			$template = locate_template( array( "{$slug}-{$name}.php", self::template_path() . "{$slug}-{$name}.php" ) );
		}
	
		// Get default slug-name.php
		if ( ! $template && $name && file_exists( self::plugin_path() . "/templates/{$slug}-{$name}.php" ) ) {
			$template = self::plugin_path() . "/templates/{$slug}-{$name}.php";
		}
	
		// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/ajaxcontroller/slug.php
		if ( ! $template ) {
			$template = locate_template( array( "{$slug}.php", self::template_path() . "{$slug}.php" ) );
		}
	
		// Allow 3rd party plugin filter template file from their plugin
		$template = apply_filters( 'ac_get_template_part', $template, $slug, $name );
	
		if ( $template ) {
			load_template( $template, false );
		}
	}
	
	/**
	 * Get other templates, passing attributes and including the file.
	 *
	 * @access public
	 * @param string $template_name
	 * @param array $args (default: array())
	 * @param string $template_path (default: '')
	 * @param string $default_path (default: '')
	 * @return void
	 */
	public static function get_template( $template_name, $args = array(), $template_path = '', $default_path = '' )
	{
		if ( $args && is_array( $args ) ) {
			extract( $args );
		}

		$located = self::locate_template( $template_name, $template_path, $default_path );

		if ( ! file_exists( $located ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );
			return;
		}

		do_action( 'ac_before_template_part', $template_name, $template_path, $located, $args );

		include( $located );

		do_action( 'ac_after_template_part', $template_name, $template_path, $located, $args );
	}

	/**
	 * Locate a template and return the path for inclusion.
	 *
	 * This is the load order:
	 *
	 *		yourtheme		/	$template_path	/	$template_name
	 *		yourtheme		/	$template_name
	 *		$default_path	/	$template_name
	 *
	 * @access public
	 * @param string $template_name
	 * @param string $template_path (default: '')
	 * @param string $default_path (default: '')
	 * @return string
	 */
	public static function locate_template( $template_name, $template_path = '', $default_path = '' )
	{
		if ( ! $template_path ) {
			if( !empty(AC()->options) && is_array(AC()->options) && array_key_exists('template_path', AC()->options)){
				$template_path = self::template_path() . AC()->options['template_path'] . '/';
			}

			else {
				$template_path = self::template_path();
			}
		}

		$template_base_path = self::template_path() . 'default/';

		if ( ! $default_path ) {
			$default_path = self::plugin_path() . '/templates/';
		}

		// Look within passed path within the theme - this is priority
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name
			)
		);

		// Look in theme default path
		if( ! $template ) {
			$template = locate_template(
				array(
					trailingslashit( $template_base_path ) . $template_name,
					$template_name
				)
			);
		}

		// Get default template
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}

		// Return what we found
		return apply_filters('ac_locate_template', $template, $template_name, $template_path);
	}

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public static function plugin_url()
	{
		return untrailingslashit( plugins_url( '/', dirname(__FILE__) ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public static function plugin_path()
	{
		return untrailingslashit( plugin_dir_path( dirname(__FILE__) ) );
	}

	/**
	 * Get the template path.
	 *
	 * @return string
	 */
	public static function template_path()
	{
		return apply_filters( 'AC_TEMPLATE_PATH', 'ajaxcontroller/' );
	}
	
	/**
	 * Check if Wordpress AJAX variable is set.
	 */
	public static function doing_ajax()
	{
		if(defined('DOING_AJAX') && DOING_AJAX){
			return true;
		}

		return false;
	}

	/**
	 * Convert an array to bracket syntax.
	 */
	public static function convert_bracket_syntax($arr, $arrkey, $depth = 0) {
		$result = array();

		foreach($arr as $key => $value) {
			$output = "[${key}]";

			if(!is_array($value)) {   
				$output .= '';

				if($depth == 0 && strstr($output, $arrkey) === false){
					$output = $arrkey . $output;
				}

				$result[] = array(
					'key'   => $output,
					'value' => $value
				);
			} else {
				foreach(AC_Helper::convert_bracket_syntax($value, $arrkey, $depth + 1) as $sub_val) {
					if($depth == 0 && strstr($output, $arrkey) === false){
						$sub_val['key'] = $arrkey . $output . $sub_val['key'];
					} else {
						$sub_val['key'] = $output . $sub_val['key'];
					}

					$result[] = $sub_val;
				}
			}
		}

		return $result;
	}

	/**
	 * Check if substring is in array.
	 */
	public static function substr_in_array($arr, $str){
		foreach($arr as $item) {
			if (stripos($str, $item) != false) {
				return true;
			}
		}
	
		return false;
	}
}