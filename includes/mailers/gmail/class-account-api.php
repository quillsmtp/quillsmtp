<?php
/**
 * Account_API class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 * @subpackage mailers
 */

namespace QuillSMTP\Mailers\Gmail;

defined( 'ABSPATH' ) || exit;

use QuillSMTP\Vendor\Google\Client as Google_Client;
use QuillSMTP\Vendor\Google\Service\Gmail;

use WP_Error;

/**
 * Account_API class.
 *
 * @since 1.0.0
 */
class Account_API {

	/**
	 * Provider
	 *
	 * @var App
	 */
	private $app;

	/**
	 * Access token
	 *
	 * @var string
	 */
	private $access_token;

	/**
	 * Refresh token
	 *
	 * @var string
	 */
	private $refresh_token;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param App    $app Provider.
	 * @param string $account_id Account id.
	 * @param array  $account_data Account data.
	 */
	public function __construct( $app, $account_id, $account_data ) {
		$this->app           = $app;
		$this->access_token  = $account_data['credentials']['access_token'];
		$this->refresh_token = $account_data['credentials']['refresh_token'];
	}

	/**
	 * Get client
	 *
	 * @return Google_Client
	 */
	public function get_client() {
		try {
			$app_credentials = $this->app->get_app_credentials();
			$client          = new Google_Client();
			$client->setApplicationName( 'QuillSMTP' );
			$client->setClientId( $app_credentials['client_id'] );
			$client->setClientSecret( $app_credentials['client_secret'] );
			$client->setAccessToken( $this->access_token );
			$client->setAccessType( 'offline' );
			$client->setApprovalPrompt( 'force' );
			$client->setRedirectUri( $this->app->get_redirect_uri() );
			$client->setScopes( [ Gmail::MAIL_GOOGLE_COM, Gmail::GMAIL_SEND ] );

			// Refresh token if expired.
			if ( $client->isAccessTokenExpired() ) {
				$refresh_token = $client->fetchAccessTokenWithRefreshToken( $this->refresh_token );
				if ( isset( $refresh_token['error_description'] ) ) {
					throw new \Exception( $refresh_token['error_description'] );
				}
				$this->access_token = $client->getAccessToken();
			}

			if ( ! $client->getAccessToken() ) {
				throw new \Exception( esc_html__( 'Unable to get access token', 'quillsmtp' ) );
			}

			return $client;
		} catch ( \Exception $e ) {
			return new WP_Error( 'get_client_error', $e->getMessage() );
		}
	}

	/**
	 * Get user profile
	 *
	 * @return object|WP_Error
	 */
	public function get_profile() {
		try {
			$client = $this->get_client();
			if ( is_wp_error( $client ) ) {
				throw new \Exception( $client->get_error_message() );
			}
			$gmail = new Gmail( $client );

			return $gmail->users->getProfile( 'me' );
		} catch ( \Exception $e ) {
			return new WP_Error( 'get_profile_error', $e->getMessage() );
		}
	}
}
