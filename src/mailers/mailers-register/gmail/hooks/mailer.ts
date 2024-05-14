/**
 * WordPress Dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

addFilter(
	'QuillSMTP.Mailers.MailerModuleSettings',
	'QuillSMTP/Gmail/ImplementIntegrationModuleSettings',
	(settings, slug: string) => {
		if (slug === 'gmail') {
			settings.connectParameters = {
				main: {
					accounts: {
						auth: {
							type: 'oauth',
						},
					},
				},
				setup: {
					Instructions: () => null,
					fields: {
						client_id: {
							label: __('Client ID', 'quillsmtp'),
							type: 'text',
							check: true,
						},
						client_secret: {
							label: __('Client Secret', 'quillsmtp'),
							type: 'password',
							check: false,
						},
					},
				},
			};
		}
		return settings;
	}
);
