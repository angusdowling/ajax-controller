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
	$query      = AC()->response['query'];
	$posttypes  = $query->query['post_type'];
?>

<?php

foreach($list as $slug => $name)
{
	$checked = '';

	foreach($posttypes as $posttype)
	{
		if($posttype == $slug)
		{
			$checked = 'checked ';
		}
	}

	echo '<div class="form-field">';
	echo    '<label>';
	echo        '<input type="checkbox" name="post_type[]" ' . $checked . ' value="' . $slug . '">';
	echo        '<span class="label" tabindex="0">'. $name .'</span>';
	echo    '</label>';
	echo '</div>';
}