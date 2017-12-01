<?php
/**
 * Member bempalte
 * @var WP_User $member
 */
?>
<div class="capitalists-item">
	
	<?php if ( capitalp_is_public_capitalist( $member->ID ) && current_user_can( 'edit_others_posts' ) ) : ?>
		<?= get_avatar( $member->ID ) ?>
		<?php if ( preg_match( '#^https?://.+#u', $member->user_url ) ) : ?>
			<a href="<?= esc_url( $member->user_url ) ?>" class="capitlist-name">
				<?= esc_html( $member->display_name ) ?>
			</a>
		<?php else: ?>
			<span class="capitalist-name">
				<?= esc_html( $member->display_name ) ?>
			</span>
		<?php endif; ?>
	<?php else : ?>
		<?= get_avatar( 0 ) ?>
		<span class="capitalist-name">
			#<?= number_format( $member->ID ) ?>
		</span>
	<?php endif; ?>
</div>
