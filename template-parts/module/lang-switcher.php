<?php
$parent = capitalp_original_page();
$child  = capitalp_translated_alternative();
if ( ! $parent && ! $child ) {
	// No content.
	return;
}
?>
<aside class="cappy-lang-switcher">
	
	<?php if ( $parent ) : // English page and has original. ?>
		<div class="for_en">
			<p>
				This page has original Japanese version
				&quot;<a href="<?= get_permalink( $parent ) ?>"><?= esc_html( get_the_title( $parent ) ) ?>&quot;</a>.
			</p>
		</div>
		<div class="for_jp">
			<p>
				このページにはオリジナルの日本語版<a href="<?= get_permalink( $parent ) ?>"><?= esc_html( get_the_title( $parent ) ) ?></a>があります。
			</p>
		</div>
	<?php endif; ?>
	
	<?php if ( $child ) : ?>
		<div class="for_en">
			<p>This post has English version &quot;<a
					href="<?= get_permalink( $child ) ?>"><?= esc_html( get_the_title( $child ) ) ?></a>&quot;.
			</p>
		</div>
		<div class="for_jp">
			<p>
				このページには英語版 <a href="<?= get_permalink( $child ) ?>"><?= esc_html( get_the_title( $child ) ) ?></a>
				があります。ご興味がある方はご一読ください。
			</p>
		</div>
	<?php elseif ( ! capitalp_is_english_page() ) : ?>
		<div class="for_en">
			<p>
				Howdy! This page is Japanese, but we are also writing <a href="<?= get_post_type_archive_link( 'en' ) ?>">English Posts</a>.
			</p>
		</div>
	<?php endif; ?>

</aside>

<script>
	jQuery(document).ready(function ($) {
		// Check UA and if english, show add title
		var browserLanguage = function () {
			var ua = window.navigator.userAgent.toLowerCase();
			try {
				// chrome
				if (ua.indexOf('chrome') != -1) {
					return ( navigator.languages[0] || navigator.browserLanguage || navigator.language || navigator.userLanguage).substr(0, 2);
				}
				// それ以外
				else {
					return ( navigator.browserLanguage || navigator.language || navigator.userLanguage).substr(0, 2);
				}
			}
			catch (e) {
				return undefined;
			}
		};
		if ('ja' !== browserLanguage()) {
			$('.cappy-lang-switcher').addClass('english-user');
		} else {
			$('.cappy-lang-switcher').addClass('japanese-user');
		}
		// Add body class.
		$( 'body' ).addClass( 'has-lang-message' );
	});
</script>
