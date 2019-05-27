/**
 *
 */

const $ = jQuery;
const { __, sprintf } = wp.i18n;

/* global CapitalpContents: false */


function retriveContents() {
  let $container = $( '.ofuse-instruction');
  $container.addClass( 'loading' );
  CookieTasting.testBefore().then( res => {
    return wp.apiFetch({
      path: `/chiramise/v1/content/${CapitalpContents.postId}`,
      method: 'POST',
    });
  }).then(res => {
    $('.gumroad-product-embed, .gumroad-embed-iframe').remove();
    const thankYou = __( 'Thank you for supporting us!', 'capitalp' );
    const $message = $( `
      <div class="ofuse-more">
        <i></i> ${thankYou}
      </div>
    ` );
    const instruction = `
      <div class="ofuse-success">
          <p>` + sprintf( __( 'Howdy, %s!', 'capitalp' ), CookieTasting.userName().replace( '+', ' ' ) ) + `</p>
          <ol>
            <li>` + sprintf( __( 'To get premium files, please go to %s and login. Then you can find Capital P resources in your library.', 'capitalp' ), '<a class="ofuse-link" href="https://gumroad.com/library" target="_blank">Gumroad</a>') + `</li>
            <li>` + sprintf( __( 'You can manage and check your license at <a href="%s" class="ofuse-link">profile page</a>.', 'capitalp' ), '/wp-admin/profile.php' ) + `</li>
          </ol>
      </div> 
    `;
    $container.before( $message ).after( instruction ).replaceWith( res.content );
    $message.nextAll( '.EnlighterJSRAW' ).each( function( index, pre ) {
      pre.enlight();
    } );
  }).catch(res => {
    // User is not customer.
    let message = `
        <div class="u-text-center">
          <p class="ofuse-instruction-text u-text-center">%s</p>
          <p>%s</p>
          <a class="ofuse-button" href="/wp-admin/profile.php">%s</a>
        </div>
    `;
    message = sprintf(
      message,
      __( 'License Required!', 'capitalp' ),
      __( 'To read full contents, buy new license at gumroad and save it on your profile page.', 'capitalp' ),
      __( 'Save License at Profile Page', 'capitalp' )
    );
    $container.removeClass( 'loading' ).html( message );
  }).finally(res => {
    // Do something.
    $( '.p-entry-content' ).effect( 'highlight' );
  });
}

// On DOM READY.
$( document ).ready( function() {
  // If logged in, try to retrieve contents.
  if ( CookieTasting.isLoggedIn() ) {
    retriveContents();
  }
  // Else, check if user clicke button.
  $( '.ofuse-login .ofuse-button' ).click( function( e ) {
    e.preventDefault();
    let url = $( this ).attr( 'href' );
    CookieTasting.testBefore().then( res => {
      // User is logged in.
      // Try to retrieve contents.
      retriveContents();
    } ).catch( res => {
      // User is not logged in.
      window.location.href = url;
    } ).finally( res => {
      // Do nothing.
    });
  } );
} );

// On clicking login button

