/**
 * WordPress Dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

addFilter(
	'QuillSMTP.Mailers.MailerModuleSettings',
	'QuillSMTP/SMTPcom/ImplementIntegrationModuleSettings',
	(settings, slug: string) => {
		if (slug === 'smtpcom') {
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
								sender_name: {
									label: __('Sender Name', 'quillsmtp'),
									type: 'text',
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
