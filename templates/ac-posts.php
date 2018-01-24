<?php
/**
 * Initial template loaded for archive
 *
 * @author      Yoke
 * @package     AjaxController/Templates
 * @since       0.1.0
 */

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly
?>

<?php
/* ac_loop_before hook
*   @hooked ac_loop_before  10
*/
do_action('ac/templates/loop_start');
?>

<?php if(!AC()->response['query']->have_posts()): ?>
	<div class="ajax-posts-noresults">
		<p>Sorry, no results found.</p>
	</div>
<?php endif; ?>

<?php while (AC()->response['query']->have_posts()) : AC()->response['query']->the_post(); ?>
	<?php
	/* ac_loop_post hook
	*   @hooked ac_loop_post_start  10
	*   @hooked ac_loop_post        20
	*   @hooked ac_loop_post_end    30
	*/
	do_action('ac/templates/loop_post');
	?>
<?php endwhile; ?>
<?php wp_reset_query(); ?>

<?php
/* ac_loop_end hook
*   @hooked ac_loop_end  10
*/
do_action('ac/templates/loop_end');
?>