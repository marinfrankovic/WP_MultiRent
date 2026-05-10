<?php
/**
 * Site footer.
 *
 * @package MultiRent
 */

?>
	<footer class="site-footer">
		<div class="container footer-inner">
			<div>
				<strong><?php echo esc_html( multirent_display_option( 'property_name', get_bloginfo( 'name' ) ) ); ?></strong>
				<p><?php esc_html_e( 'A configurable rental website powered by Multi Apartment Rental.', 'multirent' ); ?></p>
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
		</div>
	</footer>
</div>
<?php wp_footer(); ?>
</body>
</html>