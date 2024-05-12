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
									help: () => (
										<p>
											{__(
												'Follow this link to get your API key:',
												'quillsmtp'
											)}{' '}
											<a
												href="https://my.smtp.com/settings/api"
												target="_blank"
												rel="noreferrer"
											>
												{__(
													'Get SMTP.com API Key',
													'quillsmtp'
												)}
											</a>
										</p>
									),
								},
								sender_name: {
									label: __('Sender Name', 'quillsmtp'),
									type: 'text',
									required: true,
									help: () => (
										<p>
											{__(
												'Follow this link to get your Sender Name:',
												'quillsmtp'
											)}{' '}
											<a
												href="https://my.smtp.com/senders/"
												target="_blank"
												rel="noreferrer"
											>
												{__(
													'Get SMTP.com Sender Name',
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
