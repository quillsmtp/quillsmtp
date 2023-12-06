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
	'QuillSMTP/SMTP/ImplementIntegrationModuleSettings',
	(settings, slug: string) => {
		if (slug === 'smtp') {
			settings.render = Connect;
		}
		return settings;
	}
);
