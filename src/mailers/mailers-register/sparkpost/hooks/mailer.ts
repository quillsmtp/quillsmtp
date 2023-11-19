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
	'QuillSMTP/SparkPost/ImplementIntegrationModuleSettings',
	(settings, slug: string) => {
		if (slug === 'sparkpost') {
			settings.render = Connect;
		}
		return settings;
	}
);
