<?php
if ( ! is_singular( 'post' ) ) {
	return;
}
$counter = capitalp_template_counter( __FILE__ );

switch ( $counter ) {
	case 1: // Before title.
		?>
		<div class="ad-single-top">
			<span class="ad-title">SPONSORED LINK</span>
			<?php capitalp_ad( 'after_title' ) ?>
		</div>
		<?php
		get_template_part( 'template-parts/block/contributor' );
		break;
	case 2: // After title.
	case 3: // Side bar
	default:
		// Do nothing.
		break;
}
