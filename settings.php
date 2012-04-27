<?php
namespace Inpsyde\MultisiteFeed\Settings;

/**
 * Convenience wrapper to access plugin options.
 * 
 * @param  string $name    option name
 * @param  mixed  $default fallback value if option does not exist
 * @return mixed
 */
function get_site_option( $name, $default = NULL ) {
	
	$options = \get_site_option( 'inpsyde_multisitefeed' );

	return ( isset( $options[ $name ] ) ) ? $options[ $name ] : $default;
}

/**
 * Settings Page Class
 * 
 * @authors et, fb
 * @since   2.0.0  03/26/2012
 */
class Inpsyde_Settings_Page {
	
	private $page_hook;
	
	public function __construct() {
		add_action( 'network_admin_menu', array( $this, 'init_menu' ) );
		add_action( 'network_admin_menu', array( $this, 'save' ) );
	}
	
	public function init_menu() {
		
		$this->page_hook = add_submenu_page(
			/* $parent_slug*/ 'settings.php',
			/* $page_title */ 'Multisite Feed',
			/* $menu_title */ 'Multisite Feed',
			/* $capability */ 'manage_users',
			/* $menu_slug  */ 'inpsyde-multisite-feed-page',
			/* $function   */ array( $this, 'page' )
		);
	}

	public function save() {
		
		if ( ! isset( $_POST[ 'action' ] ) || $_POST[ 'action' ] != 'update' )
			return;

		if ( ! wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'inpsmf-options') )
			wp_die( 'Sorry, you failed the nonce test.' ); 

		update_site_option( 'inpsyde_multisitefeed', $_REQUEST[ 'inpsyde_multisitefeed' ] );

		if ( isset( $_REQUEST[ '_wp_http_referer' ] ) )
			wp_redirect( $_REQUEST[ '_wp_http_referer' ] );
	}
	
	/**
	 * Get settings pages incl. markup
	 * 
	 * @authors et, fb
	 * @since   2.0.0  03/26/2012
	 * @return  void
	 */
	public function page() {
		?>
		<div class="wrap">

			<?php screen_icon( 'options-general' ); ?>
			<h2><?php _e( 'Multisite Feed', 'inps-multisite-feed' ); ?></h2>
			
			<form method="post" action="#">

				<?php 
				echo '<input type="hidden" name="action" value="update" />';
				wp_nonce_field( "inpsmf-options" );
				?>

				<table class="form-table">
				    <tbody>
				        <tr valign="top">
				            <th scope="row">
				            	<label for="inpsmf_title"><?php _e( 'Feed Title.', 'inps-multisite-feed' ) ?></label>
				            </th>
				            <td>
				            	<input class="regular-text" type="text" value="<?php echo get_site_option( 'title', '' ); ?>" name="inpsyde_multisitefeed[title]" id="inpsmf_title">
				        	</td>
				        </tr>
				        <tr valign="top">
				            <th scope="row">
				            	<label for="inpsmf_description"><?php _e( 'Feed Description.', 'inps-multisite-feed' ) ?></label>
				            </th>
				            <td>
				            	<textarea name="inpsyde_multisitefeed[description]" id="inpsmf_description" cols="40" rows="7"><?php echo get_site_option( 'description', '' ); ?></textarea>
				        	</td>
				        </tr>
				        <tr valign="top">
				            <th scope="row">
				            	<label for="inpsmf_url_slug"><?php _e( 'Feed Url.', 'inps-multisite-feed' ) ?></label>
				            </th>
				            <td>
				            	<input class="regular-text" type="text" value="<?php echo get_site_option( 'url_slug', '' ); ?>" name="inpsyde_multisitefeed[url_slug]" id="inpsmf_url_slug">
				        	</td>
				        </tr>
				        <tr valign="top">
				        	<th></th>
				        	<td>
				        		<?php 
				        		echo \Inpsyde\MultisiteFeed\get_feed_url();
				        		?>
				        	</td>
				        </tr>
				    </tbody>
				</table>				
				<?php submit_button( __( 'Save Changes' ), 'button-primary', 'submit', TRUE ); ?>
			</form>
			
		</div>
		<?php
	}
	
}