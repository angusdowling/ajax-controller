<?php
/**
 * Display a list of checkboxes for taxonomy or meta queries
 *
 * @author      Yoke
 * @package     AjaxController/Templates/Filters
 * @version     0.1.0
 */

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly
?>

<?php

foreach($filter_keys as $filter_key)
{
	/**
	* @var array
	**/
	$terms = get_terms(array('tax_query' => $filter_key));

	/**
	 * @var array
	 **/
	$filter_query = (array_key_exists('tax_query', AC()->response['query']->query_vars)) ? AC()->response['query']->query_vars['tax_query'] : null;

	foreach($terms as $term_key => $term)
	{
		$checked = '';

		if(!empty($filter_query) && array_key_exists($filter_key, $filter_query))
		{
			if( $filter_query[$filter_key]['terms'] == $term->term_id || in_array($term->term_id, $filter_query[$filter_key]['terms']) )
			{
				$checked = 'checked ';
			}
		}

		echo '<div class="form-field">';
		echo    '<label>';
		echo        '<input type="checkbox" name="tax_query[' . $filter_key .'][terms]" ' . $checked . ' value="' . $term->term_id . '">';
		echo        '<span class="label" tabindex="0">'. $term->name .'</span>';
		echo    '</label>';
		echo '</div>';
	}
}