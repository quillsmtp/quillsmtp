/**
 * WordPress Dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

addFilter(
	'QuillSMTP.Mailers.MailerModuleSettings',
	'QuillSMTP/Mailjet/ImplementIntegrationModuleSettings',
	(settings, slug: string) => {
		if (slug === 'mailjet') {
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
												'Follow these instructions to get your API Key:',
												'quillsmtp'
											)}{' '}
											<a
												href="https://app.mailjet.com/account/apikeys"
												target="_blank"
												rel="noreferrer"
											>
												{__('Get API Key', 'quillsmtp')}
											</a>
										</p>
									),
								},
								secret_key: {
									label: __('Secret Key', 'quillsmtp'),
									type: 'password',
									required: true,
									help: () => (
										<p>
											{__(
												'Follow these instructions to get your Secret Key:',
												'quillsmtp'
											)}{' '}
											<a
												href="https://app.mailjet.com/account/apikeys"
												target="_blank"
												rel="noreferrer"
											>
												{__(
													'Get Secret Key',
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
