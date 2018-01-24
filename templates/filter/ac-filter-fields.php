<?php
/**
 * Filter fields
 *
 * @author      Yoke
 * @package     AjaxController/Templates
 * @since       0.1.0
 */

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

$the_query = AC()->response['query'];

?>
<div class="ajax-filter__fields">
	<?php
		AC_Helper::get_template('fields/ac-tax-checkbox.php', array(
			'the_query'   => $the_query,
			'filter_keys' => array('category'),
			'filter_type' => 'tax_query'
		));
	?>

	<div class="form-field">
		<button class="ajax-action" data-ajax-clear>Clear</button>
		<button class="ajax-action" data-ajax-reset>Reset</button>
		<button class="ajax-action" data-ajax-submit>Submit</button>
	</div>
</div>