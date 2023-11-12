<?php
/**
 * HTML email.
 *
 * @since 1.0.0
 * @package QuillSMTP
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

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

		.email-wrapper {
			width: 100%;
			max-width: 600px;
			margin: 0 auto;
			padding: 20px;
			background-color: #ffffff;
			border-radius: 10px;
			box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
		}

		.email-header {
			text-align: center;
			margin-bottom: 20px;
		}

		.email-header h1 {
			margin: 0;
			color: #333333;
			font-size: 28px;
			font-weight: bold;
		}

		.email-body {
			padding: 20px;
			border-bottom: 1px solid #ddd;
			margin-bottom: 20px;
		}

		.email-body p {
			margin: 0;
			color: #333333;
			font-size: 16px;
			line-height: 1.5;
		}

		.email-footer {
			text-align: center;
			margin-top: 20px;
		}

		.email-footer p {
			color: #777;
			font-size: 14px;
		}
	</style>
</head>

<body>
	<div class="email-wrapper">
		<div class="email-header">
			<h1><?php _e( '🎉 Congratulations! Your Test Email Was Sent Successfully! 🎉', 'quillsmtp' ); ?></h1>
		</div>
		<div class="email-body">
			<p><?php _e( "Thank you for choosing QuillSMTP, the best SMTP plugin for WordPress. You've just taken the first step towards ensuring reliable and secure email delivery for your WordPress site.", 'quillsmtp' ); ?></p>
			<br />
			<p><?php _e( 'Enjoy the power of seamless email delivery with QuillSMTP. Feel free to explore our advanced features and customize your email settings for an enhanced experience.', 'quillsmtp' ); ?></p>
		</div>
		<div class="email-footer">
			<p><?php _e( 'This is a test email sent by QuillSMTP. For any assistance or inquiries, visit our website or contact our support team.', 'quillsmtp' ); ?></p>
		</div>
	</div>
</body>

</html>

