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
	'QuillSMTP/SendInBlue/ImplementIntegrationModuleSettings',
	(settings, slug: string) => {
		if (slug === 'sendinblue') {
			settings.render = Connect;
		}
		return settings;
	}
);
