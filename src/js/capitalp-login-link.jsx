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
			CookieTasting.testBefore().then( res => {
				// Logged in. Do nothing.
				this.setState( {
					loggedin: CookieTasting.isLoggedIn(),
				} );
			} ).catch( res => {
				window.location.href = url;
			} ).finally( res => {
				this.setState( { loading: false } );
			} );
		} );
	}

	render() {
		const path = encodeURIComponent( document.location.pathname );
		const name = CookieTasting.userName().replace( '+', ' ' );
		const linkClass = [ 'header-account-link' ];
		if ( this.state.loading ) {
			linkClass.push( 'header-account-loading' );
		}
		return this.state.loggedin ? (
			<a className={ linkClass.join( ' ' ) } href='/wp-admin/'>
				<img src={ CookieTasting.get( 'avatar' ) } className='header-account-img' alt={ name } />
				<span className="header-account-label">{ sprintf( __( 'Howdy, %s!', 'capitalp' ), name ) }</span>
			</a>
		) : (
			<a className={ linkClass.join( ' ' ) } href={ `/wp-login.php?redirect_to=${ path }` }
				rel='nofollow,noopener' onClick={ this.loginHandler }>
				<span className="header-account-label-login">
					<i className="fas fa-sign-in-alt" /> { __( 'Login', 'capitalp' ) }
				</span>
			</a>
		);
	}
}

document.querySelectorAll( '.capitalp-login-link' ).forEach( ( link ) => {
	render( <LoginButton />, link );
} );
