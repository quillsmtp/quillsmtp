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
	'QuillSMTP/SendLayer/ImplementIntegrationModuleSettings',
	(settings, slug: string) => {
		if (slug === 'sendlayer') {
			settings.render = Connect;
		}
		return settings;
	}
);
