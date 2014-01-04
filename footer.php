<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */
?>

		</div><!-- #main -->
		<footer id="colophon" class="footer-area" role="contentinfo">
			<?php get_sidebar( 'main' ); ?>
			<ul id="social-icons">
				<li><a href="https://www.facebook.com/DUPirates" title="Facebook"><img alt="Facebook" src="<?php echo get_template_directory_uri(); ?>/images/facebook.jpg"></a></li>
				<li><a href="https://twitter.com/dupirates" title="Twitter"><img alt="Twitter" src="<?php echo get_template_directory_uri(); ?>/images/twitter.jpg"></a></li>
				<li><a href="<?php bloginfo('rss2_url'); ?>" title="RSS"><img alt="RSS" src="<?php echo get_template_directory_uri(); ?>/images/rss.jpg"></a></li>
			</ul>
			<div class="license-info">
	          <a href="http://creativecommons.org/licenses/by-sa/3.0/"><img class="cc-license" src="<?php echo get_template_directory_uri(); ?>/images/creative-commons.png" alt="Creative Commons Attribution-ShareAlike 3.0"></a>
	          <div class="cc-text">This site is licensed under a <a href="http://creativecommons.org/licenses/by-sa/3.0/">Creative Commons Attribution-ShareAlike 3.0 Unported License.</a></div>
	        </div>

			<div class="site-info">
				<?php do_action( 'twentythirteen_credits' ); ?>
				<a href="<?php echo esc_url( __( 'http://wordpress.org/', 'twentythirteen' ) ); ?>" title="<?php esc_attr_e( 'Semantic Personal Publishing Platform', 'twentythirteen' ); ?>"><?php printf( __( 'Proudly powered by %s', 'twentythirteen' ), 'WordPress' ); ?></a>
			</div><!-- .site-info -->
		</footer><!-- #colophon -->
	</div><!-- #page -->

	<?php wp_footer(); ?>
</body>
</html>