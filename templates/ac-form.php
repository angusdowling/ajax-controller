<?php
/**
 * Initial template loaded for archive
 *
 * @author      Yoke
 * @package     AjaxController/Templates
 * @version     0.1.0
 */

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

global $post;
?>

<?php if(!empty(AC()->response)): ?>

<?php
/* ac/templates/filter hook
*	@hooked ac_templates_filter_start 	10
*	@hooked ac_templates_filter_header	20
*	@hooked ac_templates_filter_fields	30
*	@hooked ac_templates_filter_end 	40
*/
do_action( 'ac/templates/filter');
?>

<?php
/* ac/templates/posts_start hook
*	@hooked ac_templates_posts_start	10
*	@hooked ac_templates_posts			20
*	@hooked ac_templates_posts_end		30
*/
do_action( 'ac/templates/posts' );
?>

<?php
if(AC()->options['pagination'] === 'true'):
	/* ac/templates/pagination hook
	*	@hooked ac_templates_pagination_start	10
	*	@hooked ac_templates_pagination_filter	20
	*	@hooked ac_templates_pagination_end		30
	*/
	do_action( 'ac/templates/pagination');
endif;
?>

<?php endif; ?>