/**
 * WordPress Dependencies
 */
import { useDispatch } from '@wordpress/data';
import { __, sprintf } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';

/**
 * External Dependencies.
 */
import Button from '@mui/material/Button';
import AdminPanelSettingsIcon from '@mui/icons-material/AdminPanelSettings';
import AddIcon from '@mui/icons-material/Add';

/**
 * Internal Dependencies
 */
import { AccountsLabels } from '../../../../types';

interface Props {
	connectionId: string;
	labels?: AccountsLabels;
	onAdded: (id: string, account: { name: string }) => void;
	Instructions?: React.FC;
	hasExistingAccounts?: boolean;
}

const Oauth: React.FC<Props> = ({
	connectionId,
	labels,
	onAdded,
	Instructions,
	hasExistingAccounts = false,
}) => {
	const { mailer } = useSelect((select) => {
		return {
			mailer: select('quillSMTP/core').getTempConnectionMailer(connectionId),
		};
	});

	// dispatch notices.
	const { createNotice } = useDispatch('quillSMTP/core');

	const authorize = () => {
		window[`add_new_${mailer}_account`] = (id: string, name: string) => {
			createNotice({
				type: 'success',
				message: sprintf(
					__('%s added successfully!', 'quillsmtp'),
					labels?.singular ?? __('Account', 'quillsmtp')
				),
			});
			onAdded(id, { name });
		};
		window.open(
			`${window['qsmtpAdmin'].adminUrl}admin.php?quillsmtp-${mailer}=authorize`,
			'authorize',
			'scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,width=600,height=500,left=100,top=100'
		);
	};

	const buttonLabel = hasExistingAccounts
		? sprintf(
				__('Add New %s', 'quillsmtp'),
				labels?.singular ?? __('Account', 'quillsmtp')
		  )
		: sprintf(
				__('Authorize Your %s', 'quillsmtp'),
				labels?.singular ?? __('Account', 'quillsmtp')
		  );

	return (
		<div className="mailer-auth-oauth">
			<Button
				onClick={authorize}
				variant={hasExistingAccounts ? 'outlined' : 'contained'}
				startIcon={
					hasExistingAccounts ? (
						<AddIcon />
					) : (
						<AdminPanelSettingsIcon />
					)
				}
			>
				{buttonLabel}
			</Button>
			{Instructions && (
				<div className="mailer-auth-instructions">
					<Instructions />
				</div>
			)}
		</div>
	);
};

export default Oauth;
