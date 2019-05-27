/**
 * Login link generator.
 */

const { render, Component } = wp.element;
const { __, sprintf } = wp.i18n;

class LoginButton extends Component {

  constructor( props ) {
    super( props );
    this.state = {
      loggedin: CookieTasting.isLoggedIn(),
      loading: false,
    };
    this.loginHandler = this.loginHandler.bind( this );
  }

  loginHandler( e ) {
    const url = e.target.href;
    e.preventDefault();
    this.setState( {
      loading: true,
    }, () => {
      CookieTasting.testBefore().then(res => {
        // Logged in. Do nothing.
      }).catch(res => {
        window.location.href = url;
      }).finally(res => {
        this.setState({loading: false});
      });
    });
  }

  render() {
    let path = encodeURIComponent( document.location.pathname );
    const name = CookieTasting.userName().replace( '+', ' ' );
    let linkClass = [ 'capitalp-login-label' ];
    if ( this.state.loading ) {
      linkClass.push( 'capitalp-login-loading' );
    }
    return this.state.loggedin ? (
      <a className={ linkClass.join( ' ' ) } href='/wp-admin/'>
        <img src={CookieTasting.get('avatar')} className='capitalp-login-avatar' alt={name}/>
        {sprintf(__('Howday, %s!', 'capitalp'), name)}
      </a>
    ) : (
        <a className={ linkClass.join( ' ' ) } href={`/wp-login.php?redirect_to=${path}`}
          rel='nofollow,noopener' onClick={this.loginHandler}>
          { __( 'Login', 'capitalp' ) }
        </a>
    );
  }

}

document.querySelectorAll( '.capitalp-login-link' ).forEach( ( link ) => {
  render( <LoginButton/>, link );
} );
