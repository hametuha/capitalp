<?php
$conditions = [];
// ゲスト寄稿
if ( author_can( get_post(), 'contributor' ) ) {
	$conditions[] = sprintf( 'これは%sによるゲスト投稿記事です。', get_the_author_posts_link() );
}
// シリーズ
if ( $series = capitalp_get_series() ) {
	$conditions[] = sprintf( 'この記事は連載%sの一部です。', implode( ', ', array_map( function( $tag ) {
		return sprintf( '<a href="%1$s">&quot;%2$s&quot;</a>', get_tag_link( $tag ), esc_html( $tag->name ) );
	}, $series ) ) );
}
// 会員専用
if ( chiramise_should_check() ) {
	$conditions[] = sprintf( 'この記事は<a href="%s">会員専用コンテンツ</a>を含んでいます。', home_url( 'ccp' ) );
}

// 広告
if ( has_tag( 'pr' ) ) {
	$conditions[] = sprintf( 'この記事は<a href="%s">スポンサー記事</a>です。商品の提供などを受けた上で書かれていますことをご了承ください。', get_term_link( 'pr', 'post_tag' ) );
}

if ( ! $conditions ) {
	return;
}
?>
<aside class="capitalp-condition">
	<ul class="capitalp-condition-list">
	<?php foreach ( $conditions as $condition ) : ?>
		<li class="cpitalp-condition-item"><?= $condition ?></li>
	<?php endforeach; ?>
	</ul>
	
</aside>
