<?php
/**
 * Member bempalte
 *
 * @var WP_User $member
 */
?>
<div class="capitalists-item">
	
	<?php if ( capitalp_is_public_capitalist( $member->ID ) && current_user_can( 'edit_others_posts' ) ) : ?>
		<?php echo get_avatar( $member->ID ); ?>
		<?php if ( preg_match( '#^https?://.+#u', $member->user_url ) ) : ?>
			<a href="<?php echo esc_url( $member->user_url ); ?>" class="capitlist-name">
				<?php echo esc_html( $member->display_name ); ?>
			</a>
		<?php else : ?>
			<span class="capitalist-name">
				<?php echo esc_html( $member->display_name ); ?>
			</span>
		<?php endif; ?>
	<?php else : ?>
		<?php echo get_avatar( 0 ); ?>
		<span class="capitalist-name">
			#<?php echo number_format( $member->ID ); ?>
		</span>
	<?php endif; ?>
</div>
