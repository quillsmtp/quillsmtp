/**
 * WordPress Dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

addFilter(
	'QuillSMTP.Mailers.MailerModuleSettings',
	'QuillSMTP/SMTP2GO/ImplementIntegrationModuleSettings',
	(settings, slug: string) => {
		if (slug === 'smtp2go') {
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
												href="https://app.smtp2go.com/sending/apikeys/"
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
