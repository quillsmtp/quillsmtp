/**
 * WordPress Dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

addFilter(
	'QuillSMTP.Mailers.MailerModuleSettings',
	'QuillSMTP/PostMark/ImplementIntegrationModuleSettings',
	(settings, slug: string) => {
		if (slug === 'postmark') {
			settings.connectParameters = {
				main: {
					accounts: {
						auth: {
							type: 'credentials',
							fields: {
								api_key: {
									label: __('Server API Token', 'quillsmtp'),
									type: 'password',
									required: true,
									help: () => (
										<p>
											{__(
												'Follow this link to get your API key:',
												'quillsmtp'
											)}{' '}
											<a
												href="https://account.postmarkapp.com/servers"
												target="_blank"
												rel="noreferrer"
											>
												{__(
													'Get Postmark API Key',
													'quillsmtp'
												)}
											</a>
										</p>
									),
								},
								message_stream_id: {
									label: __('Message Stream ID', 'quillsmtp'),
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
