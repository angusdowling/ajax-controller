<?php
/**
 * AjaxController
 *
 * Functions for the templating system
 *
 * @author      Yoke
 * @package     AjaxController/Includes
 * @since       0.1.0
 */

if ( ! defined( 'ABSPATH' ) )
{
	exit; // Exit if accessed directly
}

/** Form ****************************************************************/

if ( ! function_exists( 'ac_templates_form_start' ) )
{
	/**
	 * Output form start.
	 *
	 * @access public
	 * @param object $response
	 */
	function ac_templates_form_start()
	{

		ob_start();
		AC_Helper::get_template('form/ac-form-start.php');
		echo ob_get_clean();

	}
}
add_action( 'ac/templates/form', 'ac_templates_form_start', 10 );


if ( ! function_exists( 'ac_templates_form' ) )
{
	/**
	 * Output form start.
	 *
	 * @access public
	 * @param object $response
	 */
	function ac_templates_form()
	{

		ob_start();
		AC_Helper::get_template('ac-form.php');
		echo ob_get_clean();

	}
}
add_action( 'ac/templates/form', 'ac_templates_form', 20 );


if ( ! function_exists( 'ac_templates_form_end' ) )
{
	/**
	 * Output form end.
	 *
	 * @access public
	 * @param object $response
	 */
	function ac_templates_form_end()
	{

		ob_start();
		AC_Helper::get_template('form/ac-form-end.php');
		echo ob_get_clean();

	}
}
add_action( 'ac/templates/form', 'ac_templates_form_end', 30 );


/** Filter ****************************************************************/

if ( ! function_exists( 'ac_templates_filter_start' ) )
{
	/**
	 * Output filter start.
	 *
	 * @access public
	 * @param object $response
	 */
	function ac_templates_filter_start()
	{

		ob_start();
		AC_Helper::get_template('filter/ac-filter-start.php');
		echo ob_get_clean();

	}
}
add_action( 'ac/templates/filter', 'ac_templates_filter_start', 10 );


if ( ! function_exists( 'ac_templates_filter_header' ) )
{
	/**
	 * Output filter header.
	 *
	 * @access public
	 * @param object $response
	 */
	function ac_templates_filter_header()
	{

		ob_start();
		AC_Helper::get_template('filter/ac-filter-header.php');
		echo ob_get_clean();

	}
}
add_action( 'ac/templates/filter', 'ac_templates_filter_header', 20 );


if ( ! function_exists( 'ac_templates_filter_fields' ) )
{
	/**
	 * Output filter fields.
	 *
	 * @access public
	 * @param object $response
	 */
	function ac_templates_filter_fields()
	{

		ob_start();
		AC_Helper::get_template('filter/ac-filter-fields.php');
		echo ob_get_clean();

	}
}
add_action( 'ac/templates/filter', 'ac_templates_filter_fields', 30 );


if ( ! function_exists( 'ac_templates_filter_hidden' ) )
{
	/**
	 * Output filter fields.
	 *
	 * @access public
	 * @param object $response
	 */
	function ac_templates_filter_hidden()
	{

		ob_start();
		AC_Helper::get_template('filter/ac-filter-hidden.php');
		echo ob_get_clean();

	}
}
add_action( 'ac/templates/filter', 'ac_templates_filter_hidden', 30 );


if ( ! function_exists( 'ac_templates_filter_end' ) )
{
	/**
	 * Output filter end.
	 *
	 * @access public
	 * @param object $response
	 */
	function ac_templates_filter_end()
	{

		ob_start();
		AC_Helper::get_template('filter/ac-filter-end.php');
		echo ob_get_clean();

	}
}
add_action( 'ac/templates/filter', 'ac_templates_filter_end', 40 );


/** Posts ****************************************************************/

if ( ! function_exists( 'ac_templates_posts_start' ) )
{
	/**
	 * Output posts start.
	 *
	 * @access public
	 * @param object $response
	 */
	function ac_templates_posts_start()
	{

		ob_start();
		AC_Helper::get_template('posts/ac-posts-start.php');
		echo ob_get_clean();

	}
}
add_action( 'ac/templates/posts', 'ac_templates_posts_start', 10 );


if ( ! function_exists( 'ac_templates_posts' ) )
{
	/**
	 * Output posts.
	 *
	 * @access public
	 * @param object $response
	 */
	function ac_templates_posts()
	{

		ob_start();
		AC_Helper::get_template('ac-posts.php');
		echo ob_get_clean();

	}
}
add_action( 'ac/templates/posts', 'ac_templates_posts', 20 );


if ( ! function_exists( 'ac_templates_posts_end' ) )
{
	/**
	 * Output posts end.
	 *
	 * @access public
	 * @param object $response
	 */
	function ac_templates_posts_end()
	{

		ob_start();
		AC_Helper::get_template('posts/ac-posts-end.php');
		echo ob_get_clean();

	}
}
add_action( 'ac/templates/posts', 'ac_templates_posts_end', 30 );


/** Pagination ****************************************************************/

if ( ! function_exists( 'ac_templates_pagination_start' ) )
{
	/**
	 * Output pagination start.
	 *
	 * @access public
	 * @param object $response
	 */
	function ac_templates_pagination_start()
	{

		ob_start();
		AC_Helper::get_template('pagination/ac-pagination-start.php');
		echo ob_get_clean();

	}
}
add_action( 'ac/templates/pagination', 'ac_templates_pagination_start', 10 );


if ( ! function_exists( 'ac_templates_pagination' ) )
{
	/**
	 * Output pagination.
	 *
	 * @access public
	 * @param object $response
	 */
	function ac_templates_pagination()
	{

		ob_start();
		AC_Helper::get_template('ac-pagination.php');
		echo ob_get_clean();

	}
}
add_action( 'ac/templates/pagination', 'ac_templates_pagination', 20 );


if ( ! function_exists( 'ac_templates_pagination_end' ) )
{
	/**
	 * Output pagination end.
	 *
	 * @access public
	 * @param object $response
	 */
	function ac_templates_pagination_end()
	{

		ob_start();
		AC_Helper::get_template('pagination/ac-pagination-end.php');
		echo ob_get_clean();

	}
}
add_action( 'ac/templates/pagination', 'ac_templates_pagination_end', 30 );


/** Loop ****************************************************************/

if ( ! function_exists( 'ac_templates_loop_start' ) )
{
	/**
	 * Output loop start.
	 *
	 * @access public
	 * @param object $response
	 */
	function ac_templates_loop_start()
	{

		ob_start();
		AC_Helper::get_template('loop/ac-loop-start.php');
		echo ob_get_clean();

	}
}
add_action( 'ac/templates/loop_start', 'ac_templates_loop_start', 10 );


if ( ! function_exists( 'ac_templates_loop_end' ) )
{
	/**
	 * Output loop end.
	 *
	 * @access public
	 * @param object $response
	 */
	function ac_templates_loop_end()
	{

		ob_start();
		AC_Helper::get_template('loop/ac-loop-end.php');
		echo ob_get_clean();

	}
}
add_action( 'ac/templates/loop_end', 'ac_templates_loop_end', 10 );


if ( ! function_exists( 'ac_templates_loop_post_before' ) )
{
	/**
	 * Output before the post.
	 *
	 * @access public
	 * @param object $response
	 */
	function ac_templates_loop_post_before()
	{

		ob_start();
		AC_Helper::get_template('loop/ac-loop-post-before.php');
		echo ob_get_clean();

	}
}
add_action( 'ac/templates/loop_post', 'ac_templates_loop_post_before', 10 );


if ( ! function_exists( 'ac_templates_loop_post' ) )
{
	/**
	 * Output the post.
	 *
	 * @access public
	 * @param object $response
	 */
	function ac_templates_loop_post()
	{

		ob_start();
		AC_Helper::get_template_part('templates/content', get_post_type());
		echo ob_get_clean();

	}
}
add_action( 'ac/templates/loop_post', 'ac_templates_loop_post', 20 );


if ( ! function_exists( 'ac_templates_loop_post_after' ) )
{
	/**
	 * Output after the post.
	 *
	 * @access public
	 * @param object $response
	 */
	function ac_templates_loop_post_after()
	{

		ob_start();
		AC_Helper::get_template('loop/ac-loop-post-after.php');
		echo ob_get_clean();

	}
}
add_action( 'ac/templates/loop_post', 'ac_templates_loop_post_after', 30 );