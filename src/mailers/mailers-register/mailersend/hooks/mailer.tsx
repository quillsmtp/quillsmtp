/**
 * WordPress Dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

addFilter(
	'QuillSMTP.Mailers.MailerModuleSettings',
	'QuillSMTP/MailerSend/ImplementIntegrationModuleSettings',
	(settings, slug: string) => {
		if (slug === 'mailersend') {
			settings.connectParameters = {
				main: {
					accounts: {
						auth: {
							type: 'credentials',
							fields: {
								api_token: {
									label: __('API Token', 'quillsmtp'),
									type: 'password',
									required: true,
									help: () => (
										<p>
											{__(
												'Follow these instructions to get your API Key:',
												'quillsmtp'
											)}{' '}
											<a
												href="https://app.mailersend.com/api-tokens"
												target="_blank"
												rel="noreferrer"
											>
												{__('Get API Key', 'quillsmtp')}
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
