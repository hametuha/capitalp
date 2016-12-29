<?php
/**
 * Analytics related functions
 *
 * @package capitalp
 */

/**
 * Display analytics code
 */
add_action( 'wp_head', function() {
	?>
	<script>
	window.capitalP = {
	    post: 0,
        author: 0,
        tags: '',
		setDimension: function ( slot, value){
			try {
				ga( 'set', 'dimension' + slot, value );
			} catch ( err ) {
			}
		},
		setPostData: function ( postId, authorId, tags){
		  this.post = postId;
		  this.author = authorId;
		  this.tags = tags;
          this.setDimension(1, postId);
          this.setDimension(2, authorId);
          if ( tags ) {
            this.setDimension(3, tags);
          }
		},
        resetPostData: function(){
		  if ( this.post && this.author ) {
            this.setDimension(1, this.post);
            this.setDimension(2, this.author);
		    if ( this.tags ) {
              this.setDimension(3, this.tags);
            }
          }
        }
      };
    <?php if ( ! is_preview() ) : ?>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
	<?php
	if ( is_singular( 'post' ) ) :
    	$tags = get_the_tags( get_queried_object_id() );
    	$tags = ( $tags && ! is_wp_error( $tags ) ) ? implode( ',', array_map( function ( $tag ) {
		return $tag->term_id;
	}, $tags ) ) : '';
	?>
        capitalP.setPostData( <?= get_queried_object()->ID ?>, <?= get_queried_object()->post_author ?>, '<?= $tags ?>' );
        <?php endif; ?>
      ga('create', 'UA-1766751-12', 'auto');
      ga('send', 'pageview');
	</script>
	<?php endif;
}, 99 );


