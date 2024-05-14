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
									help: () => (
										<p>
											{__(
												'Follow this link to get your API key:',
												'quillsmtp'
											)}{' '}
											<a
												href="https://app.brevo.com/settings/keys/api"
												target="_blank"
												rel="noreferrer"
											>
												{__(
													'Get SendInBlue API Key',
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
