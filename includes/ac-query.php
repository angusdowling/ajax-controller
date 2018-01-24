<?php
/**
 * Class for generating query
 *
 * @author      Yoke
 * @package     AjaxController/Includes
 * @since       0.1.0
 */

if ( ! defined( 'ABSPATH' ) )
{
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'AC_Query' ) ) :

class AC_Query {
	/**
	 * @var object $query
	 */
	private $query = null;

	/**
	 * @var array $parameters
	 */
	private $parameters = null;

	/**
	 * @var array $ignored
	 */
	private $ignored = array(
		'_ajax_nonce',
		'url',
		'fragment',
		'action',
		'post',
		'options',
		'appendContent',
		'ignored'
	);

	/**
	 * Set query.
	 */
	public function set_query()
	{

		$this->query = new WP_Query(apply_filters('ac_query_args', $this->parameters, $this));

	}


	/**
	 * Set filters.
	 */
	public function set_filters()
	{

		add_filter( 'posts_join', array($this, 'cf_search_join') );
		add_filter( 'posts_where', array($this, 'cf_search_where') );
		add_filter( 'posts_distinct', array($this, 'cf_search_distinct') );
		add_filter( 'option_scporder_options', array($this, 'remove_scporder_options' ));

	}


	/**
	* Cancel force sort order
	*/
	function remove_scporder_options( $options )
	{

		$options = array();

		return $options;

	}

	/**
	 * Set parameter.
	 */
	public function set_parameter($key, $value)
	{

		$this->parameters[$key] = $this->format_parameter($key, $value);

	}


	/**
	 * Get query.
	 */
	public function get_query()
	{

		if ( isset( $this->query ) ) return $this->query;

	}


	/**
	 * Get parameters.
	 */
	public function get_parameters()
	{

		if ( isset( $this->parameters ) ) return $this->parameters;

	}


	/**
	 * Clean parameters after query.
	 */
	public function clean()
	{

		$this->parameters = null;

	}


	/**
	 * Parse request.
	 */
	public function parse_request( $parameters = null )
	{

		$arr = ( !empty( $_POST ) ) ? $_POST : $parameters;

		if( is_array( $parameters ) && is_array( $_POST ) )
		{
			if( array_key_exists( 'meta_query', $parameters ) && array_key_exists( 'meta_query', $_POST ) )
			{
				if( array_key_exists( '0', $parameters['meta_query'] ) && array_key_exists( '0', $_POST['meta_query'] ) )
				{
					$parameters['meta_query']['0'] = $_POST['meta_query']['0'];
					$arr['meta_query'] = $parameters['meta_query'];
				}

				else
				{
					$arr['meta_query'] = array_merge_recursive($parameters['meta_query'], $_POST['meta_query']);
				}
			}

			else if( array_key_exists( 'meta_query', $parameters ) )
			{
				$arr['meta_query'] = $parameters['meta_query'];
			}
		}

		foreach( $arr as $key => $value ){
			if ( !empty( $value ) && !in_array( $key, $this->ignored ) )
			{
				if( !is_array( $value ) && !is_null( json_decode( stripslashes( $value ) ) ) )
				{
					$this->set_parameter($key, json_decode(stripslashes($value), true));
				}

				else if( is_array( $value ) && !empty( array_filter( $value ) ) )
				{
					$this->set_parameter( $key, $value );
				}

				else
				{
					$this->set_parameter( $key, $value );
				}
			}
		}

		$this->set_filters();
		$this->set_query();
		$this->clean();

	}


	/**
	 * Format $_POST parameter.
	 *
	 * @return any
	 */
	public function format_parameter( $key, $value )
	{

		$result = $value;

		switch( gettype( $value ) )
		{
			case 'int':
				$result = (int)$value;
				break;

			case 'string':
				$result = sanitize_text_field( $value );
				break;

			default:
				switch( $key )
				{
					case 'tax_query':
						foreach( $value as $name => $filter )
						{
							if( empty( $filter['terms'] ) )
							{
								unset( $value[$name] );
								continue;
							}

							if( !array_key_exists('taxonomy', $filter) )
							{
								$value[$name]['taxonomy'] = $name;
							}

							if( !array_key_exists( 'field', $filter ) )
							{
								$value[$name]['field'] = 'term_id';
							}
						}

						if( !empty( $value ) && !array_key_exists( 'relation', $value ) )
						{
							$value['relation'] = 'AND';
						}

						array_unique( $value['terms'] );
						break;
				}

				$result = $value;
				break;

		}

		return $result;

	}


	/**
	 * Join posts and postmeta tables
	 */
	public function cf_search_join( $join )
	{

		global $wpdb;

		if ( AC()->options['is_search'] == 'true' )
		{
			$join .=' LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
		}

		return $join;

	}


	/**
	 * Modify the search query with posts_where
	 */
	public function cf_search_where( $where )
	{

		global $pagenow, $wpdb;

		if ( AC()->options['is_search'] == 'true' )
		{
			$where = preg_replace(
				"/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
				"(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1)", $where );
		}

		return $where;

	}


	/**
	 * Prevent duplicates
	 */
	public function cf_search_distinct( $where )
	{

		global $wpdb;

		if ( AC()->options['is_search'] == 'true' )
		{
			return "DISTINCT";
		}

		return $where;

	}
}

endif;

return new AC_Query();