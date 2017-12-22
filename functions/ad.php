<?php
/**
 * Advertisement functionality
 *
 * @package capitalp
 */

/**
 * Display advertisement
 *
 * @param string $position to display.
 * @return void
 */
function capitalp_ad( $position ) {
	switch ( $position ) {
		case 'infeed':
			echo <<<HTML
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<ins class="adsbygoogle"
     style="display:block"
     data-ad-format="fluid"
     data-ad-layout-key="-7t+26-g1+6y+14k"
     data-ad-client="ca-pub-0087037684083564"
     data-ad-slot="3217726954"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>
HTML;
			break;
		case 'inside_loop':
			echo <<<HTML
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- CapitalP InsideLoop -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-0087037684083564"
     data-ad-slot="2504472441"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
HTML;
			break;
		case 'sidebar':
			echo <<<HTML
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- CapitalP Sidebar -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-0087037684083564"
     data-ad-slot="1027739246"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
HTML;
			break;
		case 'after_content':
			echo <<<HTML
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- CapitalP AfterContent -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-0087037684083564"
     data-ad-slot="7074272842"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
HTML;
			break;
		case 'related':
			echo <<<HTML
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<ins class="adsbygoogle"
	 style="display:block"
	 data-ad-format="autorelaxed"
	 data-ad-client="ca-pub-0087037684083564"
	 data-ad-slot="7742682637"></ins>
<script>
  (adsbygoogle = window.adsbygoogle || []).push({});
</script>
HTML;

			break;
		case 'after_title':
		default:
			echo <<<HTML
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- CapitalP After Title -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-0087037684083564"
     data-ad-slot="1306940844"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
HTML;
			break;
	}
}
