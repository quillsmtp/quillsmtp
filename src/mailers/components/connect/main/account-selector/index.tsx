/**
 * QuillSMTP Dependencies
 */
import ConfigAPI from '@quillsmtp/config';

/**
 * WordPress Dependencies
 */
import { useState } from 'react';
import { __, sprintf } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal Dependencies
 */
import { size, map } from 'lodash';
import Radio from '@mui/material/Radio';
import RadioGroup from '@mui/material/RadioGroup';
import FormControlLabel from '@mui/material/FormControlLabel';
import FormControl from '@mui/material/FormControl';
import FormLabel from '@mui/material/FormLabel';
import Button from '@mui/lab/LoadingButton';
import LoadingButton from '@mui/lab/LoadingButton';
import AddIcon from '@mui/icons-material/Add';
import DeleteIcon from '@mui/icons-material/Delete';
import IconButton from '@mui/material/IconButton';
import Dialog from '@mui/material/Dialog';
import DialogActions from '@mui/material/DialogActions';
import DialogContent from '@mui/material/DialogContent';
import DialogContentText from '@mui/material/DialogContentText';
import DialogTitle from '@mui/material/DialogTitle';

/**
 * Internal Dependencies
 */
import type { ConnectMain } from '../../../types';
import type { Account } from '@quillsmtp/store';
import AccountAuth from '../../account-setup/account-auth';

interface Props {
	connectionId: string;
	main: ConnectMain;
}

const AccountSelector: React.FC<Props> = ({ connectionId, main }) => {
	// context.
	const { mailer, connection } = useSelect((select) => {
		const connection = select('quillSMTP/core').getConnection(connectionId);
		return {
			connection,
			mailer: select('quillSMTP/core').getMailer(connection.mailer),
		};
	});

	// dispatch.
	const { accounts } = mailer;
	const { addAccount, updateAccount, deleteAccount, updateConnection } =
		useDispatch('quillSMTP/core');

	// state.
	const [showingAddNewAccount, setShowingAddNewAccount] = useState(false);
	const [addingNewAccount, setAddingNewAccount] = useState(false);
	const [deleteAccountID, setDeleteAccountID] = useState(null);
	const [isDeleting, setIsDeleting] = useState(false);

	// Delete account.
	const deleteHandler = () => {
		if (isDeleting || !deleteAccountID) return;
		setIsDeleting(true);
		apiFetch({
			path: `/qsmtp/v1/mailers/${connection.mailer}/accounts/${deleteAccountID}`,
			method: 'DELETE',
		})
			.then(() => {
				deleteAccount(connection.mailer, deleteAccountID);
				setDeleteAccountID(null);
				setIsDeleting(false);
			})
			.catch((e) => {
				setIsDeleting(false);
				console.log(e);
			});
	};

	// if there is no accounts, show add account.
	if (!showingAddNewAccount) {
		if (Object.entries(accounts).length === 0) {
			setTimeout(() => setShowingAddNewAccount(true));
			return null;
		}
	}

	// updating connection on changing account selection.
	const onChange = (value) => {
		updateConnection(connectionId, {
			account_id: value,
		});
	};

	const onAdded = (id: string, account: Account) => {
		// add or update the account.
		if (accounts[id]) {
			updateAccount(connection.mailer, id, account);
		} else {
			addAccount(connection.mailer, id, account);
			ConfigAPI.setInitialPayload({
				...ConfigAPI.getInitialPayload(),
				mailers: {
					...ConfigAPI.getInitialPayload().mailers,
					[connection.mailer]: {
						...ConfigAPI.getInitialPayload().mailers[
							connection.mailer
						],
						accounts: {
							...ConfigAPI.getInitialPayload().mailers[
								connection.mailer
							].accounts,
							[id]: account,
						},
					},
				},
			});
		}
		// select it.
		onChange(id);
	};

	return (
		<div className="mailer-connect-main__account-selector">
			<div className="mailer-connect-main__account-selector__list">
				{size(accounts) > 0 && (
					<FormControl component="fieldset">
						<FormLabel component="legend">
							{__('Select an account', 'quillforms')}
						</FormLabel>
						<RadioGroup
							aria-label="account"
							name="account"
							value={connection.account_id}
							onChange={(e) => onChange(e.target.value)}
						>
							{map(accounts, (account, id) => (
								<div key={id}>
									<FormControlLabel
										value={id}
										control={<Radio />}
										label={account.name}
									/>
									<IconButton
										aria-label={__(
											'Delete account',
											'quillforms'
										)}
										onClick={() => setDeleteAccountID(id)}
										color="error"
									>
										<DeleteIcon />
									</IconButton>
								</div>
							))}
						</RadioGroup>
					</FormControl>
				)}
			</div>
			{deleteAccountID && (
				<Dialog
					open={deleteAccountID !== null}
					onClose={() => {
						if (!isDeleting) setDeleteAccountID(null);
					}}
					aria-labelledby="alert-dialog-title"
					aria-describedby="alert-dialog-description"
				>
					<DialogTitle id="alert-dialog-title">
						{__('Delete account', 'quillforms')}
					</DialogTitle>
					<DialogContent>
						<DialogContentText id="alert-dialog-description">
							{sprintf(
								__(
									'Are you sure you want to delete the account %s ?',
									'quillforms'
								),
								accounts[deleteAccountID]?.name
							)}
						</DialogContentText>
					</DialogContent>
					<DialogActions>
						<Button
							onClick={() => {
								if (!isDeleting) setDeleteAccountID(null);
							}}
							autoFocus
							disabled={isDeleting}
						>
							{__('Cancel', 'quillforms')}
						</Button>
						<LoadingButton
							onClick={deleteHandler}
							autoFocus
							color="error"
							startIcon={<DeleteIcon />}
							loading={isDeleting}
						>
							{__('Delete', 'quillforms')}
						</LoadingButton>
					</DialogActions>
				</Dialog>
			)}
			<Button
				component="label"
				variant="outlined"
				startIcon={<AddIcon />}
				onClick={() => setShowingAddNewAccount(true)}
				disabled={addingNewAccount}
			>
				{__('Add new account', 'quillforms')}
			</Button>
			{showingAddNewAccount && (
				<AccountAuth
					connectionId={connectionId}
					data={main.accounts}
					onAdding={setAddingNewAccount}
					onAdded={onAdded}
				/>
			)}
		</div>
	);
};

export default AccountSelector;
