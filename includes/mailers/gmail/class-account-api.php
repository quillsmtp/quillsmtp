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
			try {
				$client->fetchAccessTokenWithRefreshToken( $this->refresh_token );
				$this->access_token = $client->getAccessToken();
			} catch ( \Exception $e ) {
				return new WP_Error( 'refresh_token_error', $e->getMessage() );
			}
		}

		return $client;
	}

	/**
	 * Get user profile
	 *
	 * @return array|WP_Error
	 */
	public function get_profile() {
		$client = $this->get_client();
		if ( is_wp_error( $client ) ) {
			return $client;
		}

		$service = new Gmail( $client );
		$profile = $service->users->getProfile( 'me' );

		return $profile;
	}

	/**
	 * Send email
	 *
	 * @param array  $args Email arguments.
	 * @param string $content_type Content type.
	 *
	 * @return WP_Error|array
	 */
	public function send( $args, $content_type = '' ) {
		$response = wp_remote_request(
			'https://api.gmail.net/v3/qsmtp.publicvm.com/messages',
			[
				'method'  => 'POST',
				'headers' => [
					'Authorization' => 'Basic ' . base64_encode( 'api:' . $this->api_key ),
					'Content-Type'  => $content_type,
				],
				'body'    => $args,
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );

		if ( empty( $body ) ) {
			return new WP_Error( 'empty_response', __( 'Empty response.', 'quillsmtp' ) );
		}

		$body = json_decode( $body, true );

		if ( ! is_array( $body ) ) {
			return new WP_Error( 'invalid_response', __( 'Invalid response.', 'quillsmtp' ) );
		}

		if ( ! empty( $body['error'] ) ) {
			return new WP_Error( 'send_error', $body['error'] );
		}

		return $body;
	}
}
