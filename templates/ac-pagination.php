<?php
/**
 * Pagination for archive
 *
 * @author      Yoke
 * @package     AjaxController/Templates
 * @version     0.1.0
 */

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

global $wp_query, $paged;
$wp_query = AC()->response['query'];

$max_page = $wp_query->max_num_pages;
$paged    = $wp_query->query_vars['paged'];
$range    = ( !empty($range) ) ? $range : 5;
?>

<?php if ( $max_page > 1 ): ?>
<div class="pagination">
	<ul class="pagination__list">
		<?php  if ( $paged != 1 ):  ?>
		<li class="pagination__item first" data-paged="<?php echo 1; ?>">
			<span class="pagination__link">First</span>
		</li>
		<?php endif; ?>

		<?php  if( $paged > 1 ) :   ?>
		<li class="pagination__item previous" data-paged="previous">
			<span class="pagination__link">
				<?php previous_posts_link('Previous'); ?>
			</span>
		</li>
		<?php  endif;  ?>

		<?php if ( $max_page > $range ) : ?>
			<?php if ( $paged < $range ) : ?>
				<?php for ( $i = 1; $i <= ($range); $i++ ): ?>
					<?php $class = ($i == $paged) ? 'current' : ''; ?>
		<li class="pagination__item number" data-paged="<?php echo $i; ?>">
			<span class="pagination__link <?php echo $class ?>"><?php echo $i; ?></span>
		</li>
				<?php endfor; ?>
			<?php elseif( $paged >= ( $max_page - floor($range/2)) ): ?>
				<?php for( $i = $max_page - $range; $i <= $max_page; $i++ ): ?>
					<?php $class = ($i == $paged) ? 'current' : ''; ?>
		<li class="pagination__item number" data-paged="<?php echo $i; ?>">
			<span class="pagination__link <?php echo $class ?>"><?php echo $i; ?></span>
		</li>
				<?php endfor; ?>
			<?php elseif( $paged >= $range && $paged < ( $max_page - floor($range/2)) ): ?>
				<?php for ( $i = ($paged - floor($range/2)); $i <= ($paged + floor($range/2)); $i++ ): ?>
					<?php $class = ($i == $paged) ? 'current' : ''; ?>
		<li class="pagination__item number" data-paged="<?php echo $i; ?>">
			<span class="pagination__link <?php echo $class ?>"><?php echo $i ?></span>
		</li>
				<?php endfor; ?>
			<?php else: ?>
				<?php for ( $i = 1; $i <= $max_page; $i++ ): ?>
					<?php $class = ($i == $paged) ? 'current' : ''; ?>
		<li class="pagination__item number" data-paged="<?php echo $i; ?>">
			<span class="pagination__link <?php echo $class; ?>"><?php echo $i; ?></span>
		</li>
				<?php endfor; ?>
			<?php endif; ?>
		<?php else: ?>
			<?php for ( $i = 1; $i <= $max_page; $i++ ): ?>
				<?php $class = ($i == $paged) ? 'current' : ''; ?>
		<li class="pagination__item number" data-paged="<?php echo $i; ?>">
			<span class="pagination__link <?php echo $class; ?>"><?php echo $i; ?></span>
		</li>
			<?php endfor; ?>
		<?php endif; ?>

		<?php if( $paged < $max_page ) : ?>
		<li class="pagination__item next" data-paged="next">
			<span class="pagination__link">
				<?php next_posts_link('Next'); ?>
			</span>
		</li>
		<?php endif; ?>

		<?php if ( $paged != $max_page ): ?>
		<li class="pagination__item last" data-paged="<?php echo $max_page; ?>">
			<span class="pagination__link">Last</span>
		</li>
		<?php endif; ?>
	</ul>
</div>
<?php endif; ?>

<?php wp_reset_query(); ?>