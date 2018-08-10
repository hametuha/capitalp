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
	$conditions[] = sprintf( 'この記事は<a href="%s">記事広告</a>です。', get_tag_link( 'pr' ) );
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
