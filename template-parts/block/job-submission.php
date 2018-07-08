<?php

wp_enqueue_script(
	'capitalp-job-board',
	get_stylesheet_directory_uri() . '/assets/js/job-board.js',
	[ 'jquery' ],
	wp_get_theme()->get( 'Version' ),
	true
);
wp_localize_script( 'capitalp-job-board', 'JobBoard', [
	'endpoint' => rest_url( '/capitalp/v1/job/' . get_the_ID() ),
	'nonce'    => wp_create_nonce( 'wp_rest' ),
] );

if ( ! is_user_logged_in() ) : ?>
	<h2>採用企業・応募方法</h2>
	<div class="wpac-alert wpac-alert--warning mceNonEditable">
		<div class="wpac-alert__body mceEditable">
			採用企業や応募方法を見るには<a class="alert-link" href="<?= wp_login_url( get_permalink() ) ?>">ログイン</a>してください。
			アカウントは<strong>SNSログイン</strong>で作成できます。
		</div>
	</div>
<?php else : ?>
	<?php
	$companies = get_the_terms( get_post(), 'company' );
	if ( $companies && ! is_wp_error( $companies ) ) {
		foreach ( $companies as $company ) {
			?>
			<div class="wp-profile-box">
				<h2 class="wp-profile-box__title">採用企業</h2>
				<div class="wp-profile-box__container">
					<div class="wp-profile-box__figure">
						<?php if ( $attachment = get_term_meta( $company->term_id, 'image', true ) ) {
							echo wp_get_attachment_image( $attachment, 'large', false, [
								'class' => 'avatar photo',
							] );
						} ?>
					</div>
					<div class="wp-profile-box__body">
						<h3 class="wp-profile-box__name">
							<?= esc_html( $company->name ) ?>
						</h3>
						<div class="wp-profile-box__content">
							<?php echo wpautop( $company->description ) ?>
						</div>
						<ul class="wp-profile-box__sns-accounts">
							<li class="wp-profile-box__sns-accounts-item wp-profile-box__sns-accounts-item--url">
								<a href="<?= esc_url( get_term_meta( $company->term_id, 'url', true ) ) ?>" target="_blank">
									<i class="fa fa-globe fa-w-16"></i>
									ウェブサイト
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<?php
		}
	} else {
		echo '<p>記載なし</p>';
	}
	?>
	<h3>この採用に応募する</h3>
	<?php if ( $submission = capitalp_get_submission() ) : ?>
		<table>
			<tbody>
			<tr>
				<th>採用責任者</th>
				<td><?= esc_html( tscfp( '_assignee' ) ) ?></td>
			</tr>
			<?php foreach ( [
								'_email' => 'メール',
								'_tel'   => '電話',
								'_url'   => 'URL',
							] as $key => $label ) {
				if ( ! ( $value = tscfp( $key ) ) ) {
					continue;
				}
				?>
				<tr>
					<th><?= $label ?></th>
					<td>
						<?php switch ( $key ) {
							case '_email':
								printf( '<a href="mailto:%1$s">%1$s</a>', esc_attr( $value ) );
								break;
							case '_tel':
								printf( '<a href="tel:%s">%s</a>', preg_replace( '#\D#u', '', $value ),  esc_html( $value ) );
								break;
							case '_url':
								printf( '<a href="%s" target="_blank">お申し込みはこちら</a>', esc_url( $value ) );
								break;
						} ?>
					</td>
				</tr>
				<?php
			} ?>
			<tr>
				<th>注意事項</th>
				<td>
					応募の際はCapital Pで求人情報を見たことを担当者にお伝えください。
					<strong>受付がスムースに進みます。</strong>
				</td>
			</tr>
			</tbody>
		</table>
	<?php elseif ( ! capitalp_job_is_open() ) : ?>
		<div class="wpac-alert wpac-alert--warning mceNonEditable">
			<div class="wpac-alert__body mceEditable">
				この求人は<?= mysql2date( get_option( 'date_format' ), tscfp( '_job_expires' ) ) ?>で募集を終了しています。
			</div>
		</div>
	<?php else : ?>
		<p>
			下のボタンより申し込みボタンを押すことで、応募申し込み連絡先を見ることができるようになります。
		</p>
		<a class="wpac-btn wpac-btn--full" href="#" id="capitalp-job-submit-button">
			<span class="mceNonEditable"> <span class="mceEditable">連絡先を見る</span></span>
		</a>
	<?php endif; ?>
<?php endif; ?>