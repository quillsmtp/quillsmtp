/**
 * WordPress Dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { __, sprintf } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';

/**
 * External Dependencies
 */
import Button from '@mui/material/Button';
import AdminPanelSettingsIcon from '@mui/icons-material/AdminPanelSettings';
import CheckCircleIcon from '@mui/icons-material/CheckCircle';

/**
 * Internal Dependencies
 */
import config from '@quillsmtp/config';

const GmailAccountSettings: React.FC<{ connectionId: string }> = ({
	connectionId,
}) => {
	const { mailerSlug, accountId, accounts } = useSelect((select) => {
		const mailerSlug =
			select('quillSMTP/core').getTempConnectionMailer(connectionId);
		return {
			mailerSlug,
			accountId:
				select('quillSMTP/core').getTempConnectionAccountId(
					connectionId
				),
			accounts: select('quillSMTP/core').getMailer(mailerSlug)?.accounts,
		};
	});

	if (!accountId || !accounts?.[accountId]) return null;

	const account = accounts[accountId];
	const credentials = account?.credentials ?? {};
	const hasTokens = !!credentials.access_token;

	const authorize = () => {
		window[`add_new_gmail_account`] = (id: string, name: string) => {
			// Reload the page to reflect the updated account with tokens.
			window.location.reload();
		};
		window.open(
			`${window['qsmtpAdmin'].adminUrl}admin.php?quillsmtp-gmail=authorize&account_id=${accountId}`,
			'authorize',
			'scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,width=600,height=500,left=100,top=100'
		);
	};

	return (
		<div style={{ marginTop: '15px' }}>
			{hasTokens ? (
				<div
					style={{
						display: 'flex',
						alignItems: 'center',
						gap: '8px',
						color: '#2e7d32',
					}}
				>
					<CheckCircleIcon />
					{__('Account authorized successfully', 'quillsmtp')}
				</div>
			) : (
				<div>
					<p
						style={{
							marginBottom: '10px',
							color: '#d32f2f',
							fontWeight: 'bold',
						}}
					>
						{__(
							'This account needs authorization. Click the button below to authorize with Google.',
							'quillsmtp'
						)}
					</p>
					<Button
						onClick={authorize}
						variant="contained"
						color="primary"
						startIcon={<AdminPanelSettingsIcon />}
					>
						{__('Authorize with Google', 'quillsmtp')}
					</Button>
				</div>
			)}
		</div>
	);
};

addFilter(
	'QuillSMTP.Mailers.MailerModuleSettings',
	'QuillSMTP/Gmail/ImplementIntegrationModuleSettings',
	(settings, slug: string) => {
		if (slug === 'gmail') {
			settings.connectParameters = {
				main: {
					accounts: {
						auth: {
							type: 'credentials',
							Instructions: () => (
								<>
									<p
										style={{
											marginBottom: '10px',
											fontWeight: 'bold',
										}}
									>
										{__('Redirect URI:', 'quillsmtp')}{' '}
										<code>{`${config.getAdminUrl()}admin.php`}</code>
									</p>
								</>
							),
							fields: {
								client_id: {
									label: __('Client ID', 'quillsmtp'),
									type: 'text',
									required: true,
								},
								client_secret: {
									label: __('Client Secret', 'quillsmtp'),
									type: 'password',
									required: true,
								},
							},
						},
					},
				},
			};
			settings.account_settings = GmailAccountSettings;
		}
		return settings;
	}
);
