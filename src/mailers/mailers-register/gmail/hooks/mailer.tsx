/**
 * WordPress Dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

/**
 * Internal Dependencies
 */
import config from '@quillsmtp/config';

addFilter(
	'QuillSMTP.Mailers.MailerModuleSettings',
	'QuillSMTP/Gmail/ImplementIntegrationModuleSettings',
	(settings, slug: string) => {
		if (slug === 'gmail') {
			settings.connectParameters = {
				main: {
					accounts: {
						auth: {
							type: 'oauth',
						},
					},
				},
				setup: {
					Instructions: () => (
						<>
							<p
								style={{
									marginBottom: '10px',
									fontWeight: 'bold',
								}}
							>
								{__('Redirect URI:', 'quillsmtp-pro')}{' '}
								<code>{`${config.getAdminUrl()}admin.php`}</code>
							</p>
						</>
					),
					fields: {
						client_id: {
							label: __('Client ID', 'quillsmtp'),
							type: 'text',
							check: true,
						},
						client_secret: {
							label: __('Client Secret', 'quillsmtp'),
							type: 'password',
							check: false,
						},
					},
				},
			};
		}
		return settings;
	}
);
