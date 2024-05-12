/**
 * WordPress Dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

addFilter(
	'QuillSMTP.Mailers.MailerModuleSettings',
	'QuillSMTP/SparkPost/ImplementIntegrationModuleSettings',
	(settings, slug: string) => {
		if (slug === 'sparkpost') {
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
