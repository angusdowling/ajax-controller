<?php
/**
 * Display a select for taxonomy or meta queries
 *
 * @author      Yoke
 * @package     AjaxController/Templates/Filters
 * @version     0.1.0
 */

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly
?>

<?php if(!empty($filter_type)): ?>
	<?php
		switch($filter_type):
			case 'taxonomy':
				$select_name  = 'tax_query';

				if(array_key_exists('tax_query', $the_query->query_vars)){
					$filter_query = $the_query->query_vars['tax_query'];
				}

				break;
			case 'meta':
				$select_name  = 'meta_query';

				if(array_key_exists('meta_query', $the_query->query_vars)){
					$filter_query = $the_query->query_vars['meta_query'];
				}
				break;
		endswitch;
	?>

	<?php foreach($filter_keys as $filter_key): ?>
		<?php
			/**
			* @var array
			**/
			$terms = get_terms(array($filter_type => $filter_key));
		?>
		<div class="form-field">
			<select name="<?php echo $select_name; ?>[<?php echo $filter_key; ?>]">
			<option value><?php echo $default_option; ?></option>

			<?php foreach ($terms as $term_key => $term): ?>
				<?php
				/**
				* @var string
				**/
				$selected = '';

				if( !empty($filter_query) && array_key_exists($filter_key, $filter_query) ){
					if( $filter_query[$filter_key]['terms'] == $term->term_id || in_array($term->term_id, $filter_query[$filter_key]['terms']) ){
						$selected = 'selected';
					}
				}
				?>
				<option value="<?php echo $term->term_id; ?>" <?php echo $selected; ?>><?php echo $term->name; ?></option>
			<?php endforeach ?>
			</select>
		</div>
	<?php endforeach; ?>
<?php endif; ?>