<?php
/**
 * Hatebu button
 */
class Cappy_Share_Hatebu extends Sharing_Source {

	public $shortname = 'hatebu';

	public $genericon = '\f469';

	public $smart = true;

	/**
	 * @return string
	 */
	public function get_name() {
		return 'はてぶ';
	}

	/**
	 * Get permalink
	 *
	 * @param WP_Post $post
	 *
	 * @return string
	 */
	public function get_display( $post ) {
		ob_start();
		$url = sprintf( 'http://b.hatena.ne.jp/entry/%s', preg_replace( '#^https?://#', 's/', get_permalink( $post ) ) );
		?>
		<a href="<?= $url ?>" class="hatena-bookmark-button" data-hatena-bookmark-layout="basic-label-counter" data-hatena-bookmark-lang="ja" title="このエントリーをはてなブックマークに追加">
			<img src="https://b.st-hatena.com/images/entry-button/button-only@2x.png" alt="このエントリーをはてなブックマークに追加" width="20" height="20" style="border: none;" />
		</a>
		<script type="text/javascript" src="https://b.st-hatena.com/js/bookmark_button.js" charset="utf-8" async="async"></script>
		<?php
		$link = ob_get_contents();
		return $link;
	}
}
