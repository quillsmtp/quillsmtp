/**
 * WordPress Dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

addFilter(
	'QuillSMTP.Mailers.MailerModuleSettings',
	'QuillSMTP/SendGrid/ImplementIntegrationModuleSettings',
	(settings, slug: string) => {
		if (slug === 'sendgrid') {
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
												href="https://app.sendgrid.com/settings/api_keys"
												target="_blank"
												rel="noreferrer"
											>
												{__(
													'Get SendGrid API Key',
													'quillsmtp'
												)}
											</a>
										</p>
									),
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
