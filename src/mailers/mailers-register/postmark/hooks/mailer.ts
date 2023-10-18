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
	'QuillSMTP/PostMark/ImplementIntegrationModuleSettings',
	(settings, slug: string) => {
		if (slug === 'postmark') {
			settings.render = Connect;
		}
		return settings;
	}
);
