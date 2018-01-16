<?php
/**
 * Display a list of checkboxes for taxonomy or meta queries
 *
 * @author 		Yoke
 * @package 	AjaxController/Templates/Filters
 * @version     0.1.0
 */

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly
?>

<?php
foreach($filter_keys as $filter_key){
	$terms            = get_terms(array('tax_query' => $filter_key));
	$filter_query     = (array_key_exists('tax_query', AC()->response['query']->query_vars)) ? AC()->response['query']->query_vars['tax_query'] : null;
	$custom_classes   = (empty($custom_classes)) ? '' : $custom_classes;
	$default_option   = (empty($default_option)) ? 'All' : $default_option;
	$default_selected = (empty($filter_query[$filter_key])) ? 'selected' : '';

	echo '<div class="form-field">';
	echo     '<select name="tax_query['. $filter_key .'][terms]" class="'. $custom_classes .'">';
	echo         '<option '.$default_selected.' value>'.$default_option.'</option>';
	
	foreach($terms as $term_key => $term){
		$selected = '';

		if(!empty($filter_query) && array_key_exists($filter_key, $filter_query)){
			if( $filter_query[$filter_key]['terms'] == $term->term_id || in_array($term->term_id, $filter_query[$filter_key]['terms']) ){
				$selected = 'selected ';
			}
		}

		echo '<option value="'.$term->term_id.'" '.$selected.'>'.$term->name.'</option>';
	};
	echo     '</select>';
	echo '</div>';
	wp_reset_query();
}