/**
 * WordPress Dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

addFilter(
	'QuillSMTP.Mailers.MailerModuleSettings',
	'QuillSMTP/SendLayer/ImplementIntegrationModuleSettings',
	(settings, slug: string) => {
		if (slug === 'sendlayer') {
			settings.connectParameters = {
				main: {
					accounts: {
						auth: {
							type: 'credentials',
							fields: {
								api_key: {
									label: __('API Key', 'quillsmtp'),
									type: 'password',
									required: true,
								},
							},
						},
					},
				},
			};
		}
		return settings;
	}
);
