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
	'QuillSMTP/Mailgun/ImplementIntegrationModuleSettings',
	(settings, slug: string) => {
		if (slug === 'mailgun') {
			settings.render = Connect;
		}
		return settings;
	}
);
