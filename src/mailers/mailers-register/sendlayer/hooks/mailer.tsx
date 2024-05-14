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
									help: () => (
										<p>
											{__(
												'Follow this link to get your API key:',
												'quillsmtp'
											)}{' '}
											<a
												href="https://app.sendlayer.com/settings/api"
												target="_blank"
												rel="noreferrer"
											>
												{__(
													'Get SendLayer API Key',
													'quillsmtp'
												)}
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
