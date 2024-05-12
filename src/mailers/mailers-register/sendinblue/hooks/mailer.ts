/**
 * WordPress Dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

addFilter(
	'QuillSMTP.Mailers.MailerModuleSettings',
	'QuillSMTP/SendInBlue/ImplementIntegrationModuleSettings',
	(settings, slug: string) => {
		if (slug === 'sendinblue') {
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
								sending_domain: {
									label: __('Sending Domain', 'quillsmtp'),
									type: 'text',
									required: false,
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
