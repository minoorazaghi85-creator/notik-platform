<?php
/**
 * Main template file
 *
 * @package Notik
 */

get_header();
?>

<div class="container" style="padding: 2rem 1rem;">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h1><?php the_title(); ?></h1>
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
			</article>
		<?php endwhile; ?>
	<?php else : ?>
		<p><?php esc_html_e( 'محتوایی یافت نشد.', 'notik' ); ?></p>
	<?php endif; ?>
</div>

<?php
get_footer();
