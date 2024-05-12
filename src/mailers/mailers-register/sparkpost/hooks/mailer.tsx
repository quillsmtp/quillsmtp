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
									help: () => (
										<p>
											{__(
												'Follow this link to get your API key:',
												'quillsmtp'
											)}{' '}
											<a
												href="https://app.sparkpost.com/account/api-keys"
												target="_blank"
												rel="noreferrer"
											>
												{__(
													'Get SparkPost API Key',
													'quillsmtp'
												)}
											</a>
										</p>
									),
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
									help: () => (
										<p>
											{__(
												'Choose the region where your SparkPost account is located.',
												'quillsmtp'
											)}{' '}
											<a
												href="https://www.sparkpost.com/docs/getting-started/getting-started-sparkpost"
												target="_blank"
												rel="noreferrer"
											>
												{__('Learn more', 'quillsmtp')}
											</a>
										</p>
									),
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
