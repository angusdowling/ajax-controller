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
$args = array(
	'paged'          => 1,
	'posts_per_page' => -1,
	'post_type'      => $field_value,
	'order'          => 'ASC',
	'orderby'        => 'title'
);

$field_query = new WP_Query($args);

foreach($filter_keys as $filter_key)
{
	/**
	 * @var array
	 **/
	$filter_query = (array_key_exists('meta_query', AC()->response['query']->query_vars)) ? AC()->response['query']->query_vars['meta_query'] : null;

    $index = 0;
	while ($field_query->have_posts()) : $field_query->the_post();
		$checked = '';

		if(!empty($filter_query) && array_key_exists($index, $filter_query) && array_key_exists('value', $filter_query[$index]))
		{
			if( $filter_query[$index]['value'] == get_the_ID() )
			{
				$checked = 'checked';
			}

			else if(is_array($filter_query[$index]['value']) && in_array(get_the_ID(), $filter_query[$index]['value']))
			{
				$checked = 'checked';
			}
		}

		echo '<div class="form-field">';
		echo    '<label>';
		echo        '<input type="checkbox" name="meta_query[' . $index .'][value]" ' . $checked . ' value="' . get_the_ID() . '">';
		echo        '<span class="label" tabindex="0">'. get_the_title() .'</span>';
		echo    '</label>';
        echo '</div>';

        $index++;
	endwhile;
	wp_reset_query();
}