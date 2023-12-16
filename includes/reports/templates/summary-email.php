<?php
/**
 * Summary Email template.
 *
 * @since 1.0.0
 *
 * @package QuillSMTP
 */

defined( 'ABSPATH' ) || exit;

use QuillSMTP\Log_Handlers\Log_Handler_DB;
use QuillSMTP\Reports\Summary_Email;

$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

$start_date = date( 'Y-m-d H:i:s', strtotime( 'last Saturday' ) );
$end_date   = date( 'Y-m-d H:i:s' );

// Get the log.
$total_emails = Log_Handler_DB::get_count( false, $start_date, $end_date );
$succeeded    = Log_Handler_DB::get_count( array( 'info' ), $start_date, $end_date );
$failed       = Log_Handler_DB::get_count( array( 'error' ), $start_date, $end_date );

$last_weekend_start_date = date( 'Y-m-d H:i:s', strtotime( 'last Saturday - 7 days' ) );
$last_weekend_end_date   = date( 'Y-m-d H:i:s', strtotime( 'last Saturday - 1 day' ) );

$last_weekend_total_emails = Log_Handler_DB::get_count( false, $last_weekend_start_date, $last_weekend_end_date );
$last_weekend_succeeded    = Log_Handler_DB::get_count( array( 'info' ), $last_weekend_start_date, $last_weekend_end_date );
$last_weekend_failed       = Log_Handler_DB::get_count( array( 'error' ), $last_weekend_start_date, $last_weekend_end_date );

// Changes from last week plus or minus not percentage.
$total_emails_change = $total_emails > $last_weekend_total_emails ? '+' . ( $total_emails - $last_weekend_total_emails ) : ( $total_emails - $last_weekend_total_emails );
$succeeded_change    = $succeeded > $last_weekend_succeeded ? '+' . ( $succeeded - $last_weekend_succeeded ) : ( $succeeded - $last_weekend_succeeded );
$failed_change       = $failed > $last_weekend_failed ? '+' . ( $failed - $last_weekend_failed ) : ( $failed - $last_weekend_failed );

?>

<?php do_action( 'quillsmtp_before_summary_email' ); ?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width">
	<title><?php _e( 'QuillSMTP Test Email', 'quillsmtp' ); ?></title>
	<style type="text/css">
		body {
			margin: 0;
			padding: 0;
			font-family: 'Helvetica Neue', 'Arial', sans-serif;
			background-color: #f5f5f5;
		}

		#wrapper {
			width: 100%;
			max-width: 600px;
			margin: 0 auto;
			padding: 20px;
			background-color: #ffffff;
			border-radius: 10px;
			box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
		}

		#header {
			text-align: center;
			margin-bottom: 20px;
			color: #3498db;
		}

		#header h1 {
			font-size: 32px;
			margin: 0;
			font-weight: bold;
			color: #2c3e50;
		}

		#header p {
			font-size: 14px;
			color: #555555;
		}

		#body {
			color: #555555;
			text-align: center;
		}

		.box-container {
			display: flex;
			flex-wrap: wrap;
			margin-top: 20px;
		}

		.box {
			flex: 1;
			margin: 10px;
			padding: 20px;
			border-radius: 10px;
			box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
			background-color: #ffffff;
		}

		.total-box {
			border: 2px solid #3498db;
			color: #3498db;
		}

		.success-box {
			border: 2px solid #2ecc71;
			color: #2ecc71;
		}

		.fail-box {
			border: 2px solid #e74c3c;
			color: #e74c3c;
		}

		.box h2 {
			font-size: 18px;
			margin-bottom: 10px;
			color: #333333;
		}

		.box p {
			margin: 0;
			font-size: 14px;
			color: #555555;
		}

		.icon {
			font-size: 24px;
			margin-bottom: 5px;
			display: block;
		}

		#footer {
			text-align: center;
			margin-top: 20px;
			color: #777777;
		}

		#footer p {
			font-size: 14px;
			color: #555555;
		}
	</style>
</head>
<body>
	<div id="wrapper">
		<div id="header">
			<h1><?php echo esc_html__( 'QuillSMTP', 'quillsmtp' ) . ' ' . esc_html( $blogname ) . ' ' . esc_html__( 'Weekly Email Report', 'quillsmtp' ); ?></h1>
			<p><?php echo esc_html( 'Unlock insights into your email performance.', 'quillsmtp' ); ?></p>
		</div>
		<div id="body">
			<p><?php esc_html_e( 'Greetings! Here is your weekly summary of emails sent by QuillSMTP.', 'quillsmtp' ); ?></p>
			<div class="box-container">
				<div class="box total-box">
					<h2><span class="icon">✉️</span><?php esc_html_e( 'Total Emails', 'quillsmtp' ); ?></h2>
					<p><?php echo esc_html( $total_emails ); ?></p>
					<p><?php echo '(' . esc_html( $total_emails_change ) . ')'; ?></p>
				</div>
				<div class="box success-box">
					<h2><span class="icon">✅</span><?php esc_html_e( 'Succeeded', 'quillsmtp' ); ?></h2>
					<p><?php echo esc_html( $succeeded ); ?></p>
					<p><?php echo '(' . esc_html( $succeeded_change ) . ')'; ?></p>
				</div>
				<div class="box fail-box">
					<h2><span class="icon">❌</span><?php esc_html_e( 'Failed', 'quillsmtp' ); ?></h2>
					<p><?php echo esc_html( $failed ); ?></p>
					<p><?php echo '(' . esc_html( $failed_change ) . ')'; ?></p>
				</div>
			</div>
		</div>
		<div id="footer">
			<p><?php esc_html_e( 'Stay informed with QuillSMTP - Your email delivery companion.', 'quillsmtp' ); ?></p>
		</div>
	</div>
</body>
</html>

<?php do_action( 'quillsmtp_after_summary_email' ); ?>
