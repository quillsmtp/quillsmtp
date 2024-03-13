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
import EditIcon from '@mui/icons-material/Edit';
import DeleteIcon from '@mui/icons-material/Delete';
import IconButton from '@mui/material/IconButton';
import Dialog from '@mui/material/Dialog';
import DialogActions from '@mui/material/DialogActions';
import DialogContent from '@mui/material/DialogContent';
import DialogContentText from '@mui/material/DialogContentText';
import DialogTitle from '@mui/material/DialogTitle';
import CircularProgress from '@mui/material/CircularProgress';

/**
 * Internal Dependencies
 */
import type { ConnectMain } from '../../../types';
import type { Account } from '@quillsmtp/store';
import AccountAuth from '../../account-setup/account-auth';
import { EditCredentials } from '../../account-edit';
import { getMailerModule } from '@quillsmtp/mailers';

interface Props {
	connectionId: string;
	main: ConnectMain;
}

const AccountSelector: React.FC<Props> = ({ connectionId, main }) => {
	// context.
	const { mailer, mailerSlug, account_id, getConnectionsIdsByAccountId } =
		useSelect((select) => {
			const mailerSlug =
				select('quillSMTP/core').getConnectionMailer(connectionId);
			return {
				account_id:
					select('quillSMTP/core').getConnectionAccountId(
						connectionId
					),
				mailer: select('quillSMTP/core').getMailer(mailerSlug),
				mailerSlug,
				getConnectionsIdsByAccountId:
					select('quillSMTP/core').getConnectionsIdsByAccountId,
			};
		});

	// dispatch.
	const { accounts } = mailer;
	const {
		addAccount,
		updateAccount,
		deleteAccount,
		updateConnection,
		createNotice,
		deleteConnections,
	} = useDispatch('quillSMTP/core');

	// state.
	const [showingAddNewAccount, setShowingAddNewAccount] = useState(false);
	const [addingNewAccount, setAddingNewAccount] = useState(false);
	const [deleteAccountID, setDeleteAccountID] = useState(null);
	const [isDeleting, setIsDeleting] = useState(false);
	const [isAdding, setIsAdding] = useState(false);
	const [editingAccount, setEditingAccount] = useState(false);
	const [editAccountID, setEditAccountID] = useState(null);

	const getAccountConnections = (accountId) => {
		return getConnectionsIdsByAccountId(accountId);
	};

	// Delete account.
	const removeAccount = () => {
		if (isDeleting || !deleteAccountID) return;
		setIsDeleting(true);
		apiFetch({
			path: `/qsmtp/v1/mailers/${mailerSlug}/accounts/${deleteAccountID}`,
			method: 'DELETE',
		})
			.then(() => {
				deleteAccount(mailerSlug, deleteAccountID);
				setDeleteAccountID(null);
				setIsDeleting(false);
				createNotice({
					type: 'success',
					message: __('Account deleted successfully.', 'quillsmtp'),
				});
			})
			.catch((e) => {
				setIsDeleting(false);
				console.log(e);
			});
	};

	// if there is no accounts, show add account.
	if (!showingAddNewAccount) {
		if (Object.entries(accounts).length === 0) {
			setTimeout(() => {
				setShowingAddNewAccount(true);
				setIsAdding(true);
			});
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
			updateAccount(mailerSlug, id, account);
		} else {
			addAccount(mailerSlug, id, account);
			ConfigAPI.setInitialPayload({
				...ConfigAPI.getInitialPayload(),
				mailers: {
					...ConfigAPI.getInitialPayload().mailers,
					[mailerSlug]: {
						...ConfigAPI.getInitialPayload().mailers[mailerSlug],
						accounts: {
							...ConfigAPI.getInitialPayload().mailers[mailerSlug]
								.accounts,
							[id]: account,
						},
					},
				},
			});
		}
		// select it.
		onChange(id);
		setIsAdding(false);
	};

	const deleteHandler = () => {
		// check if this account is used in any connection.
		const connections = getAccountConnections(deleteAccountID);
		if (connections.length === 0) {
			removeAccount();
			return;
		}

		// First check if this is connection in stored in the initial payload.
		// If so, we need to remove it from the initial payload.
		const initialPayload = ConfigAPI.getInitialPayload();
		const newConnections = { ...initialPayload.connections };

		connections.forEach((connectionId) => {
			delete newConnections[connectionId];
		});

		setIsDeleting(true);
		apiFetch({
			path: `/qsmtp/v1/settings`,
			method: 'POST',
			data: {
				connections: newConnections,
			},
		}).then((res: any) => {
			if (res.success) {
				ConfigAPI.setInitialPayload({
					...ConfigAPI.getInitialPayload(),
					connections: newConnections,
				});
				deleteConnections(connections);
				removeAccount();
			} else {
				createNotice({
					type: 'error',
					message: __('Error deleting connections.', 'quillsmtp'),
				});
			}

			setIsDeleting(false);
		});
	};

	const mailerModule = getMailerModule(mailerSlug);
	const AccountSettings = () => {
		if (!mailerModule.account_settings) return null;
		const Component = mailerModule.account_settings;

		/* @ts-ignore */
		return <Component connectionId={connectionId} />;
	};

	return (
		<>
			<div className="mailer-connect-main__account-selector">
				<div className="mailer-connect-main__account-selector__list">
					{main.accounts.auth.type === 'credentials' &&
						size(accounts) > 0 && (
							<FormControl component="fieldset" fullWidth>
								<FormLabel component="legend">
									{__('Select an account', 'quillsmtp')}
								</FormLabel>
								<RadioGroup
									aria-label="account"
									name="account"
									value={account_id}
									onChange={(e) => onChange(e.target.value)}
								>
									{map(accounts, (account, id) => (
										<div key={id}>
											<div>
												<FormControlLabel
													value={id}
													control={<Radio />}
													label={account.name}
												/>
												{main.accounts.auth.type ===
													'credentials' && (
														<>
															{!editingAccount && (
																<IconButton
																	aria-label={__(
																		'Edit account',
																		'quillsmtp'
																	)}
																	onClick={() => {
																		setEditAccountID(
																			id
																		);
																	}}
																	color={
																		editAccountID ===
																			id
																			? 'primary'
																			: 'default'
																	}
																>
																	<EditIcon />
																</IconButton>
															)}
															{editingAccount && (
																<CircularProgress
																	size={20}
																/>
															)}
														</>
													)}
												<IconButton
													aria-label={__(
														'Delete account',
														'quillsmtp'
													)}
													onClick={() =>
														setDeleteAccountID(id)
													}
													color="error"
												>
													<DeleteIcon />
												</IconButton>
											</div>
											{main.accounts.auth.type ===
												'credentials' &&
												editAccountID &&
												editAccountID === id && (
													<EditCredentials
														connectionId={
															connectionId
														}
														accountId={
															editAccountID
														}
														fields={
															main.accounts.auth
																.fields
														}
														account={
															accounts[
															editAccountID
															]
														}
														onEditing={
															setEditingAccount
														}
														onEdited={onAdded}
														onCancel={() =>
															setEditAccountID(
																null
															)
														}
													/>
												)}
										</div>
									))}
								</RadioGroup>
							</FormControl>
						)}
					{main.accounts.auth.type === 'oauth' &&
						size(accounts) > 0 && (
							<div>
								{__('Connected with', 'quillsmtp')}{' '}
								<strong>
									{map(accounts, 'name').join(', ')}
								</strong>
							</div>
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
							{__('Delete account', 'quillsmtp')}
						</DialogTitle>
						<DialogContent>
							<DialogContentText id="alert-dialog-description">
								{sprintf(
									__(
										'Are you absolutely certain about deleting the account "%s"? This action will permanently erase all connections linked to this account.',
										'quillsmtp'
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
								{__('Cancel', 'quillsmtp')}
							</Button>
							<LoadingButton
								onClick={deleteHandler}
								autoFocus
								color="error"
								startIcon={<DeleteIcon />}
								loading={isDeleting}
							>
								{__('Delete', 'quillsmtp')}
							</LoadingButton>
						</DialogActions>
					</Dialog>
				)}
				{main.accounts.auth.type === 'credentials' && !isAdding && (
					<Button
						component="label"
						variant="outlined"
						startIcon={<AddIcon />}
						onClick={() => {
							setShowingAddNewAccount(true);
							setIsAdding(true);
						}}
						disabled={addingNewAccount}
					>
						{__('Add new account', 'quillsmtp')}
					</Button>
				)}
				{showingAddNewAccount && (
					<AccountAuth
						connectionId={connectionId}
						data={main.accounts}
						onAdding={setAddingNewAccount}
						onAdded={onAdded}
					/>
				)}
			</div>
			<AccountSettings />
		</>
	);
};

export default AccountSelector;