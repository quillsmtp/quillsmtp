/**
 * WordPress Dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

addFilter(
	'QuillSMTP.Mailers.MailerModuleSettings',
	'QuillSMTP/Loops/ImplementIntegrationModuleSettings',
	(settings, slug: string) => {
		if (slug === 'loops') {
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
												href="https://app.loops.so/settings?page=api"
												target="_blank"
												rel="noreferrer"
											>
												{__('Get API Key', 'quillsmtp')}
											</a>
										</p>
									),
								},
								transactional_id: {
									label: __('Transactional ID', 'quillsmtp'),
									type: 'text',
									required: true,
									help: () => (
										<p>
											{__(
												'Follow these instructions to get your Transactional ID:',
												'quillsmtp'
											)}{' '}
											<a
												href="https://app.loops.so/transactional"
												target="_blank"
												rel="noreferrer"
											>
												{__(
													'Get Transactional ID',
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
