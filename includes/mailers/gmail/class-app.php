<?php
/**
 * App class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 */

namespace QuillSMTP\Mailers\Gmail;

use QuillSMTP\Vendor\Google\Service\Gmail;

/**
 * App class.
 *
 * @since 1.0.0
 */
class App {

	/**
	 * Provider
	 *
	 * @var Gmail
	 */
	protected $provider;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Gmail $provider Provider.
	 */
	public function __construct( $provider ) {
		$this->provider = $provider;

		add_action( 'admin_init', array( $this, 'maybe_authorize' ) );
		add_action( 'admin_init', array( $this, 'maybe_add_account' ) );
	}

	/**
	 * Redirect the user to authorization page
	 *
	 * @return void
	 */
	public function maybe_authorize() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce not required for checking action parameter
		$action = isset( $_GET['quillsmtp-gmail'] ) ? sanitize_text_field( wp_unslash( $_GET['quillsmtp-gmail'] ) ) : '';
		if ( $action !== 'authorize' ) {
			return;
		}

		$account_id = esc_attr( $_GET['account_id'] ?? '' );
		if ( empty( $account_id ) ) {
			// Fallback to global app credentials for backward compatibility.
			$app_credentials = $this->get_app_credentials();
		} else {
			// Get account-specific credentials.
			$app_credentials = $this->get_account_app_credentials( $account_id );
		}

		if ( empty( $app_credentials ) ) {
			echo esc_html__( 'Cannot find app credentials!', 'quillsmtp' );
			exit;
		}

		// Pass account_id through OAuth state so we know which account to update on callback.
		$state = 'quillsmtp-gmail';
		if ( ! empty( $account_id ) ) {
			$state .= ':' . $account_id;
		}

		$auth_url = add_query_arg(
			[
				'response_type' => 'code',
				'access_type'   => 'offline',
				'client_id'     => $app_credentials['client_id'],
				'redirect_uri'  => urlencode( $this->get_redirect_uri() ),
				'state'         => $state,
				'scope'         => Gmail::MAIL_GOOGLE_COM . ' ' . Gmail::GMAIL_SEND,
				'prompt'        => 'consent',
			],
			'https://accounts.google.com/o/oauth2/auth'
		);
		wp_redirect( $auth_url );
		exit;
	}

	/**
	 * Add account after authorization
	 *
	 * @return void
	 */
	public function maybe_add_account() {
<<<<<<< HEAD
		$state = esc_attr( $_GET['state'] ?? '' );
		if ( strpos( $state, 'quillsmtp-gmail' ) !== 0 ) {
=======
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- OAuth callback, nonce not applicable
		$state = isset( $_GET['state'] ) ? sanitize_text_field( wp_unslash( $_GET['state'] ) ) : '';
		if ( $state !== 'quillsmtp-gmail' ) {
>>>>>>> 4fe8502 (Update QuillSMTP plugin to version 1.8.0)
			return;
		}

		// Extract account_id from state if present.
		$state_parts = explode( ':', $state );
		$account_id_from_state = isset( $state_parts[1] ) ? $state_parts[1] : '';

		// ensure authorize code.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- OAuth callback, nonce not applicable
		$code = isset( $_GET['code'] ) ? sanitize_text_field( wp_unslash( $_GET['code'] ) ) : '';
		if ( empty( $code ) ) {
			echo esc_html__( 'Error, There is no authorize code passed!', 'quillsmtp' );
			exit;
		}

		// Get credentials based on whether we have an account_id.
		if ( ! empty( $account_id_from_state ) ) {
			$app_credentials = $this->get_account_app_credentials( $account_id_from_state );
		} else {
			$app_credentials = $this->get_app_credentials();
		}

		if ( empty( $app_credentials ) ) {
			echo esc_html__( 'Cannot find app credentials!', 'quillsmtp' );
			exit;
		}

		// get account tokens.
		$tokens = $this->get_tokens(
			[
				'grant_type'    => 'authorization_code',
				'code'          => $code,
				'client_id'     => $app_credentials['client_id'],
				'client_secret' => $app_credentials['client_secret'],
				'redirect_uri'  => $this->get_redirect_uri(),
			]
		);

		if ( empty( $tokens ) ) {
			echo esc_html__( 'Error, Cannot get account tokens!', 'quillsmtp' );
			exit;
		}

		// get account details.
		$account_api       = new Account_API( $this, '', [ 'credentials' => array_merge( $app_credentials, $tokens ) ] );
		$accounts_response = $account_api->get_profile();

		if ( is_wp_error( $accounts_response ) ) {
			quillsmtp_get_logger()->error(
				'Cannot get profile details',
				[
					'code'  => 'cannot_get_profile',
					'error' => $accounts_response,
				]
			);
			echo esc_html__( 'Error, Cannot get profile details!', 'quillsmtp' );
			exit;
		}

		$account      = $accounts_response;
		$account_name = $account->emailAddress;

		// If we have an account_id from state, use it. Otherwise generate from email.
		if ( ! empty( $account_id_from_state ) ) {
			$account_id = $account_id_from_state;
		} else {
			$account_id = str_replace( '@gmail.com', '', $account->emailAddress );
		}

		// account data for adding or updating - include client_id and client_secret in credentials.
		$account_data = [
			'name'        => $account_name,
			'credentials' => array_merge( $app_credentials, $tokens ),
		];

		// check account existence.
		if ( in_array( $account_id, array_keys( $this->provider->accounts->get_accounts() ), true ) ) {
			$result = $this->provider->accounts->update_account( $account_id, $account_data );
			if ( empty( $result ) || is_wp_error( $result ) ) {
				echo esc_html__( 'Error, Cannot update the account!', 'quillsmtp' );
				exit;
			}
		} else {
			$result = $this->provider->accounts->add_account( $account_id, $account_data );
			if ( empty( $result ) || is_wp_error( $result ) ) {
				echo esc_html__( 'Error, Cannot add the new account!', 'quillsmtp' );
				exit;
			}
		}

		// sucessfully added.
		?>
		<!DOCTYPE html>
		<html lang="en">
		<head>
			<meta charset="UTF-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<title>Authorization done</title>
		</head>
		<body>
			<?php echo esc_html__( "The account is added/updated successfully. If this window isn't closed automatically. Please close it and refersh your accounts select menu.", 'quillsmtp' ); ?>
			<script>
				if ( typeof window.opener.add_new_gmail_account === 'function' ) {
					window.opener.add_new_gmail_account( '<?php echo  esc_attr( $account_id ); ?>', '<?php echo esc_attr( $account_name ); ?>' );
					window.close();
				}
			</script>
		</body>
		</html>
		<?php
		exit;
	}

	/**
	 * Get tokens
	 *
	 * @param array $query Query to get account tokens.
	 * @return boolean|array
	 */
	public function get_tokens( $query ) {
		$response = wp_remote_post(
			'https://accounts.google.com/o/oauth2/token',
			[
				'body' => $query,
			]
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$tokens = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( empty( $tokens['access_token'] ) ) {

			// log in case of first request.
			if ( $query['grant_type'] === 'authorization_code' && empty( $tokens['refresh_token'] ) ) {
				return false;
			}

			return false;
		}

		return $tokens;
	}

	/**
	 * Get global app credentials
	 *
	 * @return array|false Array of client_id & client_secret. false on failure.
	 */
	public function get_app_credentials() {
		$app_settings = $this->provider->settings->get( 'app' ) ?? [];
		if ( empty( $app_settings['client_id'] ) || empty( $app_settings['client_secret'] ) ) {
			return false;
		} else {
			return $app_settings;
		}
	}

	/**
	 * Get account-specific app credentials (client_id & client_secret stored in account credentials)
	 *
	 * @param string $account_id Account id.
	 * @return array|false Array of client_id & client_secret. false on failure.
	 */
	public function get_account_app_credentials( $account_id ) {
		$account_data = $this->provider->accounts->get_account_data( $account_id );
		if ( empty( $account_data ) ) {
			return false;
		}
		$credentials = $account_data['credentials'] ?? [];
		if ( empty( $credentials['client_id'] ) || empty( $credentials['client_secret'] ) ) {
			return false;
		}
		return [
			'client_id'     => $credentials['client_id'],
			'client_secret' => $credentials['client_secret'],
		];
	}

	/**
	 * Get redirect uri
	 *
	 * @return string
	 */
	public function get_redirect_uri() {
		return admin_url( 'admin.php' ); // TODO: use https schema?
	}

}
