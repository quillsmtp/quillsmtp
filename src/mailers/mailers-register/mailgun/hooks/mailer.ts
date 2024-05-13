/**
 * WordPress Dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

/**
 * Internal Dependencies
 */
import Connect from '../components/connect';

addFilter(
	'QuillSMTP.Mailers.MailerModuleSettings',
	'QuillSMTP/Mailgun/ImplementIntegrationModuleSettings',
	(settings, slug: string) => {
		if (slug === 'mailgun') {
			settings.connectionSettings =
			{
				main: {
					accounts: {
						auth: {
							type: 'credentials',
							fields: {
								api_key: {
									label: __('Private API Key', 'quillsmtp'),
									type: 'password',
									required: true,
								},
								domain_name: {
									label: __('Domain Name', 'quillsmtp'),
									type: 'text',
									required: true,
								},
								region: {
									label: __('Region', 'quillsmtp'),
									type: 'select',
									options: [
										{
											label: __('US', 'quillsmtp'),
											value: 'us',
										},
										{
											label: __('EU', 'quillsmtp'),
											value: 'eu',
										},
									],
								},
							},
						},
					},
				}
			};
		}
		return settings;
	}
);
