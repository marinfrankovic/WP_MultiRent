<?php
/**
 * Site footer.
 *
 * @package MultiRent
 */

?>
	<footer class="site-footer">
		<div class="container footer-inner">
			<div class="footer-brand">
				<strong><?php echo esc_html( multirent_display_option( 'property_name', get_bloginfo( 'name' ) ) ); ?></strong>
			</div>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'fallback_cb'    => false,
				)
			);
			?>
			<p class="footer-credit">
			<?php
			printf(
				/* translators: %s: MultiRent project link. */
				esc_html__( 'Made with %s', 'multirent' ),
				'<a href="' . esc_url( 'http://multirent.online/' ) . '" target="_blank" rel="noopener">' . esc_html__( 'MultiRent', 'multirent' ) . '</a>'
			);
			?>
			</p>
		</div>
	</footer>
</div>
<?php wp_footer(); ?>
</body>
</html>