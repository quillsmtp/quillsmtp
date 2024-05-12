/**
 * WordPress Dependencies
 */
import { addFilter } from '@wordpress/hooks';

addFilter(
	'QuillSMTP.Mailers.MailerModuleSettings',
	'QuillSMTP/PHPMailer/ImplementIntegrationModuleSettings',
	(settings, slug: string) => {
		if (slug === 'phpmailer') {
			settings.connectParameters = null;
		}
		return settings;
	}
);
