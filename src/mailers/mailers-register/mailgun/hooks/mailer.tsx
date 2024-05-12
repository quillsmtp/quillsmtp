/**
 * WordPress Dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

addFilter(
	'QuillSMTP.Mailers.MailerModuleSettings',
	'QuillSMTP/Mailgun/ImplementIntegrationModuleSettings',
	(settings, slug: string) => {
		if (slug === 'mailgun') {
			settings.connectParameters = {
				main: {
					accounts: {
						auth: {
							type: 'credentials',
							fields: {
								api_key: {
									label: __('Private API Key', 'quillsmtp'),
									type: 'password',
									required: true,
									help: () => (
										<p>
											{__(
												'Follow this link to get your API key:',
												'quillsmtp'
											)}{' '}
											<a
												href="https://app.mailgun.com/app/account/security/api_keys"
												target="_blank"
												rel="noreferrer"
											>
												{__(
													'Get Mailgun API Key',
													'quillsmtp'
												)}
											</a>
										</p>
									),
								},
								domain_name: {
									label: __('Domain Name', 'quillsmtp'),
									type: 'text',
									required: true,
									help: () => (
										<p>
											{__(
												'Follow this link to get your Domain Name:',
												'quillsmtp'
											)}{' '}
											<a
												href="https://app.mailgun.com/app/domains"
												target="_blank"
												rel="noreferrer"
											>
												{__(
													'Get Mailgun Domain Name',
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
