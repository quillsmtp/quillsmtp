/**
 * WordPress Dependencies
 */
import { addFilter } from '@wordpress/hooks';

/**
 * Internal Dependencies
 */
import Connect from '../components/connect';

addFilter(
	'QuillSMTP.FormMailers.MailerModuleSettings',
	'QuillSMTP/SendInBlue/ImplementIntegrationModuleSettings',
	(settings, slug: string) => {
		console.log('settings', settings);

		if (slug === 'sendinblue') {
			settings.render = Connect;
			settings.settingsRender = () => null;
		}
		return settings;
	}
);
