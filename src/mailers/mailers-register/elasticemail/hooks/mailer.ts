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
	'QuillSMTP/ElasticEmail/ImplementIntegrationModuleSettings',
	(settings, slug: string) => {
		if (slug === 'elasticemail') {
			settings.render = Connect;
		}
		return settings;
	}
);
