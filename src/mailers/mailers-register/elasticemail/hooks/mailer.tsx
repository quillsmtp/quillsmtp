/**
 * WordPress Dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

addFilter(
	'QuillSMTP.Mailers.MailerModuleSettings',
	'QuillSMTP/ElasticEmail/ImplementIntegrationModuleSettings',
	(settings, slug: string) => {
		if (slug === 'elasticemail') {
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
												href="https://elasticemail.com/account#/settings/new/manage-api"
												target="_blank"
												rel="noreferrer"
											>
												{__(
													'Elastic Email Account',
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
