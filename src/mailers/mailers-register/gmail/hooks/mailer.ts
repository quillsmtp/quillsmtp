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
	'QuillSMTP/Gmail/ImplementIntegrationModuleSettings',
	(settings, slug: string) => {
		if (slug === 'gmail') {
			settings.render = Connect;
		}
		return settings;
	}
);
