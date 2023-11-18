/**
 * WordPress Dependencies
 */
import { addFilter } from '@wordpress/hooks';

/**
 * Internal Dependencies
 */
import Connect from '../components/connect';

addFilter(
	'QuillSMTP.Mailers.MailerModuleSettings',
	'QuillSMTP/SMTPcom/ImplementIntegrationModuleSettings',
	(settings, slug: string) => {
		if (slug === 'smtpcom') {
			settings.render = Connect;
		}
		return settings;
	}
);
